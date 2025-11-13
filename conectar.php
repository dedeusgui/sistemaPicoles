<?php
/**
 * Arquivo de Conexão com Banco de Dados
 * Fábrica de Picolés
 * Usando PDO para maior segurança
 */

// Configurações do banco
define('DB_HOST', 'localhost');
define('DB_NAME', 'fabrica_picoles');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Variável global de conexão
$pdo = null;

try {
    // Criar conexão PDO
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

    $opcoes = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $opcoes);
} catch (PDOException $e) {
    // Em produção, não mostrar detalhes do erro
    die('Erro na conexão com o banco de dados. Por favor, contate o administrador.');

    // Para debug (comentar em produção):
    // die("Erro: " . $e->getMessage());
}
?>
