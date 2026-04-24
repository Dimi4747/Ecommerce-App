# Docker Setup pour Laravel

## Services inclus

- **PHP 8.2-FPM** : Application Laravel
- **Nginx** : Serveur web (port 8000)
- **MySQL 8.0** : Base de données (port 3306)
- **Redis** : Cache et sessions (port 6379)
- **Node.js 18** : Build des assets frontend (port 5173)

## Démarrage rapide

1. Construire et démarrer les conteneurs :
```bash
docker-compose up -d --build
```

2. Installer les dépendances PHP :
```bash
docker-compose exec app composer install
```

3. Configurer l'environnement :
```bash
docker-compose exec app cp .env.example .env
docker-compose exec app php artisan key:generate
```

4. Exécuter les migrations :
```bash
docker-compose exec app php artisan migrate
```

5. Accéder à l'application :
- Backend : http://localhost:8000
- Frontend (Vite) : http://localhost:5173

## Configuration .env

Les variables d'environnement sont automatiquement injectées dans le conteneur `app`. 

Si vous souhaitez personnaliser, créez `backend/.env` à partir de `.env.example` et modifiez ces valeurs :

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

Note : Les variables d'environnement définies dans docker-compose.yml ont priorité sur celles du fichier .env

## Commandes utiles

```bash
# Arrêter les conteneurs
docker-compose down

# Voir les logs
docker-compose logs -f

# Accéder au conteneur PHP
docker-compose exec app bash

# Exécuter des commandes Artisan
docker-compose exec app php artisan [command]

# Nettoyer tout (volumes inclus)
docker-compose down -v
```
