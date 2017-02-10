---
currentMenu: tasks
---

# PHPUnit

[See oficinal documentation](http://phpunit.de/)

The PhpUnit task will run your unit tests.
It lives under the `phpunit` namespace and has following configurable parameters:

```yml
# checker.yml
parameters:
  tasks:
    phpunit:
      configuration: ~
      group: []
      finder:
        extensions: ['php']
```

### configuration

*Default: null*

If your `phpunit.xml` file is located at an exotic location,
you can specify your custom config file location with this option.
This option is set to `null` by default.
This means that `phpunit.xml` or `phpunit.xml.dist` are automatically loaded
if one of them exist in the current directory.

### group

*Default: []*

This option specify to only run tests from a certain group.

### finder

*Default: {extensions: ['php']}*

[See documentation](../tasks.md#finder)
