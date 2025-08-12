<?php
namespace App\Repository;

use PDO;

final class SearchRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function search(string $tableType, string $detectedType, string $rawTerm): array
    {
        $table = match ($tableType) {
            'atendimento' => 'atendimentos',
            'internacao' => 'internacao',
            'agendamento' => 'agendamentosambulatorial',
            default => throw new \InvalidArgumentException('Tipo inválido'),
        };

        $select = $tableType === 'agendamento'
            ? 'PRONTUARIO,NOME,DTATEND,HRATEND,CNS'
            : 'PRONTUARIO,NOME,SETOR,DTATEND,HRATEND,DTCHECKOUT,CNS';

        switch ($detectedType) {
            case 'cns_exato':
                $stmt = $this->pdo->prepare("SELECT $select FROM `$table` WHERE `cns` = :v LIMIT 10");
                $stmt->bindValue(':v', $rawTerm, PDO::PARAM_STR);
                break;
            case 'prontuario_exato':
                $stmt = $this->pdo->prepare("SELECT $select FROM `$table` WHERE `prontuario` = :v LIMIT 50");
                $stmt->bindValue(':v', $rawTerm, PDO::PARAM_STR);
                break;
            case 'data':
                $date = preg_match('/^(\\d{2})\\/(\\d{2})\\/(\\d{4})$/', $rawTerm, $m) ? "{$m[3]}-{$m[2]}-{$m[1]}" : $rawTerm;
                $stmt = $this->pdo->prepare("
                    SELECT $select FROM `$table`
                    WHERE DATE(`dtatend`) = :d OR `dtatend` LIKE :like
                    ORDER BY `dtatend` DESC
                    LIMIT 100
                ");
                $stmt->bindValue(':d', $date, PDO::PARAM_STR);
                $stmt->bindValue(':like', "%{$rawTerm}%", PDO::PARAM_STR);
                break;
            case 'cpf':
                $cpf = preg_replace('/\\D/', '', $rawTerm);
                $stmt = $this->pdo->prepare("SELECT $select FROM `$table` WHERE `cpf` = :cpf OR `cpf` = :raw LIMIT 10");
                $stmt->bindValue(':cpf', $cpf, PDO::PARAM_STR);
                $stmt->bindValue(':raw', $rawTerm, PDO::PARAM_STR);
                break;
            default:
                $normalized = preg_replace('/\\s+/', ' ', trim($rawTerm));
                $flex = '%' . str_replace(' ', '%', $normalized) . '%';
                $like = "%{$rawTerm}%";
                $stmt = $this->pdo->prepare("
                    SELECT $select FROM `$table`
                    WHERE `nome` LIKE :flex
                       OR `nome` LIKE :like
                       OR `cns` LIKE :like
                       OR `prontuario` LIKE :like
                       OR `dtatend` LIKE :like
                    ORDER BY
                        CASE
                            WHEN `nome` LIKE :exact THEN 1
                            WHEN `nome` LIKE :start THEN 2
                            ELSE 3
                        END,
                        `nome` ASC
                    LIMIT 200
                ");
                $stmt->bindValue(':flex', $flex, PDO::PARAM_STR);
                $stmt->bindValue(':like', $like, PDO::PARAM_STR);
                $stmt->bindValue(':exact', $rawTerm, PDO::PARAM_STR);
                $stmt->bindValue(':start', $rawTerm . '%', PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }
}


