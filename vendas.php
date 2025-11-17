<?php
/**
 * Vendas - CRUD Completo
 * Revendedores, Lotes, Notas Fiscais e Lotes_Notas_Fiscal
 * Fábrica de Picolés
 */

require_once 'includes/verificar-sessao.php';
require_once 'conectar.php';

// Verificar permissão (apenas admin e vendedor)
verificarPermissao(['admin', 'vendedor']);

$mensagem = '';
$tipo_mensagem = '';

// ============================================
// PROCESSAR AÇÕES DOS FORMULÁRIOS
// ============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    // ========== REVENDEDORES ==========
    if ($acao === 'cadastrar_revendedor') {
        $cnpj = trim($_POST['cnpj']);
        $razao_social = trim($_POST['razao_social']);
        $contato = trim($_POST['contato']);

        if (!empty($cnpj) && !empty($razao_social) && !empty($contato)) {
            $stmt = $conn->prepare(
                'INSERT INTO revendedores (cnpj, razao_social, contato) VALUES (?, ?, ?)',
            );
            $stmt->bind_param('sss', $cnpj, $razao_social, $contato);
            if ($stmt->execute()) {
                $mensagem = 'Revendedor cadastrado com sucesso!';
                $tipo_mensagem = 'sucesso';
            } else {
                $mensagem = 'Erro ao cadastrar revendedor.';
                $tipo_mensagem = 'erro';
            }
            $stmt->close();
        } else {
            $mensagem = 'Por favor, preencha todos os campos.';
            $tipo_mensagem = 'erro';
        }
    } elseif ($acao === 'editar_revendedor') {
        $id = $_POST['id_revendedor'];
        $cnpj = trim($_POST['cnpj']);
        $razao_social = trim($_POST['razao_social']);
        $contato = trim($_POST['contato']);

        if (!empty($cnpj) && !empty($razao_social) && !empty($contato)) {
            $stmt = $conn->prepare(
                'UPDATE revendedores SET cnpj = ?, razao_social = ?, contato = ? WHERE id = ?',
            );
            $stmt->bind_param('sssi', $cnpj, $razao_social, $contato, $id);
            if ($stmt->execute()) {
                $mensagem = 'Revendedor atualizado com sucesso!';
                $tipo_mensagem = 'sucesso';
            }
            $stmt->close();
        }
    } elseif ($acao === 'excluir_revendedor') {
        $id = $_POST['id_revendedor'];
        $stmt = $conn->prepare('DELETE FROM revendedores WHERE id = ?');
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $mensagem = 'Revendedor excluído com sucesso!';
            $tipo_mensagem = 'sucesso';
        }
        $stmt->close();
    }

    // ========== LOTES ==========
    elseif ($acao === 'cadastrar_lote') {
        $id_picole = $_POST['id_picole'];
        $quantidade = $_POST['quantidade'];

        if (!empty($id_picole) && !empty($quantidade)) {
            // Buscar o tipo de picolé pelo id do picolé
            $result = $conn->query("SELECT id_tipo_picole FROM picoles WHERE id = $id_picole");
            $row = $result->fetch_assoc();
            $id_tipo_picole = $row['id_tipo_picole'];

            $stmt = $conn->prepare('INSERT INTO lotes (id_tipo_picole, quantidade) VALUES (?, ?)');
            $stmt->bind_param('ii', $id_tipo_picole, $quantidade);
            if ($stmt->execute()) {
                $mensagem = 'Lote cadastrado com sucesso!';
                $tipo_mensagem = 'sucesso';
            } else {
                $mensagem = 'Erro ao cadastrar lote.';
                $tipo_mensagem = 'erro';
            }
            $stmt->close();
        } else {
            $mensagem = 'Por favor, preencha todos os campos.';
            $tipo_mensagem = 'erro';
        }
    } elseif ($acao === 'editar_lote') {
        $id = $_POST['id_lote'];
        $id_picole = $_POST['id_picole'];
        $quantidade = $_POST['quantidade'];

        if (!empty($id_picole) && !empty($quantidade)) {
            // Buscar o tipo de picolé
            $result = $conn->query("SELECT id_tipo_picole FROM picoles WHERE id = $id_picole");
            $row = $result->fetch_assoc();
            $id_tipo_picole = $row['id_tipo_picole'];

            $stmt = $conn->prepare(
                'UPDATE lotes SET id_tipo_picole = ?, quantidade = ? WHERE id = ?',
            );
            $stmt->bind_param('iii', $id_tipo_picole, $quantidade, $id);
            if ($stmt->execute()) {
                $mensagem = 'Lote atualizado com sucesso!';
                $tipo_mensagem = 'sucesso';
            }
            $stmt->close();
        }
    } elseif ($acao === 'excluir_lote') {
        $id = $_POST['id_lote'];
        $stmt = $conn->prepare('DELETE FROM lotes WHERE id = ?');
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $mensagem = 'Lote excluído com sucesso!';
            $tipo_mensagem = 'sucesso';
        }
        $stmt->close();
    }

    // ========== NOTAS FISCAIS ==========
    elseif ($acao === 'cadastrar_nota_fiscal') {
        $data = $_POST['data'];
        $numero_serie = trim($_POST['numero_serie']);
        $descricao = trim($_POST['descricao']);
        $id_revendedor = $_POST['id_revendedor'];

        if (!empty($data) && !empty($numero_serie) && !empty($id_revendedor)) {
            // Valor será 0 inicialmente, será atualizado quando lotes forem associados
            $valor = 0;
            $stmt = $conn->prepare(
                'INSERT INTO notas_fiscal (data, valor, numero_serie, descricao, id_revendedor) VALUES (?, ?, ?, ?, ?)',
            );
            $stmt->bind_param('sdsii', $data, $valor, $numero_serie, $descricao, $id_revendedor);
            if ($stmt->execute()) {
                $mensagem = 'Nota Fiscal cadastrada com sucesso!';
                $tipo_mensagem = 'sucesso';
            } else {
                $mensagem = 'Erro ao cadastrar nota fiscal.';
                $tipo_mensagem = 'erro';
            }
            $stmt->close();
        } else {
            $mensagem = 'Por favor, preencha todos os campos obrigatórios.';
            $tipo_mensagem = 'erro';
        }
    } elseif ($acao === 'editar_nota_fiscal') {
        $id = $_POST['id_nota_fiscal'];
        $data = $_POST['data'];
        $numero_serie = trim($_POST['numero_serie']);
        $descricao = trim($_POST['descricao']);
        $id_revendedor = $_POST['id_revendedor'];

        if (!empty($data) && !empty($numero_serie) && !empty($id_revendedor)) {
            $stmt = $conn->prepare(
                'UPDATE notas_fiscal SET data = ?, numero_serie = ?, descricao = ?, id_revendedor = ? WHERE id = ?',
            );
            $stmt->bind_param('sdssi', $data, $numero_serie, $descricao, $id_revendedor, $id);
            if ($stmt->execute()) {
                $mensagem = 'Nota Fiscal atualizada com sucesso!';
                $tipo_mensagem = 'sucesso';
            }
            $stmt->close();
        }
    } elseif ($acao === 'excluir_nota_fiscal') {
        $id = $_POST['id_nota_fiscal'];
        // Deletar associações primeiro
        $conn->query("DELETE FROM lotes_notas_fiscal WHERE id_nota_fiscal = $id");
        // Depois deletar a nota fiscal
        $stmt = $conn->prepare('DELETE FROM notas_fiscal WHERE id = ?');
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $mensagem = 'Nota Fiscal excluída com sucesso!';
            $tipo_mensagem = 'sucesso';
        }
        $stmt->close();
    }

    // ========== LOTES_NOTAS_FISCAL ==========
    elseif ($acao === 'cadastrar_lote_nota') {
        $id_lote = $_POST['id_lote'];
        $id_nota_fiscal = $_POST['id_nota_fiscal'];

        if (!empty($id_lote) && !empty($id_nota_fiscal)) {
            $stmt = $conn->prepare(
                'INSERT INTO lotes_notas_fiscal (id_lote, id_nota_fiscal) VALUES (?, ?)',
            );
            $stmt->bind_param('ii', $id_lote, $id_nota_fiscal);
            if ($stmt->execute()) {
                // Recalcular valor da nota fiscal
                atualizarValorNotaFiscal($conn, $id_nota_fiscal);
                $mensagem = 'Lote associado à Nota Fiscal com sucesso!';
                $tipo_mensagem = 'sucesso';
            } else {
                $mensagem = 'Erro ao associar lote. Verifique se já não está associado.';
                $tipo_mensagem = 'erro';
            }
            $stmt->close();
        } else {
            $mensagem = 'Por favor, selecione um lote e uma nota fiscal.';
            $tipo_mensagem = 'erro';
        }
    } elseif ($acao === 'excluir_lote_nota') {
        $id = $_POST['id_lote_nota'];
        $id_nota_fiscal = $_POST['id_nota_fiscal'];

        $stmt = $conn->prepare('DELETE FROM lotes_notas_fiscal WHERE id = ?');
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            // Recalcular valor da nota fiscal
            atualizarValorNotaFiscal($conn, $id_nota_fiscal);
            $mensagem = 'Associação removida com sucesso!';
            $tipo_mensagem = 'sucesso';
        }
        $stmt->close();
    }
}

// ============================================
// FUNÇÃO PARA ATUALIZAR VALOR DA NOTA FISCAL
// ============================================
function atualizarValorNotaFiscal($conn, $id_nota_fiscal)
{
    $result = $conn->query("
        SELECT SUM(l.quantidade * p.preco) AS valor_total
        FROM lotes_notas_fiscal ln
        LEFT JOIN lotes l ON ln.id_lote = l.id
        LEFT JOIN picoles p 
            ON p.id = (
                SELECT p2.id
                FROM picoles p2
                WHERE p2.id_tipo_picole = l.id_tipo_picole
                ORDER BY p2.id DESC
                LIMIT 1
            )
        WHERE ln.id_nota_fiscal = $id_nota_fiscal
    ");

    $row = $result->fetch_assoc();
    $valor = $row['valor_total'] ?? 0;

    $stmt = $conn->prepare('UPDATE notas_fiscal SET valor = ? WHERE id = ?');
    $stmt->bind_param('di', $valor, $id_nota_fiscal);
    $stmt->execute();
    $stmt->close();
}

// ============================================
// BUSCAR DADOS PARA EXIBIÇÃO
// ============================================

$revendedores = $conn->query('SELECT * FROM revendedores ORDER BY razao_social');
$tipos_picoles = $conn->query('SELECT * FROM tipos_picoles ORDER BY id');

$lotes = $conn->query("
    SELECT l.*, tp.nome as tipo_nome
    FROM lotes l
    LEFT JOIN tipos_picoles tp ON l.id_tipo_picole = tp.id
    ORDER BY l.id DESC
");

$notas_fiscais = $conn->query("
    SELECT nf.*, r.razao_social
    FROM notas_fiscal nf
    LEFT JOIN revendedores r ON nf.id_revendedor = r.id
    ORDER BY nf.data DESC
");

$lotes_notas = $conn->query("
    SELECT ln.*, l.quantidade, tp.nome as tipo_nome, nf.numero_serie, p.preco
    FROM lotes_notas_fiscal ln
    LEFT JOIN lotes l ON ln.id_lote = l.id
    LEFT JOIN tipos_picoles tp ON l.id_tipo_picole = tp.id
    LEFT JOIN notas_fiscal nf ON ln.id_nota_fiscal = nf.id
    LEFT JOIN picoles p ON p.id_tipo_picole = l.id_tipo_picole
    ORDER BY ln.id DESC
");
?>

    <?php // API para carregar picolés por tipo

if (isset($_GET['get_picoles'])) {
        $id_tipo_picole = $_GET['get_picoles'];
        $result = $conn->query("
            SELECT p.id, s.nome as sabor, p.preco, te.nome as embalagem
            FROM picoles p
            LEFT JOIN sabores s ON p.id_sabor = s.id
            LEFT JOIN tipos_embalagem te ON p.id_tipo_embalagem = te.id
            WHERE p.id_tipo_picole = $id_tipo_picole
            ORDER BY s.nome
        ");

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    } ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendas - Fábrica de Picolés</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container-grande {
            max-width: 1200px;
            width: 95%;
        }

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

        .conteudo-aba {
            display: none;
        }

        .conteudo-aba.ativo {
            display: block;
        }

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

        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        label {
            font-weight: bold;
            color: var(--cor-primaria);
        }

        input, select, textarea {
            padding: 0.8rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
        }

        button[type="submit"] {
            background-color: var(--cor-primaria);
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        button[type="submit"]:hover {
            background-color: #2e4454;
        }

        .info-picole {
            background-color: #f8f9fa;
            padding: 0.8rem;
            border-radius: 6px;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container container-grande">
        <a href="menu-principal.php" class="btn-voltar">← Voltar ao Menu</a>

        <h2>💼 Vendas</h2>

        <?php if ($mensagem): ?>
            <div class="alerta alerta-<?php echo $tipo_mensagem; ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <!-- SISTEMA DE ABAS -->
        <div class="abas">
            <button class="aba ativa" onclick="trocarAba('revendedores')">🏪 Revendedores</button>
            <button class="aba" onclick="trocarAba('lotes')">📦 Lotes</button>
            <button class="aba" onclick="trocarAba('notas_fiscais')">📄 Notas Fiscais</button>
            <button class="aba" onclick="trocarAba('lotes_notas')">🔗 Lotes & Notas</button>
        </div>

        <!-- ========== ABA REVENDEDORES ========== -->
        <div id="aba-revendedores" class="conteudo-aba ativo">
            <h3>Cadastrar Novo Revendedor</h3>
            <form method="POST">
                <input type="hidden" name="acao" value="cadastrar_revendedor">
                
                <label>CNPJ:</label>
                <input type="text" name="cnpj" placeholder="00.000.000/0000-00" required>
                
                <label>Razão Social:</label>
                <input type="text" name="razao_social" required>
                
                <label>Contato:</label>
                <input type="text" name="contato" placeholder="Nome, email ou telefone" required>
                
                <button type="submit">Cadastrar Revendedor</button>
            </form>

            <h3>Revendedores Cadastrados</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>CNPJ</th>
                        <th>Razão Social</th>
                        <th>Contato</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($rev = $revendedores->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $rev['id']; ?></td>
                            <td><?php echo htmlspecialchars($rev['cnpj']); ?></td>
                            <td><?php echo htmlspecialchars($rev['razao_social']); ?></td>
                            <td><?php echo htmlspecialchars($rev['contato']); ?></td>
                            <td>
                                <button class="btn-acao btn-editar" onclick="editarRevendedor(<?php echo $rev[
                                    'id'
                                ]; ?>, '<?php echo htmlspecialchars(
    $rev['cnpj'],
); ?>', '<?php echo htmlspecialchars($rev['razao_social']); ?>', '<?php echo htmlspecialchars(
    $rev['contato'],
); ?>')">Editar</button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="acao" value="excluir_revendedor">
                                    <input type="hidden" name="id_revendedor" value="<?php echo $rev[
                                        'id'
                                    ]; ?>">
                                    <button type="submit" class="btn-acao btn-excluir" onclick="return confirm('Deseja excluir?')">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <div id="editar-revendedor" style="display:none; margin-top:2rem;">
                <h3>Editar Revendedor</h3>
                <form method="POST">
                    <input type="hidden" name="acao" value="editar_revendedor">
                    <input type="hidden" id="id_revendedor_edit" name="id_revendedor">
                    
                    <label>CNPJ:</label>
                    <input type="text" id="cnpj_edit" name="cnpj" required>
                    
                    <label>Razão Social:</label>
                    <input type="text" id="razao_social_edit" name="razao_social" required>
                    
                    <label>Contato:</label>
                    <input type="text" id="contato_edit" name="contato" required>
                    
                    <button type="submit">Salvar Alterações</button>
                    <button type="button" onclick="document.getElementById('editar-revendedor').style.display='none'">Cancelar</button>
                </form>
            </div>
        </div>

        <!-- ========== ABA LOTES ========== -->
        <div id="aba-lotes" class="conteudo-aba">
            <h3>Cadastrar Novo Lote</h3>
            <form method="POST">
                <input type="hidden" name="acao" value="cadastrar_lote">
                
                <label>Tipo de Picolé:</label>
                <select id="tipo_picole_lote" required onchange="carregarPicoles()">
                    <option value="">Selecione um tipo...</option>
                    <?php
                    $tipos_picoles->data_seek(0);
                    while ($tipo = $tipos_picoles->fetch_assoc()): ?>
                        <option value="<?php echo $tipo['id']; ?>"><?php echo htmlspecialchars(
    $tipo['nome'],
); ?></option>
                    <?php endwhile;
                    ?>
                </select>
                
                <label>Picolé:</label>
                <select id="id_picole" name="id_picole" required onchange="atualizarInfoPicole()">
                    <option value="">Selecione um picolé...</option>
                </select>
                <div id="info-picole" class="info-picole" style="display:none;"></div>
                
                <label>Quantidade:</label>
                <input type="number" name="quantidade" min="1" required>
                
                <button type="submit">Cadastrar Lote</button>
            </form>

            <h3>Lotes Cadastrados</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Quantidade</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $lotes->data_seek(0);
                    while ($lote = $lotes->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $lote['id']; ?></td>
                            <td><?php echo htmlspecialchars($lote['tipo_nome']); ?></td>
                            <td><?php echo $lote['quantidade']; ?></td>
                            <td>
                                <button class="btn-acao btn-editar" onclick="editarLote(<?php echo $lote[
                                    'id'
                                ]; ?>, <?php echo $lote['id_tipo_picole']; ?>, <?php echo $lote[
    'quantidade'
]; ?>)">Editar</button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="acao" value="excluir_lote">
                                    <input type="hidden" name="id_lote" value="<?php echo $lote[
                                        'id'
                                    ]; ?>">
                                    <button type="submit" class="btn-acao btn-excluir" onclick="return confirm('Deseja excluir?')">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile;
                    ?>
                </tbody>
            </table>

            <div id="editar-lote" style="display:none; margin-top:2rem;">
                <h3>Editar Lote</h3>
                <form method="POST">
                    <input type="hidden" name="acao" value="editar_lote">
                    <input type="hidden" id="id_lote_edit" name="id_lote">
                    
                    <label>Tipo de Picolé:</label>
                    <select id="tipo_picole_edit" required onchange="carregarPicolesEdit()">
                        <option value="">Selecione um tipo...</option>
                        <?php
                        $tipos_picoles->data_seek(0);
                        while ($tipo = $tipos_picoles->fetch_assoc()): ?>
                            <option value="<?php echo $tipo['id']; ?>"><?php echo htmlspecialchars(
    $tipo['nome'],
); ?></option>
                        <?php endwhile;
                        ?>
                    </select>
                    
                    <label>Picolé:</label>
                    <select id="id_picole_edit" name="id_picole" required>
                        <option value="">Selecione um picolé...</option>
                    </select>
                    
                    <label>Quantidade:</label>
                    <input type="number" id="quantidade_edit" name="quantidade" min="1" required>
                    
                    <button type="submit">Salvar Alterações</button>
                    <button type="button" onclick="document.getElementById('editar-lote').style.display='none'">Cancelar</button>
                </form>
            </div>
        </div>

        <!-- ========== ABA NOTAS FISCAIS ========== -->
        <div id="aba-notas_fiscais" class="conteudo-aba">
            <h3>Cadastrar Nova Nota Fiscal</h3>
            <form method="POST">
                <input type="hidden" name="acao" value="cadastrar_nota_fiscal">
                
                <label>Data:</label>
                <input type="date" name="data" required value="<?php echo date('Y-m-d'); ?>">
                
                <label>Número de Série:</label>
                <input type="text" name="numero_serie" required>
                
                <label>Revendedor:</label>
                <select name="id_revendedor" required>
                    <option value="">Selecione...</option>
                    <?php
                    $revendedores->data_seek(0);
                    while ($rev = $revendedores->fetch_assoc()): ?>
                        <option value="<?php echo $rev['id']; ?>"><?php echo htmlspecialchars(
    $rev['razao_social'],
); ?></option>
                    <?php endwhile;
                    ?>
                </select>
                
                <label>Descrição (Opcional):</label>
                <textarea name="descricao" placeholder="Observações sobre a nota fiscal"></textarea>
                
                <button type="submit">Cadastrar Nota Fiscal</button>
            </form>

            <h3>Notas Fiscais Cadastradas</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Data</th>
                        <th>Série</th>
                        <th>Valor</th>
                        <th>Revendedor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $notas_fiscais->data_seek(0);
                    while ($nf = $notas_fiscais->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $nf['id']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($nf['data'])); ?></td>
                            <td><?php echo htmlspecialchars($nf['numero_serie']); ?></td>
                            <td>R$ <?php echo number_format($nf['valor'], 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($nf['razao_social']); ?></td>
                            <td>
                                <button class="btn-acao btn-editar" onclick="editarNotaFiscal(<?php echo $nf[
                                    'id'
                                ]; ?>, '<?php echo $nf['data']; ?>', '<?php echo htmlspecialchars(
    $nf['numero_serie'],
); ?>', <?php echo $nf['id_revendedor']; ?>, '<?php echo htmlspecialchars(
    $nf['descricao'],
); ?>')">Editar</button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="acao" value="excluir_nota_fiscal">
                                    <input type="hidden" name="id_nota_fiscal" value="<?php echo $nf[
                                        'id'
                                    ]; ?>">
                                    <button type="submit" class="btn-acao btn-excluir" onclick="return confirm('Deseja excluir?')">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile;
                    ?>
                </tbody>
            </table>

            <div id="editar-nota-fiscal" style="display:none; margin-top:2rem;">
                <h3>Editar Nota Fiscal</h3>
                <form method="POST">
                    <input type="hidden" name="acao" value="editar_nota_fiscal">
                    <input type="hidden" id="id_nota_fiscal_edit" name="id_nota_fiscal">
                    
                    <label>Data:</label>
                    <input type="date" id="data_edit" name="data" required>
                    
                    <label>Número de Série:</label>
                    <input type="text" id="numero_serie_edit" name="numero_serie" required>
                    
                    <label>Revendedor:</label>
                    <select id="id_revendedor_edit_nf" name="id_revendedor" required>
                        <option value="">Selecione...</option>
                        <?php
                        $revendedores->data_seek(0);
                        while ($rev = $revendedores->fetch_assoc()): ?>
                            <option value="<?php echo $rev['id']; ?>"><?php echo htmlspecialchars(
    $rev['razao_social'],
); ?></option>
                        <?php endwhile;
                        ?>
                    </select>
                    
                    <label>Descrição:</label>
                    <textarea id="descricao_edit" name="descricao"></textarea>
                    
                    <button type="submit">Salvar Alterações</button>
                    <button type="button" onclick="document.getElementById('editar-nota-fiscal').style.display='none'">Cancelar</button>
                </form>
            </div>
        </div>

        <!-- ========== ABA LOTES_NOTAS_FISCAL ========== -->
        <div id="aba-lotes_notas" class="conteudo-aba">
            <h3>Associar Lote à Nota Fiscal</h3>
            <form method="POST">
                <input type="hidden" name="acao" value="cadastrar_lote_nota">
                
                <label>Lote:</label>
                <select name="id_lote" required>
                    <option value="">Selecione...</option>
                    <?php
                    $lotes->data_seek(0);
                    while ($lote = $lotes->fetch_assoc()): ?>
                        <option value="<?php echo $lote['id']; ?>">#<?php echo $lote[
    'id'
]; ?> - <?php echo htmlspecialchars($lote['tipo_nome']); ?> (<?php echo $lote[
     'quantidade'
 ]; ?> un.)</option>
                    <?php endwhile;
                    ?>
                </select>
                
                <label>Nota Fiscal:</label>
                <select name="id_nota_fiscal" required>
                    <option value="">Selecione...</option>
                    <?php
                    $notas_fiscais->data_seek(0);
                    while ($nf = $notas_fiscais->fetch_assoc()): ?>
                        <option value="<?php echo $nf['id']; ?>">#<?php echo $nf[
    'id'
]; ?> - <?php echo htmlspecialchars($nf['numero_serie']); ?> (<?php echo htmlspecialchars(
     $nf['razao_social'],
 ); ?>)</option>
                    <?php endwhile;
                    ?>
                </select>
                
                <button type="submit">Associar Lote</button>
            </form>

            <h3>Associações Cadastradas</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Lote</th>
                        <th>Tipo</th>
                        <th>Quantidade</th>
                        <th>Preço Unit.</th>
                        <th>Subtotal</th>
                        <th>Série NF</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $lotes_notas->data_seek(0);
                    while ($ln = $lotes_notas->fetch_assoc()):
                        $subtotal = ($ln['quantidade'] ?? 0) * ($ln['preco'] ?? 0); ?>
                        <tr>
                            <td><?php echo $ln['id']; ?></td>
                            <td><?php echo $ln['id_lote']; ?></td>
                            <td><?php echo htmlspecialchars($ln['tipo_nome']); ?></td>
                            <td><?php echo $ln['quantidade']; ?></td>
                            <td>R$ <?php echo number_format($ln['preco'] ?? 0, 2, ',', '.'); ?></td>
                            <td><strong>R$ <?php echo number_format(
                                $subtotal,
                                2,
                                ',',
                                '.',
                            ); ?></strong></td>
                            <td><?php echo htmlspecialchars($ln['numero_serie']); ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="acao" value="excluir_lote_nota">
                                    <input type="hidden" name="id_lote_nota" value="<?php echo $ln[
                                        'id'
                                    ]; ?>">
                                    <input type="hidden" name="id_nota_fiscal" value="<?php echo $ln[
                                        'id_nota_fiscal'
                                    ]; ?>">
                                    <button type="submit" class="btn-acao btn-excluir" onclick="return confirm('Deseja remover esta associação?')">Remover</button>
                                </form>
                            </td>
                        </tr>
                    <?php
                    endwhile;
                    ?>
                </tbody>
            </table>

            <?php if (!$lotes_notas->num_rows): ?>
                <p style="text-align: center; margin-top: 2rem; color: #999;">Nenhuma associação registrada ainda.</p>
            <?php endif; ?>
        </div>

    </div><!-- container -->

    <script>
        // Array para armazenar picolés carregados
        let picolesData = {};

        // Carregar picolés por tipo (Cadastro)
        function carregarPicoles() {
            const tipoPicole = document.getElementById('tipo_picole_lote').value;
            const selectPicole = document.getElementById('id_picole');
            
            if (!tipoPicole) {
                selectPicole.innerHTML = '<option value="">Selecione um picolé...</option>';
                return;
            }

            fetch('?get_picoles=' + tipoPicole)
                .then(response => response.json())
                .then(data => {
                    picolesData = data;
                    let html = '<option value="">Selecione um picolé...</option>';
                    data.forEach(picole => {
                        html += `<option value="${picole.id}" data-sabor="${picole.sabor}" data-preco="${picole.preco}" data-embalagem="${picole.embalagem}">
                            ${picole.sabor} - R$ ${parseFloat(picole.preco).toFixed(2)}
                        </option>`;
                    });
                    selectPicole.innerHTML = html;
                    document.getElementById('info-picole').style.display = 'none';
                });
        }

        // Carregar picolés por tipo (Edição)
        function carregarPicolesEdit() {
            const tipoPicole = document.getElementById('tipo_picole_edit').value;
            const selectPicole = document.getElementById('id_picole_edit');
            
            if (!tipoPicole) {
                selectPicole.innerHTML = '<option value="">Selecione um picolé...</option>';
                return;
            }

            fetch('?get_picoles=' + tipoPicole)
                .then(response => response.json())
                .then(data => {
                    let html = '<option value="">Selecione um picolé...</option>';
                    data.forEach(picole => {
                        html += `<option value="${picole.id}" data-sabor="${picole.sabor}" data-preco="${picole.preco}" data-embalagem="${picole.embalagem}">
                            ${picole.sabor} - R$ ${parseFloat(picole.preco).toFixed(2)}
                        </option>`;
                    });
                    selectPicole.innerHTML = html;
                });
        }

        // Atualizar informações do picolé selecionado
        function atualizarInfoPicole() {
            const select = document.getElementById('id_picole');
            const option = select.options[select.selectedIndex];
            const infoDiv = document.getElementById('info-picole');

            if (!select.value) {
                infoDiv.style.display = 'none';
                return;
            }

            const sabor = option.getAttribute('data-sabor');
            const preco = option.getAttribute('data-preco');
            const embalagem = option.getAttribute('data-embalagem');

            infoDiv.innerHTML = `
                <strong>Sabor:</strong> ${sabor}<br>
                <strong>Preço Unitário:</strong> R$ ${parseFloat(preco).toFixed(2)}<br>
                <strong>Embalagem:</strong> ${embalagem}
            `;
            infoDiv.style.display = 'block';
        }

        // Troca de abas
        function trocarAba(aba) {
            document.querySelectorAll('.aba').forEach(btn => btn.classList.remove('ativa'));
            document.querySelectorAll('.conteudo-aba').forEach(c => c.classList.remove('ativo'));

            document.querySelector(`button[onclick="trocarAba('${aba}')"]`).classList.add('ativa');
            document.getElementById(`aba-${aba}`).classList.add('ativo');
        }

        // Funções de edição - Revendedores
        function editarRevendedor(id, cnpj, razao_social, contato) {
            document.getElementById('id_revendedor_edit').value = id;
            document.getElementById('cnpj_edit').value = cnpj;
            document.getElementById('razao_social_edit').value = razao_social;
            document.getElementById('contato_edit').value = contato;
            document.getElementById('editar-revendedor').style.display = 'block';
        }

        // Funções de edição - Lotes
        function editarLote(id, id_tipo_picole, quantidade) {
            document.getElementById('id_lote_edit').value = id;
            document.getElementById('tipo_picole_edit').value = id_tipo_picole;
            document.getElementById('quantidade_edit').value = quantidade;
            document.getElementById('editar-lote').style.display = 'block';
            carregarPicolesEdit();
        }

        // Funções de edição - Notas Fiscais
        function editarNotaFiscal(id, data, numero_serie, id_revendedor, descricao) {
            document.getElementById('id_nota_fiscal_edit').value = id;
            document.getElementById('data_edit').value = data;
            document.getElementById('numero_serie_edit').value = numero_serie;
            document.getElementById('id_revendedor_edit_nf').value = id_revendedor;
            document.getElementById('descricao_edit').value = descricao;
            document.getElementById('editar-nota-fiscal').style.display = 'block';
        }
    </script>

</body>
</html>