import "./bootstrap";
import Alpine from "alpinejs";
import AOS from "aos";
import "aos/dist/aos.css";
import growPos from "./grow-pos.js";
import { onDomReady } from "./dom-ready.js";

window.Alpine = Alpine;
Alpine.data("growPos", growPos);
Alpine.start();

// Animation is optional. Content stays visible until initialization succeeds.
onDomReady(() => {
    if (
        !document.querySelector("[data-aos]") ||
        window.matchMedia("(prefers-reduced-motion: reduce)").matches
    ) {
        return;
    }

    try {
        AOS.init({
            duration: 700,
            easing: "ease-out-cubic",
            once: true,
            offset: 80,
        });
        document.documentElement.setAttribute("data-animations-ready", "");
    } catch (error) {
        console.warn(
            "Animasi tidak tersedia; konten tetap ditampilkan.",
            error,
        );
    }
});
