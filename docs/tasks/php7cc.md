---
currentMenu: tasks
---

# Php7cc

[See oficinal documentation](https://github.com/sstalle/php7cc)

The Php7cc task will check PHP 5.3 - 5.6 code compatibility with PHP 7.
It lives under the `php7cc` namespace and has following configurable parameters:

```yml
# checker.yml
parameters:
  tasks:
    php7cc:
      level: ~
      finder:
        extensions: ['php']
```

### level

*Default: null*

This option specifies which the minimum issue level.
There are 3 issue levels: "info", "warning" and "error".
"info" is reserved for future use and is the same as "warning".

### finder

*Default: {extensions: ['php']}*

[See documentation](../tasks.md#finder)
