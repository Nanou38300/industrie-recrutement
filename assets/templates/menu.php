<header>


<div class="logo">
    <div class="rectangle-header"></div>
    <div class="diagonale-header"> </div>
    <a href="/accueil">
        <img src="assets/images/LOGO.svg" alt="Logo de l'entreprise">
</a>
 </div>



    <nav>
        <a href="/accueil">Accueil</a>
        <a href="/bureauEtude">Bureau d'étude</a>
        <a href="/domaineExpertise">Notre expertise</a>
        <a href="recrutement">Recrutement</a>
        <a href="/contact/utilisateur/create">Contact</a>
    </nav>


    <nav class="menu">
        <?php if($_SESSION): ?>
    <a href="utilisateur/create">Créer un utilisateur</a>
        <a href="/utilisateur/manage">Gérer les utilisateurs</a>
        <a href="/utilisateur/logout">Déconnexion</a>
        <?php else: ?>  
        <a href="/utilisateur/login">Connexion</a>
        <a href="/utilisateur/create">Inscription</a>
        <?php endif; ?>
    </nav>
</header>
<div class="separateur-header"></div>



