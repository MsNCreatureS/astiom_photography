-- Script de mise à jour pour ajouter les nouvelles configurations

-- Ajouter les configurations pour les images
INSERT IGNORE INTO site_config (config_key, config_value, description) VALUES
('hero_image', '', 'Image principale de la section hero'),
('about_image', '', 'Image de la section à propos');

-- Vérifier que les statistiques about existent
SELECT COUNT(*) as count_stats FROM about_stats;

-- Si pas de statistiques, les ajouter
INSERT IGNORE INTO about_stats (stat_number, stat_label, order_position) VALUES
('500+', 'Projets réalisés', 1),
('10+', 'Années d\'expérience', 2),
('100%', 'Clients satisfaits', 3);