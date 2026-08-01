import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
        "./resources/js/**/*.jsx",
    ],

    theme: {
        extend: {
            // === TYPOGRAPHY ===
            fontFamily: {
                // Heading utama: Plus Jakarta Sans
                sans: ["'Plus Jakarta Sans'", ...defaultTheme.fontFamily.sans],
                // Body & UI: Inter
                body: ["'Inter'", ...defaultTheme.fontFamily.sans],
                // Angka nominal kasir: JetBrains Mono / IBM Plex Mono
                mono: [
                    "'JetBrains Mono'",
                    "'IBM Plex Mono'",
                    ...defaultTheme.fontFamily.mono,
                ],
            },

            // === COLOR PALETTE (Hex Presisi) ===
            colors: {
                primary: {
                    900: "#0B3D2E",
                    700: "#12664B",
                    600: "#16805F", // Main button & nav aktif
                    500: "#1FA179",
                    100: "#DCF3E9",
                    50: "#F1FAF6",
                },
                accent: {
                    700: "#B45309",
                    500: "#F0932B", // CTA Sekunder & badge promo
                    100: "#FDEBD3",
                },
                ink: {
                    900: "#1A2421", // Teks utama
                    700: "#465550", // Teks sekunder
                    400: "#8B9994", // Placeholder/disabled
                },
                surface: {
                    0: "#FFFFFF", // Card background
                    100: "#F7F9F8", // Page background
                },
                border: {
                    200: "#E3E9E6", // Divider/border
                },
                // Semantic Colors
                semantic: {
                    success: "#1FA179",
                    warning: "#F0932B",
                    danger: "#E24C4B",
                    info: "#3B82C4",
                },
            },

            // === SPACING BASE UNIT 4px ===
            spacing: {
                1: "4px",
                2: "8px",
                3: "12px",
                4: "16px",
                5: "20px",
                6: "24px",
                8: "32px",
                10: "40px",
                12: "48px",
                16: "64px",
                20: "80px",
                24: "96px",
            },

            // === BORDER RADIUS ===
            borderRadius: {
                sm: "6px", // Input, badge kecil
                md: "10px", // Button, card kecil
                lg: "16px", // Card utama, modal
                full: "999px", // Pill, FAB, Avatar
            },

            // === BOX SHADOW ===
            boxShadow: {
                sm: "0 1px 2px rgba(26, 36, 33, 0.06)",
                md: "0 4px 12px rgba(26, 36, 33, 0.08)",
                lg: "0 12px 32px rgba(26, 36, 33, 0.12)",
            },

            // === BREAKPOINTS ===
            screens: {
                sm: "360px", // Mobile
                md: "768px", // Tablet
                lg: "1024px", // Laptop
                xl: "1440px", // Desktop
            },

            // === CONTAINER MAX-WIDTHS ===
            maxWidth: {
                tablet: "720px",
                laptop: "1080px",
                desktop: "1320px",
                "content-desktop": "1060px",
                "modal-sm": "480px",
                "modal-lg": "720px",
            },
        },
    },

    plugins: [forms],
};
