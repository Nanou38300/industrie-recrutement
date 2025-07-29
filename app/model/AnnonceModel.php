<?php
Namespace App\Model;
use PDO;
use App\Database; //
class AnnonceModel
{
    private PDO $cnx;
    private string $prefix = 'industrie';
    public function __construct()
    {
        $database = new Database();
        $this->cnx = $database->getConnection();
    }

    public function insertAnnonce( string $titre, string $description, $reference, string $mission, string $localisation, int $salaire, $statut, $avantages, int $code_postale, $type_contrat,int $duree_contrat, date $date_publication, $profil_recherche, $secteur_activite, int $id_administrateur): void

    {
        $hash = password_hash($password, PASSWORD_ARGON2I);
        $ins = $this->cnx->prepare("INSERT INTO {$this->prefix}annonce ( titre, description, reference, mission, localisation, salaire,  statut, avantages, code_postale, type_contrat, duree_contrat, date_publication, profil_recherche, secteur_activite, id_administrateur) VALUES (?, ?, ?, ?, ?, ?, ? , ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $ins->execute([$titre, $description, $reference, $mission, $localisation, $salaire, $statut, $avantages, $code_postale, $type_contrat, $duree_contrat, $date_publication, $profil_recherche, $secteur_activite, $id_administrateur]);
    }

//     public function selectUsers(): array
//     {
//         $req = $this->cnx->query("SELECT * FROM {$this->prefix}users");
//         return $req->fetchAll(PDO::FETCH_ASSOC);
//     }

//     public function selectUser(int $id): array
//     {
//         $req = $this->cnx->prepare("SELECT * FROM {$this->prefix}users WHERE id = ?");
//         $req->execute([$id]);
//         $user = $req->fetch(PDO::FETCH_ASSOC);
//         return $user ?: null;
//     }
//     public function loginUser(string $email, string $password): array
//     {
//         $req = $this->cnx->prepare("SELECT * FROM {$this->prefix}users WHERE email = ?");
//         $req->execute([$email]);
//         $user = $req->fetch();
//         if($user){
//             return $user;
//         } else {
//             $user = null;
//             return $user;
//         }
    
//     }
//     public function logoutUser(): void
//     {    
//         // Supprime les données de session
//         session_unset();
    
//         // Détruit la session
//         session_destroy();
//     }
    
//     public function updateUser(int $id, string $firstname, string $lastname, string $email, string $phone): void
//     {
//         $upd = $this->cnx->prepare(
//             "UPDATE {$this->prefix}users SET firstname = ?, lastname = ?, email = ?, phone = ? WHERE id = ?"
//         );
//         $upd->execute([$firstname, $lastname, $email, $phone, $id]);
//     }

//     public function deleteUser(int $id): void
//     {
//         $del = $this->cnx->prepare("DELETE FROM {$this->prefix}users WHERE id = ?");
//         $del->execute([$id]);
//     }
}
// 
?>
