<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body id="P5">
    
<section class="hero">
    <img src="assets/images/P5_hero.png" loading= "lazy" alt="une image d'un soudeur en position">
</section>


<section class="P">
    <section class="coordonnees">

    <h2>NOS COORDONNÉES</h2>

            <section>
                <p>TÉLÉPHONE</p>
                <div class="bloc-co">
                    <img src="./assets/images/icone-contact-tel.png" alt="icone de téléphone">
                    <p>04.34.35.36.37</p>
                </div>
            </section>

            <section>
                <p>MAIL</p>
                <div class="bloc-co">
                    <img src="./assets/images/icone-contact-email.png" alt="icone de mail">
                    <p>contact@tcs-industries.fr</p>
                </div>
            </section>

            <section>
                    <p>ADRESSE</p>
                    <div class="bloc-co">
                        <img src="./assets/images/icone-contact-adresse.png" alt="icone de localisation">
                        <p>80 rue de l'indutrie</p>
                        <p>38300 NIVOLAS-VERMELLE</p>
                    </div>
            </section>
    </section>
        




        
        <section class="form-container">
            <section class="header">
                <div>
                    <p class="company-name">TCS Chaudronnerie</p>
                    <h2 class="title">Contactez-nous</h2>
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


                <button class="btn" type="submit">Envoyer</button>
            </form>
        </section> 

</section>



</body>
</html>