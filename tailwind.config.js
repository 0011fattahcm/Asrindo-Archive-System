/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./public/**/*.{php,html,js}",
    "./includes/**/*.{php,html,js}",
    "./config/**/*.{php,html,js}"
  ],
  theme: {
    extend: {
      colors: {
        primary: '#0A3A75',
        secondary: '#10B981',
        accent: '#007BBD'
      },
    },
  },
  plugins: [],
}
