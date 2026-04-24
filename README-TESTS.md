# Tests PHPUnit pour Laravel

## Structure des tests

```
backend/tests/
├── Feature/              # Tests d'intégration
│   ├── ApiTest.php      # Tests des endpoints API
│   ├── DatabaseTest.php # Tests de la base de données
│   ├── ProfileTest.php  # Tests du profil utilisateur
│   └── Auth/            # Tests d'authentification (existants)
└── Unit/                # Tests unitaires
    ├── UserTest.php     # Tests du modèle User
    └── ValidationTest.php # Tests de validation
```

## Configuration

La configuration PHPUnit se trouve dans `backend/phpunit.xml` :
- Base de données SQLite en mémoire pour les tests
- Cache et sessions en mode array
- Environnement de test isolé

## Exécution des tests

### Tous les tests
```bash
cd backend
php artisan test
```

Ou avec PHPUnit directement :
```bash
cd backend
./vendor/bin/phpunit
```

### Tests spécifiques

Tests unitaires uniquement :
```bash
php artisan test --testsuite=Unit
```

Tests feature uniquement :
```bash
php artisan test --testsuite=Feature
```

Test d'un fichier spécifique :
```bash
php artisan test tests/Unit/UserTest.php
```

Test d'une méthode spécifique :
```bash
php artisan test --filter test_user_can_be_created
```

### Avec couverture de code
```bash
php artisan test --coverage
```

Rapport HTML détaillé :
```bash
./vendor/bin/phpunit --coverage-html coverage
```

### Mode verbose
```bash
php artisan test --verbose
```

## Tests disponibles

### Tests unitaires

#### UserTest
- ✓ Création d'utilisateur
- ✓ Attributs fillable
- ✓ Attributs cachés
- ✓ Unicité de l'email

#### ValidationTest
- ✓ Validation d'email
- ✓ Champs requis
- ✓ Longueur minimale/maximale
- ✓ Valeurs uniques
- ✓ Types de données (numeric, array)

### Tests Feature

#### ApiTest
- ✓ Réponses JSON
- ✓ Routes protégées (authentification)
- ✓ Validation des champs

#### DatabaseTest
- ✓ Connexion à la base de données
- ✓ CRUD operations (Create, Read, Update, Delete)
- ✓ Factories

#### ProfileTest
- ✓ Affichage du profil
- ✓ Mise à jour des informations
- ✓ Vérification d'email
- ✓ Suppression de compte

## Bonnes pratiques

### 1. Utiliser RefreshDatabase
```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyTest extends TestCase
{
    use RefreshDatabase;
}
```

### 2. Factories pour les données de test
```php
$user = User::factory()->create([
    'email' => 'test@example.com'
]);
```

### 3. Assertions claires
```php
$this->assertDatabaseHas('users', ['email' => 'test@example.com']);
$this->assertEquals('expected', $actual);
$this->assertTrue($condition);
```

### 4. Tester les cas limites
- Données valides
- Données invalides
- Champs manquants
- Permissions

### 5. Nommer les tests clairement
```php
public function test_user_cannot_delete_account_with_wrong_password(): void
{
    // Test implementation
}
```

## Commandes utiles

```bash
# Créer un nouveau test
php artisan make:test UserTest --unit
php artisan make:test ApiTest

# Lister tous les tests
php artisan test --list-tests

# Exécuter en parallèle (plus rapide)
php artisan test --parallel

# Arrêter au premier échec
php artisan test --stop-on-failure

# Afficher les erreurs détaillées
php artisan test --verbose
```

## CI/CD

Exemple pour GitHub Actions :
```yaml
- name: Run tests
  run: |
    cd backend
    php artisan test --coverage
```

## Debugging

Pour déboguer un test :
```php
// Afficher les données
dump($variable);
dd($variable); // Dump and die

// Voir la réponse complète
$response->dump();
$response->dumpHeaders();
$response->dumpSession();
```

## Ressources

- [Documentation Laravel Testing](https://laravel.com/docs/testing)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Laravel HTTP Tests](https://laravel.com/docs/http-tests)
- [Database Testing](https://laravel.com/docs/database-testing)
