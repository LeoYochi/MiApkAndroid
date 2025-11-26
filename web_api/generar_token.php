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
    if (!isset($data['nombreLogin'])) {
        echo json_encode([
            'exito' => false,
            'mensaje' => 'Faltan campos requeridos',
            'token' => null,
            'email' => null,
            'expiracion' => null
        ]);
        exit;
    }

    $nombreLogin = $data['nombreLogin'];

    // Conexión PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Llamar al procedure sp_GenerarTokenRecuperacion
    $stmt = $conn->prepare("CALL sp_GenerarTokenRecuperacion(?)");
    $stmt->bindParam(1, $nombreLogin, PDO::PARAM_STR);

    $stmt->execute();

    // Obtener TODOS los resultados del procedure
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        // Convertir el éxito de string a boolean
        $exito = ($result['exito'] === '1' || $result['exito'] === 1 || $result['exito'] === true);
        
        echo json_encode([
            'exito' => $exito,
            'mensaje' => $result['mensaje'],
            'token' => $result['token'],
            'email' => $result['email'],
            'expiracion' => $result['expiracion']
        ]);
    } else {
        echo json_encode([
            'exito' => false,
            'mensaje' => 'Error al ejecutar el procedure',
            'token' => null,
            'email' => null,
            'expiracion' => null
        ]);
    }

} catch(PDOException $e) {
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error de conexión: ' . $e->getMessage(),
        'token' => null,
        'email' => null,
        'expiracion' => null
    ]);
}

// Cerrar conexión
$conn = null;
?>