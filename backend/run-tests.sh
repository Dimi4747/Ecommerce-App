#!/bin/bash

# Script pour exécuter les tests PHPUnit

echo "🧪 Exécution des tests PHPUnit..."
echo ""

# Vérifier si un argument est passé
if [ "$1" == "unit" ]; then
    echo "📦 Tests unitaires uniquement"
    php artisan test --testsuite=Unit
elif [ "$1" == "feature" ]; then
    echo "🔧 Tests feature uniquement"
    php artisan test --testsuite=Feature
elif [ "$1" == "coverage" ]; then
    echo "📊 Tests avec couverture de code"
    php artisan test --coverage
elif [ "$1" == "parallel" ]; then
    echo "⚡ Tests en parallèle"
    php artisan test --parallel
else
    echo "🚀 Tous les tests"
    php artisan test
fi

echo ""
echo "✅ Tests terminés!"
