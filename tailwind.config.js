/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      width: {
        '28rem': '28rem', // 448px
        '32rem': '32rem', // 512px
        '36rem': '36rem', // 576px
        '40rem': '40rem', // 640px
        '44rem': '44rem', // 704px
        '48rem': '48rem', // 768px
      },
      maxWidth: {
        '28rem': '28rem', // 448px
        '32rem': '32rem', // 512px
        '36rem': '36rem', // 576px
        '40rem': '40rem', // 640px
        '44rem': '44rem', // 704px
        '48rem': '48rem', // 768px
      },
      minWidth: {
        '28rem': '28rem', // 448px
        '32rem': '32rem', // 512px
        '36rem': '36rem', // 576px
        '40rem': '40rem', // 640px
        '44rem': '44rem', // 704px
        '48rem': '48rem', // 768px
      },
      height: {
        '28rem': '28rem', // 448px
        '32rem': '32rem', // 512px
        '36rem': '36rem', // 576px
        '40rem': '40rem', // 640px
        '44rem': '44rem', // 704px
        '48rem': '48rem', // 768px
      },
      maxHeight: {
        '28rem': '28rem', // 448px
        '32rem': '32rem', // 512px
        '36rem': '36rem', // 576px
        '40rem': '40rem', // 640px
        '44rem': '44rem', // 704px
        '48rem': '48rem', // 768px
      },
      minHeight: {
        '28rem': '28rem', // 448px
        '32rem': '32rem', // 512px
        '36rem': '36rem', // 576px
        '40rem': '40rem', // 640px
        '44rem': '44rem', // 704px
        '48rem': '48rem', // 768px
      },
      colors: {
        error: {
          100: "#FDECEC",
          200: "#FAC9C9",
          300: "#F6A6A6",
          400: "#F18383",
          500: "#DB2B39", // Base color
          600: "#B1222D",
          700: "#871922",
          800: "#5D1016",
          900: "#34080B",
        },
        warning: {
          100: "#FFF4E1",
          200: "#FFE3B3",
          300: "#FFD185",
          400: "#FFBF57",
          500: "#F3A712", // Base color
          600: "#C7880E",
          700: "#9A690A",
          800: "#6E4B07",
          900: "#412D03",
        },
        success: {
          100: "#EFF7E9",
          200: "#D7EEC1",
          300: "#BEE498",
          400: "#A5DB70",
          500: "#679436", // Base color
          600: "#51752B",
          700: "#3B561F",
          800: "#263814",
          900: "#101A08",
        },
        secondary: {
          100: "#F3F5F6",
          200: "#D8DEE2",
          300: "#BEC7CE",
          400: "#A3B0BA",
          500: "#7C90A0", // Base color
          600: "#647384",
          700: "#4C5668",
          800: "#33394C",
          900: "#1B1C30",
        },
        primary: {
          100: "#E6F0F5",
          200: "#BFD9E6",
          300: "#99C3D8",
          400: "#72ACCA",
          500: "#003D5B", // Base color
          600: "#002F48",
          700: "#002236",
          800: "#001523",
          900: "#000911",
        },
      },
    },
  },
  plugins: [],
};