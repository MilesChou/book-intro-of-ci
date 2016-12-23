var gulp = require('gulp');

gulp.task('build', function () {

});

gulp.task('test', ['build'], function () {

});

gulp.task('default', ['test'], function () {

});
