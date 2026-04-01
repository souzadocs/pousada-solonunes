document.addEventListener('DOMContentLoaded', () => {
    // --- SELEÇÃO DE ELEMENTOS ---
    const topBar = document.querySelector('.top-bar'); // Seleciona a barra de navegação
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('menu-overlay');
    
    // Seleciona o botão de abrir (tracinhos) e o de fechar (X)
    const menuToggle = document.getElementById('mobile-menu');
    const closeBtn = document.getElementById('close-menu');

    // --- 1. LÓGICA DA SIDEBAR (MENU LATERAL) ---
    const abrirMenu = () => {
        if (sidebar && overlay) {
            sidebar.classList.add('open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden'; // Trava o scroll do fundo
        }
    };

    const fecharMenu = () => {
        if (sidebar && overlay) {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.style.overflow = ''; // Libera o scroll
        }
    };

    if (menuToggle) menuToggle.onclick = abrirMenu;
    if (closeBtn) closeBtn.onclick = fecharMenu;
    if (overlay) overlay.onclick = fecharMenu;

    // --- 2. LÓGICA DE SCROLL (NAVBAR) ---
    let lastScroll = window.scrollY;

    window.addEventListener('scroll', () => {
        let currentScroll = window.scrollY;

        if (topBar) {
            // Esconde a barra ao descer, mostra ao subir
            if (currentScroll > lastScroll && currentScroll > 100) {
                topBar.classList.add('hidden');
            } else {
                topBar.classList.remove('hidden');
            }

            // Muda a cor de fundo ao sair do topo
            if (currentScroll > 50) {
                topBar.classList.add('scrolled');
            } else {
                topBar.classList.remove('scrolled');
            }
        }
        lastScroll = currentScroll;
    });

    // --- 3. ANIMAÇÃO DE NÚMEROS (STATS) ---
    const stats = document.querySelectorAll('.stat-box h3');
    
    const animateValue = (obj, start, end, duration) => {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            obj.innerHTML = Math.floor(progress * (end - start) + start) + (obj.innerHTML.includes('+') ? '+' : '');
            if (progress < 1) window.requestAnimationFrame(step);
        };
        window.requestAnimationFrame(step);
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target;
                const endValue = parseInt(target.innerText.replace(/\D/g, ''));
                animateValue(target, 0, endValue, 2000);
                observer.unobserve(target);
            }
        });
    }, { threshold: 0.5 });

    stats.forEach(stat => observer.observe(stat));

    // --- 4. FORMULÁRIOS ---
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Solicitação enviada com sucesso! A Pousada Solo Nunes entrará em contato em breve.');
            form.reset();
        });
    });
});