# Configuration Axios pour Laravel + React

## Installation

1. Installer les dépendances :
```bash
cd frontend
npm install
```

## Structure des fichiers

```
frontend/resources/js/
├── bootstrap.js              # Configuration Axios globale
├── services/
│   └── api.js               # Service API avec méthodes HTTP
├── hooks/
│   └── useApi.js            # Hook React pour les appels API
└── Components/
    └── ExampleApiComponent.jsx  # Exemple d'utilisation
```

## Configuration

### 1. Variables d'environnement

Créer `frontend/.env` :
```env
VITE_APP_NAME=Laravel
VITE_API_URL=http://localhost:8000
```

### 2. Bootstrap Axios

Le fichier `bootstrap.js` configure :
- Headers par défaut (X-Requested-With, CSRF Token)
- Credentials et XSRF Token
- Intercepteurs pour les requêtes et réponses
- Gestion des erreurs (401, 403, 404, 500)

## Utilisation

### Méthode 1 : Service API direct

```javascript
import api from './services/api';

// GET request
const data = await api.get('/api/users');

// POST request
const newUser = await api.post('/api/users', {
    name: 'John Doe',
    email: 'john@example.com'
});

// PUT request
const updated = await api.put('/api/users/1', { name: 'Jane Doe' });

// DELETE request
await api.delete('/api/users/1');
```

### Méthode 2 : Hook useApi (recommandé)

```javascript
import { useApi } from '../hooks/useApi';

function MyComponent() {
    const { loading, error, get, post } = useApi();
    const [users, setUsers] = useState([]);

    useEffect(() => {
        const fetchUsers = async () => {
            try {
                const data = await get('/api/users');
                setUsers(data);
            } catch (err) {
                console.error(err);
            }
        };
        fetchUsers();
    }, []);

    const createUser = async (userData) => {
        try {
            await post('/api/users', userData);
            // Refresh list
        } catch (err) {
            console.error(err);
        }
    };

    if (loading) return <div>Loading...</div>;
    if (error) return <div>Error: {error}</div>;

    return <div>{/* Your component */}</div>;
}
```

### Méthode 3 : Axios global (window.axios)

```javascript
// Disponible globalement après import de bootstrap.js
const response = await window.axios.get('/api/users');
```

## Routes API Laravel

Créer des routes API dans `backend/routes/api.php` :

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});
```

## CSRF Protection

Laravel protège automatiquement les routes avec CSRF. Axios est configuré pour :
- Envoyer le token CSRF dans les headers
- Utiliser les cookies XSRF pour l'authentification

## Gestion des erreurs

Les erreurs sont interceptées automatiquement :
- 401 : Redirection vers /login
- 403 : Accès interdit
- 404 : Ressource non trouvée
- 500 : Erreur serveur

## Exemple complet

Voir `frontend/resources/js/Components/ExampleApiComponent.jsx` pour un exemple fonctionnel.
