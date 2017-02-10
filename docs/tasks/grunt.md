---
currentMenu: tasks
---

# Grunt

[See oficinal documentation](http://gruntjs.com/)

The Grunt task will run your automated workflow tasks.
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
This means that `Gruntfile.js` is automatically loaded
if the file exists in the current directory.

### task

*Default: null*

This option specify which will task you want to run.
This option is set to `null` by default.
This means that will run the `default` task.
