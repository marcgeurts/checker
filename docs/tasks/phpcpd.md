---
currentMenu: tasks
---

# PHP Copy/Paste Detector (phpcpd)

[See oficinal documentation](https://github.com/sebastianbergmann/phpcpd)

The PhpCpd task will sniff your code for duplicated lines.
It lives under the `phpcpd` namespace and has following configurable parameters:

```yml
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
      ansi: false
      no-ansi: false
      finder:
        name: ['*.php']
        not-path: ['vendor']
```

### paths

*Default: '.'*

This option specify which will files or directory you want to run (must be relative to cwd).
This option is set to `.` by default.
This means that we will run in the root.

### log-pmd

*Default: null*

This option specify to write result in PMD-CPD XML format to file.

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

*Default: false*

This option specify if will force ANSI.

### no-ansi

*Default: false*

This option specify if will disable ANSI.

### finder

*Default: {name: ['*.php'], not-path: ['vendor']}*

[See documentation](../tasks.md#finder)
