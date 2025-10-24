<?php
// Afficher ce header uniquement si l'utilisateur est connecté
if (!isset($_SESSION)) { session_start(); }
if (empty($_SESSION['utilisateur'])) { return; }

function isActive($path) {
    $current = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $target  = rtrim($path, '/');
    return $current === $target ? 'active' : '';
}

// Prépare les entrées de menu selon le rôle
$role = $_SESSION['utilisateur']['role'] ?? null;
$menuItems = [];
if ($role === 'administrateur') {
    $menuItems = [
        '/administrateur/profil'       => 'Profil',
        '/administrateur/annonces'     => 'Annonces',
        '/administrateur/candidatures' => 'Candidatures',
        '/administrateur/calendrier'   => 'Calendrier',
        '/administrateur/logout'       => 'Déconnexion'
    ];
} elseif ($role === 'candidat') {
    $menuItems = [
        '/candidat/profil'        => 'Profil',
        '/candidat/annonces'      => 'Voir les annonces',
        '/candidat/candidatures'  => 'Mes candidatures',
        '/utilisateur/logout'     => 'Déconnexion'
    ];
}
?>
<header id="menu-connecte" role="banner">
    <!-- Toggle mobile -->
    <input type="checkbox" id="nav-toggle-connecte" class="nav-toggle" aria-hidden="true">

    <div class="bar-top">
        <a href="/accueil" class="logo-link" aria-label="Aller à l'accueil">
            <img src="assets/images/LOGO.svg" alt="Logo de l'entreprise">
        </a>
        <label for="nav-toggle-connecte" class="burger" aria-label="Ouvrir le menu" aria-controls="nav-connecte" aria-expanded="false">
            <span></span><span></span><span></span>
        </label>
    </div>

    <nav id="nav-connecte" class="menu" aria-label="Menu utilisateur">
        <div class="menu-links">
            <?php foreach ($menuItems as $link => $label): ?>
                <a href="<?= htmlspecialchars($link) ?>" class="<?= isActive($link) ?>"><?= htmlspecialchars($label) ?></a>
            <?php endforeach; ?>
        </div>
        <div class="logoconnecte">
            <a href="/accueil" aria-label="Aller à l'accueil">
                <img src="assets/images/LOGO.svg" alt="Logo de l'entreprise">
            </a>
        </div>
    </nav>
</header>