<?php

require_once("assets/templates/head.php");
require_once("assets/templates/menu.php");

$action = $_GET['action'] ?? 'accueil';

switch ($action) {

    case 'accueil':
        include 'pages/accueil.php';
        break;

    case 'bureauEtude':
        include 'pages/bureauEtude.php';
        break;

    case 'expertise':
        include 'pages/domaineExpertise.php';
        break;

    case 'recrutement':
        include 'pages/Recrutement.php';
        break;
    
    case 'conseilAstuces':
        include 'pages/ConseilAstuces.php';
        break;

    case 'contact':
        include 'pages/Contact.php';
        break;
    
    case 'mentionsLegales':
        include 'pages/MentionsLegales.php';
        break;
        
    default:
        include 'pages/Page404.php';
        break;
}


require_once("assets/templates/footer.php");
require_once("assets/templates/bulle-flottante.php");
?>