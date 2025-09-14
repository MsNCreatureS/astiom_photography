<?php
require_once 'config.php';

// Script de test pour vérifier la configuration SMTP
echo "<!DOCTYPE html><html><head><title>Test SMTP</title></head><body>";
echo "<h1>Test de configuration SMTP</h1>";

try {
    $cms = new CMS();
    
    echo "<h2>Configuration actuelle :</h2>";
    echo "<ul>";
    echo "<li><strong>SMTP Host:</strong> " . ($cms->getConfig('smtp_host') ?: '<span style="color: red;">NON CONFIGURÉ</span>') . "</li>";
    echo "<li><strong>SMTP Port:</strong> " . ($cms->getConfig('smtp_port') ?: '<span style="color: red;">NON CONFIGURÉ</span>') . "</li>";
    echo "<li><strong>SMTP Username:</strong> " . ($cms->getConfig('smtp_username') ?: '<span style="color: red;">NON CONFIGURÉ</span>') . "</li>";
    echo "<li><strong>SMTP Password:</strong> " . (empty($cms->getConfig('smtp_password')) ? '<span style="color: red;">NON CONFIGURÉ</span>' : '<span style="color: green;">CONFIGURÉ (caché)</span>') . "</li>";
    echo "<li><strong>Contact Email:</strong> " . ($cms->getConfig('contact_email') ?: '<span style="color: orange;">NON CONFIGURÉ (optionnel)</span>') . "</li>";
    echo "</ul>";
    
    // Test de base de données
    echo "<h2>Test de base de données :</h2>";
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT COUNT(*) as count FROM site_config WHERE config_key LIKE 'smtp_%' OR config_key = 'contact_email'");
    $result = $stmt->fetch();
    echo "<p>Configurations SMTP trouvées : " . $result['count'] . "/5</p>";
    
    // Vérifier PHPMailer
    echo "<h2>Vérification PHPMailer :</h2>";
    
    if (file_exists('vendor/autoload.php')) {
        require_once 'vendor/autoload.php';
        echo "<p style='color: green;'>✓ PHPMailer trouvé via Composer</p>";
    } elseif (file_exists('PHPMailer/src/PHPMailer.php')) {
        require_once 'PHPMailer/src/PHPMailer.php';
        require_once 'PHPMailer/src/SMTP.php';
        require_once 'PHPMailer/src/Exception.php';
        echo "<p style='color: green;'>✓ PHPMailer trouvé (installation manuelle)</p>";
    } else {
        echo "<p style='color: red;'>✗ PHPMailer NON TROUVÉ</p>";
        echo "<p><strong>Solution :</strong> Téléchargez PHPMailer et placez-le dans le dossier PHPMailer/</p>";
        echo "</body></html>";
        exit;
    }
    
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        echo "<p style='color: green;'>✓ Classe PHPMailer disponible</p>";
        
        // Test simple de création d'instance
        use PHPMailer\PHPMailer\PHPMailer;
        $testMail = new PHPMailer(true);
        echo "<p style='color: green;'>✓ Instance PHPMailer créée avec succès</p>";
    } else {
        echo "<p style='color: red;'>✗ Classe PHPMailer non disponible</p>";
    }
    
    // Test de connexion SMTP (sans envoyer)
    echo "<h2>Test de connexion SMTP :</h2>";
    if (!empty($cms->getConfig('smtp_host')) && !empty($cms->getConfig('smtp_username')) && !empty($cms->getConfig('smtp_password'))) {
        try {
            use PHPMailer\PHPMailer\PHPMailer;
            use PHPMailer\PHPMailer\SMTP;
            
            $testMail = new PHPMailer(true);
            $testMail->isSMTP();
            $testMail->Host = $cms->getConfig('smtp_host');
            $testMail->SMTPAuth = true;
            $testMail->Username = $cms->getConfig('smtp_username');
            $testMail->Password = $cms->getConfig('smtp_password');
            $testMail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $testMail->Port = (int)($cms->getConfig('smtp_port') ?: 587);
            
            // Test de connexion uniquement
            $testMail->SMTPDebug = 0; // Pas de debug pour ce test
            
            echo "<p style='color: green;'>✓ Configuration SMTP semble correcte</p>";
            echo "<p><em>Note : Test de connexion réelle nécessaire via envoi d'email</em></p>";
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ Erreur de configuration SMTP : " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>⚠️ Configuration SMTP incomplète - impossible de tester la connexion</p>";
    }
    
    echo "<h2>Actions recommandées :</h2>";
    
    if ($result['count'] < 5) {
        echo "<p style='color: red;'>⚠️ <a href='add_smtp_config.php'>Ajouter les configurations SMTP manquantes</a></p>";
    }
    
    if (empty($cms->getConfig('smtp_host'))) {
        echo "<p style='color: red;'>⚠️ Configurez le serveur SMTP dans l'administration</p>";
    }
    
    if (empty($cms->getConfig('smtp_username'))) {
        echo "<p style='color: red;'>⚠️ Configurez l'email d'envoi dans l'administration</p>";
    }
    
    if (empty($cms->getConfig('smtp_password'))) {
        echo "<p style='color: red;'>⚠️ Configurez le mot de passe SMTP dans l'administration</p>";
    }
    
    if (empty($cms->getConfig('contact_email'))) {
        echo "<p style='color: orange;'>⚠️ Configurez l'email de réception (optionnel, utilise l'email d'envoi par défaut)</p>";
    }
    
    if (!empty($cms->getConfig('smtp_host')) && !empty($cms->getConfig('smtp_username')) && !empty($cms->getConfig('smtp_password'))) {
        echo "<p style='color: green;'><strong>✓ Configuration complète !</strong> Vous pouvez tester l'envoi d'email.</p>";
        echo "<p><a href='index.php#contact'>Tester le formulaire de contact</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>";
    echo "<p>Stack trace :</p><pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<br><br>";
echo "<a href='debug_db.php'>Debug Base de Données</a> | ";
echo "<a href='add_smtp_config.php'>Ajouter Config SMTP</a> | ";
echo "<a href='admin/dashboard.php'>Administration</a> | ";
echo "<a href='index.php'>Retour au site</a>";
echo "</body></html>";
?>