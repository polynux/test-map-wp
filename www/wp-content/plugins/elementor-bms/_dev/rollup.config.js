import commonjs from '@rollup/plugin-commonjs';
import noderesolve from '@rollup/plugin-node-resolve';
import babel from '@rollup/plugin-babel';
import { terser } from 'rollup-plugin-terser';
import scss from 'rollup-plugin-scss';

const dev = {
  input: 'js/index.js',
  output: {
    format: 'iife',
    file: '../assets/js/bms-elementor.min.js',
    name: 'bms'
  },
  plugins: [
    commonjs(),
    noderesolve(),
    scss({ output: '../assets/css/bms-elementor.min.css', sourceMap: true, sass: require('sass'), watch: 'css' })
  ]
};
const build = {
  input: 'js/index.js',
  output: {
    format: 'iife',
    file: '../assets/js/bms-elementor.min.js',
    name: 'bms'
  },
  plugins: [
    commonjs(),
    noderesolve(),
    babel({ babelHelpers: 'bundled' }),
    terser(),
    scss({
      output: '../assets/css/bms-elementor.min.css',
      sourceMap: false,
      sass: require('sass'),
      outputStyle: 'compressed',
      watch: 'css'
    })
  ]
};

const conf = process.env.NODE_ENV === 'production' ? build : dev;
export default conf;
