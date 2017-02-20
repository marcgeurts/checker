---
currentMenu: tasks
---

# PHP Copy/Paste Detector (phpcpd)

The PhpCpd task will sniff your code for duplicated lines.

[See more information](https://github.com/sebastianbergmann/phpcpd)

## Installation

Use the following command to install:

```bash
composer require --dev sebastian/phpcpd
```

## Configuration

It lives under the `phpcpd` namespace and has following configurable parameters:

```yaml
# checker.yml
parameters:
  tasks:
    phpcpd:
      paths: '.'
      log-pmd: ~
      min-lines: ~
      min-tokens: ~
      fuzzy: false
      quiet: false
      verbose: false
      ansi: true
      no-ansi: false
      finder:
        name: ['*.php']
        not-path: ['vendor']
```

## Parameters

### paths

*Default: '.'*

This option specify which will files or directory 
you want to run (must be relative to cwd).
This option is set to `.` by default.
This means that we will run in the root.

### log-pmd

*Default: null*

This option specify which will log file.

### min-lines

*Default: null*

This option specify which will minimum number of identical lines.

### min-tokens

*Default: null*

This option specify which will minimum number of identical tokens.

### fuzzy

*Default: false*

This option specify if will fuzz variable names.

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

### finder

*Default: {name: ['*.php'], not-path: ['vendor']}*

[See documentation](../tasks.md#finder)
