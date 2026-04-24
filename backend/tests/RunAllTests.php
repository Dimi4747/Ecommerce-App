<?php

/**
 * Script personnalisé pour exécuter tous les tests backend
 * et générer un rapport détaillé
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class TestRunner
{
    private $results = [];
    private $startTime;
    private $endTime;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    public function runAllTests()
    {
        echo "🧪 Démarrage des tests backend...\n\n";

        $this->runTestSuite('Customer API Tests', 'CustomerApiTest');
        $this->runTestSuite('Product API Tests', 'ProductApiTest');
        $this->runTestSuite('Order API Tests', 'OrderApiTest');
        $this->runTestSuite('Order Statistics Tests', 'OrderStatisticsApiTest');
        $this->runTestSuite('Database Integration Tests', 'DatabaseIntegrationTest');
        $this->runTestSuite('Application Integration Tests', 'ApplicationIntegrationTest');

        $this->endTime = microtime(true);
        $this->generateReport();
    }

    private function runTestSuite($name, $testClass)
    {
        echo "📋 Exécution de: {$name}\n";
        
        $process = new Process(['php', 'artisan', 'test', '--filter', $testClass], __DIR__ . '/..');
        $process->run();

        $output = $process->getOutput();
        $errorOutput = $process->getErrorOutput();

        $this->results[$name] = [
            'success' => $process->isSuccessful(),
            'output' => $output,
            'error' => $errorOutput,
            'exit_code' => $process->getExitCode()
        ];

        if ($process->isSuccessful()) {
            echo "✅ {$name}: PASSÉ\n";
        } else {
            echo "❌ {$name}: ÉCHEC\n";
            if (!empty($errorOutput)) {
                echo "   Erreur: " . substr($errorOutput, 0, 200) . "...\n";
            }
        }
        echo "\n";
    }

    private function generateReport()
    {
        $totalTime = $this->endTime - $this->startTime;
        $totalTests = count($this->results);
        $passedTests = array_filter($this->results, fn($result) => $result['success']);
        $failedTests = array_filter($this->results, fn($result) => !$result['success']);

        echo "\n" . str_repeat("=", 60) . "\n";
        echo "📊 RAPPORT DE TESTS BACKEND\n";
        echo str_repeat("=", 60) . "\n\n";

        echo "⏱️  Temps total: " . number_format($totalTime, 2) . " secondes\n";
        echo "📈 Total tests: {$totalTests}\n";
        echo "✅ Tests réussis: " . count($passedTests) . "\n";
        echo "❌ Tests échoués: " . count($failedTests) . "\n";
        echo "📊 Taux de réussite: " . number_format((count($passedTests) / $totalTests) * 100, 1) . "%\n\n";

        if (!empty($failedTests)) {
            echo "🔍 Détails des échecs:\n";
            echo str_repeat("-", 40) . "\n";
            
            foreach ($failedTests as $testName => $result) {
                echo "❌ {$testName}\n";
                if (!empty($result['error'])) {
                    echo "   Erreur: " . substr($result['error'], 0, 300) . "...\n";
                }
                echo "\n";
            }
        }

        echo "🎯 Recommandations:\n";
        if (count($failedTests) === 0) {
            echo "✅ Tous les tests passent! L'application backend est prête pour la production.\n";
        } else {
            echo "⚠️  Certains tests échouent. Veuillez corriger les problèmes avant de déployer.\n";
        }

        echo "\n" . str_repeat("=", 60) . "\n";
        echo "🏁 FIN DES TESTS\n";
        echo str_repeat("=", 60) . "\n";
    }
}

// Exécuter les tests
$runner = new TestRunner();
$runner->runAllTests();
