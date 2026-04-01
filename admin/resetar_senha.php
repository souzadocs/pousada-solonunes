<?php
include '../api/db.php';
$token = $_GET['token'] ?? '';

// Valida se o token existe e ainda é válido
$sql = "SELECT * FROM usuarios WHERE recuperacao_token = '$token' AND recuperacao_expira > NOW()";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Link inválido ou expirado. Solicite novamente. <a href='esqueci_senha.php'>Voltar</a>");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Nova Senha - Solo Nunes</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen p-4">
    <form action="../api/confirmar_reset.php" method="POST" class="bg-white p-8 rounded-3xl shadow-lg max-w-sm w-full">
        <h2 class="text-xl font-bold mb-4">Nova Senha</h2>
        <input type="hidden" name="token" value="<?= $token ?>">
        <input type="password" name="nova_senha" placeholder="Digite sua nova senha" required 
               class="w-full p-4 border rounded-2xl mb-4 outline-none focus:ring-2 focus:ring-emerald-500">
        <button type="submit" class="w-full bg-black text-white font-bold py-4 rounded-2xl hover:bg-emerald-600 transition-all">
            Atualizar Senha
        </button>
    </form>
</body>
</html>