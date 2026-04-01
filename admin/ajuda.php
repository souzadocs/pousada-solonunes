<?php
session_start();
if (!isset($_SESSION['logado'])) { header("Location: login.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajuda - Sistema Solo Nunes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Suaviza a rolagem da página ao clicar nos links do índice */
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 leading-relaxed">

    <nav class="bg-black text-white p-4 flex justify-between items-center shadow-lg sticky top-0 z-50">
        <div class="flex items-center gap-4">
            <h1 class="font-bold uppercase tracking-widest text-sm"><i class="fa-solid fa-book-open text-emerald-400 mr-2"></i> Manual do Sistema</h1>
        </div>
        <div class="flex gap-4 md:gap-6 text-xs md:text-sm font-medium">
            <a href="dashboard.php" class="hover:text-emerald-500 transition-colors flex items-center gap-1"><i class="fa-solid fa-arrow-left"></i> <span class="hidden md:inline">Voltar ao Painel</span></a>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto p-4 md:p-8">
        
        <header class="mb-10 mt-4 text-center">
            <h2 class="text-3xl md:text-4xl font-black text-gray-800">Como usar o seu site?</h2>
            <p class="text-gray-500 mt-3 text-lg">Um guia passo a passo, simples e fácil, para você dominar a Pousada Solo Nunes.</p>
        </header>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 mb-10">
            <h3 class="font-bold text-gray-700 mb-4 uppercase tracking-widest text-xs"><i class="fa-solid fa-compass mr-2"></i> Ir direto para:</h3>
            <div class="flex flex-wrap gap-3">
                <a href="#reservas" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold transition">1. Painel de Reservas</a>
                <a href="#quartos" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 px-4 py-2 rounded-lg text-sm font-bold transition">2. Como Gerenciar Quartos</a>
                <a href="#passeios" class="bg-blue-50 hover:bg-blue-100 text-blue-700 px-4 py-2 rounded-lg text-sm font-bold transition">3. Como Gerenciar Passeios</a>
                <a href="#bloqueios" class="bg-orange-50 hover:bg-orange-100 text-orange-700 px-4 py-2 rounded-lg text-sm font-bold transition">4. Interditar Vagas (Bloqueios)</a>
                <a href="#seguranca" class="bg-red-50 hover:bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm font-bold transition">5. Senha Master</a>
            </div>
        </div>

        <div class="space-y-12">
            
            <section id="reservas" class="bg-white p-6 md:p-10 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="text-2xl font-black mb-6 flex items-center gap-3 text-gray-800 border-b pb-4">
                    <span class="bg-gray-800 text-white w-10 h-10 flex items-center justify-center rounded-xl text-lg">1</span>
                    O Painel Principal (Dashboard)
                </h3>
                <p class="text-gray-600 mb-6 text-lg">O Dashboard é a tela inicial. É lá que caem todos os pedidos feitos pelos clientes no seu site. As reservas funcionam como um "semáforo" de trânsito:</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-yellow-50 border border-yellow-200 p-5 rounded-2xl">
                        <h4 class="font-bold text-yellow-700 mb-2 flex items-center gap-2"><i class="fa-solid fa-clock"></i> PENDENTE</h4>
                        <p class="text-sm text-gray-600">O cliente preencheu os dados no site, mas ainda não pagou ou o sistema (Mercado Pago) ainda não confirmou o pagamento. O quarto fica reservado aguardando.</p>
                    </div>
                    <div class="bg-emerald-50 border border-emerald-200 p-5 rounded-2xl">
                        <h4 class="font-bold text-emerald-700 mb-2 flex items-center gap-2"><i class="fa-solid fa-circle-check"></i> PAGO</h4>
                        <p class="text-sm text-gray-600">O dinheiro caiu na conta! O cliente já garantiu a vaga. A partir daqui, o PDF do comprovante já sai com a marca verde de "Pago".</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 p-5 rounded-2xl">
                        <h4 class="font-bold text-blue-700 mb-2 flex items-center gap-2"><i class="fa-solid fa-flag-checkered"></i> FINALIZADO</h4>
                        <p class="text-sm text-gray-600">O cliente já se hospedou, já foi embora e a estadia terminou. Serve para o seu histórico e organização.</p>
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200">
                    <h4 class="font-bold text-gray-800 mb-3">O que os botões fazem?</h4>
                    <ul class="space-y-4 text-gray-600">
                        <li class="flex gap-3"><span class="bg-black text-white px-2 py-1 rounded text-xs font-bold h-fit mt-0.5">CONFIRMAR</span> Se o cliente pagou por PIX manual no WhatsApp e não pelo Mercado Pago automático, você clica aqui para avisar o sistema que o dinheiro entrou. Ele muda de Pendente para Pago.</li>
                        <li class="flex gap-3"><span class="bg-blue-600 text-white px-2 py-1 rounded text-xs font-bold h-fit mt-0.5">FINALIZAR</span> Clica aqui quando o hóspede for embora da pousada, para arquivar a reserva.</li>
                        <li class="flex gap-3"><span class="bg-white border-2 border-gray-200 text-gray-600 px-2 py-1 rounded text-xs font-bold h-fit mt-0.5">PDF</span> Gera um recibo bonitão pronto para mandar para o WhatsApp do cliente.</li>
                    </ul>
                </div>
            </section>

            <section id="quartos" class="bg-white p-6 md:p-10 rounded-3xl shadow-sm border border-emerald-100 border-t-4 border-t-emerald-500">
                <h3 class="text-2xl font-black mb-6 flex items-center gap-3 text-emerald-800 border-b border-emerald-100 pb-4">
                    <span class="bg-emerald-500 text-white w-10 h-10 flex items-center justify-center rounded-xl text-lg">2</span>
                    Como Adicionar e Editar Quartos
                </h3>
                <p class="text-gray-600 mb-6 text-lg">Esta é a vitrine da sua Pousada. Tudo o que você coloca aqui, aparece automaticamente para o cliente comprar no site principal.</p>

                <div class="space-y-8">
                    <div>
                        <h4 class="font-black text-lg text-gray-800 mb-2">Passo 1: O "Nome Base" (O Segredo do Agrupamento)</h4>
                        <p class="text-gray-600 mb-3">Imagine que você tem dois <strong>"Quartos Casal"</strong> (um no Térreo e um no 1º Andar). Para o painel não virar uma bagunça, o sistema agrupa eles como uma "sanfona".</p>
                        <p class="text-gray-600 bg-emerald-50 p-4 rounded-xl border border-emerald-100">
                            <strong>Como fazer:</strong> Na hora de adicionar, escreva o "Nome Base" exatamente igual (ex: <em>Suíte Casal</em>). Em seguida, no campo "Andar / Opção", você escolhe Térreo para o primeiro, e depois cadastra de novo escolhendo 1º Andar. O sistema vai criar uma "Gaveta" chamada Suíte Casal e guardar os dois lá dentro!
                        </p>
                    </div>

                    <div>
                        <h4 class="font-black text-lg text-gray-800 mb-2">Passo 2: Unidades e Pessoas</h4>
                        <ul class="list-disc pl-5 space-y-2 text-gray-600">
                            <li><strong>Unidades:</strong> Quantos quartos IDÊNTICOS a esse existem? (Se você tem 3 quartos de Casal no Térreo, coloque "3". Assim o sistema permite vender 3 vezes para o mesmo dia antes de dar lotado).</li>
                            <li><strong>Pessoas:</strong> Quantas pessoas cabem lá dentro. O site usa isso para saber se a família do cliente cabe no quarto.</li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-black text-lg text-gray-800 mb-2">Passo 3: Como Editar um quarto que já existe?</h4>
                        <p class="text-gray-600 mb-3">Clicando no botão <span class="text-blue-600 font-bold border border-blue-200 px-2 rounded bg-blue-50">Editar</span> embaixo de um quarto, uma janela vai se abrir. Lá você pode mudar o preço, o tipo de cama, corrigir um erro de digitação, e até enviar fotos novas. <em>Atenção: Ao enviar fotos novas, as antigas são apagadas e substituídas pelas novas!</em></p>
                    </div>
                </div>
            </section>

            <section id="passeios" class="bg-white p-6 md:p-10 rounded-3xl shadow-sm border border-blue-100 border-t-4 border-t-blue-500">
                <h3 class="text-2xl font-black mb-6 flex items-center gap-3 text-blue-800 border-b border-blue-100 pb-4">
                    <span class="bg-blue-600 text-white w-10 h-10 flex items-center justify-center rounded-xl text-lg">3</span>
                    Como Gerenciar Passeios e Experiências
                </h3>
                <p class="text-gray-600 mb-6 text-lg">Os passeios são serviços extras que você vende para o cliente, independentemente do quarto. Eles aparecem no site para o cliente agendar.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                    <div>
                        <h4 class="font-black text-gray-800 mb-2">Adicionando um Passeio:</h4>
                        <ol class="list-decimal pl-5 space-y-2 text-gray-600">
                            <li>Digite o nome (Ex: <em>Focagem de Jacaré</em>).</li>
                            <li>Coloque o <strong>Máximo de Pessoas</strong> que cabem no barco/carro daquele passeio.</li>
                            <li>Coloque o <strong>Preço por Pessoa</strong> (o site vai multiplicar automaticamente pela quantidade de pessoas da família).</li>
                            <li>Faça uma descrição atrativa e adicione fotos bonitas da natureza.</li>
                        </ol>
                    </div>
                    <div class="bg-blue-50 p-5 rounded-2xl border border-blue-200">
                        <h4 class="font-bold text-blue-800 mb-2"><i class="fa-solid fa-lightbulb text-yellow-500"></i> Como o cliente compra?</h4>
                        <p class="text-sm text-gray-700">Quando o cliente estiver fechando um quarto, o site vai perguntar: <em>"Quer adicionar um passeio na sua viagem?"</em>. Se ele escolher, o valor do passeio soma junto com o valor do quarto no carrinho final!</p>
                    </div>
                </div>
            </section>

            <section id="bloqueios" class="bg-white p-6 md:p-10 rounded-3xl shadow-sm border border-orange-100 border-t-4 border-t-orange-500">
                <h3 class="text-2xl font-black mb-6 flex items-center gap-3 text-orange-800 border-b border-orange-100 pb-4">
                    <span class="bg-orange-500 text-white w-10 h-10 flex items-center justify-center rounded-xl text-lg">4</span>
                    Como Interditar (Bloquear) uma Data
                </h3>
                <p class="text-gray-600 mb-6 text-lg">E se o ar-condicionado de um quarto queimar? Ou se o barco do passeio for para a manutenção? Você precisa tirar essa vaga do site para ninguém comprar. Isso se chama <strong>Bloqueio</strong>.</p>

                <div class="space-y-4 text-gray-600">
                    <p><strong>Passo a passo para bloquear:</strong></p>
                    <ol class="list-decimal pl-5 space-y-3 bg-gray-50 p-5 rounded-2xl border border-gray-200">
                        <li>Vá até a página de Gerenciar e encontre o Quarto ou Passeio que deseja bloquear.</li>
                        <li>Clique no botão <span class="bg-orange-50 text-orange-600 border border-orange-200 px-2 py-0.5 rounded font-bold text-xs"><i class="fa-solid fa-lock"></i> Bloquear</span>.</li>
                        <li>O sistema vai pedir a data inicial e a data final da interdição.</li>
                        <li>Escreva um motivo para você lembrar depois (Ex: <em>Pintando a parede</em> ou <em>Vendido no balcão por fora</em>).</li>
                        <li>Digite a sua Senha Master para confirmar.</li>
                    </ol>
                    <p class="mt-4">Pronto! Aquelas datas desaparecem do site imediatamente. O bloqueio vai ficar visível lá no final da página de Gerenciamento, na seção <strong>"Áreas Interditadas"</strong>. Quando o conserto acabar, você pode clicar em "Liberar Vaga" para devolver o quarto para as vendas do site.</p>
                </div>
            </section>

            <section id="seguranca" class="bg-white p-6 md:p-10 rounded-3xl shadow-sm border border-red-100 border-t-4 border-t-red-500">
                <h3 class="text-2xl font-black mb-6 flex items-center gap-3 text-red-800 border-b border-red-100 pb-4">
                    <span class="bg-red-500 text-white w-10 h-10 flex items-center justify-center rounded-xl text-lg">5</span>
                    A Senha Master (Sua Chave de Ouro)
                </h3>
                
                <div class="flex flex-col md:flex-row gap-8 items-center">
                    <div class="md:w-2/3 space-y-4 text-gray-600">
                        <p>O sistema lida com dinheiro e reservas de clientes. Para evitar que um funcionário (ou você mesmo sem querer) clique em "Excluir" e apague um quarto ou uma reserva paga por acidente, o sistema tem uma trava de segurança.</p>
                        <p>Sempre que você for fazer uma <strong>Ação Crítica</strong> (Editar preço, Apagar reserva, Excluir quarto, Bloquear datas), o sistema vai abrir uma janela pedindo a sua <strong>Senha Master</strong>.</p>
                        <p><strong>A senha padrão é:</strong> <span class="bg-gray-200 px-3 py-1 text-black font-mono font-bold tracking-widest rounded">nunes2026</span></p>
                    </div>
                    <div class="md:w-1/3 flex justify-center text-red-500 text-6xl opacity-20">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                </div>
            </section>

        </div>

        <footer class="mt-16 pt-8 border-t border-gray-200 text-center text-gray-400 font-medium">
            <p>Precisa de mais ajuda técnica? Entre em contato com o suporte do desenvolvedor.</p>
            <p class="mt-2 text-sm">&copy; 2026 Pousada Solo Nunes - Sistema de Gestão</p>
        </footer>
    </main>

</body>
</html>