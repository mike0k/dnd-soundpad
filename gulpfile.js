// npm install --save-dev gulp-cli gulp gulp-sass node-sass gulp-minify-css gulp-rename gulp-uglify gulp-plumber

const gulp = require('gulp');
const sass = require('gulp-sass');
const minifycss = require('gulp-minify-css');
const rename = require('gulp-rename');
const plumber = require('gulp-plumber');

sass.compiler = require('node-sass');

function siteCss() {
    return gulp
        .src('app/src/asset/sass/build/**/*.scss')
        .pipe(plumber())
        .pipe(sass())
        .pipe(minifycss())
        .pipe(gulp.dest('app/src/asset/css/'));
}

function watch() {
    gulp.watch('app/src/asset/sass/**/*', siteCss);
}

exports.watch = watch;
exports.siteCss = siteCss;
