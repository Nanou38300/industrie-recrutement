<section class="rdv-detail">
    <h2>Détail de l’entretien</h2>

    <div class="fiche-candidat">
        <img src="/<?= htmlspecialchars($candidat['photo_profil'] ?? 'assets/images/default.jpg') ?>" class="photo-candidat" alt="Photo">
        <div>
            <h3><?= htmlspecialchars($candidat['prenom'] ?? '') ?> <?= htmlspecialchars($candidat['nom'] ?? '') ?></h3>
            <p><strong>Email :</strong> <?= htmlspecialchars($candidat['email'] ?? '') ?></p>
            <p><strong>Téléphone :</strong> <?= htmlspecialchars($candidat['telephone'] ?? '') ?></p>
            <p><strong>Poste :</strong> <?= htmlspecialchars($entretien['poste'] ?? '') ?></p>
        </div>
    </div>

    <div class="rdv-info">
        <p><strong>Date :</strong> <?= htmlspecialchars($entretien['date_entretien'] ?? '') ?></p>
        <p><strong>Heure :</strong> <?= htmlspecialchars($entretien['heure'] ?? '') ?></p>
        <p><strong>Type :</strong> <?= htmlspecialchars($entretien['type'] ?? '') ?></p>
        <p><strong>Lien Visio :</strong> 
            <?php if (!empty($entretien['lien_visio'])): ?>
                <a href="<?= htmlspecialchars($entretien['lien_visio']) ?>" target="_blank">Accéder</a>
            <?php else: ?>
                <em>Non renseigné</em>
            <?php endif; ?>
        </p>
        <p><strong>Commentaire :</strong> <?= nl2br(htmlspecialchars($entretien['commentaire'] ?? '')) ?></p>
    </div>

    <div class="rdv-actions">
        <form method="GET" action="/administrateur/edit-entretien" style="display:inline-block;">
            <input type="hidden" name="id" value="<?= htmlspecialchars($entretien['id']) ?>">
            <button type="submit">✏️ Modifier</button>
        </form>

        <form method="POST" action="/administrateur/delete-entretien" style="display:inline-block;" onsubmit="return confirm('Confirmer la suppression de ce rendez-vous ?');">
            <input type="hidden" name="id" value="<?= htmlspecialchars($entretien['id']) ?>">
            <button type="submit">🗑️ Supprimer</button>
        </form>
    </div>
</section>

<style>
.fiche-candidat {
    margin: 1rem 1rem 2rem 15rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    background: #C9AB89;
    padding: 1rem;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.photo-candidat {
    margin: 1rem 1rem 2rem 15rem;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
}
.rdv-info {
    margin: 1rem 1rem 2rem 15rem;
    margin-top: 2rem;
    background: #fff;
    padding: 1rem;
    border: 1px solid #ccc;
    border-radius: 8px;
}
.rdv-actions {
    margin: 1rem 1rem 2rem 15rem;
    margin-top: 1rem;
    display: flex;
    gap: 1rem;
}
.rdv-actions button {
    margin: 1rem 1rem 2rem 15rem;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    background-color: #1A3E38;
    color: white;
}
.rdv-actions button:hover {
    background-color: #C9AB89;
}
</style>
