<?php
session_start();

// 1. SE CLICOU EM SAIR NO DASHBOARD
if (isset($_GET['logout'])) {
    session_destroy(); 
    header("Location: login.php"); 
    exit();
}

// 2. SE JÁ ESTIVER LOGADO
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    header("Location: dashboard.php");
    exit;
}

$erro = "";

// 3. SISTEMA DE RATE LIMIT (Proteção contra Força Bruta)
if (isset($_SESSION['bloqueio_fim']) && time() < $_SESSION['bloqueio_fim']) {
    $minutos_restantes = ceil(($_SESSION['bloqueio_fim'] - time()) / 60);
    $erro = "Muitas tentativas falhas. Aguarde $minutos_restantes minuto(s) para tentar novamente.";
} 
else {
    // PROCESSA O LOGIN SE NÃO ESTIVER BLOQUEADO
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        include '../api/db.php'; 

        $usuario = trim($_POST['usuario']);
        $senha = $_POST['senha'];

        // BUSCA SEGURA COM PREPARED STATEMENT
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Verifica a senha (aceita o Hash seguro OU a senha em texto puro se for antiga)
            if (password_verify($senha, $row['senha']) || $senha === $row['senha']) {
                
                // Sucesso: Zera as tentativas e faz login
                $_SESSION['tentativas_login'] = 0;
                unset($_SESSION['bloqueio_fim']);
                
                $_SESSION['usuario_nome'] = $row['usuario'];
                $_SESSION['nivel'] = $row['nivel']; 
                $_SESSION['logado'] = true;

                header("Location: dashboard.php");
                exit();
            } else {
                // Erro: Incrementa tentativas
                $_SESSION['tentativas_login'] = ($_SESSION['tentativas_login'] ?? 0) + 1;
                if ($_SESSION['tentativas_login'] >= 5) {
                    $_SESSION['bloqueio_fim'] = time() + (15 * 60); // Bloqueia por 15 min
                }
                $erro = "Senha incorreta!";
            }
        } else {
            $erro = "Usuário não encontrado!";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | Solo Nunes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-black h-screen flex items-center justify-center p-4">

    <div class="bg-black p-8 rounded-3xl shadow-xl w-full max-w-sm border border-gray-200">
        
        <div class="text-center mb-8">
            <img src="../imagens/logo.jpg" alt="Solo Nunes" class="h-20 mx-auto mb-4 drop-shadow-md">
            <h1 class="text-xl font-bold text-white uppercase tracking-widest">Painel Administrativo</h1>
        </div>

        <?php if($erro): ?>
            <div class="bg-red-50 text-red-600 p-3 rounded-xl text-sm font-bold text-center mb-6 border border-red-100 flex items-center justify-center gap-2">
                <i class="fa-solid fa-circle-exclamation"></i> <?= $erro ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2 ml-1">Usuário</label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute left-4 top-3.5 text-gray-300"></i>
                    <input type="text" name="usuario" required placeholder="Digite seu login" 
                           class="w-full pl-10 p-3 bg-gray-50 border border-gray-100 rounded-xl outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all font-bold text-gray-700">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2 ml-1">Senha</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-4 top-3.5 text-gray-300"></i>
                    <input type="password" name="senha" required placeholder="Digite sua senha" 
                           class="w-full pl-10 p-3 bg-gray-50 border border-gray-100 rounded-xl outline-none focus:ring-2 focus:ring-black focus:bg-white transition-all font-bold text-gray-700">
                </div>
            </div>

            <button type="submit" <?= isset($_SESSION['bloqueio_fim']) && time() < $_SESSION['bloqueio_fim'] ? 'disabled' : '' ?> class="w-full bg-black text-white py-4 rounded-xl font-bold hover:bg-emerald-600 transition-all shadow-lg text-sm uppercase tracking-wider disabled:opacity-50 disabled:cursor-not-allowed">
                Acessar Sistema
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                &copy; 2026 Pousada Solo Nunes
            </p>
        </div>
    </div>

</body>
</html>