<?php

namespace App\View;

class SharedView
{
    public static function displayMenu(): void 
    {
        echo '
        <header>
            <div class="logo">
                <div class="rectangle-header"></div>
                <div class="diagonale-header"></div>
                <a href="index.php?action=accueil">
                    <img src="assets/images/LOGO.svg" alt="Logo de l\'entreprise">
                </a>
            </div>

            <nav class="menu-principal">
                <a href="index.php?action=accueil">Accueil</a>
                <a href="index.php?action=bureauEtude">Bureau d\'étude</a>
                <a href="index.php?action=domaineExpertise">Notre expertise</a>
                <a href="index.php?action=recrutement">Recrutement</a>
                <a href="index.php?action=contact">Contact</a>
            </nav>

            <nav class="menu-espace">';
        
        if (!empty($_SESSION)) {
            echo '
                <a href="index.php?action=user&step=create">Créer un utilisateur</a>
                <a href="index.php?action=user&step=edit">Gérer le profil</a>
                <a href="index.php?action=user&step=logout">Déconnexion</a>';
        } else {
            echo '
                <a href="index.php?action=user&step=login">Connexion</a>
                <a href="index.php?action=user&step=create">Inscription</a>';
        }

        echo '
            </nav>
        </header>
        <div class="separateur-header"></div>';
    }
}
