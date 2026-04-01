<?php
session_start();
include 'db.php';

// Pega o nome do usuário que está logado (ou tenta 'admin' se não tiver ninguém)
$usuario_para_virar_chefe = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'admin';

// 1. Atualiza no Banco de Dados
$sql = "UPDATE usuarios SET nivel = 'admin' WHERE usuario = '$usuario_para_virar_chefe'";
$conn->query($sql);

// 2. Atualiza a Sessão do Navegador AGORA (sem precisar deslogar)
$_SESSION['nivel'] = 'admin';

echo "<h1>PRONTO! 👑</h1>";
echo "<p>O usuário <b>$usuario_para_virar_chefe</b> agora é ADMIN.</p>";
echo "<p>Sua permissão no banco e na sessão foi atualizada.</p>";
echo "<br><a href='../admin/dashboard.php' style='font-size:20px; font-weight:bold; color:green;'>>> CLIQUE AQUI PARA VOLTAR AO PAINEL <<</a>";
?>