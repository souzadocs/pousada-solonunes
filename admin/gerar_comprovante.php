<?php
session_start();
if (!isset($_SESSION['logado'])) { exit; }
include '../api/db.php';
require_once '../libs/dompdf/autoload.inc.php'; // Caminho da biblioteca

use Dompdf\Dompdf;
use Dompdf\Options;

$id = $_GET['id'];

// Busca os dados da reserva e do quarto para o Solo Nunes
$sql = "SELECT r.*, q.nome as nome_quarto, q.preco_noite 
        FROM reservas r 
        JOIN quartos q ON r.quarto_id = q.id 
        WHERE r.id = '$id'";
$res = $conn->query($sql)->fetch_assoc();

// Cálculo de noites para o valor total
$d1 = new DateTime($res['checkin']);
$d2 = new DateTime($res['checkout']);
$noites = $d1->diff($d2)->days;
$total = $noites * $res['preco_noite'];

// Configuração do PDF
$options = new Options();
$options->set('isRemoteEnabled', true); // Para carregar a logo
$dompdf = new Dompdf($options);

$corStatus = ($res['status'] == 'pago') ? '#10b981' : '#f59e0b'; // Verde para Pago, Amarelo para Pendente
$textoStatus = ($res['status'] == 'pago') ? 'PAGAMENTO CONFIRMADO' : 'AGUARDANDO PAGAMENTO';

// HTML do Comprovante com o Status Dinâmico
$html = "
<div style='font-family: Arial, sans-serif; padding: 30px; border: 1px solid #eee;'>
    <div style='text-align: center; border-bottom: 2px solid #000; padding-bottom: 20px;'>
        <h1 style='margin: 0;'>POUSADA SOLO NUNES</h1>
        <p style='color: #666;'>Lírio do Vale - Manaus/AM | (92) 98170-2748</p>
    </div>

    <h2 style='text-align: center; margin-top: 10px;'>COMPROVANTE DE RESERVA</h2>

    <div style='margin-top: 30px;'>
        <p><strong>Hóspede:</strong> {$res['nome_cliente']}</p>
        <p><strong>Acomodação:</strong> {$res['nome_quarto']}</p>
        <p><strong>Check-in:</strong> " . date('d/m/Y', strtotime($res['checkin'])) . " (14:00h)</p>
        <p><strong>Check-out:</strong> " . date('d/m/Y', strtotime($res['checkout'])) . " (12:00h)</p>
        <p><strong>WhatsApp:</strong> {$res['whatsapp']}</p>
    </div>

    <div style='margin-top: 30px; background: #f9f9f9; padding: 20px; border-radius: 10px; border-left: 5px solid {$corStatus};'>
        <p style='margin: 0;'><strong>Valor Total da Estadia:</strong> R$ " . number_format($total, 2, ',', '.') . "</p>
        <p style='font-size: 12px; color: #888;'>* Status atualizado em: " . date('d/m/Y H:i') . "</p>
    </div>

    <div style='margin-top: 20px; text-align: right;'>
        <span style='background-color: {$corStatus}; color: white; padding: 10px 20px; border-radius: 5px; font-weight: bold; font-size: 14px;'>
            {$textoStatus}
        </span>
    </div>

    <div style='margin-top: 50px; font-size: 11px; color: #555; border-top: 1px solid #eee; padding-top: 20px;'>
        <p><strong>Observações Importantes:</strong></p>
        <ul>
            <li>Este documento serve como comprovante oficial de solicitação de reserva.</li>
            <li>O check-in antecipado está sujeito a disponibilidade na Pousada Solo Nunes.</li>
            <li>Não é permitido fumar nas dependências internas.</li>
        </ul>
    </div>
</div>";

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Envia o PDF para o navegador
$dompdf->stream("comprovante_reserva_{$id}.pdf", ["Attachment" => false]);
?>