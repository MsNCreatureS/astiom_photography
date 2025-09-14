<?php
require_once 'config.php';

// Initialiser le CMS
$cms = new CMS();

// Gestion des messages de retour du formulaire de contact
$successMessage = '';
$errorMessage = '';

if (isset($_GET['success'])) {
    $successMessage = htmlspecialchars(urldecode($_GET['success']));
}

if (isset($_GET['error'])) {
    $errorMessage = htmlspecialchars(urldecode($_GET['error']));
}

// Récupérer toutes les données
$siteTitle = $cms->getConfig('site_title');
$siteDescription = $cms->getConfig('site_description');
$siteKeywords = $cms->getConfig('site_keywords');
$logoLight = $cms->getConfig('logo_light');
$logoDark = $cms->getConfig('logo_dark');
$faviconLight = $cms->getConfig('favicon_light');
$faviconDark = $cms->getConfig('favicon_dark');
$copyrightText = $cms->getConfig('copyright_text');

$heroSection = $cms->getSection('hero');
$servicesSection = $cms->getSection('services');
$portfolioSection = $cms->getSection('portfolio');
$aboutSection = $cms->getSection('about');
$contactSection = $cms->getSection('contact');

$services = $cms->getServices();
$portfolioItems = $cms->getPortfolioItems();
$aboutStats = $cms->getAboutStats();
$contactInfo = $cms->getContactInfo();
$socialLinks = $cms->getSocialLinks();

// Récupérer l'email SMTP pour le contact
$contactEmail = $cms->getConfig('smtp_username') ?: 'contact@astiomphotography.com';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($siteDescription); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($siteKeywords); ?>">
    <title><?php echo htmlspecialchars($siteTitle); ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&family=Exo+2:wght@300;400;500;600;700&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="style.css">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicons adaptatives selon le thème -->
    <link rel="icon" href="<?php echo htmlspecialchars($faviconLight); ?>" media="(prefers-color-scheme: light)">
    <link rel="icon" href="<?php echo htmlspecialchars($faviconDark); ?>" media="(prefers-color-scheme: dark)">
    <link rel="icon" href="<?php echo htmlspecialchars($faviconDark); ?>"> <!-- Fallback par défaut -->
</head>
<body>
    <!-- Messages de notification -->
    <?php if ($successMessage): ?>
    <div class="notification notification-success" id="notification">
        <div class="container">
            <i class="fas fa-check-circle"></i>
            <span><?php echo $successMessage; ?></span>
            <button class="notification-close" onclick="closeNotification()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
    <div class="notification notification-error" id="notification">
        <div class="container">
            <i class="fas fa-exclamation-triangle"></i>
            <span><?php echo $errorMessage; ?></span>
            <button class="notification-close" onclick="closeNotification()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Header / Navigation -->
    <header class="header">
        <nav class="navbar">
            <div class="nav-container">
                <div class="nav-logo">
                    <img src="<?php echo htmlspecialchars($logoLight); ?>" alt="<?php echo htmlspecialchars($heroSection['title'] ?? 'Astiom Photography'); ?>" class="logo">
                </div>
                
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="#accueil" class="nav-link">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a href="#services" class="nav-link">Services</a>
                    </li>
                    <li class="nav-item">
                        <a href="#portfolio" class="nav-link">Portfolio</a>
                    </li>
                    <li class="nav-item">
                        <a href="#about" class="nav-link">À propos</a>
                    </li>
                    <li class="nav-item">
                        <a href="#contact" class="nav-link">Contact</a>
                    </li>
                </ul>
                
                <div class="hamburger">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section id="accueil" class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">
                    <span class="title-main"><?php echo htmlspecialchars($heroSection['title'] ?? 'Astiom Photography'); ?></span>
                    <span class="title-sub"><?php echo htmlspecialchars($heroSection['subtitle'] ?? 'Capturer l\'émotion, révéler la beauté'); ?></span>
                </h1>
                <p class="hero-description">
                    <?php echo htmlspecialchars($heroSection['content'] ?? 'Photographe professionnel passionné par l\'art de saisir les moments uniques et de transformer vos souvenirs en œuvres d\'art intemporelles.'); ?>
                </p>
                <div class="hero-buttons">
                    <a href="#portfolio" class="btn btn-primary">Découvrir mon travail</a>
                    <a href="#contact" class="btn btn-secondary">Me contacter</a>
                </div>
            </div>
            <div class="hero-image">
                <?php 
                $heroImage = $cms->getConfig('hero_image');
                if ($heroImage && !empty($heroImage)): ?>
                    <img src="<?= htmlspecialchars($heroImage) ?>" alt="<?= htmlspecialchars($heroSection['title'] ?? 'Astiom Photography') ?>" class="hero-main-image">
                <?php else: ?>
                    <div class="image-placeholder">
                        <i class="fas fa-camera"></i>
                        <p>Photo portrait en vedette</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Scroll indicator -->
        <div class="scroll-indicator">
            <div class="scroll-line"></div>
            <span class="scroll-text">Scroll</span>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php echo htmlspecialchars($servicesSection['title'] ?? 'Mes Services'); ?></h2>
                <p class="section-subtitle"><?php echo htmlspecialchars($servicesSection['subtitle'] ?? 'Une expertise complète pour tous vos besoins photographiques'); ?></p>
            </div>
            
            <div class="services-grid">
                <?php foreach ($services as $service): ?>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="<?php echo htmlspecialchars($service['icon']); ?>"></i>
                    </div>
                    <h3 class="service-title"><?php echo htmlspecialchars($service['title']); ?></h3>
                    <p class="service-description">
                        <?php echo htmlspecialchars($service['description']); ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Portfolio Preview -->
    <section id="portfolio" class="portfolio-preview">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php echo htmlspecialchars($portfolioSection['title'] ?? 'Portfolio'); ?></h2>
                <p class="section-subtitle"><?php echo htmlspecialchars($portfolioSection['subtitle'] ?? 'Découvrez une sélection de mes meilleures réalisations'); ?></p>
            </div>
            
            <div class="portfolio-grid">
                <?php foreach ($portfolioItems as $item): ?>
                <div class="portfolio-item">
                    <div class="portfolio-image" <?php if ($item['image_path'] && $item['image_path'] !== 'placeholder'): ?>data-image="<?php echo htmlspecialchars($item['image_path']); ?>" data-title="<?php echo htmlspecialchars($item['title']); ?>" data-subtitle="<?php echo htmlspecialchars($item['subtitle']); ?>" style="cursor: pointer;"<?php endif; ?>>
                        <?php if ($item['image_path'] && $item['image_path'] !== 'placeholder'): ?>
                            <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <?php else: ?>
                            <div class="image-placeholder">
                                <i class="fas fa-image"></i>
                                <p><?php echo htmlspecialchars($item['category']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="portfolio-overlay">
                        <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                        <p><?php echo htmlspecialchars($item['subtitle']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2 class="section-title"><?php echo htmlspecialchars($aboutSection['title'] ?? 'À propos'); ?></h2>
                    <p class="about-description">
                        <?php echo htmlspecialchars($aboutSection['content'] ?? 'Passionné de photographie depuis plus de 10 ans...'); ?>
                    </p>
                    <div class="about-stats">
                        <?php foreach ($aboutStats as $stat): ?>
                        <div class="stat">
                            <span class="stat-number"><?php echo htmlspecialchars($stat['stat_number']); ?></span>
                            <span class="stat-label"><?php echo htmlspecialchars($stat['stat_label']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="about-image">
                    <?php 
                    $aboutImage = $cms->getConfig('about_image');
                    if ($aboutImage && !empty($aboutImage)): ?>
                        <img src="<?= htmlspecialchars($aboutImage) ?>" alt="<?= htmlspecialchars($aboutSection['title'] ?? 'À propos') ?>" class="about-main-image">
                    <?php else: ?>
                        <div class="image-placeholder">
                            <i class="fas fa-user-circle"></i>
                            <p>Photo du photographe</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php echo htmlspecialchars($contactSection['title'] ?? 'Contact'); ?></h2>
                <p class="section-subtitle"><?php echo htmlspecialchars($contactSection['subtitle'] ?? 'Discutons de votre projet photographique'); ?></p>
            </div>
            
            <div class="contact-content">
                <div class="contact-info">
                    <h3>Restons en contact</h3>
                    <p><?php echo htmlspecialchars($contactSection['content'] ?? 'N\'hésitez pas à me contacter pour discuter de votre projet...'); ?></p>
                    
                    <div class="contact-details">
                        <?php foreach ($contactInfo as $info): ?>
                        <div class="contact-item">
                            <i class="<?php echo htmlspecialchars($info['icon']); ?>"></i>
                            <span><?php echo htmlspecialchars($info['value']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="social-links">
                        <?php foreach ($socialLinks as $social): ?>
                        <a href="<?php echo htmlspecialchars($social['url']); ?>" class="social-link">
                            <i class="<?php echo htmlspecialchars($social['icon']); ?>"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="contact-simple">
                    <div class="contact-email">
                        <h3>Contactez-moi directement</h3>
                        <p>Pour toute demande de devis ou information, n'hésitez pas à m'écrire :</p>
                        <a href="mailto:<?php echo htmlspecialchars($contactEmail); ?>?subject=Demande de contact - Astiom Photography" class="email-link">
                            <i class="fas fa-envelope"></i>
                            <?php echo htmlspecialchars($contactEmail); ?>
                        </a>
                        <p class="email-note">Votre client email s'ouvrira automatiquement avec un sujet pré-rempli.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="<?php echo htmlspecialchars($logoDark); ?>" alt="<?php echo htmlspecialchars($heroSection['title'] ?? 'Astiom Photography'); ?>" class="logo">
                </div>
                <p class="footer-text">
                    <?php echo htmlspecialchars($copyrightText); ?>
                </p>
                <div class="footer-admin">
                    <a href="admin/" class="admin-link">Administration</a>
                </div>
            </div>
        </div>
    </footer>

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
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Favicon adaptative selon le thème du navigateur
        function updateFavicon() {
            const favicon = document.querySelector('link[rel="icon"]:not([media])');
            const isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            if (isDarkMode) {
                favicon.href = '<?php echo htmlspecialchars($logoLight); ?>'; // Logo blanc pour mode sombre
            } else {
                favicon.href = '<?php echo htmlspecialchars($logoDark); ?>';  // Logo noir pour mode clair
            }
        }
        
        // Mettre à jour au chargement
        updateFavicon();
        
        // Écouter les changements de thème
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', updateFavicon);
        
        // Modal pour l'aperçu d'image du portfolio
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
            document.body.style.overflow = 'hidden';
        }
        
        // Function pour fermer le modal
        function closeImageModal() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        // Ajouter les événements de clic sur les images du portfolio
        document.querySelectorAll('.portfolio-image[data-image]').forEach(portfolioImage => {
            portfolioImage.addEventListener('click', function() {
                const imgSrc = this.getAttribute('data-image');
                const title = this.getAttribute('data-title');
                const subtitle = this.getAttribute('data-subtitle');
                
                openImageModal(imgSrc, title, subtitle);
            });
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

        // Gestion des notifications
        function closeNotification() {
            const notification = document.getElementById('notification');
            if (notification) {
                notification.style.opacity = '0';
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 300);
            }
        }

        // Auto-fermeture des notifications après 5 secondes
        const notification = document.getElementById('notification');
        if (notification) {
            setTimeout(() => {
                closeNotification();
            }, 5000);
        }
    </script>
    <script src="script.js"></script>
</body>
</html>