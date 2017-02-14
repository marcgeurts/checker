---
currentMenu: tasks
---

# PHP 7 Compatibility Checker (php7cc)

[See oficinal documentation](https://github.com/sstalle/php7cc)

The Php7cc task will check PHP 5.3 - 5.6 code compatibility with PHP 7.
It lives under the `php7cc` namespace and has following configurable parameters:

```yml
# checker.yml
parameters:
  tasks:
    php7cc:
      level: ~
      relative-paths: false
      integer-size: ~
      quiet: false
      verbose: false
      ansi: true
      no-ansi: false
      finder:
        extensions: ['php']
```

### level

*Default: null*

This option specify which will minimum issue level.
There are 3 issue levels: `info`, `warning` and `error`.
`info` is reserved for future use and is the same as `warning`.

### relative-paths

*Default: false*

This option specify which will output paths relative to a 
checked directory instead of full paths to files.

### integer-size

*Default: null*

This option specify which will target system's integer size in bits.

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

*Default: {extensions: ['php']}*

[See documentation](../tasks.md#finder)
