<?php

namespace App\View;

class AdministrateurView
{
    // Fonction utilitaire pour sécuriser l'affichage HTML
    private function safe($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }

    // Génère / récupère le token CSRF
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

    // Champ hidden CSRF prêt à être injecté dans tous les formulaires POST
    private function csrfField(): string
    {
        return "<input type='hidden' name='csrf_token' value='" . $this->safe($this->getCsrfToken()) . "'>";
    }

    // Affiche les informations du profil administrateur
    public function renderProfil(array $profil): void
    {
        // Accepte soit un tableau plat, soit ['infos' => [...]]
        $data = isset($profil['infos']) && is_array($profil['infos']) ? $profil['infos'] : $profil;

        echo "<section class='profil-admin'>";
        echo "<h2>Mon Profil</h2>";

        // --- FLASH comme pour le candidat (one-shot) ---
        if (!empty($_SESSION['flash'])) {
            echo "<div class='flash success'>" . htmlspecialchars((string)$_SESSION['flash']) . "</div>";
            unset($_SESSION['flash']);
        }
        // ----------------------------------------------

        $champs = [
            'Nom'       => 'nom',
            'Prénom'    => 'prenom',
            'Email'     => 'email',
            'Téléphone' => 'telephone',
            'Poste'     => 'poste',
            'Ville'     => 'ville',
        ];

        foreach ($champs as $label => $key) {
            $valeur = $data[$key] ?? '';
            echo '<p class="profil-info">
                    <img loading="lazy" src="assets/images/valide.png" alt="valide" class="valide-icone">
                    <strong>' . $label . ' :</strong> ' . $this->safe($valeur) . '
                  </p>';
        }

        echo "<form method='GET' action='/administrateur/edit-profil' style='margin-top: 20px;'>";
        echo "<button type='submit' class='btn btn-primary'>Modifier mon profil</button>";
        echo "</form>";

        echo "</section><hr>";
    }


    // Affiche le formulaire de modification du profil administrateur
    public function renderFormProfil(array $profil): void
    {
        echo "<section class='form-profil-admin'>";
        echo "<h2>Modifier mon profil</h2>";

        // --- FLASH (mêmes classes que le candidat) ---
        if (!empty($_SESSION['flash'])) {
            echo "<div class='flash success'>" . $this->safe((string)$_SESSION['flash']) . "</div>";
            unset($_SESSION['flash']);
        }
        // --------------------------------------------

        echo "<form method='POST' action='/administrateur/edit-profil'>";
        echo $this->csrfField(); // CSRF ici

        // Génère les champs du formulaire à partir du tableau $profil
        $fields = ['nom', 'prenom', 'email', 'telephone', 'poste', 'ville'];
        foreach ($fields as $field) {
            $value = $this->safe($profil[$field] ?? '');
            $label = ucfirst($field);
            echo "<label>$label : <input type='text' name='$field' value='$value' required></label><br>";
        }

        echo "<button type='submit'>Enregistrer les modifications</button>";
        echo "</form>";
        echo "</section><hr>";

        // Bouton pour supprimer le compte administrateur
        echo "<section class='delete-profil'>";
        echo "<h3>Supprimer mon compte</h3>";
        echo "<form method='POST' action='/administrateur/delete-profil' onsubmit=\"return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')\">";
        echo $this->csrfField(); // CSRF ici aussi
        echo "<button type='submit' class='danger'>Supprimer mon profil</button>";
        echo "</form>";
        echo "</section><hr>";
    }



    // bouton creation d'une nouvelle annonce
    public function renderAnnonces(array $annonces): void
    {
        echo "<section class='annonces-admin'>";
        echo "<div class='bloc-annonces-admin'>"; // Bloc unique

        // Titre + bouton de création
        echo "<div class='header-annonces'>";
        echo "<h2>Gestion des annonces</h2>";
        if (!empty($_SESSION['flash'])) {
            echo "<div class='flash success'>" . htmlspecialchars((string)$_SESSION['flash']) . "</div>";
            unset($_SESSION['flash']);
        }
        echo "<form method='GET' action='/administrateur/create-annonce'>";
        echo "<button type='submit' class='btn-success'>➕ Créer une annonce</button>";
        echo "</form>";
        echo "</div>";

        // Filtres
        echo "<div class='filtres-annonces'>";
        echo "<form method='GET' action='/administrateur/annonces'>";
        echo "<label for='statut'>Filtrer par statut :</label>";
        echo "<select name='statut' id='statut' onchange='this.form.submit()'>";
        echo "<option value=''>Toutes</option>";
        echo "<option value='activée'>Activée</option>";
        echo "<option value='brouillon'>Brouillon</option>";
        echo "<option value='archivée'>Archivée</option>";
        echo "</select>";
        echo "</form>";
        echo "</div>";

        // Liste scrollable des annonces
        echo "<div class='bloc-annonces-scroll'>";

        if (empty($annonces)) {
            echo "<p>Aucune annonce disponible.</p>";
        } else {
            foreach ($annonces as $a) {
                echo "<div class='annonce-card'>";
                echo "<h3>" . $this->safe($a['titre'] ?? '') . "</h3>";
                echo "<p><strong>Référence :</strong> " . $this->safe($a['reference'] ?? '') . "</p>";
                echo "<p><strong>Date :</strong> " . $this->safe($a['date_publication'] ?? '') . "</p>";
                echo "<p><strong>Lieu :</strong> " . $this->safe($a['localisation'] ?? '') . "</p>";
                echo "<p><strong>Secteur :</strong> " . $this->safe($a['secteur_activite'] ?? '') . "</p>";
                echo "<p><strong>Description :</strong> " . substr($this->safe($a['description'] ?? ''), 0, 100) . "...</p>";

                // Modifier
                echo "<div class='btns-card'>";
                echo "<form method='GET' action='/administrateur/edit-annonce' class='form-edit'>";
                echo "<input type='hidden' name='id' value='" . $this->safe($a['id'] ?? '') . "'>";
                echo "<button type='submit' class='btn-edit'>
                        <img loading='lazy' src='assets/images/stylo.png' alt='modifier' class='icon-edit'>
                        Modifier
                      </button>";
                echo "</form>";

                // Supprimer
                echo "<form method='POST' action='/administrateur/delete-annonce' 
                        onsubmit=\"return confirm('Supprimer cette annonce ?')\" 
                        class='form-delete'>";

                echo $this->csrfField(); // CSRF ici
                echo "<input type='hidden' name='id' value='" . $this->safe($a['id'] ?? '') . "'>";

                echo "<button type='submit' class='btn-delete'>
                        <img loading='lazy' src='assets/images/poubelle.png' alt='supprimer' class='icon-delete'>
                        Supprimer
                    </button>";
                
                echo "</form>";
                echo "</div>";               
                echo "</div><hr>";
            }
        }

        echo "</div>"; // fin scroll
        echo "</div>"; // fin bloc-annonces-admin
        echo "</section><hr>";
    }



    // formulaire de creation / modification d'annonce 
    public function renderFormAnnonce(array $annonce = [], string $mode = 'create'): void
    {
        $isUpdate = ($mode === 'update');

        // Action du formulaire selon le mode
        $idAnnonce = $annonce['id'] ?? null;
        $action = $isUpdate
            ? "/administrateur/edit-annonce?id=" . htmlspecialchars((string)$idAnnonce)
            : "/administrateur/create-annonce";

        // Champs (pré-remplis en update)
        $titre            = $annonce['titre'] ?? '';
        $description      = $annonce['description'] ?? '';
        $mission          = $annonce['mission'] ?? '';
        $localisation     = $annonce['localisation'] ?? '';
        $salaire          = $annonce['salaire'] ?? '';
        $statut           = $annonce['statut'] ?? 'active'; // normalise tes statuts
        $avantages        = $annonce['avantages'] ?? '';
        $code_postale     = $annonce['code_postale'] ?? '';
        $type_contrat     = $annonce['type_contrat'] ?? 'CDI';
        $duree_contrat    = $annonce['duree_contrat'] ?? '';
        $profil_recherche = $annonce['profil_recherche'] ?? '';
        $secteur_activite = $annonce['secteur_activite'] ?? '';
        $idAdmin          = $_SESSION['utilisateur']['id'] ?? '';

        echo "<section class='form-container'>";
        echo "<h2>" . ($isUpdate ? "Modifier l’annonce" : "Nouvelle annonce") . "</h2>";

        echo "<form class='form-annonce' method='POST' action='{$action}'>";
        echo $this->csrfField(); // CSRF ici

        // Sécurité et cohérence : garder l'id en hidden en update
        if ($isUpdate && $idAnnonce) {
            echo "<input type='hidden' name='id' value='" . htmlspecialchars((string)$idAnnonce) . "'>";
        }

        echo "<div class='form-group'><label>Titre</label><input type='text' name='titre' value='" . htmlspecialchars($titre) . "' required></div>";

        echo "<div class='form-group'><label>Description</label><textarea name='description' rows='3' required>" . htmlspecialchars($description) . "</textarea></div>";

        echo "<div class='form-group'><label>Mission</label><textarea name='mission' rows='3' required>" . htmlspecialchars($mission) . "</textarea></div>";

        echo "<div class='form-row'>";
        echo "<div class='form-group'><label>Localisation</label><input type='text' name='localisation' value='" . htmlspecialchars($localisation) . "' required></div>";
        echo "<div class='form-group'><label>Code postal</label><input type='number' name='code_postale' value='" . htmlspecialchars($code_postale) . "' required></div>";
        echo "</div>";

        echo "<div class='form-row'>";
        echo "<div class='form-group'><label>Salaire (€)</label><input type='text' name='salaire' value='" . htmlspecialchars($salaire) . "' required></div>";
        echo "<div class='form-group'><label>Statut</label>
                <select name='statut' required>
                    <option value='activée' " . ($statut === 'activée' ? 'selected' : '') . ">Activée</option>
                    <option value='brouillon' " . ($statut === 'brouillon' ? 'selected' : '') . ">Brouillon</option>
                    <option value='archivée' " . ($statut === 'archivée' ? 'selected' : '') . ">Archivée</option>
                </select>
              </div>";
        echo "</div>";

        echo "<div class='form-group'><label>Avantages</label><textarea name='avantages' rows='3' required>" . htmlspecialchars($avantages) . "</textarea></div>";

        echo "<div class='form-row'>";
        echo "<div class='form-group'><label>Type de contrat</label>
                        <select name='type_contrat' required>
                            <option value='CDI' " . ($type_contrat === 'CDI' ? 'selected' : '') . ">CDI</option>
                            <option value='CDD' " . ($type_contrat === 'CDD' ? 'selected' : '') . ">CDD</option>
                            <option value='Intérim' " . ($type_contrat === 'Intérim' ? 'selected' : '') . ">Intérim</option>
                        </select>
                    </div>";
        echo "<div class='form-group'><label>Durée du contrat (mois)</label><input type='number' name='duree_contrat' value='" . htmlspecialchars($duree_contrat) . "'></div>";
        echo "</div>";

        echo "<div class='form-group'><label>Profil recherché</label><textarea name='profil_recherche' rows='3' required>" . htmlspecialchars($profil_recherche) . "</textarea></div>";

        echo "<div class='form-group'><label>Secteur d'activité</label><textarea name='secteur_activite' rows='3' required>" . htmlspecialchars($secteur_activite) . "</textarea></div>";

        echo "<input type='hidden' name='id_administrateur' value='" . htmlspecialchars((string)$idAdmin) . "'>";

        echo "<div class='form-actions'>
                <button type='submit' class='btn-annonce'>" . ($isUpdate ? "Enregistrer les modifications" : "Publier l’annonce") . "</button>
              </div>";

        echo "</form></section>";
    }
    
    
    public function renderListeCandidatures(array $candidatures): void
    {
        echo "<section class='candidatures-admin'>";
        echo "<div class='bloc-candidatures-admin'>";

        echo "<h2>Candidatures reçues</h2>";

        if (!empty($_SESSION['flash'])) {
            $type = $_SESSION['flash_type'] ?? 'success';
            echo "<div class='flash {$type}'>" . $this->safe((string)$_SESSION['flash']) . "</div>";
            unset($_SESSION['flash'], $_SESSION['flash_type']);
        }

        if (empty($candidatures)) {
            echo "<p>Aucune candidature enregistrée.</p>";
        } else {
            foreach ($candidatures as $c) {
                echo "<div class='candidature-item'>";

                // Infos candidat
                echo "<h3>" . $this->safe($c['prenom'] ?? '') . " " . $this->safe($c['nom'] ?? '') . "</h3>";
                echo "<p><strong>Poste :</strong> " . $this->safe($c['titre'] ?? '') . "</p>";
                echo "<p><strong>Référence :</strong> " . $this->safe($c['reference'] ?? '') . "</p>";
                echo "<p><strong>Statut :</strong> " . $this->safe($c['statut'] ?? '') . "</p>";

                // ---- CV (alias prioritaire cv_filename, fallback cv) ----
                $cvFile = $c['cv_filename'] ?? $c['cv'] ?? '';
                if ($cvFile !== '') {
                    $abs = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/') . '/uploads/' . basename($cvFile);
                    if (is_file($abs)) {
                        echo "<p><strong>CV :</strong> <a href='/uploads/" . $this->safe(basename($cvFile)) . "' target='_blank' rel='noopener'>Voir le CV</a></p>";
                    } else {
                        echo "<p><strong>CV :</strong> <em>Fichier introuvable : " . $this->safe($cvFile) . "</em></p>";
                    }
                }
                // --------------------------------------------------------

                // Options de statut (valeurs en minuscule, comme en BDD)
                $statuts = ['envoyée','consultée','entretien','recruté','refusé'];
                $options = '';
                foreach ($statuts as $s) {
                    $selected = (isset($c['statut']) && $c['statut'] === $s) ? ' selected' : '';
                    $options .= "<option value='{$s}'{$selected}>" . ucfirst($s) . "</option>";
                }

                // Formulaire de mise à jour
                echo "<form method='POST' action='/candidature/updateStatut'>
                        " . $this->csrfField() . " 
                        <input type='hidden' name='id' value='" . $this->safe((string)($c['id'] ?? '')) . "'>
                        <label>
                            <span>Nouveau statut :</span>
                            <select name='statut'>{$options}</select>
                        </label>
                        <label>
                            <span style='display:inline-block;width:130px;'>Commentaire :</span>
                            <input type='text' name='commentaire_admin' value='" . $this->safe($c['commentaire_admin'] ?? '') . "' placeholder='Commentaire'>
                        </label>
                        <button type='submit'>Mettre à jour</button>
                      </form>";

                // Ligne commentaire (lecture) toujours affichée
                $commentAffiche = trim((string)($c['commentaire_admin'] ?? '')) !== ''
                    ? $this->safe($c['commentaire_admin'])
                    : "<em>Aucun commentaire</em>";
                echo "<p class='commentaire-admin'>💬 {$commentAffiche}</p>";

                echo "</div><hr>";
            }
        }

        echo "</div>";
        echo "</section>";
    }


    // Détail d’une candidature
    public function renderDetailsCandidature(array $c): void
    {
        echo "<section class='details-candidature'>";
        echo "<div class='bloc-details-candidature'>"; // 🔹 Bloc unique
    
        // Titre principal
        echo "<h2>Détail de la candidature</h2>";
    
        // Informations du candidat
        echo "<p><strong>Nom :</strong> " . $this->safe($c['prenom'] ?? '') . " " . $this->safe($c['nom'] ?? '') . "</p>";
        echo "<p><strong>Poste :</strong> " . $this->safe($c['titre'] ?? '') . "</p>";
        echo "<p><strong>Référence :</strong> " . $this->safe($c['reference'] ?? '') . "</p>";
        echo "<p><strong>Date d’envoi :</strong> " . $this->safe($c['date_envoi'] ?? '') . "</p>";
        echo "<p><strong>Statut :</strong> " . $this->safe($c['statut'] ?? '') . "</p>";
        echo "<p><strong>Commentaire :</strong> " . $this->safe($c['commentaire_admin'] ?? '') . "</p>";
    
        echo "</div>"; // fin bloc-details-candidature
        echo "</section><hr>";
    }
}