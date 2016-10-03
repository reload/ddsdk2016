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

var asset_paths = {
  src: 'web/themes/custom/mungo/assets_src',
  dest: 'web/themes/custom/mungo/assets'
};

var path = {
  sass: asset_paths.src + '/sass',
  css: asset_paths.dest + '/css',
  js_src: asset_paths.src + '/js',
  js_dest: asset_paths.dest + '/js',
  bootstrap: 'node_modules/bootstrap-sass/assets/stylesheets',
  sass_burger: 'node_modules/sass-burger',
  images_src: asset_paths.src + '/images',
  images_dest: asset_paths.dest + '/images'
};

gulp.task('sass', function () {
  return gulp.src(path.sass + '/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass({
      includePaths: [bourbon.includePaths, path.bootstrap, path.sass_burger],
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
  return gulp.src([asset_paths.dest], {read: false})
    .pipe(clean());
});

var runSequence = require('run-sequence');
gulp.task('build', function() {
  runSequence ('clean', ['sass', 'javascript', 'images']);
});

gulp.task('default', function() {
  runSequence ('clean', ['sass', 'javascript', 'images'], 'watch');
});
