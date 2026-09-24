document.addEventListener("DOMContentLoaded", () => {



    /* buscador */
    const buscador = document.getElementById("buscador");

    buscador.addEventListener("input", (e) => {
        const q = e.target.value.toLowerCase().trim();
        document.querySelectorAll(".uni-card").forEach(card => {
            const txt = card.querySelector(".card-nombre").textContent.toLowerCase();
            card.style.display = txt.includes(q) ? "" : "none";
        });
    });

   
});