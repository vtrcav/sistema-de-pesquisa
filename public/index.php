<?php
    // === CONFIGURAÇÕES DE PERFORMANCE ===
    ini_set('memory_limit', '128M');

    // === INCLUSÃO DE FUNÇÕES CORE ===
    require_once '../src/core/functions.php';
    
    // === FUNÇÕES AUXILIARES ===
    function redirectIfEmpty($param) {
        if (empty($_GET[$param])) {
            header("Location: https://servicos.hnr.ma/arquivo");
            exit;
        }
    }

    function logBusca($termo, $tipo, $resultados_count, $tempo_execucao) {
        // Log para análise de performance (opcional)
        $log_entry = date('Y-m-d H:i:s') . " | Termo: '$termo' | Tipo: '$tipo' | Resultados: $resultados_count | Tempo: {$tempo_execucao}ms\n";
        file_put_contents('../logs/buscas.log', $log_entry, FILE_APPEND | LOCK_EX);
    }

    // === VALIDAÇÃO DE ENTRADA ===
    redirectIfEmpty('p');
    redirectIfEmpty('t');

    $termo_original = trim($_GET['p']);
    $tipo = $_GET['t'];

    // Validação de segurança adicional
    $tipos_validos = ['atendimento', 'internacao', 'agendamento'];
    if (!in_array($tipo, $tipos_validos)) {
        header("Location: index.php");
        exit;
    }

    // === INÍCIO DA MEDIÇÃO DE PERFORMANCE ===
    $tempo_inicio = microtime(true);

    // === DETECÇÃO INTELIGENTE DE BUSCA ===
    function detectarTipoBusca($termo) {
        $termo = trim($termo);
        
        // CNS: 15 dígitos
        if (preg_match('/^\d{15}$/', $termo)) {
            return 'cns_exato';
        }
        
        // Prontuário: números (2-8 dígitos)
        if (preg_match('/^\d{2,8}$/', $termo)) {
            return 'prontuario_exato';
        }
        
        // Data no formato DD/MM/AAAA ou AAAA-MM-DD  
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $termo) || 
            preg_match('/^\d{4}-\d{2}-\d{2}$/', $termo)) {
            return 'data';
        }
        
        // CPF (se existir campo)
        if (preg_match('/^\d{11}$/', $termo) || 
            preg_match('/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', $termo)) {
            return 'cpf';
        }
        
        return 'nome';
    }

    try {
        // === CONEXÃO COM O BANCO DE DADOS ===
        require_once '../src/config/database.php';

        // === MAPEAMENTO DE TABELAS ===
        $tabelas = [
            'atendimento' => 'atendimentos',
            'internacao' => 'internacao',
            'agendamento' => 'agendamentosambulatorial'
        ];
        $table = $tabelas[$tipo];

        // === DETECÇÃO DO TIPO DE BUSCA ===
        $tipo_busca = detectarTipoBusca($termo_original);
        
        // === CONSTRUÇÃO DA QUERY OTIMIZADA ===
        if ($tipo_busca == 'cns_exato') {
            // Busca exata por CNS - usa índice
            $query = $conexaodb->prepare("SELECT * FROM `$table` WHERE `cns` = :termo LIMIT 10");
            $query->bindParam(':termo', $termo_original, PDO::PARAM_STR);
            
        } else if ($tipo_busca == 'prontuario_exato') {
            // Busca exata por prontuário - usa índice  
            $query = $conexaodb->prepare("SELECT * FROM `$table` WHERE `prontuario` = :termo LIMIT 50");
            $query->bindParam(':termo', $termo_original, PDO::PARAM_STR);
            
        } else if ($tipo_busca == 'data') {
            // Busca otimizada por data - usa índice em dtatend
            $data_normalizada = $termo_original;
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $termo_original, $matches)) {
                $data_normalizada = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
            }
            
            $query = $conexaodb->prepare("                SELECT * FROM `$table` 
                WHERE DATE(`dtatend`) = :data 
                   OR `dtatend` LIKE :termo_like 
                ORDER BY `dtatend` DESC 
                LIMIT 100
            ");
            $query->bindParam(':data', $data_normalizada, PDO::PARAM_STR);
            $termo_like = "%" . $termo_original . "%" ;
            $query->bindParam(':termo_like', $termo_like, PDO::PARAM_STR);
            
        } else if ($tipo_busca == 'cpf') {
            // Busca por CPF (se o campo existir)
            $cpf_limpo = preg_replace('/[^0-9]/', '', $termo_original);
            $query = $conexaodb->prepare("SELECT * FROM `$table` WHERE `cpf` = :cpf OR `cpf` = :cpf_original LIMIT 10");
            $query->bindParam(':cpf', $cpf_limpo, PDO::PARAM_STR);
            $query->bindParam(':cpf_original', $termo_original, PDO::PARAM_STR);
            
        } else {
            // === BUSCA INTELIGENTE POR NOME ===
            
            // Normalizar espaços múltiplos
            $termo_normalizado = preg_replace('/\s+/', ' ', trim($termo_original));
            $termo_flexivel = str_replace(' ', '%', $termo_normalizado);
            $termo_flexivel = "%" . $termo_flexivel . "%" ;
            
            // Query otimizada com LIMIT para performance
            $query = $conexaodb->prepare("                SELECT * FROM `$table` 
                WHERE `nome` LIKE :termo_flexivel 
                   OR `nome` LIKE :termo_original 
                   OR `cns` LIKE :termo_original 
                   OR `prontuario` LIKE :termo_original 
                   OR `dtatend` LIKE :termo_original 
                ORDER BY 
                    CASE 
                        WHEN `nome` LIKE :termo_exact THEN 1
                        WHEN `nome` LIKE :termo_start THEN 2
                        ELSE 3
                    END,
                    `nome` ASC
                LIMIT 200
            ");
            
            $query->bindParam(':termo_flexivel', $termo_flexivel, PDO::PARAM_STR);
            $termo_original_like = "%" . $termo_original . "%" ;
            $query->bindParam(':termo_original', $termo_original_like, PDO::PARAM_STR);
            $termo_exact = $termo_original;
            $query->bindParam(':termo_exact', $termo_exact, PDO::PARAM_STR);
            $termo_start = $termo_original . "%" ;
            $query->bindParam(':termo_start', $termo_start, PDO::PARAM_STR);
        }

        // === EXECUÇÃO DA QUERY ===
        $query->execute();
        $resultados = $query->fetchAll();

        // === MEDIÇÃO DE PERFORMANCE ===
        $tempo_fim = microtime(true);
        $tempo_execucao = round(($tempo_fim - $tempo_inicio) * 1000, 2); // em milissegundos
        
        // Log da busca (descomente se quiser monitorar)
        // logBusca($termo_original, $tipo_busca, count($resultados), $tempo_execucao);
        
    } catch (PDOException $e) {
        error_log("Erro na busca hospitalar: " . $e->getMessage());
        die("Erro na consulta. Tente novamente em alguns instantes.");
    }

// === INCLUSÃO DO CABEÇALHO HTML ===
require_once '../src/templates/header.php';
?>

<div class="content-container">
    <div class="header-info">
        <div class="container">
            <img src="assets\img\brasao.png" width="120rem" alt="Brasão do Estado">
        </div>
        <h5>ESTADO DO MARANHÃO<br>SECRETARIA DE ESTADO DA SAÚDE<br>HOSPITAL NINA RODRIGUES</h5>      
        <p>Exibindo resultados para <em><u><?php echo strtoupper(htmlspecialchars($termo_original)) ?></u></em></p>
    </div>
    
    <a href="http://servicos.hnr.ma/arquivo" class="btn-new-search">Nova pesquisa</a>
    
    <div class="table-container">
        <table id="pacientes" class="table table-striped table-hover">
            <thead class="table-dark">
            <?php
                if (count($resultados)){
                    if($tipo === 'agendamento'){
            ?>        
                <tr>
                    <th>PRONTUÁRIO</th>
                    <th style="min-width:240px;">NOME</th>
                    <th style="min-width:100px;">ADMISSÃO</th>
                    <th>HORA</th>
                    <th>TIPO</th>
                    <th>CNS</th>
                </tr>
            </thead>
            <tbody>  
            <?php
                    } else {
            ?>
                <tr>
                    <th>PRONTUÁRIO</th>
                    <th style="min-width:240px;">NOME</th>
                    <th>SETOR</th>
                    <th style="min-width:85px;">ADMISSÃO</th>
                    <th>HORA</th>
                    <th style="min-width:85px;">ALTA</th>
                    <th>TIPO</th>
                    <th>CNS</th>
                </tr>
            </thead>
            <tbody>
            <?php
                    }
                    foreach($resultados as $Resultado){
                        if($tipo === 'agendamento'){
            ?>            
                <tr>
                    <td><?php echo display($Resultado['PRONTUARIO']) ?></td> 
                    <td><?php echo display($Resultado['NOME']) ?></td>
                    <td><?php echo display_date($Resultado['DTATEND']) ?></td>
                    <td><?php echo display_date($Resultado['HRATEND'], 'H:i') ?></td>
                    <td><?php echo display(strtoupper($tipo)) ?></td>
                    <td><?php echo display($Resultado['CNS']) ?></td>    
                </tr>
            <?php       
                        } else {
            ?>
                <tr>
                    <td><?php echo display($Resultado['PRONTUARIO']) ?></td>
                    <td><?php echo display($Resultado['NOME']) ?></td>
                    <td><?php echo display($Resultado['SETOR']) ?></td>    
                    <td><?php echo display_date($Resultado['DTATEND']) ?></td>  
                    <td><?php echo display_date($Resultado['HRATEND'], 'H:i') ?></td>
                    <td><?php echo display_date($Resultado['DTCHECKOUT']) ?></td> 
                    <td><?php echo display(strtoupper($tipo)) ?></td>  
                    <td><?php echo display($Resultado['CNS']) ?></td>   
                </tr>
            <?php    
                        }
                    }
                } else {
            ?>
                <p>Não foram encontrados resultados.</p>
            <?php
                }
            ?>    
            </tbody>
        </table>
    </div>
</div>

<?php
// === INCLUSÃO DO RODAPÉ HTML ===
require_once '../src/templates/footer.php';
?>