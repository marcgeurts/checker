---
currentMenu: tasks
---

# NPM script

The NPM script task will run your configured npm script.

[See more information](http://npmjs.com/)

## Installation

Use the following command to install:

```bash
npm install npm@latest -g
```

[See more information](https://docs.npmjs.com/getting-started/installing-node)

## Configuration

It lives under the `npm-script` namespace and has following configurable parameters:

```yaml
# checker.yml
parameters:
  tasks:
    npm-script:
      is-run-task: false
      script: ~
      working-directory: './'
```

## Parameters

### is-run-task

*Default: false*

This option specify if will append `run` to the command to make it possible to run custom scripts.

### script

*Default: null*

This option specify which will script you want to run.
This option is set to `null` by default.
This means that will stop immediately.

### working-directory

*Default: './'*

This option specify which will working directory.
This option is set to `./` by default.
This means that we will run in the root.
