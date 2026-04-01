<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pousada Solo Nunes - Seu Refúgio em Manaus</title>
    <link rel="shortcut icon" href="imagens/logo_sem_fundo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Simonetta:ital,wght@0,400;0,900;1,400&family=Bree+Serif&family=Chau+Philomene+One:ital@0;1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'solo-gold': '#DAA520',   // Dourado para sofisticação
                        'solo-green': '#2F4F4F',  // Verde floresta escuro (textos e destaques)
                        'solo-light-green': '#E8F1EC', // Verde sálvia bem claro (fundos de seção)
                        'solo-sand': '#FAF7F2',   // Areia/Off-white (fundo principal)
                        'solo-orange': '#D2691E', // Laranja terroso (botões chamativos)
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
        body { font-family: 'Chau Philomene One', sans-serif; scroll-behavior: smooth; background-color: #FAF7F2; color: #2F4F4F; }
        
        .top-bar { transition: all 0.4s ease-in-out; z-index: 50; }
        /* Quando a barra rolar, fica branca com sombra leve */
        .top-bar.scrolled { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); padding: 10px 0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .top-bar.scrolled .nav-link { color: #2F4F4F; }
        .top-bar.scrolled .menu-icon span { background-color: #2F4F4F; }
        .top-bar.hidden { transform: translateY(-100%); }

        .sidebar {
            position: fixed; top: 0; right: 0; width: 300px; height: 100vh;
            background-color: #FAF7F2; z-index: 100; transform: translateX(100%);
            transition: transform 0.4s ease-in-out; padding: 2rem; color: #2F4F4F;
            border-left: 4px solid #DAA520; box-shadow: -10px 0 20px rgba(0,0,0,0.05);
        }
        .sidebar.open { transform: translateX(0); }

        .menu-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(47, 79, 79, 0.4); display: none; z-index: 90; backdrop-filter: blur(3px);
        }
        .menu-overlay.active { display: block; }
        body.menu-open { overflow: hidden; }

        /* Estilo sutil de folhagens de fundo */
        .bg-leaves {
            background-image: url('https://www.transparenttextures.com/patterns/cubes.png');
            opacity: 0.4;
        }

        /* Animação para a seta de rolagem */
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(10px); }
        }
        .animate-bounce-slow { animation: bounce-slow 2s infinite; }
    </style>
</head>
<body class="antialiased">

    <header class="top-bar fixed w-full top-0 left-0 bg-transparent py-5 px-6 md:px-12 flex justify-between items-center transition-all" id="navbar">
        <a href="index.php" class="flex items-center">
            <img src="imagens/logo_sem_fundo.png" alt="Pousada Solo Nunes" class="h-14 md:h-16 w-auto transition-all drop-shadow-md">
        </a>

        <div class="flex items-center gap-8 font-bree tracking-wider">
            <a href="php/reserva.php" class="hidden md:block bg-solo-green text-white px-7 py-2.5 rounded-full font-bold text-sm hover:bg-solo-gold hover:text-white transition-all duration-300 shadow-md">Fazer Reserva</a>
            <button id="mobile-menu" class="menu-icon flex flex-col gap-1.5 cursor-pointer">
                <span class="w-8 h-1 bg-white rounded transition-colors shadow-sm"></span>
                <span class="w-8 h-1 bg-white rounded transition-colors shadow-sm"></span>
                <span class="w-6 h-1 bg-white rounded transition-colors shadow-sm ml-auto"></span>
            </button>
        </div>
    </header>

    <div id="menu-overlay" class="menu-overlay"></div>
    <aside id="sidebar" class="sidebar">
        <div class="flex justify-between items-center mb-10">
            <span class="text-2xl font-bold text-solo-gold font-simonetta">MENU</span>
            <button id="close-menu" class="text-4xl text-solo-green hover:text-solo-gold transition">&times;</button>
        </div>
        <nav class="flex flex-col gap-6 text-lg font-bree">
            <a href="index.php" class="hover:text-solo-gold border-b border-gray-200 pb-2 transition">Início</a>
            <a href="#sobre" class="hover:text-solo-gold border-b border-gray-200 pb-2 transition">A Pousada</a>
            <a href="#quartos" class="hover:text-solo-gold border-b border-gray-200 pb-2 transition">Acomodações</a>
            <a href="#passeios" class="hover:text-solo-gold border-b border-gray-200 pb-2 transition">Passeios na Amazônia</a>
            <a href="#contato" class="hover:text-solo-gold border-b border-gray-200 pb-2 transition">Localização & Contato</a>
        </nav>
        <div class="mt-auto pt-10 border-t border-gray-200">
            <p class="text-xs text-solo-green tracking-widest mb-2 uppercase font-bold">Fale Conosco</p>
            <p class="text-xl font-simonetta text-solo-gold">(92) 99313-8119</p>
            <a href="php/reserva.php" class="mt-4 block text-center bg-solo-green text-white px-6 py-3 rounded-xl font-bold hover:bg-solo-gold transition-colors">Reservar Agora</a>
        </div>
    </aside>

    <section class="relative h-screen w-full overflow-hidden flex items-center justify-center">
        <video autoplay muted loop playsinline class="absolute top-0 left-0 w-full h-full object-cover z-0">
            <source src="videos/solonuness.mp4" type="video/mp4">
        </video>
        
        <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-black/60 z-10"></div>
        
        <div class="relative z-20 text-center px-4 max-w-5xl mt-16">
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-light mb-4 drop-shadow-[0_4px_4px_rgba(0,0,0,0.5)] font-serif tracking-wide text-white leading-tight">
                Pousada Solo Nunes
            </h1>
            <p class="text-lg md:text-xl text-solo-gold drop-shadow-[0_2px_2px_rgba(0,0,0,0.8)] font-sans font-bold tracking-widest uppercase">
                O seu refúgio exclusivo na Amazônia
            </p>
        </div>

        <a href="#welcome" class="absolute bottom-12 left-1/2 transform -translate-x-1/2 z-20 text-white/80 hover:text-solo-gold transition-colors animate-bounce-slow">
            <i class="fa-solid fa-chevron-down text-4xl drop-shadow-md"></i>
        </a>
    </section>

    <section id="welcome" class="pt-24 pb-16 relative bg-solo-sand z-30">
        <div class="container mx-auto px-6 text-center max-w-4xl mb-16">
            <span class="bg-solo-green/10 text-solo-green px-6 py-2 rounded-full text-xs font-bold uppercase mb-6 inline-block border border-solo-green/20 font-bree tracking-widest shadow-sm">
                <i class="fa-solid fa-leaf mr-2 text-solo-gold"></i> No Coração de Manaus
            </span>
            <p class="text-2xl md:text-3xl mb-10 italic font-bree text-solo-green font-light leading-relaxed">
                Ambiente familiar, descanso garantido e o ponto de partida perfeito para a sua aventura, com o conforto que você merece.
            </p>
            <div class="flex flex-col md:flex-row gap-4 justify-center font-bree">
                <a href="#quartos" class="bg-white text-solo-green border border-gray-200 px-8 py-4 rounded-xl font-bold hover:bg-gray-50 transition-all duration-300 shadow-sm text-lg">Ver Suítes</a>
                <a href="php/reserva.php" class="bg-solo-orange text-white px-8 py-4 rounded-xl font-bold hover:bg-orange-700 hover:shadow-lg transition-all duration-300 shadow-md text-lg">Garantir Reserva</a>
            </div>
        </div>

        <div class="container mx-auto px-6">
            <div class="bg-white rounded-[2rem] shadow-xl p-8 md:p-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 text-center font-bree border border-gray-100">
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 bg-solo-light-green rounded-full flex items-center justify-center text-solo-green text-2xl mb-4">
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <h3 class="text-3xl font-extrabold text-solo-green font-simonetta mb-1">9.3</h3>
                    <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Avaliação Booking</p>
                </div>
                <div class="flex flex-col items-center border-t md:border-t-0 md:border-l border-gray-100 pt-6 md:pt-0">
                    <div class="w-16 h-16 bg-solo-light-green rounded-full flex items-center justify-center text-solo-green text-2xl mb-4">
                        <i class="fa-solid fa-wifi"></i>
                    </div>
                    <h3 class="text-2xl font-extrabold text-solo-green font-simonetta mb-1 mt-2">Wi-Fi Grátis</h3>
                    <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Em toda a pousada</p>
                </div>
                <div class="flex flex-col items-center border-t lg:border-t-0 lg:border-l border-gray-100 pt-6 lg:pt-0">
                    <div class="w-16 h-16 bg-solo-light-green rounded-full flex items-center justify-center text-solo-green text-2xl mb-4">
                        <i class="fa-solid fa-car"></i>
                    </div>
                    <h3 class="text-2xl font-extrabold text-solo-green font-simonetta mb-1 mt-2">Estacionamento</h3>
                    <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Privativo e Cortesia</p>
                </div>
                <div class="flex flex-col items-center border-t md:border-t-0 md:border-l border-gray-100 pt-6 md:pt-0">
                    <div class="w-16 h-16 bg-solo-light-green rounded-full flex items-center justify-center text-solo-green text-2xl mb-4">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <h3 class="text-2xl font-extrabold text-solo-green font-simonetta mb-1 mt-2">Localização</h3>
                    <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Fácil acesso ao Aeroporto</p>
                </div>
            </div>
        </div>
    </section>

    <section id="sobre" class="py-20 bg-white">
        <div class="container mx-auto px-6 flex flex-col lg:flex-row items-center gap-16">
            <div class="lg:w-1/2">
                <span class="text-solo-gold font-bold uppercase tracking-widest text-xs mb-2 block">Nossa História</span>
                <h2 class="text-4xl md:text-5xl font-bold font-simonetta text-solo-green mb-6">Sua Casa Longe de Casa</h2>
                <p class="text-gray-600 font-bree text-lg leading-relaxed mb-6 font-light">
                    Localizada no tranquilo bairro Lírio do Vale, a Pousada Solo Nunes oferece a combinação perfeita entre a hospitalidade manauara e o conforto que você merece após um dia intenso de exploração turística ou compromissos de negócios.
                </p>
                <p class="text-gray-600 font-bree text-lg leading-relaxed mb-8 font-light">
                    Nossa missão é proporcionar uma estadia segura, acolhedora e com um atendimento humano e personalizado 24 horas por dia. Trabalhamos com higienização rigorosa.
                </p>
                <div class="flex items-center gap-4 bg-solo-sand p-4 rounded-xl border border-gray-100">
                    <i class="fa-solid fa-clock-rotate-left text-3xl text-solo-gold"></i>
                    <div>
                        <h4 class="font-bold text-solo-green font-bree">Recepção Flexível</h4>
                        <p class="text-sm text-gray-500 font-bree">Atendimento humanizado para seu check-in.</p>
                    </div>
                </div>
            </div>
            
            <div class="lg:w-1/2 w-full">
                <img src="imagens/Entrada-solonunes.png" alt="Pousada Exterior" loading="lazy" class="rounded-[2rem] shadow-2xl w-full h-[500px] lg:h-[650px] object-cover border-[4px] border-solo-gold">
            </div>
        </div>
    </section>

    <section id="quartos" class="py-24 relative bg-solo-sand">
        <div class="absolute inset-0 bg-leaves z-0 pointer-events-none"></div>
        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16">
                <div class="max-w-xl mb-6 md:mb-0">
                    <span class="text-solo-gold font-bold uppercase tracking-widest text-xs mb-2 block">Acomodações</span>
                    <h2 class="text-4xl md:text-5xl font-bold mb-4 font-simonetta text-solo-green">Descanse na Natureza</h2>
                    <p class="text-gray-600 text-lg font-bree font-light">Quartos climatizados com frigobar, camas de excelência e limpeza impecável para renovar suas energias.</p>
                </div>
                <a href="php/quartos_principal.php" class="hidden md:flex items-center gap-2 bg-white text-solo-green px-6 py-2.5 rounded-full font-bold hover:bg-solo-green hover:text-white transition-all uppercase text-xs tracking-widest font-bree shadow-sm border border-gray-200">
                    Ver todos os quartos <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <div class="group bg-white rounded-[2rem] overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 border border-gray-100 flex flex-col">
                    <div class="relative h-64 overflow-hidden">
                        <img src="imagens/quartos/quarto-casal.jpeg" loading="lazy" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 border-[3px] border-solo-gold rounded-t-[2rem]" alt="Duplo">
                    </div>
                    <div class="p-8 flex-grow flex flex-col">
                        <h3 class="text-2xl font-bold mb-2 font-simonetta text-solo-green">Suíte Casal
                        </h3>
                        <p class="text-gray-500 mb-6 leading-relaxed font-bree text-sm font-light flex-grow">Praticidade e muito conforto no Lírio do Vale 2. Ideal para casais ou viajantes individuais a negócios.</p>
                        <div class="flex justify-between items-center pt-5 border-t border-gray-100">
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold mb-0.5">A partir de</p>
                                <span class="text-2xl font-extrabold text-solo-gold font-simonetta">R$ 160<small class="text-xs text-gray-400 font-normal font-chau">/noite</small></span>
                            </div>
                            <a href="php/reserva.php" class="bg-solo-light-green text-solo-green px-5 py-2.5 rounded-xl font-bold hover:bg-solo-green hover:text-white transition-colors text-sm font-bree">Reservar</a>
                        </div>
                    </div>
                </div>

                <div class="group bg-white rounded-[2rem] overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 border border-gray-100 flex flex-col relative">
                    <div class="absolute top-4 right-4 z-20 bg-solo-orange text-white text-[10px] font-bold uppercase px-3 py-1.5 rounded-full shadow-md font-bree tracking-wider">
                        Mais Popular
                    </div>
                    <div class="relative h-64 overflow-hidden">
                         <img src="imagens/quartos/quarto-casal-superior.png" loading="lazy" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 border-[3px] border-solo-gold rounded-t-[2rem]" alt="Triplo">
                    </div>
                    <div class="p-8 flex-grow flex flex-col">
                        <h3 class="text-2xl font-bold mb-2 font-simonetta text-solo-green">Suíte Casal Superior</h3>
                        <p class="text-gray-500 mb-6 leading-relaxed font-bree text-sm font-light flex-grow">Espaçosa e ideal para pequenas famílias. Conforto extra e cama de excelência para sua estadia em Manaus.</p>
                        <div class="flex justify-between items-center pt-5 border-t border-gray-100">
                             <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold mb-0.5">A partir de</p>
                                <span class="text-2xl font-extrabold text-solo-gold font-simonetta">R$ 160<small class="text-xs text-gray-400 font-normal font-chau">/noite</small></span>
                            </div>
                            <a href="php/reserva.php" class="bg-solo-light-green text-solo-green px-5 py-2.5 rounded-xl font-bold hover:bg-solo-green hover:text-white transition-colors text-sm font-bree">Reservar</a>
                        </div>
                    </div>
                </div>

                <div class="group bg-white rounded-[2rem] overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 border border-gray-100 flex flex-col">
                    <div class="relative h-64 overflow-hidden">
                        <img src="imagens/quartos/quarto-familia.jpeg" loading="lazy" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 border-[3px] border-solo-gold rounded-t-[2rem]" alt="Família">
                    </div>
                    <div class="p-8 flex-grow flex flex-col">
                        <h3 class="text-2xl font-bold mb-2 font-simonetta text-solo-green">Suíte Família</h3>
                        <p class="text-gray-500 mb-6 leading-relaxed font-bree text-sm font-light flex-grow">O máximo de espaço para viagens em grupo. Conta com todas as comodidades para relaxar após os passeios.</p>
                        <div class="flex justify-between items-center pt-5 border-t border-gray-100">
                             <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold mb-0.5">A partir de</p>
                                <span class="text-2xl font-extrabold text-solo-gold font-simonetta">R$ 455<small class="text-xs text-gray-400 font-normal font-chau">/noite</small></span>
                            </div>
                            <a href="php/reserva.php" class="bg-solo-light-green text-solo-green px-5 py-2.5 rounded-xl font-bold hover:bg-solo-green hover:text-white transition-colors text-sm font-bree">Reservar</a>
                        </div>
                    </div>
                </div>
            </div>
            
             <div class="text-center md:hidden mt-10">
                <a href="php/quartos_principal.php" class="inline-block bg-white text-solo-green px-8 py-3.5 rounded-full font-bold border border-gray-200 shadow-sm font-bree uppercase tracking-widest text-xs">Ver Todas as Opções</a>
            </div>
        </div>
    </section>

    <section class="py-24 bg-solo-light-green relative">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-solo-gold font-bold uppercase tracking-widest text-xs mb-2 block">Experiências Reais</span>
                <h2 class="text-4xl md:text-5xl font-bold font-simonetta text-solo-green mb-4">O que dizem nossos hóspedes</h2>
                <p class="text-gray-600 font-bree text-lg font-light">Orgulho de manter uma nota excepcional nas principais plataformas de reserva.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="flex gap-1 text-solo-gold text-sm mb-4">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    </div>
                    <p class="text-gray-600 italic font-bree font-light mb-6 flex-grow">"Lugar maravilhoso, super aconchegante, limpo e organizado. O atendimento foi excepcional do início ao fim. Recomendo de olhos fechados!"</p>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 font-bold font-bree">M</div>
                        <div>
                            <p class="font-bold text-solo-green text-sm font-bree">Maria S.</p>
                            <p class="text-xs text-gray-400">Viajante no Booking.com</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="flex gap-1 text-solo-gold text-sm mb-4">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    </div>
                    <p class="text-gray-600 italic font-bree font-light mb-6 flex-grow">"Excelente custo-benefício. A localização é ótima para quem precisa ir ao aeroporto e o quarto superou minhas expectativas. Cama muito confortável."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 font-bold font-bree">J</div>
                        <div>
                            <p class="font-bold text-solo-green text-sm font-bree">João P.</p>
                            <p class="text-xs text-gray-400">Viajante no Kayak</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="flex gap-1 text-solo-gold text-sm mb-4">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    </div>
                    <p class="text-gray-600 italic font-bree font-light mb-6 flex-grow">"A recepção é fantástica, nos sentimos em casa. Tudo muito cheiroso e o Wi-fi funciona perfeitamente para trabalhar. Voltaremos com certeza."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 font-bold font-bree">A</div>
                        <div>
                            <p class="font-bold text-solo-green text-sm font-bree">Ana C.</p>
                            <p class="text-xs text-gray-400">Hóspede a negócios</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="passeios" class="py-24 bg-white relative overflow-hidden">
        <div class="container mx-auto px-6 relative z-10">
            <div class="bg-solo-light-green rounded-[3rem] p-8 md:p-16 flex flex-col lg:flex-row items-center gap-12 border border-gray-100">
                
                <div class="lg:w-1/2 relative">
                    <img src="imagens/passeio_barco.png" alt="Passeio no Rio Negro" loading="lazy" class="rounded-[2rem] shadow-2xl object-cover h-[400px] w-full border-[4px] border-solo-gold">
                    
                    <div class="absolute -bottom-10 -right-10 hidden md:block z-20">
                        <img src="imagens/cachoeira.png" alt="Vitória Régia" loading="lazy" class="rounded-2xl w-56 h-56 object-cover shadow-2xl border-[4px] border-solo-gold">
                    </div>
                </div>

                <div class="lg:w-1/2 text-center lg:text-left">
                    <span class="text-solo-gold font-bold uppercase tracking-widest text-xs mb-2 block">Aventuras Inesquecíveis</span>
                    <h2 class="text-4xl md:text-5xl font-bold mb-6 font-simonetta text-solo-green">Explore a Magia <br>da Amazônia</h2>
                    <p class="text-gray-600 text-lg font-bree font-light leading-relaxed mb-8">
                        Não seja apenas um turista, seja um explorador. Transforme sua estadia conosco em uma experiência completa. Facilitamos o seu acesso aos melhores roteiros da região: do espetacular Encontro das Águas às focagens noturnas de jacarés e visitas a tribos indígenas.
                    </p>
                    <ul class="text-left space-y-4 mb-10 text-gray-700 font-bree">
                        <li class="flex items-center gap-3"><i class="fa-solid fa-water text-solo-gold text-xl"></i> O famoso Encontro das Águas</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-leaf text-solo-green text-xl"></i> Caminhadas e imersão na Selva</li>
                        <li class="flex items-center gap-3"><i class="fa-solid fa-masks-theater text-solo-orange text-xl"></i> Cultura e Tradições Indígenas</li>
                    </ul>
                    
                    <a href="php/passeios.php" class="inline-block bg-solo-orange text-white px-8 py-4 rounded-xl font-bold hover:bg-orange-700 transition-colors shadow-lg font-bree text-lg">
                        Conheça Nossos Roteiros
                    </a>
                </div>

            </div>
        </div>
    </section>

    <footer id="contato" class="pt-24 pb-12 bg-solo-green text-white relative z-20 font-bree">
        <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-16 pb-16 text-center md:text-left">
            <div>
                <h3 class="text-3xl font-bold mb-4 text-solo-gold font-simonetta">Pousada Solo Nunes</h3>
                <p class="text-solo-light-green/80 text-sm italic leading-relaxed font-light mt-4">
                    Atendimento familiar, camas de excelência e limpeza impecável. O seu ponto de descanso perfeito no bairro Lírio do Vale, pertinho das belezas de Manaus.
                </p>
            </div>
            
            <div>
                <h4 class="font-bold mb-6 uppercase text-sm tracking-[0.2em] text-solo-gold">Localização & Contato</h4>
                <ul class="space-y-5 text-sm font-light text-solo-light-green/90">
                    <li class="flex items-start gap-4 justify-center md:justify-start">
                        <i class="fa-solid fa-location-dot text-lg text-solo-gold mt-1"></i>
                        <span class="leading-relaxed">Rua 16, número 82<br>Lírio do Vale, Manaus / AM</span>
                    </li>
                    <li class="flex items-center gap-4 justify-center md:justify-start">
                         <i class="fa-solid fa-phone text-lg text-solo-gold"></i>
                        <span class="font-bold text-lg text-white">(92) 99313-8119</span>
                    </li>
                </ul>
            </div>
            
            <div>
                <h4 class="font-bold mb-6 uppercase text-sm tracking-[0.2em] text-solo-gold">Informações Úteis</h4>
                <div class="text-sm space-y-4 border-l-2 border-solo-gold/30 pl-5 text-solo-light-green/90 font-light mx-auto md:mx-0 table">
                    <div>
                        <p class="text-solo-gold font-bold uppercase text-[10px] tracking-widest mb-1">Check-in</p>
                        <p class="font-bold text-white text-base">A partir das 14:00h</p>
                    </div>
                    <div>
                        <p class="text-solo-gold font-bold uppercase text-[10px] tracking-widest mb-1 mt-3">Check-out</p>
                        <p class="font-bold text-white text-base">Até as 12:00h</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center border-t border-white/10 pt-8 mt-4">
             <div class="flex justify-center gap-6 text-xl text-solo-light-green/50 mb-6">
                <a href="https://www.instagram.com/solo.nunes/" target="_blank" class="hover:text-solo-gold transition-colors"><i class="fa-brands fa-instagram"></i></a>
                
                <a href="https://wa.me/5592993138119?text=Olá!%20Gostaria%20de%20saber%20mais%20sobre%20as%20reservas%20na%20Pousada%20Solo%20Nunes." target="_blank" class="hover:text-solo-gold transition-colors"><i class="fa-brands fa-whatsapp"></i></a>
                
            </div>
            <p class="text-white/40 text-xs uppercase tracking-[0.2em] font-bold">&copy; 2026 Pousada Solo Nunes - Manaus/AM. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const navbar = document.getElementById('navbar');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('menu-overlay');
            const btnOpen = document.getElementById('mobile-menu');
            const btnClose = document.getElementById('close-menu');
            const menuLines = document.querySelectorAll('.menu-icon span');
            
            const toggleMenu = (open) => {
                sidebar.classList.toggle('open', open);
                overlay.classList.toggle('active', open);
                document.body.classList.toggle('menu-open', open);
            };

            btnOpen.addEventListener('click', () => toggleMenu(true));
            btnClose.addEventListener('click', () => toggleMenu(false));
            overlay.addEventListener('click', () => toggleMenu(false));

            let lastScroll = window.scrollY;
            window.addEventListener('scroll', () => {
                let currentScroll = window.scrollY;
                
                // Esconde o menu ao rolar rapidamente para baixo (efeito mais suave)
                if (currentScroll > lastScroll && currentScroll > 150) {
                    navbar.classList.add('hidden');
                } else {
                    navbar.classList.remove('hidden');
                }

                // Efeito do fundo da barra ao rolar
                if (currentScroll > 50) {
                    navbar.classList.add('scrolled');
                    menuLines.forEach(line => line.classList.replace('bg-white', 'bg-solo-green'));
                } else {
                    navbar.classList.remove('scrolled');
                    menuLines.forEach(line => line.classList.replace('bg-solo-green', 'bg-white'));
                }
                
                lastScroll = currentScroll;
            });
        });
    </script>
</body>
</html>