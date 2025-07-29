<?php
namespace App\Model;

use App\Database;
use PDO;

class AnnonceModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // 🔍 Récupérer toutes les annonces
    public function getAll(): array
    {
        $sql = "SELECT * FROM annonces WHERE archive = 0 ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 📄 Trouver une annonce par ID
    public function find(int $id): array
    {
        $sql = "SELECT * FROM annonces WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    // 📝 Créer une annonce
    public function create(array $data): void
    {
        $sql = "INSERT INTO annonces (titre, description, lieu, contrat, salaire, created_at)
                VALUES (:titre, :description, :lieu, :contrat, :salaire, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'titre' => $data['titre'],
            'description' => $data['description'],
            'lieu' => $data['lieu'],
            'contrat' => $data['contrat'],
            'salaire' => $data['salaire']
        ]);
    }

    // 🛠️ Mettre à jour une annonce existante
    public function update(int $id, array $data): void
    {
        $sql = "UPDATE annonces SET 
                    titre = :titre,
                    description = :description,
                    lieu = :lieu,
                    contrat = :contrat,
                    salaire = :salaire,
                    updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'titre' => $data['titre'],
            'description' => $data['description'],
            'lieu' => $data['lieu'],
            'contrat' => $data['contrat'],
            'salaire' => $data['salaire']
        ]);
    }

    // 🗃️ Archiver une annonce
    public function archive(int $id): void
    {
        $sql = "UPDATE annonces SET archive = 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    // 📊 Compter le nombre d’annonces actives
    public function countAnnonces(): int
    {
        $sql = "SELECT COUNT(*) FROM annonces WHERE archive = 0";
        $stmt = $this->db->query($sql);
        return (int) $stmt->fetchColumn();
    }
}
