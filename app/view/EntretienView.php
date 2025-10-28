<?php

namespace App\View;

class EntretienView
{
    private function safe(?string $value): string
    {
        return htmlspecialchars($value ?? '');
    }

    // Affiche le calendrier mensuel
    public function renderCalendrier(array $entretiens, string $mois, string $annee): void
    {
        echo "<section class='calendrier-admin'>";
        echo "<h2>Calendrier des entretiens - $mois/$annee</h2>";

        if (empty($entretiens)) {
            echo "<p>Aucun entretien prévu ce mois-ci.</p>";
        } else {
            foreach ($entretiens as $e) {
                echo "<div class='entretien-item'>";
                echo "<p><strong>Date :</strong> " . $this->safe($e['date_entretien']) . "</p>";
                echo "<p><strong>Heure :</strong> " . $this->safe($e['heure']) . "</p>";
                echo "<p><strong>Type :</strong> " . $this->safe($e['type']) . "</p>";
                echo "<form method='GET' action='/entretien/detail'>
                        <input type='hidden' name='id' value='" . $this->safe($e['id']) . "'>
                        <button type='submit'>Voir détail</button>
                      </form>";
                echo "</div><hr>";
            }
        }

        echo "</section>";
    }

    // Affiche les rappels du jour
    public function renderRappel(array $entretien): void
    {
        echo "<section class='rappel-entretien'>";
        echo "<h3>Rappel RDV</h3>";
        echo "<p><strong>Date :</strong> " . $this->safe($entretien['date_entretien']) . "</p>";
        echo "<p><strong>Heure :</strong> " . $this->safe($entretien['heure']) . "</p>";
        echo "<p><strong>Type :</strong> " . $this->safe($entretien['type']) . "</p>";
        echo "<p><strong>Visio :</strong> " . $this->safe($entretien['lien_visio']) . "</p>";
        echo "<form method='POST' action='/entretien/envoyer-rappel'>
                <input type='hidden' name='id' value='" . $this->safe($entretien['id']) . "'>
                <button type='submit'>Marquer comme envoyé</button>
              </form>";
        echo "</section><hr>";
    }

    // Formulaire de planification
    public function renderForm(array $utilisateurs): void
    {
        echo "<section class='form-entretien'>";
        echo "<h2>➕ Planifier un entretien</h2>";
        echo "<form method='POST' action='/entretien/planifier'>";

        echo "<label>Candidat :
                <select name='id_utilisateur' required>";
        foreach ($utilisateurs as $u) {
            echo "<option value='" . $this->safe($u['id']) . "'>" . $this->safe($u['prenom']) . " " . $this->safe($u['nom']) . "</option>";
        }
        echo "</select></label><br>";

        echo "<label>Date :
                <input type='date' name='date_entretien' required>
              </label><br>";

        echo "<label>Heure :
                <input type='time' name='heure' required>
              </label><br>";

        echo "<label>Type :
                <select name='type' required>
                    <option value='Visio'>Visio</option>
                    <option value='Présentiel'>Présentiel</option>
                </select>
              </label><br>";

        echo "<label>Lien Visio (si applicable) :
                <input type='text' name='lien_visio'>
              </label><br>";

        echo "<button type='submit'>Enregistrer</button>";
        echo "</form>";
        echo "</section><hr>";
    }

    // Détail d’un entretien + fiche candidat
    public function renderFicheEntretien(array $entretien, array $candidat): void
    {
        echo "<section class='fiche-entretien'>";
        echo "<h2>Détail de l'entretien</h2>";
        echo "<p><strong>Date :</strong> " . $this->safe($entretien['date_entretien']) . "</p>";
        echo "<p><strong>Heure :</strong> " . $this->safe($entretien['heure']) . "</p>";
        echo "<p><strong>Type :</strong> " . $this->safe($entretien['type']) . "</p>";
        echo "<p><strong>Lien Visio :</strong> " . $this->safe($entretien['lien_visio']) . "</p>";

        echo "<h3>Candidat</h3>";
        echo "<p><strong>Nom :</strong> " . $this->safe($candidat['prenom']) . " " . $this->safe($candidat['nom']) . "</p>";
        echo "<p><strong>Email :</strong> " . $this->safe($candidat['email']) . "</p>";
        echo "<p><strong>Téléphone :</strong> " . $this->safe($candidat['telephone']) . "</p>";
        echo "</section><hr>";
    }

    // Vue jour (optionnelle)
    public function renderJour(array $entretiens, string $date): void
    {
        echo "<section class='jour-entretien'>";
        echo "<h2Entretiens du " . $this->safe($date) . "</h2>";

        if (empty($entretiens)) {
            echo "<p>Aucun entretien prévu ce jour-là.</p>";
        } else {
            foreach ($entretiens as $e) {
                echo "<div class='entretien-item'>";
                echo "<p><strong>Heure :</strong> " . $this->safe($e['heure']) . "</p>";
                echo "<p><strong>Type :</strong> " . $this->safe($e['type']) . "</p>";
                echo "<form method='GET' action='/entretien/detail'>
                        <input type='hidden' name='id' value='" . $this->safe($e['id']) . "'>
                        <button type='submit'>Voir détail</button>
                      </form>";
                echo "</div><hr>";
            }
        }

        echo "</section>";
    }
}
