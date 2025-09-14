<?php
session_start();
require_once '../config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION[ADMIN_SESSION_NAME])) {
    header('Location: index.php');
    exit();
}

$cms = new CMS();
$message = '';

// Traitement des mises à jour
if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'update_config':
            foreach ($_POST['config'] as $key => $value) {
                $cms->updateConfig($key, sanitizeInput($value));
            }
            $message = 'Configuration mise à jour avec succès.';
            break;
            
        case 'update_section':
            $section = sanitizeInput($_POST['section']);
            $title = sanitizeInput($_POST['title']);
            $subtitle = sanitizeInput($_POST['subtitle']);
            $content = sanitizeInput($_POST['content']);
            
            if ($cms->updateSection($section, $title, $subtitle, $content)) {
                $message = 'Section mise à jour avec succès.';
            }
            break;
    }
}

// Récupérer les données actuelles
$siteConfig = [
    'site_title' => $cms->getConfig('site_title'),
    'site_description' => $cms->getConfig('site_description'),
    'site_keywords' => $cms->getConfig('site_keywords'),
    'copyright_text' => $cms->getConfig('copyright_text')
];

$sections = [
    'hero' => $cms->getSection('hero'),
    'services' => $cms->getSection('services'),
    'portfolio' => $cms->getSection('portfolio'),
    'about' => $cms->getSection('about'),
    'contact' => $cms->getSection('contact')
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Administration</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>CMS Admin</h2>
                <p>Bienvenue, <?php echo htmlspecialchars($_SESSION[ADMIN_SESSION_NAME]['username']); ?></p>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="#config" class="nav-link active" data-tab="config"><i class="fas fa-cog"></i> Configuration</a></li>
                    <li><a href="#sections" class="nav-link" data-tab="sections"><i class="fas fa-edit"></i> Contenu</a></li>
                    <li><a href="#services" class="nav-link" data-tab="services"><i class="fas fa-briefcase"></i> Services</a></li>
                    <li><a href="#portfolio" class="nav-link" data-tab="portfolio"><i class="fas fa-images"></i> Portfolio</a></li>
                    <li><a href="#messages" class="nav-link" data-tab="messages"><i class="fas fa-envelope"></i> Messages</a></li>
                    <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Voir le site</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                </ul>
            </nav>
        </aside>
        
        <main class="main-content">
            <header class="content-header">
                <h1>Tableau de bord</h1>
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
            </header>
            
            <!-- Configuration générale -->
            <div id="config" class="tab-content active">
                <h2>Configuration générale</h2>
                
                <form method="POST" class="admin-form">
                    <input type="hidden" name="action" value="update_config">
                    
                    <div class="form-group">
                        <label for="site_title">Titre du site</label>
                        <input type="text" id="site_title" name="config[site_title]" value="<?php echo htmlspecialchars($siteConfig['site_title']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="site_description">Description du site</label>
                        <textarea id="site_description" name="config[site_description]" rows="3" required><?php echo htmlspecialchars($siteConfig['site_description']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="site_keywords">Mots-clés (SEO)</label>
                        <input type="text" id="site_keywords" name="config[site_keywords]" value="<?php echo htmlspecialchars($siteConfig['site_keywords']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="copyright_text">Texte de copyright</label>
                        <input type="text" id="copyright_text" name="config[copyright_text]" value="<?php echo htmlspecialchars($siteConfig['copyright_text']); ?>" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </form>
            </div>
            
            <!-- Gestion du contenu -->
            <div id="sections" class="tab-content">
                <h2>Gestion du contenu</h2>
                
                <div class="sections-grid">
                    <?php foreach ($sections as $key => $section): ?>
                        <div class="section-card">
                            <h3><?php echo ucfirst($key); ?></h3>
                            
                            <form method="POST" class="section-form">
                                <input type="hidden" name="action" value="update_section">
                                <input type="hidden" name="section" value="<?php echo $key; ?>">
                                
                                <div class="form-group">
                                    <label>Titre</label>
                                    <input type="text" name="title" value="<?php echo htmlspecialchars($section['title'] ?? ''); ?>" required>
                                </div>
                                
                                <?php if ($key !== 'about'): ?>
                                <div class="form-group">
                                    <label>Sous-titre</label>
                                    <input type="text" name="subtitle" value="<?php echo htmlspecialchars($section['subtitle'] ?? ''); ?>">
                                </div>
                                <?php endif; ?>
                                
                                <?php if (in_array($key, ['hero', 'about', 'contact'])): ?>
                                <div class="form-group">
                                    <label>Contenu</label>
                                    <textarea name="content" rows="4"><?php echo htmlspecialchars($section['content'] ?? ''); ?></textarea>
                                </div>
                                <?php endif; ?>
                                
                                <button type="submit" class="btn btn-secondary">Mettre à jour</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Services (placeholder pour l'instant) -->
            <div id="services" class="tab-content">
                <h2>Gestion des services</h2>
                <p>Interface de gestion des services en cours de développement...</p>
            </div>
            
            <!-- Portfolio (placeholder pour l'instant) -->
            <div id="portfolio" class="tab-content">
                <h2>Gestion du portfolio</h2>
                <p>Interface de gestion du portfolio en cours de développement...</p>
            </div>
            
            <!-- Messages -->
            <div id="messages" class="tab-content">
                <h2>Messages de contact</h2>
                <?php
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 20");
                $stmt->execute();
                $messages = $stmt->fetchAll();
                ?>
                
                <?php if ($messages): ?>
                    <div class="messages-list">
                        <?php foreach ($messages as $msg): ?>
                            <div class="message-item <?php echo $msg['is_read'] ? '' : 'unread'; ?>">
                                <div class="message-header">
                                    <strong><?php echo htmlspecialchars($msg['name']); ?></strong>
                                    <span class="message-email"><?php echo htmlspecialchars($msg['email']); ?></span>
                                    <span class="message-date"><?php echo date('d/m/Y H:i', strtotime($msg['created_at'])); ?></span>
                                </div>
                                <div class="message-subject">
                                    <strong><?php echo htmlspecialchars($msg['subject']); ?></strong>
                                </div>
                                <div class="message-content">
                                    <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Aucun message reçu.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <script>
        // Gestion des onglets
        document.querySelectorAll('.nav-link[data-tab]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Retirer la classe active de tous les liens et contenus
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
                
                // Ajouter la classe active au lien cliqué
                this.classList.add('active');
                
                // Afficher le contenu correspondant
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });
    </script>
</body>
</html>