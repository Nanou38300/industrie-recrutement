<?php
namespace App\View;

class AnnonceView
{
    public function renderAdminSlider(array $annonces, object $model): void
    {
        echo "<div class='slider-annonces'>";
        foreach ($annonces as $annonce) {
            $stats = $model->getStatsParAnnonce($annonce['id']);
            
            echo "<div class='card-admin'>";
            echo "<h3>" . htmlspecialchars($annonce['titre']) . "</h3>";
            echo "<p>" . htmlspecialchars($annonce['lieu']) . " | " . htmlspecialchars($annonce['contrat']) . " | " . htmlspecialchars($annonce['salaire']) . " €</p>";
            
            echo "<div class='stats'>";
            echo "📬 Total : " . $stats['total'] . " | 🔎 Non lues : " . $stats['non_lues'];
            echo "</div>";

            echo "<div class='actions'>";
            echo "<a href='/annonce/modifier/" . $annonce['id'] . "'>✏️ Modifier</a> ";
            echo "<a href='/annonce/archiver/" . $annonce['id'] . "'>🗃️ Archiver</a>";
            echo "</div>";
            echo "</div>";
        }
        echo "</div>";
    }

    public function renderCandidatAnnonces(array $annonces): void
    {
        echo "<div class='liste-annonces'>";
        foreach ($annonces as $annonce) {
            echo "<div class='card-candidat'>";
            echo "<h4>" . htmlspecialchars($annonce['titre']) . "</h4>";
            echo "<p>" . htmlspecialchars($annonce['lieu']) . " | " . htmlspecialchars($annonce['contrat']) . " | " . htmlspecialchars($annonce['salaire']) . " €</p>";

            echo "<button class='toggle-details'>ℹ️ Voir plus</button>";
            echo "<div class='details' style='display:none;'>";
            echo "<p>" . nl2br(htmlspecialchars($annonce['description'])) . "</p>";
            echo "<a href='/utilisateur/create' class='btn-postuler'>📝 Postuler</a>";
            echo "</div>";
            echo "</div>";
        }
        echo "</div>";
    }

    public function renderAnnonceForm(array $annonce = []): void
    {
        $titre = $annonce['titre'] ?? '';
        $description = $annonce['description'] ?? '';
        $lieu = $annonce['lieu'] ?? '';
        $contrat = $annonce['contrat'] ?? '';
        $salaire = $annonce['salaire'] ?? '';

        echo "<form method='POST'>";
        echo "<label>Titre : <input name='titre' value='" . htmlspecialchars($titre) . "' /></label><br>";
        echo "<label>Description :</label><br>";
        echo "<textarea name='description'>" . htmlspecialchars($description) . "</textarea><br>";
        echo "<label>Lieu : <input name='lieu' value='" . htmlspecialchars($lieu) . "' /></label><br>";
        echo "<label>Type de contrat : <input name='contrat' value='" . htmlspecialchars($contrat) . "' /></label><br>";
        echo "<label>Salaire : <input name='salaire' value='" . htmlspecialchars($salaire) . "' /></label><br>";
        echo "<button type='submit'>Enregistrer</button>";
        echo "</form>";
    }
}
