/** @type {import('tailwindcss').Config} */
export default {
    important: true,
    darkMode: "class",
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                logo: '#fa3501'
            }
        },
    },
    plugins: [],
};
