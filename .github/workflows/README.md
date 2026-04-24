# CI/CD Pipeline Documentation

## Overview

Ce projet utilise GitHub Actions pour l'intégration continue et le déploiement continu (CI/CD).

## Workflows

### 1. Frontend CI (`frontend-ci.yml`)
**Déclenchement:** Push ou PR sur `main`/`develop` avec modifications dans `frontend/`

**Jobs:**
- **test**: Installe les dépendances, exécute les tests et build l'application
- **code-quality**: Vérifie le linting et le formatage du code

### 2. Backend CI (`backend-ci.yml`)
**Déclenchement:** Push ou PR sur `main`/`develop` avec modifications dans `backend/`

**Jobs:**
- **test**: Configure MySQL, exécute les migrations et les tests Laravel
- **code-quality**: Exécute PHP CS Fixer et PHPStan

### 3. Security Checks (`security.yml`)
**Déclenchement:** 
- Push/PR sur `main`/`develop`
- Hebdomadaire (dimanche à minuit)

**Jobs:**
- **backend-security**: Audit de sécurité Composer
- **frontend-security**: Audit de sécurité npm
- **dependency-review**: Revue des dépendances sur les PRs

### 4. Docker Build & Push (`docker.yml`)
**Déclenchement:** Push sur `main`/`develop` ou tags `v*`

**Jobs:**
- **build-backend**: Build et push de l'image Docker backend
- **build-frontend**: Build et push de l'image Docker frontend

### 5. Deploy to Production (`deploy.yml`)
**Déclenchement:** Push sur `main` ou manuel

**Jobs:**
- **deploy-backend**: Déploie le backend Laravel
- **deploy-frontend**: Déploie le frontend React
- **notify**: Envoie une notification de déploiement

## Secrets Requis

Configurez ces secrets dans GitHub Settings > Secrets and variables > Actions:

### Déploiement
- `SERVER_HOST`: Adresse IP ou domaine du serveur
- `SERVER_USERNAME`: Nom d'utilisateur SSH
- `SERVER_SSH_KEY`: Clé privée SSH pour l'authentification
- `SERVER_PATH`: Chemin du répertoire de déploiement sur le serveur

### Application
- `VITE_API_URL`: URL de l'API backend pour le frontend (dev)
- `PRODUCTION_API_URL`: URL de l'API backend pour la production

### Docker (optionnel)
- `GITHUB_TOKEN`: Automatiquement fourni par GitHub Actions

## Configuration Locale

### Backend
1. Copier `.env.example` vers `.env`
2. Configurer la base de données MySQL
3. Exécuter `composer install`
4. Exécuter `php artisan key:generate`
5. Exécuter `php artisan migrate`

### Frontend
1. Exécuter `npm install`
2. Créer `.env.local` avec `VITE_API_URL=http://localhost:8000`
3. Exécuter `npm run dev`

## Tests

### Backend
```bash
cd backend
php artisan test
```

### Frontend
```bash
cd frontend
npm run test
```

## Déploiement Manuel

Pour déclencher un déploiement manuel:
1. Aller dans Actions > Deploy to Production
2. Cliquer sur "Run workflow"
3. Sélectionner la branche `main`
4. Cliquer sur "Run workflow"

## Monitoring

- Les tests doivent passer avec au moins 80% de couverture de code
- Les audits de sécurité sont exécutés hebdomadairement
- Les images Docker sont taguées avec le SHA du commit et la version

## Troubleshooting

### Les tests échouent
- Vérifier que MySQL est correctement configuré
- Vérifier que toutes les dépendances sont installées
- Vérifier les logs dans l'onglet Actions

### Le déploiement échoue
- Vérifier que tous les secrets sont configurés
- Vérifier les permissions SSH sur le serveur
- Vérifier les logs de déploiement

### Les images Docker ne se construisent pas
- Vérifier les Dockerfiles
- Vérifier que le GITHUB_TOKEN a les permissions nécessaires
- Vérifier les logs de build
