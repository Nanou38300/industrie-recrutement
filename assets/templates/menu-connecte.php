<?php
function isActive($path) {
    $current = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $target = rtrim($path, '/');
    return $current === $target ? 'active' : '';
}
?>

<header id="menu-connecte">
    <?php
    if (isset($_SESSION['utilisateur'])) {
        $role = $_SESSION['utilisateur']['role'];
        $menuItems = [];

        if ($role === 'administrateur') {
            $menuItems = [
                '/administrateur/profil' => 'Profil admin',
                '/administrateur/annonces' => 'Annonces',
                '/administrateur/create-annonce' => 'Créer une annonce',
                '/administrateur/edit-annonce' => 'Modifier une annonce',
                '/administrateur/candidatures' => 'Gérer les utilisateurs',
                '/administrateur/candidature' => 'Gérer un utilisateur',
                '/administrateur/logout' => 'Déconnexion'
            ];
        } elseif ($role === 'candidat') {
            $menuItems = [
                '/candidat/profil' => 'Profil',
                '/candidat/annonces' => 'Voir les annonces',
                '/candidat/candidatures' => 'Mes candidatures',
                '/utilisateur/logout' => 'Déconnexion'
            ];
        }

        echo '<nav class="menu">';
        foreach ($menuItems as $link => $label) {
            echo '<a href="' . $link . '" class="' . isActive($link) . '">' . $label . '</a>';
        }
        echo '</nav>';
    }

    echo '<div class="logo">
        <div class="rectangle-header"></div>
        <div class="diagonale-header"></div>
        <a href="/accueil">
            <img src="assets/images/LOGO.svg" alt="Logo de l\'entreprise">
        </a>
    </div>'
    
    ?>
</header>