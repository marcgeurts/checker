---
currentMenu: tasks
---

# Make

The Make task will run your automated make tasks.
It lives under the `make` namespace and has following configurable parameters:

```yml
# checker.yml
parameters:
  tasks:
    make:
      makefile: ~
      task: ~
```

### makefile

*Default: null*

If your `Makefile` file is located at an exotic location,
you can specify your custom location with this option.
This option is set to `null` by default.
This means that `Makefile` is automatically loaded
if the file exists in the current directory.

### task

*Default: null*

This option specify which will task you want to run.
This option is set to `null` by default.
This means that will run the `default` task.
