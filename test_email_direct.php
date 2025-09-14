<?php
// Test direct d'envoi d'email avec PHPMailer
require_once 'config.php';

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Test Direct Email</title></head><body>";
echo "<h1>Test Direct d'Envoi d'Email</h1>";

// Vérifier PHPMailer
if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    if (file_exists('vendor/autoload.php')) {
        require_once 'vendor/autoload.php';
    } elseif (file_exists('PHPMailer/src/PHPMailer.php')) {
        require_once 'PHPMailer/src/PHPMailer.php';
        require_once 'PHPMailer/src/SMTP.php';
        require_once 'PHPMailer/src/Exception.php';
    } else {
        die('<p style="color: red;">PHPMailer introuvable !</p></body></html>');
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

try {
    echo "<h2>Configuration utilisée :</h2>";
    $host = 'smtp.gmail.com';
    $port = 587;
    $username = 'helofreewan@gmail.com';
    $password = 'xffi zrhi fsdp ksle';
    $toEmail = 'helofreewan@gmail.com';
    
    echo "<ul>";
    echo "<li><strong>Host:</strong> $host</li>";
    echo "<li><strong>Port:</strong> $port</li>";
    echo "<li><strong>Username:</strong> $username</li>";
    echo "<li><strong>Password:</strong> " . str_repeat('*', strlen($password)) . "</li>";
    echo "<li><strong>Destinataire:</strong> $toEmail</li>";
    echo "</ul>";
    
    echo "<h2>Test d'envoi en cours...</h2>";
    
    $mail = new PHPMailer(true);
    
    // Configuration SMTP
    $mail->isSMTP();
    $mail->Host = $host;
    $mail->SMTPAuth = true;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $port;
    $mail->CharSet = 'UTF-8';
    
    // Activer le débogage détaillé
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->Debugoutput = function($str, $level) {
        echo "<p style='background: #f0f0f0; padding: 5px; margin: 2px 0; font-family: monospace; font-size: 12px;'>";
        echo "<strong>Debug Level $level:</strong> " . htmlspecialchars($str);
        echo "</p>";
    };
    
    // Configuration de l'email
    $mail->setFrom($username, 'Test Astiom Photography');
    $mail->addAddress($toEmail, 'Destinataire Test');
    
    // Contenu simple
    $mail->isHTML(true);
    $mail->Subject = 'Test Email - ' . date('Y-m-d H:i:s');
    $mail->Body = '
    <h2>Email de Test</h2>
    <p>Ceci est un email de test envoyé le ' . date('d/m/Y à H:i:s') . '</p>
    <p>Si vous recevez cet email, la configuration SMTP fonctionne correctement !</p>
    <hr>
    <p style="font-size: 12px; color: #666;">
        Envoyé depuis : ' . $username . '<br>
        Vers : ' . $toEmail . '<br>
        Serveur SMTP : ' . $host . ':' . $port . '
    </p>';
    
    $mail->AltBody = 'Email de test envoyé le ' . date('d/m/Y à H:i:s') . ' - Configuration SMTP OK !';
    
    // Tentative d'envoi
    echo "<div style='border: 2px solid #333; padding: 10px; margin: 10px 0; background: #f9f9f9;'>";
    echo "<h3>Débogage SMTP :</h3>";
    
    $result = $mail->send();
    
    echo "</div>";
    
    if ($result) {
        echo "<h2 style='color: green;'>✅ EMAIL ENVOYÉ AVEC SUCCÈS !</h2>";
        echo "<p>L'email a été envoyé vers <strong>$toEmail</strong></p>";
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 10px 0;'>";
        echo "<h3>Vérifications à faire :</h3>";
        echo "<ol>";
        echo "<li><strong>Boîte de réception</strong> de $toEmail</li>";
        echo "<li><strong>Dossier Spam/Courrier indésirable</strong></li>";
        echo "<li><strong>Dossier Promotions</strong> (si Gmail avec onglets)</li>";
        echo "<li><strong>Tous les messages</strong> dans Gmail</li>";
        echo "</ol>";
        echo "<p><strong>Recherchez :</strong> \"Test Email\" ou \"Astiom Photography\"</p>";
        echo "</div>";
        
        echo "<h3>Conseils pour résoudre le problème :</h3>";
        echo "<ul>";
        echo "<li>Attendez 1-2 minutes (délai de livraison)</li>";
        echo "<li>Vérifiez les paramètres anti-spam de Gmail</li>";
        echo "<li>Ajoutez $username à vos contacts</li>";
        echo "<li>Vérifiez que votre boîte Gmail n'est pas pleine</li>";
        echo "</ul>";
        
    } else {
        echo "<h2 style='color: red;'>❌ ÉCHEC DE L'ENVOI</h2>";
        echo "<p>L'email n'a pas pu être envoyé.</p>";
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ ERREUR DÉTECTÉE</h2>";
    echo "<p><strong>Message d'erreur :</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Code d'erreur :</strong> " . $e->getCode() . "</p>";
    echo "<p><strong>Fichier :</strong> " . $e->getFile() . " ligne " . $e->getLine() . "</p>";
    
    if (isset($mail) && !empty($mail->ErrorInfo)) {
        echo "<p><strong>Info SMTP :</strong> " . $mail->ErrorInfo . "</p>";
    }
    
    echo "<h3>Solutions possibles :</h3>";
    echo "<ul>";
    echo "<li>Vérifiez que l'authentification à 2 facteurs est activée sur Gmail</li>";
    echo "<li>Vérifiez que le mot de passe d'application est correct : <code>xffi zrhi fsdp ksle</code></li>";
    echo "<li>Essayez de régénérer un nouveau mot de passe d'application</li>";
    echo "<li>Vérifiez que \"Accès moins sécurisé\" n'est pas nécessaire</li>";
    echo "</ul>";
}

echo "<br><br>";
echo "<a href='debug_db.php'>Debug BDD</a> | ";
echo "<a href='test_smtp.php'>Test SMTP</a> | ";
echo "<a href='index.php'>Retour</a>";
echo "</body></html>";
?>