---
currentMenu: tasks
---

# NPM script

[See oficinal documentation](https://www.npmjs.com/)

The NPM script task will run your configured npm script.
It lives under the `npm-script` namespace and has following configurable parameters:

```yml
# checker.yml
parameters:
  tasks:
    npm-script:
      script: ~
      working-directory: './'
      is-run-task: false
```

### script

*Default: null*

This option specifies which NPM script you want to run.
This option is set to `null` by default.
This means that will stop immediately.
Note that this script should be used to verify things.
It is also possible to alter code during commit, but this is surely **NOT** recommended!

### working-directory

*Default: './'*

This option specifies in which directory the NPM script should be run.

### is-run-task

*Default: false*

This option will append `run` to the npm command to make it possible to run custom npm scripts.
