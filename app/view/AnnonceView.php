<?php

namespace App\View;

class AnnonceView
{
    // --------- Helpers sécurité / CSRF ---------

    private function safe($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }

    private function getCsrfToken(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    private function csrfField(): string
    {
        return "<input type='hidden' name='csrf_token' value='" . $this->safe($this->getCsrfToken()) . "'>";
    }

    // --------- Liste des annonces ---------

    public function renderListe(array $annonces): void
    {
        echo "<section class='annonces-front'>";
        echo "<h1>Nos offres d'emploi</h1>";

        if (empty($annonces)) {
            echo "<p>Aucune annonce disponible pour le moment.</p>";
            echo "<a href='index.php?action=annonce&step=create' class='btn btn-primary'>➕ Créer une annonce</a>";
            echo "</section>";
            return;
        }

        echo "<div class='annonces-list'>";

        foreach ($annonces as $a) {
            echo "<article class='annonce-item'>";
            echo "<h2>" . $this->safe($a['titre'] ?? '') . "</h2>";
            echo "<p><strong>Lieu :</strong> " . $this->safe($a['localisation'] ?? '') . "</p>";
            echo "<p><strong>Secteur :</strong> " . $this->safe($a['secteur_activite'] ?? '') . "</p>";
            echo "<p><strong>Type de contrat :</strong> " . $this->safe($a['type_contrat'] ?? '') . "</p>";
            echo "<p><strong>Référence :</strong> " . $this->safe($a['reference'] ?? '') . "</p>";

            echo "<p class='annonce-extrait'>"
                . nl2br($this->safe(mb_substr($a['description'] ?? '', 0, 200))) . "...</p>";

            echo "<div class='annonce-actions'>";
            echo "<a href='index.php?action=annonce&step=view&id=" . $this->safe($a['id'] ?? '') . "' class='btn btn-secondary'>Voir le détail</a>";
            echo "<a href='index.php?action=annonce&step=update&id=" . $this->safe($a['id'] ?? '') . "' class='btn btn-warning'>Modifier</a>";

            // Suppression via POST + CSRF
            echo "<form method='POST' action='index.php?action=annonce&step=delete' style='display:inline-block' 
                    onsubmit=\"return confirm('Supprimer cette annonce ?');\">";
            echo $this->csrfField();
            echo "<input type='hidden' name='id' value='" . $this->safe($a['id'] ?? '') . "'>";
            echo "<button type='submit' class='btn btn-danger'>Supprimer</button>";
            echo "</form>";

            echo "</div>";

            echo "</article>";
        }

        echo "</div>"; // .annonces-list
        echo "<div class='annonces-footer'>";
        echo "<a href='index.php?action=annonce&step=create' class='btn btn-primary'>➕ Créer une annonce</a>";
        echo "</div>";
        echo "</section>";
    }

    // --------- Détail d'une annonce ---------

    public function renderDetails(array $annonce): void
    {
        echo "<section class='annonce-detail'>";
        echo "<a href='index.php?action=annonce' class='btn btn-secondary'>&larr; Retour aux annonces</a>";

        echo "<h1>" . $this->safe($annonce['titre'] ?? '') . "</h1>";
        echo "<p><strong>Référence :</strong> " . $this->safe($annonce['reference'] ?? '') . "</p>";
        echo "<p><strong>Lieu :</strong> " . $this->safe($annonce['localisation'] ?? '') . "</p>";
        echo "<p><strong>Code postal :</strong> " . $this->safe($annonce['code_postale'] ?? '') . "</p>";
        echo "<p><strong>Secteur :</strong> " . $this->safe($annonce['secteur_activite'] ?? '') . "</p>";
        echo "<p><strong>Type de contrat :</strong> " . $this->safe($annonce['type_contrat'] ?? '') . "</p>";
        echo "<p><strong>Salaire :</strong> " . $this->safe($annonce['salaire'] ?? '') . "</p>";
        echo "<p><strong>Statut :</strong> " . $this->safe($annonce['statut'] ?? '') . "</p>";

        echo "<h2>Description</h2>";
        echo "<p>" . nl2br($this->safe($annonce['description'] ?? '')) . "</p>";

        echo "<h2>Mission</h2>";
        echo "<p>" . nl2br($this->safe($annonce['mission'] ?? '')) . "</p>";

        echo "<h2>Profil recherché</h2>";
        echo "<p>" . nl2br($this->safe($annonce['profil_recherche'] ?? '')) . "</p>";

        echo "<h2>Avantages</h2>";
        echo "<p>" . nl2br($this->safe($annonce['avantages'] ?? '')) . "</p>";

        echo "<div class='annonce-detail-actions'>";
        echo "<a href='index.php?action=annonce&step=update&id=" . $this->safe($annonce['id'] ?? '') . "' class='btn btn-warning'>Modifier</a>";

        // Archive
        echo "<form method='POST' action='index.php?action=annonce&step=archive' style='display:inline-block'
                onsubmit=\"return confirm('Archiver cette annonce ?');\">";
        echo $this->csrfField();
        echo "<input type='hidden' name='id' value='" . $this->safe($annonce['id'] ?? '') . "'>";
        echo "<button type='submit' class='btn btn-outline-secondary'>Archiver</button>";
        echo "</form>";

        // Activer
        echo "<form method='POST' action='index.php?action=annonce&step=activate' style='display:inline-block'
                onsubmit=\"return confirm('Activer cette annonce ?');\">";
        echo $this->csrfField();
        echo "<input type='hidden' name='id' value='" . $this->safe($annonce['id'] ?? '') . "'>";
        echo "<button type='submit' class='btn btn-success'>Activer</button>";
        echo "</form>";

        echo "</div>";

        echo "</section>";
    }

    // --------- Formulaire création / édition ---------

    /**
     * @param string $mode 'create' ou 'update'
     * @param array|null $annonce
     */
    public function renderForm(string $mode = 'create', ?array $annonce = null): void
    {
        $isUpdate = ($mode === 'update');

        $id            = $annonce['id'] ?? null;
        $titre         = $annonce['titre'] ?? '';
        $description   = $annonce['description'] ?? '';
        $mission       = $annonce['mission'] ?? '';
        $profil        = $annonce['profil_recherche'] ?? '';
        $localisation  = $annonce['localisation'] ?? '';
        $code_postale  = $annonce['code_postale'] ?? '';
        $secteur       = $annonce['secteur_activite'] ?? '';
        $type_contrat  = $annonce['type_contrat'] ?? 'CDI';
        $salaire       = $annonce['salaire'] ?? '';
        $statut        = $annonce['statut'] ?? 'activée';
        $avantages     = $annonce['avantages'] ?? '';

        $action = "index.php?action=annonce&step=" . ($isUpdate ? "update" : "create");

        echo "<section class='form-annonce-front'>";
        echo "<h1>" . ($isUpdate ? "Modifier l’annonce" : "Créer une annonce") . "</h1>";

        echo "<form method='POST' action='{$action}'>";
        echo $this->csrfField();

        if ($isUpdate && $id) {
            echo "<input type='hidden' name='id' value='" . $this->safe($id) . "'>";
        }

        echo "<label>Titre
                <input type='text' name='titre' value='" . $this->safe($titre) . "' required>
              </label>";

        echo "<label>Description
                <textarea name='description' rows='4' required>" . $this->safe($description) . "</textarea>
              </label>";

        echo "<label>Mission
                <textarea name='mission' rows='4' required>" . $this->safe($mission) . "</textarea>
              </label>";

        echo "<label>Profil recherché
                <textarea name='profil_recherche' rows='4' required>" . $this->safe($profil) . "</textarea>
              </label>";

        echo "<label>Localisation
                <input type='text' name='localisation' value='" . $this->safe($localisation) . "' required>
              </label>";

        echo "<label>Code postal
                <input type='text' name='code_postale' value='" . $this->safe($code_postale) . "' required>
              </label>";

        echo "<label>Secteur d'activité
                <input type='text' name='secteur_activite' value='" . $this->safe($secteur) . "' required>
              </label>";

        echo "<label>Type de contrat
                <select name='type_contrat' required>
                    <option value='CDI' " . ($type_contrat === 'CDI' ? 'selected' : '') . ">CDI</option>
                    <option value='CDD' " . ($type_contrat === 'CDD' ? 'selected' : '') . ">CDD</option>
                    <option value='Intérim' " . ($type_contrat === 'Intérim' ? 'selected' : '') . ">Intérim</option>
                </select>
              </label>";

        echo "<label>Salaire
                <input type='text' name='salaire' value='" . $this->safe($salaire) . "' required>
              </label>";

        echo "<label>Statut
                <select name='statut' required>
                    <option value='activée' " . ($statut === 'activée' ? 'selected' : '') . ">Activée</option>
                    <option value='brouillon' " . ($statut === 'brouillon' ? 'selected' : '') . ">Brouillon</option>
                    <option value='archivée' " . ($statut === 'archivée' ? 'selected' : '') . ">Archivée</option>
                </select>
              </label>";

        echo "<label>Avantages
                <textarea name='avantages' rows='3'>" . $this->safe($avantages) . "</textarea>
              </label>";

        echo "<div class='form-actions'>";
        echo "<button type='submit' class='btn btn-primary'>" .
                ($isUpdate ? "Enregistrer les modifications" : "Créer l’annonce") .
             "</button>";
        echo "<a href='index.php?action=annonce' class='btn btn-secondary'>Annuler</a>";
        echo "</div>";

        echo "</form>";
        echo "</section>";
    }
}