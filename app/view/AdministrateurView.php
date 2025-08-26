<?php

namespace App\View;

class AdministrateurView
{
    private function safe($value): string
    {
        return htmlspecialchars((string)($value ?? ''));
    }

    // 👤 Profil administrateur
    public function renderProfil(array $profil): void
    {
        extract($profil);
        echo "<section class='profil-admin'>";
        echo "<h2>👤 Mon Profil</h2>";
        echo "<p><strong>Nom :</strong> " . $this->safe($profil['nom'] ?? '') . "</p>";
        echo "<p><strong>Prénom :</strong> " . $this->safe($profil['prenom'] ?? '') . "</p>";
        echo "<p><strong>Email :</strong> " . $this->safe($profil['email'] ?? '') . "</p>";
        echo "<p><strong>Téléphone :</strong> " . $this->safe($profil['telephone'] ?? '') . "</p>";
        echo "<p><strong>Poste :</strong> " . $this->safe($profil['poste'] ?? '') . "</p>";
        echo "<p><strong>Ville :</strong> " . $this->safe($profil['ville'] ?? '') . "</p>";

        echo "<form method='GET' action='/administrateur/edit-profil' style='margin-top: 20px;'>";
        echo "<button type='submit' class='btn btn-primary'>✏️ Modifier mon profil</button>";
        echo "</form>";
        echo "</section><hr>";

        echo "<section class='calendrier-semaine'>";
echo "<h3>📅 Entretiens prévus cette semaine</h3>";

if (empty($entretiensSemaine)) {
    echo "<p>Aucun entretien prévu cette semaine.</p>";
} else {
    $jours = [];
    foreach ($entretiensSemaine as $e) {
        $jour = date('l', strtotime($e['date_entretien']));
        $jours[$jour][] = $e;
    }

    foreach ($jours as $jour => $rdvs) {
        echo "<h4>" . ucfirst($jour) . "</h4>";
        foreach ($rdvs as $e) {
            echo "<div class='rdv-item'>";
            echo "<p><strong>Heure :</strong> " . $this->safe($e['heure'] ?? '') . "</p>";
            echo "<p><strong>Candidat :</strong> " . $this->safe($e['prenom'] ?? '') . " " . $this->safe($e['nom'] ?? '') . "</p>";
            echo "<p><strong>Poste :</strong> " . $this->safe($e['poste'] ?? '') . "</p>";
            echo "<p><strong>Type :</strong> " . $this->safe($e['type'] ?? '') . "</p>";
            echo "</div><hr>";
        }
    }
}

echo "</section><hr>";

echo "<section class='suivi-annonces'>";
echo "<h3>📢 Suivi d'annonces</h3>";

if (empty($annoncesStats)) {
    echo "<p>Aucune annonce publiée récemment.</p>";
} else {
    foreach ($annoncesStats as $a) {
        echo "<div class='annonce-suivi'>";
        echo "<h4>" . $this->safe($a['titre'] ?? '') . "</h4>";
        echo "<p><strong>Candidatures reçues :</strong> " . $this->safe($a['total_candidatures'] ?? 0) . "</p>";
        echo "<p><strong>Non lues :</strong> " . $this->safe($a['non_lues'] ?? 0) . "</p>";
        echo "</div><hr>";
    }
}

echo "</section><hr>";

    }

    public function renderFormProfil(array $profil): void
    {
        echo "<section class='form-profil-admin'>";
        echo "<h2>✏️ Modifier mon profil</h2>";
        echo "<form method='POST' action='/administrateur/edit-profil'>";

        echo "<label>Nom :
                <input type='text' name='nom' value='" . $this->safe($profil['nom'] ?? '') . "' required>
              </label><br>";

        echo "<label>Prénom :
                <input type='text' name='prenom' value='" . $this->safe($profil['prenom'] ?? '') . "' required>
              </label><br>";

        echo "<label>Email :
                <input type='email' name='email' value='" . $this->safe($profil['email'] ?? '') . "' required>
              </label><br>";

        echo "<label>Téléphone :
                <input type='text' name='telephone' value='" . $this->safe($profil['telephone'] ?? '') . "' required>
              </label><br>";

        echo "<label>Poste :
                <input type='text' name='poste' value='" . $this->safe($profil['poste'] ?? '') . "' required>
              </label><br>";

        echo "<label>Ville :
                <input type='text' name='ville' value='" . $this->safe($profil['ville'] ?? '') . "' required>
              </label><br>";

        echo "<button type='submit'>💾 Enregistrer les modifications</button>";
        echo "</form>";
        echo "</section><hr>";

        echo "<section class='delete-profil'>";
        echo "<h3>🗑️ Supprimer mon compte</h3>";
        echo "<form method='POST' action='/administrateur/delete-profil' onsubmit=\"return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')\">";
        echo "<button type='submit' class='danger'>Supprimer mon profil</button>";
        echo "</form>";
        echo "</section><hr>";
    }

    public function renderDashboard(array $stats): void
    {
        echo "<section class='dashboard-admin'>";
        echo "<h2>📊 Tableau de bord</h2>";
        echo "<ul>";
        echo "<li><strong>Utilisateurs :</strong> " . $this->safe($stats['totalUtilisateurs'] ?? 0) . "</li>";
        echo "<li><strong>Annonces :</strong> " . $this->safe($stats['totalAnnonces'] ?? 0) . "</li>";
        echo "<li><strong>Candidatures :</strong> " . $this->safe($stats['totalCandidatures'] ?? 0) . "</li>";
        echo "</ul>";
        echo "</section><hr>";
    }

    public function renderAnnonces(array $annonces): void
    {
        echo "<section class='annonces-admin'>";
        
        // 🔹 Bouton de création
        echo "<div class='header-annonces'>";
        echo "<h2>📢 Gestion des annonces</h2>";
        echo "<form method='GET' action='/administrateur/create-annonce'>";
        echo "<button type='submit' class='btn btn-success'>➕ Créer une annonce</button>";
        echo "</form>";
        echo "</div>";
    
        // 🔹 Filtres par statut
        echo "<div class='filtres-annonces'>";
        echo "<form method='GET' action='/administrateur/annonces'>";
        echo "<label for='statut'>Filtrer par statut :</label>";
        echo "<select name='statut' id='statut' onchange='this.form.submit()'>";
        echo "<option value=''>Toutes</option>";
        echo "<option value='en_cours'>En cours</option>";
        echo "<option value='suspendu'>Suspendu</option>";
        echo "<option value='archivee'>Archivée</option>";
        echo "</select>";
        echo "</form>";
        echo "</div>";
    
        // 🔹 Bloc scrollable des annonces
        echo "<div class='bloc-annonces-scroll' style='max-height: 500px; overflow-y: auto;'>";
    
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
    
                // ✏️ Modifier
                echo "<form method='GET' action='/administrateur/edit-annonce'>";
                echo "<input type='hidden' name='id' value='" . $this->safe($a['id'] ?? '') . "'>";
                echo "<button type='submit'>✏️ Modifier</button>";
                echo "</form>";
    
                // 🗑️ Supprimer
                echo "<form method='POST' action='/administrateur/delete-annonce' onsubmit=\"return confirm('Supprimer cette annonce ?')\">";
                echo "<input type='hidden' name='id' value='" . $this->safe($a['id'] ?? '') . "'>";
                echo "<button type='submit' class='danger'>🗑️ Supprimer</button>";
                echo "</form>";
    
                echo "</div><hr>";
            }
        }
    
        echo "</div>"; // fin scroll
        echo "</section><hr>";
    }
    

    public function renderFormAnnonce(?array $annonce = null): void
    {
        $action = $annonce ? "/administrateur/edit-annonce?id=" . $this->safe($annonce['id'] ?? '') : "/administrateur/create-annonce";
        echo "<section class='form-annonce'>";
        echo "<h2>" . ($annonce ? "✏️ Modifier l'annonce" : "➕ Nouvelle annonce") . "</h2>";
        echo "<form method='POST' action='$action'>";

        $fields = [
            'titre', 'description', 'mission', 'profil_recherche', 'localisation',
            'code_postale', 'secteur_activite', 'salaire', 'avantages',
            'type_contrat', 'duree_contrat', 'statut', 'date_publication', 'reference'
        ];

        foreach ($fields as $field) {
            $value = $this->safe($annonce[$field] ?? '');
            $label = ucfirst(str_replace('_', ' ', $field));
            echo "<label>$label : <input name='$field' value='$value' required></label><br>";
        }

        echo "<button type='submit'>💾 Enregistrer</button>";
        echo "</form>";
        echo "</section><hr>";
    }

    public function renderListeCandidatures(array $candidatures): void
    {
        echo "<section class='candidatures-admin'>";
        echo "<h2>📄 Candidatures reçues</h2>";

        if (empty($candidatures)) {
            echo "<p>Aucune candidature enregistrée.</p>";
        } else {
            foreach ($candidatures as $c) {
                echo "<div class='candidature-item'>";
                echo "<h3>" . $this->safe($c['prenom'] ?? '') . " " . $this->safe($c['nom'] ?? '') . "</h3>";
                echo "<p><strong>Poste :</strong> " . $this->safe($c['titre'] ?? '') . "</p>";
                echo "<p><strong>Référence :</strong> " . $this->safe($c['reference'] ?? '') . "</p>";
                echo "<p><strong>Statut :</strong> " . $this->safe($c['statut'] ?? '') . "</p>";

                echo "<form method='POST' action='/candidature/updateStatut'>
                        <input type='hidden' name='id' value='" . $this->safe($c['id'] ?? '') . "'>
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
       public function renderDetailsCandidature(array $c): void
       {
           echo "<section class='details-candidature'>";
           echo "<h2>👁️ Détail de la candidature</h2>";
           echo "<p><strong>Nom :</strong> " . $this->safe($c['prenom'] ?? '') . " " . $this->safe($c['nom'] ?? '') . "</p>";
           echo "<p><strong>Poste :</strong> " . $this->safe($c['titre'] ?? '') . "</p>";
           echo "<p><strong>Référence :</strong> " . $this->safe($c['reference'] ?? '') . "</p>";
           echo "<p><strong>Date d’envoi :</strong> " . $this->safe($c['date_envoi'] ?? '') . "</p>";
           echo "<p><strong>Statut :</strong> " . $this->safe($c['statut'] ?? '') . "</p>";
           echo "<p><strong>Commentaire :</strong> " . $this->safe($c['commentaire_admin'] ?? '') . "</p>";
           echo "</section><hr>";
       }


       public function renderCalendrier(array $candidat, ?array $entretien, array $entretiensDuJour): void

{
    // 🔹 Bloc 1 : Infos candidat
    echo "<section class='bloc-candidat'>";
    echo "<h3>👤 Informations du candidat</h3>";
    echo "<img src='" . $this->safe($candidat['photo'] ?? '/images/default.jpg') . "' alt='Photo du candidat' class='photo-candidat'>";
    echo "<p><strong>Nom :</strong> " . $this->safe($candidat['nom'] ?? '') . "</p>";
    echo "<p><strong>Prénom :</strong> " . $this->safe($candidat['prenom'] ?? '') . "</p>";
    echo "<p><strong>Poste :</strong> " . $this->safe($candidat['poste'] ?? '') . "</p>";
    echo "<p><strong>Téléphone :</strong> " . $this->safe($candidat['telephone'] ?? '') . "</p>";
    echo "<p><strong>Email :</strong> " . $this->safe($candidat['email'] ?? '') . "</p>";
    echo "</section><hr>";

    // 🔹 Bloc 2 : Rappel RDV
    echo "<section class='bloc-rappel' style='background-color: #e0f8e0; padding: 15px; border-radius: 8px;'>";
    echo "<h3>📅 Rappel RDV</h3>";
    echo "<p><strong>Date :</strong> " . $this->safe($entretien['date_entretien'] ?? '') . "</p>";
    echo "<p><strong>Heure :</strong> " . $this->safe($entretien['heure'] ?? '') . "</p>";
    echo "<p><strong>Type :</strong> " . ucfirst($this->safe($entretien['type'] ?? '')) . "</p>";
    echo "</section><hr>";

    // 🔹 Bloc 3 : Calendrier hebdomadaire + entretiens du jour
    echo "<section class='bloc-calendrier'>";
    echo "<h3>🗓️ Calendrier hebdomadaire</h3>";

    $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
    echo "<div class='semaine'>";
    foreach ($jours as $jour) {
        echo "<div class='jour'>" . $jour . "</div>";
    }
    echo "</div>";

    echo "<div class='entretiens-jour'>";
    echo "<h4>📌 Entretiens prévus aujourd’hui</h4>";

    if (empty($entretiensDuJour)) {
        echo "<p>Aucun entretien prévu aujourd’hui.</p>";
    } else {
        foreach ($entretiensDuJour as $e) {
            echo "<div class='rdv-item'>";
            echo "<p><strong>Heure :</strong> " . $this->safe($e['heure'] ?? '') . "</p>";
            echo "<p><strong>Candidat :</strong> " . $this->safe($e['prenom'] ?? '') . " " . $this->safe($e['nom'] ?? '') . "</p>";
            echo "<p><strong>Poste :</strong> " . $this->safe($e['poste'] ?? '') . "</p>";
            echo "<p><strong>Lieu :</strong> " . $this->safe($e['lieu'] ?? '') . "</p>";
            echo "</div><hr>";
        }
    }

    echo "</div>";
    echo "</section>";
}

   }
   