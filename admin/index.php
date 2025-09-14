<?php
session_start();
require_once '../config.php';

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION[ADMIN_SESSION_NAME])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_POST && isset($_POST['username'], $_POST['password'])) {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    
    if (!empty($username) && !empty($password)) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, username, password_hash FROM admin_users WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION[ADMIN_SESSION_NAME] = [
                'id' => $user['id'],
                'username' => $user['username']
            ];
            
            // Mettre à jour la dernière connexion
            $stmt = $db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);
            
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Nom d\'utilisateur ou mot de passe incorrect.';
        }
    } else {
        $error = 'Veuillez remplir tous les champs.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Astiom Photography</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="icon" href="../logo/astiom_icon_dark.png" media="(prefers-color-scheme: light)">
    <link rel="icon" href="../logo/astiom_icon.png" media="(prefers-color-scheme: dark)">
    <link rel="icon" href="../logo/astiom_icon.png"> <!-- Fallback par défaut -->
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <h1>Administration</h1>
            <p>Astiom Photography CMS</p>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Se connecter</button>
            </form>
        </div>
    </div>
</body>
</html>