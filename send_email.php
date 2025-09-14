<?php
require_once 'config.php';

// Vérifier si PHPMailer est installé
if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    // Essayer de charger PHPMailer via Composer
    if (file_exists('vendor/autoload.php')) {
        require_once 'vendor/autoload.php';
    } else {
        // Essayer de charger PHPMailer manuellement
        if (file_exists('PHPMailer/src/PHPMailer.php')) {
            require_once 'PHPMailer/src/PHPMailer.php';
            require_once 'PHPMailer/src/SMTP.php';
            require_once 'PHPMailer/src/Exception.php';
        } else {
            die('PHPMailer n\'est pas installé. Veuillez installer PHPMailer via Composer ou télécharger les fichiers manuellement.');
        }
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$response = array('success' => false, 'message' => '');

// Vérifier si la requête est en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Méthode non autorisée.';
    header('Location: index.php?error=' . urlencode($response['message']));
    exit;
}

// Récupérer et valider les données du formulaire
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validation des données
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    $response['message'] = 'Tous les champs sont obligatoires.';
    header('Location: index.php?error=' . urlencode($response['message']));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Adresse email invalide.';
    header('Location: index.php?error=' . urlencode($response['message']));
    exit;
}

// Créer une instance de PHPMailer
$mail = new PHPMailer(true);

try {
    // Configuration SMTP directe (comme dans test_email_direct.php qui fonctionne)
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'helofreewan@gmail.com';
    $mail->Password   = 'xffi zrhi fsdp ksle';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';
    
    // Ajouter debug pour voir ce qui se passe
    $mail->SMTPDebug = 0; // Pas de debug dans le navigateur, mais on log
    
    // Headers supplémentaires pour éviter le spam
    $mail->XMailer = 'Astiom Photography Contact Form';

    // Destinataires
    $fromEmail = 'helofreewan@gmail.com';
    $toEmail = 'helofreewan@gmail.com';
    
    $mail->setFrom($fromEmail, 'Site Astiom Photography');
    $mail->addAddress($toEmail);
    $mail->addReplyTo($email, $name);
    
    // Log détaillé
    error_log("=== ENVOI EMAIL ===");
    error_log("De: $fromEmail vers: $toEmail");
    error_log("Nom: $name, Email: $email");
    error_log("Sujet original: $subject");

    // Contenu de l'email - VERSION SIMPLE
    $mail->isHTML(false); // Envoyer en texte simple
    $mail->Subject = 'Contact: ' . $subject;
    
    $mail->Body = "Nouveau message de contact\n\n"
                . "Nom: $name\n"
                . "Email: $email\n"
                . "Sujet: $subject\n"
                . "Message: $message\n"
                . "Date: " . date('d/m/Y H:i:s');

    // Envoyer l'email
    $mail->send();
    
    // Log de succès
    error_log("Email envoyé avec succès de $fromEmail vers $toEmail");
    
    $response['success'] = true;
    $response['message'] = 'Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.';
    header('Location: index.php?success=' . urlencode($response['message']));

} catch (Exception $e) {
    // Log détaillé de l'erreur
    $errorDetails = "Erreur PHPMailer: " . $e->getMessage();
    if (!empty($mail->ErrorInfo)) {
        $errorDetails .= " | SMTP Error: " . $mail->ErrorInfo;
    }
    error_log($errorDetails);
    
    // Message d'erreur plus détaillé pour le débogage
    $response['message'] = 'Erreur lors de l\'envoi du message: ' . $e->getMessage();
    header('Location: index.php?error=' . urlencode($response['message']));
}

exit;
?>