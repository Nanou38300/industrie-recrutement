<?php
namespace App\Model;

use App\Database;

class AdministrateurModel
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // 🧑‍💼 Récupérer les infos du profil administrateur
    public function getProfil(int $id): array
    {
        $stmt = $this->db->prepare("SELECT * FROM utilisateur WHERE id = :id AND role = 'administrateur'");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
    }

    // 🛠 Modifier les infos du profil administrateur
    public function updateProfil(array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE utilisateur SET 
                nom = :nom,
                prenom = :prenom,
                email = :email,
                telephone = :telephone
            WHERE id = :id AND role = 'administrateur'
        ");
        return $stmt->execute([
            'nom'       => $data['nom'],
            'prenom'    => $data['prenom'],
            'email'     => $data['email'],
            'telephone' => $data['telephone'],
            'id'        => $data['id']
        ]);
    }

    // 📊 Statistiques : total des candidatures
    public function countCandidatures(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM candidature");
        return (int) $stmt->fetchColumn();
    }

    // 📊 Statistiques : total des annonces
    public function countAnnonces(): int
    {
        $stmt = $this->query("SELECT COUNT(*) FROM annonce");
        return (int) $stmt->fetchColumn();
    }

    // 📊 Statistiques : total des utilisateurs
    public function countUtilisateurs(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM utilisateur");
        return (int) $stmt->fetchColumn();
    }
}
