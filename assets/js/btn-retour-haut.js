document.addEventListener("DOMContentLoaded", () => {
    const buttonTop = document.querySelector(".btn-scroll"); // Sélection après chargement du DOM

    window.addEventListener("scroll", () => {
        if (window.scrollY > 200) { 
            buttonTop.style.display = "block";
        } else {
            buttonTop.style.display = "none";
        }
    });

    buttonTop.addEventListener("click", () => {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
});