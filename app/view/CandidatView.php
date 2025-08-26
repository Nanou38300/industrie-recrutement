<?php

namespace App\View;

class CandidatView
{
    private function safe(?string $value): string
    {
        return htmlspecialchars($value ?? '');
    }

    public function renderDashboard(array $donnees): void
    {
        echo "<section class='dashboard'>";
        $this->renderProfil($donnees['profil']);
        $this->renderUploadForm();
        $this->renderEditForm($donnees['profil']);
        $this->renderDeleteButton();
        $this->renderAnnonces($donnees['annonces']);
        $this->renderSuiviCandidatures($donnees['candidatures']);
        echo "</section>";
    }

    public function renderProfil(array $profil): void
    {
        echo "<section class='profile-header'>";
        echo "<div class='photo'>";
        $photo = $this->safe($profil['photo_profil'] ?? 'assets/images/default.jpg');
        echo "<img src='/$photo' alt='Photo de profil' class='photo-profil'>";
        echo "</div>";

        echo "<div class='info'>";
        echo "<h2>" . $this->safe($profil['prenom']) . " " . $this->safe($profil['nom']) . "</h2>";
        echo "<p class='job-title'>" . $this->safe($profil['poste']) . "</p>";
        echo "</div>";
        echo "</section>";

        echo "<section class='details'>";
        echo "<div class='field'><label>Email</label><p>" . $this->safe($profil['email']) . "</p></div>";
        echo "<div class='field'><label>Téléphone</label><p>" . $this->safe($profil['telephone']) . "</p></div>";
        echo "<div class='field'><label>Ville</label><p>" . $this->safe($profil['ville']) . "</p></div>";
        echo "</section>";

        if (!empty($profil['cv'])) {
            echo "<section class='cv-section'>";
            echo "<div class='icon'>📄</div>";
            echo "<div class='cv-info'>";
            echo "<p>CV ajouté</p>";
            echo "<p class='date'>" . $this->safe($profil['date_cv'] ?? '') . "</p>";
            echo "</div>";
            echo "</section>";
        }
    }

    public function renderUploadForm(): void
    {
        echo "<section class='upload-cv'>";
        echo "<h2>📄 Télécharger mon CV</h2>";
        echo "<form method='POST' enctype='multipart/form-data' action='/candidat/upload-cv'>
                <input type='file' name='cv' accept='.pdf,.doc,.docx' required />
                <button type='submit'>Enregistrer</button>
            </form>";
        echo "</section><hr>";

        echo "<section class='upload-photo'>";
        echo "<h2>🖼️ Photo de profil</h2>";
        echo "<form method='POST' enctype='multipart/form-data' action='/candidat/uploadPhoto'>
                <input type='file' name='photo' accept='image/*' required />
                <button type='submit'>Envoyer</button>
            </form>";
        echo "</section><hr>";
    }

    public function renderEditForm(array $profil): void
    {
        echo "<section class='modifier-profil'>";
        echo "<h2>✏️ Modifier mes informations</h2>";
        echo "<form method='POST' action='/candidat/update'>
            <label>Nom : <input name='nom' value='" . $this->safe($profil['nom']) . "' /></label><br>
            <label>Prénom : <input name='prenom' value='" . $this->safe($profil['prenom']) . "' /></label><br>
            <label>Email : <input name='email' value='" . $this->safe($profil['email']) . "' /></label><br>
            <label>Téléphone : <input name='telephone' value='" . $this->safe($profil['telephone']) . "' /></label><br>
            <label>Ville : <input name='ville' value='" . $this->safe($profil['ville']) . "' /></label><br>
            <label>Poste : <input name='poste' value='" . $this->safe($profil['poste']) . "' /></label><br>
            <button type='submit'>💾 Enregistrer</button>
        </form>";
        echo "</section><hr>";
    }

    public function renderDeleteButton(): void
    {
        echo "<section class='supprimer-profil'>";
        echo "<form method='POST' action='/candidat/delete'>";
        echo "<button type='submit' onclick='return confirm(\"Supprimer mon profil ?\")'>🗑️ Supprimer mon compte</button>";
        echo "</form>";
        echo "</section><hr>";
    }

    public function renderAnnonces(array $annonces): void
    {
        
        if (isset($_SESSION['message'])) {
            echo "<div class='alert-success'>" . $_SESSION['message'] . "</div>";
            unset($_SESSION['message']);
        }
        echo "<section class='annonces'>";
        echo "<h2>📢 Annonces Disponibles</h2>";

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

                // Résumé cliquable
                echo "<div class='annonce-resume'>";
                    echo "<h3>" . $this->safe($a['titre'] ?? 'Titre non renseigné') . "</h3>";
                    echo "<p><strong>Lieu :</strong> " . $this->safe($a['localisation']) . " (" . $this->safe($a['code_postale']) . ")</p>";
                    echo "<p><strong>Type de contrat :</strong> " . $this->safe($a['type_contrat']) . "</p>";
                    echo "<p><strong>Salaire :</strong> " . $this->safe($a['salaire']) . "</p>";
                    echo "<p><strong>Date de publication :</strong> " . $this->safe($a['date_publication']) . "</p>";
                    echo "<p><strong>Référence :</strong> " . $this->safe($a['reference']) . "</p>";

                        echo "<button onclick='toggleDetails(this)' class='btn-toggle'>";
                        echo "<img class='img-deroulante' src='/assets/images/fleche-bas.png' alt='Voir les détails'>";
                        echo "</button>";
                        
                        echo "<form method='POST' action='/candidat/postuler?id=" . $this->safe($a['id']) . "'>";
                        echo "<button class='btn-offre' type='submit'>POSTULER</button>";
                        echo "</form>";
                        
          
                echo "</div>";

                // Bloc caché
                echo "<div class='annonce-details' style='display: none;'>";
                    echo "<p><strong>Description :</strong> " . $this->safe($a['description']) . "</p>";
                    echo "<p><strong>Missions :</strong> " . $this->safe($a['mission']) . "</p>";
                    echo "<p><strong>Profil recherché :</strong> " . $this->safe($a['profil_recherche']) . "</p>";
                    echo "<p><strong>Avantages :</strong> " . $this->safe($a['avantages']) . "</p>";
                    echo "<form method='POST' action='/candidat/postuler?id=" . $this->safe($a['id']) . "'>";

                    echo "</form>";
                echo "</div>";

                echo "</div><hr>";
            }
        }

        echo "</section>";
    }

    public function renderSuiviCandidatures(array $candidatures): void
    {
        echo "<section class='candidatures'>";
            echo "<h2>Suivi de mes candidatures</h2>";
            if (empty($candidatures)) {
                echo "<p>Aucune candidature envoyée.</p>";
            } else {
                foreach ($candidatures as $candidature) {
                    echo "<div class='candidature'>";
                        echo "<h3>" . $this->safe($candidature['titre'] ?? 'Sans titre') . " - " . $this->safe($candidature['reference'] ?? 'Réf. inconnue') . "</h3>";
                        echo "<p><strong>Date :</strong> " . $this->safe($candidature['date_publication'] ?? 'Non renseignée') . "</p>";
                        echo "<p><strong>Lieu :</strong> " . $this->safe($candidature['localisation'] ?? 'Non précisé') . "</p>";
                        echo "<p><strong>Contrat :</strong> " . $this->safe($candidature['type_contrat'] ?? 'Non précisé') . "</p>";
                        echo "<p><strong>Salaire :</strong> " . $this->safe($candidature['salaire'] ?? 'Non précisé') . "</p>";

                                
                    echo "<div class='suivi-candidature'>";
                    echo "<h4>SUIVI DE LA CANDIDATURE</h4>";
                    echo "<p>Le processus de recrutement prend entre 21 et 37 jours. Vous serez informé à chaque étape.</p>";
                    echo "<div class='timeline-wrapper'>";
                    echo "<div class='timeline-bar'></div>";
                    echo "<div class='timeline'>";
                    
                    $etapes = ['Envoyée', 'Consultée', 'Entretien', 'Réponse'];
                    $statutActuel = $candidature['statut'] ?? '';
                    
                    $reached = true;
                    foreach ($etapes as $etape) {
                        $class = '';
                        if ($etape === $statutActuel) {
                            $class = 'active';
                            $reached = false;
                        } elseif ($reached) {
                            $class = 'completed';
                        }
                        echo "<div class='etape $class'>$etape</div>";
                    }
                    
                    echo "</div>";
                    echo "</div>";
                    
                    echo "</div>";
                    echo "</div><hr>";
        }
    }
        echo "</section>";
    }
}
