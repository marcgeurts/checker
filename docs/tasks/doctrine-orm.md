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
      finder:
        extensions: ['php', 'xml', 'yml']
```

### skip-mapping

*Default: []*

This option specify to skip the mapping validation check.

### skip-sync

*Default: []*

This option specify to skip checking if the mapping is in sync with the database.

### finder

*Default: {extensions: ['php', 'xml', 'yml']}*

[See documentation](../tasks.md#finder)
