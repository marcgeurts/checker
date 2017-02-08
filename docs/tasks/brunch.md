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

This option specifies which Brunch task you want to run.
This means that brunch will run the `build` task.
Note that this task should be used to compile your assets. 
It is also possible to alter code during commit, but this is surely **NOT** recommended!

### env

*Default: 'production'*

This option specifies which format you want to compile your assets.
E.g: `--env production`. You can specify the env you set up in your brunch config file.

### jobs

*Default: 4*

This option specifies experimental multi-process support.
May improve compilation speed of large projects.
Try different WORKERS amount to see which one works best for your system.

### debug

*Default: false*

This option specifies verbose debug output.
