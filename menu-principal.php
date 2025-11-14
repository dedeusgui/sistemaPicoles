<?php
/**
 * Menu Principal - Dashboard
 * Fábrica de Picolés
 */

require_once 'includes/verificar-sessao.php';

$tipo_usuario = $_SESSION['usuario_tipo'];
$nome_usuario = $_SESSION['usuario_nome'];

// Mensagens de erro/sucesso
$erro = $_GET['erro'] ?? '';
$mensagem_erro = '';

if ($erro === 'sem_permissao') {
    $mensagem_erro = 'Você não tem permissão para acessar essa página.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Principal - Fábrica de Picolés</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .menu-container {
            max-width: 600px;
        }
        
        .boas-vindas {
            text-align: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background-color: #f0f8ff;
            border-radius: 8px;
        }
        
        .boas-vindas h3 {
            color: var(--cor-primaria);
            margin-bottom: 0.5rem;
        }
        
        .boas-vindas .tipo-usuario {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            background-color: var(--cor-secundaria);
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
            color: var(--cor-texto);
        }
        
        .botoes-menu {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .btn-menu {
            display: block;
            padding: 1.2rem;
            background-color: var(--cor-primaria);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            text-align: center;
            font-size: 1.1rem;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .btn-menu:hover {
            background-color: #2e4d6e;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .btn-logout {
            background-color: #dc3545;
            margin-top: 1rem;
        }
        
        .btn-logout:hover {
            background-color: #c82333;
        }
        
        .alerta {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        
        .alerta-erro {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container menu-container">
        <h2>Menu Principal</h2>
        
        <div class="boas-vindas">
            <h3>Bem-vindo, <?php echo htmlspecialchars($nome_usuario); ?>!</h3>
            <span class="tipo-usuario">
                <?php switch ($tipo_usuario) {
                    case 'admin':
                        echo 'Administrador';
                        break;
                    case 'vendedor':
                        echo 'Vendedor';
                        break;
                    case 'user_fabrica':
                        echo 'Usuário da Fábrica';
                        break;
                } ?>
            </span>
        </div>
        
        <?php if ($mensagem_erro): ?>
            <div class="alerta alerta-erro">
                <?php echo htmlspecialchars($mensagem_erro); ?>
            </div>
        <?php endif; ?>
        
        <div class="botoes-menu">
            <?php // Admin - Acessa tudo

if ($tipo_usuario === 'admin') {
                echo '<a href="cadastro-picoles.php" class="btn-menu">📦 Cadastro de Picolés</a>';
                echo '<a href="vendas.php" class="btn-menu">💰 Registro de Vendas</a>';
                echo '<a href="relatorios.php" class="btn-menu">📊 Relatórios</a>';
            }
            // Vendedor - Só vendas e relatórios
            elseif ($tipo_usuario === 'vendedor') {
                echo '<a href="vendas.php" class="btn-menu">💰 Registro de Vendas</a>';
                echo '<a href="relatorios.php" class="btn-menu">📊 Relatórios</a>';
            }
            // User Fabrica - Só cadastro e relatórios
            elseif ($tipo_usuario === 'user_fabrica') {
                echo '<a href="cadastro-picoles.php" class="btn-menu">📦 Cadastro de Picolés</a>';
                echo '<a href="relatorios.php" class="btn-menu">📊 Relatórios</a>';
            } ?>
            
            <a href="logout.php" class="btn-menu btn-logout">🚪 Sair</a>
        </div>
        
        <div class="footer">
            <p>© 2025 Fábrica de Picolés</p>
        </div>
    </div>
</body>
</html>