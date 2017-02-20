---
currentMenu: tasks
---

# Apache Ant

The Ant task will run your automated Ant tasks.

[See more information](https://ant.apache.org/)

## Installation

Make sure you have installed Apache Ant.

[See more information](http://ant.apache.org/manual/install.html)

## Configuration

It lives under the `ant` namespace and has following configurable parameters:

```yaml
# checker.yml
parameters:
  tasks:
    ant:
      buildfile: ~
      task: ~
```

## Parameters

### buildfile

*Default: null*

If your `build.xml` file is located at an exotic location,
you can specify your custom location with this option.
This option is set to `null` by default. 
This means that `build.xml` is automatically loaded
if the file exists in the current directory.

### task

*Default: null*

This option specify which will task you want to run.
This option is set to `null` by default. 
This means that will run the `default` task.
