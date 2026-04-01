<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Acesso - Solo Nunes</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-neutral-900 h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl p-8 shadow-2xl">
        <h2 class="text-xl font-bold mb-4">Recuperar Senha</h2>
        <p class="text-gray-500 text-sm mb-6 italic">Insira seu usuário para receber o link de recuperação no e-mail cadastrado.</p>
        
        <form action="../api/enviar_recuperacao.php" method="POST" class="space-y-4">
            <input type="text" name="usuario" placeholder="Seu usuário" required 
                   class="w-full p-4 bg-gray-50 border rounded-2xl outline-none focus:ring-2 focus:ring-emerald-500">
            <button type="submit" class="w-full bg-black text-white font-bold py-4 rounded-2xl hover:bg-emerald-600 transition-all">
                Enviar Link de Recuperação
            </button>
        </form>
    </div>
</body>
</html>