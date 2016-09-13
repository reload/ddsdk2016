/**
 * @file
 * Gulpfile.js that builds assets for theme.
 */

var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');

var path = {
  sass: 'theme_assets/mungo/sass/',
  css: 'web/themes/custom/mungo/css'
};

gulp.task('sass', function () {
  return gulp.src(path.sass + '/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass())
    .pipe(sourcemaps.write('.'))

    .pipe(gulp.dest(path.css))
});

gulp.task('watch', function () {
  gulp.watch([path.sass + '/*.scss', path.sass + '/*/*.scss'], ['sass']);
});

gulp.task('default', ['sass', 'watch']);
