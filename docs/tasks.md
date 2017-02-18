---
currentMenu: tasks
---

# Tasks

To activate a task, it is sufficient to add an empty task configuration:

```yaml
# checker.yml
parameters:
  tasks: # Merged in default task configuration
    ant: ~
    atoum: ~
    behat: ~
    brunch: ~
    codeception: ~
    doctrine-orm: ~
    gherkin: ~
    grunt: ~
    gulp: ~
    make: ~
    npm-script: ~
    phpcpd: ~
    phpmd: ~
    phpunit: ~
    robo: ~
```

Every task has it's own default configuration.
It is possible to overwrite the parameters per task.

- [Apache Ant](tasks/ant.md)
- [Atoum](tasks/atoum.md)
- [Behat](tasks/behat.md)
- [Brunch](tasks/brunch.md)
- [Codeception](tasks/codeception.md)
- [Doctrine ORM](tasks/doctrine-orm.md)
- [Gherkin](tasks/gherkin.md)
- [Grunt](tasks/grunt.md)
- [Gulp](tasks/gulp.md)
- [Make](tasks/make.md)
- [NPM script](tasks/npm-script.md)
- [PHP Copy/Paste Detector (phpcpd)](tasks/phpcpd.md)
- [PHP Mess Detector (phpmd)](tasks/phpmd.md)
- [PHPUnit](tasks/phpunit.md)
- [Robo](tasks/robo.md)

> **Note:** Some options of the tasks related to files and/or directory 
do not exist because the `finder` option abstracts the majority.

The above tasks also have the following configuration options:

### can-run-in

*Default: true*

This option allows you to run such a task in a particular context or command.
You can also specify an array with the names of contexts or commands to can run in.

### always-execute

*Default: false*

This option always run the whole task, even if no files were found.

### finder

*Default: []*

This option to allow finder specify files and directories to be found.
Options available: `name`, `not-name`, `path`, `not-path`, `extensions`.
All options accepts only `array`.

For example:

```yaml
# checker.yml
parameters:
  tasks:
    name_of_task:
      finder:
        name: ['name1']
        not-name: ['name2', 'name3']
        path: ['path1', 'path2']
        not-path: ['path3']
        extensions: ['php', 'phtml']
```

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

- [Installation](installation.md)
- [Configuration](configuration.md)
- [Parameters](parameters.md)
- [Command-Line](command-line.md)
- [Events](events.md)
- [Contributing](../CONTRIBUTING.md)
- [License](../LICENSE.md)
