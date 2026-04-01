<?php
session_start();
if (!isset($_SESSION['logado'])) { header("Location: login.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Perfil - Solo Nunes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-3xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold mb-2 text-gray-800"><i class="fa-solid fa-right-to-bracket text-emerald-500 mr-2"></i> Senha de Acesso</h2>
            <p class="text-xs text-gray-400 mb-6">Usada para fazer login no painel administrativo.</p>
            
            <form action="../api/atualizar_senha.php" method="POST" class="space-y-4">
                <input type="hidden" name="tipo_senha" value="login">
                <div>
                    <label class="block text-xs font-bold mb-2 uppercase text-gray-500">Nova Senha de Acesso</label>
                    <input type="password" name="nova_senha" required placeholder="Digite a nova senha" class="w-full p-4 border border-gray-200 rounded-2xl outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <button type="submit" class="w-full bg-black text-white font-bold py-4 rounded-2xl hover:bg-emerald-600 transition-all">
                    Salvar Senha de Acesso
                </button>
            </form>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold mb-2 text-gray-800"><i class="fa-solid fa-lock text-red-500 mr-2"></i> Senha Master</h2>
            <p class="text-xs text-gray-400 mb-6">Usada para proteger a exclusão de quartos e passeios.</p>
            
            <form action="../api/atualizar_senha.php" method="POST" class="space-y-4">
                <input type="hidden" name="tipo_senha" value="master">
                <div>
                    <label class="block text-xs font-bold mb-2 uppercase text-gray-500">Nova Senha Master</label>
                    <input type="password" name="nova_senha" required placeholder="Digite a nova senha master" class="w-full p-4 border border-gray-200 rounded-2xl outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <button type="submit" class="w-full bg-red-500 text-white font-bold py-4 rounded-2xl hover:bg-red-600 transition-all shadow-lg text-sm">
                    Atualizar Senha Master
                </button>
            </form>
        </div>
        
    </div>
    <div class="text-center mt-8">
        <a href="dashboard.php" class="text-sm font-bold text-gray-400 hover:text-gray-800 transition-colors"><i class="fa-solid fa-arrow-left mr-1"></i> Voltar ao Painel</a>
    </div>
</body>
</html>