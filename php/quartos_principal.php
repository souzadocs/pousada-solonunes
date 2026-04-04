<?php
include '../api/db.php';

// Busca os quartos ordenados e agruparemos no PHP para pegar o MENOR PREÇO e a FOTO do primeiro
// 🔥 ATUALIZADO: Agora puxa respeitando a ORDEM do painel administrativo
$sql = "SELECT * FROM quartos ORDER BY ordem ASC, nome ASC, preco_noite ASC";
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nossas Suítes | Pousada Solo Nunes</title>
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
    </style>
</head>
<body class="antialiased">

    <nav class="sticky top-0 w-full z-50 bg-white/95 backdrop-blur-md shadow-sm py-4 px-6 md:px-12 flex justify-between items-center border-b border-gray-100">
        <a href="../index.php" class="flex items-center gap-2 text-solo-green hover:text-solo-gold transition-all font-bree font-bold text-xs uppercase tracking-widest">
            <i class="fa-solid fa-arrow-left"></i> Voltar ao Início
        </a>
        <img src="../imagens/logo_sem_fundo.png" alt="Solo Nunes" class="h-10 opacity-80">
        <a href="passeios.php" class="hidden md:block bg-solo-orange text-white px-6 py-2.5 rounded-full text-xs font-bold hover:bg-orange-700 transition-all shadow-md font-bree tracking-wider">Ver Passeios</a>
    </nav>

    <header class="bg-solo-green text-white py-24 px-6 text-center shadow-md relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>
        
        <div class="relative z-10 max-w-3xl mx-auto pb-10">
            <span class="text-solo-gold font-bold uppercase tracking-widest text-xs mb-3 block"><i class="fa-solid fa-bed mr-1"></i> Conforto e Descanso</span>
            <h1 class="text-5xl md:text-7xl font-bold mb-6 font-serif text-white leading-tight">Acomodações</h1>
            <p class="text-solo-light-green/90 font-bree text-lg md:text-xl font-light max-w-2xl mx-auto leading-relaxed">
                Quartos climatizados e higienizados rigorosamente para você recarregar as energias após explorar Manaus.
            </p>
        </div>
    </header>

    <main class="container mx-auto px-6 py-16 relative z-20">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-20 -mt-24 relative z-30 max-w-5xl mx-auto">
            <div class="bg-white p-8 rounded-[2rem] shadow-xl border border-gray-100 text-center flex flex-col items-center">
                <div class="w-14 h-14 bg-solo-light-green text-solo-green rounded-full flex items-center justify-center text-2xl mb-4">
                    <i class="fa-solid fa-heart"></i>
                </div>
                <h3 class="text-xl font-bold font-simonetta text-solo-green mb-2">Para Casais</h3>
                <p class="text-sm text-gray-500 font-bree leading-relaxed">Privacidade absoluta e muito conforto para momentos de descanso a dois.</p>
            </div>
            
            <div class="bg-white p-8 rounded-[2rem] shadow-xl border border-gray-100 text-center flex flex-col items-center">
            <div class="w-14 h-14 bg-solo-light-green text-solo-green rounded-full flex items-center justify-center text-2xl mb-4">
                <i class="fa-solid fa-users"></i>
            </div>
            <h3 class="text-xl font-bold font-simonetta text-solo-green mb-2">Espaço Família</h3>
            <p class="text-sm text-gray-500 font-bree leading-relaxed">Acomodações amplas projetadas para garantir o bem-estar de adultos e crianças.</p>
        </div>
            
            <div class="bg-white p-8 rounded-[2rem] shadow-xl border border-gray-100 text-center flex flex-col items-center">
                <div class="w-14 h-14 bg-solo-light-green text-solo-green rounded-full flex items-center justify-center text-2xl mb-4">
                    <i class="fa-solid fa-briefcase"></i>
                </div>
                <h3 class="text-xl font-bold font-simonetta text-solo-green mb-2">A Negócios</h3>
                <p class="text-sm text-gray-500 font-bree leading-relaxed">Ambiente silencioso, Wi-Fi veloz e estrutura ideal para quem viaja a trabalho.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            
            <?php 
            if ($resultado && $resultado->num_rows > 0) {
                $agrupados_vitrine = [];
                while($q = $resultado->fetch_assoc()) {
                    $nome = trim($q['nome']);
                    if (!isset($agrupados_vitrine[$nome])) {
                        $agrupados_vitrine[$nome] = $q; // Pega o primeiro (mais barato devido ao ORDER BY)
                    }
                }
                
                foreach ($agrupados_vitrine as $nome_quarto => $quarto): 
            ?>
                    <div class="group bg-white rounded-[2rem] overflow-hidden shadow-md hover:shadow-2xl transition-all duration-500 border border-gray-100 flex flex-col relative transform hover:-translate-y-2">
                        
                        <div class="relative h-72 overflow-hidden bg-gray-100 flex items-center justify-center">
                            <?php if(!empty($quarto['imagem_url'])): ?>
                                <img src="../<?= $quarto['imagem_url'] ?>" loading="lazy" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 border-[3px] border-solo-gold rounded-t-[2rem]" alt="<?= $quarto['nome'] ?>">
                            <?php else: ?>
                                <div class="flex flex-col items-center text-gray-400">
                                    <i class="fa-regular fa-image text-4xl mb-2"></i>
                                    <span class="italic text-sm font-bree">Sem foto</span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            
                            <div class="absolute bottom-4 left-4 flex gap-3 text-white text-sm z-10">
                                <span class="bg-black/40 backdrop-blur-md p-2 rounded-full" title="Ar-condicionado"><i class="fa-solid fa-snowflake"></i></span>
                                <span class="bg-black/40 backdrop-blur-md p-2 rounded-full" title="Wi-Fi Grátis"><i class="fa-solid fa-wifi"></i></span>
                                <span class="bg-black/40 backdrop-blur-md p-2 rounded-full" title="Frigobar"><i class="fa-solid fa-utensils"></i></span>
                            </div>
                        </div>

                        <div class="p-8 flex flex-col flex-grow">
                            <div class="flex justify-between items-start mb-3 border-b border-gray-50 pb-3">
                                <h3 class="text-2xl font-bold font-simonetta text-solo-green"><?= $quarto['nome'] ?></h3>
                                <span class="bg-solo-light-green text-solo-green text-xs px-3 py-1.5 rounded-md font-bold flex items-center gap-1" title="Capacidade Máxima">
                                    <i class="fa-solid fa-user-group text-[10px]"></i> Veja as Disponibilidades
                                </span>
                            </div>

                            <p class="text-xs text-solo-gold font-bold mb-3 uppercase tracking-widest"><i class="fa-solid fa-bed mr-1"></i> <?= !empty($quarto['tipo_cama']) ? $quarto['tipo_cama'] : 'Ver opções de camas' ?></p>

                            <p class="text-gray-500 text-sm mb-8 leading-relaxed font-bree font-light flex-grow">
                                Acomodação confortável e silenciosa. Enxoval completo e atendimento acolhedor.
                            </p>

                            <div class="mt-auto pt-6 border-t border-gray-100 flex justify-between items-end">
                                <div>
                                    <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-0.5">A partir de</span>
                                    <span class="text-3xl font-extrabold text-solo-gold font-simonetta">R$ <?= number_format($quarto['preco_noite'], 2, ',', '.') ?></span>
                                </div>
                                <a href="reserva.php?quarto_nome=<?= urlencode($quarto['nome']) ?>" onclick="sessionStorage.setItem('aba_ativa', 'hospedagem');" class="bg-solo-light-green text-solo-green px-5 py-3 rounded-xl font-bold hover:bg-solo-green hover:text-white transition-colors shadow-sm font-bree text-sm whitespace-nowrap">
                                    Ver Opções
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php } else { ?>
                <div class="col-span-full text-center py-24 bg-white rounded-[2rem] shadow-sm border border-gray-100">
                    <div class="w-24 h-24 bg-solo-light-green rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-bed text-4xl text-solo-green"></i>
                    </div>
                    <h3 class="text-2xl font-bold font-simonetta text-solo-green mb-2">Acomodações em Preparação</h3>
                    <p class="text-gray-500 font-bree text-lg font-light">Em breve nossas suítes estarão disponíveis para reserva online.</p>
                </div>
            <?php } ?>

        </div>
    </main>

    <section class="bg-white py-12 border-t border-gray-100">
        <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
            <div>
                <i class="fa-solid fa-mug-hot text-2xl text-solo-gold mb-3"></i>
                <h4 class="font-bold font-simonetta text-solo-green mb-1">Café Incluso</h4>
                <p class="text-xs text-gray-500 font-bree">Consulte as opções no local.</p>
            </div>
            <div>
                <i class="fa-solid fa-wifi text-2xl text-solo-gold mb-3"></i>
                <h4 class="font-bold font-simonetta text-solo-green mb-1">Internet Veloz</h4>
                <p class="text-xs text-gray-500 font-bree">Wi-Fi gratuito em toda a área.</p>
            </div>
            <div>
                <i class="fa-solid fa-car text-2xl text-solo-gold mb-3"></i>
                <h4 class="font-bold font-simonetta text-solo-green mb-1">Estacionamento</h4>
                <p class="text-xs text-gray-500 font-bree">Vaga garantida para seu veículo.</p>
            </div>
            <div>
                <i class="fa-solid fa-broom text-2xl text-solo-gold mb-3"></i>
                <h4 class="font-bold font-simonetta text-solo-green mb-1">Limpeza Rigorosa</h4>
                <p class="text-xs text-gray-500 font-bree">Padrão hotelaria internacional.</p>
            </div>
        </div>
    </section>

    <footer class="bg-solo-green text-white py-12 text-center">
        <div class="flex justify-center gap-6 text-xl text-solo-light-green/50 mb-6">
            <a href="https://www.instagram.com/solo.nunes/" target="_blank" class="hover:text-solo-gold transition-colors"><i class="fa-brands fa-instagram"></i></a>
            <a href="https://wa.me/5592993138119" target="_blank" class="hover:text-solo-gold transition-colors"><i class="fa-brands fa-whatsapp"></i></a>
        </div>
        <p class="text-[10px] text-white/50 uppercase tracking-widest font-bold font-bree">
            &copy; 2026 Pousada Solo Nunes - Manaus/AM.
        </p>
    </footer>

    <a href="https://wa.me/5592993138119" target="_blank" class="fixed bottom-6 right-6 bg-green-500 text-white w-14 h-14 rounded-full flex items-center justify-center text-3xl shadow-2xl hover:bg-green-600 transition-all z-50 animate-bounce">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

</body>
</html>