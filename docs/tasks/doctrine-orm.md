---
currentMenu: tasks
---

# Doctrine ORM

The Doctrine ORM task will validate that your mapping files and check if the mapping is in sync with the database.

[See more information](http://doctrine-project.org/)

## Installation

Use the following command to install:

```bash
composer require doctrine/orm
```

[See more information](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/configuration.html)

## Configuration

It lives under the `doctrine` namespace and has following configurable parameters:

```yaml
# checker.yml
parameters:
  tasks:
    doctrine-orm:
      skip-mapping: false
      skip-sync: false
      quiet: false
      verbose: false
      ansi: true
      no-ansi: false
      finder:
        extensions: ['php', 'xml', 'yml']
```

## Parameters

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

*Default: true*

This option specify if will force ansi.

### no-ansi

*Default: false*

This option specify if will disable ansi.

### finder

*Default: {extensions: ['php', 'xml', 'yml']}*

[See documentation](../tasks.md#finder)
