<?php
namespace App\View;

class AnnonceView {

    public function renderProfil(array $data): void
{
    $infos = $data['infos'];
    $stats = $data['statsAnnonces'];
    $rendezVous = $data['rendezVous'];

    echo "<section class='admin-profil'>";
    // Bloc infos personnelles
    echo "<div class='bloc-infos'>";
    echo "<img src='/" . htmlspecialchars($infos['photo'] ?? 'default.jpg') . "' alt='Photo'>";
    echo "<h2>" . htmlspecialchars($infos['nom']) . "</h2>";
    echo "<p>" . htmlspecialchars($infos['poste']) . " - " . htmlspecialchars($infos['societe']) . "</p>";
    echo "<p>Email : " . htmlspecialchars($infos['email']) . "</p>";
    echo "<p>Tél : " . htmlspecialchars($infos['telephone']) . "</p>";
    echo "<a href='/administrateur/edit-profil'>✏️ Modifier</a>";
    echo "</div>";

    // Bloc suivi d'annonces
    echo "<div class='bloc-annonces'>";
    echo "<h3>Suivi d'annonces</h3>";
    foreach ($stats as $stat) {
        echo "<div class='stat-poste'>";
        echo "<strong>" . htmlspecialchars($stat['poste']) . "</strong><br>";
        echo "Candidatures : " . $stat['total'] . " | Non lues : " . $stat['non_lues'];
        echo "</div>";
    }
    echo "</div>";

    // Bloc calendrier
    echo "<div class='bloc-calendrier'>";
    echo "<h3>Suivi de rendez-vous</h3>";
    foreach ($rendezVous as $rdv) {
        echo "<div class='rdv'>";
        echo "<strong>" . htmlspecialchars($rdv['date_entretien']) . "</strong> : ";
        echo htmlspecialchars($rdv['nom_candidat']) . " pour le poste de " . htmlspecialchars($rdv['poste']);
        echo "</div>";
    }
    echo "</div>";
    echo "</section>";
}


    public function renderListe(array $annonces): void
{
    echo "<section class='annonces'>";
    echo "<h2>Annonces Disponibles</h2>";

    foreach ($annonces as $annonce) {
        echo "<div class='annonce'>";
        
        // Bloc résumé
        echo "<div class='resume'>";
        echo "<h3>" . htmlspecialchars($annonce['titre'] ?? 'Sans titre') . "</h3>";
        echo "<p><strong>Lieu :</strong> " . htmlspecialchars($annonce['localisation'] ?? '') . "</p>";
        echo "<p><strong>Contrat :</strong> " . htmlspecialchars($annonce['type_contrat'] ?? '') . "</p>";
        echo "<p><strong>Salaire :</strong> " . htmlspecialchars($annonce['salaire'] ?? '') . "</p>";
        echo "<p><strong>Publié le :</strong> " . htmlspecialchars($annonce['date_publication'] ?? '') . "</p>";
        echo "<button class='toggle-details'>Voir plus</button>";
        echo "<form method='POST' action='/candidat/postuler?id=" . htmlspecialchars($annonce['id']) . "'>";
        echo "<button type='submit'> Postuler</button>";
        echo "</form>";
        echo "</div>";

        // Bloc détails masqué
        echo "<div class='details'>";
       // détails
echo "<h4>Description</h4><p>" . htmlspecialchars($annonce['description'] ?? '') . "</p>";
echo "<h4>Missions</h4><p>" . htmlspecialchars($annonce['mission'] ?? '') . "</p>"; 
echo "<h4>Profil</h4><p>" . htmlspecialchars($annonce['profil_recherche'] ?? '') . "</p>";
        echo "<h4>Avantages</h4><p>" . htmlspecialchars($annonce['avantages'] ?? '') . "</p>";
        echo "</div>";

        echo "</div>";
    }

    echo "</section>";
}


    public function renderDetails(array $a) {
        echo "<div class='annonce-details'>";
        echo "<h2>{$a['titre']}</h2>";
        echo "<div class='annonce-meta'>";
        echo "<span class='badge'>{$a['statut']}</span> ";
        echo "<span class='date'>Publié le : {$a['date_publication']}</span>";
        echo "</div>";
        
        echo "<div class='section'>";
        echo "<h3>Description</h3>";
        echo "<p>{$a['description']}</p>";
        echo "</div>";
        
        echo "<div class='section'>";
        echo "<h3>Mission</h3>";
        echo "<p>{$a['mission']}</p>";
        echo "</div>";
        
        echo "<div class='section'>";
        echo "<h3>Profil recherché</h3>";
        echo "<p>{$a['profil_recherche']}</p>";
        echo "</div>";
        
        echo "<div class='info-grid'>";
        echo "<div class='info-item'><strong>Secteur :</strong> {$a['secteur_activite']}</div>";
        echo "<div class='info-item'><strong>Localisation :</strong> {$a['localisation']} ({$a['code_postale']})</div>";
        echo "<div class='info-item'><strong>Salaire :</strong> {$a['salaire']}</div>";
        echo "<div class='info-item'><strong>Avantages :</strong> {$a['avantages']}</div>";
        echo "<div class='info-item'><strong>Type de contrat :</strong> {$a['type_contrat']}</div>";
        echo "<div class='info-item'><strong>Durée :</strong> {$a['duree_contrat']}</div>";

        echo "</div>";
        
        echo "<div class='actions'>";
        echo "<a href='?action=annonce' class='btn btn-secondary'>Retour à la liste</a> ";
        echo "<a href='?action=annonce&step=update&id={$a['id']}' class='btn btn-primary'>✏️ Modifier</a>";
        echo "</div>";
        echo "</div>";
    }

    public function renderForm(string $mode, ?array $data = null) {
        $action = $mode === 'create' ? 'create' : 'update';
        $title = $mode === 'create' ? '➕ Créer une annonce' : '✏️ Modifier une annonce';
        $idField = $mode === 'update' ? "<input type='hidden' name='id' value='{$data['id']}'>" : "";
        
        echo "<div class='form-container'>";
        echo "<h2>$title</h2>";
        echo "<form method='POST' action='?action=annonce&step=$action' class='annonce-form'>";
        echo $idField;
        
        // Informations principales
        echo "<fieldset>";
        echo "<legend>Informations principales</legend>";
        echo "<div class='form-group'>";
        echo "<label for='titre'>Titre de l'annonce *:</label>";
        echo "<input type='text' id='titre' name='titre' value='" . htmlspecialchars($data['titre'] ?? '') . "' required>";
        echo "</div>";
        
        echo "<div class='form-group'>";
        echo "<label for='description'>Description *:</label>";
        echo "<textarea id='description' name='description' rows='4' required>" . htmlspecialchars($data['description'] ?? '') . "</textarea>";
        echo "</div>";
        
        echo "<div class='form-group'>";
        echo "<label for='mission'>Mission *:</label>";
        echo "<textarea id='mission' name='mission' rows='4' required>" . htmlspecialchars($data['mission'] ?? '') . "</textarea>";
        echo "</div>";
        
        echo "<div class='form-group'>";
        echo "<label for='profil_recherche'>Profil recherché *:</label>";
        echo "<textarea id='profil_recherche' name='profil_recherche' rows='3' required>" . htmlspecialchars($data['profil_recherche'] ?? '') . "</textarea>";
        echo "</div>";
        echo "</fieldset>";
        
        // Localisation
        echo "<fieldset>";
        echo "<legend>Localisation</legend>";
        echo "<div class='form-row'>";
        echo "<div class='form-group'>";
        echo "<label for='localisation'>Ville *:</label>";
        echo "<input type='text' id='localisation' name='localisation' value='" . htmlspecialchars($data['localisation'] ?? '') . "' required>";
        echo "</div>";
        echo "<div class='form-group'>";
        echo "<label for='code_postale'>Code postal *:</label>";
        echo "<input type='text' id='code_postale' name='code_postale' value='" . htmlspecialchars($data['code_postale'] ?? '') . "' required>";
        echo "</div>";
        echo "</div>";
        echo "</fieldset>";
        
        // Détails du poste
        echo "<fieldset>";
        echo "<legend>Détails du poste</legend>";
        echo "<div class='form-group'>";
        echo "<label for='secteur_activite'>Secteur d'activité *:</label>";
        echo "<input type='text' id='secteur_activite' name='secteur_activite' value='" . htmlspecialchars($data['secteur_activite'] ?? '') . "' required>";
        echo "</div>";
        
        echo "<div class='form-row'>";
        echo "<div class='form-group'>";
        echo "<label for='salaire'>Salaire:</label>";
        echo "<input type='text' id='salaire' name='salaire' value='" . htmlspecialchars($data['salaire'] ?? '') . "'>";
        echo "</div>";
        echo "<div class='form-group'>";
        echo "<label for='avantages'>Avantages:</label>";
        echo "<input type='text' id='avantages' name='avantages' value='" . htmlspecialchars($data['avantages'] ?? '') . "'>";
        echo "</div>";
        echo "</div>";
        
        echo "<div class='form-row'>";
        echo "<div class='form-group'>";
        echo "<label for='type_contrat'>Type de contrat *:</label>";
        echo "<select id='type_contrat' name='type_contrat' required>";
        $types = ['CDI', 'CDD', 'Stage', 'Alternance', 'Freelance'];
        foreach ($types as $type) {
            $selected = (($data['type_contrat'] ?? '') === $type) ? 'selected' : '';
            echo "<option value='$type' $selected>$type</option>";
        }
        echo "</select>";
        echo "</div>";
        echo "<div class='form-group'>";
        echo "<label for='duree_contrat'>Durée du contrat:</label>";
        echo "<input type='text' id='duree_contrat' name='duree_contrat' value='" . htmlspecialchars($data['duree_contrat'] ?? '') . "'>";
        echo "</div>";
        echo "</div>";
        echo "</fieldset>";
        
        // Informations administratives
        echo "<fieldset>";
        echo "<legend>Informations administratives</legend>";
        echo "<div class='form-row'>";
        echo "<div class='form-group'>";
        echo "<label for='date_publication'>Date de publication *:</label>";
        echo "<input type='date' id='date_publication' name='date_publication' value='" . htmlspecialchars($data['date_publication'] ?? date('Y-m-d')) . "' required>";
        echo "</div>";
        echo "</div>";
        
        echo "<div class='form-row'>";
        echo "<div class='form-group'>";
        echo "<label for='statut'>Statut *:</label>";
        echo "<select id='statut' name='statut' required>";
        $statuts = ['activée', 'brouillon', 'archivée'];
        foreach ($statuts as $s) {
            $selected = (($data['statut'] ?? 'activée') === $s) ? 'selected' : '';
            echo "<option value='{$s}' {$selected}>" . ucfirst($s) . "</option>";
        }
        echo "</select>";
        echo "</div>";
        echo "<div class='form-group'>";
        echo "<label for='id_administrateur'>ID Administrateur *:</label>";
        echo "<input type='number' id='id_administrateur' name='id_administrateur' value='" . htmlspecialchars($data['id_administrateur'] ?? ($_SESSION['utilisateur']['id'] ?? '')) . "' required>";
        echo "</div>";
        echo "</div>";
        echo "</fieldset>";
        
        // Actions
        echo "<div class='form-actions'>";
        echo "<button type='submit' class='btn btn-primary'>Enregistrer</button> ";
        echo "<a href='?action=annonce' class='btn btn-secondary'>Annuler</a>";
        echo "</div>";
        
        echo "</form>";
        echo "</div>";
        
 

    echo '<script src="./assets/js/annonce.js"></script>';
    }


}


?>

