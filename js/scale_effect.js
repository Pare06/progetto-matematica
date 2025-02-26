document.addEventListener("DOMContentLoaded", function() {
    const themeButton = document.getElementById("theme");

    themeButton.addEventListener("click", function() {
        themeButton.classList.add("scale-effect");

        // Rimuove la classe dopo l'animazione per poterla riapplicare ai successivi click
        setTimeout(() => {
            themeButton.classList.remove("scale-effect");
        }, 200);
    });
});
