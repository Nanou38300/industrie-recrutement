<?php

//namespace App\Model;

//use PDO;

// class CandidatModel {

//     private PDO $db;

//     public function __construct() {
//         $this->db = new PDO($_ENV['DB_DSN'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
//     }

//     public function getProfil(int $id): array {
//         $stmt = $this->db->prepare("SELECT * FROM utilisateur WHERE id = ?");
//         $stmt->execute([$id]);
//         return $stmt->fetch(PDO::FETCH_ASSOC);
//     }

//     public function updateProfil(int $id, array $data): void {
//         $stmt = $this->db->prepare("UPDATE utilisateur SET nom = ?, email = ? WHERE id = ?");
//         $stmt->execute([$data['nom'], $data['email'], $id]);
//     }

//     public function getAnnonces(): array {
//         $stmt = $this->db->query("SELECT * FROM annonce WHERE active = 1");
//         return $stmt->fetchAll(PDO::FETCH_ASSOC);
//     }

//     public function getAnnonceById(int $id): array {
//         $stmt = $this->db->prepare("SELECT * FROM annonce WHERE id = ?");
//         $stmt->execute([$id]);
//         return $stmt->fetch(PDO::FETCH_ASSOC);
//     }

//     public function envoyerCandidature(int $candidatId, int $annonceId): void {
//         $stmt = $this->db->prepare("INSERT INTO candidature (id_candidat, id_annonce, date) VALUES (?, ?, NOW())");
//         $stmt->execute([$candidatId, $annonceId]);
//     }

//     public function getCandidatures(int $candidatId): array {
//         $stmt = $this->db->prepare("SELECT * FROM candidature WHERE id_candidat = ?");
//         $stmt->execute([$candidatId]);
//         return $stmt->fetchAll(PDO::FETCH_ASSOC);
//     }
// }





namespace App\Model;

class CandidatModel
{
    public function getProfil(int $idCandidat): array
    {
        // 🔧 Simulation ou requête BDD
        return [
            'nom' => 'Dupont',
            'prenom' => 'Magalie',
            'email' => 'magalie.dupont@example.com',
            'mot_de_passe' => 'formation',
            'téléphone' => '04.05.05.06.07',
            'ville' => "Saint vulbas",
        ];
    }
    public function createProfil(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO utilisateur (nom, email, mot_de_passe, role)
            VALUES (:nom, :email, :mot_de_passe, 'candidat')
        ");
        return $stmt->execute([
            'nom' => $data['nom'],
            'email' => $data['email'],
            'mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_DEFAULT)
        ]);
    }
    
    public function updateProfil(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE utilisateur SET nom = :nom, email = :email WHERE id = :id
        ");
        return $stmt->execute([
            'nom' => $data['nom'],
            'email' => $data['email'],
            'id' => $id
        ]);
    }
    
    public function deleteProfil(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM utilisateur WHERE id = :id AND role = 'candidat'");
        return $stmt->execute(['id' => $id]);
    }
    


    public function getAnnoncesDisponibles(): array
    {
        // 📢 Annonces ouvertes
        return [
            [
                'titre' => 'Chaudronnier H/F',
                'description' => 'Maintien d’une plateforme web en Symfony.',
                'lieu' => 'Lyon',
            ],
            [
                'titre' => 'Soudeur H/F',
                'description' => 'Intégration responsive avec Tailwind et React.',
                'lieu' => 'Clermont-Ferrand',
            ],
        ];
    }
    public function getUserById($id) {
        $db = Db::getInstance(); // Singleton
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function getCandidatures(int $idCandidat): array
    {
        // 📬 Candidatures du candidat
        return [
            [
                'poste' => 'Développeur PHP',
                'entreprise' => 'TechInnov',
                'reference' => '224653',
                'date de création' => '27/07/2025',
                'localisation' => 'St vulbas',
                'type de contrat' => 'CDI',
                'salaire' => '38-40 Ke',
                'statut' => 'En attente',
            ],
       
        ];
    }
}
