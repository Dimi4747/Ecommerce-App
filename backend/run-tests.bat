@echo off
REM Script pour exécuter les tests PHPUnit sur Windows

echo 🧪 Exécution des tests PHPUnit...
echo.

if "%1"=="unit" (
    echo 📦 Tests unitaires uniquement
    php artisan test --testsuite=Unit
) else if "%1"=="feature" (
    echo 🔧 Tests feature uniquement
    php artisan test --testsuite=Feature
) else if "%1"=="coverage" (
    echo 📊 Tests avec couverture de code
    php artisan test --coverage
) else if "%1"=="parallel" (
    echo ⚡ Tests en parallèle
    php artisan test --parallel
) else (
    echo 🚀 Tous les tests
    php artisan test
)

echo.
echo ✅ Tests terminés!
