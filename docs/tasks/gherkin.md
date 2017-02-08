---
currentMenu: tasks
---

# Gherkin

[See oficinal documentation](https://github.com/malukenho/kawaii-gherkin)

The Gherkin task will run your Gherkin feature files.
It lives under the `gherkin` namespace and has following configurable parameters:

```yml
# checker.yml
parameters:
  tasks:
    gherkin:
      align: ~
      directory: 'features'
      finder:
        extensions: ['feature']
```

### align

*Default: null*

This option specify which the alignment of your file.
Possible values can be `left` or `right`.

### directory

*Default: 'features'*

This option specify which the location of your feature files.
This option is set to `features` by default.
This means that will run in `features` directory.

### finder

*Default: {extensions: ['feature']}*

[See documentation](../tasks.md#finder)
