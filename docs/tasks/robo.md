---
currentMenu: tasks
---

# Robo

[See oficinal documentation](http://robo.li/)

The Robo task will run your automated PHP tasks.
It lives under the `robo` namespace and has following configurable parameters:

```yml
# checker.yml
parameters:
  tasks:
    robo:
      load-from: ~
      task: ~
```

### load-from

*Default: null*

If your `Robofile.php` file is located at an exotic location,
you can specify your custom location with this option.
This option is set to `null` by default.
This means that `Robofile.php` is automatically loaded if the file exists in the current directory.

### task

*Default: null*

This option specifies which Robo task you want to run.
This option is set to `null` by default.
This means that robo will run the `default` task.
Note that this task should be used to verify things. 
It is also possible to alter code during commit, but this is surely **NOT** recommended!
