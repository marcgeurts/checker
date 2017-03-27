---
currentMenu: tasks
---

# StyleLint

The StyleLint task is a mighty, modern CSS linter that helps you enforce 
consistent conventions and avoid errors in your stylesheets.

[See more information](https://stylelint.io)

## Installation

Use the following command to install:

```
npm install --save-dev stylelint
```

[See more information](https://stylelint.io/user-guide/cli/)

## Configuration

It lives under the `stylelint` namespace and has following configurable parameters:

```yaml
# checker.yml
parameters:
  tasks:
    stylelint:
      config: ~
      config-basedir: ~
      ignore-path: ~
      syntax: ~
      custom-syntax: ~
      stdin-filename: ~
      ignore-disables: false
      cache: false
      cache-location: ~
      formatter: ~
      custom-formatter: ~
      quiet: false
      color: true
      no-color: false
      allow-empty-input: false
      report-needless-disables: false
      finder:
        extensions: ['extensions' => ['css', 'sass', 'scss', 'less', 'sss']
```

## Parameters

### config

*Default: null*

This option specify which will an additional configuration file.

### config-basedir

*Default: null*

This option specify which will an absolute path to the directory
that relative paths defining `extends` and `plugins` are *relative to*.

### ignore-path

*Default: null*

This option specify which will path to a file containing patterns that describe files to ignore.
This option is set to `null` by default.
This means that `.stylelintignore` is automatically loaded
if the file exists in the current directory.

### syntax

*Default: null*

This option specify which will a non-standard syntax.

### custom-syntax

*Default: null*

This option specify which will module name or path to a js file 
exporting a PostCSS-compatible syntax.

### stdin-filename

*Default: null*

This option specify which will a filename to assign stdin input.

### ignore-disables

*Default: false*

This option specify if will ignore `styleline-disable` comments.

### cache

*Default: false*

This option specify if will store the info about processed 
files in order to only operate on the changed ones.

### cache-location

*Default: null*

This option specify which will path to the cache location.
This option is set to `null` by default.
This means that `.stylelintcache` is used and that case,
the file will be created in the directory where the `stylelint` command is executed.

### formatter

*Default: null*

This option specify which will output formatter.

### custom-formatter

*Default: null*

This option specify which will path to a js file 
exporting a custom formatting function.

### quiet

*Default: false*

This option specify if will quiet mode.

### color

*Default: true*

This option specify if will color.

### no-color

*Default: false*

This option specify will to don't color.

### allow-empty-input

*Default: false*

This option specify if no files match glob pattern,
exits without throwing an error.

### report-needless-disables

*Default: false*

This option specify if will report `stylelint-disable`
comments that are not blocking a lint warning.

### finder

*Default: {extensions: ['extensions' => ['css', 'sass', 'scss', 'less', 'sss']}*

[See documentation](../tasks.md#finder)
