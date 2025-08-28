<?php

namespace App\View;

class AdministrateurView
{
    // Fonction utilitaire pour sécuriser l'affichage HTML
    private function safe($value): string
    {
        return htmlspecialchars((string)($value ?? ''));
    }

    // 🔹 Affiche les informations du profil administrateur
    public function renderProfil(array $profil): void
    {
        extract($profil); // rend les clés du tableau accessibles comme variables
        echo "<section class='profil-admin'>";
            echo "<h2>👤 Mon Profil</h2>";

            // Affiche les champs du profil
            echo "<p><strong>Nom :</strong> " . $this->safe($profil['nom'] ?? '') . "</p>";
            echo "<p><strong>Prénom :</strong> " . $this->safe($profil['prenom'] ?? '') . "</p>";
            echo "<p><strong>Email :</strong> " . $this->safe($profil['email'] ?? '') . "</p>";
            echo "<p><strong>Téléphone :</strong> " . $this->safe($profil['telephone'] ?? '') . "</p>";
            echo "<p><strong>Poste :</strong> " . $this->safe($profil['poste'] ?? '') . "</p>";
            echo "<p><strong>Ville :</strong> " . $this->safe($profil['ville'] ?? '') . "</p>";

            // Bouton pour accéder au formulaire de modification du profil
            echo "<form method='GET' action='/administrateur/edit-profil' style='margin-top: 20px;'>";
            echo "<button type='submit' class='btn btn-primary'>✏️ Modifier mon profil</button>";
            echo "</form>";
        echo "</section><hr>";
    }

    // 🔹 Affiche le formulaire de modification du profil administrateur
    public function renderFormProfil(array $profil): void
    {
        echo "<section class='form-profil-admin'>";
            echo "<h2>✏️ Modifier mon profil</h2>";
            echo "<form method='POST' action='/administrateur/edit-profil'>";

            // Génère les champs du formulaire à partir du tableau $profil
            $fields = ['nom', 'prenom', 'email', 'telephone', 'poste', 'ville'];
            foreach ($fields as $field) {
                $value = $this->safe($profil[$field] ?? '');
                $label = ucfirst($field);
                echo "<label>$label : <input type='text' name='$field' value='$value' required></label><br>";
            }

            echo "<button type='submit'>💾 Enregistrer les modifications</button>";
            echo "</form>";
        echo "</section><hr>";

        // Bouton pour supprimer le compte administrateur
        echo "<section class='delete-profil'>";
        echo "<h3>🗑️ Supprimer mon compte</h3>";
        echo "<form method='POST' action='/administrateur/delete-profil' onsubmit=\"return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')\">";
        echo "<button type='submit' class='danger'>Supprimer mon profil</button>";
        echo "</form>";
        echo "</section><hr>";
    }

    // 🔹 Affiche les statistiques globales dans le tableau de bord
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
        echo "<div class='bloc-annonces-admin'>"; // 🔹 Bloc unique
    
        // 🔸 Titre + bouton de création
        echo "<div class='header-annonces'>";
        echo "<h2>📢 Gestion des annonces</h2>";
        echo "<form method='GET' action='/administrateur/create-annonce'>";
        echo "<button type='submit' class='btn btn-success'>➕ Créer une annonce</button>";
        echo "</form>";
        echo "</div>";
    
        // 🔸 Filtres
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
    
        // 🔸 Liste scrollable des annonces
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
    
                // Modifier
                echo "<form method='GET' action='/administrateur/edit-annonce'>";
                echo "<input type='hidden' name='id' value='" . $this->safe($a['id'] ?? '') . "'>";
                echo "<button type='submit'>✏️ Modifier</button>";
                echo "</form>";
    
                // Supprimer
                echo "<form method='POST' action='/administrateur/delete-annonce' onsubmit=\"return confirm('Supprimer cette annonce ?')\">";
                echo "<input type='hidden' name='id' value='" . $this->safe($a['id'] ?? '') . "'>";
                echo "<button type='submit' class='danger'>🗑️ Supprimer</button>";
                echo "</form>";
    
                echo "</div><hr>";
            }
        }
    
        echo "</div>"; // fin scroll
        echo "</div>"; // fin bloc-annonces-admin
        echo "</section><hr>";
    }
    public function renderFormAnnonce(array $annonce = []): void
    {
        $titre            = $annonce['titre'] ?? '';
        $description      = $annonce['description'] ?? '';
        $mission          = $annonce['mission'] ?? '';
        $localisation     = $annonce['localisation'] ?? '';
        $salaire          = $annonce['salaire'] ?? '';
        $statut           = $annonce['statut'] ?? 'brouillon';
        $avantages        = $annonce['avantages'] ?? '';
        $code_postale     = $annonce['code_postale'] ?? '';
        $type_contrat     = $annonce['type_contrat'] ?? '';
        $duree_contrat    = $annonce['duree_contrat'] ?? '';
        $profil_recherche = $annonce['profil_recherche'] ?? '';
        $secteur_activite = $annonce['secteur_activite'] ?? '';
        $idAdmin          = $_SESSION['utilisateur']['id'];
    
        echo "<h2>📝 Nouvelle annonce</h2>";
        echo "<form method='POST' action='/administrateur/create-annonce' style='max-width:700px;margin:auto;'>";
    
        echo "<label>Titre :</label><input type='text' name='titre' value='$titre' required><br><br>";
        echo "<label>Description :</label><textarea name='description' required>$description</textarea><br><br>";
        echo "<label>Mission :</label><textarea name='mission' required>$mission</textarea><br><br>";
        echo "<label>Localisation :</label><input type='text' name='localisation' value='$localisation' required><br><br>";
        echo "<label>Salaire :</label><input type='text' name='salaire' value='$salaire' required><br><br>";
    
        echo "<label>Statut :</label>
            <select name='statut' required>
                <option value='activée' " . ($statut === 'activée' ? 'selected' : '') . ">Activée</option>
                <option value='brouillon' " . ($statut === 'brouillon' ? 'selected' : '') . ">Brouillon</option>
                <option value='archivée' " . ($statut === 'archivée' ? 'selected' : '') . ">Archivée</option>
            </select><br><br>";
    
        echo "<label>Avantages :</label><textarea name='avantages' required>$avantages</textarea><br><br>";
        echo "<label>Code postal :</label><input type='number' name='code_postale' value='$code_postale' required><br><br>";
    
        echo "<label>Type de contrat :</label>
            <select name='type_contrat' required>
                <option value='CDI' " . ($type_contrat === 'CDI' ? 'selected' : '') . ">CDI</option>
                <option value='CDD' " . ($type_contrat === 'CDD' ? 'selected' : '') . ">CDD</option>
                <option value='Intérim' " . ($type_contrat === 'Intérim' ? 'selected' : '') . ">Intérim</option>
            </select><br><br>";
    
        echo "<label>Durée du contrat (en mois) :</label><input type='number' name='duree_contrat' value='$duree_contrat'><br><br>";
        echo "<label>Profil recherché :</label><textarea name='profil_recherche' required>$profil_recherche</textarea><br><br>";
        echo "<label>Secteur d'activité :</label><textarea name='secteur_activite' required>$secteur_activite</textarea><br><br>";
    
        echo "<input type='hidden' name='id_administrateur' value='$idAdmin'>";
        echo "<button type='submit'>💾 Publier l’annonce</button>";
    
        echo "</form>";
    }
    
    
    
    public function renderListeCandidatures(array $candidatures): void
    {
        echo "<section class='candidatures-admin'>";
        echo "<div class='bloc-candidatures-admin'>"; // 🔹 Bloc unique
    
        // Titre principal
        echo "<h2>📄 Candidatures reçues</h2>";
    
        // Si aucune candidature
        if (empty($candidatures)) {
            echo "<p>Aucune candidature enregistrée.</p>";
        } else {
            // Boucle sur chaque candidature
            foreach ($candidatures as $c) {
                echo "<div class='candidature-item'>";
    
                // Informations du candidat
                echo "<h3>" . $this->safe($c['prenom'] ?? '') . " " . $this->safe($c['nom'] ?? '') . "</h3>";
                echo "<p><strong>Poste :</strong> " . $this->safe($c['titre'] ?? '') . "</p>";
                echo "<p><strong>Référence :</strong> " . $this->safe($c['reference'] ?? '') . "</p>";
                echo "<p><strong>Statut :</strong> " . $this->safe($c['statut'] ?? '') . "</p>";
    
                // Formulaire de mise à jour du statut
                echo "<form method='POST' action='/candidature/update-statut'>";
                echo "<input type='hidden' name='id' value='" . $this->safe($c['id'] ?? '') . "'>";
                echo "<select name='statut'>
                        <option value='Envoyée'>Envoyée</option>
                        <option value='Consultée'>Consultée</option>
                        <option value='Entretien'>Entretien</option>
                        <option value='Recruté'>Recruté</option>
                        <option value='Refusé'>Refusé</option>
                      </select>";
                echo "<input type='text' name='commentaire_admin' placeholder='Commentaire'>";
                echo "<button type='submit'>💾 Mettre à jour</button>";
                echo "</form>";
    
                echo "</div><hr>";
            }
        }
    
        echo "</div>"; // fin bloc-candidatures-admin
        echo "</section>";
    }
    

       // 👁️ Détail d’une candidature
       public function renderDetailsCandidature(array $c): void
       {
           echo "<section class='details-candidature'>";
           echo "<div class='bloc-details-candidature'>"; // 🔹 Bloc unique
       
           // Titre principal
           echo "<h2>👁️ Détail de la candidature</h2>";
       
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
       


       public function renderCalendrier(array $candidat, ?array $entretien, array $entretiensDuJour): void
       {
           echo "<section class='calendrier-admin'>";
           echo "<div class='bloc-calendrier-admin'>"; // 🔹 Bloc unique
       
           // 🔹 Infos candidat
           echo "<div class='bloc-candidat'>";
           echo "<h3>👤 Informations du candidat</h3>";
           echo "<img src='" . $this->safe($candidat['photo'] ?? '/images/default.jpg') . "' alt='Photo du candidat' class='photo-candidat'>";
           echo "<p><strong>Nom :</strong> " . $this->safe($candidat['nom'] ?? '') . "</p>";
           echo "<p><strong>Prénom :</strong> " . $this->safe($candidat['prenom'] ?? '') . "</p>";
           echo "<p><strong>Poste :</strong> " . $this->safe($candidat['poste'] ?? '') . "</p>";
           echo "<p><strong>Téléphone :</strong> " . $this->safe($candidat['telephone'] ?? '') . "</p>";
           echo "<p><strong>Email :</strong> " . $this->safe($candidat['email'] ?? '') . "</p>";
           echo "</div><hr>";
       
           // 🔹 Rappel RDV
           echo "<div class='bloc-rappel' style='background-color: #e0f8e0; padding: 15px; border-radius: 8px;'>";
           echo "<h3>📅 Rappel RDV</h3>";
           echo "<p><strong>Date :</strong> " . $this->safe($entretien['date_entretien'] ?? '') . "</p>";
           echo "<p><strong>Heure :</strong> " . $this->safe($entretien['heure'] ?? '') . "</p>";
           echo "<p><strong>Type :</strong> " . ucfirst($this->safe($entretien['type'] ?? '')) . "</p>";
           echo "</div><hr>";
       
           // 🔹 Calendrier hebdomadaire
           echo "<div class='bloc-calendrier'>";
           echo "<h3>🗓️ Calendrier hebdomadaire</h3>";
       
           $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
           echo "<div class='semaine'>";
           foreach ($jours as $jour) {
               echo "<div class='jour'>" . $jour . "</div>";
           }
           echo "</div>";
       
           // 🔹 Entretiens du jour
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
       
           echo "</div>"; // fin entretiens-jour
           echo "</div>"; // fin bloc-calendrier
           echo "</div>"; // fin bloc-calendrier-admin
           echo "</section>";
       }
       

   }
   