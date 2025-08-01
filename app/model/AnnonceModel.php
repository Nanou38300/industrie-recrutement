<?php
namespace App\Model;

use PDO;
use PDOException;

class AnnonceModel {
    private PDO $pdo;
    private string $table = 'annonces';

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Récupère toutes les annonces
     * @return array
     */
    public function getAll(): array {
        try {
            $sql = "SELECT * FROM {$this->table} ORDER BY date_publication DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des annonces : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère une annonce par son ID
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de l'annonce ID $id : " . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupère les annonces par statut
     * @param string $statut
     * @return array
     */
    public function getByStatus(string $statut): array {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE statut = :statut ORDER BY date_publication DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':statut', $statut, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des annonces par statut : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Recherche d'annonces par mots-clés
     * @param string $keyword
     * @return array
     */
    public function search(string $keyword): array {
        try {
            $keyword = '%' . $keyword . '%';
            $sql = "SELECT * FROM {$this->table} 
                   WHERE titre LIKE :keyword 
                   OR description LIKE :keyword 
                   OR localisation LIKE :keyword
                   OR secteur_activite LIKE :keyword
                   ORDER BY date_publication DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':keyword', $keyword, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Crée une nouvelle annonce
     * @param array $data
     * @return int|false ID de l'annonce créée ou false en cas d'erreur
     */
    public function create(array $data): int|false {
        try {
            // Validation des données requises
            $requiredFields = ['titre', 'description', 'profil_recherche', 
                             'localisation', 'code_postale', 'secteur_activite', 
                             'type_contrat', 'date_publication', 'statut', 'id_administrateur'];
            
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new \InvalidArgumentException("Le champ '$field' est requis");
                }
            }

            $sql = "INSERT INTO {$this->table} (
                        titre, description, profil_recherche,
                        secteur_activite, localisation, code_postale,
                        salaire, avantages, type_contrat, duree_contrat,
                        reference, date_publication, statut, id_administrateur
                    ) VALUES (
                        :titre, :description, :profil_recherche,
                        :secteur_activite, :localisation, :code_postale,
                        :salaire, :avantages, :type_contrat, :duree_contrat,
                        :reference, :date_publication, :statut, :id_administrateur
                    )";

            $stmt = $this->pdo->prepare($sql);
            
            // Binding des paramètres
            $stmt->bindParam(':titre', $data['titre'], PDO::PARAM_STR);
            $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
            $stmt->bindParam(':profil_recherche', $data['profil_recherche'], PDO::PARAM_STR);
            $stmt->bindParam(':secteur_activite', $data['secteur_activite'], PDO::PARAM_STR);
            $stmt->bindParam(':localisation', $data['localisation'], PDO::PARAM_STR);
            $stmt->bindParam(':code_postale', $data['code_postale'], PDO::PARAM_STR);
            $stmt->bindParam(':salaire', $data['salaire'], PDO::PARAM_STR);
            $stmt->bindParam(':avantages', $data['avantages'], PDO::PARAM_STR);
            $stmt->bindParam(':type_contrat', $data['type_contrat'], PDO::PARAM_STR);
            $stmt->bindParam(':duree_contrat', $data['duree_contrat'], PDO::PARAM_STR);
            $stmt->bindParam(':reference', $data['reference'], PDO::PARAM_STR);
            $stmt->bindParam(':date_publication', $data['date_publication'], PDO::PARAM_STR);
            $stmt->bindParam(':statut', $data['statut'], PDO::PARAM_STR);
            $stmt->bindParam(':id_administrateur', $data['id_administrateur'], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return (int)$this->pdo->lastInsertId();
            }
            return false;

        } catch (PDOException $e) {
            error_log("Erreur lors de la création de l'annonce : " . $e->getMessage());
            return false;
        } catch (\InvalidArgumentException $e) {
            error_log("Données invalides : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Met à jour une annonce existante
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool {
        try {
            // Vérifier que l'annonce existe
            if (!$this->getById($id)) {
                return false;
            }

            $sql = "UPDATE {$this->table} SET 
                        titre = :titre,
                        description = :description,
                        profil_recherche = :profil_recherche,
                        secteur_activite = :secteur_activite,
                        localisation = :localisation,
                        code_postale = :code_postale,
                        salaire = :salaire,
                        avantages = :avantages,
                        type_contrat = :type_contrat,
                        duree_contrat = :duree_contrat,
                        reference = :reference,
                        date_publication = :date_publication,
                        statut = :statut,
                        id_administrateur = :id_administrateur
                    WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            
            // Binding des paramètres
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':titre', $data['titre'], PDO::PARAM_STR);
            $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
            $stmt->bindParam(':profil_recherche', $data['profil_recherche'], PDO::PARAM_STR);
            $stmt->bindParam(':secteur_activite', $data['secteur_activite'], PDO::PARAM_STR);
            $stmt->bindParam(':localisation', $data['localisation'], PDO::PARAM_STR);
            $stmt->bindParam(':code_postale', $data['code_postale'], PDO::PARAM_STR);
            $stmt->bindParam(':salaire', $data['salaire'], PDO::PARAM_STR);
            $stmt->bindParam(':avantages', $data['avantages'], PDO::PARAM_STR);
            $stmt->bindParam(':type_contrat', $data['type_contrat'], PDO::PARAM_STR);
            $stmt->bindParam(':duree_contrat', $data['duree_contrat'], PDO::PARAM_STR);
            $stmt->bindParam(':reference', $data['reference'], PDO::PARAM_STR);
            $stmt->bindParam(':date_publication', $data['date_publication'], PDO::PARAM_STR);
            $stmt->bindParam(':statut', $data['statut'], PDO::PARAM_STR);
            $stmt->bindParam(':id_administrateur', $data['id_administrateur'], PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de l'annonce ID $id : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprime une annonce
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool {
        try {
            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression de l'annonce ID $id : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Compte le nombre total d'annonces
     * @return int
     */
    public function count(): int {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table}";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (PDOException $e) {
            error_log("Erreur lors du comptage des annonces : " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Récupère les annonces avec pagination
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getPaginated(int $limit = 10, int $offset = 0): array {
        try {
            $sql = "SELECT * FROM {$this->table} 
                   ORDER BY date_publication DESC 
                   LIMIT :limit OFFSET :offset";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération paginée : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère les annonces d'un administrateur
     * @param int $adminId
     * @return array
     */
    public function getByAdministrateur(int $adminId): array {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id_administrateur = :admin_id ORDER BY date_publication DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':admin_id', $adminId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des annonces de l'admin $adminId : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Archive une annonce (change le statut en 'archivee')
     * @param int $id
     * @return bool
     */
    public function archive(int $id): bool {
        try {
            $sql = "UPDATE {$this->table} SET statut = 'archivee' WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de l'archivage de l'annonce ID $id : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Active une annonce (change le statut en 'active')
     * @param int $id
     * @return bool
     */
    public function activate(int $id): bool {
        try {
            $sql = "UPDATE {$this->table} SET statut = 'active' WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de l'activation de l'annonce ID $id : " . $e->getMessage());
            return false;
        }
    }
}