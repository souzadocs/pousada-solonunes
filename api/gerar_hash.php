<?php
// Defina a senha que você deseja usar para o painel administrativo
$senha_nova = "admin123";

// Gera o hash seguro que será armazenado no banco de dados
echo password_hash($senha_nova, PASSWORD_DEFAULT);
?>