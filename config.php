<?php
/**
 * Configuration de la base de données pour le CMS Astiom Photography
 */

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'astiom_photography_cms');
define('DB_USER', 'root'); // Changez selon votre configuration WAMP
define('DB_PASS', '');     // Changez selon votre configuration WAMP
define('DB_CHARSET', 'utf8mb4');

// Configuration de sécurité
define('ADMIN_SESSION_NAME', 'astiom_admin_session');
define('SITE_URL', 'http://localhost/astiom_photography/');

// Classe pour la connexion à la base de données
class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
}

// Classe pour gérer le contenu du CMS
class CMS {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Récupérer une configuration
    public function getConfig($key) {
        $stmt = $this->db->prepare("SELECT config_value FROM site_config WHERE config_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['config_value'] : '';
    }
    
    // Récupérer le contenu d'une section
    public function getSection($section_name) {
        $stmt = $this->db->prepare("SELECT * FROM content_sections WHERE section_name = ?");
        $stmt->execute([$section_name]);
        return $stmt->fetch();
    }
    
    // Récupérer tous les services
    public function getServices() {
        $stmt = $this->db->prepare("SELECT * FROM services WHERE is_active = 1 ORDER BY order_position");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Récupérer tous les éléments du portfolio
    public function getPortfolioItems() {
        $stmt = $this->db->prepare("SELECT * FROM portfolio_items WHERE is_active = 1 ORDER BY order_position");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Récupérer les statistiques "À propos"
    public function getAboutStats() {
        $stmt = $this->db->prepare("SELECT * FROM about_stats WHERE is_active = 1 ORDER BY order_position");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Récupérer les informations de contact
    public function getContactInfo() {
        $stmt = $this->db->prepare("SELECT * FROM contact_info WHERE is_active = 1");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Récupérer les liens sociaux
    public function getSocialLinks() {
        $stmt = $this->db->prepare("SELECT * FROM social_links WHERE is_active = 1 ORDER BY order_position");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Enregistrer un message de contact
    public function saveContactMessage($name, $email, $subject, $message) {
        $stmt = $this->db->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $email, $subject, $message]);
    }
    
    // Mettre à jour une configuration
    public function updateConfig($key, $value) {
        $stmt = $this->db->prepare("UPDATE site_config SET config_value = ?, updated_at = NOW() WHERE config_key = ?");
        return $stmt->execute([$value, $key]);
    }
    
    // Mettre à jour le contenu d'une section
    public function updateSection($section_name, $title, $subtitle = null, $content = null) {
        $stmt = $this->db->prepare("UPDATE content_sections SET title = ?, subtitle = ?, content = ?, updated_at = NOW() WHERE section_name = ?");
        return $stmt->execute([$title, $subtitle, $content, $section_name]);
    }
}

// Fonctions utilitaires
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function redirectTo($url) {
    header("Location: " . $url);
    exit();
}
?>