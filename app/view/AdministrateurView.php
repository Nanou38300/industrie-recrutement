<?php
namespace App\View;


class AdministrateurView
{
    public function render(string $templatePath, array $data = []): void
{
    extract($data);
    include $templatePath;
}

    // 🧭 Vue du tableau de bord
    public function renderDashboard(array $stats): void
    {
        echo "<h1>Tableau de bord Administrateur</h1>";
        echo "<ul>";
        echo "<li>👥 Utilisateurs : " . $stats['total_utilisateurs'] . "</li>";
        echo "<li>📢 Annonces : " . $stats['total_annonces'] . "</li>";
        echo "<li>📄 Candidatures : " . $stats['total_candidatures'] . "</li>";
        echo "</ul>";
    }

    // 📝 Formulaire profil admin
    public function renderProfilForm(array $profil): void
    {
        echo "<h2>Modifier votre profil</h2>";
        echo "<form method='POST' action='/administrateur/profil'>";
        echo "<input type='hidden' name='id' value='" . htmlspecialchars($profil['id']) . "' />";
        echo "<label>Nom : <input name='nom' value='" . htmlspecialchars($profil['nom']) . "' /></label><br>";
        echo "<label>Prénom : <input name='prenom' value='" . htmlspecialchars($profil['prenom']) . "' /></label><br>";
        echo "<label>Email : <input name='email' value='" . htmlspecialchars($profil['email']) . "' /></label><br>";
        echo "<label>Poste : <input name='poste' value='" . htmlspecialchars($profil['poste']) . "' /></label><br>";
        echo "<label>Téléphone : <input name='telephone' value='" . htmlspecialchars($profil['telephone']) . "' /></label><br>";
        echo "<label>Lien Linkedin : <input name='lien_linkedin' value='" . htmlspecialchars($profil['lien_linkedin']) . "' /></label><br>";
        echo "<label>Photo de profil : <input name='photo_profil' value='" . htmlspecialchars($profil['photo_profil']) . "' /></label><br>";
        echo "<button type='submit'>Enregistrer</button>";
        echo "</form>";
    }

    public function renderCalendrierHebdo(array $rendezVous): void
{
    echo "<h2>📅 Calendrier des rendez-vous (semaine)</h2>";
    echo "<table border='1' cellpadding='8'>";
    echo "<thead><tr><th>Jour</th><th>Heure</th><th>Nom du candidat</th><th>Objet</th></tr></thead>";
    echo "<tbody>";

    foreach ($rendezVous as $rdv) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($rdv['jour']) . "</td>";
        echo "<td>" . htmlspecialchars($rdv['heure']) . "</td>";
        echo "<td>" . htmlspecialchars($rdv['nom_candidat']) . "</td>";
        echo "<td>" . htmlspecialchars($rdv['objet']) . "</td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
}

}
