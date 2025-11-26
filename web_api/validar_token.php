<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

if(isset($data['token'])) {
    $token = $data['token'];
    
    $sql = "CALL sp_ValidarTokenRecuperacion('$token')";
    $result = $conn->query($sql);
    
    if($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode([
            'mensaje' => 'Error al validar token',
            'exito' => false,
            'idLogin' => null,
            'nombreLogin' => null
        ]);
    }
} else {
    echo json_encode([
        'mensaje' => 'Token no proporcionado',
        'exito' => false,
        'idLogin' => null,
        'nombreLogin' => null
    ]);
}

while($conn->more_results()) {
    $conn->next_result();
}
$conn->close();
?>