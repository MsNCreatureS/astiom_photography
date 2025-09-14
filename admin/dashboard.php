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
            
        case 'add_service':
            $icon = sanitizeInput($_POST['icon']);
            $title = sanitizeInput($_POST['title']);
            $description = sanitizeInput($_POST['description']);
            $order = (int)$_POST['order_position'];
            
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("INSERT INTO services (icon, title, description, order_position) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$icon, $title, $description, $order])) {
                $message = 'Service ajouté avec succès.';
            }
            break;
            
        case 'update_service':
            $id = (int)$_POST['service_id'];
            $icon = sanitizeInput($_POST['icon']);
            $title = sanitizeInput($_POST['title']);
            $description = sanitizeInput($_POST['description']);
            $order = (int)$_POST['order_position'];
            $active = isset($_POST['is_active']) ? 1 : 0;
            
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE services SET icon = ?, title = ?, description = ?, order_position = ?, is_active = ? WHERE id = ?");
            if ($stmt->execute([$icon, $title, $description, $order, $active, $id])) {
                $message = 'Service mis à jour avec succès.';
            }
            break;
            
        case 'delete_service':
            $id = (int)$_POST['service_id'];
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM services WHERE id = ?");
            if ($stmt->execute([$id])) {
                $message = 'Service supprimé avec succès.';
            }
            break;
            
        case 'add_portfolio':
            $title = sanitizeInput($_POST['title']);
            $subtitle = sanitizeInput($_POST['subtitle']);
            $category = sanitizeInput($_POST['category']);
            $order = (int)$_POST['order_position'];
            $imagePath = 'placeholder';
            
            // Traitement de l'upload d'image
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../uploads/portfolio/';
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                $maxFileSize = 5 * 1024 * 1024; // 5MB
                
                $fileType = $_FILES['image']['type'];
                $fileSize = $_FILES['image']['size'];
                
                if (in_array($fileType, $allowedTypes) && $fileSize <= $maxFileSize) {
                    $fileName = uniqid() . '_' . time() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $uploadPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                        $imagePath = 'uploads/portfolio/' . $fileName;
                    }
                }
            }
            
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("INSERT INTO portfolio_items (title, subtitle, category, order_position, image_path) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$title, $subtitle, $category, $order, $imagePath])) {
                $message = 'Élément de portfolio ajouté avec succès.';
            }
            break;
            
        case 'update_portfolio':
            $id = (int)$_POST['portfolio_id'];
            $title = sanitizeInput($_POST['title']);
            $subtitle = sanitizeInput($_POST['subtitle']);
            $category = sanitizeInput($_POST['category']);
            $order = (int)$_POST['order_position'];
            $active = isset($_POST['is_active']) ? 1 : 0;
            
            // Récupérer l'image actuelle
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT image_path FROM portfolio_items WHERE id = ?");
            $stmt->execute([$id]);
            $currentItem = $stmt->fetch();
            $imagePath = $currentItem['image_path'];
            
            // Traitement de l'upload d'image si une nouvelle image est uploadée
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../uploads/portfolio/';
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                $maxFileSize = 5 * 1024 * 1024; // 5MB
                
                $fileType = $_FILES['image']['type'];
                $fileSize = $_FILES['image']['size'];
                
                if (in_array($fileType, $allowedTypes) && $fileSize <= $maxFileSize) {
                    $fileName = uniqid() . '_' . time() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $uploadPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                        // Supprimer l'ancienne image si elle existe et n'est pas un placeholder
                        if ($imagePath && $imagePath !== 'placeholder' && file_exists('../' . $imagePath)) {
                            unlink('../' . $imagePath);
                        }
                        $imagePath = 'uploads/portfolio/' . $fileName;
                    }
                }
            }
            
            $stmt = $db->prepare("UPDATE portfolio_items SET title = ?, subtitle = ?, category = ?, order_position = ?, is_active = ?, image_path = ? WHERE id = ?");
            if ($stmt->execute([$title, $subtitle, $category, $order, $active, $imagePath, $id])) {
                $message = 'Élément de portfolio mis à jour avec succès.';
            }
            break;
            
        case 'delete_portfolio':
            $id = (int)$_POST['portfolio_id'];
            
            // Récupérer l'image avant suppression pour la supprimer du serveur
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT image_path FROM portfolio_items WHERE id = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch();
            
            if ($item && $item['image_path'] && $item['image_path'] !== 'placeholder') {
                $imagePath = '../' . $item['image_path'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $stmt = $db->prepare("DELETE FROM portfolio_items WHERE id = ?");
            if ($stmt->execute([$id])) {
                $message = 'Élément de portfolio supprimé avec succès.';
            }
            break;
            
        case 'update_about_stat':
            $id = (int)$_POST['stat_id'];
            $stat_number = sanitizeInput($_POST['stat_number']);
            $stat_label = sanitizeInput($_POST['stat_label']);
            $order = (int)$_POST['order_position'];
            
            if ($cms->updateAboutStat($id, $stat_number, $stat_label, $order)) {
                $message = 'Statistique mise à jour avec succès.';
            }
            break;
            
        case 'upload_hero_image':
            if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../uploads/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                $maxFileSize = 5 * 1024 * 1024; // 5MB
                
                $fileType = $_FILES['hero_image']['type'];
                $fileSize = $_FILES['hero_image']['size'];
                
                if (in_array($fileType, $allowedTypes) && $fileSize <= $maxFileSize) {
                    $fileName = 'hero_' . uniqid() . '_' . time() . '.' . pathinfo($_FILES['hero_image']['name'], PATHINFO_EXTENSION);
                    $uploadPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $uploadPath)) {
                        $imagePath = 'uploads/' . $fileName;
                        
                        // Supprimer l'ancienne image si elle existe
                        $oldImage = $cms->getConfig('hero_image');
                        if ($oldImage && file_exists('../' . $oldImage)) {
                            unlink('../' . $oldImage);
                        }
                        
                        if ($cms->updateConfig('hero_image', $imagePath)) {
                            $message = 'Image hero mise à jour avec succès.';
                        }
                    } else {
                        $error = 'Erreur lors de l\'upload de l\'image hero.';
                    }
                } else {
                    $error = 'Type de fichier non autorisé ou fichier trop volumineux.';
                }
            }
            break;
            
        case 'upload_about_image':
            if (isset($_FILES['about_image']) && $_FILES['about_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../uploads/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                $maxFileSize = 5 * 1024 * 1024; // 5MB
                
                $fileType = $_FILES['about_image']['type'];
                $fileSize = $_FILES['about_image']['size'];
                
                if (in_array($fileType, $allowedTypes) && $fileSize <= $maxFileSize) {
                    $fileName = 'about_' . uniqid() . '_' . time() . '.' . pathinfo($_FILES['about_image']['name'], PATHINFO_EXTENSION);
                    $uploadPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['about_image']['tmp_name'], $uploadPath)) {
                        $imagePath = 'uploads/' . $fileName;
                        
                        // Supprimer l'ancienne image si elle existe
                        $oldImage = $cms->getConfig('about_image');
                        if ($oldImage && file_exists('../' . $oldImage)) {
                            unlink('../' . $oldImage);
                        }
                        
                        if ($cms->updateConfig('about_image', $imagePath)) {
                            $message = 'Image à propos mise à jour avec succès.';
                        }
                    } else {
                        $error = 'Erreur lors de l\'upload de l\'image à propos.';
                    }
                } else {
                    $error = 'Type de fichier non autorisé ou fichier trop volumineux.';
                }
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

// Récupérer les configurations SMTP
$smtpConfig = [
    'smtp_host' => $cms->getConfig('smtp_host'),
    'smtp_port' => $cms->getConfig('smtp_port'),
    'smtp_username' => $cms->getConfig('smtp_username'),
    'smtp_password' => $cms->getConfig('smtp_password'),
    'contact_email' => $cms->getConfig('contact_email')
];

$sections = [
    'hero' => $cms->getSection('hero'),
    'services' => $cms->getSection('services'),
    'portfolio' => $cms->getSection('portfolio'),
    'about' => $cms->getSection('about'),
    'contact' => $cms->getSection('contact')
];

// Récupérer les services et portfolio
$db = Database::getInstance()->getConnection();
$servicesStmt = $db->prepare("SELECT * FROM services ORDER BY order_position, id");
$servicesStmt->execute();
$allServices = $servicesStmt->fetchAll();

$portfolioStmt = $db->prepare("SELECT * FROM portfolio_items ORDER BY order_position, id");
$portfolioStmt->execute();
$allPortfolioItems = $portfolioStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Administration</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="icon" href="../logo/astiom_icon_dark.png" media="(prefers-color-scheme: light)">
    <link rel="icon" href="../logo/astiom_icon.png" media="(prefers-color-scheme: dark)">
    <link rel="icon" href="../logo/astiom_icon.png"> <!-- Fallback par défaut -->
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
                    <li><a href="#statistics" class="nav-link" data-tab="statistics"><i class="fas fa-chart-bar"></i> Statistiques</a></li>
                    <li><a href="#media" class="nav-link" data-tab="media"><i class="fas fa-photo-video"></i> Médias</a></li>
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
                
                <!-- Configuration SMTP -->
                <div class="smtp-config-section">
                    <h3>Configuration SMTP (Email)</h3>
                    <p class="description">Mettre son mail</p>
                    
                    <form method="POST" class="admin-form">
                        <input type="hidden" name="action" value="update_config">
                        
                        
                        <div class="form-group">
                            <label for="smtp_username">Email d'envoi</label>
                            <input type="email" id="smtp_username" name="config[smtp_username]" value="<?php echo htmlspecialchars($smtpConfig['smtp_username']); ?>" placeholder="votre-email@gmail.com">
                            <small>L'adresse email utilisée pour l'envoi</small>
                        </div>
                        
                        
                        <button type="submit" class="btn btn-primary">Mettre à jour la configuration SMTP</button>
                    </form>
                </div>
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
            
            <!-- Services (interface complète) -->
            <div id="services" class="tab-content">
                <h2>Gestion des services</h2>
                
                <!-- Formulaire d'ajout -->
                <div class="add-item-form">
                    <h3>Ajouter un nouveau service</h3>
                    <form method="POST" class="admin-form">
                        <input type="hidden" name="action" value="add_service">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="service_icon">Icône FontAwesome</label>
                                <input type="text" id="service_icon" name="icon" placeholder="fas fa-user" required>
                                <small>Exemple: fas fa-user, fas fa-heart, fas fa-building</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="service_title">Titre</label>
                                <input type="text" id="service_title" name="title" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="service_description">Description</label>
                            <textarea id="service_description" name="description" rows="3" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="service_order">Position d'affichage</label>
                            <input type="number" id="service_order" name="order_position" value="<?php echo count($allServices) + 1; ?>" min="1">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Ajouter le service</button>
                    </form>
                </div>
                
                <!-- Liste des services existants -->
                <div class="items-list">
                    <h3>Services existants</h3>
                    <?php if ($allServices): ?>
                        <?php foreach ($allServices as $service): ?>
                            <div class="item-card">
                                <div class="item-header">
                                    <div class="item-icon">
                                        <i class="<?php echo htmlspecialchars($service['icon']); ?>"></i>
                                    </div>
                                    <div class="item-info">
                                        <h4><?php echo htmlspecialchars($service['title']); ?></h4>
                                        <p class="item-description"><?php echo htmlspecialchars($service['description']); ?></p>
                                        <span class="item-meta">Position: <?php echo $service['order_position']; ?> | 
                                        Statut: <?php echo $service['is_active'] ? 'Actif' : 'Inactif'; ?></span>
                                    </div>
                                </div>
                                
                                <form method="POST" class="item-form">
                                    <input type="hidden" name="action" value="update_service">
                                    <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>Icône</label>
                                            <input type="text" name="icon" value="<?php echo htmlspecialchars($service['icon']); ?>" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Titre</label>
                                            <input type="text" name="title" value="<?php echo htmlspecialchars($service['title']); ?>" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Position</label>
                                            <input type="number" name="order_position" value="<?php echo $service['order_position']; ?>" min="1">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea name="description" rows="2" required><?php echo htmlspecialchars($service['description']); ?></textarea>
                                    </div>
                                    
                                    <div class="form-actions">
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="is_active" <?php echo $service['is_active'] ? 'checked' : ''; ?>>
                                            Service actif
                                        </label>
                                        
                                        <div class="action-buttons">
                                            <button type="submit" class="btn btn-secondary">Mettre à jour</button>
                                            <button type="submit" name="action" value="delete_service" class="btn btn-danger" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce service ?')">Supprimer</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Aucun service défini. Ajoutez-en un avec le formulaire ci-dessus.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Portfolio (interface complète) -->
            <div id="portfolio" class="tab-content">
                <h2>Gestion du portfolio</h2>
                
                <!-- Formulaire d'ajout -->
                <div class="add-item-form">
                    <h3>Ajouter un nouvel élément</h3>
                    <form method="POST" enctype="multipart/form-data" class="admin-form">
                        <input type="hidden" name="action" value="add_portfolio">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="portfolio_title">Titre</label>
                                <input type="text" id="portfolio_title" name="title" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="portfolio_subtitle">Sous-titre/Lieu</label>
                                <input type="text" id="portfolio_subtitle" name="subtitle">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="portfolio_category">Catégorie</label>
                                <select id="portfolio_category" name="category" required>
                                    <option value="">Choisir une catégorie</option>
                                    <option value="portrait">Portrait</option>
                                    <option value="mariage">Mariage</option>
                                    <option value="evenement">Événement</option>
                                    <option value="paysage">Paysage</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="portfolio_order">Position d'affichage</label>
                                <input type="number" id="portfolio_order" name="order_position" value="<?php echo count($allPortfolioItems) + 1; ?>" min="1">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="portfolio_image">Image</label>
                            <input type="file" id="portfolio_image" name="image" accept="image/*">
                            <small>Formats acceptés : JPG, PNG, GIF, WEBP. Taille max : 5MB</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Ajouter l'élément</button>
                    </form>
                </div>
                
                <!-- Liste du portfolio existant -->
                <div class="items-list">
                    <h3>Éléments du portfolio</h3>
                    <?php if ($allPortfolioItems): ?>
                        <?php foreach ($allPortfolioItems as $item): ?>
                            <div class="item-card">
                                <div class="item-header">
                                    <div class="item-preview">
                                        <?php if ($item['image_path'] && $item['image_path'] !== 'placeholder'): ?>
                                            <img src="../<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                            <div class="image-overlay">
                                                <span>Cliquer pour agrandir</span>
                                            </div>
                                        <?php else: ?>
                                            <div class="placeholder-image">
                                                <i class="fas fa-image"></i>
                                                <br><small>Pas d'image</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="item-info">
                                        <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                                        <p class="item-subtitle"><?php echo htmlspecialchars($item['subtitle']); ?></p>
                                        <span class="item-meta">Catégorie: <?php echo ucfirst($item['category']); ?> | 
                                        Position: <?php echo $item['order_position']; ?> | 
                                        Statut: <?php echo $item['is_active'] ? 'Actif' : 'Inactif'; ?></span>
                                    </div>
                                </div>
                                
                                <form method="POST" enctype="multipart/form-data" class="item-form">
                                    <input type="hidden" name="action" value="update_portfolio">
                                    <input type="hidden" name="portfolio_id" value="<?php echo $item['id']; ?>">
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>Titre</label>
                                            <input type="text" name="title" value="<?php echo htmlspecialchars($item['title']); ?>" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Sous-titre</label>
                                            <input type="text" name="subtitle" value="<?php echo htmlspecialchars($item['subtitle']); ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>Catégorie</label>
                                            <select name="category" required>
                                                <option value="portrait" <?php echo $item['category'] === 'portrait' ? 'selected' : ''; ?>>Portrait</option>
                                                <option value="mariage" <?php echo $item['category'] === 'mariage' ? 'selected' : ''; ?>>Mariage</option>
                                                <option value="evenement" <?php echo $item['category'] === 'evenement' ? 'selected' : ''; ?>>Événement</option>
                                                <option value="paysage" <?php echo $item['category'] === 'paysage' ? 'selected' : ''; ?>>Paysage</option>
                                                <option value="autre" <?php echo $item['category'] === 'autre' ? 'selected' : ''; ?>>Autre</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Position</label>
                                            <input type="number" name="order_position" value="<?php echo $item['order_position']; ?>" min="1">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Nouvelle image (optionnel)</label>
                                        <input type="file" name="image" accept="image/*">
                                        <small>Laisser vide pour conserver l'image actuelle. Formats : JPG, PNG, GIF, WEBP. Max : 5MB</small>
                                    </div>
                                    
                                    <div class="form-actions">
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="is_active" <?php echo $item['is_active'] ? 'checked' : ''; ?>>
                                            Élément actif
                                        </label>
                                        
                                        <div class="action-buttons">
                                            <button type="submit" class="btn btn-secondary">Mettre à jour</button>
                                            <button type="submit" name="action" value="delete_portfolio" class="btn btn-danger" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet élément et son image ?')">Supprimer</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Aucun élément dans le portfolio. Ajoutez-en un avec le formulaire ci-dessus.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Gestion des Statistiques -->
            <div id="statistics" class="tab-content">
                <h2><i class="fas fa-chart-bar"></i> Gestion des Statistiques</h2>
                
                <div class="content-section">
                    <h3>Statistiques À Propos</h3>
                    
                    <?php 
                    $aboutStats = $cms->getAboutStats(); 
                    if ($aboutStats): ?>
                        <div class="items-list">
                            <?php foreach ($aboutStats as $stat): ?>
                                <div class="item-card">
                                    <form method="POST" class="item-form">
                                        <input type="hidden" name="action" value="update_about_stat">
                                        <input type="hidden" name="stat_id" value="<?php echo $stat['id']; ?>">
                                        
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label>Chiffre</label>
                                                <input type="text" name="stat_number" value="<?php echo htmlspecialchars($stat['stat_number']); ?>" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>Label</label>
                                                <input type="text" name="stat_label" value="<?php echo htmlspecialchars($stat['stat_label']); ?>" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>Position</label>
                                                <input type="number" name="order_position" value="<?php echo $stat['order_position']; ?>" min="1">
                                            </div>
                                        </div>
                                        
                                        <div class="form-actions">
                                            <button type="submit" class="btn btn-secondary">Mettre à jour</button>
                                        </div>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Gestion des Médias -->
            <div id="media" class="tab-content">
                <h2><i class="fas fa-photo-video"></i> Gestion des Médias</h2>
                
                <!-- Image Hero -->
                <div class="content-section">
                    <h3>Image de la Section Hero</h3>
                    <?php 
                    $heroImage = $cms->getConfig('hero_image');
                    if ($heroImage && !empty($heroImage)): ?>
                        <div class="current-image">
                            <img src="../<?php echo htmlspecialchars($heroImage); ?>" alt="Image Hero actuelle" style="max-width: 300px; height: auto; border-radius: 8px;">
                            <p><strong>Image actuelle :</strong> <?php echo htmlspecialchars($heroImage); ?></p>
                        </div>
                    <?php else: ?>
                        <p class="no-image">Aucune image configurée pour la section hero.</p>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data" class="upload-form">
                        <input type="hidden" name="action" value="upload_hero_image">
                        
                        <div class="form-group">
                            <label>Nouvelle image hero</label>
                            <input type="file" name="hero_image" accept="image/*" required>
                            <small>Formats acceptés : JPG, PNG, GIF, WEBP. Taille max : 5MB. Recommandé : 1200x800px</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Mettre à jour l'image hero
                        </button>
                    </form>
                </div>
                
                <!-- Image About -->
                <div class="content-section">
                    <h3>Image de la Section À Propos</h3>
                    <?php 
                    $aboutImage = $cms->getConfig('about_image');
                    if ($aboutImage && !empty($aboutImage)): ?>
                        <div class="current-image">
                            <img src="../<?php echo htmlspecialchars($aboutImage); ?>" alt="Image À propos actuelle" style="max-width: 300px; height: auto; border-radius: 8px;">
                            <p><strong>Image actuelle :</strong> <?php echo htmlspecialchars($aboutImage); ?></p>
                        </div>
                    <?php else: ?>
                        <p class="no-image">Aucune image configurée pour la section à propos.</p>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data" class="upload-form">
                        <input type="hidden" name="action" value="upload_about_image">
                        
                        <div class="form-group">
                            <label>Nouvelle image à propos</label>
                            <input type="file" name="about_image" accept="image/*" required>
                            <small>Formats acceptés : JPG, PNG, GIF, WEBP. Taille max : 5MB. Recommandé : 400x500px (portrait)</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Mettre à jour l'image à propos
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Modal pour l'aperçu d'image -->
    <div id="imageModal" class="image-modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <img id="modalImage" src="" alt="">
            <div class="modal-info">
                <h3 id="modalTitle"></h3>
                <p id="modalSubtitle"></p>
            </div>
            <div class="modal-instructions">
                <i class="fas fa-mouse-pointer"></i> Cliquer en dehors pour fermer
                <i class="fas fa-keyboard"></i> Echap pour fermer
                <i class="fas fa-expand-arrows-alt"></i> Faire défiler pour zoomer
            </div>
        </div>
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
        
        // Modal pour l'aperçu d'image
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImage');
        const modalTitle = document.getElementById('modalTitle');
        const modalSubtitle = document.getElementById('modalSubtitle');
        const closeModal = document.querySelector('.close-modal');
        
        // Function pour ouvrir le modal
        function openImageModal(imgSrc, title, subtitle) {
            modalImg.src = imgSrc;
            modalTitle.textContent = title || 'Image du portfolio';
            modalSubtitle.textContent = subtitle || '';
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Empêcher le scroll
        }
        
        // Function pour fermer le modal
        function closeImageModal() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto'; // Rétablir le scroll
        }
        
        // Ouvrir le modal quand on clique sur une image dans le portfolio
        function attachImageClickEvents() {
            document.querySelectorAll('.item-preview').forEach(preview => {
                const img = preview.querySelector('img');
                if (img) {
                    preview.style.cursor = 'pointer';
                    preview.addEventListener('click', function() {
                        const itemInfo = this.closest('.item-header').querySelector('.item-info');
                        const title = itemInfo.querySelector('h4').textContent;
                        const subtitle = itemInfo.querySelector('.item-subtitle')?.textContent || '';
                        
                        openImageModal(img.src, title, subtitle);
                    });
                }
            });
        }
        
        // Attacher les événements au chargement
        attachImageClickEvents();
        
        // Reattacher les événements après mise à jour du contenu
        const observer = new MutationObserver(function(mutations) {
            let shouldReattach = false;
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    shouldReattach = true;
                }
            });
            if (shouldReattach) {
                setTimeout(attachImageClickEvents, 100);
            }
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        // Fermer le modal
        closeModal.addEventListener('click', closeImageModal);
        
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeImageModal();
            }
        });
        
        // Fermer avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.style.display === 'block') {
                closeImageModal();
            }
        });
        
        // Prévisualisation des fichiers sélectionnés
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    // Vérifier la taille
                    if (file.size > 5 * 1024 * 1024) {
                        alert('Fichier trop volumineux ! Taille maximum : 5MB');
                        this.value = '';
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Créer un aperçu avec image
                        const preview = document.createElement('div');
                        preview.className = 'file-preview';
                        preview.innerHTML = `
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="${e.target.result}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                                <div>
                                    <div style="font-weight: 500; color: #2c3e50;">${file.name}</div>
                                    <small style="color: #3498db;">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                                </div>
                            </div>
                        `;
                        
                        // Supprimer l'ancien aperçu s'il existe
                        const existingPreview = input.parentNode.querySelector('.file-preview');
                        if (existingPreview) {
                            existingPreview.remove();
                        }
                        
                        input.parentNode.appendChild(preview);
                    };
                    reader.readAsDataURL(file);
                } else if (file) {
                    alert('Veuillez sélectionner un fichier image valide (JPG, PNG, GIF, WEBP)');
                    this.value = '';
                }
            });
        });
        
        // Améliorer l'affichage des images avec gestion d'erreur
        document.querySelectorAll('.item-preview img').forEach(img => {
            img.addEventListener('error', function() {
                this.style.display = 'none';
                const placeholder = document.createElement('div');
                placeholder.className = 'placeholder-image';
                placeholder.innerHTML = '<i class="fas fa-exclamation-triangle"></i><br><small>Erreur de chargement</small>';
                this.parentNode.appendChild(placeholder);
            });
            
            img.addEventListener('load', function() {
                this.style.opacity = '0';
                this.style.transition = 'opacity 0.3s ease';
                setTimeout(() => {
                    this.style.opacity = '1';
                }, 50);
            });
        });
    </script>
</body>
</html>