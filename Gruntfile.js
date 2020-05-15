module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        uglify: {
            comments: {
                options: {
                    sourceMap: true,
                    sourceMapName: 'assets/js/comments.js.map'
                },
                files: {
                    'assets/js/comments.min.js': ['assets/js/comments.js']
                }
            }
        },
        sass: {
            style: {
                files: {
                    'assets/css/comments.css': ['assets/scss/comments.scss']
                }
            }
        },
        autoprefixer: {
            dist: {
                files: {
                    'assets/css/comments.css': ['assets/css/comments.css']
                }
            }
        },
        cssmin: {
            options: {
                mergeIntoShorthands: false,
                roundingPrecision: -1
            },
            target: {
                files: {
                    'assets/css/comments.min.css': ['assets/css/comments.css']
                }
            }
        },
        watch: {
            styles: {
                files: ['assets/scss/comments.scss'],
                tasks: ['sass:style', 'cssmin'],
                options: {
                    spawn: false
                }
            },
            scripts: {
                files: ['assets/js/comments.js'],
                tasks: ['uglify:comments'],
                options: {
                    spawn: false
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify-es');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-css');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-autoprefixer');

    grunt.registerTask('default', ['uglify', 'sass', 'autoprefixer', 'cssmin', 'watch']);

};