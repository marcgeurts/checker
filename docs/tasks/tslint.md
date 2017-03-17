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

### exclude

*Default: []*

### fix

*Default: false*

### force

*Default: false*

### init

*Default: false*

### out

*Default: null*

### project

*Default: null*

### rules-dir

*Default: null*

### formatters-dir

*Default: null*

### format

*Default: null*

### test

*Default: false*

### type-check

*Default: false*

### finder

*Default: {extensions: ['ts']}*

[See documentation](../tasks.md#finder)
