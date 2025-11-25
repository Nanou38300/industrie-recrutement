<?php

namespace App\Model;

use PDO;
use App\Database;

class CandidatureModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    // Crée une nouvelle candidature
    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO candidature (
                id_utilisateur, id_annonce, statut, date_envoi, commentaire_admin
            ) VALUES (
                :id_utilisateur, :id_annonce, 'envoyée', CURDATE(), :commentaire_admin
            )
        ");

        return $stmt->execute([
            'id_utilisateur'    => $data['id_utilisateur'],
            'id_annonce'        => $data['id_annonce'],
            'commentaire_admin' => $data['commentaire_admin'] ?? ''
        ]);
    }

    // Compte toutes les candidatures
    public function countAll(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM candidature");
        return (int) $stmt->fetchColumn();
    }

  // Récupère une candidature par son ID
public function findById(int $id): ?array
{
    $stmt = $this->db->prepare("
        SELECT 
            c.*,
            u.nom, u.prenom,
            u.cv      AS cv_filename,
            u.date_cv AS cv_uploaded_at,
            a.titre, a.reference
        FROM candidature c
        JOIN utilisateur u ON c.id_utilisateur = u.id
        JOIN annonce a ON c.id_annonce = a.id
        WHERE c.id = :id
    ");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

// Liste complète des candidatures (admin)
public function findAll(): array
{
    $stmt = $this->db->query("
        SELECT 
            c.*,
            u.nom, u.prenom,
            u.cv      AS cv_filename,
            u.date_cv AS cv_uploaded_at,
            a.titre, a.reference
        FROM candidature c
        JOIN utilisateur u ON c.id_utilisateur = u.id
        JOIN annonce a ON c.id_annonce = a.id
        ORDER BY c.date_envoi DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // Liste des candidatures d’un utilisateur (candidat)
    public function findByUtilisateur(int $idUtilisateur): array
    {
        $stmt = $this->db->prepare("
            SELECT c.statut, c.date_envoi, a.titre, a.reference, a.localisation,
                   a.type_contrat, a.salaire, a.date_publication
            FROM candidature c
            JOIN annonce a ON c.id_annonce = a.id
            WHERE c.id_utilisateur = :id
            ORDER BY c.date_envoi DESC
        ");
        $stmt->execute(['id' => $idUtilisateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✏️ Mise à jour du statut et commentaire
    public function update(int $id, array $data): bool
    {
        $data['statut'] = mb_strtolower(trim((string)($data['statut'] ?? ''))); // ← normalisation
    
        $statutsValides = ['envoyée', 'consultée', 'entretien', 'recruté', 'refusé'];
        if (!in_array($data['statut'], $statutsValides, true)) {
            return false;
        }
    
        $stmt = $this->db->prepare("
            UPDATE candidature
            SET statut = :statut, commentaire_admin = :commentaire_admin
            WHERE id = :id
        ");
    
        return $stmt->execute([
            'statut'            => $data['statut'],
            'commentaire_admin' => $data['commentaire_admin'] ?? '',
            'id'                => $id
        ]);
    }

    // Supprime une candidature
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM candidature WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
