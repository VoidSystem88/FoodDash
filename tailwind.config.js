/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                dark: {
                    950: '#0a0a0b',
                    900: '#18181b',
                    850: '#1f1f23',
                    800: '#27272a',
                    700: '#3f3f46',
                    600: '#52525b',
                    500: '#71717a',
                    400: '#a1a1aa',
                    300: '#d4d4d8',
                },
            },
        },
    },
    plugins: [],
}