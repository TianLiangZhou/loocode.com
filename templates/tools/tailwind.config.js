/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
      "./templates/tools/*.html.twig",
      "./assets/js/*.js",
  ],
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        primary: '#74b566',
      }
    },
  },
  plugins: [],
  corePlugins: {
    preflight: false,
  }
}

