---
currentMenu: tasks
---

# ESLint

The EsLint task has goal to provide a pluggable linting utility for JavaScript.

[See more information](http://eslint.org/)

## Installation

Use the following command to install:

```
npm install --save-dev eslint
```

[See more information](http://eslint.org/docs/user-guide/command-line-interface)

## Configuration

It lives under the `eslint` namespace and has following configurable parameters:

```yaml
# checker.yml
parameters:
  tasks:
    eslint:
      config: ~
      no-eslintrc: false
      env: []
      global: []
      parser: ~
      parser-options: []
      cache: false
      cache-location: ~
      rulesdir: []
      plugin: []
      rule: []
      ignore-path: ~
      no-ignore: false
      ignore-pattern: []
      stdin: false
      stdin-filename: ~
      quiet: false
      max-warnings: ~
      output-file: ~
      format: ~
      color: true
      no-color: false
      init: false
      fix: false
      debug: false
      no-inline-config: false
      print-config: false
      finder:
        extensions: ['js']
```

## Parameters

### config

*Default: null*

This option specify which will an additional configuration file.

### no-eslintrc

*Default: false*

This option specify if will disable use of configuration 
from `.eslintrc` and `package.json` files.

### env

*Default: []*

This option specify which will environments.

### global

*Default: []*

This option specify which will global variables so that they 
will not be flagged as undefined by the `no-undef` rule.

### parser

*Default: null*

This option specify which will a parser to be used.
This option is set to `null` by default.
This means that `espree` will be used.

### parser-options

*Default: []*

This option specify parser options to be used.

### cache

*Default: false*

This option specify if will store the info about processed 
files in order to only operate on the changed ones.

### cache-location

*Default: null*

This option specify which will path to the cache location.
This option is set to `null` by default.
This means that `.eslintcache` is used and that case,
the file will be created in the directory where the `eslint` command is executed.

### rulesdir

*Default: []*

This option specify which will another directory from which to load rules files.

### plugin

*Default: []*

This option specify which will plugins to load.

### rule

*Default: []*

This option specify which will rules to be used.

### ignore-path

*Default: null*

If your `.eslintignore` file is located at an exotic location,
you can specify your custom location with this option.
This option is set to `null` by default.
This means that `.eslintignore` is automatically loaded
if the file exists in the current directory.

### no-ignore

*Default: false*

This option specify if will disable excluding files
from `.eslintignore`, `--ignore-path` and `--ignore-pattern`.

### ignore-pattern

*Default: []*

This option specify which will patterns of files to ignore 
(in addition to those in `.eslintignore`). 

### stdin

*Default: false*

This option specify if will read and lint source code 
from STDIN instead of from files.

### stdin-filename

*Default: null*

This option specify which will filename to process STDIN.

### quiet

*Default: false*

This option specify if will quiet mode.

### max-warnings

*Default: null*

This option specify which will warning threshold.

### output-file

*Default: null*

This option specify which will report output file.

### format

*Default: null*

This option specify which will output format.

### color

*Default: true*

This option specify if will color.

### no-color

*Default: false*

This option specify will to don't color.

### init

*Default: false*

This option specify if will start config initialization wizard.

### fix

*Default: false*

This option specify if will to try to fix as many issues as possible.

### debug

*Default: false*

This option specify if will enable debug mode.

### no-inline-config

*Default: false*

This option specify if will prevents inline comments like 
`/*eslint-disable*/` or `/*global foo*/` from having any effect.

### print-config

*Default: false*

This option specify if will outputs the configuration to be used for the file passed.

### finder

*Default: {extensions: ['js']}*

[See documentation](../tasks.md#finder)
