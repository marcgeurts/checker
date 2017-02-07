---
currentMenu: tasks
---

# Behat

[See oficinal documentation](http://behat.org/)

The Behat task will run your Behat tests.
It lives under the `behat` namespace and has following configurable parameters:

```yml
# checker.yml
parameters:
  tasks:
    behat:
      config: ~
      format: []
      suite: ~
      finder:
        extensions: ['php']
```

### config

*Default: null*

If your `behat.yml` file is located at an exotic location,
you can specify your custom location with this option.
This means that `behat.yml` or `behat.yml.dist` is automatically loaded
if the file exists in the current directory.

### format

*Default: []*

This option specify to use a different formatter than the default.

### suite

*Default: []*

This option specify to run a particular suite only.

### finder

*Default: {extensions: ['php']}*

[See documentation](../tasks.md#finder)
