/*!
 * gulp file
 * xiewulong <xiewulong@vip.qq.com>
 * create: 2016/9/20
 * since: 0.0.1
 */
const PATH = require('path');

const GULP = require('gulp');
const PLUGINS = require('gulp-load-plugins')();

const BASE_PATH = __dirname;
const CONFIG = {
	// css: {
	// 	src: PATH.join(BASE_PATH, 'scss', '**', '*.scss'),
	// 	dist: PATH.join(BASE_PATH, 'dist', 'css'),
	// 	outputStyles: ['compact', 'compressed'],
	// },
	js: {
		src: PATH.join(BASE_PATH, 'js', '**', '*.js'),
		dist: PATH.join(BASE_PATH, 'dist', 'js'),
	},
};

let ns = 'attachment';
let _ns = (name, parent = ns) => (parent ? parent + ':' : '') + name;

// GULP.task(_ns('scss'), function() {
// 	CONFIG.css.outputStyles.forEach((style) => {
// 		GULP.src(CONFIG.css.src)
// 			.pipe(PLUGINS.plumber())
// 			.pipe(PLUGINS.sourcemaps.init())
// 			.pipe(PLUGINS.sass({outputStyle: style}).on('error', PLUGINS.sass.logError))
// 			.pipe(PLUGINS.autoprefixer())
// 			.pipe(PLUGINS.rename(function(path) {
// 				if(style == 'compressed') {
// 					path.basename += '.min';
// 				}
// 			}))
// 			.pipe(PLUGINS.sourcemaps.write('.'))
// 			.pipe(GULP.dest(CONFIG.css.dist));
// 	});
// });

GULP.task(_ns('babel'), function() {
	GULP.src(CONFIG.js.src)
		.pipe(PLUGINS.plumber())
		.pipe(PLUGINS.sourcemaps.init())
		.pipe(PLUGINS.babel())
		.pipe(PLUGINS.sourcemaps.write('.'))
		.pipe(GULP.dest(CONFIG.js.dist));
	GULP.src(CONFIG.js.src)
		.pipe(PLUGINS.plumber())
		.pipe(PLUGINS.sourcemaps.init())
		.pipe(PLUGINS.babel())
		.pipe(PLUGINS.uglify())
		.pipe(PLUGINS.rename(function(path) {
			path.basename += '.min';
		}))
		.pipe(PLUGINS.sourcemaps.write('.'))
		.pipe(GULP.dest(CONFIG.js.dist));
});

GULP.task(_ns('watch'), function() {
	// GULP.watch(CONFIG.css.src, [_ns('scss')]);
	GULP.watch(CONFIG.js.src, [_ns('babel')]);
});

GULP.task('release', (GULP.tasks.release ? GULP.tasks.release.dep : []).concat([
	// _ns('scss'),
	_ns('babel'),
]));
GULP.task('default', (GULP.tasks.default ? GULP.tasks.default.dep : []).concat([_ns('watch')]));
