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
      php: ~
      short-open-tag: false
      asp-tags: false
      exclude: []
      jobs: ~
      no-colors: true
      blame: false
      git: ~
      ignore-fails: false
      finder:
        extensions: ['php', 'php3', 'php4', 'php5', 'phtm']
```

## Parameters

### php

*Default: null*

This option specify path to `php`.

### short-open-tag

*Default: false*

This option specify if will enable `short_open_tags`.

### asp-tags

*Default: false*

This option specify if will enable `asp_tags`.

### exclude

*Default: []*

This option specify which will directories excluded.

### jobs

*Default: null*

This option specify which will the number of jobs you wish to use for parallel processing.

### no-colors

*Default: true*

This option specify if will disable colors in console output.

### blame

*Default: false*

This option specify if will try to show git blame for row with error.

### git

*Default: null*

This option specify path to `git`.

### ignore-fails

*Default: false*

This option specify if will ignore failed tests.

### finder

*Default: {extensions: ['php', 'php3', 'php4', 'php5', 'phtm']}*

[See documentation](../tasks.md#finder)
