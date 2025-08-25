<?php

namespace App\View;

class CalendrierView
{
    private function safe(?string $value): string
    {
        return htmlspecialchars($value ?? '');
    }
  
    // 🗓️ Vue mensuelle
    public function renderSemaine(array $rendezVous, string $semaine, string $annee): void
    {
        echo "<section class='calendrier-semaine'>";
        echo "<h2>📅 Semaine $semaine - $annee</h2>";

        if (empty($rendezVous)) {
            echo "<p>Aucun rendez-vous cette semaine.</p>";
        } else {
            foreach ($rendezVous as $rdv) {
                echo "<div class='rdv-item'>";
                echo "<p><strong>Date :</strong> " . $this->safe($rdv['date_entretien']) . "</p>";
                echo "<p><strong>Heure :</strong> " . $this->safe($rdv['heure']) . "</p>";
                echo "<p><strong>Type :</strong> " . $this->safe($rdv['type']) . "</p>";
                echo "<form method='GET' action='/calendrier/info'>
                        <input type='hidden' name='id' value='" . $this->safe($rdv['id']) . "'>
                        <button type='submit'>👁️ Voir détail</button>
                      </form>";
                echo "</div><hr>";
            }
        }

        echo "</section>";
    }

    // 📆 Vue jour
    public function renderJour(array $rendezVous, string $date): void
    {
        echo "<section class='calendrier-jour'>";
        echo "<h2>📅 Rendez-vous du " . $this->safe($date) . "</h2>";

        if (empty($rendezVous)) {
            echo "<p>Aucun rendez-vous prévu ce jour-là.</p>";
        } else {
            foreach ($rendezVous as $rdv) {
                echo "<div class='rdv-item'>";
                echo "<p><strong>Heure :</strong> " . $this->safe($rdv['heure']) . "</p>";
                echo "<p><strong>Type :</strong> " . $this->safe($rdv['type']) . "</p>";
                echo "<form method='GET' action='/calendrier/info'>
                        <input type='hidden' name='id' value='" . $this->safe($rdv['id']) . "'>
                        <button type='submit'>👁️ Voir détail</button>
                      </form>";
                echo "</div><hr>";
            }
        }

        echo "</section>";
    }
    public function renderCalendrier(array $entretiens, string $mois, string $annee): void
    {
        echo "<section class='calendrier-admin'>";
        echo "<h2>📅 Calendrier des entretiens - $mois/$annee</h2>";
    
        if (empty($entretiens)) {
            echo "<p>Aucun entretien prévu ce mois-ci.</p>";
        } else {
            foreach ($entretiens as $e) {
                echo "<div class='entretien-item'>";
                echo "<p><strong>Date :</strong> " . htmlspecialchars($e['date_entretien']) . "</p>";
                echo "<p><strong>Heure :</strong> " . htmlspecialchars($e['heure']) . "</p>";
                echo "<p><strong>Type :</strong> " . htmlspecialchars($e['type']) . "</p>";
                echo "<p><strong>Visio :</strong> " . htmlspecialchars($e['lien_visio']) . "</p>";
                echo "</div><hr>";
            }
        }
    
        echo "</section>";
    }
    
    // 🔔 Rappels du jour
    public function renderRappels(array $rappels): void
    {
        echo "<section class='rappels-jour'>";
        echo "<h2>🔔 Rappels du jour</h2>";

        if (empty($rappels)) {
            echo "<p>Aucun rappel à envoyer.</p>";
        } else {
            foreach ($rappels as $rdv) {
                echo "<div class='rappel-item'>";
                echo "<p><strong>Date :</strong> " . $this->safe($rdv['date_entretien']) . "</p>";
                echo "<p><strong>Heure :</strong> " . $this->safe($rdv['heure']) . "</p>";
                echo "<p><strong>Type :</strong> " . $this->safe($rdv['type']) . "</p>";
                echo "<form method='POST' action='/calendrier/envoyer-rappel'>
                        <input type='hidden' name='id' value='" . $this->safe($rdv['id']) . "'>
                        <button type='submit'>📩 Marquer comme envoyé</button>
                      </form>";
                echo "</div><hr>";
            }
        }

        echo "</section>";
    }

    // 👁️ Détail d’un rendez-vous
    public function renderDetails(array $rdv): void
    {
        echo "<section class='detail-rdv'>";
        echo "<h2>👁️ Détail du rendez-vous</h2>";
        echo "<p><strong>Date :</strong> " . $this->safe($rdv['date_entretien']) . "</p>";
        echo "<p><strong>Heure :</strong> " . $this->safe($rdv['heure']) . "</p>";
        echo "<p><strong>Type :</strong> " . $this->safe($rdv['type']) . "</p>";
        echo "<p><strong>Lien Visio :</strong> " . $this->safe($rdv['lien_visio']) . "</p>";
        echo "</section><hr>";
    }

    // 📋 Liste complète
    public function renderListe(array $entretiens): void
    {
        echo "<section class='liste-entretiens'>";
        echo "<h2>📋 Tous les entretiens</h2>";

        foreach ($entretiens as $e) {
            echo "<div class='entretien-item'>";
            echo "<p><strong>Date :</strong> " . $this->safe($e['date_entretien']) . "</p>";
            echo "<p><strong>Heure :</strong> " . $this->safe($e['heure']) . "</p>";
            echo "<p><strong>Type :</strong> " . $this->safe($e['type']) . "</p>";
            echo "<form method='GET' action='/calendrier/info'>
                    <input type='hidden' name='id' value='" . $this->safe($e['id']) . "'>
                    <button type='submit'>👁️ Voir détail</button>
                  </form>";
            echo "</div><hr>";
        }

        echo "</section>";
    }

    // 📝 Formulaire de planification (optionnel)
    public function renderForm(): void
    {
        echo "<section class='form-calendrier'>";
        echo "<h2>➕ Planifier un rendez-vous</h2>";
        echo "<form method='POST' action='/calendrier/planifier'>";

        echo "<label>ID utilisateur :
                <input type='number' name='id_utilisateur' required>
              </label><br>";

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

        echo "<label>Lien Visio :
                <input type='text' name='lien_visio'>
              </label><br>";

        echo "<button type='submit'>💾 Enregistrer</button>";
        echo "</form>";
        echo "</section><hr>";
    }
}
