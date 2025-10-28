<?php
// Sécurisation / valeurs par défaut
$photo    = $candidat['photo_profil'] ?? 'assets/images/default.jpg';
$photoUrl = '/' . ltrim($photo, '/');

$nom      = htmlspecialchars($candidat['nom']     ?? '', ENT_QUOTES, 'UTF-8');
$prenom   = htmlspecialchars($candidat['prenom']  ?? '', ENT_QUOTES, 'UTF-8');
$email    = htmlspecialchars($candidat['email']   ?? '', ENT_QUOTES, 'UTF-8');
$tel      = htmlspecialchars($candidat['telephone'] ?? '', ENT_QUOTES, 'UTF-8');
$poste    = htmlspecialchars($entretien['poste']  ?? ($candidat['poste'] ?? ''), ENT_QUOTES, 'UTF-8');

$dateEnt  = htmlspecialchars($entretien['date_entretien'] ?? '', ENT_QUOTES, 'UTF-8');
$heureEnt = htmlspecialchars($entretien['heure']         ?? '', ENT_QUOTES, 'UTF-8');
$typeEnt  = htmlspecialchars($entretien['type']          ?? '', ENT_QUOTES, 'UTF-8');
$lienVisio= $entretien['lien_visio'] ?? '';
$comment  = nl2br(htmlspecialchars($entretien['commentaire'] ?? '', ENT_QUOTES, 'UTF-8'));

// CV (on stocke uniquement le "nom de fichier" en BDD)
$cvFile   = $candidat['cv'] ?? '';
$cvName   = htmlspecialchars(basename($cvFile), ENT_QUOTES, 'UTF-8');
$cvAbs    = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/') . '/uploads/' . basename($cvFile);
$cvUrl    = '/uploads/' . $cvName;
?>

<section class="rdv-detail">
  <h2>Détail de l’entretien</h2>

  <!-- FICHE CANDIDAT -->
  <div class="fiche-candidat" style="display:flex;gap:16px;align-items:flex-start;">
    <img src="<?= htmlspecialchars($photoUrl, ENT_QUOTES, 'UTF-8') ?>"
         class="photo-candidat"
         alt="Photo"
         style="max-width:120px;border-radius:8px;">

    <div>
      <h3><?= $prenom ?> <?= $nom ?></h3>
      <p><strong>Email :</strong> <?= $email ?></p>
      <p><strong>Téléphone :</strong> <?= $tel ?></p>
      <p><strong>Poste :</strong> <?= $poste ?></p>

      <?php if ($cvFile !== ''): ?>
        <?php if (is_file($cvAbs)): ?>
          <p><strong>CV :</strong>
            <a href="<?= htmlspecialchars($cvUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Voir le CV</a>
          </p>
        <?php else: ?>
          <p><strong>CV :</strong>
            <em>Fichier introuvable : <?= $cvName ?></em>
          </p>
        <?php endif; ?>
      <?php else: ?>
        <p><strong>CV :</strong> <em>Non fourni</em></p>
      <?php endif; ?>
    </div>
  </div>

  <!-- INFOS RDV -->
  <div class="rdv-info" style="margin-top:16px;">
    <p><strong>Date :</strong> <?= $dateEnt ?></p>
    <p><strong>Heure :</strong> <?= $heureEnt ?></p>
    <p><strong>Type :</strong> <?= $typeEnt ?></p>
    <p><strong>Lien Visio :</strong>
      <?php if (!empty($lienVisio)): ?>
        <a href="<?= htmlspecialchars($lienVisio, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Accéder</a>
      <?php else: ?>
        <em>Non renseigné</em>
      <?php endif; ?>
    </p>
    <p><strong>Commentaire :</strong> <?= $comment ?></p>
  </div>

  <!-- ACTIONS -->
  <div class="rdv-actions" style="display:flex;gap:12px;margin-top:16px;">

    <!-- Bouton Modifier -->
    <form method="GET" action="/administrateur/edit-entretien" class="form-edit">
      <input type="hidden" name="id" value="<?= htmlspecialchars((string)($entretien['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      <button type="submit" class="btn-edit">
        <img src="/assets/images/stylo.png" alt="modifier" class="icon-btn">
        Modifier
      </button>
    </form>

    <!-- Bouton Supprimer -->
    <form method="POST"
          action="/administrateur/delete-entretien"
          onsubmit="return confirm('Confirmer la suppression de ce rendez-vous ?');"
          class="form-delete">
      <input type="hidden" name="id" value="<?= htmlspecialchars((string)($entretien['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      <button type="submit" class="btn-delete">
        <img src="/assets/images/poubelle.png" alt="supprimer" class="icon-btn">
        Supprimer
      </button>
    </form>

  </div>
</section>