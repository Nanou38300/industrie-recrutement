// Attend que tout le contenu de la page soit chargé avant d'exécuter le script
document.addEventListener("DOMContentLoaded", () => {

    // Sélectionne le bouton permettant de remonter en haut de la page
    const buttonTop = document.querySelector(".btn-scroll"); 

    // Écoute l'événement de défilement sur la fenêtre
    window.addEventListener("scroll", () => {

        // Si l'utilisateur a défilé de plus de 200px, on affiche le bouton
        if (window.scrollY > 200) { 
            buttonTop.style.display = "block";

        // Sinon, on le masque
        } else {
            buttonTop.style.display = "none";
        }
    });

    // Lorsque l'utilisateur clique sur le bouton...
    buttonTop.addEventListener("click", () => {

        // ...la fenêtre remonte en haut de la page avec un défilement fluide
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
});
