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
    echo "<section class='profil-admin'>";
    echo "<h2>👤 Mon Profil</h2>";
    echo "<p><strong>Nom :</strong> " . htmlspecialchars($profil['nom']) . "</p>";
    echo "<p><strong>Prénom :</strong> " . htmlspecialchars($profil['prenom']) . "</p>";
    echo "<p><strong>Email :</strong> " . htmlspecialchars($profil['email']) . "</p>";
    echo "<p><strong>Téléphone :</strong> " . htmlspecialchars($profil['telephone']) . "</p>";
    echo "<p><strong>Poste :</strong> " . $this->safe($profil['poste']) . "</p>";
    echo "<p><strong>Ville :</strong> " . $this->safe($profil['ville']) . "</p>";
    

    // ✏️ Bouton de modification
    echo "<form method='GET' action='/administrateur/edit-profil' style='margin-top: 20px;'>";
    echo "<button type='submit' class='btn btn-primary'>✏️ Modifier mon profil</button>";
    echo "</form>";

    echo "</section><hr>";
}

    public function renderFormProfil(array $profil): void
    {
        echo "<section class='form-profil-admin'>";
        echo "<h2>✏️ Modifier mon profil</h2>";
        echo "<form method='POST' action='/administrateur/edit-profil'>";
    
        echo "<label>Nom :
                <input type='text' name='nom' value='" . htmlspecialchars($profil['nom']) . "' required>
              </label><br>";
    
        echo "<label>Prénom :
                <input type='text' name='prenom' value='" . htmlspecialchars($profil['prenom']) . "' required>
              </label><br>";
    
        echo "<label>Email :
                <input type='email' name='email' value='" . htmlspecialchars($profil['email']) . "' required>
              </label><br>";
    
        echo "<label>Téléphone :
                <input type='text' name='telephone' value='" . htmlspecialchars($profil['telephone']) . "' required>
              </label><br>";
    
        echo "<label>Poste :
                <input type='text' name='poste' value='" . htmlspecialchars($profil['poste']) . "' required>
              </label><br>";
    
        echo "<label>Ville :
                <input type='text' name='ville' value='" . htmlspecialchars($profil['ville']) . "' required>
              </label><br>";
    
        echo "<button type='submit'>💾 Enregistrer les modifications</button>";
        echo "</form>";
        echo "</section><hr>";
    
        // 🗑️ Bouton de suppression
        echo "<section class='delete-profil'>";
        echo "<h3>🗑️ Supprimer mon compte</h3>";
        echo "<form method='POST' action='/administrateur/delete-profil' onsubmit=\"return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')\">";
        echo "<button type='submit' class='danger'>Supprimer mon profil</button>";
        echo "</form>";
        echo "</section><hr>";
    }
    
    // 📊 Tableau de bord
    public function renderDashboard(array $stats): void
    {
        echo "<section class='dashboard-admin'>";
        echo "<h2>📊 Tableau de bord</h2>";
        echo "<ul>";
        echo "<li><strong>Utilisateurs :</strong> {$stats['totalUtilisateurs']}</li>";
        echo "<li><strong>Annonces :</strong> {$stats['totalAnnonces']}</li>";
        echo "<li><strong>Candidatures :</strong> {$stats['totalCandidatures']}</li>";
        echo "</ul>";
        echo "</section><hr>";
    }

    // 📢 Liste des annonces
    public function renderAnnonces(array $annonces): void
    {
        echo "<section class='annonces-admin'>";
        echo "<h2>📢 Annonces publiées</h2>";

        if (empty($annonces)) {
            echo "<p>Aucune annonce disponible.</p>";
        } else {
            foreach ($annonces as $annonce) {
                echo "<div class='annonce-item'>";
                echo "<h3>" . $this->safe($annonce['titre']) . "</h3>";
                echo "<p><strong>Référence :</strong> " . $this->safe($annonce['reference']) . "</p>";
                echo "<p><strong>Statut :</strong> " . $this->safe($annonce['statut']) . "</p>";
                echo "<p><strong>Date :</strong> " . $this->safe($annonce['date_publication']) . "</p>";

                echo "<form method='GET' action='/administrateur/edit-annonce'>
                        <input type='hidden' name='id' value='" . $this->safe($annonce['id']) . "'>
                        <button type='submit'>✏️ Modifier</button>
                      </form>";

                echo "<form method='POST' action='/administrateur/archive-annonce'>
                        <input type='hidden' name='id' value='" . $this->safe($annonce['id']) . "'>
                        <button type='submit'>📦 Archiver</button>
                      </form>";

                echo "</div><hr>";
            }
        }

        echo "</section>";
    }

    // 📝 Formulaire d’annonce
    public function renderFormAnnonce(?array $annonce = null): void
    {
        $action = $annonce ? "/administrateur/edit-annonce?id=" . $this->safe($annonce['id']) : "/administrateur/create-annonce";
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

    // 📋 Liste des candidatures
    public function renderListeCandidatures(array $candidatures): void
    {
        echo "<section class='candidatures-admin'>";
        echo "<h2>📄 Candidatures reçues</h2>";

        if (empty($candidatures)) {
            echo "<p>Aucune candidature enregistrée.</p>";
        } else {
            foreach ($candidatures as $c) {
                echo "<div class='candidature-item'>";
                echo "<h3>" . $this->safe($c['prenom']) . " " . $this->safe($c['nom']) . "</h3>";
                echo "<p><strong>Poste :</strong> " . $this->safe($c['titre']) . "</p>";
                echo "<p><strong>Référence :</strong> " . $this->safe($c['reference']) . "</p>";
                echo "<p><strong>Statut :</strong> " . $this->safe($c['statut']) . "</p>";

                echo "<form method='POST' action='/candidature/updateStatut'>
                        <input type='hidden' name='id' value='" . $this->safe($c['id']) . "'>
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
        echo "<p><strong>Nom :</strong> " . $this->safe($c['prenom']) . " " . $this->safe($c['nom']) . "</p>";
        echo "<p><strong>Poste :</strong> " . $this->safe($c['titre']) . "</p>";
        echo "<p><strong>Référence :</strong> " . $this->safe($c['reference']) . "</p>";
        echo "<p><strong>Date d’envoi :</strong> " . $this->safe($c['date_envoi']) . "</p>";
        echo "<p><strong>Statut :</strong> " . $this->safe($c['statut']) . "</p>";
        echo "<p><strong>Commentaire :</strong> " . $this->safe($c['commentaire_admin']) . "</p>";
        echo "</section><hr>";
    }
}
