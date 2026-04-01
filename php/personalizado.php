<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grupos e Delegações - Pousada Solo Nunes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">

    <header class="fixed top-0 w-full z-50 bg-black py-4 px-6 md:px-12 flex justify-between items-center shadow-lg">
        <a href="../index.php" class="flex items-center">
            <img src="../imagens/logo.jpg" alt="Solo Nunes" class="h-12 w-auto">
        </a>
        <a href="../index.php" class="text-white hover:text-emerald-500 transition-colors font-medium">
            <i class="fa-solid fa-arrow-left mr-2"></i> Voltar ao Início
        </a>
    </header>

    <section class="pt-32 pb-12 bg-black text-white text-center px-4">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Grupos & Delegações</h1>
        <p class="text-gray-400 italic max-w-2xl mx-auto">Adaptamos nossa estrutura com camas extras e beliches para acomodar sua equipe, empresa ou família grande com conforto e economia.</p>
    </section>

    <main class="max-w-4xl mx-auto px-4 py-12">
        <div class="bg-white p-8 md:p-12 rounded-2xl shadow-xl border border-gray-100">
            
            <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-100">
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center text-xl">
                    <i class="fa-solid fa-users-gear"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Solicitar Orçamento Personalizado</h2>
                    <p class="text-sm text-gray-500">Nossa equipe analisará a disponibilidade do nosso estoque flutuante de camas.</p>
                </div>
            </div>

            <form action="../api/salvar_personalizado.php" method="POST" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold mb-2">Nome do Responsável</label>
                        <input type="text" name="nome_cliente" required class="w-full p-3 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Empresa / Delegação (Opcional)</label>
                        <input type="text" name="nome_empresa" class="w-full p-3 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold mb-2">Check-in</label>
                        <input type="date" name="checkin" id="checkin" required class="w-full p-3 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Check-out</label>
                        <input type="date" name="checkout" id="checkout" required class="w-full p-3 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Total de Pessoas</label>
                        <input type="number" name="qnt_pessoas" min="5" placeholder="Ex: 15" required class="w-full p-3 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                        <p class="text-[10px] text-gray-400 mt-1 italic">* Mínimo sugerido de 5 pessoas</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">WhatsApp para Contato</label>
                    <input type="tel" name="whatsapp" required placeholder="(92) 99999-9999" class="w-full p-3 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Detalhes da Necessidade</label>
                    <textarea name="obs" rows="4" placeholder="Ex: Precisamos de 2 quartos separados para diretoria e o restante da equipe dividida em beliches..." class="w-full p-3 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100 flex items-start gap-3 mt-6">
                    <i class="fa-solid fa-circle-info text-indigo-600 mt-1"></i>
                    <p class="text-xs text-indigo-800 leading-relaxed">
                        <strong>Como funciona?</strong> Seu pedido entrará em "Análise". Nossa administração vai calcular a melhor distribuição usando nossa estrutura física e camas extras. Você receberá um link de pagamento personalizado via WhatsApp com a proposta final.
                    </p>
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg transition-all transform hover:scale-[1.01] text-lg mt-8">
                    Enviar Solicitação para Análise
                </button>
            </form>
        </div>
    </main>

    <footer class="bg-black text-white py-8 text-center text-xs">
        &copy; 2026 Pousada Solo Nunes - Manaus/AM.
    </footer>

    <script>
        // Trava simples para datas
        const checkin = document.getElementById('checkin');
        const checkout = document.getElementById('checkout');
        
        checkin.addEventListener('change', function() { checkout.min = this.value; });
        
        document.querySelector('form').addEventListener('submit', function(e) {
            if (new Date(checkout.value) <= new Date(checkin.value)) {
                e.preventDefault();
                alert("O Check-out deve ser após o Check-in.");
            }
        });
    </script>
</body>
</html>