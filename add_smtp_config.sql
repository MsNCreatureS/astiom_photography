-- Ajout des configurations SMTP pour PHPMailer
INSERT INTO site_config (config_key, config_value, description) VALUES
('smtp_host', 'smtp.gmail.com', 'Serveur SMTP (ex: smtp.gmail.com, smtp.outlook.com)'),
('smtp_port', '587', 'Port SMTP (587 pour TLS, 465 pour SSL)'),
('smtp_username', '', 'Nom d\'utilisateur SMTP (votre email)'),
('smtp_password', '', 'Mot de passe SMTP ou mot de passe d\'application'),
('contact_email', '', 'Email de réception des messages de contact')
ON DUPLICATE KEY UPDATE 
config_value = VALUES(config_value),
description = VALUES(description),
updated_at = NOW();