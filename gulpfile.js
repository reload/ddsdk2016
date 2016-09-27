/**
 * @file
 * Gulpfile.js that builds assets for theme.
 */

var gulp = require('gulp');
var clean = require('gulp-clean');
var sass = require('gulp-sass');
var jshint  = require('gulp-jshint');
var stylish = require('jshint-stylish');
var sourcemaps = require('gulp-sourcemaps');
var notify = require('gulp-notify');
var bourbon =  require('node-bourbon');


var path = {
  sass: 'theme_assets/mungo/sass',
  css: 'web/themes/custom/mungo/css',
  js_src: 'theme_assets/mungo/javascript',
  js_dest: 'web/themes/custom/mungo/js',
  bootstrap: 'node_modules/bootstrap-sass/assets/stylesheets',
  images_src: 'theme_assets/mungo/images',
  images_dest: 'web/themes/custom/mungo/images'
};

gulp.task('sass', function () {
  return gulp.src(path.sass + '/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass({
      includePaths: [bourbon.includePaths, path.bootstrap],
      outputStyle: 'expanded'
    }).on('error', notify.onError(function (error) {
      return "\n\nSASS error: " + error.message;
    })))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(path.css))
});

gulp.task('javascript', function () {
  return gulp.src([path.js_src + '/custom/*.js', path.js_src + '/contrib/*.js'])
    .pipe(jshint())
    .pipe(jshint.reporter(stylish))

    .pipe(gulp.dest(path.js_dest))
});
gulp.task('images', function () {
  return gulp.src(path.images_src + '/*.*')

    .pipe(gulp.dest(path.images_dest))
});

gulp.task('watch', function () {
  gulp.watch([path.sass + '/*.scss', path.sass + '/*/*.scss'], ['sass']);
  gulp.watch(path.js_src + '/*/*.js', ['javascript']);
  gulp.watch(path.images_src + '/*', ['images']);
});

gulp.task('clean', function () {
  return gulp.src([path.css, path.js_dest], {read: false})
    .pipe(clean());
});

gulp.task('build', ['clean', 'sass', 'javascript', 'images']);

gulp.task('default', ['clean', 'sass', 'javascript', 'images', 'watch']);
