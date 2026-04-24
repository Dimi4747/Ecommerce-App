# Intégration Frontend-Backend

## Modifications apportées

### 1. Configuration API centralisée
- **Fichier**: `src/config/api.js`
- **Description**: Configuration centralisée pour tous les appels API avec axios
- **Avantages**:
  - URLs d'API centralisées et faciles à maintenir
  - Gestion globale des erreurs
  - Support des variables d'environnement
  - Intercepteurs pour le logging et l'authentification future

### 2. Proxy Vite configuré
- **Fichier**: `vite.config.js`
- **Configuration**: Proxy `/api` vers `http://localhost:8000`
- **Avantage**: Évite les problèmes CORS en développement

### 3. Composants mis à jour

#### Dashboard.jsx
- ✅ Utilise `apiClient` et `API_ENDPOINTS`
- ✅ Compatible avec la structure de réponse du backend
- ✅ Affiche les statistiques correctement

#### ProductList.jsx
- ✅ Utilise `apiClient` et `API_ENDPOINTS`
- ✅ Corrigé: `stock` → `stock_quantity` (correspond au backend)
- ✅ Suppression des données mock
- ✅ Gestion correcte des produits sans pagination

#### OrderList.jsx
- ✅ Utilise `apiClient` et `API_ENDPOINTS`
- ✅ Compatible avec la pagination Laravel
- ✅ Mise à jour du statut fonctionnelle
- ✅ Suppression de commandes fonctionnelle

#### CustomerList.jsx
- ✅ Utilise `apiClient` et `API_ENDPOINTS`
- ✅ Affichage des clients avec statistiques
- ✅ Gestion correcte des clients sans pagination

## Structure des données Backend

### Products
```json
{
  "id": 1,
  "name": "Product Name",
  "sku": "SKU-001",
  "description": "Description",
  "price": 99.99,
  "stock_quantity": 10,
  "category": "Category",
  "status": "active",
  "created_at": "2024-01-01T00:00:00Z",
  "updated_at": "2024-01-01T00:00:00Z"
}
```

### Orders (avec pagination)
```json
{
  "data": [...],
  "current_page": 1,
  "from": 1,
  "to": 20,
  "total": 100,
  "per_page": 20,
  "last_page": 5,
  "links": [...],
  "prev_page_url": null,
  "next_page_url": "..."
}
```

### Customers
```json
{
  "id": 1,
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@example.com",
  "phone": "0612345678",
  "address": "123 Street",
  "city": "Paris",
  "postal_code": "75001",
  "country": "France",
  "created_at": "2024-01-01T00:00:00Z",
  "total_orders": 5,
  "total_spent": 499.95
}
```

### Statistics
```json
{
  "total_orders": 100,
  "pending_orders": 10,
  "processing_orders": 20,
  "completed_orders": 70,
  "total_revenue": 10000.00,
  "total_customers": 50,
  "total_products": 30,
  "recent_orders": [...]
}
```

## Configuration requise

### Variables d'environnement
Créer un fichier `.env` à la racine du frontend :
```env
VITE_API_URL=http://localhost:8000
```

### Backend Laravel
Le backend doit être démarré sur `http://localhost:8000` avec :
```bash
cd backend
php artisan serve
```

### Frontend React
Démarrer le serveur de développement :
```bash
cd frontend
npm install
npm run dev
```

## CORS Configuration (Backend)

Si vous rencontrez des problèmes CORS, assurez-vous que le backend Laravel a la configuration CORS correcte dans `config/cors.php` :

```php
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:5173'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
```

## Endpoints API disponibles

### Orders
- `GET /api/orders` - Liste paginée des commandes
- `GET /api/orders/{id}` - Détails d'une commande
- `POST /api/orders` - Créer une commande
- `PUT /api/orders/{id}` - Mettre à jour une commande
- `DELETE /api/orders/{id}` - Supprimer une commande
- `GET /api/orders/statistics` - Statistiques des commandes

### Products
- `GET /api/products` - Liste des produits
- `GET /api/products/{id}` - Détails d'un produit
- `POST /api/products` - Créer un produit
- `PUT /api/products/{id}` - Mettre à jour un produit
- `DELETE /api/products/{id}` - Supprimer un produit

### Customers
- `GET /api/customers` - Liste des clients
- `GET /api/customers/{id}` - Détails d'un client
- `POST /api/customers` - Créer un client
- `PUT /api/customers/{id}` - Mettre à jour un client
- `DELETE /api/customers/{id}` - Supprimer un client

## Prochaines étapes

1. ✅ Adapter les interfaces frontend pour le backend
2. ⏳ Implémenter les formulaires de création/édition
3. ⏳ Ajouter l'authentification
4. ⏳ Implémenter la gestion des catégories
5. ⏳ Implémenter la gestion des variantes de produits
6. ⏳ Ajouter la gestion du panier
7. ⏳ Implémenter le système de paiement

## Tests

Pour tester l'intégration :

1. Démarrer le backend Laravel
2. Démarrer le frontend React
3. Naviguer vers `http://localhost:5173`
4. Vérifier que les données s'affichent correctement dans chaque section

## Notes importantes

- Le backend utilise SQLite par défaut (voir `backend/database/database.sqlite`)
- Les seeders doivent être exécutés pour avoir des données de test
- La pagination est gérée côté backend pour les commandes
- Les produits et clients ne sont pas paginés actuellement (à implémenter si nécessaire)
