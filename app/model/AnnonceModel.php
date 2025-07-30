<?php
// Déclare le namespace : organisation du code
namespace App\Model;

// Importe la classe PDO pour la gestion de base de données
use PDO; 

// Définition de la classe principale du modèle
class AnnonceModel {
    // Propriété contenant les annonces simulées
    public $annonces = [];

    // Constructeur qui initialise les annonces simulées
    public function __construct() {
        // Deux exemples d’annonces : l’une simple, l’autre complète
        $this->annonces = [
            [
                "titre" => "CHAUDRONNIER H/F",               // Intitulé du poste
                "lieu" => "Saint fons - 69",                 // Localisation
                "contrat" => "CDD - 6 mois",                 // Type et durée du contrat
                "salaire" => "27 - 30 Ke",                   // Fourchette salariale
                "date" => "22/07/2025",                      // Date de publication
                "ref" => "201343",                           // Référence unique
                "description" => "",                         // Description vide ici
                "complet" => false                           // Annonce allégée
            ],
            [
                "titre" => "TUYAUTEUR H/F",
                "lieu" => "Saint vulbas - 01",
                "contrat" => "CDI",
                "salaire" => "35 - 40 Ke",
                "date" => "22/07/2025",
                "ref" => "224653",
                "description" => "En nous rejoignant, vous êtes à même de...", // Description complète
                "missions" => ["Débiter les tronçons", "Examiner les travaux", "Préparer le matériel"], // Liste de missions
                "profil" => "Issu des filières chaudronnerie/tuyauterie...",   // Profil recherché
                "avantages" => ["13e mois", "Prime d’intéressement", "Tickets restaurant"], // Avantages proposés
                "complet" => true
            ]
        ];
    }

    // Retourne la liste complète des annonces
    public function getAll() {
        return $this->annonces;
    }

    // Retourne une annonce en fonction de sa référence
    public function getByRef($ref) {
        foreach ($this->annonces as $a) {
            if ($a['ref'] === $ref) return $a; // Si la référence correspond, retourne l’annonce
        }
        return null; // Sinon, retourne null
    }

    // Enregistre une candidature dans la base de données
    public function enregistrerCandidature($ref, $cvPath) {
        // Connexion à la base avec PDO (exemple à adapter)
        $pdo = new PDO('mysql:host=localhost;dbname=ton_db', 'user', 'password');
        // Préparation de la requête SQL avec placeholders
        $stmt = $pdo->prepare("INSERT INTO candidatures (ref_annonce, cv_path, date_postulation) VALUES (?, ?, NOW())");
        // Exécution de la requête avec les vraies données
        $stmt->execute([$ref, $cvPath]);
    }
}
