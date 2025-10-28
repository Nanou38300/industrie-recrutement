<?php

namespace App\View;

class CandidatureView
{
    private function safe(?string $value): string
    {
        return htmlspecialchars($value ?? '');
    }

    // 📋 Liste des candidatures (admin)
    public function renderListe(array $candidatures): void
    {
        echo "<section class='liste-candidatures'>";
        echo "<h2>📋 Toutes les candidatures</h2>";
    
        if (empty($candidatures)) {
            echo "<p>Aucune candidature enregistrée.</p>";
        } else {
            foreach ($candidatures as $c) {
                echo "<div class='candidature-item'>";
                echo "<h3>" . $this->safe($c['prenom'] ?? '') . " " . $this->safe($c['nom'] ?? '') . "</h3>";
                echo "<p><strong>Poste :</strong> " . $this->safe($c['titre'] ?? '') . "</p>";
                echo "<p><strong>Référence :</strong> " . $this->safe($c['reference'] ?? '') . "</p>";
                echo "<p><strong>Date :</strong> " . $this->safe($c['date_envoi'] ?? '') . "</p>";
                echo "<p><strong>Statut :</strong> " . $this->safe($c['statut'] ?? '') . "</p>";
    
                // 🔗 Lien vers le CV si dispo (priorité à l'alias cv_filename)
                $cvFile = $c['cv_filename'] ?? $c['cv'] ?? '';
                if ($cvFile !== '') {
                    $abs = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . basename($cvFile);
                    if (is_file($abs)) {
                        echo "<p><strong>CV :</strong> <a href='/uploads/" . $this->safe(basename($cvFile)) . "' target='_blank' rel='noopener'>Voir le CV</a></p>";
                    } else {
                        echo "<p><strong>CV :</strong> <em>Fichier introuvable : " . $this->safe($cvFile) . "</em></p>";
                    }
                }
    
                // 🔽 Select des statuts cohérents avec le Model (valeurs en minuscule)
                $statuts = ['envoyée','consultée','entretien','recruté','refusé'];
                $options = '';
                foreach ($statuts as $s) {
                    $selected = (isset($c['statut']) && $c['statut'] === $s) ? ' selected' : '';
                    $options .= "<option value='{$s}'{$selected}>" . ucfirst($s) . "</option>";
                }
    
                // 📝 Formulaire de mise à jour (tout à l’intérieur du <form>)
                echo "<form method='POST' action='/candidature/updateStatut'>
                        <input type='hidden' name='id' value='" . $this->safe((string)($c['id'] ?? '')) . "'>
                        <label style='display:block;margin:6px 0;'>
                            <span style='display:inline-block;width:120px;'>Nouveau statut :</span>
                            <select name='statut'>{$options}</select>
                        </label>
                        <label style='display:block;margin:6px 0;'>
                            <span style='display:inline-block;width:120px;'>Commentaire :</span>
                            <input type='text' name='commentaire_admin' value='" . $this->safe($c['commentaire_admin'] ?? '') . "' placeholder='Commentaire'>
                        </label>
                        <button type='submit'>💾 Mettre à jour</button>
                      </form>";
    
                // 💬 Ligne commentaire (lecture) sous la carte — toujours affichée
                $commentAffiche = trim((string)($c['commentaire_admin'] ?? '')) !== ''
                    ? $this->safe($c['commentaire_admin'])
                    : "<em>Aucun commentaire</em>";
                echo "<p class='commentaire-admin' style='margin-top:8px;'>💬 {$commentAffiche}</p>";
    
                echo "</div><hr>";
            }
        }
    
        echo "</section>";
    }

    // 👁️ Détail d’une candidature
    public function renderDetails(array $c): void
    {
        echo "<section class='details-candidature'>";
        echo "<h2>👁️ Détail de la candidature</h2>";
        echo "<p><strong>Nom :</strong> " . $this->safe($c['prenom']) . " " . $this->safe($c['nom']) . "</p>";
        echo "<p><strong>Poste :</strong> " . $this->safe($c['titre']) . "</p>";
        echo "<p><strong>Référence :</strong> " . $this->safe($c['reference']) . "</p>";
        if (!empty($c['cv'])) {
            echo "<p><strong>CV :</strong> <a href='/uploads/" . $this->safe($c['cv']) . "' target='_blank' rel='noopener'>Voir le CV</a></p>";
        }
        echo "<p><strong>Date d’envoi :</strong> " . $this->safe($c['date_envoi']) . "</p>";
        echo "<p><strong>Statut :</strong> " . $this->safe($c['statut']) . "</p>";
        echo "<p><strong>Commentaire :</strong> " . $this->safe($c['commentaire_admin']) . "</p>";
        echo "</section><hr>";
    }

    // 📊 Suivi des candidatures (candidat)
    public function renderSuivi(array $candidatures): void
    {
        echo "<section class='suivi-candidatures'>";
        echo "<h2>Mes candidatures</h2>";

        if (empty($candidatures)) {
            echo "<p>Aucune candidature envoyée.</p>";
        } else {
            foreach ($candidatures as $c) {
                echo "<div class='suivi-item'>";
                echo "<h3>" . $this->safe($c['titre']) . " - " . $this->safe($c['reference']) . "</h3>";
                echo "<p><strong>Date :</strong> " . $this->safe($c['date_publication']) . "</p>";
                echo "<p><strong>Lieu :</strong> " . $this->safe($c['localisation']) . "</p>";
                echo "<p><strong>Contrat :</strong> " . $this->safe($c['type_contrat']) . "</p>";
                echo "<p><strong>Salaire :</strong> " . $this->safe($c['salaire']) . "</p>";

                // Timeline visuelle
                $etapes = ['Envoyée', 'Consultée', 'Entretien', 'Réponse'];
                echo "<div class='timeline'>";
                foreach ($etapes as $etape) {
                    $active = ($etape === $c['statut']) ? 'active' : '';
                    echo "<span class='etape $active'>$etape</span>";
                }
                echo "</div>";
                echo "</div><hr>";
            }
        }

        echo "</section>";
    }
}
