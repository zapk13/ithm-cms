/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./**/*.html",
    "./assets/js/**/*.js"
  ],
  theme: {
    extend: {
      colors: {
        'ithm': {
          'primary': '#4f46e5',
          'secondary': '#7c3aed',
          'success': '#059669',
          'warning': '#d97706',
          'danger': '#dc2626',
          'info': '#0891b2'
        }
      },
      animation: {
        'fade-in': 'fadeIn 0.3s ease-out',
        'slide-in': 'slideIn 0.3s ease-out'
      },
      keyframes: {
        fadeIn: {
          'from': {
            opacity: '0',
            transform: 'translateY(10px)'
          },
          'to': {
            opacity: '1',
            transform: 'translateY(0)'
          }
        },
        slideIn: {
          'from': {
            transform: 'translateX(-100%)'
          },
          'to': {
            transform: 'translateX(0)'
          }
        }
      }
    },
  },
  plugins: [
    require('@tailwindcss/forms')
  ],
}
