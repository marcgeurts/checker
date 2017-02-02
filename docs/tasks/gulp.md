---
currentMenu: tasks
---

# Gulp

http://gulpjs.com/

The Gulp task will run your automated frontend tasks.
It lives under the `gulp` namespace and has following configurable parameters:

```yml
# checker.yml
parameters:
  tasks:
    gulp:
      gulpfile: ~
      task: ~
```

### gulpfile

*Default: null*

If your `gulpfile.js` file is located at an exotic location,
you can specify your custom location with this option.
This option is set to `null` by default.
This means that `gulpfile.js` is automatically loaded if the file exists in the current directory.

### task

*Default: null*

This option specifies which Gulp task you want to run.
This option is set to `null` by default.
This means that gulp will run the `default` task.
Note that this task should be used to verify things. 
It is also possible to alter code during commit, but this is surely **NOT** recommended!
