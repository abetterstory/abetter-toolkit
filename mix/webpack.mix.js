const mix = require('laravel-mix');
const request = require('request');

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

mix.js('resources/scripts/app.js', 'public/scripts')
	.sourceMaps(!mix.inProduction())
	.extract(['vue','axios','uikit','es6-promise'])
	.version();

// ---

mix.sass('resources/styles/app.scss', 'public/styles').version();
mix.sass('resources/styles/editor.scss', 'public/styles');

// ---

mix.copy('resources/images', 'public/images');
mix.copy('resources/fonts', 'public/fonts');
mix.copy('resources/videos', 'public/videos');

//mix.copy('node_modules/font-awesome/fonts', 'public/fonts/fontawesome');
//mix.copy('node_modules/@fortawesome/fontawesome-pro/webfonts', 'public/fonts/fontawesome');

// ---

mix.webpackConfig({
	resolve: { alias: {
		'$scripts': '../../vendor/abetter/toolkit/scripts',
		'$styles': '../../vendor/abetter/toolkit/styles'
	}}
}).browserSync({
	proxy: process.env.APP_PROXY,
	open: false,
	files: [
		'app/**/*.php',
		'routes/**/*.php',
		'resources/**/*.php',
		'resources/**/*.js',
		// Use service to inject components styles
		'public/styles/**/*.css', { match: ['resources/**/*.scss'], fn: function(event,file){
			request(process.env.APP_PROXY+'/browsersync/'+event+'/'+file,function(){
				console.log('Service event ['+event+'] : '+file);
			});
   		}}
	]
}).disableNotifications();
