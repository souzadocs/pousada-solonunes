<?php
session_start();
if (!isset($_SESSION['logado'])) { header("Location: ../admin/login.php"); exit(); }
include 'db.php';

$acao = $_GET['acao'] ?? '';

// === FUNÇÃO: Valida a Senha Master buscando do Banco de Dados ===
function validarSenhaMaster($conn, $senha_digitada) {
    $usuario_logado = $_SESSION['usuario_nome'] ?? '';
    $stmt = $conn->prepare("SELECT senha_master FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario_logado);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows > 0) {
        $hash = $res->fetch_assoc()['senha_master'] ?? '';
        $stmt->close();
        // Verifica se bate com o hash novo, ou com a senha em texto (se não mudou ainda), ou com a de emergência
        return password_verify($senha_digitada, $hash) || $senha_digitada === $hash || $senha_digitada === 'nunes2026';
    }
    $stmt->close();
    return false;
}
// ====================================================================

function fazerUploadImagens($files, $pasta) {
    $caminhos = [];
    if (isset($files['name']) && !empty($files['name'][0])) {
        $diretorio = "../imagens/" . $pasta . "/";
        if (!is_dir($diretorio)) { mkdir($diretorio, 0777, true); }
        $total_arquivos = count($files['name']);
        
        for ($i = 0; $i < $total_arquivos; $i++) {
            $nome_original = $files['name'][$i];
            $tmp_name = $files['tmp_name'][$i];
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $extensao = strtolower(pathinfo($nome_original, PATHINFO_EXTENSION));
                $novo_nome = uniqid($pasta . '_') . '.' . $extensao;
                $destino = $diretorio . $novo_nome;
                if (move_uploaded_file($tmp_name, $destino)) {
                    $caminhos[] = "imagens/" . $pasta . "/" . $novo_nome;
                }
            }
        }
    }
    return $caminhos;
}

// ==========================================
// AÇÕES PARA QUARTOS 
// ==========================================

if ($acao === 'adicionar') {
    $nome = trim($_POST['nome']);
    $quantidade = (int)$_POST['quantidade']; // NOVO CAMPO: Quantidade de quartos
    $capacidade = (int)$_POST['capacidade'];
    $preco = (float)$_POST['preco'];
    $descricao = trim($_POST['descricao']);
    $andar = trim($_POST['andar'] ?? 'Térreo');
    $tipo_cama = trim($_POST['tipo_cama'] ?? 'Não informado');
    
    $caminhos_imagens = fazerUploadImagens($_FILES['imagens'], 'quartos');
    $imagem_url = !empty($caminhos_imagens) ? $caminhos_imagens[0] : '';
    $galeria_json = json_encode($caminhos_imagens);
    
    $stmt = $conn->prepare("INSERT INTO quartos (nome, quantidade, capacidade, preco_noite, descricao, imagem_url, galeria, andar, tipo_cama) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siidsssss", $nome, $quantidade, $capacidade, $preco, $descricao, $imagem_url, $galeria_json, $andar, $tipo_cama);
    $stmt->execute();
    $stmt->close();
    
    header("Location: ../admin/quartos.php");
    exit();
}

if ($acao === 'editar') {
    $senha_digitada = trim($_POST['master_key'] ?? '');
    
    // Validação da Senha Master
    if (!validarSenhaMaster($conn, $senha_digitada)) { 
        die("<script>alert('Senha master incorreta!'); window.location.href='../admin/quartos.php';</script>");
    }

    $id = (int)$_POST['quarto_id'];
    $nome = trim($_POST['nome']);
    $quantidade = (int)$_POST['quantidade'];
    $capacidade = (int)$_POST['capacidade'];
    $preco = (float)$_POST['preco'];
    $descricao = trim($_POST['descricao']);
    
    // CAPTURANDO OS NOVOS CAMPOS NA EDIÇÃO
    $andar = trim($_POST['andar'] ?? 'Térreo');
    $tipo_cama = trim($_POST['tipo_cama'] ?? 'Não informado');

    $caminhos_novas_imagens = fazerUploadImagens($_FILES['imagens'], 'quartos');
    
    if (!empty($caminhos_novas_imagens)) {
        $imagem_url = $caminhos_novas_imagens[0];
        $galeria_json = json_encode($caminhos_novas_imagens);
        
        $stmt = $conn->prepare("UPDATE quartos SET nome = ?, quantidade = ?, capacidade = ?, preco_noite = ?, descricao = ?, imagem_url = ?, galeria = ?, andar = ?, tipo_cama = ? WHERE id = ?");
        // string(s), int(i), int(i), double(d), string(s), string(s), string(s), string(s), string(s), int(i) -> 9 strings/doubles/ints + 1 int
        $stmt->bind_param("siidsssssi", $nome, $quantidade, $capacidade, $preco, $descricao, $imagem_url, $galeria_json, $andar, $tipo_cama, $id);
    } else {
        $stmt = $conn->prepare("UPDATE quartos SET nome = ?, quantidade = ?, capacidade = ?, preco_noite = ?, descricao = ?, andar = ?, tipo_cama = ? WHERE id = ?");
        $stmt->bind_param("siidsssi", $nome, $quantidade, $capacidade, $preco, $descricao, $andar, $tipo_cama, $id);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: ../admin/quartos.php");
    exit();
}

if ($acao === 'excluir') {
    $id = (int)$_GET['id'];
    // Captura a senha de forma oculta via POST (Segurança do Histórico)
    $senha_digitada = trim($_POST['master_key'] ?? '');
    
    // Validação da Senha Master
    if (!validarSenhaMaster($conn, $senha_digitada)) { 
        die("<script>alert('Senha master incorreta!'); window.location.href='../admin/quartos.php';</script>");
    }
    
    $stmt_res = $conn->prepare("DELETE FROM reservas WHERE quarto_id = ?");
    $stmt_res->bind_param("i", $id);
    $stmt_res->execute();
    $stmt_res->close();
    
    $stmt_qto = $conn->prepare("DELETE FROM quartos WHERE id = ?");
    $stmt_qto->bind_param("i", $id);
    $stmt_qto->execute();
    $stmt_qto->close();
    
    header("Location: ../admin/quartos.php");
    exit();
}

// ==========================================
// AÇÕES PARA PASSEIOS
// ==========================================

if ($acao === 'adicionar_passeio') {
    $nome = trim($_POST['nome']);
    $capacidade = (int)$_POST['capacidade'];
    $preco = (float)$_POST['preco'];
    $descricao = trim($_POST['descricao']);
    
    $caminhos_imagens = fazerUploadImagens($_FILES['imagens'], 'passeios');
    $imagem_url = !empty($caminhos_imagens) ? $caminhos_imagens[0] : '';
    $galeria_json = json_encode($caminhos_imagens);
    
    $stmt = $conn->prepare("INSERT INTO passeios (nome, capacidade, preco, descricao, imagem_url, galeria) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sidsss", $nome, $capacidade, $preco, $descricao, $imagem_url, $galeria_json);
    $stmt->execute();
    $stmt->close();
    
    header("Location: ../admin/quartos.php");
    exit();
}

if ($acao === 'editar_passeio') {
    $senha_digitada = trim($_POST['master_key'] ?? '');
    
    // Validação da Senha Master
    if (!validarSenhaMaster($conn, $senha_digitada)) { 
        die("<script>alert('Senha master incorreta!'); window.location.href='../admin/quartos.php';</script>");
    }

    $id = (int)$_POST['passeio_id'];
    $nome = trim($_POST['nome']);
    $capacidade = (int)$_POST['capacidade'];
    $preco = (float)$_POST['preco'];
    $descricao = trim($_POST['descricao']);

    $caminhos_novas_imagens = fazerUploadImagens($_FILES['imagens'], 'passeios');
    
    if (!empty($caminhos_novas_imagens)) {
        $imagem_url = $caminhos_novas_imagens[0];
        $galeria_json = json_encode($caminhos_novas_imagens);
        
        $stmt = $conn->prepare("UPDATE passeios SET nome = ?, capacidade = ?, preco = ?, descricao = ?, imagem_url = ?, galeria = ? WHERE id = ?");
        $stmt->bind_param("sidsssi", $nome, $capacidade, $preco, $descricao, $imagem_url, $galeria_json, $id);
    } else {
        $stmt = $conn->prepare("UPDATE passeios SET nome = ?, capacidade = ?, preco = ?, descricao = ? WHERE id = ?");
        $stmt->bind_param("sidsi", $nome, $capacidade, $preco, $descricao, $id);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: ../admin/quartos.php");
    exit();
}

if ($acao === 'excluir_passeio') {
    $id = (int)$_GET['id'];
    // Captura a senha de forma oculta via POST (Segurança do Histórico)
    $senha_digitada = trim($_POST['master_key'] ?? '');
    
    // Validação da Senha Master
    if (!validarSenhaMaster($conn, $senha_digitada)) { 
        die("<script>alert('Senha master incorreta!'); window.location.href='../admin/quartos.php';</script>");
    }
    
    $stmt_res_pass = $conn->prepare("DELETE FROM reservas_passeios WHERE passeio_id = ?");
    $stmt_res_pass->bind_param("i", $id);
    $stmt_res_pass->execute();
    $stmt_res_pass->close();
    
    $stmt_pass = $conn->prepare("DELETE FROM passeios WHERE id = ?");
    $stmt_pass->bind_param("i", $id);
    $stmt_pass->execute();
    $stmt_pass->close();
    
    header("Location: ../admin/quartos.php");
    exit();
}

if ($acao === 'bloquear_passeio') {
    $senha_digitada = trim($_POST['master_key'] ?? '');
    
    // Validação da Senha Master
    if (!validarSenhaMaster($conn, $senha_digitada)) { 
        die("<script>alert('Senha master incorreta!'); window.location.href='../admin/quartos.php';</script>");
    }

    $passeio_id = (int)$_POST['passeio_id'];
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $horario = $_POST['horario'];
    $motivo = "🚫 BLOQUEIO: " . trim($_POST['motivo']);
    $status_bloqueado = 'bloqueado';
    $valor_zero = 0;

    $stmt = $conn->prepare("INSERT INTO reservas_passeios (passeio_id, nome_cliente, whatsapp, checkin, checkout, status, valor_total) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssd", $passeio_id, $motivo, $horario, $checkin, $checkout, $status_bloqueado, $valor_zero);
    $stmt->execute();
    $stmt->close();
    
    header("Location: ../admin/quartos.php");
    exit();
}
?>