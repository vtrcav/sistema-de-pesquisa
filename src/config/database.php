<?php
try {
    // === CONEXÃO PDO OTIMIZADA ===
    $conexaodb = new PDO(
        'mysql:host=localhost;dbname=basehosp;charset=utf8',
        'root', 
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false,
            PDO::ATTR_PERSISTENT => true, // Conexão persistente
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ]
    );
} catch (PDOException $e) {
    error_log("Erro de conexão com o banco de dados: " . $e->getMessage());
    // Em um ambiente de produção, evite exibir detalhes do erro para o usuário.
    die("Erro ao conectar com o banco de dados. Tente novamente mais tarde.");
}
?>