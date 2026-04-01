<?php
include 'db.php'; // Puxa a conexão que criamos antes

$quarto_id = $_POST['quarto_id'];
$checkin = $_POST['checkin'];
$checkout = $_POST['checkout'];

// SQL para contar se existe alguma reserva que "bate" com essas datas
$sql = "SELECT COUNT(*) as total FROM reservas 
        WHERE quarto_id = '$quarto_id' 
        AND status = 'pago'
        AND (
            (checkin <= '$checkin' AND checkout > '$checkin') OR
            (checkin < '$checkout' AND checkout >= '$checkout') OR
            ('$checkin' <= checkin AND '$checkout' > checkin)
        )";

$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($row['total'] > 0) {
    echo json_encode(['disponivel' => false, 'mensagem' => 'Desculpe, este quarto já está ocupado nestas datas.']);
} else {
    echo json_encode(['disponivel' => true, 'mensagem' => 'Quarto disponível!']);
}
?>