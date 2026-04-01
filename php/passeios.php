<?php
include '../api/db.php';

// Busca todos os passeios cadastrados no banco de dados
$sql = "SELECT * FROM passeios ORDER BY preco ASC";
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passeios na Amazônia | Pousada Solo Nunes</title>
    <link rel="shortcut icon" href="../imagens/logo_sem_fundo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Simonetta:ital,wght@0,400;0,900;1,400&family=Bree+Serif&family=Chau+Philomene+One:ital@0;1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'solo-gold': '#DAA520',   
                        'solo-green': '#2F4F4F',  
                        'solo-light-green': '#E8F1EC', 
                        'solo-sand': '#FAF7F2',   
                        'solo-orange': '#D2691E', 
                    },
                    fontFamily: {
                        'simonetta': ['Simonetta', 'serif'],
                        'bree': ['Bree Serif', 'serif'],
                        'chau': ['Chau Philomene One', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Chau Philomene One', sans-serif; background-color: #FAF7F2; color: #2F4F4F; }
        /* Esconde a scrollbar das miniaturas na galeria mas permite rolar */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="antialiased">

    <nav class="sticky top-0 w-full z-50 bg-white/95 backdrop-blur-md shadow-sm py-4 px-6 md:px-12 flex justify-between items-center border-b border-gray-100">
        <a href="../index.php" class="flex items-center gap-2 text-solo-green hover:text-solo-gold transition-all font-bree font-bold text-xs uppercase tracking-widest">
            <i class="fa-solid fa-arrow-left"></i> Voltar ao Início
        </a>
        <img src="../imagens/logo_sem_fundo.png" alt="Solo Nunes" class="h-10 opacity-80">
        <a href="quartos_principal.php" class="hidden md:block bg-solo-green text-white px-6 py-2.5 rounded-full text-xs font-bold hover:bg-solo-gold transition-all shadow-md font-bree tracking-wider">Ver Acomodações</a>
    </nav>

    <header class="bg-gradient-to-br from-solo-green to-[#1b3b3b] text-white py-24 px-6 text-center shadow-md relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>
        
        <div class="relative z-10 max-w-3xl mx-auto pb-10">
            <span class="text-solo-orange font-bold uppercase tracking-widest text-xs mb-3 block"><i class="fa-solid fa-leaf mr-1"></i> Aventura e Natureza</span>
            <h1 class="text-5xl md:text-7xl font-bold mb-6 font-serif text-white leading-tight">Explorações Amazônicas</h1>
            <p class="text-solo-light-green/90 font-bree text-lg md:text-xl font-light max-w-2xl mx-auto leading-relaxed">
                Nossos guias experientes preparam os roteiros mais autênticos para você viver a verdadeira essência da selva.
            </p>
        </div>
    </header>

    <main class="container mx-auto px-6 py-16 relative z-20">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-20 -mt-24 relative z-30 max-w-5xl mx-auto">
            <div class="bg-white p-8 rounded-[2rem] shadow-xl border border-gray-100 text-center flex flex-col items-center">
                <div class="w-14 h-14 bg-orange-50 text-solo-orange rounded-full flex items-center justify-center text-2xl mb-4">
                    <i class="fa-solid fa-leaf"></i>
                </div>
                <h3 class="text-xl font-bold font-simonetta text-solo-green mb-2">Ecoturismo Real</h3>
                <p class="text-sm text-gray-500 font-bree leading-relaxed">Imersão sustentável e respeitosa na maior floresta tropical do mundo.</p>
            </div>
            
            <div class="bg-white p-8 rounded-[2rem] shadow-xl border border-gray-100 text-center flex flex-col items-center">
                <div class="w-14 h-14 bg-orange-50 text-solo-orange rounded-full flex items-center justify-center text-2xl mb-4">
                    <i class="fa-solid fa-camera"></i>
                </div>
                <h3 class="text-xl font-bold font-simonetta text-solo-green mb-2">Safáris Fotográficos</h3>
                <p class="text-sm text-gray-500 font-bree leading-relaxed">Cenários de tirar o fôlego para você capturar as melhores memórias.</p>
            </div>
            
            <div class="bg-white p-8 rounded-[2rem] shadow-xl border border-gray-100 text-center flex flex-col items-center">
                <div class="w-14 h-14 bg-orange-50 text-solo-orange rounded-full flex items-center justify-center text-2xl mb-4">
                    <i class="fa-solid fa-ship"></i>
                </div>
                <h3 class="text-xl font-bold font-simonetta text-solo-green mb-2">Roteiros Fluviais</h3>
                <p class="text-sm text-gray-500 font-bree leading-relaxed">Navegue pelos rios sinuosos e descubra a vida ribeirinha local.</p>
            </div>
        </div>

        <div class="flex flex-col gap-16 max-w-6xl mx-auto">
            
            <?php 
            if ($resultado->num_rows > 0): 
                $contadorPasseio = 0; // Ajuda a identificar as galerias
                while($p = $resultado->fetch_assoc()): 
                    $contadorPasseio++;
                    $idGaleria = "galeria-" . $contadorPasseio;
            ?>
                    
                    <div id="<?= $idGaleria ?>" class="hidden">
                        <?php if(!empty($p['imagem_url'])): ?>
                            <img src="../<?= $p['imagem_url'] ?>" alt="Foto Principal">
                        <?php endif; ?>
                        <img src="../imagens/passeios/f0e7dd08-231f-4d74-bad3-b6d0ae79d4e4.jpeg" alt="Foto Extra 1">
                        <img src="../imagens/passeios/c94c5faf-f818-408c-8780-4e65249901f8.jpeg" alt="Foto Extra 2">
                        <img src="../imagens/passeios/4e54c833-3a89-4d6c-b05d-2b9dae12f093.jpeg" alt="Foto Extra 1">
                        <img src="../imagens/passeios/43a1c0ba-41c0-42c4-a510-8b22ce318fd1.jpeg" alt="Foto Extra 2">
                        </div>
                    <div class="bg-white rounded-[2rem] overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 border border-gray-100 flex flex-col lg:flex-row group">
                        
                        <div class="lg:w-1/2 p-4 lg:p-6 bg-gray-50">
                            <div class="grid grid-cols-2 gap-3 h-full">
                                
                                <div class="col-span-2 relative h-64 lg:h-80 rounded-2xl overflow-hidden cursor-pointer" onclick="abrirModal('<?= $idGaleria ?>', 0)">
                                    <?php if(!empty($p['imagem_url'])): ?>
                                        <img src="../<?= $p['imagem_url'] ?>" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700" alt="<?= $p['nome'] ?>">
                                    <?php else: ?>
                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                                            <i class="fa-solid fa-tree text-5xl"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="absolute top-4 left-4 bg-solo-orange text-white backdrop-blur-sm px-4 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider shadow-md">
                                        <i class="fa-solid fa-star mr-1"></i> Experiência Completa
                                    </div>
                                    <div class="absolute inset-0 bg-black/0 hover:bg-black/10 transition-colors duration-300"></div>
                                </div>

                                <div class="relative h-32 lg:h-40 rounded-2xl overflow-hidden cursor-pointer" onclick="abrirModal('<?= $idGaleria ?>', 1)">
                                    <img src="../imagens/passeios/f0e7dd08-231f-4d74-bad3-b6d0ae79d4e4.jpeg" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
                                </div>

                                <div class="relative h-32 lg:h-40 rounded-2xl overflow-hidden cursor-pointer" onclick="abrirModal('<?= $idGaleria ?>', 2)">
                                    <img src="../imagens/passeios/c94c5faf-f818-408c-8780-4e65249901f8.jpeg" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
                                    
                                    <div class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center opacity-90 hover:bg-black/60 transition-colors duration-300">
                                        <i class="fa-regular fa-images text-white text-2xl mb-1"></i>
                                        <span class="text-white font-bree font-bold text-sm drop-shadow-md">Ver todas</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="lg:w-1/2 p-8 lg:p-12 flex flex-col justify-center">
                            
                            <div class="flex justify-between items-start mb-6 border-b border-gray-100 pb-6">
                                <div>
                                    <h3 class="text-3xl font-bold font-simonetta text-solo-green mb-2 leading-tight"><?= $p['nome'] ?></h3>
                                    <div class="flex items-center gap-4 text-solo-gold text-sm font-bold">
                                        <span><i class="fa-regular fa-clock"></i> Duração: Meio Período</span>
                                        <span><i class="fa-solid fa-users"></i> Máx. <?= $p['capacidade'] ?? 10 ?> pessoas</span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-gray-600 text-sm md:text-base leading-relaxed font-bree flex-grow space-y-5">
                                <?php if (!empty($p['descricao'])): ?>
                                    <p><?= nl2br(htmlspecialchars($p['descricao'])) ?></p>
                                <?php else: ?>
                                    <p class="font-bold text-solo-green text-lg">🌿 Mais do que um passeio, uma jornada ao coração da Amazônia!</p>
                                    
                                    <p>Testemunhe o espetáculo natural do Encontro das Águas e adentre a floresta para descobrir a rica cultura ribeirinha. Uma experiência completa que conecta você à natureza.</p>
                                    
                                    <div class="bg-solo-light-green/40 p-4 rounded-xl border border-solo-light-green">
                                        <p class="font-bold text-solo-green mb-3">Destaques do Roteiro:</p>
                                        <ul class="space-y-3 ml-1 grid grid-cols-1 md:grid-cols-2">
                                            <li class="flex items-center gap-3"><span class="text-xl">🐬</span> Interação com os botos</li>
                                            <li class="flex items-center gap-3"><span class="text-xl">🌊</span> O Encontro das Águas</li>
                                            <li class="flex items-center gap-3"><span class="text-xl">🪶</span> Visita à tribo indígena</li>
                                            <li class="flex items-center gap-3"><span class="text-xl">🌳</span> A gigante Samaúma</li>
                                            <li class="flex items-center gap-3"><span class="text-xl">🌺</span> Vitórias-régias</li>
                                            <li class="flex items-center gap-3"><span class="text-xl">🥾</span> Trilha na floresta</li>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mt-8 pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
                                <div class="text-center md:text-left">
                                    <span class="block text-xs text-gray-400 font-bold uppercase tracking-widest mb-1">Valor por Turista</span>
                                    <span class="text-4xl font-extrabold text-solo-orange font-simonetta">R$ <?= number_format($p['preco'], 2, ',', '.') ?></span>
                                </div>
                                <a href="reserva.php" onclick="sessionStorage.setItem('aba_ativa', 'passeios');" class="w-full md:w-auto bg-solo-orange text-white px-8 py-4 rounded-xl font-bold hover:bg-orange-700 transition-colors shadow-lg font-bree text-lg flex items-center justify-center gap-3 transform hover:scale-105 duration-300">
                                    📲 Reserve Agora
                                </a>
                            </div>

                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-24 bg-white rounded-[2rem] shadow-sm border border-gray-100">
                    <div class="w-24 h-24 bg-solo-light-green rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-compass text-4xl text-solo-green"></i>
                    </div>
                    <h3 class="text-2xl font-bold font-simonetta text-solo-green mb-2">Roteiros Sendo Mapeados</h3>
                    <p class="text-gray-500 font-bree text-lg font-light">Nossa equipe de guias locais está preparando novas rotas incríveis para você.</p>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <section class="bg-solo-light-green py-16 mt-10">
        <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-10 text-center max-w-5xl">
            <div class="bg-white p-8 rounded-2xl shadow-sm">
                <i class="fa-solid fa-user-shield text-4xl text-solo-green mb-4"></i>
                <h4 class="font-bold font-simonetta text-solo-green text-xl mb-2">Guias Locais</h4>
                <p class="text-sm text-gray-600 font-light font-bree">Especialistas na região para sua segurança e aprendizado.</p>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-sm">
                <i class="fa-solid fa-van-shuttle text-4xl text-solo-green mb-4"></i>
                <h4 class="font-bold font-simonetta text-solo-green text-xl mb-2">Transporte</h4>
                <p class="text-sm text-gray-600 font-light font-bree">Saída direto da pousada para os portos de embarque.</p>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-sm">
                <i class="fa-solid fa-camera text-4xl text-solo-green mb-4"></i>
                <h4 class="font-bold font-simonetta text-solo-green text-xl mb-2">Experiências</h4>
                <p class="text-sm text-gray-600 font-light font-bree">Roteiros pensados para criar memórias inesquecíveis.</p>
            </div>
        </div>
    </section>

    <footer class="bg-white border-t border-gray-200 py-12 text-center">
        <div class="flex justify-center gap-6 text-xl text-solo-green mb-6">
            <a href="https://www.instagram.com/solo.nunes/" target="_blank" class="hover:text-solo-gold transition-colors"><i class="fa-brands fa-instagram"></i></a>
            <a href="https://wa.me/5592993138119" target="_blank" class="hover:text-solo-gold transition-colors"><i class="fa-brands fa-whatsapp"></i></a>
        </div>
        <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold font-bree">
            &copy; 2026 Pousada Solo Nunes - Manaus/AM.
        </p>
    </footer>

    <a href="https://wa.me/5592993138119" target="_blank" class="fixed bottom-6 right-6 bg-green-500 text-white w-14 h-14 rounded-full flex items-center justify-center text-3xl shadow-2xl hover:bg-green-600 transition-all z-50 animate-bounce">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <div id="galeriaModal" class="fixed inset-0 z-[100] bg-black/95 hidden flex-col items-center justify-center opacity-0 transition-opacity duration-300">
        
        <button onclick="fecharModal()" class="absolute top-6 right-6 lg:top-10 lg:right-10 text-white/50 hover:text-white text-4xl z-50 transition-colors">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="relative w-full max-w-5xl h-[65vh] flex items-center justify-center p-4 mt-10 lg:mt-0">
            <img id="modalImagemPrincipal" src="" class="max-w-full max-h-full object-contain rounded-lg shadow-2xl transition-opacity duration-300">
        </div>

        <div class="w-full max-w-4xl h-[20vh] mt-4 p-4">
            <div id="modalMiniaturas" class="flex gap-4 overflow-x-auto no-scrollbar pb-2 items-center justify-start sm:justify-center h-full">
                </div>
        </div>
    </div>

    <script>
        function abrirModal(idContainer, indiceDesejado) {
            const container = document.getElementById(idContainer);
            if (!container) return;

            const imagens = container.querySelectorAll('img');
            if (imagens.length === 0) return;

            const modal = document.getElementById('galeriaModal');
            const imgPrincipal = document.getElementById('modalImagemPrincipal');
            const miniaturasDiv = document.getElementById('modalMiniaturas');

            // Limpa as miniaturas antigas (se abrir de outro passeio)
            miniaturasDiv.innerHTML = '';

            // Se o índice passado for maior que as fotos que existem, abre a foto 0
            if(indiceDesejado >= imagens.length) indiceDesejado = 0;

            // Define a foto grande inicial
            imgPrincipal.src = imagens[indiceDesejado].src;

            // Cria as fotinhas menores (miniaturas)
            imagens.forEach((img, index) => {
                const miniatura = document.createElement('img');
                miniatura.src = img.src;
                
                // Estilo da miniatura
                miniatura.className = 'h-16 w-24 md:h-20 md:w-28 object-cover rounded-lg cursor-pointer border-2 transition-all duration-300 shrink-0';
                
                // Se for a foto selecionada, deixa ela destacada com borda dourada
                if (index === indiceDesejado) {
                    miniatura.classList.add('border-solo-gold', 'opacity-100', 'scale-105');
                } else {
                    miniatura.classList.add('border-transparent', 'opacity-50', 'hover:opacity-100');
                }

                // O que acontece ao clicar na miniatura
                miniatura.onclick = () => {
                    // Efeito de fade na foto grande
                    imgPrincipal.style.opacity = '0';
                    setTimeout(() => {
                        imgPrincipal.src = img.src;
                        imgPrincipal.style.opacity = '1';
                    }, 150);

                    // Atualiza a borda de qual tá selecionada
                    Array.from(miniaturasDiv.children).forEach(m => {
                        m.className = 'h-16 w-24 md:h-20 md:w-28 object-cover rounded-lg cursor-pointer border-2 transition-all duration-300 shrink-0 border-transparent opacity-50 hover:opacity-100';
                    });
                    miniatura.className = 'h-16 w-24 md:h-20 md:w-28 object-cover rounded-lg cursor-pointer border-2 transition-all duration-300 shrink-0 border-solo-gold opacity-100 scale-105';
                };

                miniaturasDiv.appendChild(miniatura);
            });

            // Mostra o Modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            // Timeout para dar tempo do navegador aplicar o flex antes de mudar a opacidade (faz o efeito fade funcionar)
            setTimeout(() => modal.classList.remove('opacity-0'), 10);
            document.body.style.overflow = 'hidden'; // Trava o scroll da página de trás
        }

        function fecharModal() {
            const modal = document.getElementById('galeriaModal');
            modal.classList.add('opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = 'auto'; // Destrava o scroll
            }, 300); // tempo certinho do CSS duration-300
        }
    </script>
</body>
</html>