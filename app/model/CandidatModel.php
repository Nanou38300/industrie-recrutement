<?php

namespace App\Model;

use PDO;
use App\Database;

class CandidatModel
{
    private PDO $db;

    /** Cache des colonnes DESCRIBE par table (déclaré UNE SEULE FOIS) */
    private array $tableColumnsCache = [];

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    /* ===================== LECTURE ===================== */

    public function getProfil(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM utilisateur WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getAnnoncesDisponibles(): array
    {
        return $this->db->query("SELECT * FROM annonce")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCandidatures(int $idUtilisateur): array
    {
        $stmt = $this->db->prepare("
            SELECT
                a.titre,
                a.reference,
                a.date_publication,
                a.localisation,
                a.type_contrat,
                a.salaire,
                c.date_envoi AS date_postulation,
                c.statut
            FROM candidature c
            JOIN annonce a ON a.id = c.id_annonce
            WHERE c.id_utilisateur = :id_utilisateur
            ORDER BY c.date_envoi DESC
        ");
        $stmt->execute(['id_utilisateur' => $idUtilisateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ===================== HELPERS ===================== */

    /**
     * Retourne la liste des colonnes existantes pour une table.
     * Résultat mis en cache process.
     */
    private function getExistingColumns(string $table): array
    {
        if (isset($this->tableColumnsCache[$table])) {
            return $this->tableColumnsCache[$table];
        }
        $stmt = $this->db->prepare("DESCRIBE {$table}");
        $stmt->execute();
        $cols = $stmt->fetchAll(PDO::FETCH_COLUMN, 0) ?: [];
        return $this->tableColumnsCache[$table] = array_map('strval', $cols);
    }

    /* ===================== ÉCRITURE PROFIL ===================== */

    /**
     * Met à jour le profil :
     * - Mode ciblé: ['champ' => 'colonne', 'valeur' => '...'] (avec liste blanche + vérif colonne existante)
     * - Mode global: tableau de champs (tolérant si certaines colonnes n'existent pas en BDD)
     */
    public function updateProfil(int $id, array $data): bool
    {
        // --- Mode ciblé (champ + valeur)
        if (isset($data['champ'], $data['valeur'])) {
            $champ  = (string)$data['champ'];
            $valeur = (string)$data['valeur'];

            $allowed  = ['nom','prenom','email','telephone','ville','poste','linkedin','photo_profil','cv','date_cv'];
            $existing = $this->getExistingColumns('utilisateur');

            if (!in_array($champ, $allowed, true) || !in_array($champ, $existing, true)) {
                return false;
            }

            $sql = "UPDATE utilisateur SET {$champ} = :val WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['val' => $valeur, 'id' => $id]);
        }

        // --- Mode global (formulaire complet)
        $candidates = [
            'nom'        => $data['nom']        ?? null,
            'prenom'     => $data['prenom']     ?? null,
            'email'      => $data['email']      ?? null,
            'telephone'  => $data['telephone']  ?? null,
            'ville'      => $data['ville']      ?? null,
            'poste'      => $data['poste']      ?? null,
            'linkedin'   => $data['linkedin']   ?? null, // ignoré si colonne absente
        ];

        $existing = $this->getExistingColumns('utilisateur');
        $fields = [];
        $params = ['id' => $id];

        foreach ($candidates as $col => $val) {
            if (in_array($col, $existing, true)) {
                $fields[]     = "{$col} = :{$col}";
                $params[$col] = (string)($val ?? '');
            }
        }

        if (empty($fields)) {
            // rien à mettre à jour (ex: toutes colonnes manquantes)
            return false;
        }

        $sql = "UPDATE utilisateur SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
/** Récupère le nom de fichier CV actuel (ou null) */
public function getCurrentCV(int $id): ?string
{
    $stmt = $this->db->prepare("SELECT cv FROM utilisateur WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $cv = $stmt->fetchColumn();
    return $cv ? (string)$cv : null;
}

/** Récupère le chemin relatif de la photo actuelle (ou null) */
public function getCurrentPhoto(int $id): ?string
{
    $stmt = $this->db->prepare("SELECT photo_profil FROM utilisateur WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $p = $stmt->fetchColumn();
    return $p ? (string)$p : null;
}

/** Met à jour le CV (nom de fichier) + date_cv (si colonne présente) */
public function updateCV(int $id, string $filename): bool
{
    // stocker le nom de fichier (ex: 1730...-cv.pdf)
    $ok = $this->updateProfil($id, ['champ' => 'cv', 'valeur' => $filename]);
    // historiser la date si la colonne existe
    $this->updateProfil($id, ['champ' => 'date_cv', 'valeur' => date('Y-m-d H:i:s')]);
    return $ok;
}

/** Met à jour la photo de profil (chemin relatif 'uploads/...') */
public function updatePhoto(int $id, string $relativePath): bool
{
    return $this->updateProfil($id, ['champ' => 'photo_profil', 'valeur' => $relativePath]);
}

/** Supprime définitivement l'utilisateur */
public function deleteUtilisateur(int $id): bool
{
    $stmt = $this->db->prepare("DELETE FROM utilisateur WHERE id = :id");
    return $stmt->execute(['id' => $id]);
}

    /* ===================== CANDIDATURES ===================== */

    public function postuler(int $idUtilisateur, int $idAnnonce): bool
    {
        // Évite les doublons
        $check = $this->db->prepare("
            SELECT COUNT(*) FROM candidature
            WHERE id_utilisateur = :id_utilisateur AND id_annonce = :id_annonce
        ");
        $check->execute([
            'id_utilisateur' => $idUtilisateur,
            'id_annonce'     => $idAnnonce
        ]);
        if ($check->fetchColumn() > 0) {
            return false;
        }

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