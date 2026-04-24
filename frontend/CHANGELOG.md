# Changelog - Intégration Frontend-Backend

## [1.0.0] - 2026-04-24

### Ajouté
- **Configuration API centralisée** (`src/config/api.js`)
  - Instance axios configurée avec intercepteurs
  - Endpoints API organisés par ressource
  - Support des variables d'environnement
  - Gestion globale des erreurs

- **Proxy Vite** pour éviter les problèmes CORS en développement
  - Configuration dans `vite.config.js`
  - Redirection `/api` vers `http://localhost:8000`

- **Documentation d'intégration** (`INTEGRATION.md`)
  - Guide complet d'intégration frontend-backend
  - Structure des données API
  - Configuration requise
  - Endpoints disponibles

- **Fichier d'exemple d'environnement** (`.env.example`)
  - Variables d'environnement pour la configuration API

### Modifié
- **Dashboard.jsx**
  - ✅ Migration vers `apiClient` et `API_ENDPOINTS`
  - ✅ Suppression des URLs hardcodées
  - ✅ Gestion améliorée des erreurs

- **ProductList.jsx**
  - ✅ Migration vers `apiClient` et `API_ENDPOINTS`
  - ✅ Correction: `stock` → `stock_quantity` (compatibilité backend)
  - ✅ Suppression des données mock
  - ✅ Adaptation à la réponse backend (tableau direct, pas de pagination)

- **OrderList.jsx**
  - ✅ Migration vers `apiClient` et `API_ENDPOINTS`
  - ✅ Support de la pagination Laravel
  - ✅ Mise à jour du statut avec `notes` inclus

- **OrderDetail.jsx**
  - ✅ Migration vers `apiClient` et `API_ENDPOINTS`
  - ✅ Affichage détaillé des commandes
  - ✅ Mise à jour du statut avec conservation des notes

- **CustomerList.jsx**
  - ✅ Migration vers `apiClient` et `API_ENDPOINTS`
  - ✅ Suppression des données mock
  - ✅ Adaptation à la réponse backend (tableau direct, pas de pagination)
  - ✅ Affichage des statistiques clients (total_orders, total_spent)

- **Statistics.jsx**
  - ✅ Migration vers `apiClient` et `API_ENDPOINTS`
  - ✅ Utilisation des statistiques du backend

### Compatibilité Backend

#### Endpoints utilisés
- `GET /api/orders` - Liste paginée des commandes
- `GET /api/orders/{id}` - Détails d'une commande
- `PUT /api/orders/{id}` - Mise à jour d'une commande
- `DELETE /api/orders/{id}` - Suppression d'une commande
- `GET /api/orders/statistics` - Statistiques des commandes
- `GET /api/products` - Liste des produits
- `GET /api/customers` - Liste des clients

#### Structure des données
- **Products**: Utilise `stock_quantity` au lieu de `stock`
- **Orders**: Pagination Laravel standard
- **Customers**: Inclut `total_orders` et `total_spent`
- **Statistics**: Inclut toutes les métriques du dashboard

### Technique
- Tous les composants utilisent maintenant l'instance axios centralisée
- Suppression de toutes les URLs hardcodées
- Gestion cohérente des erreurs
- Support des variables d'environnement via Vite

### À faire
- [ ] Implémenter les formulaires de création/édition pour les produits
- [ ] Implémenter les formulaires de création/édition pour les clients
- [ ] Ajouter l'authentification utilisateur
- [ ] Implémenter la gestion des catégories
- [ ] Implémenter la gestion des variantes de produits
- [ ] Ajouter la gestion du panier
- [ ] Implémenter le système de paiement
- [ ] Ajouter la pagination pour les produits et clients
- [ ] Améliorer la gestion des erreurs avec des notifications toast
- [ ] Ajouter des tests unitaires et d'intégration

### Notes de migration
Pour les développeurs qui travaillent sur ce projet :

1. **Ne plus utiliser axios directement** - Utiliser `apiClient` de `src/config/api.js`
2. **Ne plus hardcoder les URLs** - Utiliser `API_ENDPOINTS` de `src/config/api.js`
3. **Configurer les variables d'environnement** - Copier `.env.example` vers `.env`
4. **Démarrer le backend Laravel** sur le port 8000 avant de tester le frontend

### Commandes utiles
```bash
# Frontend
cd frontend
npm install
npm run dev

# Backend
cd backend
php artisan serve
```
