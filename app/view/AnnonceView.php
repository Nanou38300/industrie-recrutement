<?php
// Namespace pour organiser les vues dans le projet
namespace App\View;

// Classe pour gérer l’affichage des annonces
class AnnonceView {
    
    // Méthode pour afficher la liste des annonces
    public function renderListe($annonces) {
        // Parcours de chaque annonce dans le tableau
        foreach ($annonces as $a) {
            echo "<div class='annonce'>"; // Bloc contenant l’annonce
            echo "<h3>{$a['titre']}</h3>"; // Titre du poste
            echo "<p>{$a['lieu']} | {$a['contrat']} | {$a['salaire']}<br>Publié le {$a['date']} | Réf: {$a['ref']}</p>"; // Infos principales

            // Bouton pour afficher les détails (fonction JS)
            echo "<button onclick=\"toggleDetails('{$a['ref']}')\">🔽 Détails</button>";

            // Bloc masqué qui contient les détails
            echo "<div id='details_{$a['ref']}' style='display:none'>";

            // Si l’annonce est complète, affiche les détails avec une autre méthode
            if ($a['complet']) {
                $this->renderDetails($a);
            } else {
                // Sinon, indique qu’il n’y a pas de détails
                echo "<p>Aucun détail disponible.</p>";
            }

            echo "</div></div><hr>"; // Fin du bloc + séparateur
        }

        // Script JavaScript intégré pour basculer l’affichage des détails
        echo "<script>
            function toggleDetails(ref) {
                var el = document.getElementById('details_' + ref);
                el.style.display = el.style.display === 'none' ? 'block' : 'none';
            }
        </script>";
    }

    // Méthode pour afficher les détails d’une annonce complète
    public function renderDetails($a) {
        echo "<p><strong>Description :</strong> {$a['description']}</p>";

        // Missions à afficher en liste si présentes
        echo "<p><strong>Missions :</strong><ul>";
        foreach ($a['missions'] ?? [] as $m) echo "<li>$m</li>"; // Boucle sur les missions
        echo "</ul></p>";

        // Profil recherché
        echo "<p><strong>Profil :</strong> {$a['profil']}</p>";

        // Liste des avantages
        echo "<p><strong>Avantages :</strong><ul>";
        foreach ($a['avantages'] ?? [] as $av) echo "<li>$av</li>"; // Boucle sur les avantages
        echo "</ul></p>";

        // Formulaire intégré pour postuler avec upload de CV
        echo "<form method='POST' action='index.php?action=postuler' enctype='multipart/form-data'>
                <input type='hidden' name='ref' value='{$a['ref']}'> <!-- Référence cachée -->
                <label>Déposez votre CV :</label><br>
                <input type='file' name='cv' required><br><br> <!-- Champ d’upload -->
                <button type='submit'>POSTULER</button> <!-- Bouton de soumission -->
              </form>";
    }
}
