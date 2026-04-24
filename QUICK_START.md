# 🚀 Guide de Démarrage Rapide

## Intégration Frontend-Backend Complète ✅

Toutes les interfaces frontend ont été adaptées pour être compatibles avec le backend Laravel.

## 📦 Installation et Démarrage

### 1️⃣ Backend (Laravel)

```bash
# Aller dans le dossier backend
cd backend

# Installer les dépendances (si pas déjà fait)
composer install

# Vérifier que la base de données existe
# Le fichier database/database.sqlite doit exister

# Exécuter les migrations et seeders (si pas déjà fait)
php artisan migrate:fresh --seed

# Démarrer le serveur Laravel
php artisan serve
```

✅ Le backend sera accessible sur **http://localhost:8000**

### 2️⃣ Frontend (React + Vite)

```bash
# Ouvrir un nouveau terminal
# Aller dans le dossier frontend
cd frontend

# Installer les dépendances
npm install

# Créer le fichier .env (optionnel, le proxy Vite fonctionne par défaut)
# echo "VITE_API_URL=http://localhost:8000" > .env

# Démarrer le serveur de développement
npm run dev
```

✅ Le frontend sera accessible sur **http://localhost:5173**

## 🎯 Tester l'Application

1. Ouvrez votre navigateur sur **http://localhost:5173**
2. Vous devriez voir le Dashboard avec les statistiques
3. Naviguez vers les différentes sections :
   - 📊 **Dashboard** - Vue d'ensemble et statistiques
   - 📦 **Commandes** - Liste des commandes avec pagination
   - 🛍️ **Produits** - Liste des produits
   - 👥 **Clients** - Liste des clients
   - 📈 **Statistiques** - Graphiques détaillés

## ✨ Fonctionnalités Disponibles

### ✅ Fonctionnalités Opérationnelles
- Affichage du dashboard avec statistiques en temps réel
- Liste des commandes avec pagination (20 par page)
- Détails d'une commande (cliquer sur l'icône œil)
- Mise à jour du statut d'une commande (dropdown dans la liste)
- Suppression d'une commande (icône poubelle)
- Liste des produits avec statistiques de stock
- Liste des clients avec statistiques (commandes, dépenses)
- Recherche locale dans les listes
- Graphiques de statistiques

### ⏳ À Implémenter
- Formulaires de création/édition
- Authentification utilisateur
- Gestion des catégories
- Upload d'images

## 🔧 Configuration

### Variables d'Environnement Frontend (Optionnel)
Le proxy Vite est configuré par défaut. Si vous voulez personnaliser l'URL de l'API :

```bash
# frontend/.env
VITE_API_URL=http://localhost:8000
```

### CORS Backend
Le CORS est déjà configuré pour accepter les requêtes depuis :
- http://localhost:5173 (Vite)
- http://localhost:3000 (alternative)

## 📁 Fichiers Importants

### Frontend
- `frontend/src/config/api.js` - Configuration API centralisée
- `frontend/vite.config.js` - Configuration Vite avec proxy
- `frontend/INTEGRATION.md` - Documentation complète frontend

### Backend
- `backend/config/cors.php` - Configuration CORS
- `backend/routes/api.php` - Routes API
- `backend/FRONTEND_INTEGRATION.md` - Documentation complète backend

## 🐛 Problèmes Courants

### Le backend ne démarre pas
```bash
# Vérifier que le port 8000 n'est pas déjà utilisé
# Ou démarrer sur un autre port
php artisan serve --port=8001
```

### Le frontend ne se connecte pas au backend
1. Vérifiez que le backend est bien démarré
2. Ouvrez la console du navigateur (F12) pour voir les erreurs
3. Vérifiez que l'URL dans la console correspond à `http://localhost:8000/api/...`

### Pas de données affichées
```bash
# Exécuter les seeders pour avoir des données de test
cd backend
php artisan db:seed
```

### Erreur CORS
```bash
# Vider le cache Laravel
cd backend
php artisan config:clear
php artisan cache:clear

# Redémarrer le serveur
php artisan serve
```

## 📚 Documentation Complète

Pour plus de détails, consultez :
- 📄 **FRONTEND_BACKEND_INTEGRATION.md** - Résumé complet de l'intégration
- 📄 **frontend/INTEGRATION.md** - Guide d'intégration frontend
- 📄 **backend/FRONTEND_INTEGRATION.md** - Guide d'intégration backend
- 📄 **frontend/CHANGELOG.md** - Changelog détaillé

## 🎉 C'est Prêt !

Votre application e-commerce est maintenant opérationnelle avec :
- ✅ Backend Laravel fonctionnel
- ✅ Frontend React moderne
- ✅ Communication API complète
- ✅ CORS configuré
- ✅ Toutes les interfaces adaptées

Bon développement ! 🚀
