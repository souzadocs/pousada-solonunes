<?php
include 'db.php';

$access_token = 'APP_USR-1119459203459502-022715-6e0cb6e2f12f914b8b57bcab3e31edf2-396338259'; 

// --- SISTEMA DE LOG PARA DESCOBRIR O ERRO ---
$log_file = 'log_mp.txt';
$hora = date('Y-m-d H:i:s');

$input = file_get_contents('php://input');
$dados = json_decode($input, true);

// Grava o que o Mercado Pago mandou
file_put_contents($log_file, "[$hora] RECEBIDO WEBHOOK:\nGET: " . print_r($_GET, true) . "\nBODY: $input\n", FILE_APPEND);

// Captura o ID do pagamento de todas as formas possíveis (Webhook ou IPN)
$pagamento_id = $dados['data']['id'] ?? $_GET['data_id'] ?? $_GET['id'] ?? null;

if ($pagamento_id) {
    file_put_contents($log_file, "[$hora] ID do Pagamento capturado: $pagamento_id\n", FILE_APPEND);

    // Consulta o Mercado Pago para confirmar se o pagamento é real
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.mercadopago.com/v1/payments/" . $pagamento_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer " . $access_token
        ],
    ]);
    
    $response = curl_exec($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    file_put_contents($log_file, "[$hora] Resposta API MP (Status $http_status): $response\n", FILE_APPEND);

    $info_pagamento = json_decode($response, true);
    
    // Verifica se o status é "Aprovado"
    if (isset($info_pagamento['status']) && $info_pagamento['status'] === 'approved') {
        
        $referencia = $info_pagamento['external_reference'] ?? '';
        file_put_contents($log_file, "[$hora] Pagamento APROVADO! Referencia Externa: $referencia\n", FILE_APPEND);
        
        if ($referencia) {
            // Tenta dividir a referência (ex: "quarto_15" ou apenas "15")
            if (strpos($referencia, '_') !== false) {
                $partes = explode("_", $referencia);
                $tipo = $partes[0]; // 'quarto' ou 'passeio'
                $reserva_id = (int)$partes[1];
            } else {
                // Se a referência vier só o número, assume que é quarto
                $tipo = 'quarto';
                $reserva_id = (int)$referencia;
            }
            
            file_put_contents($log_file, "[$hora] Atualizando Banco... Tipo: $tipo, ID: $reserva_id\n", FILE_APPEND);

            if ($tipo === 'quarto') {
                $stmt = $conn->prepare("UPDATE reservas SET status = 'pago' WHERE id = ?");
            } elseif ($tipo === 'passeio') {
                $stmt = $conn->prepare("UPDATE reservas_passeios SET status = 'pago' WHERE id = ?");
            }

            if (isset($stmt)) {
                $stmt->bind_param("i", $reserva_id);
                if($stmt->execute()){
                    file_put_contents($log_file, "[$hora] SUCESSO: Banco de dados atualizado para PAGO!\n\n", FILE_APPEND);
                } else {
                    file_put_contents($log_file, "[$hora] ERRO AO ATUALIZAR BANCO: " . $stmt->error . "\n\n", FILE_APPEND);
                }
                $stmt->close();
            }
        } else {
            file_put_contents($log_file, "[$hora] ERRO: O campo external_reference veio vazio do Mercado Pago.\n\n", FILE_APPEND);
        }
    } else {
        file_put_contents($log_file, "[$hora] Pagamento nao esta com status 'approved'. Status atual: " . ($info_pagamento['status'] ?? 'N/A') . "\n\n", FILE_APPEND);
    }
} else {
    file_put_contents($log_file, "[$hora] ERRO: Nenhum ID de pagamento foi recebido no Webhook.\n\n", FILE_APPEND);
}

// Responde "OK" para o Mercado Pago
http_response_code(200);
echo "OK";
?>