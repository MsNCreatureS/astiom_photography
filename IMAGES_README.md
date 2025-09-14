# Gestion des Images - Portfolio

## 📸 **Fonctionnalités ajoutées :**

### **Upload d'images :**
- ✅ **Formats supportés :** JPG, JPEG, PNG, GIF, WEBP
- ✅ **Taille maximum :** 5MB par image
- ✅ **Noms uniques :** Génération automatique avec timestamp
- ✅ **Stockage sécurisé :** Dossier `uploads/portfolio/`

### **Gestion des images :**
- 🖼️ **Aperçu dans l'admin :** Vignettes cliquables
- 🔍 **Modal d'agrandissement :** Clic sur image pour voir en grand
- 🗑️ **Suppression automatique :** L'image est supprimée du serveur quand l'élément est supprimé
- 🔄 **Remplacement :** Upload d'une nouvelle image remplace l'ancienne

### **Interface améliorée :**
- 📤 **Drag & drop** pour l'upload (interface file moderne)
- 👁️ **Prévisualisation** des fichiers sélectionnés
- ⚡ **Aperçu temps réel** avec taille du fichier
- 🎨 **Modal responsive** pour l'affichage des images

## 🔒 **Sécurité :**
- Vérification des types MIME
- Limitation de taille de fichier
- Noms de fichiers sécurisés
- Dossier protégé par .htaccess

## 📁 **Structure des fichiers :**
```
uploads/
├── .htaccess              # Sécurité du dossier
└── portfolio/             # Images du portfolio
    ├── 60f7b2c3_1640995200.jpg
    ├── 60f7b2c4_1640995201.png
    └── ...
```

## 🚀 **Comment utiliser :**

1. **Ajouter un élément avec image :**
   - Remplir le formulaire
   - Sélectionner une image
   - Cliquer "Ajouter l'élément"

2. **Modifier une image :**
   - Dans la section "Éléments du portfolio"
   - Sélectionner une nouvelle image (optionnel)
   - Cliquer "Mettre à jour"

3. **Voir une image en grand :**
   - Cliquer sur la vignette dans l'admin
   - La modal s'ouvre avec l'image agrandie

## 📋 **À faire ensuite :**
- [ ] Redimensionnement automatique des images
- [ ] Galerie d'images pour le portfolio
- [ ] Filtres par catégorie sur le site
- [ ] Compression automatique des images