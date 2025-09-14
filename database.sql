-- Base de données pour le CMS Astiom Photography
-- Créer la base de données
CREATE DATABASE IF NOT EXISTS astiom_photography_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE astiom_photography_cms;

-- Table pour les paramètres généraux du site
CREATE TABLE site_config (
    id INT PRIMARY KEY AUTO_INCREMENT,
    config_key VARCHAR(50) UNIQUE NOT NULL,
    config_value TEXT NOT NULL,
    description VARCHAR(191),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table pour les contenus des sections
CREATE TABLE content_sections (
    id INT PRIMARY KEY AUTO_INCREMENT,
    section_name VARCHAR(30) UNIQUE NOT NULL,
    title VARCHAR(191) NOT NULL,
    subtitle TEXT,
    content TEXT,
    image_path VARCHAR(191),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table pour les services
CREATE TABLE services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    icon VARCHAR(50) NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    order_position INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table pour le portfolio
CREATE TABLE portfolio_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    subtitle VARCHAR(100),
    image_path VARCHAR(191),
    category VARCHAR(30),
    order_position INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table pour les statistiques "À propos"
CREATE TABLE about_stats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    stat_number VARCHAR(20) NOT NULL,
    stat_label VARCHAR(100) NOT NULL,
    order_position INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table pour les informations de contact
CREATE TABLE contact_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    info_type VARCHAR(30) NOT NULL, -- email, phone, address
    icon VARCHAR(50) NOT NULL,
    value VARCHAR(191) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table pour les liens sociaux
CREATE TABLE social_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    platform VARCHAR(30) NOT NULL,
    icon VARCHAR(50) NOT NULL,
    url VARCHAR(191) NOT NULL,
    order_position INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table pour les messages de contact
CREATE TABLE contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(191) NOT NULL,
    subject VARCHAR(191) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table pour l'administration
CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(30) UNIQUE NOT NULL,
    email VARCHAR(191) UNIQUE NOT NULL,
    password_hash VARCHAR(191) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Insertion des données par défaut

-- Configuration générale du site
INSERT INTO site_config (config_key, config_value, description) VALUES
('site_title', 'Astiom Photography - Photographe Professionnel', 'Titre principal du site'),
('site_description', 'Astiom Photography - Photographe professionnel spécialisé dans les portraits, événements et paysages', 'Description meta du site'),
('site_keywords', 'photographie, photographe, portraits, événements, mariage, paysages', 'Mots-clés meta du site'),
('logo_light', 'logo/astiom_logoV2w.png', 'Logo pour fond sombre'),
('logo_dark', 'logo/astiom_logoV2.png', 'Logo pour fond clair'),
('favicon_light', 'logo/astiom_icon_dark.png', 'Favicon pour mode clair'),
('favicon_dark', 'logo/astiom_icon.png', 'Favicon pour mode sombre'),
('copyright_text', '© 2025 Astiom Photography. Tous droits réservés.', 'Texte de copyright');

-- Sections de contenu
INSERT INTO content_sections (section_name, title, subtitle, content, image_path) VALUES
('hero', 'Astiom Photography', 'Capturer l\'émotion, révéler la beauté', 'Photographe professionnel passionné par l\'art de saisir les moments uniques et de transformer vos souvenirs en œuvres d\'art intemporelles.', NULL),
('services', 'Mes Services', 'Une expertise complète pour tous vos besoins photographiques', NULL, NULL),
('portfolio', 'Portfolio', 'Découvrez une sélection de mes meilleures réalisations', NULL, NULL),
('about', 'À propos', NULL, 'Passionné de photographie depuis plus de 10 ans, je me spécialise dans la capture d\'émotions authentiques et la création d\'images qui racontent une histoire. Mon approche allie technique maîtrisée et sensibilité artistique pour offrir des résultats uniques et mémorables.', NULL),
('contact', 'Contact', 'Discutons de votre projet photographique', 'N\'hésitez pas à me contacter pour discuter de votre projet. Je serais ravi de vous accompagner dans la réalisation de vos idées.', NULL);

-- Services
INSERT INTO services (icon, title, description, order_position) VALUES
('fas fa-user', 'Portraits', 'Portraits professionnels, artistiques et lifestyle qui révèlent votre personnalité unique.', 1),
('fas fa-heart', 'Mariages', 'Immortalisez le plus beau jour de votre vie avec des images pleines d\'émotion et d\'authenticité.', 2),
('fas fa-building', 'Événements', 'Couverture photo complète pour vos événements d\'entreprise, célébrations et occasions spéciales.', 3),
('fas fa-mountain', 'Paysages', 'Photographie de paysages et d\'architecture pour sublimer la beauté de notre environnement.', 4);

-- Portfolio
INSERT INTO portfolio_items (title, subtitle, image_path, category, order_position) VALUES
('Portrait Artistique', 'Séance studio', 'placeholder', 'portrait', 1),
('Mariage Romantique', 'Château de Versailles', 'placeholder', 'mariage', 2),
('Événement Corporate', 'Conférence Tech', 'placeholder', 'evenement', 3),
('Lever de Soleil', 'Alpes françaises', 'placeholder', 'paysage', 4);

-- Statistiques À propos
INSERT INTO about_stats (stat_number, stat_label, order_position) VALUES
('500+', 'Projets réalisés', 1),
('10+', 'Années d\'expérience', 2),
('100%', 'Clients satisfaits', 3);

-- Informations de contact
INSERT INTO contact_info (info_type, icon, value) VALUES
('email', 'fas fa-envelope', 'contact@astiom-photography.fr'),
('phone', 'fas fa-phone', '+33 6 12 34 56 78'),
('address', 'fas fa-map-marker-alt', 'Paris, France');

-- Liens sociaux
INSERT INTO social_links (platform, icon, url, order_position) VALUES
('Instagram', 'fab fa-instagram', '#', 1),
('Facebook', 'fab fa-facebook', '#', 2),
('LinkedIn', 'fab fa-linkedin', '#', 3);

-- Utilisateur admin par défaut (mot de passe: admin123)
INSERT INTO admin_users (username, email, password_hash) VALUES
('admin', 'admin@astiom-photography.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');