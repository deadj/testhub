const mix = require('laravel-mix');

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

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css');
    
mix.scripts('resources/js/new.js', 'public/js/new.js');
mix.scripts('resources/js/tests.js', 'public/js/tests.js');
mix.scripts('resources/js/testQuestion.js', 'public/js/testQuestion.js');
mix.scripts('resources/js/testResult.js', 'public/js/testResult.js');
mix.scripts('resources/js/main.js', 'public/js/main.js');
mix.scripts('resources/js/publish.js', 'public/js/publish.js');