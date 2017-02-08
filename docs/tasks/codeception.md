---
currentMenu: tasks
---

# Codeception

[See oficinal documentation](http://http://codeception.com/)

The Codeception task will run your full-stack tests.
It lives under the `codeception` namespace and has following configurable parameters:

```yml
# checker.yml
parameters:
  tasks:
    codeception:
      override: []
      config: ~
      report: false
      silent: false
      steps: false
      debug: false
      group: []
      skip: []
      skip-group: []
      env: []
      fail-fast: false
      suite: ~
      test: ~
```

### override

*Default: []*

This option specify which will config values override.

### config

*Default: null*

If your `codeception.yml` file is located at an exotic location,
you can specify your custom config file location with this option.
This option is set to `null` by default.
This means that `codeception.yml` or `codeception.dist.yml` are automatically loaded
if one of them exist in the current directory.

### report

*Default: false*

This option specify to show output in compact style.

### silent

*Default: false*

This option specify to only outputs suite names and final results.

### steps

*Default: false*

This option specify to show steps in output.

### debug

*Default: false*

This option specify to show debug and scenario output.

### group

*Default: []*

This option specify which groups of tests to be executed.

### skip

*Default: []*

This option specify to skip selected suites.

### skip-group

*Default: []*

This option specify to skip selected groups.

### env

*Default: []*

This option specify to run tests in selected environments.

### fail-fast

*Default: false*

This option specify to stop at the first error.
This means that it will not run your full test suite when an error occurs.

### suite: ~

*Default: null*

This option specify which suite to be tested.
This option is set to `null` by default.
This means that it will run tests for your full test suite.

### test: ~

*Default: null*

This option specify which test to be run.
This option is set to `null` by default.
This means that it will run all tests within the suite.
Note that this option can only be used in combination with a suite.
