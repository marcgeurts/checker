---
currentMenu: tasks
---

# PHPUnit

The PhpUnit task will run your unit tests.

[See more information](http://phpunit.de/)

## Installation

Use the following command to install:

```
composer require --dev phpunit/phpunit
```

## Configuration

It lives under the `phpunit` namespace and has following configurable parameters:

```yaml
# checker.yml
parameters:
  tasks:
    phpunit:
      coverage-clover: ~
      coverage-crap4j: ~
      coverage-html: ~
      coverage-php: ~
      coverage-text: ~
      coverage-xml: ~
      log-junit: ~
      log-tap: ~
      log-json: ~
      testdox-html: ~
      testdox-text: ~
      filter: ~
      testsuite: ~
      group: []
      exclude-group: []
      test-suffix: []
      report-useless-tests: false
      strict-coverage: false
      strict-global-state: false
      disallow-test-output: false
      enforce-time-limit: false
      disallow-todo-tests: false
      process-isolation: false
      no-globals-backup: false
      static-backup: false
      colors: ~
      columns: ~
      stderr: false
      stop-on-error: false
      stop-on-failure: false
      stop-on-risky: false
      stop-on-skipped: false
      stop-on-incomplete: false
      verbose: false
      debug: false
      loader: ~
      repeat: ~
      tap: false
      testdox: false
      printer: ~
      bootstrap: ~
      configuration: ~
      no-configuration: false
      no-coverage: false
      include-path: ~
      finder:
        extensions: ['php']
```

## Parameters

### coverage-clover

*Default: null*

This option specify which will clover report path of code coverage.

### coverage-crap4j

*Default: null*

This option specify which will crap4j xml report path of code coverage.

### coverage-html

*Default: null*

This option specify which will html report path of code coverage.

### coverage-php

*Default: null*

This option specify which will path file to object `PHP_CodeCoverage`.

### coverage-text

*Default: null*

This option specify which will text report path of code coverage.

### coverage-xml

*Default: null*

This option specify which will xml report path of code coverage.

### log-junit

*Default: null*

This option specify which will log file in junit xml format.

### log-tap

*Default: null*

This option specify which will log file in tap format.

### log-json

*Default: null*

This option specify which will log file in json format.

### testdox-html

*Default: null*

This option specify which will file write with agile documentation in html format.

### testdox-text

*Default: null*

This option specify which will file write with agile documentation in text format.

### filter

*Default: null*

This option specify which will filter which tests to run.

### testsuite

*Default: null*

This option specify which will filter which testsuite to run.

### group

*Default: []*

This option specify which will groups to run.

### exclude-group

*Default: []*

This option specify which will exclude groups.

### test-suffix

*Default: []*

This option specify which will only search for test in files with specified suffixes.

### report-useless-tests

*Default: false*

This option specify if will be strict about tests that do not test anything.

### strict-coverage

*Default: false*

This option specify if will be strict about unintentionally covered code.

### strict-global-state

*Default: false*

This option specify if will be strict about changes to global state.

### disallow-test-output

*Default: false*

This option specify if will be strict about output during tests.

### enforce-time-limit

*Default: false*

This option specify if will enforce time limit based on test size.

### disallow-todo-tests

*Default: false*

This option specify if will disallow `@todo-annotated` tests.

### process-isolation

*Default: false*

This option specify if will run each test in a separate process.

### no-globals-backup

*Default: false*

This option specify if will do not backup and restore `$GLOBALS` for each test.

### static-backup

*Default: false*

This option specify if will backup and restore static attributes for each test.

### colors

*Default: null*

This option specify which will use colors in output.

### columns

*Default: null*

This option specify which will number of columns to use for progress output.

### stderr

*Default: false*

This option specify if will write to `STDERR` instead of `STDOUT`.

### stop-on-error

*Default: false*

This option specify if will stop execution on first error.

### stop-on-failure

*Default: false*

This option specify if will stop execution on first error or failure.

### stop-on-risky

*Default: false*

This option specify if will stop execution on first risky test.

### stop-on-skipped

*Default: false*

This option specify if will stop execution on first skipped test.

### stop-on-incomplete

*Default: false*

This option specify if will stop execution on first incomplete test.

### verbose

*Default: false*

This option specify if will verbose mode.

### debug

*Default: false*

This option specify if will enable debug mode.

### loader

*Default: null*

This option specify which will `TestSuiteLoader` implementation to use.

### repeat

*Default: null*

This option specify which will number of times tests runs repeatedly.

### tap

*Default: false*

This option specify if will report test execution progress in tap format.

### testdox

*Default: false*

This option specify if will report test execution progress in testdox format.

### printer

*Default: null*

This option specify which will `TestListener` implementation to use.

### bootstrap

*Default: null*

This option specify which will the path to your bootstrap file.

### configuration

*Default: null*

If your `phpunit.xml` file is located at an exotic location,
you can specify your custom config file location with this option.
This option is set to `null` by default.
This means that `phpunit.xml` or `phpunit.xml.dist` are automatically loaded
if one of them exist in the current directory.

### no-configuration

*Default: false*

This option specify if will ignore default configuration file.

### no-coverage

*Default: false*

This option specify if will ignore code coverage configuration.

### include-path

*Default: null*

This option specify which will include path.

### finder

*Default: {extensions: ['php']}*

[See documentation](../tasks.md#finder)
