<?php
echo "<!DOCTYPE html><html><head><title>Vérification Logs</title></head><body>";
echo "<h1>Vérification des Logs d'Email</h1>";

// Fonction pour lire les dernières lignes d'un fichier
function tail($file, $lines = 50) {
    if (!file_exists($file)) {
        return ["Fichier non trouvé : $file"];
    }
    
    $handle = fopen($file, "r");
    if (!$handle) {
        return ["Impossible d'ouvrir le fichier : $file"];
    }
    
    $linecounter = 0;
    $pos = -2;
    $beginning = false;
    $text = array();
    
    while ($linecounter < $lines) {
        $t = " ";
        while ($t != "\n") {
            if (fseek($handle, $pos, SEEK_END) == -1) {
                $beginning = true;
                break;
            }
            $t = fgetc($handle);
            $pos--;
        }
        $linecounter++;
        if ($beginning) {
            rewind($handle);
        }
        $text[$lines - $linecounter] = fgets($handle);
        if ($beginning) break;
    }
    fclose($handle);
    return array_reverse($text);
}

echo "<h2>Logs PHP récents (recherche d'erreurs email) :</h2>";

// Chemins possibles des logs
$logPaths = [
    'C:\wamp64\logs\php_error.log',
    'C:\wamp64\logs\apache_error.log',
    'C:\wamp64\www\error.log',
    'C:\Windows\temp\php_errors.log',
    ini_get('error_log'),
    ini_get('log_errors_max_len')
];

$foundLogs = false;

foreach ($logPaths as $logPath) {
    if (!empty($logPath) && file_exists($logPath)) {
        $foundLogs = true;
        echo "<h3>Log : $logPath</h3>";
        
        $lines = tail($logPath, 30);
        $emailLines = [];
        
        foreach ($lines as $line) {
            if (stripos($line, 'smtp') !== false || 
                stripos($line, 'email') !== false || 
                stripos($line, 'phpmailer') !== false ||
                stripos($line, 'mail') !== false ||
                stripos($line, 'astiom') !== false) {
                $emailLines[] = $line;
            }
        }
        
        if (!empty($emailLines)) {
            echo "<div style='background: #ffe6e6; border: 1px solid #ff9999; padding: 10px; margin: 10px 0;'>";
            echo "<strong>Lignes liées aux emails :</strong><br>";
            foreach ($emailLines as $line) {
                echo "<code style='display: block; margin: 2px 0;'>" . htmlspecialchars($line) . "</code>";
            }
            echo "</div>";
        } else {
            echo "<p style='color: green;'>Aucune erreur email trouvée dans ce log.</p>";
        }
        
        // Afficher les 10 dernières lignes générales
        echo "<details><summary>Voir les 10 dernières lignes générales</summary>";
        echo "<pre style='background: #f0f0f0; padding: 10px; max-height: 200px; overflow-y: scroll;'>";
        foreach (array_slice($lines, -10) as $line) {
            echo htmlspecialchars($line);
        }
        echo "</pre></details>";
    }
}

if (!$foundLogs) {
    echo "<p style='color: orange;'>Aucun fichier de log trouvé dans les emplacements standards.</p>";
    echo "<p>Vérifiez dans le panneau WAMP ou créez un log personnalisé.</p>";
}

echo "<h2>Configuration PHP actuelle :</h2>";
echo "<ul>";
echo "<li><strong>error_log :</strong> " . (ini_get('error_log') ?: 'Non configuré') . "</li>";
echo "<li><strong>log_errors :</strong> " . (ini_get('log_errors') ? 'Activé' : 'Désactivé') . "</li>";
echo "<li><strong>display_errors :</strong> " . (ini_get('display_errors') ? 'Activé' : 'Désactivé') . "</li>";
echo "<li><strong>mail.log :</strong> " . (ini_get('mail.log') ?: 'Non configuré') . "</li>";
echo "</ul>";

echo "<h2>Test de création de log personnalisé :</h2>";
$customLog = 'email_debug.log';
$testMessage = date('Y-m-d H:i:s') . " - Test de log email créé\n";

if (file_put_contents($customLog, $testMessage, FILE_APPEND | LOCK_EX)) {
    echo "<p style='color: green;'>✅ Log personnalisé créé : $customLog</p>";
    
    // Modifier temporairement send_email.php pour logger dans ce fichier
    echo "<p><strong>Conseil :</strong> Ajoutez cette ligne au début de send_email.php pour un log dédié :</p>";
    echo "<code style='background: #f0f0f0; padding: 10px; display: block;'>";
    echo "ini_set('error_log', __DIR__ . '/email_debug.log');";
    echo "</code>";
} else {
    echo "<p style='color: red;'>❌ Impossible de créer le log personnalisé</p>";
}

echo "<h2>Actions recommandées :</h2>";
echo "<ol>";
echo "<li><strong>Testez l'envoi direct :</strong> <a href='test_email_direct.php'>Test Email Direct</a></li>";
echo "<li><strong>Vérifiez Gmail :</strong>";
echo "<ul>";
echo "<li>Boîte de réception</li>";
echo "<li>Spam/Courrier indésirable</li>";
echo "<li>Promotions (onglet Gmail)</li>";
echo "<li>Tous les messages</li>";
echo "</ul></li>";
echo "<li><strong>Attendez 1-2 minutes</strong> (délai de livraison possible)</li>";
echo "<li><strong>Vérifiez les paramètres Gmail :</strong>";
echo "<ul>";
echo "<li>Authentification à 2 facteurs activée</li>";
echo "<li>Mot de passe d'application correct</li>";
echo "<li>Filtres anti-spam</li>";
echo "</ul></li>";
echo "</ol>";

echo "<br><br>";
echo "<a href='test_email_direct.php'>Test Email Direct</a> | ";
echo "<a href='debug_db.php'>Debug BDD</a> | ";
echo "<a href='index.php'>Retour</a>";
echo "</body></html>";
?>