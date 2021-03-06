module.exports = function(grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

	    // Defines project's meta data
	    // the banner is used by minification task and 
	    // will be at the beginning of each processed file
	    meta: {
	      banner: '/*! <%= pkg.title || pkg.name %> - v<%= pkg.version %> - ' +
	        '<%= grunt.template.today("yyyy-mm-dd") %>\n' +
	        '<%= pkg.homepage ? "* " + pkg.homepage + "\\n" : "" %>' +
	        '* Copyright (c) <%= grunt.template.today("yyyy") %> <%= pkg.author.name %>;' +
	        ' Licensed <%= pkg.license %> */\n'
	    },

	    // Defines a custom config property.
	    // This property '<%= build.dest %>'
	    build: {
	      dest: 'dist'
	    },
	    // lint task
	    // Defines witch files to lint
	    jshint: {
	      files: ['gruntfile.js', 'js/map.js']
	    },
	    // qunit multi-task
	    // Defines 2 different targets
	    // (dev, dist) for running qunit.
	    // Each target defines a list of test files
	    qunit: {
	      // dev: ['tests/index.html'],
	      // dist: ['<%= build.dest %>/tests/index.html']
	    },
	    // concat multi-task
	    // Defines 3 different targets 
	    // (libs, tests, dist) for concatenation.
	    // Each target defines a list of files to concatenate
	    // and a destination file to write the output
	    concat: {
	      libs: {
	      	src: ['<%= meta.banner %>', 'js/jquery.cookie.js', 'js/markercluster.js' ],
	        dest: '<%= build.dest %>/js/<%= pkg.name %>-libs.<%= pkg.version %>.js'
	      },
	      dist: {
	        src: ['<%= meta.banner %>', 'js/map.js' ],
	        dest: '<%= build.dest %>/js/<%= pkg.name %>-app.<%= pkg.version %>.js'
	      }
	    },
	    // min multi-task
	    // Defines 3 different targets 
	    // (libs, tests, dist) for minification.
	    // Each target defines a list of files to minify
	    // and a destination file to write the output
	    uglify: {
	      dist: {
	      	options: {
	      		banner: '<%= meta.banner %>'
	      	},
	      	files: {
	      		'<%= build.dest %>/js/<%= pkg.name %>-libs.<%= pkg.version %>.min.js' : ['<%= concat.libs.dest %>'],
	      		'<%= build.dest %>/js/<%= pkg.name %>-app.<%= pkg.version %>.min.js' : ['<%= concat.dist.dest %>']
	      	}
	      }
	    },
	    // targethtml multi-task
	    // Defines 2 different targets (release, tests) 
	    // Each target defines a source file to process
	    // and a destination file to write the output
	    targethtml: {
	      dist: {
	      	options: {
	      		curlyTags: {
	      			'pkgName': '<%= pkg.name %>',
	      			'pkgVersion': '<%= pkg.version %>'
	      		}
	      	},
	        files: {
			  '<%= build.dest %>/index.php' : 'index.php',
			  '<%= build.dest %>/about.php' : 'about.php',
			  '<%= build.dest %>/db_config.php' : 'db_config_dist.php'
	        }
	      }
	    },
	    // copy multi-task
	    // Defines 1 single target (dist) 
	    // copy files from development path 
	    // to 'build.dest' folder
	    copy: {
	      dist: {
	        files: {
	          "<%= build.dest %>/json_gen.php": "json_gen.php",
	          "<%= build.dest %>/": "img/**",
	          "<%= build.dest %>/db_connect.php": "db_connect.php",

	          // "<%= build.dest %>/tests/": ["tests/js/**", "tests/libs/**"],
	          // "<%= build.dest %>/gallery_data.json": "gallery_data.json"
	        }
	      }
	    },

	    // clean task
	    // remove every file from the defined folders
	    clean: ["<%= build.dest %>"],

	    // watch task
	    // runs the defined tasks every time a watched file is updated

		sass: {
		    dev: {
		        options: {
		            style: 'expanded',
		            lineNumbers: true,
		            require: ['./sass/helpers/url64.rb']
		        },
		        expand: true,
		        cwd: 'sass/',
		        src: ['*.scss'],
		        dest: 'css/',
		        ext: '.css'
		    }
		},
		cssmin: {
		  dist: {
		    files: {
		      '<%= build.dest %>/css/<%= pkg.name %>.<%= pkg.version %>.min.css' : ['css/html5-reset.css', 'css/styles.css', 'css/!*.min.css', 'css/!*.css.map'],
		    }
		  }
		},
		autoprefixer: {
		  dev: {
		    options: {
		      browsers: ['last 3 versions', '> 1%', 'ie 8', 'ie 7']
		    },
		    src: 'css/styles.css',
		  }
		},
		watch: {
			css: {
				files: '**/*.scss',
				tasks: ['sass', 'autoprefixer']
			},
			jshint: {
				files: '<%= jshint.files %>',
				tasks: 'jshint'
			}
		}
	});

	grunt.loadNpmTasks('grunt-autoprefixer');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-targethtml');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-compass');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.registerTask('default',['watch']);
	grunt.registerTask('dist', ['clean', 'copy', 'cssmin', 'concat', 'uglify', 'targethtml']);
};