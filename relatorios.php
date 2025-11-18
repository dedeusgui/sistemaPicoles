<?php
require 'conectar.php';

/* =====================================================
   1. KPIs PRINCIPAIS
===================================================== */

// Total de notas fiscais
$total_notas = $conn->query('SELECT COUNT(*) AS total FROM notas_fiscal')->fetch_assoc()['total'];

// Total de lotes
$total_lotes = $conn->query('SELECT COUNT(*) AS total FROM lotes')->fetch_assoc()['total'];

// Receita total das notas
$receita_total =
    $conn->query('SELECT SUM(valor) AS soma FROM notas_fiscal')->fetch_assoc()['soma'] ?? 0;

// Total de picolés cadastrados
$total_picoles = $conn->query('SELECT COUNT(*) AS total FROM picoles')->fetch_assoc()['total'];

/* =====================================================
   2. DADOS PARA O GRÁFICO - TIPOS MAIS VENDIDOS
===================================================== */

$graf1 = $conn->query("
    SELECT tp.nome AS tipo, SUM(l.quantidade) AS total
    FROM lotes_notas_fiscal ln
    INNER JOIN lotes l ON l.id = ln.id_lote
    INNER JOIN tipos_picoles tp ON tp.id = l.id_tipo_picole
    GROUP BY tp.id
");

$labels_tipos = [];
$dados_tipos = [];

while ($row = $graf1->fetch_assoc()) {
    $labels_tipos[] = $row['tipo'];
    $dados_tipos[] = $row['total'];
}

/* =====================================================
   3. DADOS PARA O GRÁFICO - RECEITA POR MÊS
===================================================== */

$graf2 = $conn->query("
    SELECT DATE_FORMAT(data, '%Y-%m') AS mes, SUM(valor) AS total
    FROM notas_fiscal
    GROUP BY mes
    ORDER BY mes ASC
");

$labels_meses = [];
$dados_meses = [];

while ($row = $graf2->fetch_assoc()) {
    $labels_meses[] = $row['mes'];
    $dados_meses[] = $row['total'];
}

/* =====================================================
   4. TABELA FINAL DE RESUMO DAS VENDAS
===================================================== */
/*
Campos válidos:
revendedores = razao_social, contato, cnpj
lotes = id_picole, id_tipo_picole, quantidade
notas_fiscal = id_revendedor, numero_serie, valor, data, descricao
*/

$tabela = $conn->query("
    SELECT 
        nf.numero_serie, 
        nf.data, 
        nf.valor,
        r.razao_social AS revendedor,
        l.quantidade,
        tp.nome AS tipo_picole
    FROM notas_fiscal nf
    LEFT JOIN revendedores r ON r.id = nf.id_revendedor
    LEFT JOIN lotes_notas_fiscal ln ON ln.id_nota_fiscal = nf.id
    LEFT JOIN lotes l ON l.id = ln.id_lote
    LEFT JOIN tipos_picoles tp ON tp.id = l.id_tipo_picole
    ORDER BY nf.data DESC
");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatórios - Fábrica de Picolés</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .kpi-card {
            border-radius: 12px;
            padding: 20px;
            background: #ffffffaa;
            box-shadow: 0 0 12px rgba(0,0,0,0.15);
            text-align: center;
        }
        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 0 12px rgba(0,0,0,0.15);
        }
        body {
            overflow-y: auto;
            padding: 40px 0;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="mb-4">Relatórios Gerais</h1>

    <!-- ======================= KPIs ======================= -->
    <div class="row my-4">
        <div class="col-md-3">
            <div class="kpi-card">
                <h4>Notas Fiscais</h4>
                <h2><?= $total_notas ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="kpi-card">
                <h4>Total de Lotes</h4>
                <h2><?= $total_lotes ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="kpi-card">
                <h4>Picolés Cadastrados</h4>
                <h2><?= $total_picoles ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="kpi-card">
                <h4>Receita Total</h4>
                <h2>R$ <?= number_format($receita_total, 2, ',', '.') ?></h2>
            </div>
        </div>
    </div>


    <!-- ============ GRÁFICO 1: TIPOS MAIS VENDIDOS ============ -->
    <div class="chart-container">
        <h3 class="text-center mb-3">Picolés Mais Vendidos por Tipo</h3>
        <canvas id="graficoTipos"></canvas>
    </div>

    <!-- ============ GRÁFICO 2: RECEITA ============ -->
    <div class="chart-container">
        <h3 class="text-center mb-3">Receita por Mês</h3>
        <canvas id="graficoMeses"></canvas>
    </div>

    <!-- ============ TABELA FINAL ============ -->
    <div class="chart-container">
        <h3 class="text-center mb-3">Resumo de Vendas</h3>

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Nº Série</th>
                    <th>Data</th>
                    <th>Revendedor</th>
                    <th>Tipo Picolé</th>
                    <th>Qtd</th>
                    <th>Valor NF</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $tabela->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['numero_serie'] ?></td>
                        <td><?= date('d/m/Y', strtotime($row['data'])) ?></td>
                        <td><?= $row['revendedor'] ?? '—' ?></td>
                        <td><?= $row['tipo_picole'] ?? '—' ?></td>
                        <td><?= $row['quantidade'] ?? 0 ?></td>
                        <td>R$ <?= number_format($row['valor'], 2, ',', '.') ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>

</div>


<!-- ================= SCRIPTS CHART.JS ================= -->
<script>
// ---------- Gráfico Tipos ----------
new Chart(document.getElementById('graficoTipos'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels_tipos) ?>,
        datasets: [{
            label: 'Quantidade Vendida',
            data: <?= json_encode($dados_tipos) ?>,
            backgroundColor: 'rgba(73, 102, 140, 0.8)',
        }]
    }
});

// ---------- Gráfico Meses ----------
new Chart(document.getElementById('graficoMeses'), {
    type: 'line',
    data: {
        labels: <?= json_encode($labels_meses) ?>,
        datasets: [{
            label: 'Receita (R$)',
            data: <?= json_encode($dados_meses) ?>,
            borderColor: 'rgba(242, 210, 114, 1)',
            borderWidth: 3,
            fill: false
        }]
    }
});
</script>

</body>
</html>
