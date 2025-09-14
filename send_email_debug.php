<?php
require_once 'config.php';

// Version de debug du formulaire de contact
// Cette version affiche toutes les informations au lieu de rediriger

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Debug Send Email</title></head><body>";
echo "<h1>Debug - Envoi d'Email</h1>";

// Vérifier si c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<p style='color: red;'>Cette page doit être appelée en POST</p>";
    echo "<a href='index.php#contact'>Retour au formulaire</a>";
    echo "</body></html>";
    exit;
}

// Afficher les données reçues
echo "<h2>Données reçues du formulaire :</h2>";
echo "<ul>";
echo "<li><strong>Nom :</strong> " . htmlspecialchars($_POST['name'] ?? 'MANQUANT') . "</li>";
echo "<li><strong>Email :</strong> " . htmlspecialchars($_POST['email'] ?? 'MANQUANT') . "</li>";
echo "<li><strong>Sujet :</strong> " . htmlspecialchars($_POST['subject'] ?? 'MANQUANT') . "</li>";
echo "<li><strong>Message :</strong> " . htmlspecialchars(substr($_POST['message'] ?? 'MANQUANT', 0, 100)) . "...</li>";
echo "</ul>";

// Récupérer et valider les données du formulaire
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validation des données
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo "<p style='color: red;'>❌ Validation échouée : Tous les champs sont obligatoires.</p>";
    echo "<a href='index.php#contact'>Retour au formulaire</a>";
    echo "</body></html>";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<p style='color: red;'>❌ Validation échouée : Adresse email invalide.</p>";
    echo "<a href='index.php#contact'>Retour au formulaire</a>";
    echo "</body></html>";
    exit;
}

echo "<p style='color: green;'>✅ Validation des données réussie</p>";

// Vérifier PHPMailer
if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    if (file_exists('vendor/autoload.php')) {
        require_once 'vendor/autoload.php';
        echo "<p style='color: green;'>✅ PHPMailer chargé via Composer</p>";
    } elseif (file_exists('PHPMailer/src/PHPMailer.php')) {
        require_once 'PHPMailer/src/PHPMailer.php';
        require_once 'PHPMailer/src/SMTP.php';
        require_once 'PHPMailer/src/Exception.php';
        echo "<p style='color: green;'>✅ PHPMailer chargé manuellement</p>";
    } else {
        echo "<p style='color: red;'>❌ PHPMailer non trouvé</p>";
        echo "</body></html>";
        exit;
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

try {
    // Initialiser le CMS pour récupérer la configuration
    $cms = new CMS();
    echo "<p style='color: green;'>✅ CMS initialisé</p>";
    
    // Récupérer les configurations SMTP
    $smtpHost = $cms->getConfig('smtp_host');
    $smtpPort = $cms->getConfig('smtp_port');
    $smtpUsername = $cms->getConfig('smtp_username');
    $smtpPassword = $cms->getConfig('smtp_password');
    $contactEmail = $cms->getConfig('contact_email');
    
    echo "<h2>Configuration SMTP récupérée :</h2>";
    echo "<ul>";
    echo "<li><strong>Host :</strong> " . ($smtpHost ?: '<span style="color: red;">VIDE</span>') . "</li>";
    echo "<li><strong>Port :</strong> " . ($smtpPort ?: '<span style="color: red;">VIDE</span>') . "</li>";
    echo "<li><strong>Username :</strong> " . ($smtpUsername ?: '<span style="color: red;">VIDE</span>') . "</li>";
    echo "<li><strong>Password :</strong> " . (empty($smtpPassword) ? '<span style="color: red;">VIDE</span>' : '<span style="color: green;">CONFIGURÉ</span>') . "</li>";
    echo "<li><strong>Contact Email :</strong> " . ($contactEmail ?: '<span style="color: orange;">VIDE (utilise username)</span>') . "</li>";
    echo "</ul>";
    
    // Vérifier que les configurations existent
    if (empty($smtpHost) || empty($smtpUsername) || empty($smtpPassword)) {
        echo "<p style='color: red;'>❌ Configuration SMTP incomplète</p>";
        echo "<a href='add_smtp_config.php'>Ajouter la configuration SMTP</a>";
        echo "</body></html>";
        exit;
    }
    
    echo "<p style='color: green;'>✅ Configuration SMTP complète</p>";
    
    // Créer une instance de PHPMailer
    $mail = new PHPMailer(true);
    
    // Configuration du serveur SMTP avec débogage
    $mail->isSMTP();
    $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Débogage complet
    $mail->Debugoutput = function($str, $level) {
        echo "<div style='background: #f0f0f0; border-left: 3px solid #007cba; padding: 5px; margin: 2px 0; font-family: monospace; font-size: 12px;'>";
        echo "<strong>SMTP Debug Level $level:</strong> " . htmlspecialchars($str);
        echo "</div>";
    };
    
    $mail->Host       = $smtpHost;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUsername;
    $mail->Password   = $smtpPassword;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = (int)($smtpPort ?: 587);
    $mail->CharSet    = 'UTF-8';

    // Destinataires
    $fromEmail = $smtpUsername;
    $toEmail = !empty($contactEmail) ? $contactEmail : $smtpUsername;
    
    $mail->setFrom($fromEmail, 'Astiom Photography');
    $mail->addAddress($toEmail, 'Astiom Photography');
    $mail->addReplyTo($email, $name);
    
    echo "<h2>Configuration email :</h2>";
    echo "<ul>";
    echo "<li><strong>De :</strong> $fromEmail</li>";
    echo "<li><strong>Vers :</strong> $toEmail</li>";
    echo "<li><strong>Répondre à :</strong> $email ($name)</li>";
    echo "</ul>";

    // Contenu de l'email
    $mail->isHTML(true);
    $mail->Subject = 'Nouveau message de contact: ' . $subject;
    
    $mail->Body = "
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #000; color: #fff; padding: 20px; text-align: center; }
            .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #555; }
            .value { margin-top: 5px; padding: 10px; background: #fff; border-left: 3px solid #000; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Nouveau message de contact</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <div class='label'>Nom:</div>
                    <div class='value'>" . htmlspecialchars($name) . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Email:</div>
                    <div class='value'>" . htmlspecialchars($email) . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Sujet:</div>
                    <div class='value'>" . htmlspecialchars($subject) . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Message:</div>
                    <div class='value'>" . nl2br(htmlspecialchars($message)) . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Date:</div>
                    <div class='value'>" . date('d/m/Y à H:i:s') . "</div>
                </div>
            </div>
        </div>
    </body>
    </html>
    ";

    $mail->AltBody = "Nouveau message de contact\n\n"
                   . "Nom: $name\n"
                   . "Email: $email\n"
                   . "Sujet: $subject\n"
                   . "Message: $message\n"
                   . "Date: " . date('d/m/Y à H:i:s');

    echo "<h2>Tentative d'envoi en cours...</h2>";
    echo "<div style='border: 2px solid #333; padding: 10px; margin: 10px 0; background: #f9f9f9;'>";
    
    // Envoyer l'email
    $result = $mail->send();
    
    echo "</div>";
    
    if ($result) {
        echo "<h2 style='color: green;'>🎉 EMAIL ENVOYÉ AVEC SUCCÈS !</h2>";
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 10px 0;'>";
        echo "<p><strong>L'email a été envoyé vers :</strong> $toEmail</p>";
        echo "<p><strong>Sujet :</strong> $mail->Subject</p>";
        echo "<h3>Où vérifier :</h3>";
        echo "<ol>";
        echo "<li><strong>Boîte de réception</strong> de $toEmail</li>";
        echo "<li><strong>Dossier Spam/Courrier indésirable</strong></li>";
        echo "<li><strong>Dossier Promotions</strong> (Gmail avec onglets)</li>";
        echo "<li><strong>Rechercher</strong> : \"Astiom Photography\" ou \"$subject\"</li>";
        echo "</ol>";
        echo "</div>";
        
        echo "<p><strong>Si vous ne trouvez toujours pas l'email :</strong></p>";
        echo "<ul>";
        echo "<li>Attendez 1-2 minutes (délai possible)</li>";
        echo "<li>Vérifiez les filtres Gmail</li>";
        echo "<li>Vérifiez que la boîte n'est pas pleine</li>";
        echo "<li>Essayez avec un autre email de test</li>";
        echo "</ul>";
        
    } else {
        echo "<h2 style='color: red;'>❌ ÉCHEC DE L'ENVOI</h2>";
        echo "<p>L'email n'a pas pu être envoyé.</p>";
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ ERREUR DÉTECTÉE</h2>";
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 10px 0;'>";
    echo "<p><strong>Message d'erreur :</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Code d'erreur :</strong> " . $e->getCode() . "</p>";
    echo "<p><strong>Fichier :</strong> " . $e->getFile() . " ligne " . $e->getLine() . "</p>";
    
    if (isset($mail) && !empty($mail->ErrorInfo)) {
        echo "<p><strong>Info SMTP :</strong> " . htmlspecialchars($mail->ErrorInfo) . "</p>";
    }
    echo "</div>";
}

echo "<br><br>";
echo "<a href='index.php#contact'>Retour au formulaire</a> | ";
echo "<a href='test_email_direct.php'>Test Email Direct</a> | ";
echo "<a href='check_logs.php'>Vérifier les Logs</a>";
echo "</body></html>";
?>