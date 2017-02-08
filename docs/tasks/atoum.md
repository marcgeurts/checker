---
currentMenu: tasks
---

# Atoum

[See oficinal documentation](http://atoum.org/)

The Atoum task will run your unit tests.
It lives under the `atoum` namespace and has following configurable parameters:

```yml
# checker.yml
parameters:
  tasks:
    atoum:
      configuration: ~
      bootstrap-file: ~
      namespaces: []
      methods: []
      tags: []
      finder:
        extensions: ['php']
```

### configuration

*Default: null*

If your `.atoum.php` file is located at an exotic location,
you can specify your custom location with this option.
This option is set to `null` by default. 
This means that `.atoum.php` is automatically loaded
if the file exists in the current directory.

### bootstrap-file

*Default: null*

This option specify the path to your bootstrap file if you need any.

### namespaces

*Default: []*

This option specify to limit the execution of the unit tests to certain namespaces.

### methods

*Default: []*

This option specify to limit the execution of the unit tests to certain methods or classes.

### tags

*Default: []*

This option specify to limit the execution of the unit tests to certain tags.

### finder

*Default: {extensions: ['php']}*

[See documentation](../tasks.md#finder)
