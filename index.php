<?php
// Adicionado: Conexão com o banco de dados
include 'api/db.php';

// Busca todos os quartos respeitando a ordem do painel admin
$sql = "SELECT * FROM quartos ORDER BY ordem ASC, preco_noite ASC";
$resultado_banco = $conn->query($sql);

$quartos_vitrine = [];
if ($resultado_banco && $resultado_banco->num_rows > 0) {
    while($q = $resultado_banco->fetch_assoc()) {
        // Agrupa pelo nome base para não repetir o mesmo quarto (ex: Térreo e 1º Andar)
        $nome_base = trim(preg_replace('/[-\s\d]+$/', '', trim($q['nome'])));
        if (!isset($quartos_vitrine[$nome_base])) {
            $quartos_vitrine[$nome_base] = $q;
        }
    }
}
// Pega apenas os 3 primeiros da lista ordenada
$quartos_destaque = array_slice($quartos_vitrine, 0, 3);

// Busca o primeiro passeio para a vitrine "Eventos e Passeios"
$sql_passeio = "SELECT * FROM passeios LIMIT 1";
$resultado_passeio = $conn->query($sql_passeio);
$passeio_destaque = ($resultado_passeio && $resultado_passeio->num_rows > 0) ? $resultado_passeio->fetch_assoc() : null;
?>
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
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
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
        body { font-family: 'Chau Philomene One', sans-serif; scroll-behavior: smooth; background-color: #FAF7F2; color: #2F4F4F; overflow-x: hidden; }
        
        .top-bar { transition: all 0.4s ease-in-out; z-index: 50; }
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

        /* Estilo parallax */
        .bg-parallax { background-attachment: fixed; background-position: center; background-repeat: no-repeat; background-size: cover; }
    </style>
</head>
<body class="antialiased">

    <header class="top-bar fixed w-full top-0 left-0 bg-transparent py-5 px-6 md:px-12 flex justify-between items-center transition-all" id="navbar">
        <a href="index.php" class="flex items-center">
            <img src="imagens/logo_sem_fundo.png" alt="Pousada Solo Nunes" class="h-14 md:h-16 w-auto transition-all drop-shadow-md">
        </a>

        <div class="flex items-center gap-8 font-bree tracking-wider">
            <a href="php/reserva.php" class="hidden md:block bg-solo-gold text-white px-7 py-2.5 rounded-full font-bold text-sm hover:bg-solo-green transition-all duration-300 shadow-md uppercase">Reservar</a>
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
            <a href="#passeios" class="hover:text-solo-gold border-b border-gray-200 pb-2 transition">Passeios e Eventos</a>
            <a href="#contato" class="hover:text-solo-gold border-b border-gray-200 pb-2 transition">Contatos</a>
        </nav>
        <div class="mt-auto pt-10 border-t border-gray-200">
            <p class="text-xs text-solo-green tracking-widest mb-2 uppercase font-bold">Fale Conosco</p>
            <p class="text-xl font-simonetta text-solo-gold">(92) 99313-8119</p>
            <a href="php/reserva.php" class="mt-4 block text-center bg-solo-green text-white px-6 py-3 rounded-xl font-bold hover:bg-solo-gold transition-colors">Reservar Agora</a>
        </div>
    </aside>

    <section class="relative h-screen w-full flex flex-col items-center justify-center">
        <video autoplay muted loop playsinline class="absolute top-0 left-0 w-full h-full object-cover z-0">
            <source src="videos/solonuness.mp4" type="video/mp4">
        </video>
        <div class="absolute inset-0 bg-black/40 z-10"></div>
        
        <div class="relative z-20 text-center px-4 w-full max-w-6xl mt-20" data-aos="fade-up" data-aos-duration="1000">
            <p class="text-solo-gold font-bold tracking-[0.3em] uppercase text-sm mb-4 drop-shadow-md">Pousada Solo Nunes</p>
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-black mb-12 drop-shadow-lg font-bree text-white leading-tight uppercase">
                Viva Momentos<br>Inesquecíveis em Manaus
            </h1>
            
            <form action="php/reserva.php" method="GET" class="bg-white/90 backdrop-blur-md p-3 md:p-4 rounded-3xl md:rounded-full flex flex-col md:flex-row items-center gap-4 md:gap-2 shadow-2xl mx-auto w-full md:w-auto inline-flex">
                
                <div class="flex items-center bg-white px-4 py-3 rounded-full flex-1 w-full md:w-auto">
                    <i class="fa-regular fa-calendar text-solo-green mr-3"></i>
                    <input type="text" placeholder="Check in / Check out" class="bg-transparent outline-none text-sm font-bree text-gray-700 w-full md:w-40" onfocus="(this.type='date')" onblur="(this.type='text')">
                </div>

                <div class="hidden md:block w-px h-8 bg-gray-300"></div>

                <div class="flex items-center bg-white px-4 py-3 rounded-full flex-1 w-full md:w-auto justify-between">
                    <span class="text-sm font-bree text-gray-700">Nº Hóspedes</span>
                    <div class="flex items-center gap-3">
                        <button type="button" class="text-gray-400 hover:text-solo-green"><i class="fa-solid fa-minus"></i></button>
                        <span class="font-bold">2</span>
                        <button type="button" class="text-gray-400 hover:text-solo-green"><i class="fa-solid fa-plus"></i></button>
                    </div>
                </div>

                <button type="submit" class="w-full md:w-auto bg-solo-green text-white px-8 py-3 rounded-full font-bold hover:bg-solo-gold transition-colors text-sm font-bree mt-2 md:mt-0 shadow-md uppercase tracking-wider">
                    Procurar
                </button>
            </form>
        </div>
    </section>

    <section id="sobre" class="py-24 bg-solo-sand overflow-hidden">
        <div class="container mx-auto px-6 flex flex-col lg:flex-row items-center gap-16">
            <div class="lg:w-1/2" data-aos="fade-right">
                <span class="text-solo-gold font-bold uppercase tracking-[0.2em] text-xs mb-3 block">Pousada Solo Nunes</span>
                <h2 class="text-4xl md:text-5xl font-black font-bree text-solo-green mb-8 leading-tight">Porque se hospedar Conosco</h2>
                <p class="text-gray-600 font-bree text-lg leading-relaxed mb-6 font-light">
                    A Pousada Solo Nunes se destaca como uma das mais completas e confortáveis de toda Manaus!
                </p>
                <p class="text-gray-600 font-bree text-lg leading-relaxed font-light border-l-4 border-solo-gold pl-4 italic">
                    "Sua localização privilegiada (perto do aeroporto) dá acesso a inúmeros atrativos e rápido deslocamento. Outro destaque é o nosso atendimento, sempre cordial e atencioso com nossos hóspedes."
                </p>
                <div class="mt-8 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-gray-300 overflow-hidden flex items-center justify-center bg-solo-green text-solo-gold text-xl">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div>
                        <p class="font-simonetta text-xl text-solo-green font-bold">Marivania Ribeiro Batista</p>
                    </div>
                </div>
            </div>
            
            <div class="lg:w-1/2 w-full relative h-[400px] md:h-[500px]" data-aos="fade-left">
                <img src="imagens/Entrada-solonunes.png" alt="Fachada" class="w-full h-full object-cover rounded-2xl shadow-xl bg-gray-300 border-4 border-white">
            </div>
        </div>
    </section>

    <section class="py-32 bg-parallax relative flex items-center justify-center bg-gray-800" style="background-image: url('');">
        <video autoplay muted loop playsinline class="absolute top-0 left-0 w-full h-full object-cover z-0">
            <source src="videos/solonuness.mp4" type="video/mp4">
        </video>
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 text-center" data-aos="zoom-in">
            <span class="text-white uppercase tracking-[0.3em] text-sm font-bold block mb-2 drop-shadow-md">Pousada Solo Nunes</span>
            <h2 class="text-5xl md:text-7xl font-black font-bree text-white drop-shadow-lg">Colecione<br>Momentos</h2>
        </div>
    </section>

    <section id="quartos" class="py-24 bg-solo-sand">
        <div class="container mx-auto px-6">
            <div class="mb-16" data-aos="fade-up">
                <span class="text-solo-gold font-bold uppercase tracking-widest text-xs mb-2 block">Pousada Solo Nunes</span>
                <h2 class="text-4xl md:text-5xl font-black font-bree text-solo-green">Suítes em Destaque</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php 
                if (!empty($quartos_destaque)): 
                    $delay = 100;
                    foreach($quartos_destaque as $q): 
                ?>
                <div class="relative h-80 md:h-96 rounded-3xl overflow-hidden group shadow-lg cursor-pointer" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
                    <?php $img_src = !empty($q['imagem_url']) ? $q['imagem_url'] : ''; ?>
                    
                    <img src="<?= htmlspecialchars($img_src) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 bg-gray-200" alt="<?= htmlspecialchars($q['nome']) ?>">
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                    
                    <div class="absolute bottom-0 left-0 p-6 w-full">
                        <span class="text-solo-gold text-[10px] font-bold uppercase tracking-widest block mb-1">Suíte</span>
                        <h3 class="text-2xl font-bold font-bree text-white mb-4"><?= htmlspecialchars($q['nome']) ?></h3>
                        <a href="php/reserva.php?quarto_nome=<?= urlencode($q['nome']) ?>" class="inline-block bg-white text-black px-5 py-2 rounded-full text-xs font-bold font-bree uppercase tracking-wider hover:bg-solo-gold hover:text-white transition-colors">
                            + Informações
                        </a>
                    </div>
                </div>
                <?php 
                    $delay += 100;
                    endforeach; 
                else: 
                ?>
                    <p class="col-span-full text-gray-500 font-bree">Nenhum quarto disponível para exibir.</p>
                <?php endif; ?>
            </div>
            
            <div class="text-center mt-12" data-aos="fade-up">
                <a href="php/quartos_principal.php" class="inline-block border-2 border-solo-green text-solo-green px-8 py-3 rounded-full font-bold hover:bg-solo-green hover:text-white transition-colors font-bree uppercase text-sm tracking-widest">Ver todas as Suítes</a>
            </div>
        </div>
    </section>

    <section class="py-24 bg-white border-t border-gray-100">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="text-solo-gold font-bold uppercase tracking-widest text-xs mb-2 block">Pousada Solo Nunes</span>
                <h2 class="text-4xl md:text-5xl font-black font-bree text-solo-green">Alguns Diferenciais</h2>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-y-12 gap-x-6 text-center">
                <div data-aos="zoom-in" data-aos-delay="100">
                    <i class="fa-solid fa-car text-3xl text-solo-gold mb-4"></i>
                    <h4 class="font-bold font-bree text-solo-green mb-2">Estacionamento</h4>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">Comodidade e segurança para os hóspedes. A tranquilidade começa desde a chegada.</p>
                </div>
                <div data-aos="zoom-in" data-aos-delay="200">
                    <i class="fa-solid fa-wifi text-3xl text-solo-gold mb-4"></i>
                    <h4 class="font-bold font-bree text-solo-green mb-2">Internet Wi-Fi</h4>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">Conexão veloz para os hóspedes. Desfrute de uma estadia online perfeita.</p>
                </div>
                <div data-aos="zoom-in" data-aos-delay="300">
                    <i class="fa-solid fa-snowflake text-3xl text-solo-gold mb-4"></i>
                    <h4 class="font-bold font-bree text-solo-green mb-2">Ar-condicionado</h4>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">Quartos climatizados garantindo todo o conforto durante o calor amazônico.</p>
                </div>
                <div data-aos="zoom-in" data-aos-delay="400">
                    <i class="fa-solid fa-utensils text-3xl text-solo-gold mb-4"></i>
                    <h4 class="font-bold font-bree text-solo-green mb-2">Cozinha Compartilhada</h4>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">Nossa pousada oferece uma cozinha compartilhada e equipada para maior comodidade durante a sua estadia.</p>
                </div>
                <div data-aos="zoom-in" data-aos-delay="100">
                    <i class="fa-solid fa-plane text-3xl text-solo-gold mb-4"></i>
                    <h4 class="font-bold font-bree text-solo-green mb-2">Perto do Aeroporto</h4>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">Localização estratégica a apenas 6km do Aeroporto Internacional Eduardo Gomes.</p>
                </div>
                <div data-aos="zoom-in" data-aos-delay="200">
                    <i class="fa-solid fa-volume-xmark text-3xl text-solo-gold mb-4"></i>
                    <h4 class="font-bold font-bree text-solo-green mb-2">Quartos Silenciosos</h4>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">Acomodações com isolamento acústico garantindo a melhor noite de sono.</p>
                </div>
                <div data-aos="zoom-in" data-aos-delay="300">
                    <i class="fa-solid fa-tv text-3xl text-solo-gold mb-4"></i>
                    <h4 class="font-bold font-bree text-solo-green mb-2">TV de Tela Plana</h4>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">Entretenimento nos quartos com as melhores opções para relaxar na sua cama.</p>
                </div>
                <div data-aos="zoom-in" data-aos-delay="400">
                    <i class="fa-solid fa-broom text-3xl text-solo-gold mb-4"></i>
                    <h4 class="font-bold font-bree text-solo-green mb-2">Limpeza Impecável</h4>
                    <p class="text-xs text-gray-500 font-light leading-relaxed">Trabalhamos com um padrão de higienização de primeira linha em todos os ambientes.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-solo-sand">
        <div class="container mx-auto px-6 flex flex-col lg:flex-row items-center gap-16">
            <div class="lg:w-1/2" data-aos="fade-right">
                <span class="text-solo-gold font-bold uppercase tracking-widest text-xs mb-3 block">O que fazer em</span>
                <h2 class="text-4xl md:text-5xl font-black font-bree text-solo-green mb-6">Manaus?</h2>
                <p class="text-gray-600 font-bree text-base leading-relaxed mb-8 font-light">
                    Manaus é uma cidade vibrante com uma rica cultura e uma natureza exuberante. Aqui estão algumas atividades imperdíveis para quem visita a "Paris brasileira": Explore o Centro Histórico. Visite o icônico Teatro Amazonas, admire a Igreja de São Sebastião e faça compras no Mercado Municipal Adolpho Lisboa...
                </p>
                <a href="https://www.tripadvisor.com.br/Attractions-g303235-Activities-Manaus_Amazon_River_State_of_Amazonas.html" target="_blank" class="inline-block border-2 border-solo-green text-solo-green px-8 py-3 rounded-full font-bold hover:bg-solo-green hover:text-white transition-colors font-bree text-sm uppercase">Mais informações</a>
            </div>
            <div class="lg:w-1/2 w-full" data-aos="fade-left">
                <img src="imagens/teatro amazonas.jpg" alt="Manaus" class="w-full h-[400px] object-cover rounded-3xl shadow-xl bg-gray-300">
            </div>
        </div>
    </section>

    <section class="py-24 bg-white">
        <div class="container mx-auto px-6 flex flex-col-reverse lg:flex-row items-center gap-16">
            <div class="lg:w-1/2 w-full" data-aos="fade-right">
                <img src="imagens/comida.jpg" alt="Gastronomia" class="w-full h-[400px] object-cover rounded-3xl shadow-xl bg-gray-300">
            </div>
            <div class="lg:w-1/2" data-aos="fade-left">
                <span class="text-solo-gold font-bold uppercase tracking-widest text-xs mb-3 block">Bares & Restaurantes em</span>
                <h2 class="text-4xl md:text-5xl font-black font-bree text-solo-green mb-6">Manaus</h2>
                <p class="text-gray-600 font-bree text-base leading-relaxed mb-8 font-light">
                    A gastronomia de Manaus é uma verdadeira celebração dos sabores da Amazônia. Influenciada pela rica biodiversidade da região, a culinária local é um mosaico de ingredientes exóticos e receitas tradicionais.
                </p>
                <a href="https://www.tripadvisor.com.br/Restaurants-g303235-Manaus_Amazon_River_State_of_Amazonas.html" target="_blank" class="inline-block border-2 border-solo-green text-solo-green px-8 py-3 rounded-full font-bold hover:bg-solo-green hover:text-white transition-colors font-bree text-sm uppercase">Mais informações</a>
            </div>
        </div>
    </section>

    <section class="py-32 bg-parallax relative bg-gray-900" style="background-image: url('');">
        <div class="absolute inset-0 bg-black/60"></div>
        <div class="container mx-auto px-6 relative z-10 text-center">
            <span class="text-solo-gold font-bold uppercase tracking-[0.2em] text-xs mb-2 block" data-aos="fade-up">Vejam o que os clientes</span>
            <h2 class="text-4xl md:text-5xl font-black font-bree text-white mb-16" data-aos="fade-up" data-aos-delay="100">Falam da Pousada</h2>
            
            <div class="max-w-3xl mx-auto bg-black/40 backdrop-blur-md border border-white/10 p-10 md:p-12 rounded-3xl shadow-2xl flex flex-col md:flex-row items-center gap-8 text-left transition-opacity duration-500 ease-in-out" data-aos="zoom-in" data-aos-delay="200" id="review-container">
                <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-solo-gold flex-shrink-0 flex items-center justify-center bg-gray-700 text-3xl text-gray-300">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div>
                    <h4 class="text-white font-bree font-bold text-lg" id="rev-name">Rodrigo</h4>
                    <p class="text-gray-400 text-sm mb-4" id="rev-role">A negócios no Booking.com</p>
                    <p class="text-gray-200 italic font-light leading-relaxed text-lg" id="rev-text">"Muito próximo ao aeroporto, facilitou muito a minha viagem a trabalho. Ambiente extremamente limpo, recepção atenciosa e quarto silencioso!"</p>
                </div>
            </div>
            
            <div class="flex justify-center gap-4 mt-8">
                <button onclick="changeReview(0)" class="rev-dot w-4 h-4 md:w-3 md:h-3 rounded-full bg-white opacity-100 transition-all focus:outline-none"></button>
                <button onclick="changeReview(1)" class="rev-dot w-4 h-4 md:w-3 md:h-3 rounded-full border border-white opacity-50 hover:opacity-100 transition-all focus:outline-none"></button>
                <button onclick="changeReview(2)" class="rev-dot w-4 h-4 md:w-3 md:h-3 rounded-full border border-white opacity-50 hover:opacity-100 transition-all focus:outline-none"></button>
            </div>
        </div>
    </section>

    <section id="passeios" class="py-24 bg-solo-sand">
        <div class="container mx-auto px-6">
            <div class="mb-12" data-aos="fade-right">
                <span class="text-solo-gold font-bold uppercase tracking-widest text-xs mb-2 block">Agenda da Pousada</span>
                <h2 class="text-4xl md:text-5xl font-black font-bree text-solo-green">Eventos & Passeios</h2>
            </div>
            
            <div class="max-w-md bg-white rounded-3xl overflow-hidden shadow-lg border border-gray-100 group" data-aos="fade-up">
                <div class="h-60 overflow-hidden bg-gray-200">
                    <?php 
                        $img_passeio = ($passeio_destaque && !empty($passeio_destaque['imagem_url'])) ? $passeio_destaque['imagem_url'] : ''; 
                    ?>
                    <img src="<?= htmlspecialchars($img_passeio) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Passeio em Destaque">
                </div>
                <div class="p-8 relative">
                    <span class="absolute -top-5 left-8 bg-white text-solo-green font-bold text-[10px] uppercase tracking-widest px-4 py-2 rounded-lg shadow-md border border-gray-100">Passeios</span>
                    <h3 class="text-2xl font-bold font-bree text-solo-green mb-6 mt-2">
                        <?= ($passeio_destaque) ? htmlspecialchars($passeio_destaque['nome']) : 'Explore a Magia da Amazônia' ?>
                    </h3>
                    <a href="php/passeios.php" class="text-sm font-bold uppercase tracking-widest text-gray-500 hover:text-solo-gold transition-colors border-b-2 border-transparent hover:border-solo-gold pb-1">
                        Reservar
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

    <a href="https://wa.me/5592993138119" target="_blank" class="fixed bottom-6 right-6 bg-green-500 text-white w-14 h-14 rounded-full flex items-center justify-center text-3xl shadow-2xl hover:bg-green-600 transition-all z-50 animate-bounce">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });

        // Script Menu
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
                
                if (currentScroll > lastScroll && currentScroll > 150) {
                    navbar.classList.add('hidden');
                } else {
                    navbar.classList.remove('hidden');
                }

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

        // Script Avaliações (Booking)
        const reviews = [
            { name: "Rodrigo", role: "A negócios no Booking.com", text: "\"Muito próximo ao aeroporto, facilitou muito a minha viagem a trabalho. Ambiente extremamente limpo, recepção atenciosa e quarto silencioso!\"" },
            { name: "Larissa C.", role: "Família no Booking.com", text: "\"Fomos muito bem recebidos de madrugada. A pousada é super segura, quartos amplos e o chuveiro é excelente após os passeios.\"" },
            { name: "Guilherme", role: "Viajante no Booking.com", text: "\"Tudo novinho e muito limpo. O ar condicionado gela muito bem (essencial em Manaus). Ótimo atendimento das meninas da recepção!\"" }
        ];
        
        let currentReview = 0;
        let reviewTimer;

        function changeReview(index) {
            currentReview = index;
            const container = document.getElementById('review-container');
            
            // Efeito fade pelo CSS opacity
            container.style.opacity = '0';
            
            setTimeout(() => {
                document.getElementById('rev-name').innerText = reviews[index].name;
                document.getElementById('rev-role').innerText = reviews[index].role;
                document.getElementById('rev-text').innerText = reviews[index].text;
                
                document.querySelectorAll('.rev-dot').forEach((dot, i) => {
                    if(i === index) {
                        dot.classList.replace('opacity-50', 'opacity-100');
                        dot.classList.replace('border', 'border-0');
                        dot.classList.add('bg-white');
                    } else {
                        dot.classList.replace('opacity-100', 'opacity-50');
                        dot.classList.remove('bg-white');
                        dot.classList.add('border', 'border-white');
                    }
                });
                container.style.opacity = '1';
            }, 500); // 500ms bate com a duration da transição CSS

            // Reseta o timer automático ao clicar manualmente
            clearInterval(reviewTimer);
            startReviewTimer();
        }

        function startReviewTimer() {
            reviewTimer = setInterval(() => {
                let next = (currentReview + 1) % reviews.length;
                changeReview(next);
            }, 6000);
        }
        
        // Inicia o carrossel
        startReviewTimer();

    </script>
</body>
</html>