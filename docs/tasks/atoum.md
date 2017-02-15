---
currentMenu: tasks
---

# Atoum

[See oficinal documentation](http://atoum.org/)

The Atoum task will run your unit tests.
It lives under the `atoum` namespace and has following configurable parameters:

```yaml
# checker.yml
parameters:
  tasks:
    atoum:
      configuration: ~
      php: ~
      default-report-title: ~
      score-file: ~
      max-children-number: ~
      no-code-coverage: false
      no-code-coverage-in-directories: []
      no-code-coverage-for-namespaces: []
      no-code-coverage-for-classes: []
      no-code-coverage-for-methods: []
      enable-branch-and-path-coverage: false
      files: []
      directories: []
      test-file-extensions: []
      glob: []
      tags: []
      methods: []
      namespaces: []
      force-terminal: false
      autoloader-file: ~
      bootstrap-file: ~
      use-light-report: false
      use-tap-report: false
      debug: false
      xdebug-config: false
      fail-if-void-methods: false
      fail-if-skipped-methods: false
      verbose: false
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

### php

*Default: null*

This option specify which will path to 
PHP binary which must be used to run tests.

### default-report-title

*Default: null*

This option specify which will default report title.

### score-file

*Default: null*

This option specify which will file save score.

### max-children-number

*Default: null*

This option specify which will maximum number
of sub-process which will be run simultaneously.

### no-code-coverage

*Default: false*

This option specify if will disable code coverage.

### no-code-coverage-in-directories

*Default: []*

This option specify which will the directories
to disable the code coverage.

### no-code-coverage-for-namespaces

*Default: []*

This option specify which will the namespaces
to disable the code coverage.

### no-code-coverage-for-classes

*Default: []*

This option specify which will the classes
to disable the code coverage.

### no-code-coverage-for-methods

*Default: []*

This option specify which will the methods
to disable the code coverage.

### enable-branch-and-path-coverage

*Default: false*

This option specify if will enable branch and path coverage.

### files

*Default: []*

This option specify which will execution unit test files.

### directories

*Default: []*

This option specify which will limit the execution
of the unit tests files in directories.

### test-file-extensions

*Default: []*

This option specify which will the extensions
of test files to run.

### glob

*Default: []*

This option specify which will limit the execution
of the unit tests files which match pattern.

### tags

*Default: []*

This option specify which will limit the execution
of the unit tests to certain tags.

### methods

*Default: []*

This option specify which will limit the execution
of the unit tests to certain methods or classes.

### namespaces

*Default: []*

This option specify which will limit the execution
of the unit tests to certain namespaces.

### force-terminal

*Default: false*

This option specify if will force output as in terminal.

### autoloader-file

*Default: null*

This option specify which will the path to your autoloader file.

### bootstrap-file

*Default: null*

This option specify which will the path to your bootstrap file.

### use-light-report

*Default: false*

This option specify if will use lighter report.

### use-tap-report

*Default: false*

This option specify if will use tap report.

### debug

*Default: false*

This option specify if will enable debug mode.

### xdebug-config

*Default: false*

This option specify if will set `XDEBUG_CONFIG` variable.

### fail-if-void-methods

*Default: false*

This option specify if will make the test suite
fail if there is at least one void tests method.

### fail-if-skipped-methods

*Default: false*

This option specify if will make the test suite
fail if there is at least one skipped tests method.

### verbose

*Default: false*

This option specify if will verbose mode.

### finder

*Default: {extensions: ['php']}*

[See documentation](../tasks.md#finder)
