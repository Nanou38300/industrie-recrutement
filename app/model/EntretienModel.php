<?php

namespace App\Model;

use PDO;
use App\Database;

class EntretienModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    // ➕ Planifie un nouvel entretien
    public function create(array $data): bool
    {
$stmt = $this->db->prepare("
    INSERT INTO entretien (
        id_utilisateur, date_entretien, heure, type, lien_visio, commentaire
    ) VALUES (
        :id_utilisateur, :date_entretien, :heure, :type, :lien_visio, :commentaire
    )
");

return $stmt->execute([
    'id_utilisateur' => $data['id_utilisateur'],
    'date_entretien' => $data['date_entretien'],
    'heure'          => $data['heure'],
    'type'           => $data['type'],
    'lien_visio'     => $data['lien_visio'],
    'commentaire'    => $data['commentaire']
]);

        
    }
    

    // 📅 Récupère les entretiens d’un jour donné
    public function getByDate(string $date): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM entretien
            WHERE date_entretien = :date
            ORDER BY heure ASC
        ");
        $stmt->execute(['date' => $date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 📆 Récupère les entretiens d’une semaine
    public function getByWeek(string $semaine, string $annee): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM entretien
            WHERE WEEK(date_entretien, 1) = :semaine AND YEAR(date_entretien) = :annee
            ORDER BY date_entretien, heure
        ");
        $stmt->execute([
            'semaine' => $semaine,
            'annee'   => $annee
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🗓️ Récupère les entretiens d’un mois
    public function getByMonth(int $mois, int $annee): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM entretien
            WHERE MONTH(date_entretien) = :mois AND YEAR(date_entretien) = :annee
            ORDER BY date_entretien
        ");
        $stmt->execute([
            'mois'  => $mois,
            'annee'=> $annee
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 👨‍💼 Récupère les entretiens liés à un administrateur
    public function getByAdmin(int $idAdmin): array
    {
        $stmt = $this->db->prepare("
            SELECT e.date_entretien, e.heure, e.type, e.lien_visio,
                   u.nom, u.prenom,
                   a.titre AS poste
            FROM entretien e
            JOIN utilisateur u ON u.id = e.id_utilisateur
            JOIN annonce a ON a.id = e.id_annonce
            WHERE a.id_administrateur = :idAdmin
            ORDER BY e.date_entretien ASC, e.heure ASC
        ");
        $stmt->execute(['idAdmin' => $idAdmin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 📅 Récupère les entretiens d’un jour pour un administrateur
    public function getByDateAdmin(int $idAdmin, string $date): array
    {
        $stmt = $this->db->prepare("
            SELECT e.*, u.nom, u.prenom, a.titre AS poste, a.localisation AS lieu
            FROM entretien e
            JOIN utilisateur u ON u.id = e.id_utilisateur
            JOIN annonce a ON a.id = e.id_annonce
            WHERE a.id_administrateur = :idAdmin AND e.date_entretien = :date
            ORDER BY e.heure ASC
        ");
        $stmt->execute([
            'idAdmin' => $idAdmin,
            'date'    => $date
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔔 Rappels du jour
    public function getRemindersFor(string $date): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM entretien
            WHERE date_entretien = :date AND rappel_envoye = 0
            ORDER BY heure ASC
        ");
        $stmt->execute(['date' => $date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 📧 Marque un rappel comme envoyé
    public function envoyerRappel(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE entretien SET rappel_envoye = 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // 👁️ Détail d’un entretien
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM entretien WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // 📋 Liste complète des entretiens
    public function findAll(): array
    {
        $stmt = $this->db->query("
            SELECT * FROM entretien
            ORDER BY date_entretien DESC, heure ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🗑️ Supprime un entretien
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM entretien WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // 📆 Entretiens sur une plage hebdomadaire
    public function getEntretiensSemaine(int $idAdmin, string $dateDebut, string $dateFin): array
    {
        $stmt = $this->db->prepare("
            SELECT e.date_entretien, e.heure, e.type, u.nom, u.prenom, a.titre AS poste
            FROM entretien e
            JOIN utilisateur u ON u.id = e.id_utilisateur
            JOIN annonce a ON a.id = e.id_annonce
            WHERE a.id_administrateur = :idAdmin
              AND e.date_entretien BETWEEN :debut AND :fin
            ORDER BY e.date_entretien ASC, e.heure ASC
        ");
        $stmt->execute([
            'idAdmin' => $idAdmin,
            'debut'   => $dateDebut,
            'fin'     => $dateFin
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 📅 Format FullCalendar : tous les RDV
    public function getAllRdv(): array
    {
        $stmt = $this->db->query("
            SELECT e.id,
                   e.type AS title,
                   CONCAT(e.date_entretien, 'T', e.heure) AS start,
                   u.nom, u.prenom, e.lien_visio
            FROM entretien e
            JOIN utilisateur u ON u.id = e.id_utilisateur
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
}  
