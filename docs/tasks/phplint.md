---
currentMenu: tasks
---

# PHPLint

The PHPLint task will check your source files for syntax errors.

[See more information](https://github.com/JakubOnderka/PHP-Parallel-Lint/)

## Installation

Use the following command to install:

```
composer require --dev jakub-onderka/php-parallel-lint
```

[See more information](https://github.com/JakubOnderka/PHP-Parallel-Lint/#install)

## Configuration

It lives under the `phplint` namespace and has following configurable parameters:

```yaml
# checker.yml
parameters:
  tasks:
    phplint:
      finder:
        extensions: ['php', 'php3', 'php4', 'php5', 'phtm']
```

## Parameters

### finder

*Default: {extensions: ['php', 'php3', 'php4', 'php5', 'phtm']}*

[See documentation](../tasks.md#finder)
