
/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                inter: ['Inter', 'sans-serif'],
                karla: ['Karla', 'sans-serif'],
            },
            fontSize: {
                "heading": "34px",
                "subheading": "24px",
                "item-heading": "17px",
                "item-caption": "13px",
                "tag": "10px ",
                "button": "10px",

                "display-large": "64px",
                "display-medium": "48px",
                "display-small": "40px",
                "headline-large": "32px",
                "headline-medium": "28px",
                "headline-small": "24px",
                "title-large": "22px",
                "title-medium": "16px",
                "title-small": "14px",
                "label-large": "16px",
                "label-medium": "14px",
                "label-small": "12px",
                "body-large": "16px",
                "body-medium": "14px",
                "body-small": "12px"
            },
            colors: {
                'waldorf-red': "#99162A",
                'waldorf-red-light': "#F2E1DC",
                'waldorf-red-dark': "#870317",
                'waldorf-cream': "#FEF8EA"

            },
            ringWidth: {
                '05': '0.5px'
            }
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
