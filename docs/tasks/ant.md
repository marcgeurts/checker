---
currentMenu: tasks
---

# Ant

[See oficinal documentation](https://ant.apache.org/)

The Ant task will run your automated Ant tasks.
It lives under the `ant` namespace and has following configurable parameters:

```yml
# checker.yml
parameters:
  tasks:
    ant:
      buildfile: ~
      task: ~
```

### buildfile

*Default: null*

If your `build.xml` file is located at an exotic location,
you can specify your custom location with this option.
This means that `build.xml` is automatically loaded
if the file exists in the current directory.

### task

*Default: null*

This option specifies which Ant task you want to run.
This means that ant will run the `default` task.
Note that this task should be used to verify things. 
It is also possible to alter code during commit, but this is surely **NOT** recommended!
