<?php

namespace App\View;

class CandidatView
{
    public function renderDashboard(array $donnees): void
    {

        $this->renderProfil($donnees['profil']);
        $this->renderAnnonces($donnees['annonces']);
        $this->renderCandidatures($donnees['candidatures']);
   
    }

    public function renderProfil(array $profil): void
    {
        echo "<section class='profil'>";

        echo "<h2>👤 Bienvenue</h2>";
        echo "<section class='infos'>";
            echo "<section class='info-personnel'>";
                echo "<p><strong>Nom :</strong> " . htmlspecialchars($profil['nom']) . "</p>";
                echo "<p><strong>Email :</strong> " . htmlspecialchars($profil['email']) . "</p>";
                echo "<p><strong>Mot de passe :</strong> " . htmlspecialchars($profil['mot_de_passe']) . "</p>";
                echo "<p><strong>Téléphone :</strong> " . htmlspecialchars($profil['téléphone']) . "</p>";
                echo "<p><strong>Ville :</strong> " . htmlspecialchars($profil['ville']) . "</p>";
            echo "</section><hr>";

            // 📤 Formulaire d’upload de photo
            echo "<section class='CV'>";
                echo "<form method='POST' enctype='multipart/form-data' action='candidat/uploadPhoto'>";
                echo "<input type='file' name='photo' accept='image/*'>";
                echo "<button type='submit'>Envoyer</button>";
                echo "</form>";
            echo "</section><hr>";

        
        echo "<section class='photo'>";
            // 🔄 Utilisation de 'photo_profil' comme nom de la clé
            $photo = !empty($profil['photo_profil']) ? htmlspecialchars($profil['photo_profil']) : 'default.jpg';
            echo "<img src='" . $photo . "' alt='Photo de profil' />";
            
            echo "<span class='nom-utilisateur'>" . htmlspecialchars($profil['nom']) . "</span>";
            echo "</section><hr>";


    }
    
public function renderUploadForm(): void
{
    echo "<section class='upload-cv'>";
    echo "<h2>📄 Télécharger mon CV</h2>";
    echo "<form method='POST' enctype='multipart/form-data' action='/candidat/upload-cv'>
        <input type='file' name='cv' accept='.pdf,.doc,.docx' />
        <button type='submit'>Enregistrer</button>
    </form>";
    echo "</section><hr>";
}

    
    public function renderAnnonces(array $annonces): void
    {
        echo "<section class='annonces'>";
        echo "<h2>📢 Annonces Disponibles</h2>";
        if (empty($annonces)) {
            echo "<p>Aucune annonce disponible pour le moment.</p>";
        } else {
            foreach ($annonces as $annonce) {
                echo "<div class='annonce'>";
                echo "<h3>{$annonce['titre']}</h3>";
                echo "<p><strong>Description :</strong> {$annonce['description']}</p>";
                echo "<p><strong>Lieu :</strong> {$annonce['lieu']}</p>";
                echo "</div><hr>";
            }
        }
        echo "</section>";
    }

    public function renderCandidatures(array $candidatures): void
    {
        echo "<section class='candidatures'>";
        echo "<h2>📬 Mes Candidatures</h2>";
        if (empty($candidatures)) {
            echo "<p>Aucune candidature envoyée.</p>";
        } else {
            echo "<ul>";
            foreach ($candidatures as $candidature) {
                echo "<strong>{$candidature['poste']}</strong> ";
                echo "Localisation: {$candidature['localisation']}";
                echo "Référence : {$candidature['reference']}";
                echo "Type de contrat : {$candidature['type de contrat']}"; 
                echo "Salaire : {$candidature['salaire']}";
                echo "Date de création : {$candidature['date de création']}"; 


                echo "Statut : {$candidature['statut']}";
            }
            echo "</ul>";
        }
        echo "</section>";
    }

    public function renderEditForm(array $profil): void
{
    echo "<section class='modifier-profil'>";
    echo "<h2>✏️ Modifier mes informations</h2>";
    echo "<form method='POST' action='/candidat/update'>
        <label>Nom : <input name='nom' value='{$profil['nom']}' /></label><br>
        <label>Email : <input name='email' value='{$profil['email']}' /></label><br>";
    ?>
        <input type="image" src="assets/images/stylo-modif.png" alt="Modifier" class="btn-image">
    <?php
    echo "</form>";

    echo "</section><hr>";
}

public function renderDeleteButton(): void
{
    echo "<section class='supprimer-profil'>";
    echo "<form method='POST' action='/candidat/delete'>";
    
    // 🗑️ Image cliquable à la place du bouton texte
    echo "<input type='image' src='assets/images/poubelle.png' alt='Supprimer' class='btn-sup' onclick='return confirm(\"Supprimer mon profil ?\")'>";

    echo "</form>";
    echo "</section><hr>";
}


}
