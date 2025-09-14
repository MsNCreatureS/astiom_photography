<?php
require_once 'config.php';

echo "<!DOCTYPE html><html><head><title>Debug Base de Données</title></head><body>";
echo "<h1>Debug - Vérification Base de Données</h1>";

try {
    $cms = new CMS();
    $db = Database::getInstance()->getConnection();
    
    echo "<h2>1. Structure de la table site_config :</h2>";
    $stmt = $db->query("DESCRIBE site_config");
    $columns = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>2. Toutes les configurations existantes :</h2>";
    $stmt = $db->query("SELECT * FROM site_config ORDER BY config_key");
    $configs = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Clé</th><th>Valeur</th><th>Description</th><th>Créé le</th><th>Mis à jour le</th></tr>";
    foreach ($configs as $config) {
        echo "<tr>";
        echo "<td style='font-weight: bold;'>" . htmlspecialchars($config['config_key']) . "</td>";
        echo "<td>" . htmlspecialchars($config['config_value']) . "</td>";
        echo "<td style='font-size: 12px; color: #666;'>" . htmlspecialchars($config['description']) . "</td>";
        echo "<td style='font-size: 12px;'>" . $config['created_at'] . "</td>";
        echo "<td style='font-size: 12px;'>" . $config['updated_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>3. Test des méthodes CMS pour SMTP :</h2>";
    $smtpConfigs = [
        'smtp_host' => $cms->getConfig('smtp_host'),
        'smtp_port' => $cms->getConfig('smtp_port'),
        'smtp_username' => $cms->getConfig('smtp_username'),
        'smtp_password' => $cms->getConfig('smtp_password'),
        'contact_email' => $cms->getConfig('contact_email')
    ];
    
    echo "<ul>";
    foreach ($smtpConfigs as $key => $value) {
        $status = empty($value) ? "<span style='color: red;'>VIDE</span>" : "<span style='color: green;'>CONFIGURÉ</span>";
        $displayValue = ($key === 'smtp_password' && !empty($value)) ? "****** (caché)" : htmlspecialchars($value);
        echo "<li><strong>$key:</strong> $displayValue - $status</li>";
    }
    echo "</ul>";
    
    echo "<h2>4. Test de requête SQL directe :</h2>";
    echo "<h3>Recherche des configurations SMTP :</h3>";
    $stmt = $db->query("SELECT * FROM site_config WHERE config_key LIKE 'smtp_%' OR config_key = 'contact_email'");
    $smtpRows = $stmt->fetchAll();
    
    if (empty($smtpRows)) {
        echo "<p style='color: red;'><strong>PROBLÈME:</strong> Aucune configuration SMTP trouvée dans la base de données !</p>";
        echo "<p>Les configurations SMTP n'ont pas été ajoutées à la base de données.</p>";
        
        echo "<h3>Solution - Exécuter ces requêtes SQL :</h3>";
        echo "<pre style='background: #f0f0f0; padding: 10px; margin: 10px 0;'>";
        echo "INSERT INTO site_config (config_key, config_value, description) VALUES\n";
        echo "('smtp_host', 'smtp.gmail.com', 'Serveur SMTP (ex: smtp.gmail.com, smtp.outlook.com)'),\n";
        echo "('smtp_port', '587', 'Port SMTP (587 pour TLS, 465 pour SSL)'),\n";
        echo "('smtp_username', 'helofreewan@gmail.com', 'Nom d\\'utilisateur SMTP (votre email)'),\n";
        echo "('smtp_password', 'xffi zrhi fsdp ksle', 'Mot de passe SMTP ou mot de passe d\\'application'),\n";
        echo "('contact_email', 'helofreewan@gmail.com', 'Email de réception des messages de contact')\n";
        echo "ON DUPLICATE KEY UPDATE \n";
        echo "config_value = VALUES(config_value),\n";
        echo "description = VALUES(description),\n";
        echo "updated_at = NOW();";
        echo "</pre>";
    } else {
        echo "<p style='color: green;'>✓ Configurations SMTP trouvées :</p>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Clé</th><th>Valeur</th></tr>";
        foreach ($smtpRows as $row) {
            $displayValue = ($row['config_key'] === 'smtp_password') ? "****** (caché)" : htmlspecialchars($row['config_value']);
            echo "<tr><td>" . $row['config_key'] . "</td><td>" . $displayValue . "</td></tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>5. Test de connexion à la base de données :</h2>";
    echo "<p style='color: green;'>✓ Connexion à la base de données réussie</p>";
    echo "<p>Base de données : " . DB_NAME . "</p>";
    echo "<p>Host : " . DB_HOST . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>";
    echo "<p>Trace : <pre>" . $e->getTraceAsString() . "</pre></p>";
}

echo "<br><br>";
echo "<a href='admin/dashboard.php'>Administration</a> | ";
echo "<a href='test_smtp.php'>Test SMTP</a> | ";
echo "<a href='index.php'>Retour au site</a>";
echo "</body></html>";
?>