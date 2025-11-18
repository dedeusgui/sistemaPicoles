<?php
/**
 * Sistema de Relatórios Comerciais
 * Fábrica de Picolés
 */

require_once 'includes/verificar-sessao.php';
require_once 'conectar.php';

// =====================================================
// FILTROS DE PERÍODO
// =====================================================

$data_inicio = $_GET['data_inicio'] ?? date('Y-m-01'); // Primeiro dia do mês atual
$data_fim = $_GET['data_fim'] ?? date('Y-m-d'); // Hoje

// Filtro rápido
$filtro_rapido = $_GET['filtro'] ?? '';

switch ($filtro_rapido) {
    case '7dias':
        $data_inicio = date('Y-m-d', strtotime('-7 days'));
        $data_fim = date('Y-m-d');
        break;
    case '30dias':
        $data_inicio = date('Y-m-d', strtotime('-30 days'));
        $data_fim = date('Y-m-d');
        break;
    case 'mes_atual':
        $data_inicio = date('Y-m-01');
        $data_fim = date('Y-m-d');
        break;
    case 'ano_atual':
        $data_inicio = date('Y-01-01');
        $data_fim = date('Y-m-d');
        break;
}

// Criar condição WHERE para queries
$where_periodo = "nf.data BETWEEN '$data_inicio' AND '$data_fim'";

// =====================================================
// 1. KPIs PRINCIPAIS
// =====================================================

$total_notas = $conn
    ->query(
        "
    SELECT COUNT(*) AS total 
    FROM notas_fiscal nf 
    WHERE $where_periodo
",
    )
    ->fetch_assoc()['total'];

$total_lotes = $conn
    ->query(
        "
    SELECT COUNT(DISTINCT l.id) AS total 
    FROM lotes l
    INNER JOIN lotes_notas_fiscal ln ON ln.id_lote = l.id
    INNER JOIN notas_fiscal nf ON nf.id = ln.id_nota_fiscal
    WHERE $where_periodo
",
    )
    ->fetch_assoc()['total'];

$receita_total = $conn
    ->query(
        "
    SELECT COALESCE(SUM(valor), 0) AS soma 
    FROM notas_fiscal nf
    WHERE $where_periodo
",
    )
    ->fetch_assoc()['soma'];

$ticket_medio = $total_notas > 0 ? $receita_total / $total_notas : 0;

$total_picoles = $conn->query('SELECT COUNT(*) AS total FROM picoles')->fetch_assoc()['total'];

$quantidade_vendida = $conn
    ->query(
        "
    SELECT COALESCE(SUM(l.quantidade), 0) AS total
    FROM lotes l
    INNER JOIN lotes_notas_fiscal ln ON ln.id_lote = l.id
    INNER JOIN notas_fiscal nf ON nf.id = ln.id_nota_fiscal
    WHERE $where_periodo
",
    )
    ->fetch_assoc()['total'];

// =====================================================
// 2. VENDAS POR TIPO (Normal vs Ao Leite)
// =====================================================

$vendas_tipo = $conn->query("
    SELECT 
        tp.nome AS tipo, 
        SUM(l.quantidade) AS quantidade,
        COUNT(DISTINCT nf.id) AS num_vendas
    FROM lotes l
    INNER JOIN tipos_picoles tp ON tp.id = l.id_tipo_picole
    INNER JOIN lotes_notas_fiscal ln ON ln.id_lote = l.id
    INNER JOIN notas_fiscal nf ON nf.id = ln.id_nota_fiscal
    WHERE $where_periodo
    GROUP BY tp.id, tp.nome
");

$labels_tipos = [];
$dados_tipos = [];
$cores_tipos = ['rgba(73, 102, 140, 0.8)', 'rgba(242, 210, 114, 0.8)'];

while ($row = $vendas_tipo->fetch_assoc()) {
    $labels_tipos[] = $row['tipo'];
    $dados_tipos[] = $row['quantidade'];
}

// =====================================================
// 3. TOP 10 REVENDEDORES
// =====================================================

$top_revendedores = $conn->query("
    SELECT 
        r.razao_social,
        COUNT(DISTINCT nf.id) AS num_compras,
        SUM(nf.valor) AS total_gasto,
        SUM(l.quantidade) AS quantidade_total
    FROM notas_fiscal nf
    INNER JOIN revendedores r ON r.id = nf.id_revendedor
    LEFT JOIN lotes_notas_fiscal ln ON ln.id_nota_fiscal = nf.id
    LEFT JOIN lotes l ON l.id = ln.id_lote
    WHERE $where_periodo
    GROUP BY r.id, r.razao_social
    ORDER BY total_gasto DESC
    LIMIT 10
");

$labels_revendedores = [];
$dados_revendedores = [];

while ($row = $top_revendedores->fetch_assoc()) {
    $labels_revendedores[] = $row['razao_social'];
    $dados_revendedores[] = $row['total_gasto'];
}

// =====================================================
// 4. RECEITA POR MÊS (Últimos 12 meses)
// =====================================================

$receita_mensal = $conn->query("
    SELECT 
        DATE_FORMAT(data, '%Y-%m') AS mes,
        DATE_FORMAT(data, '%b/%Y') AS mes_formatado,
        SUM(valor) AS total
    FROM notas_fiscal
    WHERE data >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY mes
    ORDER BY mes ASC
");

$labels_meses = [];
$dados_meses = [];

while ($row = $receita_mensal->fetch_assoc()) {
    $labels_meses[] = $row['mes_formatado'];
    $dados_meses[] = $row['total'];
}

// =====================================================
// 5. RECEITA POR TIPO AO LONGO DO TEMPO
// =====================================================

$receita_tipo_tempo = $conn->query("
    SELECT 
        DATE_FORMAT(nf.data, '%Y-%m') AS mes,
        DATE_FORMAT(nf.data, '%b/%Y') AS mes_formatado,
        tp.nome AS tipo,
        SUM(nf.valor) AS total
    FROM notas_fiscal nf
    INNER JOIN lotes_notas_fiscal ln ON ln.id_nota_fiscal = nf.id
    INNER JOIN lotes l ON l.id = ln.id_lote
    INNER JOIN tipos_picoles tp ON tp.id = l.id_tipo_picole
    WHERE nf.data >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY mes, tp.id, tp.nome
    ORDER BY mes ASC, tp.nome
");

$dados_temporal = [];
while ($row = $receita_tipo_tempo->fetch_assoc()) {
    $dados_temporal[$row['tipo']][$row['mes_formatado']] = $row['total'];
}

// Preparar dados para o gráfico
$datasets_temporal = [];
$cores_temporal = ['rgba(73, 102, 140, 0.6)', 'rgba(242, 210, 114, 0.6)'];
$i = 0;

foreach ($dados_temporal as $tipo => $valores) {
    $dataset = [
        'label' => $tipo,
        'data' => array_values($valores),
        'backgroundColor' => $cores_temporal[$i] ?? 'rgba(100, 100, 100, 0.6)',
        'borderColor' => str_replace('0.6', '1', $cores_temporal[$i] ?? 'rgba(100, 100, 100, 1)'),
        'borderWidth' => 2,
    ];
    $datasets_temporal[] = $dataset;
    $i++;
}

// =====================================================
// 6. TABELA RESUMO DE VENDAS
// =====================================================

$tabela_vendas = $conn->query("
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
    WHERE $where_periodo
    ORDER BY nf.data DESC
    LIMIT 100
");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - Fábrica de Picolés</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background: #ecf0f1;
            min-height: 100vh;
            padding: 20px 0;
            overflow-y: auto;
        }

        .container-relatorios {
            max-width: 1400px;
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
        }

        .btn-voltar {
            display: inline-block;
            margin-bottom: 1.5rem;
            padding: 0.6rem 1.5rem;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .btn-voltar:hover {
            background-color: #5a6268;
            color: white;
        }

        /* KPI Cards */
        .kpi-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .kpi-card {
            background: linear-gradient(135deg, #49668c, #5a7aa0);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            transition: transform 0.3s;
        }

        .kpi-card:hover {
            transform: translateY(-5px);
        }

        .kpi-card h4 {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            opacity: 0.9;
        }

        .kpi-card .valor {
            font-size: 2rem;
            font-weight: bold;
            margin: 0;
        }

        .kpi-card.destaque {
            background: linear-gradient(135deg, #f2d272, #f5dc8d);
            color: #333;
        }

        /* Filtros */
        .filtros-container {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            border: 1px solid #e0e0e0;
        }

        .filtros-container h5 {
            color: #49668c;
            margin-bottom: 1.5rem;
            font-weight: bold;
        }

        .form-filtros {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 1rem;
            margin-bottom: 1.5rem;
            align-items: end;
        }

        .form-group-filtro {
            display: flex;
            flex-direction: column;
        }

        .form-group-filtro label {
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: #333;
        }

        .filtros-rapidos {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 0.75rem;
        }

        .btn-filtro {
            padding: 0.6rem 1rem;
            border: 2px solid #49668c;
            background: white;
            color: #49668c;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            font-weight: 500;
        }

        .btn-filtro:hover, .btn-filtro.ativo {
            background: #49668c;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(73, 102, 140, 0.3);
        }

        /* Sistema de Abas */
        .abas-container {
            display: flex;
            gap: 0.5rem;
            margin: 2rem 0 1rem 0;
            border-bottom: 2px solid #49668c;
            flex-wrap: wrap;
        }

        .aba {
            padding: 0.8rem 1.5rem;
            background: #e0e0e0;
            border: none;
            border-radius: 8px 8px 0 0;
            cursor: pointer;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .aba:hover {
            background: #d0d0d0;
        }

        .aba.ativa {
            background: #49668c;
            color: white;
        }

        .conteudo-aba {
            display: none;
            padding: 2rem 0;
        }

        .conteudo-aba.ativo {
            display: block;
        }

        /* Gráficos */
        .chart-container {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
        }

        .chart-container h3 {
            color: #49668c;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1.3rem;
            font-weight: bold;
        }

        .chart-wrapper {
            position: relative;
            height: 400px;
            margin: 0 auto;
        }

        .chart-wrapper canvas {
            max-height: 400px;
        }

        /* Grid de gráficos */
        .graficos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .grafico-full {
            grid-column: 1 / -1;
        }

        /* Tabelas */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
        }

        table th {
            background-color: #49668c;
            color: white;
            padding: 1rem;
        }

        table td {
            padding: 0.8rem;
        }

        table tbody tr:hover {
            background-color: #f5f5f5;
        }

        /* Responsivo */
        @media (max-width: 768px) {
            .kpi-container {
                grid-template-columns: 1fr;
            }

            .form-filtros {
                grid-template-columns: 1fr;
            }

            .filtros-rapidos {
                grid-template-columns: 1fr;
            }

            .graficos-grid {
                grid-template-columns: 1fr;
            }

            .chart-wrapper {
                height: 300px;
            }
        }

        @media (max-width: 1200px) {
            .graficos-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="container container-relatorios">
    <a href="menu-principal.php" class="btn-voltar">← Voltar ao Menu</a>
    
    <h1 style="color: #49668c; margin-bottom: 1rem;">📊 Relatórios Comerciais</h1>
    <p style="color: #666;">Análise de desempenho e vendas da fábrica de picolés</p>

    <!-- ======================= FILTROS ======================= -->
    <div class="filtros-container">
        <h5>🔍 Filtrar Período</h5>
        
        <form method="GET" class="form-filtros">
            <div class="form-group-filtro">
                <label for="data_inicio">Data Inicial:</label>
                <input type="date" id="data_inicio" name="data_inicio" value="<?= $data_inicio ?>" class="form-control">
            </div>
            
            <div class="form-group-filtro">
                <label for="data_fim">Data Final:</label>
                <input type="date" id="data_fim" name="data_fim" value="<?= $data_fim ?>" class="form-control">
            </div>
            
            <button type="submit" class="btn btn-primary" style="background-color: #49668c; border: none; height: 38px;">Aplicar</button>
        </form>

        <div class="filtros-rapidos">
            <a href="?filtro=7dias" class="btn-filtro <?= $filtro_rapido === '7dias'
                ? 'ativo'
                : '' ?>">📅 Últimos 7 dias</a>
            <a href="?filtro=30dias" class="btn-filtro <?= $filtro_rapido === '30dias'
                ? 'ativo'
                : '' ?>">📅 Últimos 30 dias</a>
            <a href="?filtro=mes_atual" class="btn-filtro <?= $filtro_rapido === 'mes_atual'
                ? 'ativo'
                : '' ?>">📅 Mês Atual</a>
            <a href="?filtro=ano_atual" class="btn-filtro <?= $filtro_rapido === 'ano_atual'
                ? 'ativo'
                : '' ?>">📅 Ano Atual</a>
            <a href="relatorios.php" class="btn-filtro">🔄 Limpar</a>
        </div>
    </div>

    <!-- ======================= KPIs ======================= -->
    <div class="kpi-container">
        <div class="kpi-card destaque">
            <h4>💰 Receita Total</h4>
            <p class="valor">R$ <?= number_format($receita_total, 2, ',', '.') ?></p>
        </div>

        <div class="kpi-card">
            <h4>📄 Notas Fiscais</h4>
            <p class="valor"><?= $total_notas ?></p>
        </div>

        <div class="kpi-card">
            <h4>📦 Lotes Vendidos</h4>
            <p class="valor"><?= $total_lotes ?></p>
        </div>

        <div class="kpi-card">
            <h4>🍦 Quantidade Vendida</h4>
            <p class="valor"><?= number_format($quantidade_vendida, 0, ',', '.') ?></p>
        </div>

        <div class="kpi-card">
            <h4>💵 Ticket Médio</h4>
            <p class="valor">R$ <?= number_format($ticket_medio, 2, ',', '.') ?></p>
        </div>

        <div class="kpi-card">
            <h4>🏭 Picolés Cadastrados</h4>
            <p class="valor"><?= $total_picoles ?></p>
        </div>
    </div>

    <!-- ======================= SISTEMA DE ABAS ======================= -->
    <div class="abas-container">
        <button class="aba ativa" onclick="trocarAba('dashboard')">📊 Dashboard</button>
        <button class="aba" onclick="trocarAba('tipos')">🍦 Vendas por Tipo</button>
        <button class="aba" onclick="trocarAba('revendedores')">🏆 Top Revendedores</button>
        <button class="aba" onclick="trocarAba('evolucao')">📈 Evolução Temporal</button>
        <button class="aba" onclick="trocarAba('detalhes')">📋 Detalhes de Vendas</button>
    </div>

    <!-- ========== ABA DASHBOARD ========== -->
    <div id="aba-dashboard" class="conteudo-aba ativo">
        <div class="graficos-grid">
            <div class="chart-container">
                <h3>🍦 Vendas por Tipo de Picolé</h3>
                <div class="chart-wrapper">
                    <canvas id="graficoTiposPizza"></canvas>
                </div>
            </div>
            
            <div class="chart-container">
                <h3>💰 Receita Mensal (Últimos 12 Meses)</h3>
                <div class="chart-wrapper">
                    <canvas id="graficoReceitaMensal"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== ABA VENDAS POR TIPO ========== -->
    <div id="aba-tipos" class="conteudo-aba">
        <div class="chart-container grafico-full">
            <h3>📊 Quantidade Vendida por Tipo</h3>
            <div class="chart-wrapper">
                <canvas id="graficoTiposBarras"></canvas>
            </div>
        </div>
        
        <div class="chart-container grafico-full">
            <h3>🎯 Comparativo Normal vs Ao Leite</h3>
            <div class="chart-wrapper">
                <canvas id="graficoTiposRosca"></canvas>
            </div>
        </div>
    </div>

    <!-- ========== ABA TOP REVENDEDORES ========== -->
    <div id="aba-revendedores" class="conteudo-aba">
        <div class="chart-container grafico-full">
            <h3>🏆 Top 10 Revendedores por Faturamento</h3>
            <div class="chart-wrapper">
                <canvas id="graficoRevendedores"></canvas>
            </div>
        </div>

        <div class="chart-container grafico-full">
            <h3>📋 Ranking Detalhado</h3>
            <div class="table-container">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Posição</th>
                            <th>Revendedor</th>
                            <th>Nº Compras</th>
                            <th>Quantidade</th>
                            <th>Total Gasto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $top_revendedores->data_seek(0);
                        $posicao = 1;
                        while ($row = $top_revendedores->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?= $posicao ?>º</strong></td>
                                <td><?= htmlspecialchars($row['razao_social']) ?></td>
                                <td><?= $row['num_compras'] ?></td>
                                <td><?= number_format($row['quantidade_total'], 0, ',', '.') ?></td>
                                <td><strong>R$ <?= number_format(
                                    $row['total_gasto'],
                                    2,
                                    ',',
                                    '.',
                                ) ?></strong></td>
                            </tr>
                        <?php $posicao++;endwhile;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========== ABA EVOLUÇÃO TEMPORAL ========== -->
    <div id="aba-evolucao" class="conteudo-aba">
        <div class="chart-container grafico-full">
            <h3>📈 Evolução da Receita por Tipo (Últimos 12 Meses)</h3>
            <div class="chart-wrapper">
                <canvas id="graficoEvolucao"></canvas>
            </div>
        </div>
    </div>

    <!-- ========== ABA DETALHES ========== -->
    <div id="aba-detalhes" class="conteudo-aba">
        <div class="chart-container grafico-full">
            <h3>📋 Resumo Detalhado de Vendas</h3>
            <p style="color: #666; margin-bottom: 1.5rem; text-align: center;">Últimas 100 vendas no período selecionado</p>
            
            <div class="table-container">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nº Série</th>
                            <th>Data</th>
                            <th>Revendedor</th>
                            <th>Tipo Picolé</th>
                            <th>Quantidade</th>
                            <th>Valor NF</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $tabela_vendas->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['numero_serie']) ?></td>
                                <td><?= date('d/m/Y', strtotime($row['data'])) ?></td>
                                <td><?= htmlspecialchars($row['revendedor'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($row['tipo_picole'] ?? '—') ?></td>
                                <td><?= $row['quantidade'] ?? 0 ?></td>
                                <td>R$ <?= number_format($row['valor'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- ================= SCRIPTS CHART.JS ================= -->
<script>
// Configuração global dos gráficos
Chart.defaults.font.family = 'Arial, sans-serif';
Chart.defaults.color = '#333';

// ========== FUNÇÃO TROCAR ABAS ==========
function trocarAba(nomeAba) {
    // Esconder todas as abas
    document.querySelectorAll('.conteudo-aba').forEach(el => {
        el.classList.remove('ativo');
    });
    
    // Remover classe ativa dos botões
    document.querySelectorAll('.aba').forEach(el => {
        el.classList.remove('ativa');
    });
    
    // Mostrar aba selecionada
    document.getElementById('aba-' + nomeAba).classList.add('ativo');
    
    // Ativar botão correspondente
    event.target.classList.add('ativa');
}

// ========== GRÁFICO: TIPOS (PIZZA - DASHBOARD) ==========
new Chart(document.getElementById('graficoTiposPizza'), {
    type: 'pie',
    data: {
        labels: <?= json_encode($labels_tipos) ?>,
        datasets: [{
            data: <?= json_encode($dados_tipos) ?>,
            backgroundColor: ['rgba(73, 102, 140, 0.8)', 'rgba(242, 210, 114, 0.8)'],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// ========== GRÁFICO: RECEITA MENSAL (LINHA - DASHBOARD) ==========
new Chart(document.getElementById('graficoReceitaMensal'), {
    type: 'line',
    data: {
        labels: <?= json_encode($labels_meses) ?>,
        datasets: [{
            label: 'Receita (R$)',
            data: <?= json_encode($dados_meses) ?>,
            borderColor: 'rgba(73, 102, 140, 1)',
            backgroundColor: 'rgba(73, 102, 140, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'R$ ' + value.toLocaleString('pt-BR');
                    }
                }
            }
        }
    }
});

// ========== GRÁFICO: TIPOS (BARRAS) ==========
new Chart(document.getElementById('graficoTiposBarras'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels_tipos) ?>,
        datasets: [{
            label: 'Quantidade Vendida',
            data: <?= json_encode($dados_tipos) ?>,
            backgroundColor: ['rgba(73, 102, 140, 0.8)', 'rgba(242, 210, 114, 0.8)'],
            borderWidth: 2,
            borderColor: ['rgba(73, 102, 140, 1)', 'rgba(242, 210, 114, 1)']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// ========== GRÁFICO: TIPOS (ROSCA) ==========
new Chart(document.getElementById('graficoTiposRosca'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($labels_tipos) ?>,
        datasets: [{
            data: <?= json_encode($dados_tipos) ?>,
            backgroundColor: ['rgba(73, 102, 140, 0.8)', 'rgba(242, 210, 114, 0.8)'],
            borderWidth: 3,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// ========== GRÁFICO: TOP REVENDEDORES ==========
new Chart(document.getElementById('graficoRevendedores'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels_revendedores) ?>,
        datasets: [{
            label: 'Faturamento (R$)',
            data: <?= json_encode($dados_revendedores) ?>,
            backgroundColor: 'rgba(242, 210, 114, 0.8)',
            borderColor: 'rgba(242, 210, 114, 1)',
            borderWidth: 2
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'R$ ' + value.toLocaleString('pt-BR');
                    }
                }
            }
        }
    }
});

// ========== GRÁFICO: EVOLUÇÃO TEMPORAL (ÁREA EMPILHADA) ==========
new Chart(document.getElementById('graficoEvolucao'), {
    type: 'line',
    data: {
        labels: <?= json_encode($labels_meses) ?>,
        datasets: <?= json_encode($datasets_temporal) ?>
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                stacked: true,
                ticks: {
                    callback: function(value) {
                        return 'R$ ' + value.toLocaleString('pt-BR');
                    }
                }
            },
            x: {
                stacked: true
            }
        }
    }
});
</script>

</body>
</html>