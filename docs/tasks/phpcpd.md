---
currentMenu: tasks
---

# PhpCpd

[See oficinal documentation](https://github.com/sebastianbergmann/phpcpd)

The PhpCpd task will sniff your code for duplicated lines.
It lives under the `phpcpd` namespace and has following configurable parameters:

```yml
# checker.yml
parameters:
  tasks:
    phpcpd:
      paths: '.'
      min-lines: ~
      min-tokens: ~
      fuzzy: false
      finder:
        name: ['*.php']
        not-path: ['vendor']
```

### paths

*Default: '.'*

This option specify which files or directory you want to run (must be relative to cwd).
This option is set to `.` by default.
This means that we will run in the root.

### min-lines

*Default: null*

This option specify minimum number of identical lines.

### min-tokens

*Default: null*

This option specify minimum number of identical tokens.

### fuzzy

*Default: false*

This option specify to fuzz variable names.

### finder

*Default: {name: ['*.php'], not-path: ['vendor']}*

[See documentation](../tasks.md#finder)
