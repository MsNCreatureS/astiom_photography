<?php
/**
 * Script d'installation pour le CMS Astiom Photography
 * Ce script crée automatiquement la base de données et insère les données par défaut
 */

require_once 'config.php';

$installed = false;
$error = '';
$success = '';

// Vérifier si la base de données existe déjà
try {
    $cms = new CMS();
    $testConfig = $cms->getConfig('site_title');
    if ($testConfig) {
        $installed = true;
    }
} catch (Exception $e) {
    // Base de données pas encore créée
}

if ($_POST && isset($_POST['install']) && !$installed) {
    try {
        // Lire le fichier SQL
        $sqlContent = file_get_contents('database.sql');
        
        if ($sqlContent === false) {
            throw new Exception('Impossible de lire le fichier database.sql');
        }
        
        // Connexion à MySQL sans spécifier de base de données
        $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        
        // Diviser les requêtes SQL
        $queries = explode(';', $sqlContent);
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                $pdo->exec($query);
            }
        }
        
        $success = 'Installation terminée avec succès ! Vous pouvez maintenant utiliser votre CMS.';
        $installed = true;
        
    } catch (Exception $e) {
        $error = 'Erreur lors de l\'installation : ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - CMS Astiom Photography</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2c3e50, #3498db);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .install-container {
            background: white;
            max-width: 600px;
            width: 100%;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
        }
        
        .install-container h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 2.5em;
        }
        
        .install-container p {
            color: #7f8c8d;
            margin-bottom: 30px;
            line-height: 1.6;
            font-size: 16px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .install-info {
            background-color: #e3f2fd;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
            text-align: left;
        }
        
        .install-info h3 {
            color: #1976d2;
            margin-bottom: 15px;
        }
        
        .install-info ul {
            margin-left: 20px;
            line-height: 1.8;
        }
        
        .install-info li {
            margin-bottom: 8px;
        }
        
        .btn {
            display: inline-block;
            padding: 15px 30px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 10px;
        }
        
        .btn:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background-color: #27ae60;
        }
        
        .btn-success:hover {
            background-color: #229954;
        }
        
        .config-info {
            background-color: #fff3cd;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: left;
        }
        
        .config-info h4 {
            color: #856404;
            margin-bottom: 10px;
        }
        
        .config-info code {
            background-color: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <h1>🎨 CMS Installation</h1>
        <p><strong>Astiom Photography</strong><br>Système de gestion de contenu</p>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($installed): ?>
            <div class="alert alert-success">
                <strong>✅ Installation terminée !</strong><br>
                Votre CMS est maintenant prêt à être utilisé.
            </div>
            
            <div class="install-info">
                <h3>🔑 Informations de connexion</h3>
                <ul>
                    <li><strong>Nom d'utilisateur :</strong> admin</li>
                    <li><strong>Mot de passe :</strong> admin123</li>
                    <li><strong>URL d'administration :</strong> <a href="admin/" target="_blank">admin/</a></li>
                </ul>
                <p style="margin-top: 15px; color: #e74c3c; font-weight: 500;">
                    ⚠️ N'oubliez pas de changer le mot de passe par défaut !
                </p>
            </div>
            
            <a href="index.php" class="btn btn-success">Voir le site</a>
            <a href="admin/" class="btn">Administration</a>
            
        <?php else: ?>
            <div class="config-info">
                <h4>⚙️ Configuration requise</h4>
                <p>Avant l'installation, assurez-vous que :</p>
                <ul>
                    <li>WAMP/XAMPP est démarré</li>
                    <li>MySQL est accessible</li>
                    <li>Les paramètres dans <code>config.php</code> sont corrects</li>
                </ul>
            </div>
            
            <div class="install-info">
                <h3>📋 Ce que va faire l'installation</h3>
                <ul>
                    <li>Créer la base de données <code>astiom_photography_cms</code></li>
                    <li>Créer toutes les tables nécessaires</li>
                    <li>Insérer les données par défaut</li>
                    <li>Créer un compte administrateur</li>
                </ul>
            </div>
            
            <form method="POST">
                <button type="submit" name="install" class="btn">🚀 Installer le CMS</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>