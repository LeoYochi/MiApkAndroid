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
    
    // Validar que todos los campos estén presentes
    if (!isset($data['nombreUsuario']) || !isset($data['apPaternoUsuario']) || 
        !isset($data['apMaternoUsuario']) || !isset($data['emailUsuario']) || 
        !isset($data['telefonoCelularUsuario']) || !isset($data['nombreLogin']) || 
        !isset($data['passwordLogin']) || !isset($data['idRolUsuario'])) {
        
        echo json_encode([
            'exito' => false,
            'mensaje' => 'Faltan campos requeridos'
        ]);
        exit;
    }

    // Asignar variables
    $nombreUsuario = $data['nombreUsuario'];
    $apPaternoUsuario = $data['apPaternoUsuario'];
    $apMaternoUsuario = $data['apMaternoUsuario'];
    $emailUsuario = $data['emailUsuario'];
    $telefonoCelularUsuario = $data['telefonoCelularUsuario'];
    $nombreLogin = $data['nombreLogin'];
    $passwordLogin = $data['passwordLogin']; // Debe venir hasheado desde Android
    $idRolUsuario = $data['idRolUsuario'];

    // Conexión PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Llamar al procedure con parámetros
    $stmt = $conn->prepare("CALL sp_crear_cuenta(?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bindParam(1, $nombreUsuario, PDO::PARAM_STR);
    $stmt->bindParam(2, $apPaternoUsuario, PDO::PARAM_STR);
    $stmt->bindParam(3, $apMaternoUsuario, PDO::PARAM_STR);
    $stmt->bindParam(4, $emailUsuario, PDO::PARAM_STR);
    $stmt->bindParam(5, $telefonoCelularUsuario, PDO::PARAM_STR);
    $stmt->bindParam(6, $nombreLogin, PDO::PARAM_STR);
    $stmt->bindParam(7, $passwordLogin, PDO::PARAM_STR);
    $stmt->bindParam(8, $idRolUsuario, PDO::PARAM_INT);

    $stmt->execute();

    // Obtener resultados del procedure
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result && isset($result['idUsuario'])) {
        echo json_encode([
            'exito' => true,
            'mensaje' => $result['mensaje'] ?? 'Cuenta creada exitosamente',
            'idUsuario' => $result['idUsuario']
        ]);
    } else {
        echo json_encode([
            'exito' => false,
            'mensaje' => 'Error al crear la cuenta'
        ]);
    }

} catch(PDOException $e) {
    // Manejar errores específicos de MySQL
    $errorMessage = $e->getMessage();
    
    if (strpos($errorMessage, 'email ya está registrado') !== false) {
        $mensaje = 'El email ya está registrado';
    } else if (strpos($errorMessage, 'nombre de usuario ya existe') !== false) {
        $mensaje = 'El nombre de usuario ya existe';
    } else {
        $mensaje = 'Error en el servidor: ' . $errorMessage;
    }
    
    echo json_encode([
        'exito' => false,
        'mensaje' => $mensaje
    ]);
}

// Cerrar conexión
$conn = null;
?>