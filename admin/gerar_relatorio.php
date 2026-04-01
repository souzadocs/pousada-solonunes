<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['nivel'] !== 'admin') { exit; }
include '../api/db.php';
require_once '../libs/dompdf/autoload.inc.php'; 

use Dompdf\Dompdf;
use Dompdf\Options;

$data_inicio = $_GET['inicio'] ?? date('Y-m-01');
$data_fim = $_GET['fim'] ?? date('Y-m-t');

$inicio_esc = $conn->real_escape_string($data_inicio);
$fim_esc = $conn->real_escape_string($data_fim);

// Formatação para exibição
$data_in_fmt = date('d/m/Y', strtotime($data_inicio));
$data_out_fmt = date('d/m/Y', strtotime($data_fim));

// 1. Busca Resumo de Quartos
$sql_quartos = "SELECT q.nome, COUNT(r.id) as qtd, SUM(r.valor_total) as faturamento 
                FROM reservas r JOIN quartos q ON r.quarto_id = q.id 
                WHERE r.status = 'pago' AND r.checkin BETWEEN '$inicio_esc' AND '$fim_esc' 
                GROUP BY q.id";
$res_quartos = $conn->query($sql_quartos);
$total_quartos = 0;
$linhas_quartos = "";

if($res_quartos && $res_quartos->num_rows > 0) {
    while($row = $res_quartos->fetch_assoc()){
        $total_quartos += $row['faturamento'];
        $linhas_quartos .= "<tr>
            <td style='padding: 10px; border-bottom: 1px solid #ddd;'>{$row['nome']}</td>
            <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center;'>{$row['qtd']}</td>
            <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: right;'>R$ " . number_format($row['faturamento'], 2, ',', '.') . "</td>
        </tr>";
    }
} else {
    $linhas_quartos = "<tr><td colspan='3' style='padding: 10px; text-align: center; color: #888;'>Nenhuma reserva de quarto no período.</td></tr>";
}

// 2. Busca Resumo de Passeios
$sql_passeios = "SELECT p.nome, COUNT(rp.id) as qtd, SUM(rp.valor_total) as faturamento 
                FROM reservas_passeios rp JOIN passeios p ON rp.passeio_id = p.id 
                WHERE rp.status = 'pago' AND rp.checkin BETWEEN '$inicio_esc' AND '$fim_esc' 
                GROUP BY p.id";
$res_passeios = $conn->query($sql_passeios);
$total_passeios = 0;
$linhas_passeios = "";

if($res_passeios && $res_passeios->num_rows > 0) {
    while($row = $res_passeios->fetch_assoc()){
        $total_passeios += $row['faturamento'];
        $linhas_passeios .= "<tr>
            <td style='padding: 10px; border-bottom: 1px solid #ddd;'>{$row['nome']}</td>
            <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center;'>{$row['qtd']}</td>
            <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: right;'>R$ " . number_format($row['faturamento'], 2, ',', '.') . "</td>
        </tr>";
    }
} else {
    $linhas_passeios = "<tr><td colspan='3' style='padding: 10px; text-align: center; color: #888;'>Nenhum passeio vendido no período.</td></tr>";
}

$faturamento_geral = $total_quartos + $total_passeios;

// HTML do Relatório em PDF
$html = "
<div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
    
    <div style='text-align: center; border-bottom: 3px solid #10b981; padding-bottom: 15px; margin-bottom: 30px;'>
        <h1 style='margin: 0; text-transform: uppercase; letter-spacing: 2px; color: #1f2937;'>RELATÓRIO DE DESEMPENHO</h1>
        <p style='color: #6b7280; font-size: 14px;'>Pousada Solo Nunes | {$data_in_fmt} a {$data_out_fmt}</p>
    </div>

    <div style='background: #f9fafb; border-left: 5px solid #10b981; padding: 20px; border-radius: 5px; margin-bottom: 40px;'>
        <p style='margin: 0; font-size: 12px; font-weight: bold; color: #9ca3af; text-transform: uppercase;'>Faturamento Confirmado Geral</p>
        <h2 style='margin: 5px 0 0 0; color: #10b981; font-size: 32px;'>R$ " . number_format($faturamento_geral, 2, ',', '.') . "</h2>
    </div>

    <h3 style='color: #1f2937; border-bottom: 1px solid #eee; padding-bottom: 5px;'>Resumo por Acomodação</h3>
    <table style='width: 100%; border-collapse: collapse; margin-bottom: 40px; font-size: 14px;'>
        <thead>
            <tr style='background-color: #10b981; color: white;'>
                <th style='padding: 10px; text-align: left;'>Quarto / Suíte</th>
                <th style='padding: 10px; text-align: center;'>Qtd. Reservas</th>
                <th style='padding: 10px; text-align: right;'>Total Faturado</th>
            </tr>
        </thead>
        <tbody>
            {$linhas_quartos}
            <tr>
                <td colspan='2' style='padding: 10px; text-align: right; font-weight: bold;'>Subtotal Hospedagem:</td>
                <td style='padding: 10px; text-align: right; font-weight: bold; color: #10b981;'>R$ " . number_format($total_quartos, 2, ',', '.') . "</td>
            </tr>
        </tbody>
    </table>

    <h3 style='color: #1f2937; border-bottom: 1px solid #eee; padding-bottom: 5px;'>Resumo por Passeios Avulsos</h3>
    <table style='width: 100%; border-collapse: collapse; margin-bottom: 40px; font-size: 14px;'>
        <thead>
            <tr style='background-color: #ea580c; color: white;'>
                <th style='padding: 10px; text-align: left;'>Nome do Passeio</th>
                <th style='padding: 10px; text-align: center;'>Qtd. Vendas</th>
                <th style='padding: 10px; text-align: right;'>Total Faturado</th>
            </tr>
        </thead>
        <tbody>
            {$linhas_passeios}
            <tr>
                <td colspan='2' style='padding: 10px; text-align: right; font-weight: bold;'>Subtotal Passeios:</td>
                <td style='padding: 10px; text-align: right; font-weight: bold; color: #ea580c;'>R$ " . number_format($total_passeios, 2, ',', '.') . "</td>
            </tr>
        </tbody>
    </table>

    <div style='text-align: center; margin-top: 50px; font-size: 10px; color: #9ca3af;'>
        Relatório gerado automaticamente pelo Sistema Gerencial Solo Nunes em " . date('d/m/Y H:i') . "
    </div>
</div>";

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("relatorio_solo_nunes_{$data_in_fmt}_a_{$data_out_fmt}.pdf", ["Attachment" => false]);
?>