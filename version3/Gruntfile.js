module.exports = function(grunt) {

	grunt.initConfig({
		pkg : grunt.file.readJSON('package.json'),

		// variables
		src  : 'assets/src',
		dest : 'assets/dest',

		watch : {
			options : {
				spawn: false
			},
			scripts : {
				files : ["<%= src %>/**/*"],
				tasks : ['default']
			}
		},

		sass: {
			dist : {
				options : {
					style : 'compressed'
				},
				files: {
					'<%= dest %>/css/style.min.css' : '<%= src %>/css/betadesign.scss',
					'<%= dest %>/css/under-construction.min.css' : '<%= src %>/css/under-construction.scss'
				}
			}
		},

		uglify : {
			dist : {
				options : {
					compress : true,
	                beautify : false,
					mangle : true
				},

				src : ['<%= src %>/js/beta.select.jquery.js', '<%= src %>/js/scripts.js'],
				dest : '<%= dest %>/js/scripts.min.js'
			}
		}
	});

	// Load Tasks
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-uglify');

	// Register Tasks
	grunt.registerTask('default', ['sass:dist', 'uglify:dist']);

};