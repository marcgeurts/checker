---
currentMenu: tasks
---

# NPM script

[See oficinal documentation](http://npmjs.com/)

The NPM script task will run your configured npm script.
It lives under the `npm-script` namespace and has following configurable parameters:

```yml
# checker.yml
parameters:
  tasks:
    npm-script:
      is-run-task: false
      script: ~
      working-directory: './'
```

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
