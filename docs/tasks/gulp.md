---
currentMenu: tasks
---

# Gulp

The Gulp task will run your automated workflow tasks.

[See more information](http://gulpjs.com/)

## Installation

Use the following command to install:

```
npm install -g gulp-cli
```

## Configuration

It lives under the `gulp` namespace and has following configurable parameters:

```yaml
# checker.yml
parameters:
  tasks:
    gulp:
      gulpfile: ~
      task: ~
```

## Parameters

### gulpfile

*Default: null*

If your `gulpfile.js` file is located at an exotic location,
you can specify your custom location with this option.
This option is set to `null` by default.
This means that `gulpfile.js` is automatically loaded
if the file exists in the current directory.

### task

*Default: null*

This option specify which will task you want to run.
This option is set to `null` by default.
This means that will run the `default` task.
