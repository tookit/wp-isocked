const gulp = require( 'gulp' ),
	browserSync = require( 'browser-sync' ),
	server = browserSync.create(),
	dev_url = 'http://localhost/starter-bootstrap';


/**
 * Define all source paths
 */

var paths = {
	styles: {
		src: './assets/scss/*.scss',
		dest: './assets/css'
	},
	scripts: {
		src: './assets/*.js',
		dest: './assets/js'
	}
};


/**
 * Webpack compilation: http://webpack.js.org, https://github.com/shama/webpack-stream#usage-with-gulp-watch
 * 
 * buildJs()
 */

function buildJs() {
	const compiler = require( 'webpack' ),
		webpackStream = require( 'webpack-stream' );

	return gulp.src( paths.scripts.src )
		.pipe(
			webpackStream( {
				config: require( './webpack.config.js' )
			},
				compiler
			)
		)
		.pipe(
			gulp.dest( paths.scripts.dest )
		)
		/*.pipe(
			server.stream() // Browser Reload
		)*/;
}


/**
 * SASS-CSS compilation: https://www.npmjs.com/package/gulp-sass
 * 
 * buildCss()
 */

function buildCss() {
	const sass = require( 'gulp-sass' )( require( 'sass' ) ),
		postcss = require( 'gulp-postcss' ),
		sourcemaps = require( 'gulp-sourcemaps' ),
		autoprefixer = require( 'autoprefixer' ),
		cssnano = require( 'cssnano' );

	const plugins = [
		autoprefixer(),
		cssnano(),
	];

	return gulp.src( paths.styles.src )
		.pipe(
			sourcemaps.init()
		)
		.pipe(
			sass()
				.on( 'error', sass.logError )
		)
		.pipe(
			postcss( plugins )
		)
		.pipe(
			sourcemaps.write( './' )
		)
		.pipe(
			gulp.dest( paths.styles.dest )
		)
		/*.pipe(
			server.stream() // Browser Reload
		)*/;
}

/**
 * Watch task: Webpack + SASS
 * 
 * $ gulp watch
 */

gulp.task( 'watch',
	function () {

		// gulp.watch( paths.scripts.src, buildJs );
		gulp.watch( [ paths.styles.src, './assets/scss/*.scss' ], buildCss() );
	}
);