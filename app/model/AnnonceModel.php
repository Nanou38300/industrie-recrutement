<?php

namespace App\Model;

use PDO;
use App\Database;


class AnnonceModel
{
    private PDO $db;
    private string $table = 'annonce';
    private const STATUT_ALLOWED = ['activée', 'brouillon', 'archivée'];


    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        // Recommandé : s'assurer d'avoir des exceptions PDO parlantes
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Normalise toute valeur de 'statut' en une des valeurs autorisées.
     * - Mappe les anciennes orthographes/accents/termes vers la norme
     * - Fallback sécurisé: 'draft'
     */
    private function normalizeStatut(?string $statut): string
    {
        $s = strtolower(trim((string)$statut));
    
        $map = [
            // → activée
            'activée'   => 'activée',
            'active'    => 'activée',
            'en_cours'  => 'activée',
    
            // → brouillon
            'brouillon' => 'brouillon',
            'inactive'  => 'brouillon',
            'suspendu'  => 'brouillon',
            'draft'     => 'brouillon',
    
            // → archivée
            'archivée'  => 'archivée',
            'archivee'  => 'archivée',
            'archived'  => 'archivée',
        ];
    
        $s = $map[$s] ?? $s;
        return in_array($s, self::STATUT_ALLOWED, true) ? $s : 'brouillon';
    }

    // ➕ Créer une annonce
    public function create(array $data): bool
    {
        $required = [
            'titre', 'description', 'mission', 'localisation', 'salaire', 'statut',
            'avantages', 'code_postale', 'type_contrat', 'profil_recherche',
            'secteur_activite', 'id_administrateur'
        ];

        foreach ($required as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }

        // Normalisation du statut (clé de la correction du warning 1265)
        $statut = $this->normalizeStatut($data['statut'] ?? null);

        $heure = date('H:i:s');

        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (
                titre, description, mission, localisation, salaire, statut,
                avantages, code_postale, type_contrat, profil_recherche,
                secteur_activite, date_publication, date_miseajour, heure, id_administrateur
            ) VALUES (
                :titre, :description, :mission, :localisation, :salaire, :statut,
                :avantages, :code_postale, :type_contrat, :profil_recherche,
                :secteur_activite, NOW(), CURRENT_TIMESTAMP, :heure, :id_administrateur
            )
        ");

        return $stmt->execute([
            'titre'             => $data['titre'],
            'description'       => $data['description'],
            'mission'           => $data['mission'],
            'localisation'      => $data['localisation'],
            'salaire'           => $data['salaire'],
            'statut'            => $statut,
            'avantages'         => $data['avantages'],
            'code_postale'      => $data['code_postale'],
            'type_contrat'      => $data['type_contrat'],
            'profil_recherche'  => $data['profil_recherche'],
            'secteur_activite'  => $data['secteur_activite'],
            'heure'             => $heure,
            'id_administrateur' => $data['id_administrateur']
        ]);
    }

    // ✏️ Modifier une annonce
    public function update(int $id, array $data): bool
    {
        // Normalise aussi en update (au cas où l’UI enverrait une valeur legacy)
        $statut = $this->normalizeStatut($data['statut'] ?? null);

        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET titre = :titre, description = :description, mission = :mission,
                localisation = :localisation, salaire = :salaire, statut = :statut,
                avantages = :avantages, code_postale = :code_postale, type_contrat = :type_contrat,
                profil_recherche = :profil_recherche, secteur_activite = :secteur_activite,
                date_miseajour = CURRENT_TIMESTAMP
            WHERE id = :id
        ");

        return $stmt->execute([
            'id'                => $id,
            'titre'             => $data['titre'],
            'description'       => $data['description'],
            'mission'           => $data['mission'],
            'localisation'      => $data['localisation'],
            'salaire'           => $data['salaire'],
            'statut'            => $statut,
            'avantages'         => $data['avantages'],
            'code_postale'      => $data['code_postale'],
            'type_contrat'      => $data['type_contrat'],
            'profil_recherche'  => $data['profil_recherche'],
            'secteur_activite'  => $data['secteur_activite']
        ]);
    }

    // 🗑️ Supprimer une annonce
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // 📦 Archiver une annonce (statut 'archived' aligné)
    public function archive(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET statut = 'archivée' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // 🔍 Récupérer une annonce par ID
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // 📋 Récupérer toutes les annonces
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY date_publication DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 📊 Compter toutes les annonces
    public function countAll(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table}");
        return (int) $stmt->fetchColumn();
    }

    // 🔍 Récupérer les annonces d’un administrateur
    public function getByAdministrateur(int $idAdmin, ?string $statut = null): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id_administrateur = :idAdmin";
        $params = ['idAdmin' => $idAdmin];
    
        if ($statut) {
            $sql .= " AND statut = :statut";
            $params['statut'] = $this->normalizeStatut($statut); // ← normalise
        }
    
        $sql .= " ORDER BY date_publication DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Alias existant dans ton contrôleur
    public function getByAdmin(int $idAdmin): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE id_administrateur = :idAdmin
            ORDER BY date_publication DESC
        ");
        $stmt->execute(['idAdmin' => $idAdmin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔍 Annonces disponibles (statut 'active' aligné)
    public function getAnnoncesDisponibles(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE statut = :s ORDER BY date_publication DESC");
        $stmt->execute(['s' => 'activée']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 📊 Statistiques par annonce
    public function getStatsByAdmin(int $idAdmin): array
    {
        $stmt = $this->db->prepare("
            SELECT a.titre,
                   COUNT(c.id) AS total_candidatures,
                   SUM(CASE WHEN c.statut = 'non_lue' THEN 1 ELSE 0 END) AS non_lues
            FROM {$this->table} a
            LEFT JOIN candidature c ON c.id_annonce = a.id
            WHERE a.id_administrateur = :idAdmin
            GROUP BY a.titre
        ");
        $stmt->execute(['idAdmin' => $idAdmin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 📊 Annonces avec stats (limitées)
    public function getAnnoncesAvecStats(int $idAdmin, int $limit = 4): array
    {
        $stmt = $this->db->prepare("
            SELECT a.id, a.titre,
                   COUNT(c.id) AS total_candidatures,
                   SUM(CASE WHEN c.statut = 'non_lue' THEN 1 ELSE 0 END) AS non_lues
            FROM {$this->table} a
            LEFT JOIN candidature c ON c.id_annonce = a.id
            WHERE a.id_administrateur = :idAdmin
            GROUP BY a.id, a.titre
            ORDER BY a.date_publication DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':idAdmin', $idAdmin, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}