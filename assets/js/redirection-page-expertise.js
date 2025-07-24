// Sélectionne tous les éléments avec la classe .scroll-link (liens internes)
document.querySelectorAll('.scroll-link').forEach(link => {
  
    // Pour chaque lien, on ajoute un écouteur de clic
    link.addEventListener('click', function(e) {
      
      // Empêche le comportement par défaut (saut direct ou rechargement)
      e.preventDefault();
      
      // Récupère la valeur de l'attribut href (ex : "#article-chimie")
      const targetId = this.getAttribute('href');
      
      // Sélectionne l’élément dans la page correspondant à l’ID ciblé
      const target = document.querySelector(targetId);
      
      // Si l’élément existe bien
      if (target) {
        
        // Scroll fluide jusqu'à l'élément
        target.scrollIntoView({ behavior: 'smooth' });
        
        // Met à jour l'URL dans la barre d’adresse sans recharger la page
        history.pushState(null, '', targetId);
      }
    });
  });
  