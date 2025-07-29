<?php


// namespace App\Model;

// use PDO;
// use App\Database;

// class UtilisateurModel
// {
//     private PDO $cnx;

//     public function __construct()
//     {
//         $database = new Database();
//         $this->cnx = $database->getConnection();
//     }

//     public function insertUtilisateur(string $nom, string $prenom, string $email, string $telephone, string $mot_de_passe): void
//     {
//         $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
//         $ins = $this->cnx->prepare("
//             INSERT INTO utilisateur (nom, prenom, email, telephone, mot_de_passe)
//             VALUES (?, ?, ?, ?, ?)
//         ");
//         $ins->execute([$nom, $prenom, $email, $telephone, $hash]);
//     }

//     public function selectUtilisateurs(): array
//     {
//         $req = $this->cnx->query("SELECT * FROM utilisateur");
//         return $req->fetchAll(PDO::FETCH_ASSOC);
//     }

//     public function selectUtilisateur(int $id): ?array
//     {
//         $req = $this->cnx->prepare("SELECT * FROM utilisateur WHERE id = ?");
//         $req->execute([$id]);
//         $utilisateur = $req->fetch(PDO::FETCH_ASSOC);
//         return $utilisateur ?: null;
//     }

//     public function loginUtilisateur(string $email): ?array
//     {
//         $req = $this->cnx->prepare("SELECT * FROM utilisateur WHERE email = ?");
//         $req->execute([$email]);
//         $utilisateur = $req->fetch(PDO::FETCH_ASSOC);
//         return $utilisateur ?: null;
//     }

//     public function logoutUtilisateur(): void
//     {
//         session_unset();
//         session_destroy();
//     }

//     public function updateUtilisateur(int $id, string $nom, string $prenom, string $email, string $telephone): void
//     {
//         $upd = $this->cnx->prepare("
//             UPDATE utilisateur
//             SET nom = ?, prenom = ?, email = ?, telephone = ?
//             WHERE id = ?
//         ");
//         $upd->execute([$nom, $prenom, $email, $telephone, $id]);
//     }

//     public function deleteUtilisateur(int $id): void
//     {
//         $del = $this->cnx->prepare("DELETE FROM utilisateur WHERE id = ?");
//         $del->execute([$id]);
//     }
// }


namespace App\Model;

use PDO;
use App\Database;

class UtilisateurModel
{
    private PDO $cnx;
    private string $table = 'utilisateur';

    public function __construct()
    {
        $database = new Database();
        $this->cnx = $database->getConnection();
    }

    public function insertUtilisateur(
        string $nom,
        string $prenom,
        string $email,
        string $mot_de_passe,
        string $date_naissance,
        int $telephone,

    ): void {
        $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $stmt = $this->cnx->prepare("
            INSERT INTO {$this->table} 
            (nom, prenom, email, mot_de_passe, date_naissance, telephone)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$nom, $prenom, $email, $hash, $date_naissance, $telephone]);
    }

    public function selectUtilisateurs(): array
    {
        $req = $this->cnx->query("SELECT * FROM {$this->table}");
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectUtilisateur(int $id): ?array
    {
        $req = $this->cnx->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $req->execute([$id]);
        $utilisateur = $req->fetch(PDO::FETCH_ASSOC);
        return $utilisateur ?: null;
    }

    public function loginUtilisateur(string $email): ?array
    {

        $sql = "SELECT id, nom, prenom, email, mot_de_passe, role FROM utilisateur WHERE email = :email";
        $req = $this->cnx->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $req->execute([$email]);
        return $req->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function logoutUtilisateur(): void
    {
        session_unset();
        session_destroy();
    }

    public function updateUtilisateur(int $id, string $nom, string $prenom, string $email, int $telephone): void
    {
        $upd = $this->cnx->prepare("
            UPDATE {$this->table}
            SET nom = ?, prenom = ?, email = ?, telephone = ?
            WHERE id = ?
        ");
        $upd->execute([$nom, $prenom, $email, $telephone, $id]);
    }

    public function deleteUtilisateur(int $id): void
    {
        $del = $this->cnx->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $del->execute([$id]);
    }
}

?> 
