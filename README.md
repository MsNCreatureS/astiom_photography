# 🎨 CMS Astiom Photography

Un système de gestion de contenu (CMS) complet pour le site de photographie Astiom Photography.

## 📋 Fonctionnalités

### ✨ Front-end
- Site responsive et moderne
- Contenu 100% dynamique depuis la base de données
- Sections : Accueil, Services, Portfolio, À propos, Contact
- Formulaire de contact fonctionnel
- Design adaptatif (dark/light mode)

### 🔧 Back-end (Administration)
- Interface d'administration intuitive
- Gestion complète du contenu
- Système d'authentification sécurisé
- Gestion des messages de contact
- Configuration globale du site

## 🚀 Installation

### Prérequis
- WAMP/XAMPP avec PHP 7.4+ et MySQL
- Serveur web local en cours d'exécution

### Étapes d'installation

1. **Placer les fichiers**
   ```
   Copier tous les fichiers dans : c:\wamp64\www\astiom_photography\
   ```

2. **Configuration de la base de données**
   - Ouvrir `config.php`
   - Modifier les paramètres de connexion si nécessaire :
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'astiom_photography_cms');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

3. **Installation automatique**
   - Aller sur : `http://localhost/astiom_photography/install.php`
   - Cliquer sur "🚀 Installer le CMS"
   - Attendre la fin de l'installation

4. **Accès à l'administration**
   - URL : `http://localhost/astiom_photography/admin/`
   - **Nom d'utilisateur :** `admin`
   - **Mot de passe :** `admin123`
   
   ⚠️ **IMPORTANT :** Changez le mot de passe par défaut !

## 📁 Structure des fichiers

```
astiom_photography/
├── index.php              # Page principale (convertie depuis HTML)
├── config.php             # Configuration et classes PHP
├── database.sql           # Structure et données de la BDD
├── install.php            # Script d'installation automatique
├── style.css              # Styles CSS (inchangé)
├── script.js              # JavaScript (inchangé)
├── logo/                  # Dossier des logos
│   ├── astiom_icon.png
│   ├── astiom_icon_dark.png
│   ├── astiom_logoV2.png
│   └── astiom_logoV2w.png
└── admin/                 # Interface d'administration
    ├── index.php          # Page de connexion
    ├── dashboard.php      # Tableau de bord
    ├── logout.php         # Déconnexion
    └── admin.css          # Styles de l'admin
```

## 🗃️ Base de données

### Tables principales
- `site_config` - Configuration générale du site
- `content_sections` - Contenu des sections (Hero, Services, etc.)
- `services` - Liste des services proposés
- `portfolio_items` - Éléments du portfolio
- `about_stats` - Statistiques de la section "À propos"
- `contact_info` - Informations de contact
- `social_links` - Liens vers les réseaux sociaux
- `contact_messages` - Messages reçus via le formulaire
- `admin_users` - Comptes administrateurs

## 🎛️ Utilisation de l'administration

### Configuration générale
- Titre du site
- Description et mots-clés SEO
- Texte de copyright

### Gestion du contenu
- Modification des titres et sous-titres de chaque section
- Édition du contenu textuel
- Gestion des services et du portfolio (interface en développement)

### Messages de contact
- Consultation des messages reçus
- Marquage automatique des nouveaux messages

## 🔐 Sécurité

- Mots de passe hashés avec `password_hash()` PHP
- Protection contre les injections SQL avec PDO
- Sessions sécurisées pour l'administration
- Validation et échappement des données utilisateur

## 🛠️ Développement futur

### Fonctionnalités planifiées
- Upload et gestion d'images pour le portfolio
- Éditeur WYSIWYG pour le contenu
- Gestion avancée des services
- Système de sauvegarde automatique
- Interface responsive pour l'administration
- Gestion des utilisateurs multiples

## 📞 Support

Pour toute question ou problème :
1. Vérifiez que WAMP/XAMPP est bien démarré
2. Contrôlez les paramètres de connexion dans `config.php`
3. Consultez les logs d'erreur de votre serveur

## 📝 Notes techniques

- **Framework :** PHP natif avec POO
- **Base de données :** MySQL avec PDO
- **Frontend :** HTML5, CSS3, JavaScript
- **Compatibilité :** PHP 7.4+, MySQL 5.7+

---

*Créé pour Astiom Photography - Système CMS complet et évolutif*