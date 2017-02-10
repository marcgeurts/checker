---
currentMenu: tasks
---

# Doctrine ORM

[See oficinal documentation](http://doctrine-project.org/)

The Doctrine ORM task will validate that your mapping files and check if the mapping is in sync with the database.
It lives under the `doctrine` namespace and has following configurable parameters:

```yml
# checker.yml
parameters:
  tasks:
    doctrine-orm:
      skip-mapping: false
      skip-sync: false
      quiet: false
      verbose: false
      ansi: false
      no-ansi: false
      finder:
        extensions: ['php', 'xml', 'yml']
```

### skip-mapping

*Default: []*

This option specify if will skip the mapping validation check.

### skip-sync

*Default: []*

This option specify if will skip checking if the mapping is in sync with the database.

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

*Default: {extensions: ['php', 'xml', 'yml']}*

[See documentation](../tasks.md#finder)
