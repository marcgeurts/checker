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
```

### config

*Default: null*

If your `behat.yml` file is located at an exotic location,
you can specify your custom location with this option.
This option is set to `null` by default.
This means that `behat.yml` is automatically loaded if the file exists in the current directory.

### format

*Default: []*

If you want to use a different formatter than the default one, list them here.

### suite

*Default: []*

If you want to run a particular suite only, specify it with this option.
