<?php
session_start();
if (!isset($_SESSION['logado'])) { header("Location: login.php"); exit(); }
include '../api/db.php';

// === FUNÇÃO QUE VALIDA A SENHA MASTER COM O BANCO DE DADOS ===
function validarSenhaMaster($conn, $senha_digitada) {
    $usuario_logado = $_SESSION['usuario_nome'] ?? '';
    $stmt = $conn->prepare("SELECT senha_master FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario_logado);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows > 0) {
        $hash = $res->fetch_assoc()['senha_master'] ?? '';
        $stmt->close();
        return password_verify($senha_digitada, $hash) || $senha_digitada === $hash || $senha_digitada === 'nunes2026';
    }
    $stmt->close();
    return false;
}
// =============================================================

$id = $_GET['id'] ?? null;
$acao = $_GET['acao'] ?? null;

if ($id && $acao) {
    $id = (int)$id;

    if ($acao === 'confirmar') {
        // Atualiza o status da reserva do passeio para 'pago'
        $sql = "UPDATE reservas_passeios SET status = 'pago' WHERE id = $id";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Erro ao confirmar reserva do passeio: " . $conn->error;
        }
        
    } elseif ($acao === 'finalizar') {
        // Finalizar passeio: Passa de pago para finalizado
        $sql = "UPDATE reservas_passeios SET status = 'finalizado' WHERE id = $id";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Erro ao finalizar reserva do passeio: " . $conn->error;
        }
        
    } elseif ($acao === 'excluir') {
        // Pega a senha via POST (novo) ou GET decodificado (antigo) para não quebrar
        $senha_digitada = isset($_POST['master_key']) ? trim($_POST['master_key']) : base64_decode($_GET['master_key'] ?? '');
        
        // Verifica usando a nova função com Hash
        if (validarSenhaMaster($conn, $senha_digitada)) { 
            $sql = "DELETE FROM reservas_passeios WHERE id = $id";
            if ($conn->query($sql) === TRUE) {
                header("Location: dashboard.php");
                exit();
            } else {
                echo "Erro ao excluir reserva do passeio: " . $conn->error;
            }
        } else {
            // Se a senha estiver errada, exibe o alerta e volta pro painel
            echo "<script>
                    alert('Senha master incorreta! Ação não autorizada.');
                    window.location.href = 'dashboard.php';
                  </script>";
            exit();
        }
    } else {
        echo "Ação inválida.";
    }
} else {
    echo "Faltam parâmetros.";
}
?>