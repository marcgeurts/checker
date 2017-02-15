---
currentMenu: tasks
---

# Behat

[See oficinal documentation](http://behat.org/)

The Behat task will run your Behat tests.
It lives under the `behat` namespace and has following configurable parameters:

```yaml
# checker.yml
parameters:
  tasks:
    behat:
      suite: ~
      format: []
      out: []
      format-settings: []
      lang: ~
      name: []
      tags: []
      role: ~
      strict: false
      order: ~
      rerun: false
      stop-on-failure: false
      profile: ~
      config: ~
      verbose: false
      colors: false
      no-colors: false
      finder:
        extensions: ['php']
```

### suite

*Default: null*

This option specify if will run a particular suite only.

### format

*Default: []*

This option specify which will formatters.

### out

*Default: []*

This option specify which will write format output.

### format-settings

*Default: []*

This option specify which will formatters settings.

### lang

*Default: null*

This option specify which will output language.

### name

*Default: []*

This option specify which will feature elements
which match part of given name or regex will be executed.

### tags

*Default: []*

This option specify which will feature or scenarios 
with tags matching tag filter expression will be executed.

### role

*Default: null*

This option specify which will features 
with role matching a wildcard will be executed.

### strict

*Default: false*

This option specify if will passes only if all tests are explicitly passing.

### order

*Default: null*

This option specify which will order of execution.

### rerun

*Default: false*

This option specify if will scenarios 
that failed during last execution will be re-run.

### stop-on-failure

*Default: false*

This option specify if will stop processing on first failed.
This means that it will not run your full test suite when an error occurs.

### profile

*Default: null*

This option specify which will profile to use.

### config

*Default: null*

If your `behat.yml` file is located at an exotic location,
you can specify your custom location with this option.
This option is set to `null` by default. 
This means that `behat.yml` or `behat.yml.dist` is automatically loaded
if the file exists in the current directory.

### verbose

*Default: false*

This option specify if will verbose mode.

### colors

*Default: false*

This option specify if will color.

### no-colors

*Default: false*

This option specify will to don't color.

### finder

*Default: {extensions: ['php']}*

[See documentation](../tasks.md#finder)
