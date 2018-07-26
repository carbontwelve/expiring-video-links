var gulp    = require('gulp'),
    plumber = require('gulp-plumber'),
    less         = require('gulp-less'),
    autoprefixer = require('gulp-autoprefixer');

var onError  = function(err) {
    console.log(err);
};

gulp.task('less', function() {
    return gulp.src('./resources/less/app.less')
        .pipe(plumber({errorHandler: onError}))
        .pipe(less())
        .pipe(autoprefixer(['last 15 versions', '> 1%', 'ie 8', 'ie 7'], {cascade: true}))
        .pipe(gulp.dest('./public/css'));
});

gulp.task('watch', function () {
    gulp.watch('./resources/less/**/*', ['less']);
});

gulp.task('default', ['less', 'watch']);
