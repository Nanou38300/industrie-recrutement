<script>
document.querySelectorAll('.toggle-details').forEach(button => {
  button.addEventListener('click', () => {
    const annonce = button.closest('.annonce');
    const icon = button.querySelector('.icon');

    annonce.classList.toggle('active');
    const isActive = annonce.classList.contains('active');

    icon.src = isActive
      ? 'assets/images/fleche-remontee.png' // flèche vers le haut
      : 'assets/images/fleche-deroulante.png'; // flèche vers le bas

    icon.alt = isActive
      ? 'Flèche remontée'
      : 'Flèche déroulante';
  });
});

</script>

