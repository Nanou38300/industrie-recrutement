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
    public function getAllRdv(): array
    {
        $stmt = $this->db->query("SELECT id, objet AS title, CONCAT(date, 'T', heure) AS start FROM rdv");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getRdvById(int $id): array
    {
        $stmt = $this->db->prepare("SELECT * FROM rdv WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getCandidatById(int $id): array
    {
        $stmt = $this->db->prepare("SELECT * FROM candidats WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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

    public function updateStatutCandidature(int $id, string $statut): bool
    {
        $stmt = $this->db->prepare("
            UPDATE candidature SET statut = :statut WHERE id = :id
        ");
        return $stmt->execute([
            'statut' => $statut,
            'id'     => $id
        ]);
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

    public function renderListeCandidatures(array $candidatures): void
{
    foreach ($candidatures as $c) {
        echo "<div class='candidature-item'>";
        echo "<p><strong>Nom :</strong> {$c['nom']} {$c['prenom']}</p>";
        echo "<p><strong>Statut actuel :</strong> {$c['statut']}</p>";

        // 👉 Ton formulaire ici
        echo "<form method='POST' action='/candidature/update-statut'>";
        echo "<input type='hidden' name='id_candidature' value='{$c['id']}'>";
        echo "<select name='statut'>
                <option value='en_attente'>En attente</option>
                <option value='acceptee'>Acceptée</option>
                <option value='refusee'>Refusée</option>
              </select>";
        echo "<button type='submit'>Modifier</button>";
        echo "</form>";

        echo "</div><hr>";
    }
}
public function renderDetailsCandidature(array $candidature): void
{
    echo "<h2>Détail de la candidature</h2>";
    echo "<p><strong>Nom :</strong> {$candidature['nom']} {$candidature['prenom']}</p>";
    echo "<p><strong>Statut actuel :</strong> {$candidature['statut']}</p>";

    // 👉 Ton formulaire ici
    echo "<form method='POST' action='/candidature/update-statut'>";
    echo "<input type='hidden' name='id_candidature' value='{$candidature['id']}'>";
    echo "<select name='statut'>
            <option value='en_attente'>En attente</option>
            <option value='acceptee'>Acceptée</option>
            <option value='refusee'>Refusée</option>
          </select>";
    echo "<button type='submit'>Modifier</button>";
    echo "</form>";
}

}
