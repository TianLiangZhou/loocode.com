const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.setPublicPath('public');

//合并文件，分离库和页面JS
mix.js('resources/js/app.js', "assets/js/app.js")
    .js('resources/js/posts.js', "assets/js/article.js")
    .js('resources/js/user.js', "assets/js/user.js")
    .postCss(
      'resources/css/app.css',
      'assets/css/app.css', [
      require("tailwindcss"),
    ])
    .copyDirectory(
        ['resources/images/'],
        'public/assets/images/'
    )
    .extract([]);

if (mix.inProduction()) {
    mix.version();
} else {
    mix.webpackConfig({
        devtool: 'inline-source-map'
    });
}
