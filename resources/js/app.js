import "./bootstrap";
import Alpine from "alpinejs";
import growPos from "./grow-pos.js";
import { initRupiahInputs } from "./currency-input.js";
import { onDomReady } from "./dom-ready.js";

window.Alpine = Alpine;
Alpine.data("growPos", growPos);
Alpine.start();

// Safari/Chrome may restore a page from the back-forward cache. Reloading a
// restored page forces Laravel auth/tenant middleware to validate the session.
window.addEventListener("pageshow", (event) => {
    if (event.persisted) {
        window.location.reload();
    }
});

onDomReady(() => initRupiahInputs(document));
