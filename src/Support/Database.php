<?php
namespace App\Support;

use PDO;
use PDOException;

final class Database
{
    public static function makePdo(array $dbConfig): PDO
    {
        try {
            return new PDO(
                $dbConfig['dsn'],
                $dbConfig['user'],
                $dbConfig['pass'],
                $dbConfig['options'] ?? []
            );
        } catch (PDOException $e) {
            error_log('Erro de conexão com o banco de dados: ' . $e->getMessage());
            die('Erro ao conectar com o banco de dados. Tente novamente mais tarde.');
        }
    }
}


