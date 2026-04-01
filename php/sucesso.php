<?php
// Captura o link do WhatsApp que passamos via URL pelo Mercado Pago
$link_whatsapp = isset($_GET['wa']) ? $_GET['wa'] : 'https://wa.me/5592993138119';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Aprovado - Solo Nunes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md border border-emerald-100 text-center relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-2 bg-emerald-500"></div>

        <div class="mb-6 mt-4">
            <div class="w-20 h-20 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl shadow-sm">
                <i class="fa-solid fa-check-double"></i>
            </div>
            <h1 class="text-3xl font-black text-gray-800">Tudo Certo!</h1>
            <p class="text-sm text-gray-500 mt-3 leading-relaxed">
                Seu pagamento foi aprovado pelo Mercado Pago. A sua vaga na Pousada Solo Nunes já está garantida!
            </p>
        </div>

        <div class="bg-emerald-50 border border-emerald-200 p-5 rounded-xl mb-6 shadow-inner">
            <p class="text-sm font-bold text-emerald-800 mb-1"><i class="fa-regular fa-bell"></i> Falta só um detalhe!</p>
            <p class="text-xs text-emerald-600 mt-2 leading-relaxed">
                Clique no botão abaixo para nos avisar no WhatsApp. Assim, já organizamos os detalhes da sua chegada (e dos seus passeios, caso tenha escolhido algum).
            </p>
        </div>

        <a href="<?= $link_whatsapp ?>" target="_blank" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 rounded-xl shadow-lg transition-all flex items-center justify-center gap-3 animate-pulse">
            <i class="fa-brands fa-whatsapp text-2xl"></i> Enviar Mensagem Agora
        </a>
        
        <div class="mt-8 pt-6 border-t border-gray-100">
            <a href="../index.php" class="text-xs font-bold text-gray-400 hover:text-gray-700 uppercase tracking-widest">Voltar para a página inicial</a>
        </div>
    </div>

</body>
</html>