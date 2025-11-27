<?php

namespace App\View;

class UtilisateurView
{
    // Sécurise l'affichage HTML
    private function safe($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }

    // Génère / récupère le token CSRF
    private function getCsrfToken(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    // Champ hidden CSRF prêt à être injecté dans tous les formulaires POST
    private function csrfField(): string
    {
        return "<input type='hidden' name='csrf_token' value='" . $this->safe($this->getCsrfToken()) . "'>";
    }

    public function displayUtilisateurs(array $utilisateurs): void
    {
        echo "<h2>Liste des utilisateurs</h2>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Nom</th><th>Prénom</th><th>Email</th><th>Téléphone</th></tr>";

        foreach ($utilisateurs as $utilisateur) {
            echo "<tr>
                <td>" . $this->safe($utilisateur['id'] ?? '') . "</td>
                <td>" . $this->safe($utilisateur['nom'] ?? '') . "</td>
                <td>" . $this->safe($utilisateur['prenom'] ?? '') . "</td>
                <td>" . $this->safe($utilisateur['email'] ?? '') . "</td>
                <td>" . $this->safe($utilisateur['telephone'] ?? '') . "</td>
            </tr>";
        }

        echo "</table>";
    }

    public function displayInsertForm(): void
    {
        echo '
        <section class="inscription">

            <div class="superposition-insc">
                <img loading="lazy" class="img-inscription" src="./assets/images/P1_soudeur.webp" alt="une image d\'un soudeur.">
                <div class="cadre-img">
                    <img loading="lazy" src="./assets/images/icone_fleche_blanche.webp" alt="une icone fleche">
                    <a href="/utilisateur/login" class="btn-insc">SE CONNECTER</a>
                </div>
            </div>

            <form class="form-inscription" action="/utilisateur/create" method="POST">
        ';
        

        // 🔐 CSRF
        echo $this->csrfField();

        echo '
                <h1 class="titre-inscription">CRÉATION DE COMPTE</h1>
                <label>Nom: <input type="text" name="nom" required></label><br>
                <label>Prénom: <input type="text" name="prenom" required></label><br>
                <label>Email: <input type="email" name="email" required></label><br>
                <label>Mot de passe: <input type="password" name="mot_de_passe" required></label><br>
                <label>Date de naissance: <input type="date" name="date_naissance" required></label><br>
                <label>Téléphone: <input type="text" name="telephone" required></label><br>

';

        if (!empty($_SESSION['flash'])) {
            echo "<div class='flash flash-error'>" . $this->safe((string)$_SESSION['flash']) . "</div>";
            unset($_SESSION['flash']);
        }

        echo '

                <button class="btn-crea" type="submit">CRÉER UN COMPTE</button>
            </form>      
        </section>  
        ';
    }

    public function loginForm(): void
    {
        echo '
        <section class="connexion">
            <form class="form-connexion" action="/utilisateur/login" method="POST">
        ';

        // 🔐 CSRF
        echo $this->csrfField();

        echo '
                <h1 class="titre-co">CONNEXION</h1>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>

                <div class="options-co">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember_me">
                        <span>Se souvenir de moi</span>
                    </label>

                    <a href="/utilisateur/mot-de-passe-oublie" class="lien-mdp-oublie">Mot de passe oublié&nbsp;?</a>
                </div>

                <button class="btn-crea" type="submit">Se connecter</button>
            </form>
            
            <div class="superposition-co">
                <img loading="lazy" class="img-connexion" src="./assets/images/P4_soudeurPosition.webp" alt="un soudeur.">

                <div class="cadre-co">
                    <a href="/utilisateur/create" class="btn-co">CREER UN COMPTE</a>
                    <img loading="lazy" src="./assets/images/icone_fleche_blanche_inverse.webp" alt="une icone fleche">
                </div>
            </div>
        </section>
        ';
    }

    public function displayUpdateForm(array $utilisateur): void
    {
        echo '
            <h2>Modifier un utilisateur</h2>
            <form method="POST" action="/utilisateur/update">
        ';

        // 🔐 CSRF
        echo $this->csrfField();

        echo '
                <input type="hidden" name="id" value="' . $this->safe($utilisateur['id'] ?? '') . '">
                <label>Nom:</label>
                <input type="text" name="nom" value="' . $this->safe($utilisateur['nom'] ?? '') . '"><br>

                <label>Prénom:</label>
                <input type="text" name="prenom" value="' . $this->safe($utilisateur['prenom'] ?? '') . '"><br>

                <label>Email:</label>
                <input type="email" name="email" value="' . $this->safe($utilisateur['email'] ?? '') . '"><br>

                <label>Téléphone:</label>
                <input type="text" name="telephone" value="' . $this->safe($utilisateur['telephone'] ?? '') . '"><br>

                <input type="submit" value="Mettre à jour">
            </form>
        ';
    }
}
