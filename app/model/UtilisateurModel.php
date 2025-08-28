<?php

namespace App\Model;

use PDO;
use App\Database;

class UtilisateurModel
{
    private PDO $db;
    private string $table = 'utilisateur';

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    // ➕ Insérer un nouvel utilisateur
    public function insertUtilisateur(
        string $nom,
        string $prenom,
        string $email,
        string $mot_de_passe,
        string $date_naissance,
        int $telephone
    ): void {
        $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $role = (preg_match('/@cts\.fr$/', $email)) ? 'administrateur' : 'candidat';

        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (nom, prenom, email, mot_de_passe, date_naissance, telephone, role)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$nom, $prenom, $email, $hash, $date_naissance, $telephone, $role]);
    }

    // 📋 Liste des utilisateurs
    public function selectUtilisateurs(): array
    {
        $req = $this->db->query("SELECT * FROM {$this->table}");
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    // 👤 Sélection par ID
    public function selectUtilisateur(int $id): ?array
    {
        $req = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $req->execute([$id]);
        return $req->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // 🔐 Connexion utilisateur
    public function loginUtilisateur(string $email): ?array
    {
        $req = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $req->execute([$email]);
        return $req->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // 🔓 Déconnexion
    public function logoutUtilisateur(): void
    {
        session_unset();
        session_destroy();
    }

    // ✏️ Mise à jour simple
    public function updateUtilisateur(int $id, string $nom, string $prenom, string $email, int $telephone): void
    {
        $upd = $this->db->prepare("
            UPDATE {$this->table}
            SET nom = ?, prenom = ?, email = ?, telephone = ?
            WHERE id = ?
        ");
        $upd->execute([$nom, $prenom, $email, $telephone, $id]);
    }

    // 🗑️ Suppression
    public function deleteUtilisateur(int $id): bool
    {
        $del = $this->db->prepare("DELETE FROM utilisateur WHERE id = ?");
        return $del->execute([$id]);
    }
    

    // 🔍 Récupération par ID
    public function getById(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (\PDOException $e) {
            error_log("Erreur getById($id) : " . $e->getMessage());
            return null;
        }
    }

    // ✏️ Mise à jour complète du profil
    public function updateProfil(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} SET
                nom       = :nom,
                prenom    = :prenom,
                email     = :email,
                telephone = :telephone,
                poste     = :poste,
                ville     = :ville
            WHERE id = :id
        ");

        return $stmt->execute([
            'id'        => $id,
            'nom'       => $data['nom'] ?? '',
            'prenom'    => $data['prenom'] ?? '',
            'email'     => $data['email'] ?? '',
            'telephone' => $data['telephone'] ?? '',
            'poste'     => $data['poste'] ?? '',
            'ville'     => $data['ville'] ?? ''
        ]);
    }

    public function getAllCandidats(): array
{
    $stmt = $this->db->prepare("
        SELECT * FROM utilisateur
        WHERE role = 'candidat'
        ORDER BY nom ASC, prenom ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
