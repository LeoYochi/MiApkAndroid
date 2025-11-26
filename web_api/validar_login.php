<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost:3306";
$username = "root";
$password = "Mysql123!";
$dbname = "miApiLeo";

try {
    // Obtener datos JSON del request
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Validar campos requeridos
    if (!isset($data['nombreLogin']) || !isset($data['passwordLogin'])) {
        echo json_encode([
            'exito' => false,
            'mensaje' => 'Faltan campos requeridos',
            'idLogin' => null,
            'idUsuario' => null,
            'idRolUsuario' => null
        ]);
        exit;
    }

    $nombreLogin = $data['nombreLogin'];
    $passwordLogin = $data['passwordLogin']; // Debe venir hasheado desde Android

    // Conexión PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Llamar al procedure sp_validar_login
    $stmt = $conn->prepare("CALL sp_validar_login(?, ?)");
    $stmt->bindParam(1, $nombreLogin, PDO::PARAM_STR);
    $stmt->bindParam(2, $passwordLogin, PDO::PARAM_STR);

    $stmt->execute();

    // Obtener resultados del procedure
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo json_encode([
            'exito' => $result['exito'],
            'mensaje' => $result['mensaje'],
            'idLogin' => $result['idLogin'],
            'idUsuario' => $result['idUsuario'],
            'idRolUsuario' => $result['idRolUsuario']
        ]);
    } else {
        echo json_encode([
            'exito' => false,
            'mensaje' => 'Error en el servidor',
            'idLogin' => null,
            'idUsuario' => null,
            'idRolUsuario' => null
        ]);
    }

} catch(PDOException $e) {
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error de conexión: ' . $e->getMessage(),
        'idLogin' => null,
        'idUsuario' => null,
        'idRolUsuario' => null
    ]);
}

// Cerrar conexión
$conn = null;
?>