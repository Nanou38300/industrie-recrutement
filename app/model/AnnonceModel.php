<?php

namespace App\Model;

use PDO;
use App\Database;

class AnnonceModel
{
    private PDO $db;
    private string $table = 'annonce';

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    // 📊 Compter toutes les annonces
    public function countAll(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table}");
        return (int) $stmt->fetchColumn();
    }

    // 🔍 Récupérer une annonce par ID
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // 📋 Récupérer toutes les annonces
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY date_publication DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ➕ Créer une annonce
    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (titre, description, date_publication, id_administrateur)
            VALUES (:titre, :description, NOW(), :id_admin)
        ");
        return $stmt->execute([
            'titre'       => $data['titre'],
            'description' => $data['description'],
            'id_admin'    => $data['id_administrateur']
        ]);
    }

    // ✏️ Modifier une annonce
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET titre = :titre, description = :description
            WHERE id = :id
        ");
        return $stmt->execute([
            'id'          => $id,
            'titre'       => $data['titre'],
            'description' => $data['description']
        ]);
    }

    // 📦 Archiver une annonce
    public function archive(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET archivee = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // 🔍 Récupérer les annonces d’un administrateur
    public function getByAdministrateur(int $idAdmin, ?string $statut = null): array
    {
        $sql = "SELECT * FROM annonce WHERE id_administrateur = :idAdmin";
        $params = ['idAdmin' => $idAdmin];
    
        if ($statut) {
            $sql .= " AND statut = :statut";
            $params['statut'] = $statut;
        }
    
        $sql .= " ORDER BY date_publication DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    public function getAnnoncesDisponibles(): array
    {
    $stmt = $this->db->query("SELECT * FROM annonce WHERE statut = 'active' ORDER BY date_publication DESC");
    return $stmt->fetchAll();
    }

    public function getStatsByAdmin(int $idAdmin): array
{
    $stmt = $this->db->prepare("
        SELECT titre,
               COUNT(c.id) AS total_candidatures,
               SUM(CASE WHEN c.statut = 'non_lue' THEN 1 ELSE 0 END) AS non_lues
        FROM annonce a
        LEFT JOIN candidature c ON c.id_annonce = a.id
        GROUP BY titre
    ");
    $stmt = $this->db->prepare("SELECT * FROM annonce WHERE id_administrateur = :id AND statut = :statut");
    $stmt->execute(['id' => $idAdmin, 'statut' => 'active']);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getAnnoncesAvecStats(int $idAdmin, int $limit = 4): array
{
    $stmt = $this->db->prepare("
        SELECT a.id, a.titre,
               COUNT(c.id) AS total_candidatures,
               SUM(CASE WHEN c.statut = 'non_lue' THEN 1 ELSE 0 END) AS non_lues
        FROM annonce a
        LEFT JOIN candidature c ON c.id_annonce = a.id
        WHERE a.id_administrateur = :idAdmin
        GROUP BY a.id, a.titre
        ORDER BY a.date_publication DESC
        LIMIT :limit
    ");
    $stmt->bindValue(':idAdmin', $idAdmin, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
