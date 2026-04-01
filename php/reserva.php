<?php 
// 1. Conexão com o banco de dados
include '../api/db.php'; 

// 🔥 ATUALIZADO: Busca respeitando a coluna 'ordem' (Sincronizado com o Admin)
$quartos_result = $conn->query("SELECT * FROM quartos ORDER BY ordem ASC, nome ASC, preco_noite ASC");
$passeios_result = $conn->query("SELECT * FROM  passeios");

// Define a variável de seleção para evitar erro de "undefined variable"
$quarto_selecionado = isset($_GET['quarto_nome']) ? urldecode($_GET['quarto_nome']) : '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva - Pousada Solo Nunes</title>
    <link rel="shortcut icon" href="../imagens/logo_sem_fundo.png" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        .tab-btn { transition: all 0.3s ease; }
        .tab-btn.active { background-color: #000; color: white; border-color: #000; }
        .tab-btn.inactive { background-color: #fff; color: #6b7280; border-color: #e5e7eb; }

        /* Estilo para Radio Buttons customizados */
        .radio-andar:checked + div {
            border-color: #10b981; /* emerald-500 */
            background-color: #ecfdf5; /* emerald-50 */
        }
        .radio-andar:checked + div .radio-icon {
            background-color: #10b981;
            border-color: #10b981;
        }
        .radio-andar:checked + div .radio-icon::after {
            content: '';
            display: block;
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
            margin: auto;
            margin-top: 3px;
        }
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

    <main class="max-w-6xl mx-auto px-4 py-32">
        
        <div class="flex flex-col md:flex-row justify-center gap-4 mb-10 border-b border-gray-200 pb-8">
            <button onclick="mudarAba('hospedagem')" id="btn-tab-hospedagem" class="tab-btn active px-6 py-4 font-bold rounded-xl shadow-sm border text-sm flex items-center justify-center gap-2">
                <i class="fa-solid fa-bed"></i> Hospedagem
            </button>
            <button onclick="mudarAba('passeios')" id="btn-tab-passeios" class="tab-btn inactive px-6 py-4 font-bold rounded-xl shadow-sm border text-sm flex items-center justify-center gap-2">
                <i class="fa-solid fa-compass"></i> Passeios Avulsos
            </button>
            <a href="personalizado.php" class="tab-btn inactive px-6 py-4 font-bold rounded-xl shadow-sm border text-sm flex items-center justify-center gap-2 hover:bg-indigo-50 hover:text-indigo-700 hover:border-indigo-200">
                <i class="fa-solid fa-users-gear"></i> Grupos e Empresas
            </a>
        </div>

        <div id="aba-hospedagem" class="block">
            <form action="../api/salvar_reserva.php" method="POST" id="form-hospedagem">
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-8">
                        
                        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                            <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                                <span class="bg-emerald-500 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">1</span>
                                Detalhes da Estadia
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold mb-2">Check-in</label>
                                    <input type="date" id="checkin" name="checkin" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-2">Check-out</label>
                                    <input type="date" id="checkout" name="checkout" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold mb-2">Quantidade de Hóspedes</label>
                                    <select id="hospedes" name="hospedes" class="w-full p-3 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-emerald-500">
                                        <option value="1">1 Pessoa</option>
                                        <option value="2" selected>2 Pessoas</option>
                                        <option value="3">3 Pessoas</option>
                                        <option value="4">4 Pessoas</option>
                                        <option value="5">5+ Pessoas</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                            <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                                <span class="bg-emerald-500 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">2</span>
                                Escolha sua Acomodação
                            </h2>
                            <div class="space-y-6">
                                <?php
                                if ($quartos_result && $quartos_result->num_rows > 0) {
                                    $quartos_agrupados = [];
                                    
                                    while($q = $quartos_result->fetch_assoc()) {
                                        // LÓGICA DE AGRUPAMENTO SINCRONIZADA
                                        $nome_base = preg_replace('/[-\s\d]+$/', '', trim($q['nome']));
                                        
                                        if (!isset($quartos_agrupados[$nome_base])) {
                                            $quartos_agrupados[$nome_base] = [];
                                        }
                                        $quartos_agrupados[$nome_base][] = $q;
                                    }

                                    foreach ($quartos_agrupados as $nome_quarto => $opcoes) {
                                        $is_ativo = (trim($quarto_selecionado) === trim($nome_quarto));
                                ?>
                                    <div class="quarto-grupo border border-gray-200 rounded-xl overflow-hidden transition-all shadow-sm <?= $is_ativo ? 'ring-2 ring-emerald-500' : 'hover:border-emerald-300' ?>">
                                        
                                        <div class="bg-gray-100 px-5 py-3 flex justify-between items-center border-b border-gray-200">
                                            <span class="font-bold text-lg text-gray-800"><?= $nome_quarto ?></span>
                                            <span class="bg-white text-gray-500 text-[10px] px-2 py-0.5 rounded-full font-bold border border-gray-200 flex items-center gap-1 shadow-sm">
                                                <i class="fa-solid fa-layer-group text-emerald-500"></i> <?= count($opcoes) ?> Opções
                                            </span>
                                        </div>

                                        <div class="p-3 space-y-3 bg-white">
                                            <?php foreach ($opcoes as $opcao): ?>
                                                <label data-capacidade="<?= $opcao['capacidade'] ?>" class="quarto-item flex flex-col sm:flex-row items-start sm:items-center justify-between p-3 border border-gray-200 rounded-lg cursor-pointer hover:border-emerald-500 hover:bg-emerald-50/30 transition-all gap-4 group">
                                                    
                                                    <div class="flex items-center gap-4 w-full sm:w-auto">
                                                        <input type="radio" name="quarto_id" value="<?= $opcao['id'] ?>" data-preco="<?= $opcao['preco_noite'] ?>" class="radio-andar absolute opacity-0 w-0 h-0" required>
                                                        
                                                        <div class="radio-icon w-4 h-4 rounded-full border-2 border-gray-300 transition-colors flex-shrink-0 hidden sm:block"></div>

                                                        <?php if(!empty($opcao['imagem_url'])): ?>
                                                            <img src="../<?= $opcao['imagem_url'] ?>" onclick="abrirModalImagem(this.src); event.preventDefault();" class="w-16 h-16 object-cover rounded-md border border-gray-200 flex-shrink-0 hover:opacity-80 transition-opacity cursor-zoom-in" alt="<?= $opcao['andar'] ?>">
                                                        <?php else: ?>
                                                            <div class="w-16 h-16 bg-gray-100 rounded-md border border-gray-200 flex items-center justify-center text-gray-400 flex-shrink-0"><i class="fa-solid fa-bed"></i></div>
                                                        <?php endif; ?>
                                                        
                                                        <div class="flex-1">
                                                            <span class="font-bold text-sm text-gray-800 flex flex-wrap items-center gap-2 mt-1 sm:mt-0">
                                                                <span><i class="fa-solid fa-stairs text-gray-400 mr-1 text-[10px]"></i> <?= $opcao['andar'] ?? 'Padrão' ?></span>
                                                                <span class="text-[10px] text-gray-500 font-normal"><i class="fa-solid fa-user-group text-emerald-500"></i> Até <?= $opcao['capacidade'] ?></span>
                                                                <span class="text-[10px] text-gray-500 font-normal"><i class="fa-solid fa-bed text-emerald-500"></i> <?= !empty($opcao['tipo_cama']) ? $opcao['tipo_cama'] : 'Não informado' ?></span>
                                                            </span>
                                                            <span class="text-[11px] text-gray-500 italic block mt-1 leading-tight"><?= $opcao['descricao'] ?></span>
                                                            
                                                            <div class="status-container mt-1"></div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="text-left sm:text-right mt-2 sm:mt-0 pl-4 sm:pl-0 w-full sm:w-auto border-t sm:border-t-0 border-gray-100 pt-2 sm:pt-0">
                                                        <span class="font-black text-emerald-600 text-lg whitespace-nowrap block">
                                                            R$ <?= number_format($opcao['preco_noite'], 2, ',', '.') ?>
                                                        </span>
                                                        <span class="text-[10px] text-gray-400 uppercase tracking-widest block">por noite</span>
                                                    </div>

                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php }} ?>
                            </div>
                        </div>

                        <div class="bg-emerald-50 p-8 rounded-xl shadow-sm border border-emerald-100">
                            <h2 class="text-2xl font-bold mb-2 flex items-center gap-3 text-emerald-900">
                                <span class="bg-emerald-500 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm"><i class="fa-solid fa-plus"></i></span>
                                Adicionar Experiências (Opcional)
                            </h2>
                            <div class="space-y-3">
                                <?php
                                if ($passeios_result->num_rows > 0) {
                                    mysqli_data_seek($passeios_result, 0); 
                                    while($p = $passeios_result->fetch_assoc()) {
                                ?>
                                    <label class="flex items-center p-4 border border-emerald-200 bg-white rounded-lg cursor-pointer hover:border-emerald-500 transition-colors">
                                        <input type="checkbox" name="passeios_add[]" value="<?= $p['id']; ?>" data-preco="<?= $p['preco']; ?>" class="w-5 h-5 text-emerald-600 checkbox-passeio cursor-pointer">
                                        <div class="ml-4 flex-1 font-bold text-gray-800"><?= $p['nome']; ?></div>
                                        <span class="text-sm font-bold text-emerald-600">+ R$ <?= number_format($p['preco'], 2, ',', '.'); ?>/pessoa</span>
                                    </label>
                                <?php }} ?>
                            </div>
                        </div>

                        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                            <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                                <span class="bg-emerald-500 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">3</span>
                                Seus Dados
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold mb-2">Nome Completo</label>
                                    <input type="text" id="nome" name="nome" placeholder="Seu nome" required class="w-full p-3 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-2">WhatsApp</label>
                                    <input type="tel" id="whatsapp" name="whatsapp" placeholder="Digite seu número aqui" required class="w-full p-3 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>
                            </div>
                        </div>

                        <div class="pt-6">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg transition-all transform hover:scale-[1.02] flex items-center justify-center gap-2">
                                <i class="fa-solid fa-calendar-check text-xl"></i> Confirmar Reserva Agora
                            </button>
                        </div>
                    </div> 

                    <aside class="space-y-6">
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 sticky top-24">
                            <h3 class="text-xl font-bold mb-4 text-center">Resumo</h3>
                            
                            <div class="space-y-3 text-sm border-t pt-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-500 italic">Local:</span>
                                    <span class="font-bold">Lírio do Vale - Manaus/AM</span>
                                </div>
                            </div>

                            <div class="mt-6 bg-red-50 text-red-500 text-xs italic p-4 rounded-xl text-center shadow-sm border border-red-100">
                                Os hóspedes devem fazer silêncio entre 23:00 e 07:00.
                            </div>

                            <div class="mt-4 bg-blue-50 text-blue-900 text-xs p-5 rounded-xl shadow-sm border border-blue-200 leading-relaxed text-left">
                                <p class="font-black uppercase tracking-wider mb-2 text-blue-700"><i class="fa-solid fa-clock mr-1"></i> Check-in das 14:00 às 20:00</p>
                                <p class="mb-3 font-medium">Os hóspedes devem apresentar um documento com foto e cartão de crédito no momento do check-in. Informe o horário de sua chegada à acomodação com antecedência.</p>
                                <p class="font-black uppercase tracking-wider text-blue-700"><i class="fa-solid fa-clock mr-1"></i> Check-out das 09:00 às 12:00</p>
                            </div>

                            <div id="aviso-pagamento-passeio" class="hidden mt-4 bg-orange-50 text-orange-600 text-[10px] font-bold p-3 rounded-xl text-center shadow-sm uppercase border border-orange-200">
                                (O PAGAMENTO DOS PASSEIOS SÓ SÃO EFETUADOS ATRAVÉS DO WHATSAPP)
                            </div>
                            
                            <div class="flex justify-between mt-6 pt-4 border-t border-gray-200">
                                <span class="text-gray-900 font-bold uppercase text-xs mt-1">Total Quartos:</span>
                                <span id="valor-total" class="font-bold text-emerald-600 text-xl">R$ 0,00</span>
                            </div>
                            <p id="info-noites" class="text-[10px] text-gray-400 italic text-right mt-1">Selecione as datas e o quarto</p>
                        </div>
                    </aside>
                </div> 
            </form>
        </div>
        </main>
    <script>
    function mudarAba(aba) {
        document.getElementById('aba-hospedagem').classList.toggle('hidden', aba !== 'hospedagem');
        document.getElementById('aba-passeios').classList.toggle('hidden', aba === 'hospedagem');
        document.getElementById('btn-tab-hospedagem').className = aba === 'hospedagem' ? 'tab-btn active px-6 py-4 font-bold rounded-xl shadow-sm border text-sm flex items-center justify-center gap-2' : 'tab-btn inactive px-6 py-4 font-bold rounded-xl shadow-sm border text-sm flex items-center justify-center gap-2';
        document.getElementById('btn-tab-passeios').className = aba !== 'hospedagem' ? 'tab-btn active px-6 py-4 font-bold rounded-xl shadow-sm border text-sm flex items-center justify-center gap-2' : 'tab-btn inactive px-6 py-4 font-bold rounded-xl shadow-sm border text-sm flex items-center justify-center gap-2';
    }

    function filtrarQuartosPorHospedes() {
        const numHospedes = parseInt(document.getElementById('hospedes').value) || 1;
        document.querySelectorAll('.quarto-grupo').forEach(grupo => {
            let temQuartoVisivel = false;
            grupo.querySelectorAll('.quarto-item').forEach(item => {
                const capacidade = parseInt(item.getAttribute('data-capacidade')) || 0;
                if (capacidade >= numHospedes) {
                    item.style.display = 'flex'; 
                    temQuartoVisivel = true;
                } else {
                    item.style.display = 'none'; 
                    const radio = item.querySelector('.radio-andar');
                    if(radio && radio.checked) { radio.checked = false; calcularTotalHospedagem(); }
                }
            });
            grupo.style.display = temQuartoVisivel ? 'block' : 'none';
        });
    }

    document.getElementById('hospedes').addEventListener('change', () => {
        filtrarQuartosPorHospedes();
        calcularTotalHospedagem(); 
    });
    filtrarQuartosPorHospedes();

    function calcularTotalHospedagem() {
        const checkin = document.getElementById('checkin').value;
        const checkout = document.getElementById('checkout').value;
        const quarto = document.querySelector('.radio-andar:checked'); 

        if (quarto && checkin && checkout) {
            const d1 = new Date(checkin); const d2 = new Date(checkout);
            const diffDays = Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24));
            if (diffDays > 0) {
                const total = diffDays * parseFloat(quarto.getAttribute('data-preco'));
                document.getElementById('valor-total').innerText = total.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                document.getElementById('info-noites').innerText = `${diffDays} diária(s) selecionada(s)`;
            } else { document.getElementById('valor-total').innerText = "R$ 0,00"; }
        } else { document.getElementById('valor-total').innerText = "R$ 0,00"; }
    }

    document.querySelectorAll('.checkbox-passeio').forEach(box => {
        box.addEventListener('change', () => {
            const algumMarcado = Array.from(document.querySelectorAll('.checkbox-passeio')).some(cb => cb.checked);
            document.getElementById('aviso-pagamento-passeio').classList.toggle('hidden', !algumMarcado);
        });
    });

    document.querySelectorAll('#form-hospedagem input, #form-hospedagem select').forEach(input => {
        input.addEventListener('change', calcularTotalHospedagem);
    });

    function abrirModalImagem(src) {
        document.getElementById('img-modal').src = src;
        document.getElementById('modal-imagem').classList.remove('hidden');
    }
    function fecharModalImagem() {
        document.getElementById('modal-imagem').classList.add('hidden');
    }
    </script>
</body>
</html>