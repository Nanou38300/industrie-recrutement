<?php

namespace App\Model;

use PDO;
use App\Database;

class CandidatModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function getProfil(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM utilisateur WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function updateProfil(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE utilisateur SET
                nom = :nom,
                prenom = :prenom,
                email = :email,
                telephone = :telephone,
                ville = :ville,
                poste = :poste
            WHERE id = :id
        ");

        return $stmt->execute([
            'id' => $id,
            'nom' => $data['nom'] ?? '',
            'prenom' => $data['prenom'] ?? '',
            'email' => $data['email'] ?? '',
            'telephone' => $data['telephone'] ?? '',
            'ville' => $data['ville'] ?? '',
            'poste' => $data['poste'] ?? ''
        ]);
    }

    public function updatePhoto(int $id, string $path): bool
    {
        $stmt = $this->db->prepare("UPDATE utilisateur SET photo_profil = :photo WHERE id = :id");
        return $stmt->execute(['photo' => $path, 'id' => $id]);
    }

// App/Model/CandidatModel.php
public function updateCV(int $id, string $filename): bool
{
    $stmt = $this->db->prepare("UPDATE utilisateur SET cv = :cv, date_cv = NOW() WHERE id = :id");
    return $stmt->execute(['cv' => $filename, 'id' => $id]);
}

    public function deleteProfil(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM utilisateur WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function getAnnoncesDisponibles(): array
    {
        return $this->db->query("SELECT * FROM annonce")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCandidatures(int $idUtilisateur): array
    {
        $stmt = $this->db->prepare("
            SELECT a.titre, a.reference, a.date_publication, a.localisation, a.type_contrat, a.salaire,
                   c.date_envoi, c.statut
            FROM candidature c
            JOIN annonce a ON a.id = c.id_annonce
            WHERE c.id_utilisateur = :id_utilisateur
            ORDER BY c.date_envoi DESC
        ");
        $stmt->execute(['id_utilisateur' => $idUtilisateur]);
        return $stmt->fetchAll();
    }
    
    
    
    public function envoyerCandidature(int $idUtilisateur, int $idAnnonce): bool
{
    $stmt = $this->db->prepare("
        INSERT INTO candidature (id_utilisateur, id_annonce, date_publication, statut)
        VALUES (:id_utilisateur, :id_annonce, NOW(), 'Envoyée')
    ");

    return $stmt->execute([
        'id_utilisateur' => $idUtilisateur,
        'id_annonce'     => $idAnnonce
    ]);
}

public function postuler(int $idUtilisateur, int $idAnnonce): bool
{
    // Vérifie si l'utilisateur a déjà postulé à cette annonce
    $check = $this->db->prepare("
        SELECT COUNT(*) FROM candidature
        WHERE id_utilisateur = :id_utilisateur AND id_annonce = :id_annonce
    ");
    $check->execute([
        'id_utilisateur' => $idUtilisateur,
        'id_annonce'     => $idAnnonce
    ]);

    if ($check->fetchColumn() > 0) {
        // Déjà postulé : ne pas dupliquer
        return false;
    }

    // Insère la nouvelle candidature
    $stmt = $this->db->prepare("
        INSERT INTO candidature (id_utilisateur, id_annonce)
        VALUES (:id_utilisateur, :id_annonce)
    ");

    return $stmt->execute([
        'id_utilisateur' => $idUtilisateur,
        'id_annonce'     => $idAnnonce
    ]);
}




}
