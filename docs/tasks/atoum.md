---
currentMenu: tasks
---

# Atoum

[See oficinal documentation](http://docs.atoum.org/)

The Atoum task will run your unit tests.
It lives under the `atoum` namespace and has following configurable parameters:

```yml
# checker.yml
parameters:
  tasks:
    atoum:
      configuration: .atoum.php
      bootstrap-file: tests/units/bootstrap.php
      directories:
        - tests/units/db/
        - tests/units/entities/
      files
        - tests/units/db/mysql.php
        - tests/units/db/pgsql.php
      namespaces
        - mageekguy\\atoum\\tests\\units\\asserters
      methods:
        - vendor\\project\\test\\units\\myClass::testMyMethod
        - vendor\\project\\test\\units\\myClass::*
        - *::testMyMethod
      tags
        - OneTag
        - TwoTag
```

### configuration

*Default: null*

If your `.atoum.php` file is located at an exotic location,
you can specify your custom location with this option.
This option is set to `null` by default.
This means that `.atoum.php` is automatically loaded if the file exists in the current directory.

### bootstrap-file

*Default: null*

The path to your bootstrap file if you need any.

### directories

*Default: []*

If you want to limit the execution of the unit tests to certain directories, list them here.

### files

*Default: []*

If you want to limit the execution of the unit tests to certain files, list them here.

### namespaces

*Default: []*

If you want to limit the execution of the unit tests to certain namespaces, list them here.

### methods

*Default: []*

If you want to limit the execution of the unit tests to certain methods or classes, list them here.

### tags

*Default: []*

If you want to limit the execution of the unit tests to certain tags, list them here.
