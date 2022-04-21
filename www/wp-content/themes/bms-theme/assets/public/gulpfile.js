'use strict';

const gulp = require('gulp');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');
const autoprefixer = require('gulp-autoprefixer');
const minifyCss = require('gulp-minify-css');
const browserSync = require('browser-sync');

gulp.task('scss', function() {
    return gulp.src('css/style.scss')
    .pipe(sourcemaps.init())
    .pipe(sass().on('error',sass.logError))
    .pipe(autoprefixer('last 1 version', '&gt; 1%', 'ie 8'))
    .pipe(minifyCss())
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('../..'));
});
//
//gulp.task('js', function() {
//  return gulp.src([
//    'node_modules/bootstrap/dist/js/bootstrap.min.js',
//    'node_modules/popper.js/dist/umd/popper.min.js',
//    // 'node_modules/jquery/dist/jquery.min.js'
//    ])
//    .pipe(gulp.dest('www/skin/js'))
//});

gulp.task('watch', gulp.series(/*'browser-sync', */function(){
  gulp.watch('css/*.scss', gulp.series('scss'));
}));
