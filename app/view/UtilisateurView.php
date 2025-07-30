<?php
Namespace App\View;



class UtilisateurView
{
    public function displayUtilisateurs(array $utilisateurs): void
    {
        echo "<h2>Liste des utilisateurs</h2>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Nom</th><th>Prénom</th><th>Email</th><th>Téléphone</th></tr>";

        foreach ($utilisateurs as $utilisateur) {
            echo "<tr>
                <td>{$utilisateur['id']}</td>
                <td>{$utilisateur['nom']}</td>
                <td>{$utilisateur['prenom']}</td>
                <td>{$utilisateur['email']}</td>
                <td>{$utilisateur['telephone']}</td>
            </tr>";
        }

        echo "</table>";
    }

    public function displayInsertForm()
    {
        echo '
        <section class="inscription">

        <div class="superposition-insc">
            <img class="img-inscription" src="./assets/images/P1_soudeur.jpg" alt="une image d\'un soudeur.">
            <div class="cadre-img">
                <img src="./assets/images/icone_fleche_blanche.png" alt="une icone fleche">
                <a href="/utilisateur/login" class="btn-insc">SE CONNECTER</a>
            </div>
        </div>

                <form class="form-inscription" action="/utilisateur/create" method="POST">
                    <h1 class="titre-inscription">CRÉATION DE COMPTE</h1>
                        <label>Nom: <input type="text" name="nom" required></label><br>
                        <label>Prénom: <input type="text" name="prenom" required></label><br>
                        <label>Email: <input type="email" name="email" required></label><br>
                        <label>Mot de passe: <input type="password" name="mot_de_passe" required></label><br>
                        <label>Date de naissance: <input type="date" name="date_naissance" required></label><br>
                        <label>Téléphone: <input type="int" name="telephone" required></label><br>

                        <button class="btn-crea" type="submit">CRÉER UN COMPTE</button>
                    </form>      
                </section>  
            ';
    }
  


public function loginForm()
{
    echo '
    <section class="connexion">
        <form class="form-connexion" action="utilisateur/login" method="post">
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
            <img class="img-connexion" src="./assets/images/P4_soudeurPosition.png" alt="un soudeur.">

            <div class="cadre-co">
                <a href="/utilisateur/create" class="btn-co">CREER UN COMPTE</a>

                <img src="./assets/images/icone_fleche_blanche_inverse.png" alt="une icone fleche">
            </div>
        </div>
    </section>
    ';
}

    public function displayUpdateForm(array $utilisateur): void
    {
        echo '
            <h2>Modifier un utilisateur</h2>
            <form method="POST">
                <input type="hidden" name="id" value="' . $utilisateur['id'] . '">
                <label>Nom:</label><input type="text" name="nom" value="' . $utilisateur['nom'] . '"><br>
                <label>Prénom:</label><input type="text" name="prenom" value="' . $utilisateur['prenom'] . '"><br>
                <label>Email:</label><input type="email" name="email" value="' . $utilisateur['email'] . '"><br>
                <label>Téléphone:</label><input type="text" name="telephone" value="' . $utilisateur['telephone'] . '"><br>
                <input type="submit" value="Mettre à jour">
            </form>
        ';
    }
}
?>
