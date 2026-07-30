import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Plus Jakarta Sans", ...defaultTheme.fontFamily.sans],
            },

            // Sentralisasi warna brand — sebelumnya hex diulang puluhan kali di blade.
            // Sekarang tinggal pakai: bg-brand, text-brand-dark, border-brand-light, dst.
            colors: {
                brand: {
                    DEFAULT: "#006C4E", // hijau utama (headline, ikon, border aktif)
                    dark: "#003824", // hijau paling gelap (CTA section, hover state)
                    light: "#16805F", // hijau navbar / CTA bg
                    accent: "#00885D", // hijau aksen (link hover, button)
                },
                "brand-mint": "#BCFFE0", // hero bg tint
                "brand-mint-2": "#A5F2CF", // fitur section bg tint
                warning: {
                    DEFAULT: "#F0932B",
                    dark: "#4B2800",
                },
            },

            maxWidth: {
                "8xl": "90rem",
            },
        },
    },

    plugins: [forms],
};
