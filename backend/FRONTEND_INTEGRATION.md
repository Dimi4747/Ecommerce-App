# Guide d'intégration Frontend-Backend

## Configuration CORS

Le fichier `config/cors.php` a été configuré pour permettre les requêtes depuis le frontend React.

### Origins autorisées
- `http://localhost:5173` (Vite dev server)
- `http://localhost:3000` (Alternative)
- `http://127.0.0.1:5173`
- `http://127.0.0.1:3000`

### Méthodes autorisées
Toutes les méthodes HTTP sont autorisées (`GET`, `POST`, `PUT`, `DELETE`, `PATCH`, `OPTIONS`)

### Headers autorisés
Tous les headers sont autorisés

## Middleware CORS

Assurez-vous que le middleware CORS est activé dans `bootstrap/app.php` ou `app/Http/Kernel.php` :

```php
// Laravel 11+
->withMiddleware(function (Middleware $middleware) {
    $middleware->api(prepend: [
        \Illuminate\Http\Middleware\HandleCors::class,
    ]);
})

// Laravel 10 et antérieur
protected $middleware = [
    \Illuminate\Http\Middleware\HandleCors::class,
    // ...
];
```

## API Endpoints disponibles

### Orders
- `GET /api/orders` - Liste paginée (20 par page)
- `GET /api/orders/{id}` - Détails avec relations (customer, orderItems.product)
- `POST /api/orders` - Créer une commande
- `PUT /api/orders/{id}` - Mettre à jour (status, notes)
- `DELETE /api/orders/{id}` - Supprimer
- `GET /api/orders/statistics` - Statistiques complètes

### Products
- `GET /api/products` - Liste complète (non paginée)
- `GET /api/products/{id}` - Détails
- `POST /api/products` - Créer (à implémenter)
- `PUT /api/products/{id}` - Mettre à jour (à implémenter)
- `DELETE /api/products/{id}` - Supprimer (à implémenter)

### Customers
- `GET /api/customers` - Liste complète avec statistiques (non paginée)
- `GET /api/customers/{id}` - Détails (à implémenter)
- `POST /api/customers` - Créer (à implémenter)
- `PUT /api/customers/{id}` - Mettre à jour (à implémenter)
- `DELETE /api/customers/{id}` - Supprimer (à implémenter)

## Structure des réponses

### Order (avec pagination)
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "order_number": "ORD-ABC123",
      "customer_id": 1,
      "status": "pending",
      "subtotal": 100.00,
      "tax_amount": 20.00,
      "shipping_amount": 5.00,
      "total_amount": 125.00,
      "shipping_address": "123 Street",
      "notes": "Special instructions",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z",
      "customer": {
        "id": 1,
        "first_name": "John",
        "last_name": "Doe",
        "email": "john@example.com",
        "phone": "0612345678"
      },
      "order_items": [
        {
          "id": 1,
          "order_id": 1,
          "product_id": 1,
          "quantity": 2,
          "unit_price": 50.00,
          "total_price": 100.00,
          "product": {
            "id": 1,
            "name": "Product Name",
            "sku": "SKU-001"
          }
        }
      ]
    }
  ],
  "first_page_url": "http://localhost:8000/api/orders?page=1",
  "from": 1,
  "last_page": 5,
  "last_page_url": "http://localhost:8000/api/orders?page=5",
  "links": [...],
  "next_page_url": "http://localhost:8000/api/orders?page=2",
  "path": "http://localhost:8000/api/orders",
  "per_page": 20,
  "prev_page_url": null,
  "to": 20,
  "total": 100
}
```

### Product (tableau direct)
```json
[
  {
    "id": 1,
    "name": "Product Name",
    "sku": "SKU-001",
    "description": "Product description",
    "price": 99.99,
    "stock_quantity": 10,
    "category": "Electronics",
    "status": "active",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
]
```

### Customer (tableau direct)
```json
[
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
    "created_at": "2024-01-01T00:00:00.000000Z",
    "total_orders": 5,
    "total_spent": 499.95
  }
]
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
  "recent_orders": [
    {
      "id": 1,
      "order_number": "ORD-ABC123",
      "status": "pending",
      "status_label": "En attente",
      "total_amount": 125.00,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "customer": {
        "id": 1,
        "first_name": "John",
        "last_name": "Doe",
        "email": "john@example.com"
      }
    }
  ]
}
```

## Validation des requêtes

### Créer une commande (POST /api/orders)
```json
{
  "customer_id": 1,
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    }
  ],
  "shipping_address": "123 Street, Paris",
  "notes": "Special instructions"
}
```

**Règles de validation:**
- `customer_id`: requis, doit exister dans la table customers
- `items`: requis, tableau avec au moins 1 élément
- `items.*.product_id`: requis, doit exister dans la table products
- `items.*.quantity`: requis, entier, minimum 1
- `shipping_address`: optionnel, chaîne de caractères
- `notes`: optionnel, chaîne de caractères

### Mettre à jour une commande (PUT /api/orders/{id})
```json
{
  "status": "processing",
  "notes": "Updated notes"
}
```

**Règles de validation:**
- `status`: requis, doit être l'une des valeurs: pending, processing, shipped, delivered, cancelled
- `notes`: optionnel, chaîne de caractères

## Calculs automatiques

Lors de la création d'une commande, le backend calcule automatiquement :
- `subtotal`: Somme des (prix unitaire × quantité) de tous les articles
- `tax_amount`: 20% du subtotal (TVA française)
- `shipping_amount`: 5.00 € (fixe pour le moment)
- `total_amount`: subtotal + tax_amount + shipping_amount

## Démarrage du serveur

```bash
# Installer les dépendances
composer install

# Configurer l'environnement
cp .env.example .env
php artisan key:generate

# Créer la base de données
touch database/database.sqlite

# Exécuter les migrations
php artisan migrate

# Exécuter les seeders (données de test)
php artisan db:seed

# Démarrer le serveur
php artisan serve
```

Le serveur sera accessible sur `http://localhost:8000`

## Tests

Pour tester les endpoints API :

```bash
# Tester les statistiques
curl http://localhost:8000/api/orders/statistics

# Tester la liste des commandes
curl http://localhost:8000/api/orders

# Tester la liste des produits
curl http://localhost:8000/api/products

# Tester la liste des clients
curl http://localhost:8000/api/customers
```

## Prochaines étapes

### À implémenter
1. [ ] Méthodes CRUD complètes pour Products
2. [ ] Méthodes CRUD complètes pour Customers
3. [ ] Authentification API (Sanctum)
4. [ ] Gestion des catégories
5. [ ] Gestion des variantes de produits
6. [ ] Système de panier
7. [ ] Intégration de paiement
8. [ ] Pagination pour Products et Customers
9. [ ] Filtres et recherche avancée
10. [ ] Upload d'images pour les produits

### Améliorations suggérées
- Ajouter des Resource classes pour formater les réponses API
- Implémenter des Form Request classes pour la validation
- Ajouter des tests unitaires et d'intégration
- Documenter l'API avec Swagger/OpenAPI
- Ajouter un système de logs pour les actions importantes
- Implémenter un système de cache pour les statistiques

## Dépannage

### Erreur CORS
Si vous rencontrez des erreurs CORS :
1. Vérifiez que `config/cors.php` existe et est correctement configuré
2. Vérifiez que le middleware CORS est activé
3. Videz le cache de configuration : `php artisan config:clear`
4. Redémarrez le serveur Laravel

### Erreur 404 sur les routes API
1. Vérifiez que les routes sont bien définies dans `routes/api.php`
2. Videz le cache des routes : `php artisan route:clear`
3. Listez les routes disponibles : `php artisan route:list`

### Erreur de base de données
1. Vérifiez que le fichier `database/database.sqlite` existe
2. Exécutez les migrations : `php artisan migrate:fresh --seed`
3. Vérifiez les permissions du fichier de base de données

## Support

Pour toute question ou problème, consultez :
- Documentation Laravel : https://laravel.com/docs
- Documentation CORS : https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
