<?php
/**
 * Cadastro de Picolés - CRUD Completo
 * Fábrica de Picolés
 */

require_once 'includes/verificar-sessao.php';
require_once 'conectar.php';

// Verificar permissão (apenas admin e user_fabrica)
verificarPermissao(['admin', 'user_fabrica']);

$mensagem = '';
$tipo_mensagem = '';

// ============================================
// PROCESSAR AÇÕES DOS FORMULÁRIOS
// ============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    // ========== SABORES ==========
    if ($acao === 'cadastrar_sabor') {
        $nome = trim($_POST['nome_sabor']);
        if (!empty($nome)) {
            $stmt = $conn->prepare('INSERT INTO sabores (nome) VALUES (?)');
            $stmt->bind_param('s', $nome);
            if ($stmt->execute()) {
                $mensagem = 'Sabor cadastrado com sucesso!';
                $tipo_mensagem = 'sucesso';
            } else {
                $mensagem = 'Erro ao cadastrar sabor.';
                $tipo_mensagem = 'erro';
            }
            $stmt->close();
        }
    } elseif ($acao === 'editar_sabor') {
        $id = $_POST['id_sabor'];
        $nome = trim($_POST['nome_sabor']);
        if (!empty($nome)) {
            $stmt = $conn->prepare('UPDATE sabores SET nome = ? WHERE id = ?');
            $stmt->bind_param('si', $nome, $id);
            if ($stmt->execute()) {
                $mensagem = 'Sabor atualizado com sucesso!';
                $tipo_mensagem = 'sucesso';
            }
            $stmt->close();
        }
    } elseif ($acao === 'excluir_sabor') {
        $id = $_POST['id_sabor'];
        $stmt = $conn->prepare('DELETE FROM sabores WHERE id = ?');
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $mensagem = 'Sabor excluído com sucesso!';
            $tipo_mensagem = 'sucesso';
        }
        $stmt->close();
    }

    // ========== INGREDIENTES ==========
    elseif ($acao === 'cadastrar_ingrediente') {
        $nome = trim($_POST['nome_ingrediente']);
        if (!empty($nome)) {
            $stmt = $conn->prepare('INSERT INTO ingredientes (nome) VALUES (?)');
            $stmt->bind_param('s', $nome);
            if ($stmt->execute()) {
                $mensagem = 'Ingrediente cadastrado com sucesso!';
                $tipo_mensagem = 'sucesso';
            }
            $stmt->close();
        }
    } elseif ($acao === 'editar_ingrediente') {
        $id = $_POST['id_ingrediente'];
        $nome = trim($_POST['nome_ingrediente']);
        if (!empty($nome)) {
            $stmt = $conn->prepare('UPDATE ingredientes SET nome = ? WHERE id = ?');
            $stmt->bind_param('si', $nome, $id);
            if ($stmt->execute()) {
                $mensagem = 'Ingrediente atualizado com sucesso!';
                $tipo_mensagem = 'sucesso';
            }
            $stmt->close();
        }
    } elseif ($acao === 'excluir_ingrediente') {
        $id = $_POST['id_ingrediente'];
        $stmt = $conn->prepare('DELETE FROM ingredientes WHERE id = ?');
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $mensagem = 'Ingrediente excluído com sucesso!';
            $tipo_mensagem = 'sucesso';
        }
        $stmt->close();
    }

    // ========== TIPOS DE EMBALAGEM ==========
    elseif ($acao === 'cadastrar_embalagem') {
        $nome = trim($_POST['nome_embalagem']);
        if (!empty($nome)) {
            $stmt = $conn->prepare('INSERT INTO tipos_embalagem (nome) VALUES (?)');
            $stmt->bind_param('s', $nome);
            if ($stmt->execute()) {
                $mensagem = 'Tipo de embalagem cadastrado com sucesso!';
                $tipo_mensagem = 'sucesso';
            }
            $stmt->close();
        }
    } elseif ($acao === 'editar_embalagem') {
        $id = $_POST['id_embalagem'];
        $nome = trim($_POST['nome_embalagem']);
        if (!empty($nome)) {
            $stmt = $conn->prepare('UPDATE tipos_embalagem SET nome = ? WHERE id = ?');
            $stmt->bind_param('si', $nome, $id);
            if ($stmt->execute()) {
                $mensagem = 'Tipo de embalagem atualizado com sucesso!';
                $tipo_mensagem = 'sucesso';
            }
            $stmt->close();
        }
    } elseif ($acao === 'excluir_embalagem') {
        $id = $_POST['id_embalagem'];
        $stmt = $conn->prepare('DELETE FROM tipos_embalagem WHERE id = ?');
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $mensagem = 'Tipo de embalagem excluído com sucesso!';
            $tipo_mensagem = 'sucesso';
        }
        $stmt->close();
    }

    // ========== ADITIVOS NUTRITIVOS ==========
    elseif ($acao === 'cadastrar_aditivo') {
        $nome = trim($_POST['nome_aditivo']);
        $formula = trim($_POST['formula_aditivo']);
        if (!empty($nome) && !empty($formula)) {
            $stmt = $conn->prepare(
                'INSERT INTO aditivos_nutritivos (nome, formula_quimica) VALUES (?, ?)',
            );
            $stmt->bind_param('ss', $nome, $formula);
            if ($stmt->execute()) {
                $mensagem = 'Aditivo nutritivo cadastrado com sucesso!';
                $tipo_mensagem = 'sucesso';
            }
            $stmt->close();
        }
    } elseif ($acao === 'editar_aditivo') {
        $id = $_POST['id_aditivo'];
        $nome = trim($_POST['nome_aditivo']);
        $formula = trim($_POST['formula_aditivo']);
        if (!empty($nome) && !empty($formula)) {
            $stmt = $conn->prepare(
                'UPDATE aditivos_nutritivos SET nome = ?, formula_quimica = ? WHERE id = ?',
            );
            $stmt->bind_param('ssi', $nome, $formula, $id);
            if ($stmt->execute()) {
                $mensagem = 'Aditivo nutritivo atualizado com sucesso!';
                $tipo_mensagem = 'sucesso';
            }
            $stmt->close();
        }
    } elseif ($acao === 'excluir_aditivo') {
        $id = $_POST['id_aditivo'];
        $stmt = $conn->prepare('DELETE FROM aditivos_nutritivos WHERE id = ?');
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $mensagem = 'Aditivo nutritivo excluído com sucesso!';
            $tipo_mensagem = 'sucesso';
        }
        $stmt->close();
    }

    // ========== CONSERVANTES ==========
    elseif ($acao === 'cadastrar_conservante') {
        $nome = trim($_POST['nome_conservante']);
        $descricao = trim($_POST['descricao_conservante']);
        if (!empty($nome) && !empty($descricao)) {
            $stmt = $conn->prepare('INSERT INTO conservantes (nome, descricao) VALUES (?, ?)');
            $stmt->bind_param('ss', $nome, $descricao);
            if ($stmt->execute()) {
                $mensagem = 'Conservante cadastrado com sucesso!';
                $tipo_mensagem = 'sucesso';
            }
            $stmt->close();
        }
    } elseif ($acao === 'editar_conservante') {
        $id = $_POST['id_conservante'];
        $nome = trim($_POST['nome_conservante']);
        $descricao = trim($_POST['descricao_conservante']);
        if (!empty($nome) && !empty($descricao)) {
            $stmt = $conn->prepare('UPDATE conservantes SET nome = ?, descricao = ? WHERE id = ?');
            $stmt->bind_param('ssi', $nome, $descricao, $id);
            if ($stmt->execute()) {
                $mensagem = 'Conservante atualizado com sucesso!';
                $tipo_mensagem = 'sucesso';
            }
            $stmt->close();
        }
    } elseif ($acao === 'excluir_conservante') {
        $id = $_POST['id_conservante'];
        $stmt = $conn->prepare('DELETE FROM conservantes WHERE id = ?');
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $mensagem = 'Conservante excluído com sucesso!';
            $tipo_mensagem = 'sucesso';
        }
        $stmt->close();
    }

    // ========== PICOLÉS ==========
    elseif ($acao === 'cadastrar_picole') {
        $id_sabor = $_POST['id_sabor'];
        $preco = $_POST['preco'];
        $id_tipo_embalagem = $_POST['id_tipo_embalagem'];
        $id_tipo_picole = $_POST['id_tipo_picole'];
        $ingredientes = $_POST['ingredientes'] ?? [];

        // Inserir picolé
        $stmt = $conn->prepare(
            'INSERT INTO picoles (id_sabor, preco, id_tipo_embalagem, id_tipo_picole) VALUES (?, ?, ?, ?)',
        );
        $stmt->bind_param('idii', $id_sabor, $preco, $id_tipo_embalagem, $id_tipo_picole);

        if ($stmt->execute()) {
            $id_picole = $conn->insert_id;

            // Inserir ingredientes
            if (!empty($ingredientes)) {
                $stmt_ing = $conn->prepare(
                    'INSERT INTO ingredientes_picole (id_ingrediente, id_picole) VALUES (?, ?)',
                );
                foreach ($ingredientes as $id_ingrediente) {
                    $stmt_ing->bind_param('ii', $id_ingrediente, $id_picole);
                    $stmt_ing->execute();
                }
                $stmt_ing->close();
            }

            // Inserir aditivos (se normal)
            if ($id_tipo_picole == 1 && !empty($_POST['aditivos'])) {
                $aditivos = $_POST['aditivos'];
                $stmt_adit = $conn->prepare(
                    'INSERT INTO aditivos_nutritivos_picole (id_aditivo_nutritivo, id_picole) VALUES (?, ?)',
                );
                foreach ($aditivos as $id_aditivo) {
                    $stmt_adit->bind_param('ii', $id_aditivo, $id_picole);
                    $stmt_adit->execute();
                }
                $stmt_adit->close();
            }

            // Inserir conservantes (se ao leite)
            if ($id_tipo_picole == 2 && !empty($_POST['conservantes'])) {
                $conservantes = $_POST['conservantes'];
                $stmt_cons = $conn->prepare(
                    'INSERT INTO conservantes_picole (id_conservante, id_picole) VALUES (?, ?)',
                );
                foreach ($conservantes as $id_conservante) {
                    $stmt_cons->bind_param('ii', $id_conservante, $id_picole);
                    $stmt_cons->execute();
                }
                $stmt_cons->close();
            }

            $mensagem = 'Picolé cadastrado com sucesso!';
            $tipo_mensagem = 'sucesso';
        }
        $stmt->close();
    } elseif ($acao === 'excluir_picole') {
        $id = $_POST['id_picole'];

        // Excluir relacionamentos primeiro
        $conn->query("DELETE FROM ingredientes_picole WHERE id_picole = $id");
        $conn->query("DELETE FROM aditivos_nutritivos_picole WHERE id_picole = $id");
        $conn->query("DELETE FROM conservantes_picole WHERE id_picole = $id");

        // Excluir picolé
        $stmt = $conn->prepare('DELETE FROM picoles WHERE id = ?');
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $mensagem = 'Picolé excluído com sucesso!';
            $tipo_mensagem = 'sucesso';
        }
        $stmt->close();
    }
}

// ============================================
// BUSCAR DADOS PARA EXIBIÇÃO
// ============================================

// Sabores
$sabores = $conn->query('SELECT * FROM sabores ORDER BY nome');

// Ingredientes
$ingredientes = $conn->query('SELECT * FROM ingredientes ORDER BY nome');

// Tipos de Embalagem
$embalagens = $conn->query('SELECT * FROM tipos_embalagem ORDER BY nome');

// Aditivos Nutritivos
$aditivos = $conn->query('SELECT * FROM aditivos_nutritivos ORDER BY nome');

// Conservantes
$conservantes = $conn->query('SELECT * FROM conservantes ORDER BY nome');

// Tipos de Picolé
$tipos_picoles = $conn->query('SELECT * FROM tipos_picoles ORDER BY id');

// Picolés (com JOIN)
$picoles = $conn->query("
    SELECT p.*, 
           s.nome as sabor_nome, 
           te.nome as embalagem_nome,
           tp.nome as tipo_picole_nome
    FROM picoles p
    LEFT JOIN sabores s ON p.id_sabor = s.id
    LEFT JOIN tipos_embalagem te ON p.id_tipo_embalagem = te.id
    LEFT JOIN tipos_picoles tp ON p.id_tipo_picole = tp.id
    ORDER BY p.id DESC
");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Picolés - Fábrica de Picolés</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container-grande {
            max-width: 1200px;
            width: 95%;
        }
        
        /* Abas */
        .abas {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid var(--cor-primaria);
            flex-wrap: wrap;
        }
        
        .aba {
            padding: 0.8rem 1.5rem;
            background-color: #e0e0e0;
            border: none;
            border-radius: 8px 8px 0 0;
            cursor: pointer;
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        
        .aba:hover {
            background-color: #d0d0d0;
        }
        
        .aba.ativa {
            background-color: var(--cor-primaria);
            color: white;
        }
        
        /* Conteúdo das abas */
        .conteudo-aba {
            display: none;
        }
        
        .conteudo-aba.ativo {
            display: block;
        }
        
        /* Tabelas */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        table th {
            background-color: var(--cor-primaria);
            color: white;
            padding: 1rem;
            text-align: left;
        }
        
        table td {
            padding: 0.8rem 1rem;
            border-bottom: 1px solid #eee;
        }
        
        table tr:hover {
            background-color: #f5f5f5;
        }
        
        /* Botões de ação */
        .btn-acao {
            padding: 0.4rem 0.8rem;
            margin-right: 0.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .btn-editar {
            background-color: #ffc107;
            color: #333;
        }
        
        .btn-excluir {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-voltar {
            display: inline-block;
            margin-bottom: 1rem;
            padding: 0.6rem 1.2rem;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
        
        /* Mensagens */
        .alerta {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .alerta-sucesso {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alerta-erro {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Checkboxes */
        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            max-height: 200px;
            overflow-y: auto;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        
        .checkbox-group label {
            font-weight: normal;
            margin: 0;
        }
        
        /* Campos extras dinâmicos */
        #extras {
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="container container-grande">
        <a href="menu-principal.php" class="btn-voltar">← Voltar ao Menu</a>
        
        <h2>Cadastro de Picolés</h2>
        
        <?php if ($mensagem): ?>
            <div class="alerta alerta-<?php echo $tipo_mensagem; ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>
        
        <!-- SISTEMA DE ABAS -->
        <div class="abas">
            <button class="aba ativa" onclick="trocarAba('picoles')">🍦 Picolés</button>
            <button class="aba" onclick="trocarAba('sabores')">🍓 Sabores</button>
            <button class="aba" onclick="trocarAba('ingredientes')">🥛 Ingredientes</button>
            <button class="aba" onclick="trocarAba('embalagens')">📦 Embalagens</button>
            <button class="aba" onclick="trocarAba('aditivos')">💊 Aditivos</button>
            <button class="aba" onclick="trocarAba('conservantes')">🧪 Conservantes</button>
        </div>
        
        <!-- ========== ABA PICOLÉS ========== -->
        <div id="aba-picoles" class="conteudo-aba ativo">
            <h3>Cadastrar Novo Picolé</h3>
            <form method="POST">
                <input type="hidden" name="acao" value="cadastrar_picole">
                
                <label for="id_sabor">Sabor:</label>
                <select id="id_sabor" name="id_sabor" required>
                    <option value="">Selecione...</option>
                    <?php
                    $sabores->data_seek(0);
                    while ($sabor = $sabores->fetch_assoc()): ?>
                        <option value="<?php echo $sabor['id']; ?>">
                            <?php echo htmlspecialchars($sabor['nome']); ?>
                        </option>
                    <?php endwhile;
                    ?>
                </select>
                
                <label for="preco">Preço (R$):</label>
                <input type="number" id="preco" name="preco" step="0.01" min="0" required>
                
                <label for="id_tipo_embalagem">Tipo de Embalagem:</label>
                <select id="id_tipo_embalagem" name="id_tipo_embalagem" required>
                    <option value="">Selecione...</option>
                    <?php
                    $embalagens->data_seek(0);
                    while ($embalagem = $embalagens->fetch_assoc()): ?>
                        <option value="<?php echo $embalagem['id']; ?>">
                            <?php echo htmlspecialchars($embalagem['nome']); ?>
                        </option>
                    <?php endwhile;
                    ?>
                </select>
                
                <label for="id_tipo_picole">Tipo de Picolé:</label>
                <select id="id_tipo_picole" name="id_tipo_picole" required onchange="mostrarExtras()">
                    <option value="">Selecione...</option>
                    <?php while ($tipo = $tipos_picoles->fetch_assoc()): ?>
                        <option value="<?php echo $tipo['id']; ?>">
                            <?php echo htmlspecialchars($tipo['nome']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <label>Ingredientes:</label>
                <div class="checkbox-group">
                    <?php
                    $ingredientes->data_seek(0);
                    while ($ingrediente = $ingredientes->fetch_assoc()): ?>
                        <label>
                            <input type="checkbox" name="ingredientes[]" value="<?php echo $ingrediente[
                                'id'
                            ]; ?>">
                            <?php echo htmlspecialchars($ingrediente['nome']); ?>
                        </label>
                    <?php endwhile;
                    ?>
                </div>
                
                <div id="extras"></div>
                
                <button type="submit">Cadastrar Picolé</button>
            </form>
            
            <h3>Picolés Cadastrados</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Sabor</th>
                        <th>Tipo</th>
                        <th>Preço</th>
                        <th>Embalagem</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($picole = $picoles->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $picole['id']; ?></td>
                            <td><?php echo htmlspecialchars($picole['sabor_nome']); ?></td>
                            <td><?php echo htmlspecialchars($picole['tipo_picole_nome']); ?></td>
                            <td>R$ <?php echo number_format($picole['preco'], 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($picole['embalagem_nome']); ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="acao" value="excluir_picole">
                                    <input type="hidden" name="id_picole" value="<?php echo $picole[
                                        'id'
                                    ]; ?>">
                                    <button type="submit" class="btn-acao btn-excluir" onclick="return confirm('Deseja excluir este picolé?')">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <!-- ========== ABA SABORES ========== -->
        <div id="aba-sabores" class="conteudo-aba">
            <h3>Cadastrar Novo Sabor</h3>
            <form method="POST">
                <input type="hidden" name="acao" value="cadastrar_sabor">
                <label for="nome_sabor">Nome do Sabor:</label>
                <input type="text" id="nome_sabor" name="nome_sabor" required>
                <button type="submit">Cadastrar Sabor</button>
            </form>
            
            <h3>Sabores Cadastrados</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sabores->data_seek(0);
                    while ($sabor = $sabores->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $sabor['id']; ?></td>
                            <td><?php echo htmlspecialchars($sabor['nome']); ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                                                        <input type="hidden" name="acao" value="excluir_sabor">
                                    <input type="hidden" name="id_sabor" value="<?php echo $sabor[
                                        'id'
                                    ]; ?>">
                                    <button type="submit" class="btn-acao btn-excluir" onclick="return confirm('Deseja excluir este sabor?')">Excluir</button>
                                </form>

                                <!-- EDITAR SABOR -->
                                <button class="btn-acao btn-editar" onclick="editarSabor(<?php echo $sabor[
                                    'id'
                                ]; ?>, '<?php echo htmlspecialchars(
    $sabor['nome'],
); ?>')">Editar</button>
                            </td>
                        </tr>
                    <?php endwhile;
                    ?>
                </tbody>
            </table>

            <!-- Formulário de edição oculto -->
            <div id="editar-sabor" style="display:none; margin-top:2rem;">
                <h3>Editar Sabor</h3>
                <form method="POST">
                    <input type="hidden" name="acao" value="editar_sabor">
                    <input type="hidden" id="id_sabor_edit" name="id_sabor">
                    
                    <label for="nome_sabor_edit">Nome do Sabor:</label>
                    <input type="text" id="nome_sabor_edit" name="nome_sabor" required>

                    <button type="submit">Salvar Alterações</button>
                    <button type="button" onclick="document.getElementById('editar-sabor').style.display='none'">Cancelar</button>
                </form>
            </div>
        </div>

        <!-- ========== ABA INGREDIENTES ========== -->
        <div id="aba-ingredientes" class="conteudo-aba">
            <h3>Cadastrar Novo Ingrediente</h3>
            <form method="POST">
                <input type="hidden" name="acao" value="cadastrar_ingrediente">
                <label>Nome do Ingrediente:</label>
                <input type="text" name="nome_ingrediente" required>
                <button type="submit">Cadastrar</button>
            </form>

            <h3>Ingredientes Cadastrados</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Nome</th><th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $ingredientes->data_seek(0);
                    while ($ing = $ingredientes->fetch_assoc()): ?>
                        <tr>
                            <td><?= $ing['id'] ?></td>
                            <td><?= htmlspecialchars($ing['nome']) ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="acao" value="excluir_ingrediente">
                                    <input type="hidden" name="id_ingrediente" value="<?= $ing[
                                        'id'
                                    ] ?>">
                                    <button type="submit" class="btn-acao btn-excluir" onclick="return confirm('Excluir ingrediente?')">Excluir</button>
                                </form>
                                <button class="btn-acao btn-editar" onclick="editarIngrediente(<?= $ing[
                                    'id'
                                ] ?>, '<?= htmlspecialchars($ing['nome']) ?>')">Editar</button>
                            </td>
                        </tr>
                    <?php endwhile;
                    ?>
                </tbody>
            </table>

            <div id="editar-ingrediente" style="display:none; margin-top:2rem;">
                <h3>Editar Ingrediente</h3>
                <form method="POST">
                    <input type="hidden" name="acao" value="editar_ingrediente">
                    <input type="hidden" id="id_ingrediente_edit" name="id_ingrediente">

                    <label>Nome:</label>
                    <input type="text" id="nome_ingrediente_edit" name="nome_ingrediente" required>

                    <button type="submit">Salvar</button>
                    <button type="button" onclick="document.getElementById('editar-ingrediente').style.display='none'">Cancelar</button>
                </form>
            </div>
        </div>

        <!-- ========== ABA EMBALAGENS ========== -->
        <div id="aba-embalagens" class="conteudo-aba">
            <h3>Cadastrar Tipo de Embalagem</h3>
            <form method="POST">
                <input type="hidden" name="acao" value="cadastrar_embalagem">
                <label>Nome da Embalagem:</label>
                <input type="text" name="nome_embalagem" required>
                <button type="submit">Cadastrar</button>
            </form>

            <h3>Embalagens Cadastradas</h3>
            <table>
                <thead><tr><th>ID</th><th>Nome</th><th>Ações</th></tr></thead>
                <tbody>
                    <?php
                    $embalagens->data_seek(0);
                    while ($emb = $embalagens->fetch_assoc()): ?>
                        <tr>
                            <td><?= $emb['id'] ?></td>
                            <td><?= htmlspecialchars($emb['nome']) ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="acao" value="excluir_embalagem">
                                    <input type="hidden" name="id_embalagem" value="<?= $emb[
                                        'id'
                                    ] ?>">
                                    <button type="submit" class="btn-acao btn-excluir">Excluir</button>
                                </form>

                                <button class="btn-acao btn-editar" onclick="editarEmbalagem(<?= $emb[
                                    'id'
                                ] ?>, '<?= htmlspecialchars($emb['nome']) ?>')">Editar</button>
                            </td>
                        </tr>
                    <?php endwhile;
                    ?>
                </tbody>
            </table>

            <div id="editar-embalagem" style="display:none; margin-top:2rem;">
                <h3>Editar Embalagem</h3>
                <form method="POST">
                    <input type="hidden" name="acao" value="editar_embalagem">
                    <input type="hidden" id="id_embalagem_edit" name="id_embalagem">
                    
                    <label>Nome:</label>
                    <input type="text" id="nome_embalagem_edit" name="nome_embalagem" required>

                    <button type="submit">Salvar</button>
                    <button type="button" onclick="document.getElementById('editar-embalagem').style.display='none'">Cancelar</button>
                </form>
            </div>
        </div>

        <!-- ========== ABA ADITIVOS ========== -->
        <div id="aba-aditivos" class="conteudo-aba">
            <h3>Cadastrar Aditivo Nutritivo</h3>
            <form method="POST">
                <input type="hidden" name="acao" value="cadastrar_aditivo">
                <label>Nome:</label>
                <input type="text" name="nome_aditivo" required>

                <label>Fórmula Química:</label>
                <input type="text" name="formula_aditivo" required>

                <button type="submit">Cadastrar</button>
            </form>

            <h3>Aditivos Cadastrados</h3>
            <table>
                <thead>
                    <tr><th>ID</th><th>Nome</th><th>Fórmula</th><th>Ações</th></tr>
                </thead>
                <tbody>
                    <?php
                    $aditivos->data_seek(0);
                    while ($ad = $aditivos->fetch_assoc()): ?>
                        <tr>
                            <td><?= $ad['id'] ?></td>
                            <td><?= htmlspecialchars($ad['nome']) ?></td>
                            <td><?= htmlspecialchars($ad['formula_quimica']) ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="acao" value="excluir_aditivo">
                                    <input type="hidden" name="id_aditivo" value="<?= $ad['id'] ?>">
                                    <button class="btn-acao btn-excluir">Excluir</button>
                                </form>

                                <button class="btn-acao btn-editar" onclick="editarAditivo(<?= $ad[
                                    'id'
                                ] ?>, '<?= htmlspecialchars($ad['nome']) ?>', '<?= htmlspecialchars(
    $ad['formula_quimica'],
) ?>')">Editar</button>
                            </td>
                        </tr>
                    <?php endwhile;
                    ?>
                </tbody>
            </table>

            <div id="editar-aditivo" style="display:none; margin-top:2rem;">
                <h3>Editar Aditivo</h3>
                <form method="POST">
                    <input type="hidden" name="acao" value="editar_aditivo">
                    <input type="hidden" id="id_aditivo_edit" name="id_aditivo">

                    <label>Nome:</label>
                    <input type="text" id="nome_aditivo_edit" name="nome_aditivo" required>

                    <label>Fórmula:</label>
                    <input type="text" id="formula_aditivo_edit" name="formula_aditivo" required>

                    <button type="submit">Salvar</button>
                    <button type="button" onclick="document.getElementById('editar-aditivo').style.display='none'">Cancelar</button>
                </form>
            </div>
        </div>

        <!-- ========== ABA CONSERVANTES ========== -->
        <div id="aba-conservantes" class="conteudo-aba">
            <h3>Cadastrar Conservante</h3>
            <form method="POST">
                <input type="hidden" name="acao" value="cadastrar_conservante">
                <label>Nome:</label>
                <input type="text" name="nome_conservante" required>

                <label>Descrição:</label>
                <textarea name="descricao_conservante" required></textarea>

                <button type="submit">Cadastrar</button>
            </form>

            <h3>Conservantes Cadastrados</h3>
            <table>
                <thead><tr><th>ID</th><th>Nome</th><th>Descrição</th><th>Ações</th></tr></thead>
                <tbody>
                    <?php
                    $conservantes->data_seek(0);
                    while ($c = $conservantes->fetch_assoc()): ?>
                        <tr>
                            <td><?= $c['id'] ?></td>
                            <td><?= htmlspecialchars($c['nome']) ?></td>
                            <td><?= htmlspecialchars($c['descricao']) ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="acao" value="excluir_conservante">
                                    <input type="hidden" name="id_conservante" value="<?= $c[
                                        'id'
                                    ] ?>">
                                    <button class="btn-acao btn-excluir">Excluir</button>
                                </form>

                                <button class="btn-acao btn-editar" onclick="editarConservante(<?= $c[
                                    'id'
                                ] ?>, '<?= htmlspecialchars($c['nome']) ?>', '<?= htmlspecialchars(
    $c['descricao'],
) ?>')">Editar</button>
                            </td>
                        </tr>
                    <?php endwhile;
                    ?>
                </tbody>
            </table>

            <div id="editar-conservante" style="display:none; margin-top:2rem;">
                <h3>Editar Conservante</h3>
                <form method="POST">
                    <input type="hidden" name="acao" value="editar_conservante">
                    <input type="hidden" id="id_conservante_edit" name="id_conservante">

                    <label>Nome:</label>
                    <input type="text" id="nome_conservante_edit" name="nome_conservante" required>

                    <label>Descrição:</label>
                    <textarea id="descricao_conservante_edit" name="descricao_conservante" required></textarea>

                    <button type="submit">Salvar</button>
                    <button type="button" onclick="document.getElementById('editar-conservante').style.display='none'">Cancelar</button>
                </form>
            </div>
        </div>

    </div><!-- container -->

    <script>
        // Troca de abas
        function trocarAba(aba) {
            document.querySelectorAll('.aba').forEach(btn => btn.classList.remove('ativa'));
            document.querySelectorAll('.conteudo-aba').forEach(c => c.classList.remove('ativo'));

            document.querySelector(`button[onclick="trocarAba('${aba}')"]`).classList.add('ativa');
            document.getElementById(`aba-${aba}`).classList.add('ativo');
        }

        // Mostrar campos extras conforme tipo de picolé
        function mostrarExtras() {
            const tipo = document.getElementById('id_tipo_picole').value;
            const extras = document.getElementById('extras');

            extras.innerHTML = '';

            if (tipo == 1) {
                // Normal → Aditivos
                extras.innerHTML = `
                    <label>Aditivos Nutritivos:</label>
                    <div class="checkbox-group">
                        <?php
                        $aditivos->data_seek(0);
                        while ($ad = $aditivos->fetch_assoc()): ?>
                            <label><input type="checkbox" name="aditivos[]" value="<?= $ad[
                                'id'
                            ] ?>"> <?= htmlspecialchars($ad['nome']) ?></label>
                        <?php endwhile;
                        ?>
                    </div>
                `;
            }

            if (tipo == 2) {
                // Ao leite → Conservantes
                extras.innerHTML = `
                    <label>Conservantes:</label>
                    <div class="checkbox-group">
                        <?php
                        $conservantes->data_seek(0);
                        while ($c = $conservantes->fetch_assoc()): ?>
                            <label><input type="checkbox" name="conservantes[]" value="<?= $c[
                                'id'
                            ] ?>"> <?= htmlspecialchars($c['nome']) ?></label>
                        <?php endwhile;
                        ?>
                    </div>
                `;
            }
        }

        // Funções de edição (preenche o form e exibe)
        function editarSabor(id, nome) {
            document.getElementById('id_sabor_edit').value = id;
            document.getElementById('nome_sabor_edit').value = nome;
            document.getElementById('editar-sabor').style.display = 'block';
        }

        function editarIngrediente(id, nome) {
            document.getElementById('id_ingrediente_edit').value = id;
            document.getElementById('nome_ingrediente_edit').value = nome;
            document.getElementById('editar-ingrediente').style.display = 'block';
        }

        function editarEmbalagem(id, nome) {
            document.getElementById('id_embalagem_edit').value = id;
            document.getElementById('nome_embalagem_edit').value = nome;
            document.getElementById('editar-embalagem').style.display = 'block';
        }

        function editarAditivo(id, nome, formula) {
            document.getElementById('id_aditivo_edit').value = id;
            document.getElementById('nome_aditivo_edit').value = nome;
            document.getElementById('formula_aditivo_edit').value = formula;
            document.getElementById('editar-aditivo').style.display = 'block';
        }

        function editarConservante(id, nome, descricao) {
            document.getElementById('id_conservante_edit').value = id;
            document.getElementById('nome_conservante_edit').value = nome;
            document.getElementById('descricao_conservante_edit').value = descricao;
            document.getElementById('editar-conservante').style.display = 'block';
        }
    </script>
</body>
</html>
