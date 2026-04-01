<?php
session_start();
if (!isset($_SESSION['logado'])) { header("Location: login.php"); exit(); }
include '../api/db.php';

// AUTO-CRIAÇÃO: Garante que a coluna 'ordem' exista no banco para salvar a posição
$check_col = $conn->query("SHOW COLUMNS FROM quartos LIKE 'ordem'");
if($check_col && $check_col->num_rows == 0) {
    $conn->query("ALTER TABLE quartos ADD COLUMN ordem INT DEFAULT 999");
}

// SALVAR NOVA ORDEM (VIA ARRASTAR E SOLTAR)
$dados_json = json_decode(file_get_contents('php://input'), true);
if (isset($dados_json['ordem'])) {
    $stmt = $conn->prepare("UPDATE quartos SET ordem = ? WHERE nome LIKE ?");
    foreach ($dados_json['ordem'] as $item) {
        $peso = (int)$item['ordem'];
        // Usamos o LIKE para garantir que se for Térreo, 1º Andar etc, ele atualize a gaveta toda junta.
        $nome_busca = $conn->real_escape_string($item['nome_base']) . '%'; 
        $stmt->bind_param("is", $peso, $nome_busca);
        $stmt->execute();
    }
    $stmt->close();
    echo json_encode(['sucesso' => true]);
    exit();
}

$quartos = $conn->query("SELECT * FROM quartos ORDER BY ordem ASC, nome ASC, preco_noite ASC");
$bloqueios = $conn->query("SELECT r.*, q.nome as nome_quarto, q.andar FROM reservas r JOIN quartos q ON r.quarto_id = q.id WHERE r.status = 'bloqueado' ORDER BY r.checkin ASC");
$passeios = $conn->query("SELECT * FROM  passeios ORDER BY nome ASC");
$bloqueios_passeios = $conn->query("SELECT r.*, p.nome as nome_passeio FROM reservas_passeios r JOIN  passeios p ON r.passeio_id = p.id WHERE r.status = 'bloqueado' ORDER BY r.checkin ASC");

// Agrupando os quartos para exibir a sanfona no painel
$quartos_agrupados = [];
if ($quartos && $quartos->num_rows > 0) {
    while($q = $quartos->fetch_assoc()) {
        $nome_base = trim(preg_replace('/[-\s\d]+$/', '', trim($q['nome'])));
        
        if (!isset($quartos_agrupados[$nome_base])) {
            $quartos_agrupados[$nome_base] = [
                'info' => $q,
                'opcoes' => []
            ];
        }
        $quartos_agrupados[$nome_base]['opcoes'][] = $q;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Pousada - Solo Nunes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .grupo-draggable { cursor: grab; }
        .grupo-draggable:active { cursor: grabbing; }
        .drag-over { outline: 2px dashed #10b981; outline-offset: 2px; background-color: #ecfdf5; border-radius: 1rem; }
        .dragging { opacity: 0.4; }
        #toast-ordem { transition: opacity 0.4s ease; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">

    <nav class="bg-black text-white p-4 flex justify-between items-center shadow-lg relative z-50">
        <div class="flex items-center gap-4">
            <h1 class="font-bold uppercase tracking-widest text-xs md:text-sm">Gestão Pousada</h1>
        </div>
        <div class="flex gap-4 md:gap-6 text-xs md:text-sm items-center font-medium">
            <a href="dashboard.php" class="hover:text-emerald-500 transition-colors bg-gray-800 px-3 py-1.5 rounded-lg flex items-center gap-2">
                <i class="fa-solid fa-house md:mr-1"></i><span class="hidden md:inline">Painel</span>
            </a>
            <a href="login.php" class="text-red-400 hover:text-red-600 transition-colors font-bold flex items-center gap-2">
                <i class="fa-solid fa-right-from-bracket md:mr-1"></i><span class="hidden md:inline">Sair</span>
            </a>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-4 md:p-8">
        
        <h2 class="text-xl md:text-2xl font-black mb-6 text-gray-800 uppercase tracking-wider border-b-2 border-emerald-500 pb-2 inline-block">Módulo de Quartos</h2>

        <section class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-gray-100 mb-10">
            <h2 class="text-lg font-bold mb-6 flex items-center gap-3">
                <i class="fa-solid fa-plus-circle text-emerald-500"></i>
                Adicionar Nova Acomodação
            </h2>
            <form action="../api/gerenciar_quartos.php?acao=adicionar" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row flex-wrap items-start md:items-end gap-4">
                
                <div class="w-full md:flex-1 md:min-w-[200px]">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Nome Base (Ex: Suíte Casal)</label>
                    <input type="text" name="nome" placeholder="Nome igual para agrupar..." required class="w-full p-3 bg-gray-50 border rounded-xl outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="w-full md:w-32">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Andar / Opção</label>
                    <select name="andar" class="w-full p-3 bg-gray-50 border rounded-xl outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="Térreo">Térreo</option>
                        <option value="1º Andar">1º Andar</option>
                        <option value="2º Andar">2º Andar</option>
                        <option value="Reduzido">Reduzido</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4 w-full md:w-auto">
                    <div class="md:w-24">
                        <label class="block text-[10px] font-bold text-indigo-500 uppercase mb-2">Unidades</label>
                        <input type="number" name="quantidade" value="1" min="1" required class="w-full p-3 bg-indigo-50 border border-indigo-200 text-indigo-900 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 font-bold text-center">
                    </div>
                    <div class="md:w-24">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Pessoas</label>
                        <input type="number" name="capacidade" value="2" min="1" required class="w-full p-3 bg-gray-50 border rounded-xl outline-none focus:ring-2 focus:ring-emerald-500 text-center">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 w-full md:w-auto">
                    <div class="md:w-40">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Tipo de Cama</label>
                        <input type="text" name="tipo_cama" placeholder="Ex: 1 Casal" required class="w-full p-3 bg-gray-50 border rounded-xl outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div class="md:w-32">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Preço/Noite</label>
                        <input type="number" step="0.01" name="preco" placeholder="0,00" required class="w-full p-3 bg-gray-50 border rounded-xl outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="w-full">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Descrição Curta</label>
                    <input type="text" name="descricao" placeholder="Ex: Ar-condicionado, Wi-fi..." required class="w-full p-3 bg-gray-50 border rounded-xl outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="w-full md:flex-1">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Foto Deste Quarto</label>
                    <input type="file" name="imagens[]" multiple accept="image/*" class="w-full p-2 bg-gray-50 border rounded-xl outline-none focus:ring-2 focus:ring-emerald-500 text-xs text-gray-500">
                </div>

                <button type="submit" class="w-full md:w-auto bg-black text-white px-8 py-3 rounded-xl font-bold hover:bg-emerald-600 transition-all shadow-md mt-2 md:mt-0 uppercase tracking-widest text-xs">
                    Cadastrar
                </button>
            </form>
        </section>

        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-700"><i class="fa-solid fa-layer-group mr-2"></i> Acomodações Agrupadas</h3>
            <span class="text-[11px] text-gray-400 italic flex items-center gap-1">
                <i class="fa-solid fa-grip-lines text-emerald-400"></i> Arraste os cards para reordenar
            </span>
        </div>
        
        <div id="lista-grupos" class="space-y-4 mb-12">
            <?php if(!empty($quartos_agrupados)): ?>
                <?php foreach($quartos_agrupados as $nome_quarto => $grupo): ?>
                <div 
                    class="grupo-draggable bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden"
                    draggable="true"
                    data-nome-base="<?= htmlspecialchars($nome_quarto) ?>"
                >
                    <button onclick="toggleAccordion('<?= md5($nome_quarto) ?>')" class="w-full p-5 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors">
                        <div class="flex items-center gap-4 text-left">
                            <span class="text-gray-300 hover:text-emerald-500 transition-colors mr-1 cursor-grab" title="Arrastar para reordenar">
                                <i class="fa-solid fa-grip-vertical text-lg"></i>
                            </span>
                            <h3 class="font-black text-lg text-gray-800"><?= $nome_quarto ?></h3>
                            <span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-widest"><?= count($grupo['opcoes']) ?> Variações</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-gray-400 transition-transform" id="icon-<?= md5($nome_quarto) ?>"></i>
                    </button>

                    <div id="content-<?= md5($nome_quarto) ?>" class="hidden border-t border-gray-100">
                        <?php foreach($grupo['opcoes'] as $q): ?>
                        <div class="p-5 border-b border-gray-50 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 hover:bg-emerald-50/30 transition-colors">
                            
                            <div class="flex items-start gap-4 flex-1">
                                <div class="flex flex-col gap-1">
                                    <?php 
                                        $galeria = json_decode($q['galeria'] ?? '[]', true);
                                        if(!empty($galeria) && is_array($galeria)): 
                                    ?>
                                        <img src="../<?= $galeria[0] ?>" class="w-20 h-20 rounded-lg object-cover shadow-sm border border-gray-200">
                                    <?php elseif(!empty($q['imagem_url'])): ?>
                                        <img src="../<?= $q['imagem_url'] ?>" class="w-20 h-20 rounded-lg object-cover shadow-sm border border-gray-200">
                                    <?php else: ?>
                                        <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400"><i class="fa-regular fa-image"></i></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="bg-gray-800 text-white text-[10px] px-2 py-1 rounded uppercase font-bold tracking-wider">
                                            <i class="fa-solid fa-stairs mr-1"></i><?= $q['andar'] ?? 'Padrão' ?>
                                        </span>
                                        <span class="text-emerald-600 font-black">R$ <?= number_format($q['preco_noite'], 2, ',', '.') ?></span>
                                    </div>
                                    <div class="flex flex-wrap gap-4 text-xs text-gray-500 mt-2 font-medium">
                                        <span><i class="fa-solid fa-door-open mr-1"></i> <?= $q['quantidade'] ?> und.</span>
                                        <span><i class="fa-solid fa-user-group mr-1"></i> Até <?= $q['capacidade'] ?></span>
                                        <span class="text-yellow-600"><i class="fa-solid fa-bed mr-1"></i> <?= !empty($q['tipo_cama']) ? $q['tipo_cama'] : 'Não informado' ?></span>
                                    </div>
                                    <p class="text-[10px] text-gray-400 italic mt-1 max-w-md"><?= $q['descricao'] ?></p>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2 w-full lg:w-auto mt-4 lg:mt-0">
                                <button onclick="abrirModalEdicao(<?= $q['id'] ?>, '<?= addslashes($q['nome']) ?>', <?= $q['quantidade'] ?? 1 ?>, '<?= $q['capacidade'] ?>', '<?= $q['preco_noite'] ?>', '<?= addslashes($q['descricao']) ?>', '<?= addslashes($q['andar'] ?? 'Térreo') ?>', '<?= addslashes($q['tipo_cama'] ?? '') ?>')" class="flex-1 bg-white border border-gray-200 text-blue-600 hover:bg-blue-50 px-3 py-2 rounded-lg text-xs font-bold uppercase transition flex items-center justify-center gap-1 shadow-sm">
                                    <i class="fa-solid fa-pen"></i> Editar
                                </button>
                                <button onclick="abrirModalBloqueio(<?= $q['id'] ?>, '<?= addslashes($q['nome'] . " - " . $q['andar']) ?>', 'quarto')" class="flex-1 bg-orange-50 border border-orange-100 text-orange-600 hover:bg-orange-100 hover:text-orange-700 px-3 py-2 rounded-lg text-xs font-bold uppercase transition flex items-center justify-center gap-1 shadow-sm">
                                    <i class="fa-solid fa-lock"></i> Bloquear
                                </button>
                                <button onclick="pedirSenhaAcao('../api/gerenciar_quartos.php?acao=excluir&id=<?= $q['id'] ?>')" class="flex-none text-red-400 hover:text-red-600 p-2 rounded-lg transition-colors" title="Excluir Variação">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center text-gray-400">Nenhum quarto cadastrado.</div>
            <?php endif; ?>
        </div>

        <h2 class="text-xl md:text-2xl font-black mb-6 mt-16 text-gray-800 uppercase tracking-wider border-b-2 border-blue-600 pb-2 inline-block">Módulo de Passeios</h2>

        <section class="bg-blue-50 p-6 md:p-8 rounded-3xl shadow-sm border border-blue-100 mb-10">
            <h2 class="text-lg font-bold mb-6 flex items-center gap-3 text-blue-900">
                <i class="fa-solid fa-water text-blue-600"></i>
                Adicionar Novo Passeio
            </h2>
            <form action="../api/gerenciar_quartos.php?acao=adicionar_passeio" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row flex-wrap items-start md:items-end gap-4">
                <div class="w-full md:flex-1 md:min-w-[200px]">
                    <label class="block text-[10px] font-bold text-blue-800 uppercase mb-2">Nome do Passeio</label>
                    <input type="text" name="nome" placeholder="Ex: Encontro das Águas" required class="w-full p-3 bg-white border border-blue-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-600">
                </div>
                <div class="grid grid-cols-2 gap-4 w-full md:w-auto">
                    <div class="md:w-24">
                        <label class="block text-[10px] font-bold text-blue-800 uppercase mb-2">Max. Pessoas</label>
                        <input type="number" name="capacidade" value="10" min="1" required class="w-full p-3 bg-white border border-blue-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-600 text-center">
                    </div>
                    <div class="md:w-32">
                        <label class="block text-[10px] font-bold text-blue-800 uppercase mb-2">Preço/Pessoa</label>
                        <input type="number" step="0.01" name="preco" placeholder="0,00" required class="w-full p-3 bg-white border border-blue-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                </div>
                <div class="w-full">
                    <label class="block text-[10px] font-bold text-blue-800 uppercase mb-2">Descrição Curta</label>
                    <input type="text" name="descricao" placeholder="Roteiro e detalhes..." required class="w-full p-3 bg-white border border-blue-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-600">
                </div>
                <div class="w-full md:flex-1">
                    <label class="block text-[10px] font-bold text-blue-800 uppercase mb-2">Imagens do Passeio</label>
                    <input type="file" name="imagens[]" multiple accept="image/*" class="w-full p-2 bg-white border border-blue-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-600 text-xs text-gray-500">
                </div>
                <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-md mt-2 md:mt-0 uppercase tracking-widest text-xs">
                    Cadastrar Passeio
                </button>
            </form>
        </section>

        <h3 class="font-bold text-gray-700 mb-4"><i class="fa-solid fa-tree mr-2"></i> Passeios Disponíveis no Site</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            <?php if($passeios->num_rows > 0): ?>
                <?php while($p = $passeios->fetch_assoc()): ?>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 flex flex-col gap-4 relative transition-all hover:shadow-md">
                    
                    <div class="flex gap-4 items-center">
                        <?php if(!empty($p['imagem_url'])): ?>
                            <img src="../<?= $p['imagem_url'] ?>" alt="<?= $p['nome'] ?>" class="w-20 h-20 object-cover rounded-xl shadow-sm border-[2px] border-solo-gold">
                        <?php else: ?>
                            <div class="w-20 h-20 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400 text-xs text-center p-2">Sem foto</div>
                        <?php endif; ?>
                        <div>
                            <h3 class="font-black text-lg text-blue-800 leading-tight"><?= $p['nome'] ?></h3>
                            <span class="bg-blue-50 text-blue-600 border border-blue-100 px-2 py-1 rounded-md text-[10px] font-bold inline-block mt-2 uppercase tracking-widest">
                                Até <?= $p['capacidade'] ?> Pessoas
                            </span>
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 italic flex-1 bg-gray-50 p-3 rounded-lg"><?= $p['descricao'] ?></p>

                    <div class="flex justify-between items-center mt-1 border-t pt-4 border-gray-100">
                        <span class="text-xs text-gray-400 uppercase font-bold tracking-widest">Preço/Pessoa:</span>
                        <span class="text-blue-600 font-black text-xl">R$ <?= number_format($p['preco'], 2, ',', '.') ?></span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mt-2">
                        <button onclick="abrirModalEdicaoPasseio(<?= $p['id'] ?>, '<?= addslashes($p['nome']) ?>', '<?= $p['capacidade'] ?>', '<?= $p['preco'] ?>', '<?= addslashes($p['descricao']) ?>')" class="bg-blue-50 border border-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white py-3 rounded-xl text-xs font-bold uppercase transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-pen-to-square"></i> Editar
                        </button>
                        <button onclick="abrirModalBloqueio(<?= $p['id'] ?>, '<?= addslashes($p['nome']) ?>', 'passeio')" class="bg-orange-50 border border-orange-100 text-orange-600 hover:bg-orange-600 hover:text-white py-3 rounded-xl text-xs font-bold uppercase transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-calendar-xmark"></i> Bloquear
                        </button>
                        <button onclick="pedirSenhaAcao('../api/gerenciar_quartos.php?acao=excluir_passeio&id=<?= $p['id'] ?>')" class="col-span-2 text-red-400 hover:text-red-600 text-[10px] font-bold italic py-2 uppercase tracking-widest flex items-center justify-center gap-1 mt-1">
                            <i class="fa-solid fa-trash-can"></i> Excluir Passeio
                        </button>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center text-gray-400">Nenhum passeio cadastrado.</div>
            <?php endif; ?>
        </div>

        <h2 class="text-xl md:text-2xl font-black mb-6 mt-16 text-gray-800 uppercase tracking-wider border-b-2 border-orange-500 pb-2 inline-block">Áreas Interditadas</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php 
            $temBloqueios = false;
            while($b = $bloqueios->fetch_assoc()): 
                $temBloqueios = true;
            ?>
            <div class="bg-orange-50 p-5 rounded-2xl shadow-sm border border-orange-200 flex flex-col gap-3 relative transition-all hover:shadow-md">
                <div class="flex justify-between items-start">
                    <span class="text-emerald-700 font-bold text-[10px] uppercase tracking-widest bg-white border border-emerald-100 px-2 py-1 rounded-md shadow-sm"><i class="fa-solid fa-bed mr-1"></i> Quarto</span>
                    <span class="text-lg text-orange-500"><i class="fa-solid fa-lock"></i></span>
                </div>
                
                <h3 class="font-black text-xl text-gray-800 mt-1"><?= $b['nome_quarto'] ?> <span class="text-xs text-gray-500 italic block mt-1">(<?= $b['andar'] ?>)</span></h3>
                
                <div class="bg-white p-4 rounded-xl shadow-inner text-sm border border-orange-100 flex-1 flex flex-col justify-center">
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Motivo do Bloqueio</span>
                    <p class="text-gray-600 font-medium text-sm mb-3"><?= str_replace("🚫 BLOQUEIO: ", "", $b['nome_cliente']) ?></p>
                    
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Período Interditado</span>
                    <p class="font-black text-orange-700 text-sm">De <?= date('d/m/y', strtotime($b['checkin'])) ?> a <?= date('d/m/y', strtotime($b['checkout'])) ?></p>
                </div>

                <button onclick="pedirSenhaAcao('../api/excluir_reserva.php?id=<?= $b['id'] ?>&tipo=quarto')" class="w-full bg-white border-2 border-orange-200 text-orange-600 py-3 rounded-xl font-bold text-xs hover:bg-orange-600 hover:text-white hover:border-orange-600 transition-all shadow-sm uppercase tracking-wider mt-2 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-unlock"></i> Liberar Vaga
                </button>
            </div>
            <?php endwhile; ?>

            <?php 
            while($bp = $bloqueios_passeios->fetch_assoc()): 
                $temBloqueios = true;
            ?>
            <div class="bg-orange-50 p-5 rounded-2xl shadow-sm border border-orange-200 flex flex-col gap-3 relative transition-all hover:shadow-md">
                <div class="flex justify-between items-start">
                    <span class="text-blue-700 font-bold text-[10px] uppercase tracking-widest bg-white border border-blue-100 px-2 py-1 rounded-md shadow-sm"><i class="fa-solid fa-tree mr-1"></i> Passeio</span>
                    <span class="text-lg text-orange-500"><i class="fa-solid fa-lock"></i></span>
                </div>
                
                <h3 class="font-black text-xl text-gray-800 mt-1"><?= $bp['nome_passeio'] ?></h3>
                
                <div class="bg-white p-4 rounded-xl shadow-inner text-sm border border-orange-100 flex-1 flex flex-col justify-center">
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Motivo do Bloqueio</span>
                    <p class="text-gray-600 font-medium text-sm mb-3"><?= str_replace("🚫 BLOQUEIO: ", "", $bp['nome_cliente']) ?></p>
                    
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Período Interditado</span>
                    <p class="font-black text-orange-700 text-sm">De <?= date('d/m/y', strtotime($bp['checkin'])) ?> a <?= date('d/m/y', strtotime($bp['checkout'])) ?></p>
                </div>

                <button onclick="pedirSenhaAcao('../api/excluir_reserva.php?id=<?= $bp['id'] ?>&tipo=passeio')" class="w-full bg-white border-2 border-orange-200 text-orange-600 py-3 rounded-xl font-bold text-xs hover:bg-orange-600 hover:text-white hover:border-orange-600 transition-all shadow-sm uppercase tracking-wider mt-2 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-unlock"></i> Liberar Vaga
                </button>
            </div>
            <?php endwhile; ?>

            <?php if(!$temBloqueios): ?>
                <div class="col-span-full bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center text-gray-400">Nenhuma área da pousada está interditada no momento.</div>
            <?php endif; ?>
        </div>

    </main>

    <div id="modalEdicao" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden">
            <div class="bg-blue-500 p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold text-white" id="modalEdicaoTitulo">Editar Quarto</h3>
                <button onclick="document.getElementById('modalEdicao').classList.add('hidden')" class="text-white hover:text-blue-200">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="../api/gerenciar_quartos.php?acao=editar" method="POST" enctype="multipart/form-data" class="p-4 md:p-8 space-y-5">
                <input type="hidden" name="quarto_id" id="editQuartoId">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Nome Base da Acomodação</label>
                        <input type="text" name="nome" id="editNome" required class="w-full p-3 border rounded-xl outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Andar / Opção</label>
                        <select name="andar" id="editAndar" class="w-full p-3 border rounded-xl outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="Térreo">Térreo</option>
                            <option value="1º Andar">1º Andar</option>
                            <option value="2º Andar">2º Andar</option>
                            <option value="Reduzido">Reduzido</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-indigo-400 uppercase mb-1">Unidades</label>
                        <input type="number" name="quantidade" id="editQuantidade" min="1" required class="w-full p-3 bg-indigo-50 border border-indigo-200 text-indigo-900 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 font-bold text-center">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Pessoas</label>
                        <input type="number" name="capacidade" id="editCapacidade" min="1" required class="w-full p-3 border rounded-xl outline-none focus:ring-2 focus:ring-blue-500 text-center">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Preço/Noite</label>
                        <input type="number" step="0.01" name="preco" id="editPreco" required class="w-full p-3 border rounded-xl outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Tipo de Cama</label>
                        <input type="text" name="tipo_cama" id="editTipoCama" required class="w-full p-3 border rounded-xl outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Descrição Curta</label>
                        <input type="text" name="descricao" id="editDescricao" required class="w-full p-3 border rounded-xl outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Novas Imagens (Substitui)</label>
                        <input type="file" name="imagens[]" multiple accept="image/*" class="w-full p-2 bg-gray-50 border rounded-xl outline-none focus:ring-2 focus:ring-blue-500 text-xs text-gray-500">
                    </div>
                </div>
                
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                    <label class="block text-[10px] font-black text-blue-400 uppercase mb-1 flex items-center gap-1"><i class="fa-solid fa-lock"></i> Senha Master</label>
                    <input type="password" name="master_key" required placeholder="Confirme a senha para salvar" class="w-full p-3 bg-white border border-blue-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500 text-center font-bold">
                </div>
                
                <button type="submit" class="w-full bg-blue-500 text-white py-4 rounded-xl font-bold hover:bg-blue-600 transition-all shadow-lg uppercase tracking-widest text-sm">SALVAR MODIFICAÇÕES</button>
            </form>
        </div>
    </div>

    <div id="modalEdicaoPasseio" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden">
            <div class="bg-blue-600 p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold text-white" id="modalEdicaoTituloPasseio">Editar Passeio</h3>
                <button onclick="document.getElementById('modalEdicaoPasseio').classList.add('hidden')" class="text-white hover:text-blue-200">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="../api/gerenciar_quartos.php?acao=editar_passeio" method="POST" enctype="multipart/form-data" class="p-4 md:p-8 space-y-5">
                <input type="hidden" name="passeio_id" id="editPasseioId">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Nome do Passeio</label>
                        <input type="text" name="nome" id="editNomePasseio" required class="w-full p-3 border rounded-xl outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Nova Imagem</label>
                        <input type="file" name="imagens[]" accept="image/*" class="w-full p-2 bg-gray-50 border rounded-xl outline-none focus:ring-2 focus:ring-blue-600 text-xs text-gray-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Capacidade</label>
                            <input type="number" name="capacidade" id="editCapacidadePasseio" min="1" required class="w-full p-3 border rounded-xl outline-none focus:ring-2 focus:ring-blue-600 text-center">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Preço/Pessoa</label>
                            <input type="number" step="0.01" name="preco" id="editPrecoPasseio" required class="w-full p-3 border rounded-xl outline-none focus:ring-2 focus:ring-blue-600">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Descrição</label>
                        <input type="text" name="descricao" id="editDescricaoPasseio" required class="w-full p-3 border rounded-xl outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                </div>
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 mt-2">
                    <label class="block text-[10px] font-black text-blue-400 uppercase mb-1 flex items-center gap-1"><i class="fa-solid fa-lock"></i> Senha Master</label>
                    <input type="password" name="master_key" required placeholder="Confirme a senha para salvar" class="w-full p-3 bg-white border border-blue-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-600 text-center font-bold">
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg uppercase tracking-widest text-sm">SALVAR</button>
            </form>
        </div>
    </div>

    <div id="modalBloqueio" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden">
            <div class="bg-orange-500 p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold text-white flex items-center gap-2"><i class="fa-solid fa-triangle-exclamation"></i> Interditar Vaga</h3>
                <button onclick="document.getElementById('modalBloqueio').classList.add('hidden')" class="text-white hover:text-orange-200">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            
            <form action="../api/salvar_bloqueio.php" method="POST" class="p-6 md:p-8 space-y-5" id="formBloqueio">
                <input type="hidden" name="acao" value="bloquear">
                <input type="hidden" name="tipo_bloqueio" id="tipoBloqueioItem">
                <input type="hidden" name="item_id" id="idBloqueioItem">
                
                <p class="text-sm text-gray-500 mb-2" id="textoBloqueio">Bloqueando vendas para:<br><strong class="text-gray-800 text-lg" id="nomeBloqueioItem"></strong></p>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Data Início</label>
                        <input type="date" name="data_inicio" required class="w-full p-3 border rounded-xl outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Data Final</label>
                        <input type="date" name="data_fim" required class="w-full p-3 border rounded-xl outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Motivo (Opcional)</label>
                    <input type="text" name="motivo" placeholder="Ex: Manutenção..." class="w-full p-3 border rounded-xl outline-none focus:ring-2 focus:ring-orange-500">
                </div>

                <div class="bg-orange-50 p-4 rounded-xl border border-orange-100">
                    <label class="block text-[10px] font-black text-orange-500 uppercase mb-1 flex items-center gap-1"><i class="fa-solid fa-lock"></i> Senha Master</label>
                    <input type="password" name="master_key" required placeholder="Confirme sua senha" class="w-full p-3 bg-white border border-orange-200 rounded-lg outline-none focus:ring-2 focus:ring-orange-500 text-center font-bold">
                </div>

                <button type="submit" class="w-full bg-orange-500 text-white py-4 rounded-xl font-bold hover:bg-orange-600 transition-all shadow-lg text-xs uppercase tracking-widest">Confirmar Bloqueio</button>
            </form>
        </div>
    </div>

    <form id="formAcaoSegura" method="POST" style="display:none;">
        <input type="password" name="master_key" id="inputSenhaSegura">
    </form>

    <div id="toast-ordem" class="fixed bottom-6 right-6 z-50 hidden bg-emerald-600 text-white text-sm font-bold px-5 py-3 rounded-xl shadow-xl flex items-center gap-2">
        <i class="fa-solid fa-check-circle"></i> Ordem salva com sucesso!
    </div>

    <script>
        // =============================================
        // ACCORDION
        // =============================================
        function toggleAccordion(id) {
            const content = document.getElementById('content-' + id);
            const icon = document.getElementById('icon-' + id);
            content.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }

        // =============================================
        // DRAG AND DROP
        // =============================================
        const lista = document.getElementById('lista-grupos');
        let draggingEl = null;

        lista.addEventListener('dragstart', (e) => {
            draggingEl = e.target.closest('.grupo-draggable');
            if (!draggingEl) return;
            setTimeout(() => draggingEl.classList.add('dragging'), 0);
            e.dataTransfer.effectAllowed = 'move';
        });

        lista.addEventListener('dragend', () => {
            if (!draggingEl) return;
            draggingEl.classList.remove('dragging');
            document.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over'));
            draggingEl = null;
            salvarOrdem();
        });

        lista.addEventListener('dragover', (e) => {
            e.preventDefault();
            const alvo = e.target.closest('.grupo-draggable');
            if (!alvo || alvo === draggingEl) return;

            document.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over'));
            alvo.classList.add('drag-over');

            const rect = alvo.getBoundingClientRect();
            const meio = rect.top + rect.height / 2;
            if (e.clientY < meio) {
                lista.insertBefore(draggingEl, alvo);
            } else {
                lista.insertBefore(draggingEl, alvo.nextSibling);
            }
        });

        lista.addEventListener('dragleave', (e) => {
            const alvo = e.target.closest('.grupo-draggable');
            if (alvo) alvo.classList.remove('drag-over');
        });

        // =============================================
        // SALVAR ORDEM DIRETO NESTE ARQUIVO (VIA FETCH)
        // =============================================
        function salvarOrdem() {
            const grupos = lista.querySelectorAll('.grupo-draggable');
            const ordem = [];
            grupos.forEach((el, index) => {
                ordem.push({
                    nome_base: el.getAttribute('data-nome-base'),
                    ordem: index
                });
            });

            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ordem })
            })
            .then(res => res.json())
            .then(data => {
                if (data.sucesso) mostrarToast();
            })
            .catch(err => console.error('Erro ao salvar ordem:', err));
        }

        function mostrarToast() {
            const toast = document.getElementById('toast-ordem');
            toast.classList.remove('hidden');
            toast.style.opacity = '1';
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.classList.add('hidden'), 400);
            }, 2500);
        }

        function abrirModalEdicao(id, nome, quantidade, capacidade, preco, descricao, andar, tipoCama) {
            document.getElementById('editQuartoId').value = id;
            document.getElementById('editNome').value = nome;
            document.getElementById('editQuantidade').value = quantidade;
            document.getElementById('editCapacidade').value = capacidade;
            document.getElementById('editPreco').value = preco;
            document.getElementById('editDescricao').value = descricao;
            document.getElementById('editAndar').value = andar;
            document.getElementById('editTipoCama').value = tipoCama;
            
            document.getElementById('modalEdicaoTitulo').innerText = 'Editar Quarto: ' + nome + ' (' + andar + ')';
            document.getElementById('modalEdicao').classList.remove('hidden');
        }

        function abrirModalEdicaoPasseio(id, nome, capacidade, preco, descricao) {
            document.getElementById('editPasseioId').value = id;
            document.getElementById('editNomePasseio').value = nome;
            document.getElementById('editCapacidadePasseio').value = capacidade;
            document.getElementById('editPrecoPasseio').value = preco;
            document.getElementById('editDescricaoPasseio').value = descricao;
            document.getElementById('modalEdicaoTituloPasseio').innerText = 'Editar Passeio: ' + nome;
            document.getElementById('modalEdicaoPasseio').classList.remove('hidden');
        }

        function abrirModalBloqueio(id, nome, tipo) {
            document.getElementById('idBloqueioItem').value = id;
            document.getElementById('nomeBloqueioItem').innerText = nome;
            document.getElementById('tipoBloqueioItem').value = tipo;
            document.getElementById('modalBloqueio').classList.remove('hidden');
        }

        function pedirSenhaAcao(urlDestino) {
            let senha = prompt("Ação Restrita! Digite sua Senha Master para confirmar:");
            if (senha != null && senha.trim() !== "") {
                const form = document.getElementById('formAcaoSegura');
                form.action = urlDestino;
                document.getElementById('inputSenhaSegura').value = senha;
                form.submit();
            }
        }
    </script>
</body>
</html>