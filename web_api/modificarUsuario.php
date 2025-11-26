<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$servername = "localhost:3306";
$username = "root";
$password = "Mysql123!";
$dbname = "miApiLeo";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("call miApiLeo.sp_modificar_Usuario(6, 'Army', 'Pasten', 'Martinez', 'arcana2007@gmail.com', '5567611711');
");
    $stmt->execute();
    $mensaje ="Datos modificados correctamente.";
  


    echo json_encode(array('mensaje' => $mensaje));

    
} catch(PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
$conn = null;
?>

