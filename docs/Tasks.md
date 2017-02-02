---
currentMenu: tasks
---

# Tasks

To activate a task, it is sufficient to add an empty task configuration:

```yml
# checker.yml
parameters:
  tasks: # Merged in default task configuration
    grunt: ~
    gulp: ~
    make: ~
    robo: ~
```

Every task has it's own default configuration. It is possible to overwrite the parameters per task.

- [Grunt](tasks/grunt.md)
- [Gulp](tasks/gulp.md)
- [Make](tasks/make.md)
- [Robo](tasks/robo.md)

## Creating a custom task

It is very easy to configure your own project specific task.
You just have to create a class that implements the `ClickNow\Checker\Task\TaskInterface`.
Next register it to the service manager and add your task configuration.

For example:

```yaml
# checker.yml
parameters:
  tasks:
    myConfigKey:
      config1: config-value

services:
  task.myCustomTask:
    class: My\Custom\Task # Must implement `ClickNow\Checker\Task\TaskInterface`
    tags:
      - { name: checker.task, config: myConfigKey }
```

> **Note:** 
You do NOT have to add the main and task configuration.
This example just shows you how to do it.

***
See also:

- [Installation](Installation.md)
- [Configuration](Configuration.md)
- [Parameters](Parameters.md)
- [Commands](Commands.md)
- [Events](Events.md)
- [Contributing](../CONTRIBUTING.md)
- [License](../LICENSE.md)
