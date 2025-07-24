// Fonction pour animer le compteur de 0 jusqu'à target
function animeCompteur(element) {
    const target = +element.getAttribute('data-target'); // Valeur finale
    const duration = 2000; // durée totale en ms
    const start = 0;
    const increment = target / (duration / 20); // incrément à chaque étape (~20ms)
    let current = start;
  
    const update = () => {
      current += increment;
      if (current >= target) {
        element.textContent = target;
      } else {
        element.textContent = Math.floor(current);
        requestAnimationFrame(update);
      }
    };
  
    update();
  }
  
  // Observer pour déclencher quand le compteur entre dans la vue
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animeCompteur(entry.target);
        observer.unobserve(entry.target); // on arrête de l'observer après l'animation
      }
    });
  }, { threshold: 0.6 }); // déclenche quand 60% du bloc est visible
  
  // Sélectionne tous les compteurs
  document.querySelectorAll('.compteur').forEach(compteur => {
    observer.observe(compteur);
  });
  