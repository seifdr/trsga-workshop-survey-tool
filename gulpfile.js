var gulp 		= require('gulp');
var sass 		= require('gulp-sass');
var compass		= require('gulp-compass');
var path 		= require('path');
var jshint		= require('gulp-jshint');
var jshint 		= require('gulp-jshint');
var concat		= require('gulp-concat');
var babel 		= require('gulp-babel');
var imagemin	= require('gulp-imagemin');
var rename 		= require('gulp-rename');
//gulp-plumber to handle errors in our tasks
var plumber 	= require('gulp-plumber');
//gulp-notify to show a nice growl life notifications upon error
var notify 		= require('gulp-notify');

//for server and live reload, use gulp connect -- not being used anymore, doenst work with php
// connect = require('gulp-connect');

var browserSync = require('browser-sync');
var reload  	= browserSync.reload;

//this is all for the init function which clones everything and move to a new folder named via "themeName" in the package.json file
var fs 			= require('node-fs');

var fse 		= require('fs-extra');

var json		= require('json-file');

var themeName 	= json.read('./package.json').get('themeName');

var themeDir 	= '../' + themeName;


// gulp.task('init', function(){

// 	fs.mkdirSync( themeDir, 765, true );

// 	fse.copySync( 'theme-boilerplate', themeDir + '/' );

// });


//Here is a handy plumber setting that we will use when an error occurs in any of the tasks
var plumberErrorHandler = { errorHandler: notify.onError({
 
    title: 'Gulp',
 
    message: 'Error: <%= error.message %>'
 
  })
 
};

//compile sass
gulp.task('sass', function(){

	var sourceSass = [
		'./css/sass/main.scss',
	];

	gulp.src( sourceSass )
		.pipe( plumber( plumberErrorHandler ) )
		// .pipe( sass() )
		.pipe( compass({
			//project: path.join(__dirname, '/'),
			css: 'css',
			sass: 'css/sass',
			// image: 'images'
		}) )
		.pipe( rename('main.css') )
		.pipe( gulp.dest('css') )
		.pipe( reload({stream:true}) );
});


//minify and combine js
gulp.task('js', function(){

	var needScripts = [
		//'js/src/*.js',
		'js/src/tether.min.js',
		'js/src/jquery-3.2.1.min.js',
		'js/src/bootstrap.min.js',
		'js/src/googleCharts_loader.js',
		'js/custom/*.js'
	];

	// gulp.src('js/src/**/*.js')
	gulp.src( needScripts )
		.pipe( plumber( plumberErrorHandler ) )
		//.pipe( jshint() )
			//.pipe(jshint.reporter('fail'))
			// .pipe( babel( {
           	// 	presets: ['es2015']
        	// }))
			.pipe( concat('main.js') )
			.pipe( gulp.dest('js') )
			.pipe( reload({stream:true}) );
});


//minify images 
gulp.task('img', function(){

	gulp.src('img/src/*.{png, jpg, gif}')
		.pipe( plumber( plumberErrorHandler ) )
		.pipe( imagemin({
			optimizationLevel: 7,
			progressive: true
		}) )
		.pipe( gulp.dest('img') );
});

// browser-sync task for starting the server.
gulp.task('browser-sync', function() {
    //watch files
    var files = [
	    './style.css',
	    './**/*.php'
    ];
 
    //initialize browsersync
    browserSync.init(files, {
	    //browsersync with a php server
	    proxy: "http://localhost:8888/ws/",
		//proxy: "http://localhost/ws/",
	    notify: false
    });
});


//setting up a watch for automating tasks
gulp.task('watch', function(){

	gulp.watch('css/sass/**/*.scss', ['sass'] );

	gulp.watch('js/custom/*.js', ['js'] );

	gulp.watch('js/src/*.js', ['js'] );

	// gulp.watch( 'img/src/*.{png, jpg, gif}', ['img'] );

	// gulp.watch('**/*.php', function(){
	// 	 browserSync.reload();
	// })

});

gulp.task('default', ['sass', 'js', 'browser-sync', 'watch'] );
