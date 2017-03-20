---
currentMenu: tasks
---

# SassLint

The SassLint task linter for both sass and scss syntax.

[See more information](https://github.com/sasstools/sass-lint)

## Installation

Use the following command to install:

```
npm install --save-dev sass-lint
```

[See more information](https://github.com/sasstools/sass-lint/tree/master/docs/cli)

## Configuration

It lives under the `sasslint` namespace and has following configurable parameters:

```yaml
# checker.yml
parameters:
  tasks:
    tslint:
      config: ~
      format: ~
      ignore: []
      max-warnings: ~
      output: ~
      no-exit: false
      syntax: false
      verbose: false
      finder:
        extensions: ['sass', 'scss']
```

## Parameters

### config

*Default: null*

If your `.sass-lint.yml` file is located at an exotic location,
you can specify your custom location with this option.
This option is set to `null` by default.
This means that `.sass-lint.yml` is automatically loaded
if the file exists in the current directory.

### format

*Default: null*

This option specify which will output format.

### ignore

*Default: []*

This option specify which will patterns of files to ignore.

### max-warnings

*Default: null*

This option specify which will warning threshold.

### output

*Default: null*

This option specify which will report output file.

### no-exit

*Default: false*

This option specify if will prevents from throwing an error if there is one.

### syntax

*Default: false*

This option specify if will evaluate the given file(s) with, either sass or scss.

### verbose

*Default: false*

This option specify if will verbose mode.

### finder

*Default: {extensions: ['sass', 'scss']}*

[See documentation](../tasks.md#finder)
