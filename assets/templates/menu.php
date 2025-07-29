
    <header>
    <!-- 🔐 Menu connecté dynamique -->
    <?php
    if (isset($_SESSION['utilisateur'])) {
        $role = $_SESSION['utilisateur']['role'];
        if ($role === 'administrateur' || $role === 'candidat') {
            echo '<nav class="menu">';
            if ($role === 'administrateur') {
                echo '<a href="/administrateur/profil">Profil admin</a>';
                echo '<a href="/administrateur/annonces">Annonces</a>';
                echo '<a href="/administrateur/create-annonce">Créer une annonce</a>';
                echo '<a href="/utilisateur/edit-annonce">Modifier une annonce</a>';
                echo '<a href="/utilisateur/candidatures">Gérer les utilisateurs</a>';
                echo '<a href="/utilisateur/candidature">Gérer un utilisateur</a>';
                echo '<a href="/utilisateur/logout">Déconnexion</a>';
            } elseif ($role === 'candidat') {
                echo '<a href="/candidat/profil" class="' . ($_SERVER['REQUEST_URI'] == '/candidat/profil' ? 'active' : '') . '">Profil</a>';
                echo '<a href="/candidat/annonces">Voir les annonces</a>';
                echo '<a href="/candidat/candidatures">Mes candidatures</a>';
                echo '<a href="/utilisateur/logout">Déconnexion</a>';
            }
            echo '</nav>';
        }
    }
    ?>

    <!-- 🎨 Logo entreprise -->
    <div class="logo">
        <div class="rectangle-header"></div>
        <div class="diagonale-header"></div>
        <a href="/accueil">
            <img src="assets/images/LOGO.svg" alt="Logo de l'entreprise">
        </a>
    </div>

    <!-- 🌍 Menu principal général -->
    <nav class="nav-principal">
        <a href="/accueil">Accueil</a>
        <a href="/bureauEtude">Bureau d'étude</a>
        <a href="/domaineExpertise">Notre expertise</a>
        <a href="/recrutement">Recrutement</a>
        <a href="/contact">Contact</a>
    </nav>
</header>

<!-- 📏 Ligne de séparation visuelle -->
<div class="separateur-header"></div>




<!-- 
    <!-- 🎨 Logo entreprise -->
    <div class="logo">
        <div class="rectangle-header"></div>
        <div class="diagonale-header"></div>
        <a href="/accueil">
            <img src="assets/images/LOGO.svg" alt="Logo de l'entreprise">
        </a>
    </div>



    <!-- 🌍 Menu principal général -->
    <nav class="nav-principal">
        <a href="/accueil">Accueil</a>
        <a href="/bureauEtude">Bureau d'étude</a>
        <a href="/domaineExpertise">Notre expertise</a>
        <a href="/recrutement">Recrutement</a>
        <a href="/contact">Contact</a>
    </nav>

</header>

<!-- 📏 Ligne de séparation visuelle -->
<div class="separateur-header"></div> -->
