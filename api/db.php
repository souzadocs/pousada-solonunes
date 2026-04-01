<?php
// Isso força o servidor a mostrar o erro na tela em vez de dar a tela preta 500
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$user = "u408812913_usr_GJskipIg";

// 🚨 ATENÇÃO: COLOQUE A SENHA REAL ENTRE AS ASPAS ABAIXO! 🚨
$pass = "Xj;5X#u>"; 

$dbname = "u408812913_db_GJskipIg";

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    $conn->set_charset("utf8mb4");
    date_default_timezone_set('America/Manaus');
} catch (Exception $e) {
    // Se a senha estiver errada, a tela vai ficar branca e mostrar essa mensagem vermelha:
    die("<br><br><h3 style='color:red;'>🚨 ERRO DE CONEXÃO COM O BANCO:</h3> <b>" . $e->getMessage() . "</b>");
}
?>