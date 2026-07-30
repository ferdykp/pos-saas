import "./bootstrap";

import Alpine from "alpinejs";
import AOS from "aos";
import "aos/dist/aos.css";

// ------------------------------------------------------------------
// Alpine.js — dipakai untuk mobile menu (navbar), toggle, dsb.
// Pastikan install dulu: npm install alpinejs
// ------------------------------------------------------------------
window.Alpine = Alpine;

// ------------------------------------------------------------------
// Counter component — animasi angka naik dari 0 saat pertama render.
// Dipakai di hero section (x-data="counterGroup()").
// Kalau mau counter cuma jalan saat elemen masuk viewport, bisa
// dikombinasikan dengan Intersection Observer di bawah.
// ------------------------------------------------------------------
Alpine.data("counterGroup", () => ({
    counters: [
        { target: 10000, current: 0, display: "0" },
        { target: 9, current: 0, display: "0" },
        { target: 98, current: 0, display: "0" },
    ],
    start() {
        const duration = 1500; // ms
        const steps = 60;
        const stepTime = duration / steps;

        this.counters.forEach((counter, i) => {
            let step = 0;
            const increment = counter.target / steps;
            const interval = setInterval(() => {
                step++;
                counter.current = Math.min(
                    Math.round(increment * step),
                    counter.target,
                );
                counter.display =
                    counter.current >= 1000
                        ? counter.current.toLocaleString("id-ID") + "+"
                        : counter.current.toString();
                if (step >= steps) clearInterval(interval);
            }, stepTime);
        });
    },
}));

Alpine.start();

// ------------------------------------------------------------------
// AOS (Animate On Scroll) — untuk elemen dengan atribut data-aos="..."
// Install dulu: npm install aos
// ------------------------------------------------------------------
document.addEventListener("DOMContentLoaded", () => {
    AOS.init({
        duration: 700,
        easing: "ease-out-cubic",
        once: true, // animasi cuma jalan sekali (tidak reverse saat scroll balik)
        offset: 80, // jarak trigger dari viewport
    });
});
