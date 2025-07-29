<!DOCTYPE html>
<html lang="fr">

<body>


<section class="bulle">
        
    <section class="floating-bubble" aria-label="Menu d'accès rapide">

        <nav class="actions" id="actionButtons" aria-label="Actions rapides">
            <!-- Nouveau -->
            <div id="vcfNotice" class="vcf-notice" style="display: none;">
            📇 Contact en cours d'enregistrement...
</div>
            <a class="action-button call-vcf" title="Appeler et enregistrer le contact" aria-label="Appeler et enregistrer">
            <img src="./assets/images/bulle-appel.png" alt="Icône téléphone" width="24" height="24" loading="lazy">
            </a>

            <a href="sms:+1234567890" class="action-button" title="Envoyer un message" aria-label="Message">
            <img src="./assets/images/bulle-whattsapp.png" alt="Icône message" width="24" height="24" loading="lazy">
            </a>
            <a href="mailto:exemple@mail.com" class="action-button" title="Envoyer un mail" aria-label="Email">
            <img src="./assets/images/bulle-email.png" alt="Icône email" width="24" height="24" loading="lazy">
            </a>
            <a href="https://maps.google.com?q=Votre+Adresse" target="_blank" class="action-button" title="Localiser" aria-label="Localiser">
            <img src="./assets/images/bulle-gps.png" alt="Icône localisation" width="24" height="24" loading="lazy">
            </a>
        </nav>

        <button class="main-button" id="mainBubbleBtn" aria-label="Ouvrir le menu">
            <img src="./assets/images/bulle-info.png" alt="une icone de bulle" width="28" height="28" loading="lazy">
        </button>
    </section>
</section>

<script src="/assets/js/bulle-flottante.js"></script>

    