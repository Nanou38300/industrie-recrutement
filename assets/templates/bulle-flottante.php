<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Mon site</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>

  <!-- ... contenu de ta page ... -->

  <!-- La bulle flottante -->
  <a href="tel:+33123456789" class="bulle-flottante__btn" data-phone="+33123456789">
    📞
  </a>

  <!-- 🔽 ICI, juste avant </body> -->
  <div class="bulle-flottante__modal" hidden>
    <div class="bulle-flottante__modal-backdrop"></div>
    <div class="bulle-flottante__modal-content">
      <p>
        Souhaitez-vous enregistrer le contact de l'entreprise dans votre téléphone ?
        <small>(Pour vous permettre de nous recontacter facilement.)</small>
      </p>
      <div class="bulle-flottante__modal-actions">
        <button type="button" data-action="call-save">Appeler & enregistrer</button>
        <button type="button" data-action="call">Juste appeler</button>
        <button type="button" data-action="close">Annuler</button>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="/assets/js/bulle-flottante.js"></script>
</body>
</html>