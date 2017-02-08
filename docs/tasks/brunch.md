---
currentMenu: tasks
---

# Brunch

[See oficinal documentation](http://brunch.io/)

The Brunch task will run your automated workflow tasks.
It lives under the `brunch` namespace and has following configurable parameters:

```yml
# checker.yml
parameters:
  tasks:
    brunch:
      task: 'build'
      env: 'production'
      jobs: 4
      debug: false
```

### task

*Default: 'build'*

This option specify which task you want to run.
This option is set to `build` by default. 
This means that will run the `build` task.

### env

*Default: 'production'*

This option specify which format you want to compile your assets.
E.g: `--env production`. You can specify the env you set up in your config file.

### jobs

*Default: 4*

This option specify experimental multi-process support.
May improve compilation speed of large projects.
Try different WORKERS amount to see which one works best for your system.

### debug

*Default: false*

This option specify verbose debug output.
