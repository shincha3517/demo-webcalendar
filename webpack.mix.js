let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css');



mix.copy([
    'vendor/bower_dl/bootstrap-calendar/css/calendar.css',
    'vendor/bower_dl/bootstrap/dist/css/bootstrap.min.css',
], 'public/css');


mix.copy('vendor/bower_dl/jquery/dist/jquery.min.js', 'public/js');
mix.copy('vendor/bower_dl/bootstrap/dist/js/bootstrap.min.js', 'public/js');
mix.copy('vendor/bower_dl/underscore/underscore-min.js', 'public/js');
mix.copy('vendor/bower_dl/bootstrap-calendar/js/calendar.js', 'public/js');
mix.copy('vendor/bower_dl/moment/moment.js', 'public/js');
mix.copyDirectory('vendor/bower_dl/bootstrap-calendar/img', 'public/img');
mix.copyDirectory('vendor/bower_dl/bootstrap-calendar/tmpls', 'public/tmpls');
