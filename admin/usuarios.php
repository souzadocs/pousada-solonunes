<?php
session_start();
include '../api/db.php';

// Segurança: Só admin acessa essa página
if (!isset($_SESSION['logado']) || $_SESSION['nivel'] !== 'admin') {
    header("Location: dashboard.php"); exit();
}

// Lógica para Adicionar Novo Usuário
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['novo_usuario'])) {
    $user = mysqli_real_escape_string($conn, $_POST['usuario']);
    $pass = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Gera o hash automático
    $nivel = $_POST['nivel'];

    $sql = "INSERT INTO usuarios (usuario, senha, nivel) VALUES ('$user', '$pass', '$nivel')";
    if ($conn->query($sql)) {
        echo "<script>alert('Usuário criado com sucesso!');</script>";
    } else {
        echo "<script>alert('Erro: Usuário já existe ou falha no banco.');</script>";
    }
}

// Lógica para Excluir
if (isset($_GET['excluir'])) {
    $id = (int)$_GET['excluir'];
    $conn->query("DELETE FROM usuarios WHERE id = $id AND usuario != '{$_SESSION['usuario_nome']}'"); // Não deixa apagar a si mesmo
    header("Location: usuarios.php");
}

$usuarios = $conn->query("SELECT * FROM usuarios ORDER BY nivel ASC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Equipe - Solo Nunes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-900">
    <nav class="bg-black text-white p-4 shadow-lg flex justify-between">
        <h1 class="font-bold uppercase tracking-widest text-sm">Gestão de Equipe</h1>
        <a href="dashboard.php" class="text-gray-300 hover:text-white">Voltar ao Painel</a>
    </nav>

    <main class="max-w-4xl mx-auto p-8">
        
        <section class="bg-white p-8 rounded-3xl shadow-sm mb-8">
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-emerald-500"></i> Novo Colaborador
            </h2>
            <form method="POST" class="flex gap-4 items-end flex-wrap">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Login</label>
                    <input type="text" name="usuario" required class="w-full p-3 border rounded-xl outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Senha</label>
                    <input type="password" name="senha" required class="w-full p-3 border rounded-xl outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div class="w-40">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Função</label>
                    <select name="nivel" class="w-full p-3 border rounded-xl bg-white outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="funcionario">Funcionário</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <button type="submit" name="novo_usuario" class="bg-black text-white px-6 py-3 rounded-xl font-bold hover:bg-emerald-600 transition-all">
                    Cadastrar
                </button>
            </form>
        </section>

        <section class="bg-white rounded-3xl shadow-sm overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-xs font-bold text-gray-400 uppercase">
                    <tr>
                        <th class="p-4">Usuário</th>
                        <th class="p-4">Nível</th>
                        <th class="p-4 text-center">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($u = $usuarios->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4 font-bold"><?= $u['usuario'] ?></td>
                        <td class="p-4">
                            <?php if($u['nivel'] == 'admin'): ?>
                                <span class="bg-black text-white text-[10px] px-2 py-1 rounded font-bold uppercase">Admin</span>
                            <?php else: ?>
                                <span class="bg-gray-200 text-gray-600 text-[10px] px-2 py-1 rounded font-bold uppercase">Funcionário</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-center">
                            <?php if($u['usuario'] !== $_SESSION['usuario_nome']): ?>
                                <a href="usuarios.php?excluir=<?= $u['id'] ?>" onclick="return confirm('Tem certeza?')" class="text-red-400 hover:text-red-600 text-xs font-bold uppercase">Excluir</a>
                            <?php else: ?>
                                <span class="text-gray-300 text-xs italic">Você</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

    </main>
</body>
</html>