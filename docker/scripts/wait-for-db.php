<?php

/**
 * Script d'attente pour la base de données MySQL
 * Attend que MySQL soit prêt avant de continuer l'initialisation
 */

$host = $_ENV['DB_HOST'] ?? 'mysql';
$port = $_ENV['DB_PORT'] ?? '3306';
$database = $_ENV['DB_DATABASE'] ?? 'bracongo_stages';
$username = $_ENV['DB_USERNAME'] ?? 'bracongo_user';
$password = $_ENV['DB_PASSWORD'] ?? 'bracongo_pass_2024';

$maxAttempts = 30;
$attempt = 0;

echo "Connexion à MySQL sur {$host}:{$port}...\n";

while ($attempt < $maxAttempts) {
    $attempt++;
    
    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$database}";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_TIMEOUT => 5,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        
        // Test simple de connexion
        $pdo->query('SELECT 1');
        
        echo "✅ Base de données MySQL prête !\n";
        exit(0);
        
    } catch (PDOException $e) {
        echo "Tentative {$attempt}/{$maxAttempts} : {$e->getMessage()}\n";
        
        if ($attempt >= $maxAttempts) {
            echo "❌ Impossible de se connecter à MySQL après {$maxAttempts} tentatives.\n";
            exit(1);
        }
        
        sleep(2);
    }
} 