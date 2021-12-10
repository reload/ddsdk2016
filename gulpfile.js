/**
 * @file
 * Gulpfile.js that builds assets for theme.
 */

var gulp = require('gulp');
var autoprefixer = require('gulp-autoprefixer');
var clean = require('gulp-clean');
var sass = require('gulp-sass')(require('node-sass'));
var jshint  = require('gulp-jshint');
var stylish = require('jshint-stylish');
var sourcemaps = require('gulp-sourcemaps');
var notify = require('gulp-notify');
// If you upgrade Bourbon to v5, remember to remove
// "output-bourbon-deprecation-warnings" from mungo.bundle.scss
var bourbon =  require('node-bourbon');
var runSequence = require('gulp4-run-sequence');

var asset_paths = {
  src: 'web/themes/custom/mungo/assets_src',
  dest: 'web/themes/custom/mungo/assets'
};

var bootstrap_src = 'node_modules/bootstrap-sass';

var path = {
  stylesheets: {
    src: asset_paths.src + '/sass',
    dest: asset_paths.dest + '/css',
    burger: 'node_modules/sass-burger'
  },
  js: {
    src: asset_paths.src + '/js',
    dest: asset_paths.dest + '/js'
  },
  bootstrap: {
    sass: bootstrap_src + '/assets/stylesheets',
    fonts: {
      src: bootstrap_src + '/assets/fonts',
      dest: asset_paths.dest + '/fonts'
    }
  },
  fontawesome: {
    src: 'node_modules/font-awesome/scss',
  },
  images: {
    src: asset_paths.src + '/images',
    dest: asset_paths.dest + '/images'
  }
};

gulp.task('sass', function () {
  return gulp.src(path.stylesheets.src + '/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass({
      includePaths: [bourbon.includePaths, path.bootstrap.sass, path.stylesheets.burger, path.fontawesome.src, 'node_modules/object-fit/dist/'],
      outputStyle: 'expanded'
    }).on('error', notify.onError(function (error) {
      return "\n\nSASS error: " + error.message;
    })))
    .pipe(autoprefixer({
      cascade: false
    }))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(path.stylesheets.dest))
});

gulp.task('javascript', function () {
  return gulp.src([path.js.src + '/custom/*.js', path.js.src + '/contrib/*.js'])
    .pipe(jshint())
    .pipe(jshint.reporter(stylish))

    .pipe(gulp.dest(path.js.dest))
});
gulp.task('images', function () {
  return gulp.src(path.images.src + '/**/*', { "base" : path.images.src })
    .pipe(gulp.dest(path.images.dest))
});

gulp.task('bootstrap_fonts', function () {
  return gulp.src([path.bootstrap.fonts.src + '/*', path.bootstrap.fonts.src + '/*/*.*'])
    .pipe(gulp.dest(path.bootstrap.fonts.dest))
});
gulp.task('fontawesome_fonts', function () {
  return gulp.src('node_modules/font-awesome/fonts/*.*')
    .pipe(gulp.dest(asset_paths.dest + '/fonts/font-awesome'))
});
gulp.task('object-fit-polyfill', function () {
  return gulp.src('node_modules/object-fit-images/dist/*.js')
    .pipe(gulp.dest(asset_paths.dest + '/js'))
});

gulp.task('watch', function () {
  gulp.watch(
    [path.stylesheets.src + '/*.scss', path.stylesheets.src + '/*/*.scss'],
    gulp.series('sass')
  );
  gulp.watch(
    path.js.src + '/*/*.js',
    gulp.series('javascript')
  );
  gulp.watch(
    path.images.src + '/*',
    gulp.series('images')
  );
});

gulp.task('clean', function () {
  return gulp.src([asset_paths.dest], {read: false, allowEmpty: true})
    .pipe(clean());
});


gulp.task('build', function(done) {
  runSequence(
    'clean',
    ['sass', 'javascript', 'images', 'bootstrap_fonts', 'fontawesome_fonts', 'object-fit-polyfill'],
    function() { done(); }
  );
});

gulp.task('default', function(done) {
  runSequence(
    'build',
    ['watch'],
    function() { done(); }
  );
});
