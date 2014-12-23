module.exports = function( grunt ) {

  // Configuration goes here 
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    coffee: {
        compile: {
          options: {
              bare: true,
          },
          files: {
            'plugin.src.js': ['coffee/plugin.coffee']
          }
        }
    },
    uglify: {
        dist: {
            options: {
                report: 'min'
            },
            files: [{
                'plugin.js' : 'plugin.src.js'
            }]
        }
    },
    watch: {
        dist: {
            files: ['coffee/plugin.coffee'],
            tasks: ['clear', 'coffee', 'uglify'],
            options: {
                 event: ['added', 'deleted', 'changed'],
                 spawn: false
            }
       }
    }
  });

  // Load plugins here
  grunt.loadNpmTasks('grunt-contrib-coffee');
  grunt.loadNpmTasks('grunt-clear');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-uglify');

  // Define your tasks here
  grunt.registerTask('default', ['clear', 'coffee']);
  grunt.registerTask('watchman', ['watch']);

};