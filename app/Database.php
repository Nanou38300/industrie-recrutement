<?php
namespace App; // 🌐 Espace de noms pour organiser ton code et éviter les conflits

use PDO; // 📚 Utilisation de la classe PDO pour la connexion à la base de données

class Database
{
    private PDO $connection; // 🔒 Propriété privée qui contiendra l'objet PDO
    private static ?Database $instance = null;

    public function __construct()
    {
        // 🔧 Paramètres de connexion récupérés depuis le fichier .env (via $_ENV[])
        $host = $_ENV["DB_HOST_LOCAL"];          // Adresse du serveur MySQL (ex: localhost)
        $dbname = $_ENV["DB_NAME_LOCAL"];        // Nom de ta base de données
        $charset = "utf8mb4";                    // Jeu de caractères recommandé (support emojis, accents...)
        $username = $_ENV["DB_USER_LOCAL"];      // Nom d'utilisateur pour MySQL
        $password = $_ENV["DB_PASSWORD_LOCAL"];  // Mot de passe associé

        // 🔌 Création de la connexion PDO avec les paramètres
        $this->connection = new PDO("mysql:host=$host;dbname=$dbname;charset=$charset", $username, $password);

        // ⚠️ Configuration des options PDO :
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);      // Active les exceptions en cas d'erreur SQL
        $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Par défaut, les résultats seront sous forme de tableau associatif
    }

    // 🔄 Méthode publique pour récupérer l’objet PDO ailleurs dans ton projet
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

}
