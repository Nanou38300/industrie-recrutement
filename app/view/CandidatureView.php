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
                echo "<h3>" . $this->safe($c['prenom']) . " " . $this->safe($c['nom']) . "</h3>";
                echo "<p><strong>Poste :</strong> " . $this->safe($c['titre']) . "</p>";
                echo "<p><strong>Référence :</strong> " . $this->safe($c['reference']) . "</p>";
                echo "<p><strong>Date :</strong> " . $this->safe($c['date_envoi']) . "</p>";
                echo "<p><strong>Statut :</strong> " . $this->safe($c['statut']) . "</p>";
                echo "<form method='POST' action='/candidature/updateStatut'>
                    <input type='hidden' name='id' value='" . $this->safe($c['id']) . "'>
                    <select name='statut'>
                        <option value='Envoyée'>Envoyée</option>
                        <option value='Consultée'>Consultée</option>
                        <option value='Entretien'>Entretien</option>
                        <option value='Recruté'>Recruté</option>
                        <option value='Refusé'>Refusé</option>
                    </select>
                    <input type='text' name='commentaire_admin' placeholder='Commentaire'>
                    <button type='submit'>💾 Mettre à jour</button>
                </form>";
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
        echo "<p><strong>Date d’envoi :</strong> " . $this->safe($c['date_envoi']) . "</p>";
        echo "<p><strong>Statut :</strong> " . $this->safe($c['statut']) . "</p>";
        echo "<p><strong>Commentaire :</strong> " . $this->safe($c['commentaire_admin']) . "</p>";
        echo "</section><hr>";
    }

    // 📊 Suivi des candidatures (candidat)
    public function renderSuivi(array $candidatures): void
    {
        echo "<section class='suivi-candidatures'>";
        echo "<h2>📊 Suivi de mes candidatures</h2>";

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
