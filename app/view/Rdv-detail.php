<section class="rdv-detail">
    <h2>Détail de l’entretien</h2>
    <div class="fiche-candidat">
        <img src="/<?= htmlspecialchars($candidat['photo_profil'] ?? 'assets/images/default.jpg') ?>" class="photo-candidat" alt="Photo">
        <div>
            <h3><?= htmlspecialchars($candidat['prenom']) ?> <?= htmlspecialchars($candidat['nom']) ?></h3>
            <p><strong>Email :</strong> <?= htmlspecialchars($candidat['email']) ?></p>
            <p><strong>Téléphone :</strong> <?= htmlspecialchars($candidat['telephone']) ?></p>
            <p><strong>Poste :</strong> <?= htmlspecialchars($candidat['poste']) ?></p>
        </div>
    </div>
    <div class="rdv-info">
        <p><strong>Date :</strong> <?= htmlspecialchars($entretien['date']) ?></p>
        <p><strong>Heure :</strong> <?= htmlspecialchars($entretien['heure']) ?></p>
        <p><strong>Objet :</strong> <?= htmlspecialchars($entretien['objet']) ?></p>
    </div>
</section>


    <div class="rdv-info">
        <p><strong>Date :</strong> <?= htmlspecialchars($rdv['date']) ?></p>
        <p><strong>Heure :</strong> <?= htmlspecialchars($rdv['heure']) ?></p>
        <p><strong>Objet :</strong> <?= htmlspecialchars($rdv['objet']) ?></p>
    </div>
</section>

<style>
.fiche-candidat {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: #f9f9f9;
    padding: 1rem;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.photo-candidat {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
}
.rdv-info {
    margin-top: 2rem;
    background: #fff;
    padding: 1rem;
    border: 1px solid #ccc;
    border-radius: 8px;
}
</style>
