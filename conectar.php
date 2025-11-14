<?php
/**
 * Arquivo de Conexão com Banco de Dados
 * Fábrica de Picolés
 * Usando MySQLi
 */

// Configurações do banco
define('DB_HOST', 'localhost');
define('DB_NAME', 'fabrica_picoles');
define('DB_USER', 'root');
define('DB_PASS', '');

// Variável global de conexão
$conn = null;

try {
    // Criar conexão MySQLi
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexão
    if ($conn->connect_error) {
        throw new Exception('Falha na conexão: ' . $conn->connect_error);
    }

    // Definir charset para UTF-8
    $conn->set_charset('utf8mb4');
} catch (Exception $e) {
    // Em produção, não mostrar detalhes do erro
    die('Erro na conexão com o banco de dados. Por favor, contate o administrador.');

    // Para debug (comentar em produção):
    // die("Erro: " . $e->getMessage());
}
?>
