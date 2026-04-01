<?php
session_start();
if (!isset($_SESSION['logado'])) { header("Location: login.php"); exit(); }
include '../api/db.php';

// Busca os quartos para o select
$quartos = $conn->query("SELECT * FROM quartos ORDER BY nome ASC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Bloquear Datas - Solo Nunes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-900">

    <nav class="bg-black text-white p-4 flex justify-between items-center shadow-lg">
        <h1 class="font-bold uppercase tracking-widest text-sm">Bloqueio de Datas</h1>
        <div class="flex gap-6 text-sm items-center">
            <a href="dashboard.php" class="hover:text-emerald-500 font-medium">Voltar ao Painel</a>
        </div>
    </nav>

    <main class="max-w-2xl mx-auto p-12">
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                <i class="fa-solid fa-calendar-xmark text-red-500"></i>
                Inserir Bloqueio Manual
            </h2>
            
            <form action="../api/salvar_bloqueio.php" method="POST" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-2 uppercase">Selecionar Acomodação</label>
                    <select name="quarto_id" required class="w-full p-3 bg-gray-50 border rounded-xl outline-none focus:ring-2 focus:ring-red-500">
                        <?php while($q = $quartos->fetch_assoc()): ?>
                            <option value="<?= $q['id'] ?>"><?= $q['nome'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 mb-2 uppercase">Início do Bloqueio</label>
                        <input type="date" name="checkin" required class="w-full p-3 bg-gray-50 border rounded-xl outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 mb-2 uppercase">Fim do Bloqueio</label>
                        <input type="date" name="checkout" required class="w-full p-3 bg-gray-50 border rounded-xl outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-2 uppercase">Motivo (Opcional)</label>
                    <input type="text" name="motivo" placeholder="Ex: Manutenção do Ar-condicionado" class="w-full p-3 bg-gray-50 border rounded-xl outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <button type="submit" class="w-full bg-red-600 text-white font-bold py-4 rounded-2xl hover:bg-red-700 transition-all shadow-lg active:scale-95">
                    Bloquear Datas no Calendário
                </button>
            </form>
        </div>
    </main>
</body>
</html>