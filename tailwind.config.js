import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["expose", ...defaultTheme.fontFamily.sans],
                mainh: ["clash display", "sans-serif"],
                subh: ["Satoshi", "sans-serif"],
            },
            colors: {
                primary: "#032031",
                secondary: "#042f47",
                accent: "#1b8f67",
                accentdark: "#136b4d",
            },
        },
    },

    plugins: [forms],
};
