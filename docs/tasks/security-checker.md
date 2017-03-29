---
currentMenu: tasks
---

# Security Checker

The Security Checker task checks if your application uses
dependencies with known security vulnerabilities.

[See more information](https://security.sensiolabs.org/)

## Installation

Use the following command to install:

```
composer require --dev sensiolabs/security-checker
```

## Configuration

It lives under the `security-checker` namespace and has following configurable parameters:

```yaml
# checker.yml
parameters:
  tasks:
    security-checker:
      lockfile: './composer.lock'
      format: ~
      end-point: ~
      timeout: ~
```

## Parameters

### lockfile

*Default: './composer.lock'*

If your `composer.lock` file is located at an exotic location,
you can specify your custom location with this option.

### format

*Default: null*

This option specify which will output format.
The available options are `text`, `json` and `simple`.

### end-point

*Default: null*

This option specify which will security checker server.

### timeout

*Default: null*

This option specify which will timeout in seconds.
