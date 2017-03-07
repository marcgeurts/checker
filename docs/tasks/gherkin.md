---
currentMenu: tasks
---

# Gherkin

The Gherkin task will run your Gherkin feature files.

[See more information](https://github.com/malukenho/kawaii-gherkin)

## Installation

Use the following command to install:

```
composer require --dev malukenho/kawaii-gherkin
```

## Configuration

It lives under the `gherkin` namespace and has following configurable parameters:

```yaml
# checker.yml
parameters:
  tasks:
    gherkin:
      directory: 'features'
      align: ~
      quiet: false
      verbose: false
      ansi: true
      no-ansi: false
      finder:
        extensions: ['feature']
```

## Parameters

### directory

*Default: 'features'*

This option specify which will location of your feature files.
This option is set to `features` by default.
This means that will run in `features` directory.

### align

*Default: null*

This option specify which will alignment of your file.
Possible values can be `left` or `right`.

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

*Default: {extensions: ['feature']}*

[See documentation](../tasks.md#finder)
