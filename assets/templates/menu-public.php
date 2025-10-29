<?php
// Afficher ce header uniquement si l'utilisateur n'est PAS connecté
if (!isset($_SESSION)) { session_start(); }
if (!empty($_SESSION['utilisateur'])) { return; }

function isActive($path) {
    $current = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $target  = rtrim($path, '/');
    return $current === $target ? 'active' : '';
}
?>
<header id="menu-public" role="banner">
    <!-- Toggle mobile -->
    <input type="checkbox" id="nav-toggle-public" class="nav-toggle" aria-hidden="true">


    <div class="bar-top">
        <div class="logo">
            <div class="rectangle-header" aria-hidden="true"></div>
            <div class="diagonale-header" aria-hidden="true"></div>
            <a href="/accueil" class="logo-link" aria-label="Aller à l'accueil">
                <img src="assets/images/TCS.svg" alt="Logo de l'entreprise">
            </a>
        </div>

        <label for="nav-toggle-public" class="burger" aria-label="Ouvrir le menu" aria-controls="nav-public" aria-expanded="false">
            <span></span><span></span><span></span>
        </label>
    </div>

    <nav id="nav-public" class="nav-principal" aria-label="Navigation principale">
        <a href="/accueil"           class="<?= isActive('/accueil') ?>">Accueil</a>
        <a href="/bureauEtude"       class="<?= isActive('/bureauEtude') ?>">Bureau d'étude</a>
        <a href="/domaineExpertise"  class="<?= isActive('/domaineExpertise') ?>">Notre expertise</a>
        <a href="/recrutement"       class="<?= isActive('/recrutement') ?>">Recrutement</a>
        <a href="/contact"           class="<?= isActive('/contact') ?>">Contact</a>
        
        <div class="separateur-header" aria-hidden="true"></div>

        <div class="icone-login">
            <a class="log" href="/utilisateur/login">
                <img src="assets/images/icone-connexion.png" alt="Connexion">
                <p>Connexion</p>
            </a>
            <a class="log" href="/utilisateur/create">
                <img src="assets/images/icone-inscription.png" alt="Inscription">
                <p>Inscription</p>
            </a>
        </div>
 
    </nav>
</header>