<?php
/**
 * Sistema de Login
 * Fábrica de Picolés
 */

session_start();

// Se já estiver logado, redireciona para o menu
if (isset($_SESSION['usuario_id'])) {
    header('Location: menu-principal.php');
    exit();
}

require_once 'conectar.php';

$erro = '';
$sucesso = '';

// Processar login quando o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $erro = 'Por favor, preencha todos os campos.';
    } else {
        // Buscar usuário no banco
        $stmt = $conn->prepare('SELECT id, nome, email, senha, tipo FROM usuarios WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();

            // Verificar senha (SEM hash - apenas para teste)
            if ($senha === $usuario['senha']) {
                // Login bem-sucedido - criar sessão
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_tipo'] = $usuario['tipo'];

                // Redirecionar para menu principal
                header('Location: menu-principal.php');
                exit();
            } else {
                $erro = 'Email ou senha incorretos.';
            }
        } else {
            $erro = 'Email ou senha incorretos.';
        }

        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fábrica de Picolés</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Login - Fábrica de Picolés</h2>
        
        <?php if ($erro): ?>
            <div class="alerta alerta-erro">
                <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($sucesso): ?>
            <div class="alerta alerta-sucesso">
                <?php echo htmlspecialchars($sucesso); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label for="email">Email:</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="Digite seu email"
                required
                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
            />

            <label for="senha">Senha:</label>
            <input
                type="password"
                id="senha"
                name="senha"
                placeholder="Digite sua senha"
                required
            />

            <button type="submit">Entrar</button>
        </form>

        <div class="footer">
            <p>© 2025 Fábrica de Picolés</p>
        </div>
    </div>
</body>
</html>