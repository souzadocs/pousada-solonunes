<?php
// Desative exibição de erros no front-end para não quebrar o layout
error_reporting(0);

// 1. DADOS RECEBIDOS DA RESERVA
$id = (int)($_GET['id'] ?? 0);
$nome = $_GET['nome'] ?? 'Cliente';
$total = (float)($_GET['total'] ?? 0);
$tipo = $_GET['tipo'] ?? 'quarto'; // Pode ser 'quarto' ou 'passeio'

// Pega o link do Zap super detalhado que veio do arquivo salvar_reserva.php
$link_whatsapp = $_GET['wa'] ?? 'https://wa.me/5592993138119';

// Se não tiver ID ou total, redireciona pro início
if ($id === 0 || $total <= 0) {
    header("Location: ../index.php");
    exit;
}

$access_token = 'APP_USR-1119459203459502-022715-6e0cb6e2f12f914b8b57bcab3e31edf2-396338259'; 
// =========================================================================

$link_mercadopago = "";
$erro_api = "";

// 2. COMUNICAÇÃO COM O MERCADO PAGO (SOMENTE SE FOR QUARTO)
if ($tipo !== 'passeio' && !empty($access_token)) {
    
    // Monta os dados da cobrança
    $dados_pagamento = [
        "items" => [
            [
                "title" => "Reserva Solo Nunes - Hospedagem",
                "description" => "Pagamento de reserva de quarto",
                "quantity" => 1,
                "currency_id" => "BRL",
                "unit_price" => $total
            ]
        ],
        // O external_reference é crucial. É como o webhook vai saber O QUE foi pago
        "external_reference" => $tipo . "_" . $id, 
        
        // 🚨 MUDANÇA: Redireciona pro sucesso.php levando o link do Zap detalhado junto!
        "back_urls" => [
            "success" => "https://pousadasolonunes.com.br/php/sucesso.php?wa=" . urlencode($link_whatsapp),
            "failure" => "https://pousadasolonunes.com.br/index.php?status=falha",
            "pending" => "https://pousadasolonunes.com.br/index.php?status=pendente"
        ],
        "auto_return" => "approved"
    ];

    // Inicia a requisição cURL para a API do Mercado Pago
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.mercadopago.com/checkout/preferences',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($dados_pagamento),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token
        ],
    ]);

    $resposta = curl_exec($curl);
    $erro_curl = curl_error($curl);
    curl_close($curl);

    if ($erro_curl) {
        $erro_api = "Erro de conexão com o sistema de pagamentos.";
    } else {
        $json_resposta = json_decode($resposta, true);
        if (isset($json_resposta['init_point'])) {
            // init_point é o link da tela de pagamento segura do Mercado Pago
            $link_mercadopago = $json_resposta['init_point']; 
        } else {
            $erro_api = "Credenciais do Mercado Pago inválidas ou erro na configuração.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento da Reserva - Solo Nunes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white p-8 rounded-3xl shadow-xl w-full max-w-md border border-gray-100 text-center relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-2 bg-emerald-500"></div>

        <div class="mb-6 mt-4">
            <div class="w-16 h-16 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl shadow-sm">
                <i class="fa-solid fa-check"></i>
            </div>
            <h1 class="text-2xl font-black text-gray-800">Pedido Registrado!</h1>
            <p class="text-sm text-gray-500 mt-2">Olá, <strong><?= htmlspecialchars($nome) ?></strong>! Sua solicitação foi salva com sucesso.</p>
        </div>

        <div class="bg-gray-50 p-4 rounded-xl mb-6 border border-gray-100 flex justify-between items-center">
            <span class="text-sm font-bold text-gray-500 uppercase tracking-widest">Total:</span>
            <span class="text-2xl font-black text-emerald-600">R$ <?= number_format($total, 2, ',', '.') ?></span>
        </div>

        <p class="text-xs text-gray-400 mb-6 italic">Para garantir sua vaga no sistema, escolha uma forma de pagamento abaixo.</p>

        <div class="space-y-4">
            <?php if ($tipo !== 'passeio'): ?>
                <?php if (!empty($link_mercadopago)): ?>
                    <a href="<?= $link_mercadopago ?>" class="w-full bg-[#009EE3] hover:bg-[#0080B7] text-white font-bold py-4 rounded-xl shadow-lg transition-all flex items-center justify-center gap-3">
                        <i class="fa-regular fa-credit-card text-xl"></i> Pagar com Mercado Pago
                    </a>
                <?php else: ?>
                    <div class="p-3 bg-orange-50 border border-orange-200 rounded-xl text-orange-600 text-xs font-bold">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> O pagamento automático está em configuração. Por favor, conclua pelo WhatsApp.
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-blue-700 text-xs font-bold">
                    <i class="fa-solid fa-circle-info mr-1"></i> O agendamento de passeios é concluído exclusivamente via WhatsApp para confirmação de roteiros e horários.
                </div>
            <?php endif; ?>

            <a href="<?= $link_whatsapp ?>" target="_blank" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 rounded-xl shadow-md transition-all flex items-center justify-center gap-3">
                <i class="fa-brands fa-whatsapp text-xl"></i> Concluir via WhatsApp
            </a>
        </div>
        
        <div class="mt-8 pt-6 border-t border-gray-100">
            <a href="../index.php" class="text-xs font-bold text-gray-400 hover:text-gray-700 uppercase tracking-widest">Voltar para o site</a>
        </div>
    </div>

</body>
</html>