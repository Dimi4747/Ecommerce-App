# Requirements Document

## Introduction

Ce document définit les exigences pour la séparation d'une application Laravel + Inertia.js + React monolithique en deux projets distincts : un backend Laravel API et un frontend React standalone. L'objectif est de découpler l'architecture tout en maintenant la fonctionnalité existante via Inertia.js.

## Glossary

- **Backend_Project**: Le projet Laravel contenant l'API, les controllers, models, migrations et la logique métier
- **Frontend_Project**: Le projet React contenant les composants, pages, layouts et assets frontend
- **Inertia_Adapter**: Le middleware et la configuration permettant la communication entre Backend et Frontend via Inertia.js
- **Build_System**: Le système de build (Vite) responsable de la compilation des assets frontend
- **API_Endpoint**: Les routes HTTP exposées par le Backend_Project
- **Asset_Manifest**: Le fichier manifest.json généré par Vite contenant les chemins des assets compilés
- **CORS_Configuration**: La configuration Cross-Origin Resource Sharing permettant les requêtes entre domaines différents
- **Session_Manager**: Le système de gestion des sessions Laravel pour l'authentification
- **Development_Server**: Le serveur de développement local (Laravel serve + Vite dev server)

## Requirements

### Requirement 1: Séparation de la structure des dossiers

**User Story:** En tant que développeur, je veux séparer le projet en deux dossiers distincts backend/ et frontend/, afin de découpler clairement les responsabilités et faciliter le déploiement indépendant.

#### Acceptance Criteria

1. THE Backend_Project SHALL contenir tous les fichiers PHP Laravel (app/, config/, database/, routes/, storage/, bootstrap/, public/, vendor/)
2. THE Frontend_Project SHALL contenir tous les fichiers React/JSX (components, pages, layouts, assets CSS)
3. THE Backend_Project SHALL conserver composer.json, artisan, phpunit.xml et les fichiers de configuration PHP
4. THE Frontend_Project SHALL conserver package.json, vite.config.js, tailwind.config.js, jsconfig.json et postcss.config.js
5. THE Backend_Project SHALL maintenir la structure Laravel standard avec public/index.php comme point d'entrée

### Requirement 2: Configuration du Build System frontend

**User Story:** En tant que développeur, je veux que le Frontend_Project compile ses assets indépendamment, afin que le backend puisse les servir sans dépendance directe au code source frontend.

#### Acceptance Criteria

1. THE Build_System SHALL compiler les assets React dans un dossier de sortie accessible au Backend_Project
2. THE Build_System SHALL générer un Asset_Manifest avec les chemins des fichiers compilés
3. WHEN le Build_System compile les assets, THE Frontend_Project SHALL produire des fichiers avec hash pour le cache-busting
4. THE Build_System SHALL supporter le mode développement avec hot module replacement (HMR)
5. THE Build_System SHALL configurer le chemin de base (base URL) pour les assets en production

### Requirement 3: Configuration Inertia.js pour architecture séparée

**User Story:** En tant que développeur, je veux configurer Inertia.js pour fonctionner avec des projets séparés, afin de maintenir la communication SSR-like entre backend et frontend.

#### Acceptance Criteria

1. THE Inertia_Adapter SHALL résoudre les composants React depuis les assets compilés du Frontend_Project
2. THE Backend_Project SHALL servir le template HTML racine (app.blade.php) avec les références aux assets frontend
3. WHEN une requête Inertia est reçue, THE Backend_Project SHALL retourner les données JSON avec les props de page
4. THE Inertia_Adapter SHALL inclure les chemins corrects vers les assets JS et CSS depuis le Asset_Manifest
5. THE Backend_Project SHALL configurer le rootView Inertia pour pointer vers le template blade approprié

### Requirement 4: Gestion des assets et du manifest Vite

**User Story:** En tant que développeur, je veux que Laravel charge automatiquement les assets depuis le manifest Vite, afin d'avoir les bons chemins en production et développement.

#### Acceptance Criteria

1. WHEN en mode développement, THE Backend_Project SHALL pointer vers le Development_Server Vite pour le HMR
2. WHEN en mode production, THE Backend_Project SHALL lire le Asset_Manifest pour résoudre les chemins des assets
3. THE Backend_Project SHALL exposer une route ou un dossier public pour servir les assets compilés
4. THE Asset_Manifest SHALL être accessible au Backend_Project via un chemin configurable
5. IF le Asset_Manifest est manquant en production, THEN THE Backend_Project SHALL retourner une erreur explicite

### Requirement 5: Configuration CORS et sessions

**User Story:** En tant que développeur, je veux configurer CORS et les sessions correctement, afin que l'authentification et les requêtes cross-origin fonctionnent entre backend et frontend.

#### Acceptance Criteria

1. THE CORS_Configuration SHALL autoriser les requêtes depuis l'origine du Frontend_Project en développement
2. THE Session_Manager SHALL supporter les cookies cross-domain avec les attributs SameSite appropriés
3. THE Backend_Project SHALL configurer les domaines de confiance pour les sessions (SANCTUM_STATEFUL_DOMAINS)
4. THE CORS_Configuration SHALL autoriser les credentials (cookies) dans les requêtes
5. THE Backend_Project SHALL configurer SESSION_DOMAIN pour supporter les sous-domaines si nécessaire

### Requirement 6: Variables d'environnement et configuration

**User Story:** En tant que développeur, je veux centraliser la configuration des URLs et chemins, afin de faciliter le déploiement dans différents environnements.

#### Acceptance Criteria

1. THE Backend_Project SHALL définir une variable d'environnement pour l'URL du Frontend_Project
2. THE Frontend_Project SHALL définir une variable d'environnement pour l'URL du Backend_Project (API)
3. THE Backend_Project SHALL définir le chemin vers le dossier des assets compilés du Frontend_Project
4. THE Frontend_Project SHALL définir l'URL de base pour les assets en production
5. THE Backend_Project SHALL valider la présence des variables d'environnement critiques au démarrage

### Requirement 7: Scripts de développement

**User Story:** En tant que développeur, je veux des scripts npm/composer simplifiés, afin de démarrer facilement les serveurs de développement backend et frontend.

#### Acceptance Criteria

1. THE Backend_Project SHALL fournir un script pour démarrer le serveur Laravel (php artisan serve)
2. THE Frontend_Project SHALL fournir un script pour démarrer le Development_Server Vite
3. THE Backend_Project SHALL documenter la commande pour démarrer les deux serveurs simultanément
4. THE Frontend_Project SHALL fournir un script de build pour la production
5. THE Backend_Project SHALL fournir un script pour copier les assets compilés si nécessaire

### Requirement 8: Migration des fichiers existants

**User Story:** En tant que développeur, je veux migrer tous les fichiers existants vers la nouvelle structure, afin de préserver tout le code et la configuration actuels.

#### Acceptance Criteria

1. THE Backend_Project SHALL contenir tous les controllers, models, migrations, seeders et factories existants
2. THE Frontend_Project SHALL contenir tous les composants React, pages et layouts existants
3. THE Backend_Project SHALL conserver tous les fichiers de routes (web.php, api.php, auth.php)
4. THE Frontend_Project SHALL conserver tous les fichiers CSS et la configuration Tailwind
5. THE Backend_Project SHALL conserver les tests PHPUnit existants dans leur structure actuelle

### Requirement 9: Documentation de la nouvelle architecture

**User Story:** En tant que développeur, je veux une documentation claire de la nouvelle architecture, afin de comprendre comment les deux projets interagissent.

#### Acceptance Criteria

1. THE Backend_Project SHALL inclure un README expliquant la structure et les commandes de développement
2. THE Frontend_Project SHALL inclure un README expliquant la structure et les commandes de build
3. THE Backend_Project SHALL documenter la configuration Inertia et le chargement des assets
4. THE Frontend_Project SHALL documenter la configuration Vite et les variables d'environnement
5. THE Backend_Project SHALL documenter le processus de déploiement pour les deux projets

### Requirement 10: Compatibilité avec l'authentification existante

**User Story:** En tant que développeur, je veux que l'authentification Laravel Breeze continue de fonctionner, afin de préserver les fonctionnalités de login, register et gestion de profil.

#### Acceptance Criteria

1. THE Backend_Project SHALL conserver tous les controllers d'authentification (Auth/)
2. THE Frontend_Project SHALL conserver toutes les pages d'authentification React (Login, Register, etc.)
3. THE Session_Manager SHALL maintenir les sessions utilisateur entre les requêtes backend et frontend
4. THE Inertia_Adapter SHALL partager les données auth.user avec le frontend via les shared props
5. WHEN un utilisateur se connecte, THE Backend_Project SHALL créer une session valide pour les requêtes suivantes
