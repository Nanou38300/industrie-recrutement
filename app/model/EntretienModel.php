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
            INSERT INTO entretien (id_utilisateur, date_entretien, heure, type, lien_visio, rappel_envoye)
            VALUES (:id_utilisateur, :date_entretien, :heure, :type, :lien_visio, 0)
        ");
        return $stmt->execute([
            'id_utilisateur' => $data['id_utilisateur'],
            'date_entretien' => $data['date_entretien'],
            'heure'          => $data['heure'],
            'type'           => $data['type'],
            'lien_visio'     => $data['lien_visio'] ?? null
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
}
