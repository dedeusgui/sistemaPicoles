<?php
/**
 * Arquivo de Verificação de Sessão
 * Protege páginas que exigem login
 * Fábrica de Picolés
 */

// Iniciar sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_tipo'])) {
    // Redirecionar para login se não estiver logado
    header('Location: login.php');
    exit();
}

// Função para verificar permissões específicas
function verificarPermissao($permissoes_permitidas)
{
    if (!in_array($_SESSION['usuario_tipo'], $permissoes_permitidas)) {
        // Redirecionar para menu principal se não tiver permissão
        header('Location: menu-principal.php?erro=sem_permissao');
        exit();
    }
}

// Dados do usuário logado disponíveis em:
// $_SESSION['usuario_id']
// $_SESSION['usuario_nome']
// $_SESSION['usuario_email']
// $_SESSION['usuario_tipo'] -> 'admin', 'vendedor', 'user_fabrica'

?>
