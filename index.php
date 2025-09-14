<?php
require_once 'config.php';

// Initialiser le CMS
$cms = new CMS();

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

// Traitement du formulaire de contact
if ($_POST && isset($_POST['name'], $_POST['email'], $_POST['subject'], $_POST['message'])) {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $subject = sanitizeInput($_POST['subject']);
    $message = sanitizeInput($_POST['message']);
    
    if (!empty($name) && !empty($email) && !empty($subject) && !empty($message)) {
        if ($cms->saveContactMessage($name, $email, $subject, $message)) {
            $contactSuccess = true;
        } else {
            $contactError = true;
        }
    } else {
        $contactError = true;
    }
}
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
                        <a href="#portfolio" class="nav-link">Portfolio</a>
                    </li>
                    <li class="nav-item">
                        <a href="#services" class="nav-link">Services</a>
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
                <div class="image-placeholder">
                    <i class="fas fa-camera"></i>
                    <p>Photo portrait en vedette</p>
                </div>
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
                    <div class="portfolio-image">
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
            
            <div class="portfolio-cta">
                <a href="#" class="btn btn-primary">Voir tout le portfolio</a>
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
                    <div class="image-placeholder">
                        <i class="fas fa-user-circle"></i>
                        <p>Photo du photographe</p>
                    </div>
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
                
                <form class="contact-form" method="POST" action="#contact">
                    <?php if (isset($contactSuccess)): ?>
                        <div class="alert alert-success">Votre message a été envoyé avec succès !</div>
                    <?php endif; ?>
                    
                    <?php if (isset($contactError)): ?>
                        <div class="alert alert-error">Erreur lors de l'envoi du message. Veuillez réessayer.</div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <input type="text" id="name" name="name" placeholder="Votre nom" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <input type="email" id="email" name="email" placeholder="Votre email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" id="subject" name="subject" placeholder="Sujet" required value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <textarea id="message" name="message" placeholder="Votre message" rows="5" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Envoyer le message</button>
                </form>
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
            </div>
        </div>
    </footer>

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
    </script>
    <script src="script.js"></script>
</body>
</html>