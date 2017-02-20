---
currentMenu: tasks
---

# PHP Mess Detector (phpmd)

The PhpMd task will sniff your code for bad coding standards.

[See more information](http://phpmd.org/)

## Installation

Use the following command to install:

```bash
composer require --dev phpmd/phpmd
```

[See more information](https://phpmd.org/download/index.html)

## Configuration

It lives under the `phpmd` namespace and has following configurable parameters:

```yaml
# checker.yml
parameters:
  tasks:
    phpmd:
      ruleset: ['cleancode', 'codesize', 'controversial', 'design', 'naming', 'unusedcode']
      minimum-priority: ~
      strict: false
      coverage: ~
      reportfile: ~
      reportfile-html: ~
      reportfile-text: ~
      reportfile-xml: ~
      finder:
        extensions: ['php']
```

## Parameters

### ruleset

*Default: ['cleancode', 'codesize', 'controversial', 'design', 'naming', 'unusedcode']*

This option specify which will rule/rulesets you want to use.
You can use the standard sets provided or you can configure your own xml.

### minimum-priority

*Default: null*

This option specify which will rules with lower priority than they will not be used.

### strict

*Default: false*

This option specify if will include to report those nodes with a `@SuppressWarnings` annotation.

### coverage

*Default: null*

This option specify which will code coverage report.

### reportfile

*Default: null*

This option specify which will report file.

### reportfile-html

*Default: null*

This option specify which will html report file.

### reportfile-text

*Default: null*

This option specify which will text report file.

### reportfile-xml

*Default: null*

This option specify which will xml report file.

### finder

*Default: {extensions: ['php']}*

[See documentation](../tasks.md#finder)
