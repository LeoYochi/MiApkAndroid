<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Importar PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// RUTAS CORRECTAS para tu estructura de carpetas
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// TUS DATOS GMAIL
$smtp_username = 'nesnesdswii64@gmail.com';
$smtp_password = 'toad ytih yfbh sufu';
$smtp_from = 'nesnesdswii64@gmail.com';
$smtp_from_name = 'ApiLeoBase';

// Log para depuración
error_log("🔄 enviar_email.php ejecutándose");

function enviarEmailPHPMailer($emailDestino, $nombreUsuario, $token) {
    global $smtp_username, $smtp_password, $smtp_from, $smtp_from_name;
    
    error_log("📧 Intentando enviar email a: $emailDestino");
    
    $mail = new PHPMailer(true);
    
    try {
        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_username;
        $mail->Password = $smtp_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
         $mail->CharSet = 'UTF-8'; 
        $mail->SMTPDebug = 2; // ← AGREGAR PARA DEBUG
        $mail->Debugoutput = 'error_log'; // ← ENVIAR DEBUG A LOGS
        
        // Configuración del email
        $mail->setFrom($smtp_from, $smtp_from_name);
        $mail->addAddress($emailDestino, $nombreUsuario);
        $mail->addReplyTo($smtp_from, $smtp_from_name);
        
        // Contenido - Texto plano
        $mail->isHTML(false);
        $mail->Subject = 'Recuperación de Contraseña - ApiLeoBase';
        
        $mail->Body = "
Hola $nombreUsuario,

Has solicitado recuperar tu contraseña en ApiLeoBase.

🔑 Tu token de recuperación es: $token

⏰ Este token expira en 5 minutos.

Ingresa este token en la aplicación para restablecer tu contraseña.

Si no solicitaste este cambio, ignora este mensaje.

--
Equipo ApiLeoBase
        ";
        
        error_log("✅ Configuración PHPMailer lista, intentando enviar...");
        
        // Enviar email
        if ($mail->send()) {
            error_log("✅ Email enviado correctamente a: $emailDestino");
            return ['exito' => 1, 'mensaje' => 'Email enviado correctamente'];
        } else {
            error_log("❌ Error enviando email: {$mail->ErrorInfo}");
            return ['exito' => 0, 'mensaje' => "Error PHPMailer: {$mail->ErrorInfo}"];
        }
        
    } catch (Exception $e) {
        error_log("🚨 Exception en PHPMailer: {$e->getMessage()}");
        return ['exito' => 0, 'mensaje' => "Error PHPMailer: {$mail->ErrorInfo}"];
    }
}

// PROCESAR REQUEST
try {
    $data = json_decode(file_get_contents("php://input"), true);
    error_log("📥 Datos recibidos: " . print_r($data, true));
    
    // Validar campos requeridos
    if (!isset($data['email']) || !isset($data['token']) || !isset($data['nombreUsuario'])) {
        error_log("❌ Faltan campos requeridos");
        echo json_encode([
            'exito' => 0,
            'mensaje' => 'Faltan campos requeridos'
        ]);
        exit;
    }

    $email = $data['email'];
    $token = $data['token'];
    $nombreUsuario = $data['nombreUsuario'];
    
    error_log("📧 Procesando email para: $email, token: $token");
    
    // Validar formato de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_log("❌ Formato de email inválido: $email");
        echo json_encode([
            'exito' => 0,
            'mensaje' => 'Formato de email inválido'
        ]);
        exit;
    }
    
    // Enviar email con PHPMailer
    $resultado = enviarEmailPHPMailer($email, $nombreUsuario, $token);
    error_log("📤 Resultado envío: " . print_r($resultado, true));
    
    echo json_encode($resultado);

} catch (Exception $e) {
    error_log("🚨 Error general: " . $e->getMessage());
    echo json_encode([
        'exito' => 0,
        'mensaje' => 'Error general: ' . $e->getMessage()
    ]);
}
?>