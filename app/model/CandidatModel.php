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

    public function getCandidatures(int $id): array
    {
        $stmt = $this->db->prepare("SELECT * FROM candidature WHERE id_utilisateur = ?");
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

}
