<?php
session_start();
if (!isset($_SESSION['logado'])) { header("Location: login.php"); exit(); }
include '../api/db.php';

// Captura o filtro atual da URL (padrão: mostrar todos)
$filtro_status = isset($_GET['status']) ? $_GET['status'] : 'todos';
$where_status = "r.status != 'bloqueado'";

if ($filtro_status !== 'todos') {
    $where_status .= " AND r.status = '" . $conn->real_escape_string($filtro_status) . "'";
}

// Busca Reservas de QUARTOS com o filtro aplicado
$sql_quartos = "SELECT r.*, q.nome as nome_quarto 
        FROM reservas r 
        JOIN quartos q ON r.quarto_id = q.id 
        WHERE $where_status 
        ORDER BY r.id DESC";
$reservas = $conn->query($sql_quartos);

// Busca Reservas de PASSEIOS AVULSOS (Filtro de status não se aplica a eles agora, mostramos todos)
$sql_passeios = "SELECT r.*, p.nome as nome_passeio 
        FROM reservas_passeios r 
        JOIN passeios p ON r.passeio_id = p.id 
        ORDER BY r.id DESC";
$reservas_passeios = $conn->query($sql_passeios);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Painel Admin - Solo Nunes</title>
    <style>
        #modal-senha { display: none; }
        /* Esconde a barra de rolagem dos filtros mas mantém funcionando no dedo */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-100">
    <nav class="bg-black text-white p-4 flex justify-between items-center shadow-lg">
        <div class="flex items-center gap-4">
            <h1 class="font-bold uppercase tracking-widest text-sm">Admin Solo Nunes</h1>
            <span class="text-[10px] bg-gray-800 px-2 py-1 rounded text-gray-400 uppercase hidden lg:inline-block">
                <?= $_SESSION['nivel'] ?? 'Funcionario' ?>
            </span>
        </div>

        <div class="flex gap-4 md:gap-6 text-xl md:text-sm font-medium text-gray-300 items-center">
            
            <a href="perfil.php" class="hover:text-emerald-500 transition" title="Perfil">
                <i class="fa-solid fa-user md:hidden"></i>
                <span class="hidden md:inline-block">Perfil</span>
            </a>

            <?php if (isset($_SESSION['nivel']) && $_SESSION['nivel'] === 'admin'): ?>
                <a href="relatorios.php" class="hover:text-emerald-500 transition text-emerald-400" title="Relatórios">
                    <i class="fa-solid fa-chart-line md:mr-1"></i>
                    <span class="hidden md:inline-block">Relatórios</span>
                </a>
                <a href="usuarios.php" class="hover:text-emerald-500 transition text-emerald-400" title="Equipe">
                    <i class="fa-solid fa-users md:mr-1"></i>
                    <span class="hidden md:inline-block">Equipe</span>
                </a>
            <?php endif; ?>

            <a href="ajuda.php" class="hover:text-emerald-500 transition" title="Ajuda">
                <i class="fa-solid fa-circle-question md:hidden"></i>
                <span class="hidden md:inline-block">Ajuda</span>
            </a>
            
            <a href="quartos.php" class="hover:text-emerald-500 transition-colors bg-gray-800 px-3 py-1.5 rounded-lg" title="Gerenciar Quartos e Passeios">
                <i class="fa-solid fa-bed md:mr-2"></i><span class="hidden md:inline-block text-sm">Gerenciar</span>
            </a>
            
            <a href="login.php?logout=true" class="text-red-400 hover:text-red-600 transition-colors font-bold" title="Sair">
                <i class="fa-solid fa-right-from-bracket md:mr-2"></i><span class="hidden md:inline-block text-sm">Sair</span>
            </a>
        </div>
    </nav>

    <main class="p-4 md:p-8 max-w-7xl mx-auto">
        
        <div class="flex gap-3 overflow-x-auto pb-2 mb-6 scrollbar-hide">
            <a href="?status=todos" class="<?= $filtro_status == 'todos' ? 'bg-black text-white' : 'bg-white border border-gray-200 text-gray-600' ?> px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap shadow-sm transition-colors">Todos</a>
            <a href="?status=pendente" class="<?= $filtro_status == 'pendente' ? 'bg-yellow-100 text-yellow-700 border border-yellow-200' : 'bg-white border border-gray-200 text-gray-600' ?> px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap shadow-sm transition-colors">Pendentes</a>
            <a href="?status=pago" class="<?= $filtro_status == 'pago' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-white border border-gray-200 text-gray-600' ?> px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap shadow-sm transition-colors">Pagos</a>
            <a href="?status=finalizado" class="<?= $filtro_status == 'finalizado' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-white border border-gray-200 text-gray-600' ?> px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap shadow-sm transition-colors">Finalizados</a>
        </div>

        <h2 class="text-xl md:text-2xl font-bold mb-6 flex items-center gap-2"><i class="fa-solid fa-bed text-emerald-600"></i> Reservas de Hospedagem</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            <?php if($reservas->num_rows > 0): ?>
                <?php while($res = $reservas->fetch_assoc()): ?>
                <?php 
                    $status = trim(strtolower($res['status']));
                    if ($status == 'pago') {
                        $cor_bg = 'bg-emerald-50 border-emerald-200';
                        $cor_tag = 'bg-emerald-100 text-emerald-700'; 
                        $txt = 'PAGO'; 
                        $icone = 'fa-circle-check';
                    } elseif ($status == 'finalizado') {
                        $cor_bg = 'bg-blue-50 border-blue-200';
                        $cor_tag = 'bg-blue-100 text-blue-700'; 
                        $txt = 'FINALIZADO'; 
                        $icone = 'fa-flag-checkered';
                    } else {
                        $cor_bg = 'bg-white border-gray-200';
                        $cor_tag = 'bg-yellow-100 text-yellow-700'; 
                        $txt = 'PENDENTE'; 
                        $icone = 'fa-clock';
                    }
                ?>
                <div class="<?= $cor_bg ?> p-5 rounded-2xl shadow-sm border flex flex-col gap-4 relative transition-all hover:shadow-md">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-black text-lg text-gray-800 leading-tight"><?= $res['nome_quarto'] ?></h3>
                            <p class="text-sm font-bold text-gray-600 mt-1"><i class="fa-regular fa-user mr-1 text-gray-400"></i> <?= $res['nome_cliente'] ?></p>
                            <p class="text-xs text-gray-500 mt-1"><i class="fa-brands fa-whatsapp mr-1 text-emerald-500"></i> <?= $res['whatsapp'] ?></p>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] tracking-widest uppercase font-black flex items-center gap-1 <?= $cor_tag ?>">
                            <i class="fa-solid <?= $icone ?>"></i> <?= $txt ?>
                        </span>
                    </div>

                    <?php if(!empty($res['passeios_inclusos']) && $res['passeios_inclusos'] !== 'Nenhum passeio adicional.'): ?>
                        <div class="bg-orange-50 border border-orange-100 rounded-xl p-2.5 text-xs font-bold text-orange-700 flex items-center gap-2">
                            <i class="fa-solid fa-compass text-orange-500"></i> <?= $res['passeios_inclusos'] ?>
                        </div>
                    <?php endif; ?>

                    <div class="grid grid-cols-2 gap-3 bg-white p-3 rounded-xl shadow-inner border border-gray-100 text-sm mt-auto">
                        <div>
                            <span class="block text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-0.5">Check-in</span>
                            <span class="font-black text-gray-700"><?= date('d/m/Y', strtotime($res['checkin'])) ?></span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-0.5">Check-out</span>
                            <span class="font-black text-gray-700"><?= date('d/m/Y', strtotime($res['checkout'])) ?></span>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 pt-1">
                        <?php if ($status == 'pendente'): ?>
                            <a href="acoes_reserva.php?id=<?= $res['id'] ?>&acao=confirmar" class="flex-1 bg-black hover:bg-emerald-600 text-white text-center py-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm uppercase tracking-wider">Confirmar</a>
                        <?php elseif ($status == 'pago'): ?>
                            <a href="acoes_reserva.php?id=<?= $res['id'] ?>&acao=finalizar" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm uppercase tracking-wider"><i class="fa-solid fa-flag-checkered"></i> Finalizar</a>
                        <?php endif; ?>
                        
                        <a href="gerar_comprovante.php?id=<?= $res['id'] ?>" class="flex-1 bg-white border-2 border-gray-200 hover:border-blue-500 hover:text-blue-600 text-gray-600 text-center py-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 uppercase tracking-wider"><i class="fa-solid fa-file-pdf"></i> PDF</a>
                    </div>
                    
                    <button onclick="pedirSenhaAcao('acoes_reserva.php?id=<?= $res['id'] ?>&acao=excluir')" class="w-full text-red-400 hover:text-red-600 text-[10px] font-bold italic mt-1 py-2 uppercase tracking-widest flex items-center justify-center gap-1">
                        <i class="fa-solid fa-trash-can"></i> Excluir Reserva
                    </button>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center text-gray-400">
                    Nenhuma hospedagem encontrada com este status.
                </div>
            <?php endif; ?>
        </div>

        <h2 class="text-xl md:text-2xl font-bold mb-6 flex items-center gap-2"><i class="fa-solid fa-compass text-orange-600"></i> Agendamento de Passeios</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            <?php if($reservas_passeios && $reservas_passeios->num_rows > 0): ?>
                <?php while($rp = $reservas_passeios->fetch_assoc()): ?>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 flex flex-col gap-4 relative transition-all hover:shadow-md">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-black text-lg text-orange-600 leading-tight"><?= $rp['nome_passeio'] ?></h3>
                            <p class="text-sm font-bold text-gray-600 mt-1"><i class="fa-regular fa-user mr-1 text-gray-400"></i> <?= $rp['nome_cliente'] ?></p>
                            <p class="text-xs text-gray-500 mt-1"><i class="fa-brands fa-whatsapp mr-1 text-emerald-500"></i> <?= $rp['whatsapp'] ?></p>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] tracking-widest uppercase font-black flex items-center gap-1 bg-gray-100 text-gray-600">
                            <i class="fa-brands fa-whatsapp"></i> Via Zap
                        </span>
                    </div>

                    <div class="bg-orange-50 p-3 rounded-xl shadow-inner border border-orange-100 text-sm mt-auto text-center">
                        <span class="block text-[10px] uppercase tracking-widest text-orange-400 font-bold mb-0.5">Data Agendada</span>
                        <span class="font-black text-orange-700 text-lg"><?= date('d/m/Y', strtotime($rp['checkin'])) ?></span>
                    </div>

                    <div class="flex flex-wrap gap-2 pt-1">
                        <a href="gerar_comprovante_passeio.php?id=<?= $rp['id'] ?>" class="w-full bg-white border-2 border-gray-200 hover:border-blue-500 hover:text-blue-600 text-gray-600 text-center py-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 uppercase tracking-wider"><i class="fa-solid fa-file-pdf"></i> Visualizar Ficha PDF</a>
                    </div>
                    
                    <button onclick="pedirSenhaAcao('acoes_reserva_passeio.php?id=<?= $rp['id'] ?>&acao=excluir')" class="w-full text-red-400 hover:text-red-600 text-[10px] font-bold italic mt-1 py-2 uppercase tracking-widest flex items-center justify-center gap-1">
                        <i class="fa-solid fa-trash-can"></i> Excluir Agendamento
                    </button>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center text-gray-400">
                    Nenhum passeio agendado até o momento.
                </div>
            <?php endif; ?>
        </div>

    </main>

    <div id="modal-senha" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-[100] p-4">
        <div class="bg-white p-8 rounded-2xl shadow-2xl max-w-sm w-full">
            <h3 class="text-lg font-bold mb-2">Segurança</h3>
            <p class="text-sm text-gray-500 mb-6 font-bold uppercase tracking-widest text-[10px]">Confirme a senha de ação para excluir.</p>
            <input type="password" id="input-senha-master" class="w-full p-4 bg-gray-100 rounded-xl mb-6 outline-none focus:ring-2 focus:ring-emerald-500 text-center text-xl font-bold" placeholder="••••">
            <div class="flex gap-4">
                <button onclick="fecharModalSenha()" class="flex-1 py-3 font-bold text-gray-400">Cancelar</button>
                <button onclick="validarESubmeter()" class="flex-1 bg-black text-white py-3 rounded-xl font-bold hover:bg-emerald-600 transition-all">Confirmar</button>
            </div>
        </div>
    </div>

    <script>
        let linkPendente = '';

        function pedirSenhaAcao(url) {
            linkPendente = url;
            document.getElementById('modal-senha').style.display = 'flex';
            document.getElementById('input-senha-master').focus();
        }

        function fecharModalSenha() {
            document.getElementById('modal-senha').style.display = 'none';
            document.getElementById('input-senha-master').value = '';
        }

        function validarESubmeter() {
            const senha = document.getElementById('input-senha-master').value;
            if(senha) {
                window.location.href = linkPendente + "&master_key=" + btoa(senha);
            } else {
                alert("Digite a senha!");
            }
        }
    </script>
</body>
</html>