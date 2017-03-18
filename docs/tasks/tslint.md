---
currentMenu: tasks
---

# TSLint

The TsLint task checks your TypeScript code for readability, maintainability, and functionality errors.

[See more information](https://palantir.github.io/tslint/)

## Installation

Use the following command to install:

```
npm install --save-dev tslint typescript
```

[See more information](https://palantir.github.io/tslint/usage/cli/)

## Configuration

It lives under the `tslint` namespace and has following configurable parameters:

```yaml
# checker.yml
parameters:
  tasks:
    tslint:
      config: ~
      exclude: []
      fix: false
      force: false
      init: false
      out: ~
      project: ~
      rules-dir: ~
      formatters-dir: ~
      format: ~
      test: false
      type-check: false
      finder:
        extensions: ['ts']
```

## Parameters

### config

*Default: null*

If your `tslint.json` file is located at an exotic location,
you can specify your custom location with this option.
This option is set to `null` by default.
This means that `tslint.json` is automatically loaded
if the file exists in the current directory.

### exclude

*Default: []*

This option specify which will filenames or glob to excluded.

### fix

*Default: false*

This option specify if will fixes linting errors for select rules. 

### force

*Default: false*

This option specify if will return status code 0 even 
if there are any lint errors.

### init

*Default: false*

This options specify if will generates a `tslint.json`
config file in the current working directory.

### out

*Default: null*

This option specify a filename to output result.
This option is set to `null` by default.
This means that  `tslint` outputs to stdout,
which is usually the console where you're running it from.

### project

*Default: null*

This option specify wich will the location of a `tsconfig.json`
file that will be used to determine which files will be linted.

### rules-dir

*Default: null*

This option specify which wil an additional rules directory.

### formatters-dir

*Default: null*

This option specify which wil an additional formatters directory.

### format

*Default: null*

This option specify which will the formatter to use to format the results.

### test

*Default: false*

This optiosn specify if will test that `tslint` produces the correct output.

### type-check

*Default: false*

This options specify if will enables the type checker when running.

### finder

*Default: {extensions: ['ts']}*

[See documentation](../tasks.md#finder)
