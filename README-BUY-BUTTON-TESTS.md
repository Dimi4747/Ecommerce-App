# Tests du Bouton "Acheter"

## Vue d'ensemble

Ce document décrit les tests unitaires et d'interface pour le bouton "Acheter" de l'application e-commerce.

## Structure des tests

```
├── backend/
│   ├── app/Http/Controllers/OrderController.php  # Contrôleur de commande
│   ├── app/routes/api.php                        # Routes API
│   └── tests/Feature/BuyButtonTest.php           # Tests PHPUnit
└── frontend/
    ├── resources/js/Components/BuyButton.jsx     # Composant React
    ├── tests/BuyButton.test.jsx                  # Tests Vitest
    ├── tests/setup.js                            # Configuration des tests
    └── vitest.config.js                          # Configuration Vitest
```

## Tests Backend (PHPUnit)

### OrderController

Le contrôleur gère :
- Traitement des commandes d'achat
- Validation des données
- Calcul du total
- Vérification du statut de commande

### Tests Feature

**BuyButtonTest.php** couvre :

✓ Utilisateur authentifié peut acheter un produit
✓ Utilisateur non authentifié ne peut pas acheter
✓ Validation des champs requis (product_id, quantity, price)
✓ Quantité minimale de 1
✓ Prix positif requis
✓ Calcul correct du total
✓ Vérification du statut de commande

### Exécution des tests backend

```bash
cd backend

# Tous les tests
php artisan test

# Tests du bouton acheter uniquement
php artisan test --filter BuyButtonTest

# Avec couverture
php artisan test --filter BuyButtonTest --coverage
```

## Tests Frontend (Vitest)

### Composant BuyButton

Le composant React :
- Affiche un bouton "Buy Now"
- Gère l'état de chargement (Processing...)
- Appelle l'API pour créer une commande
- Gère les callbacks de succès/erreur
- Désactive le bouton pendant le traitement

### Tests Vitest

**BuyButton.test.jsx** couvre :

✓ Rendu correct du bouton
✓ Appel API lors du clic
✓ Affichage de l'état "Processing..."
✓ Désactivation pendant le traitement
✓ Callback onSuccess appelé
✓ Callback onError appelé
✓ Réactivation après traitement
✓ Protection contre les clics multiples

### Exécution des tests frontend

```bash
cd frontend

# Installer les dépendances
npm install

# Tous les tests
npm test

# Mode watch
npm test -- --watch

# Avec interface UI
npm run test:ui

# Avec couverture
npm run test:coverage
```

## API Endpoints

### POST /api/orders/purchase

Crée une nouvelle commande.

**Authentification requise** : Oui (Sanctum)

**Paramètres** :
```json
{
  "product_id": 1,
  "quantity": 2,
  "price": 29.99
}
```

**Réponse** (201) :
```json
{
  "success": true,
  "message": "Order placed successfully",
  "order": {
    "id": 1234,
    "user_id": 1,
    "product_id": 1,
    "quantity": 2,
    "price": 29.99,
    "total": 59.98,
    "status": "pending",
    "created_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

### GET /api/orders/{orderId}/status

Vérifie le statut d'une commande.

**Authentification requise** : Oui

**Réponse** (200) :
```json
{
  "order_id": "1234",
  "status": "completed",
  "message": "Order has been processed"
}
```

## Utilisation du composant

```jsx
import BuyButton from '@/Components/BuyButton';

function ProductPage() {
    const handleSuccess = (response) => {
        console.log('Order placed:', response.order);
        alert('Purchase successful!');
    };

    const handleError = (error) => {
        console.error('Purchase failed:', error);
        alert('Purchase failed. Please try again.');
    };

    return (
        <BuyButton
            productId={1}
            price={29.99}
            quantity={2}
            onSuccess={handleSuccess}
            onError={handleError}
        />
    );
}
```

## Configuration CI/CD

Les tests sont automatiquement exécutés via GitHub Actions lors des push et pull requests.

### Backend Tests

```yaml
- name: Run PHPUnit Tests
  run: |
    cd backend
    php artisan test --filter BuyButtonTest
```

### Frontend Tests

```yaml
- name: Run Vitest Tests
  run: |
    cd frontend
    npm install
    npm test
```

## Bonnes pratiques

### Tests Backend

1. Utiliser `RefreshDatabase` pour isoler les tests
2. Tester tous les cas de validation
3. Vérifier les codes de statut HTTP
4. Valider la structure JSON des réponses
5. Tester l'authentification

### Tests Frontend

1. Utiliser `data-testid` pour sélectionner les éléments
2. Mocker les appels API
3. Tester les états de chargement
4. Vérifier les callbacks
5. Tester les cas limites (clics multiples, erreurs)

## Debugging

### Backend

```php
// Dans les tests
$response->dump();        // Afficher la réponse
$response->dumpHeaders(); // Afficher les headers
dd($response->json());    // Dump and die
```

### Frontend

```javascript
// Dans les tests
screen.debug();           // Afficher le DOM
console.log(screen.getByTestId('buy-button'));
```

## Couverture de code

### Objectifs

- Backend : > 80% de couverture
- Frontend : > 80% de couverture

### Vérification

```bash
# Backend
cd backend
php artisan test --coverage --min=80

# Frontend
cd frontend
npm run test:coverage
```

## Ressources

- [Laravel Testing](https://laravel.com/docs/testing)
- [Vitest Documentation](https://vitest.dev/)
- [React Testing Library](https://testing-library.com/react)
- [Testing Best Practices](https://kentcdodds.com/blog/common-mistakes-with-react-testing-library)
