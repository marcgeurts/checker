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

This option specifies which the alignment of your file.
Possible values can be `left` or `right`.

### directory

*Default: 'features'*

This option specifies which the location of your Gherkin feature files.
By default the Behat prefered `features` folder is chosen.

### finder

*Default: {extensions: ['feature']}*

[See documentation](../tasks.md#finder)
