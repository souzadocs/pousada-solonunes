<?php
session_start();
if (!isset($_SESSION['logado'])) { header("Location: login.php"); exit(); }
include '../api/db.php';

if ($_SESSION['nivel'] !== 'admin') {
    echo "<script>alert('Acesso negado: Apenas administradores podem ver o financeiro.'); window.location.href='dashboard.php';</script>";
    exit();
}

// 1. Capturar datas do filtro
$data_inicio = $_GET['data_inicio'] ?? date('Y-m-01');
$data_fim = $_GET['data_fim'] ?? date('Y-m-t');

$inicio_esc = $conn->real_escape_string($data_inicio);
$fim_esc = $conn->real_escape_string($data_fim);

// 2. Faturamento Filtrado (Hospedagem e Passeios)
$fat_quartos_sql = "SELECT SUM(valor_total) as total FROM reservas 
                    WHERE status = 'pago' AND checkin BETWEEN '$inicio_esc' AND '$fim_esc'";
$fat_quartos = $conn->query($fat_quartos_sql)->fetch_assoc()['total'] ?? 0;

$fat_passeios_sql = "SELECT SUM(valor_total) as total FROM reservas_passeios 
                     WHERE status = 'pago' AND checkin BETWEEN '$inicio_esc' AND '$fim_esc'";
$fat_passeios = $conn->query($fat_passeios_sql)->fetch_assoc()['total'] ?? 0;

$faturamento_geral = $fat_quartos + $fat_passeios;

// 3. Mais Populares no Período
$popular_q_sql = "SELECT q.nome, COUNT(r.id) as total FROM reservas r 
                  JOIN quartos q ON r.quarto_id = q.id 
                  WHERE r.checkin BETWEEN '$inicio_esc' AND '$fim_esc' AND r.status != 'bloqueado' 
                  GROUP BY q.id ORDER BY total DESC LIMIT 1";
$quarto_popular = $conn->query($popular_q_sql)->fetch_assoc();

$popular_p_sql = "SELECT p.nome, COUNT(rp.id) as total FROM reservas_passeios rp 
                  JOIN passeios p ON rp.passeio_id = p.id 
                  WHERE rp.checkin BETWEEN '$inicio_esc' AND '$fim_esc' AND rp.status != 'bloqueado' 
                  GROUP BY p.id ORDER BY total DESC LIMIT 1";
$passeio_popular = $conn->query($popular_p_sql)->fetch_assoc();

// 4. Histórico Mensal (Construindo matriz de dados para Tabela e Gráfico)
$hist_quartos = $conn->query("SELECT MONTH(checkin) as mes, YEAR(checkin) as ano, SUM(valor_total) as total FROM reservas WHERE status = 'pago' GROUP BY ano, mes");
$hist_passeios = $conn->query("SELECT MONTH(checkin) as mes, YEAR(checkin) as ano, SUM(valor_total) as total FROM reservas_passeios WHERE status = 'pago' GROUP BY ano, mes");

$dados_mensais = [];

// Popula matriz com quartos
while($r = $hist_quartos->fetch_assoc()) {
    $chave = $r['ano'] . '-' . str_pad($r['mes'], 2, '0', STR_PAD_LEFT);
    $dados_mensais[$chave] = [
        'label' => str_pad($r['mes'], 2, '0', STR_PAD_LEFT) . '/' . $r['ano'],
        'quartos' => (float)$r['total'],
        'passeios' => 0
    ];
}

// Popula matriz com passeios
while($r = $hist_passeios->fetch_assoc()) {
    $chave = $r['ano'] . '-' . str_pad($r['mes'], 2, '0', STR_PAD_LEFT);
    if (!isset($dados_mensais[$chave])) {
        $dados_mensais[$chave] = [
            'label' => str_pad($r['mes'], 2, '0', STR_PAD_LEFT) . '/' . $r['ano'],
            'quartos' => 0,
            'passeios' => 0
        ];
    }
    $dados_mensais[$chave]['passeios'] = (float)$r['total'];
}

// Ordena do mês mais recente para o mais antigo
krsort($dados_mensais);

// Prepara arrays para o Gráfico (Chart.js precisa da ordem cronológica, então invertemos)
$grafico_labels = [];
$grafico_quartos = [];
$grafico_passeios = [];
foreach (array_reverse($dados_mensais) as $dado) {
    $grafico_labels[] = $dado['label'];
    $grafico_quartos[] = $dado['quartos'];
    $grafico_passeios[] = $dado['passeios'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Relatórios - Solo Nunes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 text-gray-900">

    <nav class="bg-black text-white p-4 flex justify-between items-center shadow-lg z-50 relative">
        <h1 class="font-bold uppercase tracking-widest text-xs md:text-sm">Relatórios Gerenciais</h1>
        <div class="flex gap-2 md:gap-6 text-sm items-center">
            <a href="dashboard.php" class="hover:text-emerald-500 transition-colors font-medium bg-gray-800 px-3 py-2 rounded-lg text-xs md:text-sm flex items-center gap-2">
                <i class="fa-solid fa-house"></i><span class="hidden md:inline">Painel</span>
            </a>
            <a href="gerar_relatorio.php?inicio=<?= $data_inicio ?>&fim=<?= $data_fim ?>" 
                target="_blank" class="bg-blue-600 px-3 py-2 md:px-4 md:py-2 rounded-lg font-bold hover:bg-blue-700 transition-all flex items-center gap-2 text-xs md:text-sm shadow-sm">
                <i class="fa-solid fa-file-pdf"></i><span class="hidden md:inline">Exportar PDF</span>
            </a>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-4 md:p-8">
        
        <section class="mb-8">
            <form method="GET" class="flex flex-col md:flex-row md:flex-wrap items-start md:items-end gap-4 bg-white p-5 md:p-6 rounded-3xl border border-gray-100 shadow-sm">
                <div class="w-full md:flex-1 md:min-w-[200px]">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Início do Período</label>
                    <input type="date" name="data_inicio" value="<?= $data_inicio ?>" 
                           class="w-full p-3 bg-gray-50 border rounded-xl outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                </div>
                <div class="w-full md:flex-1 md:min-w-[200px]">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Fim do Período</label>
                    <input type="date" name="data_fim" value="<?= $data_fim ?>" 
                           class="w-full p-3 bg-gray-50 border rounded-xl outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                </div>
                <button type="submit" class="w-full md:w-auto bg-black text-white px-8 py-3 rounded-xl font-bold hover:bg-emerald-600 transition-all uppercase tracking-widest text-xs mt-2 md:mt-0 shadow-md">
                    Aplicar Filtro
                </button>
            </form>
        </section>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 p-6 rounded-3xl shadow-md text-white flex flex-col justify-center relative overflow-hidden">
                <div class="absolute -right-4 -top-4 opacity-10 text-9xl">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
                <p class="text-emerald-100 text-[10px] font-bold uppercase tracking-widest mb-1 relative z-10">Faturamento no Período</p>
                <h3 class="text-3xl md:text-4xl font-black mb-2 relative z-10">R$ <?= number_format($faturamento_geral, 2, ',', '.') ?></h3>
                <div class="flex flex-col sm:flex-row sm:justify-between text-xs mt-4 border-t border-emerald-400/50 pt-3 relative z-10 gap-2">
                    <span><i class="fa-solid fa-bed mr-1 opacity-70"></i> R$ <?= number_format($fat_quartos, 2, ',', '.') ?></span>
                    <span><i class="fa-solid fa-compass mr-1 opacity-70"></i> R$ <?= number_format($fat_passeios, 2, ',', '.') ?></span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-center transition-all hover:shadow-md">
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-2"><i class="fa-solid fa-fire text-orange-500 mr-1"></i> Acomodação Pop</p>
                <h3 class="text-xl md:text-2xl font-bold text-emerald-800 truncate"><?= $quarto_popular['nome'] ?? 'Nenhuma' ?></h3>
                <span class="text-xs font-bold text-emerald-500 bg-emerald-50 px-2 py-1 rounded-md mt-2 self-start"><?= $quarto_popular['total'] ?? 0 ?> reservas filtradas</span>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-center transition-all hover:shadow-md">
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-2"><i class="fa-solid fa-fire text-orange-500 mr-1"></i> Passeio Pop</p>
                <h3 class="text-xl md:text-2xl font-bold text-orange-600 truncate"><?= $passeio_popular['nome'] ?? 'Nenhum' ?></h3>
                <span class="text-xs font-bold text-orange-500 bg-orange-50 px-2 py-1 rounded-md mt-2 self-start"><?= $passeio_popular['total'] ?? 0 ?> vendas filtradas</span>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-12">
            
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-5 md:p-6">
                <h4 class="font-bold text-lg mb-6 text-gray-800"><i class="fa-solid fa-chart-column mr-2 text-blue-500"></i> Gráfico de Receitas</h4>
                <div class="relative w-full h-64 md:h-72">
                    <canvas id="graficoFaturamento"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                <div class="p-5 md:p-6 border-b flex justify-between items-center bg-gray-50">
                    <h4 class="font-bold text-lg text-gray-800">Histórico Consolidado</h4>
                    <i class="fa-solid fa-table-list text-gray-400 text-xl"></i>
                </div>
                
                <div class="overflow-x-auto w-full pb-2">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead class="bg-white text-gray-400 text-[10px] uppercase font-bold tracking-widest border-b">
                            <tr>
                                <th class="p-4 text-center">Mês/Ano</th>
                                <th class="p-4 text-right">Hospedagem</th>
                                <th class="p-4 text-right">Passeios</th>
                                <th class="p-4 text-right">Total Geral</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <?php foreach($dados_mensais as $dado): ?>
                            <tr class="border-b hover:bg-gray-50 transition-colors">
                                <td class="p-4 text-center font-black text-gray-700"><?= $dado['label'] ?></td>
                                <td class="p-4 text-right text-emerald-600 font-medium">R$ <?= number_format($dado['quartos'], 2, ',', '.') ?></td>
                                <td class="p-4 text-right text-orange-600 font-medium">R$ <?= number_format($dado['passeios'], 2, ',', '.') ?></td>
                                <td class="p-4 text-right font-black text-gray-900 bg-gray-50">R$ <?= number_format($dado['quartos'] + $dado['passeios'], 2, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($dados_mensais)): ?>
                                <tr><td colspan="4" class="p-6 text-center text-gray-400 italic text-sm">Nenhum dado financeiro encontrado.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <script>
        const ctx = document.getElementById('graficoFaturamento').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($grafico_labels) ?>,
                datasets: [
                    {
                        label: 'Hospedagem (R$)',
                        data: <?= json_encode($grafico_quartos) ?>,
                        backgroundColor: '#10b981', // emerald-500
                        borderRadius: 4
                    },
                    {
                        label: 'Passeios (R$)',
                        data: <?= json_encode($grafico_passeios) ?>,
                        backgroundColor: '#ea580c', // orange-600
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true }
                },
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 10 } } // Legenda menor para celular
                    }
                }
            }
        });
    </script>
</body>
</html>