---
currentMenu: tasks
---

# Codeception

The Codeception task will run your full-stack tests.

[See more information](http://codeception.com/)

## Installation

Use the following command to install:

```
composer require codeception/codeception
```

[See more information](http://codeception.com/install)

## Configuration

It lives under the `codeception` namespace and has following configurable parameters:

```yaml
# checker.yml
parameters:
  tasks:
    codeception:
      suite: ~
      test: ~
      override: []
      config: ~
      report: false
      html: ~
      xml: ~
      tap: ~
      json: ~
      colors: false
      no-colors: false
      silent: false
      steps: false
      debug: false
      coverage: ~
      coverage-html: ~
      coverage-xml: ~
      coverage-text: ~
      coverage-crap4j: ~
      group: []
      skip: []
      skip-group: []
      env: []
      fail-fast: false
      no-rebuild: false
      quiet: false
      verbose: false
      ansi: true
      no-ansi: false
```

## Parameters

### suite: ~

*Default: null*

This option specify which will suite to be tested.
This option is set to `null` by default.
This means that it will run tests for your full test suite.

### test: ~

*Default: null*

This option specify which will test to be run.
This option is set to `null` by default.
This means that it will run all tests within the suite.
Note that this option can only be used in combination with a suite.

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

This option specify if will show output in compact style.

### html

*Default: null*

This option specify which will html file with results.

### xml

*Default: null*

This option specify which will xml file with results.

### tap

*Default: null*

This option specify which will tap file with results.

### json

*Default: null*

This option specify which will json file with results.

### colors

*Default: false*

This option specify if will color.

### no-colors

*Default: false*

This option specify will to don't color.

### silent

*Default: false*

This option specify if will only outputs suite names and final results.

### steps

*Default: false*

This option specify if will show steps in output.

### debug

*Default: false*

This option specify if will enable debug mode.

### coverage

*Default: null*

This option specify which will run with code coverage.

### coverage-html

*Default: null*

This option specify which will html report path of code coverage.

### coverage-xml

*Default: null*

This option specify which will xml report path of code coverage.

### coverage-text

*Default: null*

This option specify which will text report path of code coverage.

### coverage-crap4j

*Default: null*

This option specify which will crap4j xml report path of code coverage.

### group

*Default: []*

This option specify which will groups of tests to be executed.

### skip

*Default: []*

This option specify which will skip selected suites.

### skip-group

*Default: []*

This option specify which will skip selected groups.

### env

*Default: []*

This option specify which will run tests in selected environments.

### fail-fast

*Default: false*

This option specify if will stop processing on first failed.
This means that it will not run your full test suite when an error occurs.

### no-rebuild

*Default: false*

This option specify if will do not rebuild actor classes on start.

### quiet

*Default: false*

This option specify if will quiet mode.

### verbose

*Default: false*

This option specify if will verbose mode.

### ansi

*Default: true*

This option specify if will force ansi.

### no-ansi

*Default: false*

This option specify if will disable ansi.
