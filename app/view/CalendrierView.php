<?php

namespace App\View;

class CalendrierView
{
    private function safe(?string $value): string
    {
        return htmlspecialchars($value ?? '');
    }
  
    // Vue mensuelle
    public function renderSemaine(array $rendezVous, string $semaine, string $annee): void
    {
        echo "<section class='calendrier-semaine'>";
        echo "<h2>Semaine $semaine - $annee</h2>";

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
                        <button type='submit'>Voir détail</button>
                      </form>";
                echo "</div><hr>";
            }
        }

        echo "</section>";
    }

    // Vue jour
    public function renderJour(array $rendezVous, string $date): void
    {
        echo "<section class='calendrier-jour'>";
        echo "<h2>Rendez-vous du " . $this->safe($date) . "</h2>";

        if (empty($rendezVous)) {
            echo "<p>Aucun rendez-vous prévu ce jour-là.</p>";
        } else {
            foreach ($rendezVous as $rdv) {
                echo "<div class='rdv-item'>";
                echo "<p><strong>Heure :</strong> " . $this->safe($rdv['heure']) . "</p>";
                echo "<p><strong>Type :</strong> " . $this->safe($rdv['type']) . "</p>";
                echo "<form method='GET' action='/calendrier/info'>
                        <input type='hidden' name='id' value='" . $this->safe($rdv['id']) . "'>
                        <button type='submit'>Voir détail</button>
                      </form>";
                echo "</div><hr>";
            }
        }

        echo "</section>";
    }
    public function renderCalendrier(array $entretiens, string $mois, string $annee): void
    {
        echo "<section class='calendrier-admin'>";
        echo "<h2>Calendrier des entretiens - $mois/$annee</h2>";
    
        echo "<pre>";
        var_dump($entretiens);
        echo "</pre>";
        

        if (empty($entretiens)) {
            echo "<p>Aucun entretien prévu ce mois-ci.</p>";
        } else {
            var_dump($entretiens);
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
        /**
     * Formulaire d'édition d'un entretien (pré-rempli)
     * Appelée par AdministrateurController::editEntretien()
     */
    public function renderFormModification(array $entretien): void
    {
        $id           = $this->safe((string)($entretien['id'] ?? ''));
        $date         = $this->safe($entretien['date_entretien'] ?? '');
        $heure        = $this->safe($entretien['heure'] ?? '');
        $type         = $this->safe($entretien['type'] ?? 'présentiel');
        $lien_visio   = $this->safe($entretien['lien_visio'] ?? '');
        $commentaire  = $this->safe($entretien['commentaire'] ?? '');

        // Options type
        $types = ['présentiel', 'visio', 'téléphone'];
        $optionsType = '';
        foreach ($types as $t) {
            $sel = ($t === strtolower($type)) ? ' selected' : '';
            $optionsType .= "<option value='{$this->safe($t)}'{$sel}>" . ucfirst($this->safe($t)) . "</option>";
        }

        echo "<section class='form-entretien'>";
        echo "<h2>Modifier l’entretien</h2>";
        echo "<form method='POST' action='/administrateur/edit-entretien'>";
        echo "  <input type='hidden' name='id' value='{$id}'>";

        echo "  <div class='form-row'>";
        echo "    <label style='display:block;margin:8px 0;'>Date";
        echo "      <input type='date' name='date_entretien' value='{$date}' required>";
        echo "    </label>";
        echo "    <label style='display:block;margin:8px 0;'>Heure";
        echo "      <input type='time' name='heure' value='{$heure}' required>";
        echo "    </label>";
        echo "  </div>";

        echo "  <label style='display:block;margin:8px 0;'>Type";
        echo "    <select name='type' required>{$optionsType}</select>";
        echo "  </label>";

        echo "  <label style='display:block;margin:8px 0;'>Lien visio (si visio)";
        echo "    <input type='url' name='lien_visio' value='{$lien_visio}' placeholder='https://...'>";
        echo "  </label>";

        echo "  <label style='display:block;margin:8px 0;'>Commentaire";
        echo "    <textarea name='commentaire' rows='4' placeholder='Notes internes...'>{$commentaire}</textarea>";
        echo "  </label>";

        echo "  <div class='actions'>";
        echo "    <button type='submit' class='btn btn-primary'>Enregistrer</button>";
        echo "    <a href='/administrateur/vue-calendrier' class='btn btn-secondary'>Annuler</a>";
        echo "  </div>";

        echo "</form>";
        echo "</section>";
    }
    
    // Rappels du jour
    public function renderRappels(array $rappels): void
    {
        echo "<section class='rappels-jour'>";
        echo "<h2>Rappels du jour</h2>";

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
                        <button type='submit'>Marquer comme envoyé</button>
                      </form>";
                echo "</div><hr>";
            }
        }

        echo "</section>";
    }

    // Détail d’un rendez-vous
    public function renderDetails(array $rdv): void
    {
        echo "<section class='detail-rdv'>";
        echo "<h2>Détail du rendez-vous</h2>";
        echo "<p><strong>Date :</strong> " . $this->safe($rdv['date_entretien']) . "</p>";
        echo "<p><strong>Heure :</strong> " . $this->safe($rdv['heure']) . "</p>";
        echo "<p><strong>Type :</strong> " . $this->safe($rdv['type']) . "</p>";
        echo "<p><strong>Lien Visio :</strong> " . $this->safe($rdv['lien_visio']) . "</p>";
        echo "</section><hr>";
    }

    // Liste complète
    public function renderListe(array $entretiens): void
    {
        echo "<section class='liste-entretiens'>";
        echo "<h2>Tous les entretiens</h2>";

        foreach ($entretiens as $e) {
            echo "<div class='entretien-item'>";
            echo "<p><strong>Date :</strong> " . $this->safe($e['date_entretien']) . "</p>";
            echo "<p><strong>Heure :</strong> " . $this->safe($e['heure']) . "</p>";
            echo "<p><strong>Type :</strong> " . $this->safe($e['type']) . "</p>";
            echo "<form method='GET' action='/calendrier/info'>
                    <input type='hidden' name='id' value='" . $this->safe($e['id']) . "'>
                    <button type='submit'>Voir détail</button>
                  </form>";
            echo "</div><hr>";
        }

        echo "</section>";
    }

    // Formulaire de planification (optionnel)
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

        echo "<button type='submit'>Enregistrer</button>";
        echo "</form>";
        echo "</section><hr>";
    }

//planification d'un entretien
public function renderFormCreation(string $date, string $heure, array $annonces, array $candidats): void
{
    echo "<section class='form-container'>";
    echo "<h2>Planifier un entretien</h2>";

    echo "<form class='form-annonce' method='POST' action='/administrateur/valider-entretien'>";

    // Ligne 1 : Date + Heure
    echo "<div class='form-row'>";
      echo "<div class='form-group'>
              <label for='date_entretien'>Date</label>
              <input type='date' id='date_entretien' name='date_entretien' value='" . htmlspecialchars($date) . "' required>
            </div>";

      echo "<div class='form-group'>
              <label for='heure'>Heure</label>
              <input type='time' id='heure' name='heure' value='" . htmlspecialchars($heure) . "' required>
            </div>";
    echo "</div>";

    // Ligne 2 : Candidat
    echo "<div class='form-group'>
            <label for='id_utilisateur'>Candidat</label>
            <select id='id_utilisateur' name='id_utilisateur' required>
              <option value='' disabled selected>Choisir un candidat</option>";
              foreach ($candidats as $c) {
                  echo "<option value='" . htmlspecialchars($c['id']) . "'>" . htmlspecialchars($c['prenom'] . ' ' . $c['nom']) . "</option>";
              }
    echo "  </select>
          </div>";

    // (Optionnel) Ligne 3 : Annonce liée
    // Décommente si ton backend attend un id d’annonce (name='id_annonce')
    /*
    echo "<div class='form-group'>
            <label for='id_annonce'>Annonce liée</label>
            <select id='id_annonce' name='id_annonce' required>
              <option value='' disabled selected>Associer une annonce</option>";
              foreach ($annonces as $a) {
                  echo "<option value='" . htmlspecialchars($a['id']) . "'>" . htmlspecialchars($a['titre']) . "</option>";
              }
    echo "  </select>
          </div>";
    */

    // Ligne 4 : Type + Lien visio
    echo "<div class='form-row'>";
      echo "<div class='form-group'>
              <label for='type'>Type d'entretien</label>
              <select id='type' name='type' required>
                <option value='' disabled selected>Sélectionner</option>
                <option value='Présentiel'>Présentiel</option>
                <option value='Visio'>Visio</option>
              </select>
            </div>";

      echo "<div class='form-group'>
              <label for='lien_visio'>Lien visio (si visio)</label>
              <input type='url' id='lien_visio' name='lien_visio' placeholder='https://...'>
              <small class='helper'>Renseigner uniquement pour un entretien en visio.</small>
            </div>";
    echo "</div>";

    // Ligne 5 : Commentaire
    echo "<div class='form-group'>
            <label for='commentaire'>Commentaire</label>
            <textarea id='commentaire' name='commentaire' rows='4' placeholder='Notes, contexte, objectifs...'></textarea>
          </div>";

    // Actions
    echo "<div class='form-actions'>
            <button type='submit' class='btn-entretien'>Créer le rendez-vous</button>
          </div>";

    echo "</form>";
    echo "</section>";
}
    

}
