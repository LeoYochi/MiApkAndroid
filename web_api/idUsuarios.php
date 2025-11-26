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
    $stmt = $conn->prepare("call miApiLeo.sp_buscar_usuario_por_id(6);
");
    $stmt->execute();

    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();

    echo json_encode($result);


      
} catch(PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
$conn = null;
?>