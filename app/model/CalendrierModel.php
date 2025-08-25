<?php

namespace App\Model;

use PDO;
use App\Database;

class CalendrierModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    // 📅 Récupère les événements du mois
    public function getByMonth(int $mois, int $annee): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM entretien
            WHERE MONTH(date_entretien) = :mois AND YEAR(date_entretien) = :annee
            ORDER BY date_entretien, heure
        ");
        $stmt->execute([
            'mois'  => $mois,
            'annee'=> $annee
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 📆 Récupère les événements du jour
    public function getByDate(string $date): array
    {
        $stmt = $this->db->prepare("SELECT * FROM entretien WHERE date_entretien = :date ORDER BY heure ASC");
        $stmt->execute(['date' => $date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
