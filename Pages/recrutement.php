<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


</head>
<body id="P4">

<?php if (!empty($_SESSION['flash'])): ?>
    <div class="flash success">
        <?= htmlspecialchars((string)$_SESSION['flash'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php unset($_SESSION['flash'], $_SESSION['flash_type']); ?>
<?php endif; ?>
    
<section class="hero">
    <img src="assets/images/P4-hero.webp" loading= "lazy" alt="une image d'un soudeur en position">
</section>

<h1>Notre processus de recrutement</h1>
<section class="picto-processus">
  
    <div class="etape-process">
        <img src="assets/images/P4_pictodossier.webp" alt="un picto de dossier">
        <p class="texte-beige">Tri des candidatures pour identifier les profils correspondant aux critères.</p>
        <p>Délai entre 3 à 7 jours.
        (Analyse des CV et tri des candidatures).</p>
    </div>
    
    <img class="fleche" src="assets/images/P4_fleche.webp" alt="un picto flèche">


    <div class="etape-process">
        <img src="assets/images/P4_pictotri.webp" alt="un picto de tris de candidatures">
        <p class="texte-beige">Entretiens individuels.</p>
        <p>Délai entre 7 à 10 jours.
        (Organisation et réalisation des entretiens, incluant la coordination des plannings).</p>
    </div>

    <img class="fleche" src="assets/images/P4_fleche.webp" alt="un picto flèche">

 
    <div class="etape-process">
        <img src="assets/images/P4_pictoentretien.webp" alt="un picto de deux personnes qui discutent">
        <p class="texte-beige">Entretiens individuels.</p>
        <p>Délai entre 7 à 10 jours.
        (Organisation et réalisation des entretiens, incluant la coordination des plannings).</p>
    </div>

    <img class="fleche" src="assets/images/P4_fleche.webp" alt="un picto flèche">

 
    <div class="etape-process">
        <img src="assets/images/P4_pictochoix.webp" alt="un picto de trois personnes les mains en l'air tenant deux pieces de puzels">
        <p class="texte-beige">Choix finale
        & Intégration.</p>
        <p>Délai entre 7 à 10 jours.
        (Prise de décision, préparation des documents, organisation des formations et accompagnement du nouvel employé dans ses premiers jours ).</p>
    </div>
</section>

<section  class="template-titre1">
        <div >
        <p class="tcsC">TCS Chaudronnerie</p>
        <h2 class="titre">CRÉATION DE COMPTE </h2>
        </div>
    <div class="separateur"></div>


    <article>
        <p>Le processus de recrutement prend entre 24 jours et 37 jours.</p>

        <p>Pour accéder aux informations relatives au suivi de votre candidature, il est nécessaire de créer un compte personnel sur notre plateforme. Ce compte vous permettra de consulter l'état de votre dossier, d'obtenir des mises à jour en temps réel et de rester informé de l'avancée de votre candidature. La création d'un compte est rapide et sécurisée, et elle garantit une gestion personnalisée et confidentielle de votre processus de recrutement.</p>
    </article>

    <a href="/utilisateur/create"><button class="btn1">CRÉATION COMPTE</button></a>
</section>

<section  class="template-titre1">
        <div >
        <p class="tcsC">TCS Chaudronnerie</p>
        <h2 class="titre">OFFRE D'EMPLOI</h2>
        </div>
    <div class="separateur"></div>

    <img src="assets/images/P4_chaudronnier.webp" alt="une image représentant un chaudronnier">
    <article class="article"> 
        <h3>Chaudronnier H/F</h3>
        <p>C.D.I</p>
        <p>25.03.2025</p>
        <p>Rattaché au responsable de production, vous mettez en œuvre des techniques de pliage, cintrage à froid, roulage et vérinage pour former des tôles. Ces pièces ainsi façonnées sont assemblées par soudage et boulonnage pour créer des structures métalliques.</p>
        <a href="/utilisateur/create"><button class="btn1">POSTULER</button></a>
    </article>



    <img class="img2" src="assets/images/P4_soudeur2.webp" alt="une image représentant un chaudronnier">
    <article class="article2"> 
        <h3>Soudeur H/F</h3>
        <p>C.D.I</p>
        <p>14.03.2025</p>
        <p>Rattaché au responsable de production, vous mettez en œuvre des techniques de pliage, cintrage à froid, roulage et vérinage pour former des tôles. Ces pièces ainsi façonnées sont assemblées par soudage et boulonnage pour créer des structures métalliques.</p>
        <a href="/utilisateur/create"><button class="btn2">POSTULER</button></a>
    </article>
  

    <img src="assets/images/P4_soudeurPosition.webp" alt="une image représentant un chaudronnier">
    <article class="article3"> 
        <h3>Soudeur H/F</h3>
        <p>C.D.I</p>
        <p>09.03.2025</p>
        <p>Rattaché au responsable de production, vous mettez en œuvre des techniques de pliage, cintrage à froid, roulage et vérinage pour former des tôles. Ces pièces ainsi façonnées sont assemblées par soudage et boulonnage pour créer des structures métalliques.</p>
        <a href="/utilisateur/create"><button class="btn1">POSTULER</button></a>
    </article>
    </section>

<section class="form-spontanne">
    <section class="header">
        <div>
            <p class="company-name">TCS Chaudronnerie</p>
            <h2 class="title">Candidature spontanée</h2>
        </div>
        <div class="separator"></div>
    </section>

    <form action="candidature.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom" required>
        </div>

        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" name="prenom" id="prenom" required>
        </div>

        <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" required>
        </div>

        <div class="form-group">
            <label for="message">Message :</label>
            <textarea name="message" id="message" rows="5" required class="message"></textarea>
        </div>

        <div class="form-group">
            <label for="cv">CV (PDF ou DOC) :</label>
            <input type="file" name="cv" id="cv" accept=".pdf,.doc,.docx" required>
        </div>

        <div class="rgpd">
            <input type="checkbox" name="rgpd" id="rgpd" required>
            <label for="rgpd">J’accepte que mes données soient utilisées dans le cadre de ce recrutement, conformément à la politique de confidentialité.</label>
        </div>

        <button class="btn" type="submit">Envoyer</button>
    </form>
</section> 



</body>
</html>
