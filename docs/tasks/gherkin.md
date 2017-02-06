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
```

### align

*Default: null*

This option will specify the alignment of your file.
Possible values can be `left` or `right`.

### directory

*Default: 'features'*

This option will specify the location of your Gherkin feature files.
By default the Behat prefered `features` folder is chosen.
