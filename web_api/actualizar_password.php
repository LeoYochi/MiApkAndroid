<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

if(isset($data['token']) && isset($data['nuevoPassword']) && isset($data['nombreLogin'])) {
    $token = $data['token'];
    $nuevoPassword = $data['nuevoPassword'];
    $nombreLogin = $data['nombreLogin'];
    
    $sql = "CALL sp_ActualizarPassword('$token', '$nuevoPassword', '$nombreLogin')";
    $result = $conn->query($sql);
    
    if($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode([
            'mensaje' => 'Error al actualizar contraseña',
            'exito' => false
        ]);
    }
} else {
    echo json_encode([
        'mensaje' => 'Faltan parámetros',
        'exito' => false
    ]);
}

while($conn->more_results()) {
    $conn->next_result();
}
$conn->close();
?>