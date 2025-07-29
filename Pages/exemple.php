
#P7 {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #121212;
    color: white;
    font-family: Arial, sans-serif;
    margin: 0;


.login-container {
    background-color: #1E1E1E;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    width: 300px;
    text-align: center;
}

h1 {
    margin: 0 0 1rem;
    font-size: 2rem;
}

p {
    color: #A0A0A0;
    margin-bottom: 2rem;
}

.input-group {
    margin-bottom: 1.5rem;
    text-align: left;
}

.input-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #A0A0A0;
}

.input-group input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #333;
    border-radius: 5px;
    background-color: #2D2D2D;
    color: white;
    box-sizing: border-box;
}

.login-button {
    width: 100%;
    padding: 0.75rem;
    background-color: #007BFF;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s;
}

.login-button:hover {
    background-color: #0056b3;
}

.footer-text {
    margin-top: 1rem;
    color: #A0A0A0;
    font-size: 0.875rem;
}

.footer-text a {
    color: #007BFF;
    text-decoration: none;
}

.footer-text a:hover {
    text-decoration: underline;
}
}




<body id="P7">
    <div class="login-container">
        <h1>Se connecter</h1>
        <p>Connectez-vous à votre compte</p>
        <form>
            <div class="input-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-button">Se connecter</button>
        </form>
        <p class="footer-text">Vous n'avez pas de compte ? <a href="#">Insrivez-vous</a></p>
        <p class="footer-text"><a href="#">Mot de passe oublié ?</a></p>
    </div>






    | 🧭 Action (segment 1)  | 🚪 Étape (segment 2)     | 🎯 Contrôleur                 | 🧩 Méthode                            |
|------------------------|--------------------------|-------------------------------|----------------------------------------|
| `accueil`              | *(aucune)*               | *(page statique)*             | `Pages/accueil.php`                    |
| `bureauEtude`          | *(aucune)*               | *(page statique)*             | `Pages/bureauEtude.php`                |
| `domaineExpertise`     | *(aucune)*               | *(page statique)*             | `Pages/domaineExpertise.php`           |
| `recrutement`          | *(aucune)*               | *(page statique)*             | `Pages/recrutement.php`                |
| `contact`              | *(aucune)*               | *(page statique)*             | `Pages/contact.php`                    |

### 👩‍💼 Administration

| `administrateur`       | `dashboard`              | `AdministrateurController`    | `dashboard()`                          |
|                        | `profil`                 |                                | `editProfil()`                         |
|                        | `annonces`               |                                | `viewAnnonces()`                       |
|                        | `create-annonce`         |                                | `createAnnonce()`                      |
|                        | `edit-annonce`           |                                | `editAnnonce(id)`                      |
|                        | `archive-annonce`        |                                | `archiveAnnonce(id)`                   |
|                        | `candidatures`           |                                | `listCandidatures()`                   |
|                        | `candidature`            |                                | `viewCandidature(id)`                  |

### 📢 Annonces globales

| `annonce`              | `create`                 | `AnnonceController`           | `createAnnonce(id)`                    |
|                        | `update`                 |                                | `updateAnnonce()`                      |
|                        | `delete`                 |                                | `deleteAnnonce(id)`                    |
|                        | *(autre)*                |                                | `listAnnonces()`                       |

### 📄 Candidatures globales

| `candidature`          | `submit`                 | `CandidatureController`       | `submitCandidature()`                  |
|                        | `view`                   |                                | `viewCandidature(id)`                  |
|                        | `delete`                 |                                | `deleteCandidature(id)`               |
|                        | `suivi`                  |                                | `suivi()`                              |
|                        | *(autre)*                |                                | `listCandidatures()`                   |

### 📅 Entretiens

| `entretien`            | `planifier`              | `EntretienController`         | `planifierEntretien()`                 |
|                        | `rappel`                 |                                | `envoyerRappel(id)`                    |
|                        | *(autre)*                |                                | `listEntretiens()`                     |

### 👥 Utilisateurs

| `utilisateur`          | `create`                 | `UtilisateurController`       | `createUtilisateur()`                  |
|                        | `edit`                   |                                | `editUtilisateur(id)`                  |
|                        | `login`                  |                                | `loginUtilisateur(id)`                 |
|                        | `logout`                 |                                | `logoutUtilisateur()`                  |
|                        | `update`                 |                                | `updateUtilisateur()`                  |
|                        | `delete`                 |                                | `deleteUtilisateur(id)`                |
|                        | *(autre)*                |                                | `listUtilisateurs()`                   |

### 📆 Calendrier

| `calendrier`           | `semaine`                | `CalendrierController`        | `vueSemaine()`                         |
|                        | `jour`                   |                                | `vueJour(id)`                          |
|                        | `rappel`                 |                                | `rappelDuJour()`                       |
|                        | `rendez-vous`            |                                | `infoRendezVous(id)`                   |
|                        | *(autre)*                |                                | `vueSemaine()`                         |

### 🧑‍🎓 Candidat (front office)

| `candidat`             | `profil`                 | `CandidatController`          | `profil()`                             |
|                        | `update`                 |                                | `update()`                             |
|                        | `upload-cv`              |                                | `uploadCV()`                           |
|                        | `annonces`               |                                | `listAnnonces()`                       |
|                        | `annonce-view`           |                                | `viewAnnonce(id)`                      |
|                        | `postuler`               |                                | `postuler(id)`                         |
|                        | `candidatures`           |                                | `suiviCandidatures()`                  |
|                        | *(autre)*                |                                | `profil()`                             |
