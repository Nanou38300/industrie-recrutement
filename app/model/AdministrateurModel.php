<?php

namespace App\Model;

use PDO;

class AdministrateurModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // 🔹 Récupère les infos personnelles de l'administrateur
    public function getInfosAdmin(int $id): array
    {
        $stmt = $this->db->prepare("SELECT * FROM administrateur WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 Statistiques par poste : candidatures totales et non lues
    public function getStatsAnnonces(int $idAdmin): array
    {
        $stmt = $this->db->prepare("
            SELECT a.poste,
                   COUNT(c.id) AS total,
                   SUM(CASE WHEN c.statut = 'non_lue' THEN 1 ELSE 0 END) AS non_lues
            FROM annonce a
            LEFT JOIN candidature c ON c.id_annonce = a.id
            WHERE a.id_administrateur = ?
            GROUP BY a.poste
        ");
        $stmt->execute([$idAdmin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Statistiques globales des candidatures
    public function getStatsCandidatures(int $idAdmin): array
    {
        $stmt = $this->db->prepare("
            SELECT
                SUM(CASE WHEN statut = 'non_lue' THEN 1 ELSE 0 END) AS non_lues,
                SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) AS en_attente,
                SUM(CASE WHEN statut = 'traitee' THEN 1 ELSE 0 END) AS traitees
            FROM candidature
            WHERE id_annonce IN (
                SELECT id FROM annonce WHERE id_administrateur = ?
            )
        ");
        $stmt->execute([$idAdmin]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 Liste des candidatures liées à l'administrateur
    public function getToutesCandidatures(int $idAdmin): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, u.nom, u.prenom, a.poste
            FROM candidature c
            JOIN utilisateur u ON u.id = c.id_utilisateur
            JOIN annonce a ON a.id = c.id_annonce
            WHERE a.id_administrateur = ?
            ORDER BY c.date_publication DESC
        ");
        $stmt->execute([$idAdmin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Liste des entretiens planifiés
    public function getCalendrierAdmin(int $idAdmin): array
    {
        $stmt = $this->db->prepare("
            SELECT e.date_entretien, e.heure, u.nom, u.prenom, a.poste
            FROM entretien e
            JOIN utilisateur u ON u.id = e.id_utilisateur
            JOIN annonce a ON a.id = e.id_annonce
            WHERE a.id_administrateur = ?
            ORDER BY e.date_entretien ASC
        ");
        $stmt->execute([$idAdmin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Détail d’un entretien par jour
    public function getEntretiensParJour(int $idAdmin, string $date): array
    {
        $stmt = $this->db->prepare("
            SELECT e.*, u.nom, u.prenom, a.poste
            FROM entretien e
            JOIN utilisateur u ON u.id = e.id_utilisateur
            JOIN annonce a ON a.id = e.id_annonce
            WHERE a.id_administrateur = ? AND e.date_entretien = ?
        ");
        $stmt->execute([$idAdmin, $date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
