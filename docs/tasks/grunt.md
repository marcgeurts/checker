---
currentMenu: tasks
---

# Grunt

http://gruntjs.com/

The Grunt task will run your automated frontend tasks.
It lives under the `grunt` namespace and has following configurable parameters:

```yml
# checker.yml
parameters:
  tasks:
    grunt:
      gruntfile: ~
      task: ~
```

### gruntfile

*Default: null*

If your `Gruntfile.js` file is located at an exotic location,
you can specify your custom location with this option.
This option is set to `null` by default.
This means that `Gruntfile.js` is automatically loaded if the file exists in the current directory.

### task

*Default: null*

This option specifies which Grunt task you want to run.
This option is set to `null` by default.
This means that grunt will run the `default` task.
Note that this task should be used to verify things. 
It is also possible to alter code during commit, but this is surely **NOT** recommended!
