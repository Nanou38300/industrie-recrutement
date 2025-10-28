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

    // 📅 Format FullCalendar enrichi
    public function getAllRdv(): array
    {
        $stmt = $this->db->query("
            SELECT e.id,
                   CONCAT(e.date_entretien, 'T', e.heure) AS start,
                   CONCAT(e.heure, ' - ', COALESCE(u.prenom, ''), ' ', COALESCE(u.nom, ''), ' - ', COALESCE(a.titre, '')) AS title,
                   CASE
                       WHEN e.type = 'Visio' THEN '#7F847D'
                       WHEN e.type = 'Présentiel' THEN '#C9AB89'
                       ELSE '#7F847D'
                   END AS color
            FROM entretien e
            LEFT JOIN utilisateur u ON u.id = e.id_utilisateur
            LEFT JOIN annonce a ON a.id = e.id_annonce
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $stmt = $this->db->prepare("
            SELECT 
                e.id,
                e.id_utilisateur,   
                e.id_annonce,
                e.date_entretien,
                e.heure,
                e.type,
                e.lien_visio,
                e.commentaire
            FROM entretien e
            WHERE e.id = :id
            LIMIT 1
        ");
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

    // ✏️ Met à jour un entretien existant
public function update(int $id, array $data): bool
{
    $stmt = $this->db->prepare("
        UPDATE entretien
        SET date_entretien = :date_entretien,
            heure = :heure,
            type = :type,
            lien_visio = :lien_visio,
            commentaire = :commentaire
        WHERE id = :id
    ");

    return $stmt->execute([
        'date_entretien' => $data['date_entretien'],
        'heure'          => $data['heure'],
        'type'           => $data['type'],
        'lien_visio'     => $data['lien_visio'],
        'commentaire'    => $data['commentaire'],
        'id'             => $id
    ]);
}

}
