---
currentMenu: tasks
---

# PHPUnit

[See oficinal documentation](https://phpunit.de/)

The PHPUnit task will run your unit tests.
It lives under the `phpunit` namespace and has following configurable parameters:

```yml
# checker.yml
parameters:
  tasks:
    phpunit:
      configuration: ~
      group: []
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

If you wish to only run tests from a certain Group. group: [fast,quick,small]
