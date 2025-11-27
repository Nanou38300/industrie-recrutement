<?php

namespace App\View;

class CandidatView
{
    /**
     * Sécurise une valeur pour l'affichage HTML.
     */
    private function safe($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Génère / récupère le token CSRF
     */
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

    /**
     * Champ hidden CSRF réutilisable
     */
    private function csrfField(): string
    {
        return "<input type='hidden' name='csrf_token' value='" . $this->safe($this->getCsrfToken()) . "'>";
    }

    /**
     * Affichage du profil (lecture seule) + boutons
     */
    public function renderProfil(array $profil): void
    {
        $data  = $profil;
        $photo = $this->safe($data['photo_profil'] ?? 'assets/images/default.jpg');

        echo "<section class='profil-candidat'>";

        if (!empty($_SESSION['flash'])) {
            echo "<div class='flash success'>" . $this->safe((string)$_SESSION['flash']) . "</div>";
            unset($_SESSION['flash']);
        }

        echo "<h2>Mon Profil</h2>";

        // === Photo + bouton modifier === 
        echo '<div class="photo-profil">';
        echo '    <img loading="lazy" src="/' . ltrim($photo, '/') . '" alt="Photo de profil du candidat" class="photo-candidat">';
        echo '    <form method="POST" enctype="multipart/form-data" action="/candidat/uploadPhoto">';
        echo          $this->csrfField();
        echo '        <input 
                            type="file" 
                            name="photo" 
                            accept=".jpg,.jpeg,.png,.gif,.webp" 
                            required
                        >';
        echo '        <button type="submit">Modifier la photo</button>';
        echo '    </form>';
        echo '</div>';

        // === Infos personnelles ===
        $champs = [
            'Nom'        => 'nom',
            'Prénom'     => 'prenom',
            'Email'      => 'email',
            'Téléphone'  => 'telephone',
            'Poste'      => 'poste',
            'Ville'      => 'ville',
        ];

        foreach ($champs as $label => $key) {
            $val = $this->safe($data[$key] ?? '');
            echo "<p class='profil-info'>
                    <img loading='lazy' src='/assets/images/valide.png' alt='' class='valide-icone'>
                    <strong>{$label} :</strong> {$val}
                  </p>";
        }

        // === LinkedIn ===
        if (!empty($data['linkedin'])) {
            $lnk = $this->safe($data['linkedin']);
            echo "<p class='profil-info'>
                    <img loading='lazy' src='/assets/images/valide.png' alt='' class='valide-icone'>
                    <strong>LinkedIn :</strong> <a href='{$lnk}' target='_blank'>{$lnk}</a>
                  </p>";
        }

        // === CV + bouton upload ===
        echo "<div class='cv-section'>";
        $cvFile = $data['cv'] ?? '';
        if ($cvFile !== '') {
            echo "<p>📄 <a href='/uploads/" . $this->safe(basename($cvFile)) . "' target='_blank'>Voir mon CV</a></p>";
        } else {
            echo "<p><em>Aucun CV enregistré</em></p>";
        }
        echo "  <form method='POST' enctype='multipart/form-data' action='/candidat/upload-cv'>
                    " . $this->csrfField() . "
                    <input type='file' name='cv' accept='.pdf,.doc,.docx' required>
                    <button type='submit'>📎 Mettre à jour le CV</button>
                </form>";
        echo "</div>";

        // === Boutons d’action ===
        echo "<div class='profil-actions'>";

        echo "<form method='GET' action='/candidat/edit-profil'>
                <button type='submit' class='btn-primary'>
                    ✏️ Modifier mes informations
                </button>
              </form>";

        echo "<form method='POST' action='/candidat/delete' onsubmit=\"return confirm('Supprimer mon profil ? Cette action est irréversible.')\">
                " . $this->csrfField() . "
                <button type='submit' class='btn-danger'>
                    🗑️ Supprimer mon compte
                </button>
              </form>";

        echo "</div>";

        echo "</section><hr>";
    }

    /**
     * Alias pour compatibilité
     */
    public function renderProfilCandidat(array $profil): void
    {
        $this->renderProfil($profil);
    }

    /**
     * Formulaire global pour modifier le profil
     */
    public function renderFormProfilCandidat(array $profil): void
    {
        echo "<section class='form-profil-candidat'>";
        echo "<h2>Modifier mon profil</h2>";

        if (!empty($_SESSION['flash'])) {
            echo "<div class='flash'>" . $this->safe((string)$_SESSION['flash']) . "</div>";
            unset($_SESSION['flash']);
        }

        echo "<form method='POST' action='/candidat/edit-profil' class='profile-form' novalidate>";
        echo $this->csrfField();

        // nom
        echo "<div class='form-group'>";
        echo "  <label for='f-nom'>Nom</label>";
        echo "  <input id='f-nom' class='input' type='text' name='nom' value='" . $this->safe($profil['nom'] ?? '') . "' required>";
        echo "</div>";

        // prenom
        echo "<div class='form-group'>";
        echo "  <label for='f-prenom'>Prénom</label>";
        echo "  <input id='f-prenom' class='input' type='text' name='prenom' value='" . $this->safe($profil['prenom'] ?? '') . "' required>";
        echo "</div>";

        // email
        echo "<div class='form-group'>";
        echo "  <label for='f-email'>Email</label>";
        echo "  <input id='f-email' class='input' type='email' name='email' value='" . $this->safe($profil['email'] ?? '') . "' required>";
        echo "  <span class='help'>Ex : prenom.nom@exemple.com</span>";
        echo "</div>";

        // telephone
        echo "<div class='form-group'>";
        echo "  <label for='f-telephone'>Téléphone</label>";
        echo "  <input id='f-telephone' class='input' type='tel' name='telephone' value='" . $this->safe($profil['telephone'] ?? '') . "' required>";
        echo "  <span class='help'>Ex : 06 12 34 56 78</span>";
        echo "</div>";

        // poste
        echo "<div class='form-group form-group--full'>";
        echo "  <label for='f-poste'>Poste</label>";
        echo "  <input id='f-poste' class='input' type='text' name='poste' value='" . $this->safe($profil['poste'] ?? '') . "' required>";
        echo "</div>";

        // ville
        echo "<div class='form-group'>";
        echo "  <label for='f-ville'>Ville</label>";
        echo "  <input id='f-ville' class='input' type='text' name='ville' value='" . $this->safe($profil['ville'] ?? '') . "' required>";
        echo "</div>";

        // linkedin
        echo "<div class='form-group'>";
        echo "  <label for='f-linkedin'>LinkedIn (facultatif)</label>";
        echo "  <input id='f-linkedin' class='input' type='url' name='linkedin' value='" . $this->safe($profil['linkedin'] ?? '') . "' placeholder='https://www.linkedin.com/in/votre-profil'>";
        echo "</div>";

        // actions
        echo "<div class='form-actions form-group--full'>";
        echo "  <button type='submit' class='btn-primary'>Enregistrer les modifications</button>";
        echo "  <a href='/candidat/profil' class='btn-secondary' role='button'>Annuler</a>";
        echo "</div>";

        echo "</form>";
        echo "</section><hr>";
    }

    /**
     * Section CV (non utilisée directement mais OK)
     */
    private function renderCVSection(array $profil): void
    {
        echo "<section class='cv-section'>";

        $cvFile = $profil['cv'] ?? '';

        echo "<div class='icon'>📄</div>";
        echo "<div class='cv-info'>";

        if ($cvFile !== '') {
            $absRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
            $abs     = $absRoot . '/uploads/' . basename($cvFile);

            if ($absRoot && is_file($abs)) {
                echo "<p>CV ajouté</p>";
                echo "<p class='date'>" . $this->safe($profil['date_cv'] ?? '') . "</p>";
                echo "<p><a href='/uploads/" . $this->safe(basename($cvFile)) . "' target='_blank' rel='noopener'>Voir mon CV</a></p>";
            } else {
                echo "<p><strong>CV :</strong> <em>fichier introuvable : " . $this->safe($cvFile) . "</em></p>";
            }
        } else {
            echo "<p><em>Aucun CV enregistré</em></p>";
        }

        echo "</div>";
        echo "</section>";
    }

    /**
     * Uploads : CV + Photo (formulaires standalone)
     */
    public function renderUploadForm(): void
    {
        echo "<section class='upload-cv'>";
        echo "<h2>Télécharger mon CV</h2>";
        echo "<form method='POST' enctype='multipart/form-data' action='/candidat/upload-cv'>";
        echo $this->csrfField();
        echo "    <input type='file' name='cv' accept='.pdf,.doc,.docx' required />";
        echo "    <button type='submit'>Enregistrer</button>";
        echo "</form>";
        echo "</section><hr>";

        echo "<section class='upload-photo'>";
        echo "<h2>Photo de profil</h2>";
        echo "<form method='POST' enctype='multipart/form-data' action='/candidat/uploadPhoto'>";
        echo $this->csrfField();
        echo "    <input type='file' name='photo' accept='image/*' required />";
        echo "    <button type='submit'>Envoyer</button>";
        echo "</form>";
        echo "</section><hr>";
    }

    /**
     * Bouton suppression de compte
     */
    public function renderDeleteButton(): void
    {
        echo "<section class='supprimer-profil'>";
        echo "<h3>Supprimer mon compte</h3>";
        echo "<form method='POST' action='/candidat/delete' onsubmit=\"return confirm('Supprimer mon profil ? Cette action est irréversible.')\">";
        echo $this->csrfField();
        echo "    <button type='submit' class='danger'>🗑️ Supprimer mon compte</button>";
        echo "</form>";
        echo "</section><hr>";
    }

    /**
     * Liste des annonces
     */
    public function renderAnnonces(array $annonces): void
    {
        if (isset($_SESSION['popup'])) {
            $popup = $_SESSION['popup'];
            echo "<div class='popup-overlay'>
                    <div class='popup-message'>
                        <p>" . $this->safe($popup['message'] ?? '') . "</p>
                        <a href='" . $this->safe($popup['retour'] ?? '/') . "' class='btn-retour'>↩️ Retour aux annonces</a>
                    </div>
                  </div>";
            unset($_SESSION['popup']);
        }

        echo "<section class='annonces'>";
        echo "<h2>Les emplois disponibles</h2>";

        echo "<script>
                function toggleDetails(button) {
                    const details = button.parentElement.nextElementSibling;
                    details.style.display = details.style.display === 'block' ? 'none' : 'block';
                }
            </script>";

        if (empty($annonces)) {
            echo "<p>Aucune annonce disponible pour le moment.</p>";
        } else {
            foreach ($annonces as $a) {
                echo "<div class='annonce-wrapper'>";

                echo "<div class='annonce-resume'>";
                echo "<h3>" . $this->safe($a['titre'] ?? 'Titre non renseigné') . "</h3>";
                echo "<p><strong>Lieu :</strong> " . $this->safe($a['localisation'] ?? '') . " (" . $this->safe($a['code_postale'] ?? '') . ")</p>";
                echo "<p><strong>Type de contrat :</strong> " . $this->safe($a['type_contrat'] ?? '') . "</p>";
                echo "<p><strong>Salaire :</strong> " . $this->safe($a['salaire'] ?? '') . "</p>";
                echo "<p><strong>Date de publication :</strong> " . $this->safe($a['date_publication'] ?? '') . "</p>";

                echo "<button onclick='toggleDetails(this)' class='btn-toggle'>
                        <img loading='lazy' class='img-deroulante' src='/assets/images/fleche-bas.png' alt='Voir les détails'>
                      </button>";

                // POSTULER → POST + CSRF
                echo "<form method='POST' action='/candidat/postuler?id=" . $this->safe((string)($a['id'] ?? '')) . "'>";
                echo $this->csrfField();
                echo "    <button class='btn-offre' type='submit'>POSTULER</button>";
                echo "</form>";

                echo "</div>";

                echo "<div class='annonce-details' style='display: none;'>";
                echo "<p><strong>Description :</strong> " . $this->safe($a['description'] ?? '') . "</p>";
                echo "<p><strong>Missions :</strong> " . $this->safe($a['mission'] ?? '') . "</p>";
                echo "<p><strong>Profil recherché :</strong> " . $this->safe($a['profil_recherche'] ?? '') . "</p>";
                echo "<p><strong>Avantages :</strong> " . $this->safe($a['avantages'] ?? '') . "</p>";
                echo "</div>";

                echo "</div><hr>";
            }
        }

        echo "</section>";
    }

    /**
     * Suivi des candidatures (timeline)
     */
    public function renderSuiviCandidatures(array $candidatures): void
    {
        echo "<section class='candidatures'>";
        echo "<h2>LE SUIVI DE MES CANDIDATURES</h2>";

        if (empty($candidatures)) {
            echo "<p>Aucune candidature envoyée.</p>";
        } else {
            foreach ($candidatures as $candidature) {
                echo "<div class='candidature'>";
                echo "<h3>" . $this->safe($candidature['titre'] ?? 'Sans titre') . " - " . $this->safe($candidature['reference'] ?? 'Réf. inconnue') . "</h3>";
                echo "<p><strong>Date de publication :</strong> " . $this->safe($candidature['date_publication'] ?? 'Non renseignée') . "</p>";
                echo "<p><strong>Lieu :</strong> " . $this->safe($candidature['localisation'] ?? 'Non précisé') . "</p>";
                echo "<p><strong>Type de contrat :</strong> " . $this->safe($candidature['type_contrat'] ?? 'Non précisé') . "</p>";
                echo "<p><strong>Salaire :</strong> " . $this->safe($candidature['salaire'] ?? 'Non précisé') . "</p>";
                echo "<p><strong>Date de candidature :</strong> " . $this->safe($candidature['date_postulation'] ?? 'Non renseignée') . "</p>";

                echo "<div class='suivi-candidature'>";
                echo "<h4>LES ÉTAPES DE RECRUTEMENT</h4>";
                echo "<p>Suivez l'évolution de votre candidature. Le processus de recrutement prend entre 21 et 37 jours.</p>";
                echo "<div class='timeline-wrapper'>";
                echo "<div class='timeline-bar'></div>";
                echo "<div class='timeline'>";

                $etapes       = ['envoyée', 'consultée', 'entretien', 'recruté', 'refusé'];
                $statutActuel = mb_strtolower(trim((string)($candidature['statut'] ?? '')));
                $phase        = 'completed';

                foreach ($etapes as $etape) {
                    if ($etape === $statutActuel) {
                        $class = 'active';
                        $phase = 'pending';
                    } else {
                        $class = ($phase === 'completed') ? 'completed' : 'pending';
                    }
                    echo "<div class='etape {$class}'>" . $this->safe(ucfirst($etape)) . "</div>";
                }

                echo "</div>"; // .timeline
                echo "</div>"; // .timeline-wrapper
                echo "</div>"; // .suivi-candidature

                echo "</div><hr>";
            }
        }

        echo "</section>";
    }
}
