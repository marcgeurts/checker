# Tasks

## Creating a custom task

It is very easy to configure your own project specific task.
You just have to create a class that implements the `ClickNow\Checker\Task\TaskInterface`.
Next register it to the service manager and add your task configuration.

For example:

```yml
# checker.yml
parameters:
  tasks:
    myConfigKey:
      config1: config-value

services:
  task.myCustomTask:
    class: My\Custom\Task
    arguments:
      - '@config'
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
