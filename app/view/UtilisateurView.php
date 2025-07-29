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
            <div>
                <img src="./assets/images/icone_fleche_blanche.png" alt="une icone fleche">
                <button class="btn-insc">SE CONNECTER</button>
            </div>
        </div>

                <form class="form-inscription" action="/utilisateur/create" method="POST">
                    <h1>CRÉATION DE COMPTE</h1>
                        <label>Nom: <input type="text" name="nom" required></label><br>
                        <label>Prénom: <input type="text" name="prenom" required></label><br>
                        <label>Email: <input type="email" name="email" required></label><br>
                        <label>Mot de passe: <input type="password" name="mot_de_passe" required></label><br>
                        <label>Date de naissance: <input type="date" name="date_naissance" required></label><br>
                        <label>Téléphone: <input type="number" name="telephone" required></label><br>
                        <button type="submit">CRÉER UN COMPTE</button>
                    </form>      
                </section>  
            ';
    }
  


public function loginForm()
    {
        echo '<h1>Connexion</h1>
        <section class="connexion">
            <img class="img-connexion" src="./assets/images/P4_soudeurPosition.png" alt="un soudeur.">

            <form class="form-connexion" action="utilisateur/login" method="post">
                            
                <label>Email</label>
                <input type="email" name="email" required >
                
                <label>Mot de passe</label>
                <input type="passeword" name="mot de passe" required >
                
                <button type="submit">Se connecter</button>
            </form>
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
