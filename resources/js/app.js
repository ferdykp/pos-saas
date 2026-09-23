import "./bootstrap";
import Alpine from "alpinejs";
import growPos from "./grow-pos.js";
import "./currency-input.js";

window.Alpine = Alpine;
Alpine.data("growPos", growPos);
Alpine.start();
