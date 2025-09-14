<?php
// Test ultra-minimal d'envoi d'email
require_once 'config.php';

// Charger PHPMailer
if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
} elseif (file_exists('PHPMailer/src/PHPMailer.php')) {
    require_once 'PHPMailer/src/PHPMailer.php';
    require_once 'PHPMailer/src/SMTP.php';
    require_once 'PHPMailer/src/Exception.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "<!DOCTYPE html><html><head><title>Test Ultra Simple</title></head><body>";
echo "<h1>Test Ultra Simple</h1>";

try {
    $mail = new PHPMailer(true);
    
    // Configuration minimale
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'helofreewan@gmail.com';
    $mail->Password = 'xffi zrhi fsdp ksle';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    // Email ultra-simple
    $mail->setFrom('helofreewan@gmail.com', 'Test');
    $mail->addAddress('helofreewan@gmail.com');
    $mail->Subject = 'TEST';
    $mail->Body = 'TEST';
    
    echo "<p>Envoi en cours...</p>";
    
    if ($mail->send()) {
        echo "<h2 style='color: green;'>✅ EMAIL ENVOYÉ !</h2>";
        echo "<p><strong>Vérifiez maintenant votre Gmail :</strong></p>";
        echo "<ul>";
        echo "<li>Boîte de réception</li>";
        echo "<li><strong>SPAM</strong> (très important !)</li>";
        echo "<li>Tous les messages</li>";
        echo "<li>Recherchez 'TEST'</li>";
        echo "</ul>";
    } else {
        echo "<h2 style='color: red;'>❌ ÉCHEC</h2>";
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ ERREUR</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}

echo "<br><a href='index.php'>Retour</a>";
echo "</body></html>";
?>