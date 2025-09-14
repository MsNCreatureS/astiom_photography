<?php
require_once 'config.php';

echo "<!DOCTYPE html><html><head><title>Ajouter Config SMTP</title></head><body>";
echo "<h1>Ajout des configurations SMTP</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Préparer les configurations SMTP avec vos vraies données
    $smtpConfigs = [
        ['smtp_host', 'smtp.gmail.com', 'Serveur SMTP (ex: smtp.gmail.com, smtp.outlook.com)'],
        ['smtp_port', '587', 'Port SMTP (587 pour TLS, 465 pour SSL)'],
        ['smtp_username', 'helofreewan@gmail.com', 'Nom d\'utilisateur SMTP (votre email)'],
        ['smtp_password', 'xffi zrhi fsdp ksle', 'Mot de passe SMTP ou mot de passe d\'application'],
        ['contact_email', 'helofreewan@gmail.com', 'Email de réception des messages de contact']
    ];
    
    echo "<h2>Ajout des configurations :</h2>";
    
    $stmt = $db->prepare("
        INSERT INTO site_config (config_key, config_value, description) 
        VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE 
        config_value = VALUES(config_value),
        description = VALUES(description),
        updated_at = NOW()
    ");
    
    foreach ($smtpConfigs as $config) {
        $result = $stmt->execute($config);
        if ($result) {
            echo "<p style='color: green;'>✓ " . $config[0] . " : " . ($config[0] === 'smtp_password' ? '****** (caché)' : $config[1]) . "</p>";
        } else {
            echo "<p style='color: red;'>✗ Erreur pour " . $config[0] . "</p>";
        }
    }
    
    echo "<h2>Vérification :</h2>";
    $cms = new CMS();
    
    $testConfigs = [
        'smtp_host' => $cms->getConfig('smtp_host'),
        'smtp_port' => $cms->getConfig('smtp_port'),
        'smtp_username' => $cms->getConfig('smtp_username'),
        'smtp_password' => $cms->getConfig('smtp_password'),
        'contact_email' => $cms->getConfig('contact_email')
    ];
    
    echo "<ul>";
    foreach ($testConfigs as $key => $value) {
        $status = empty($value) ? "<span style='color: red;'>VIDE</span>" : "<span style='color: green;'>OK</span>";
        echo "<li><strong>$key:</strong> $status</li>";
    }
    echo "</ul>";
    
    if (empty($testConfigs['smtp_host']) || empty($testConfigs['smtp_username']) || empty($testConfigs['smtp_password'])) {
        echo "<p style='color: red;'><strong>ATTENTION:</strong> Certaines configurations sont encore vides !</p>";
    } else {
        echo "<p style='color: green;'><strong>SUCCESS:</strong> Toutes les configurations SMTP sont en place !</p>";
        echo "<p>Vous pouvez maintenant tester l'envoi d'email.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>";
}

echo "<br><br>";
echo "<a href='debug_db.php'>Debug Base de Données</a> | ";
echo "<a href='test_smtp.php'>Test SMTP</a> | ";
echo "<a href='admin/dashboard.php'>Administration</a> | ";
echo "<a href='index.php'>Retour au site</a>";
echo "</body></html>";
?>