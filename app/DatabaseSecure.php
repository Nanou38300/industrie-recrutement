<?php
namespace App;

use PDO;
use PDOException;

class Database
{
    private PDO $connection;
    private static ?Database $instance = null;

    public function __construct()
    {
        try {
            // Paramètres de connexion récupérés depuis le fichier .env
            $host = $_ENV["DB_HOST_LOCAL"] ?? 'localhost';
            $dbname = $_ENV["DB_NAME_LOCAL"] ?? '';
            $charset = "utf8mb4";
            $username = $_ENV["DB_USER_LOCAL"] ?? '';
            $password = $_ENV["DB_PASSWORD_LOCAL"] ?? '';

            // Validation des paramètres
            if (empty($dbname) || empty($username)) {
                throw new \Exception("Configuration de base de données invalide");
            }

            // DSN avec options de sécurité
            $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
            
            // Options PDO sécurisées
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES $charset COLLATE utf8mb4_unicode_ci",
            ];

            // Création de la connexion PDO avec les options
            $this->connection = new PDO($dsn, $username, $password, $options);
            
        } catch (PDOException $e) {
            error_log("Erreur de connexion à la base de données: " . $e->getMessage());
            throw new \Exception("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
        }
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function closeConnection(): void
    {
        $this->connection = null;
        self::$instance = null;
    }

    public function testConnection(): bool
    {
        try {
            $this->connection->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            error_log("Test de connexion échoué: " . $e->getMessage());
            return false;
        }
    }
}
