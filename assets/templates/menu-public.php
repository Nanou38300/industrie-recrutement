<?php
function isActive($path) {
    $current = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $target = rtrim($path, '/');
    return $current === $target ? 'active' : '';
}
?>

<header id="menu-public">
   
    <!-- 🎨 Logo entreprise -->
    <div class="logo">
        <div class="rectangle-header"></div>
        <div class="diagonale-header"></div>
        <a href="/accueil">
            <img src="assets/images/LOGO.svg" alt="Logo de l\'entreprise">
        </a>
    </div>

    <!-- 🌍 Menu principal général -->
    <nav class="nav-principal">
        <a href="/accueil" class="<?= isActive('/accueil') ?>">Accueil</a>
        <a href="/bureauEtude" class="<?= isActive('/bureauEtude') ?>">Bureau d'étude</a>
        <a href="/domaineExpertise" class="<?= isActive('/domaineExpertise') ?>">Notre expertise</a>
        <a href="/recrutement" class="<?= isActive('/recrutement') ?>">Recrutement</a>
        <a href="/contact" class="<?= isActive('/contact') ?>">Contact</a>
    </nav>


        <!-- 📏 Ligne de séparation visuelle -->
        <div class="separateur-header"></div>


        <div class="icone-login">
            <div>
            <a class="log" href="/utilisateur/login">
            <img src="assets/images/icone-connexion.png" alt="icone de connexion">
            <p>Connexion</p>
            </a>
            </div>

            <div>
            <a class="log" href="/utilisateur/create">
            <img src="assets/images/icone-inscription.png" alt="icone d'inscription">
            <p>Inscription</p>
            </a>
        </div>
        </div>
</header>
