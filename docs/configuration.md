---
currentMenu: configuration
---

# Configuration

Some things in Checker can be configured in a `checker.yml`, `checker.dist.yml` or `checker.yml.dist`
file in the root of your project (the directory where you run the `checker` command).
You can specify a custom config filename and location in `composer.json`
or in the `--config` option of the console commands.

```yaml
# cheker.yml
parameters:
  bin-dir: './vendor/bin'
  git-dir: '.'
  hooks-dir: ~
  hooks-preset: 'local'
  process-timeout: 60
  process-async-wait: 1000
  process-async-limit: 10
  stop-on-failure: false
  ignore-unstaged-changes: false
  strict: false
  progress: 'list'
  skip-empty-output: false
  skip-success-output: false
  message:
    successfully: 'successfully.txt'
    failed: 'failed.txt'
  extensions: []
  tasks: ~
  git-hooks:
    commit-msg: ~
    pre-commit: ~
    pre-push: ~
  commands: ~
```

## Disable on git hooks

Use the `--no-verify` (`-n`) flag in your command git which bypasses git hooks.

For example:

```
git commit --no-verify -m "commmit"
```
or
```
git push --no-verify
```

> **Note:** This is surely **NOT** recommended!

## Windows limitation

The command prompt has a limit on command line input strings of `8191`.

This one is causing external commands to fail with exit code 1 without any error.

[See for more information](https://support.microsoft.com/en-us/kb/830473)

***
See also:

- [Installation](installation.md)
- [Parameters](parameters.md)
- [Tasks](tasks.md)
- [Command-Line](command-line.md)
- [Events](events.md)
- [Contributing](../CONTRIBUTING.md)
- [License](../LICENSE.md)
