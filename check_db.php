<?php
require_once 'config.php';

$db = Database::getInstance();

echo "=== VÉRIFICATION DE LA TABLE ABOUT_STATS ===\n";
try {
    $stmt = $db->query("DESCRIBE about_stats");
    echo "Structure de la table about_stats:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
    echo "\n=== DONNÉES EXISTANTES ===\n";
    $stmt = $db->query("SELECT * FROM about_stats ORDER BY sort_order");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['id'] . " | Numéro: " . $row['stat_number'] . " | Label: " . $row['stat_label'] . "\n";
    }
    
} catch(Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}

echo "\n=== VÉRIFICATION DE LA TABLE SITE_CONFIG ===\n";
try {
    $stmt = $db->query("SELECT config_key, config_value FROM site_config WHERE config_key LIKE '%image%' OR config_key LIKE '%hero%' OR config_key LIKE '%about%'");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['config_key'] . " = " . $row['config_value'] . "\n";
    }
} catch(Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
?>