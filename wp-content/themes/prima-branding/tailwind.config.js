let fontSizes = {};
let spacing = {};
for (let i = 0; i < 200; i++) {
    fontSizes[i] = i + "px";
    spacing[i] = i + "px";
}


module.exports = {
    content: require('fast-glob').sync([
      './*.php',
      './includes/blocks/*.php',
      './includes/lib/*.php',
      './includes/partials/*.php',
      './js/*.js',
      './includes/blocks/**/*.php'
    ]),
    theme: {
        screens: {
            'sm': '420px',
            // => @media (min-width: 420px) { ... }
        
            'md': '768px',
            // => @media (min-width: 768px) { ... }
        
            'md-lg': '830px',
            // => @media (min-width: 830px) { ... }

            'lg': '1024px',
            // => @media (min-width: 1024px) { ... }
        
            'xl': '1280px',
            // => @media (min-width: 1280px) { ... }
        
            '2xl': '1600px',
            // => @media (min-width: 1600px) { ... }
        },
        spacing: spacing,
        fontSize: fontSizes,
        fontFamily: {
            yellowtail: ["Yellowtail", 'serif'],
            gibson: ["canada-type-gibson", 'sans-serif'],
        },
        lineHeight: {
            "0.1": "0.1",
            "0.2": "0.2",
            "0.3": "0.3",
            "0.4": "0.4",
            "0.5": "0.5",
            "0.6": "0.6",
            "0.7": "0.7",
            "0.8": "0.8",
            "0.9": "0.9",
            "1": "1",
            "1.1": "1.1",
            "1.2": "1.2",
            "1.3": "1.3",
            "1.4": "1.4",
            "1.5": "1.5",
            "1.6": "1.6",
            "1.7": "1.7",
            "1.8": "1.8",
            "1.9": "1.9",
            "2.0": "2.0"
        },
        extend: { 
            colors: {
                /**
                 * Define colours here, please follow:
                 * https://tailwindcss.com/docs/customizing-colors#adding-additional-colors
                 */
                brown: {
                    50: '#fdf8f6',
                    100: '#f2e8e5',
                    200: '#eaddd7',
                    300: '#e0cec7',
                    400: '#d2bab0',
                    500: '#bfa094',
                    600: '#a18072',
                    700: '#977669',
                    800: '#846358',
                    900: '#43302b',
                  },
            }
        },
       
    },
    variants: {},
    plugins: [],
  }
