---
currentMenu: configuration
---

# Configuration

Some things in Checker can be configured in a `checker.yml` or `checker.yml.dist`
file in the root of your project (the directory where you run the `checker` command).
You can specify a custom config filename and location in `composer.json`
or in the `--config` option of the console commands.

```yaml
# cheker.yml
parameters:
  bin-dir: "./vendor/bin"
  git-dir: "."
  hooks-dir: ~
  hooks-preset: local
  process-timeout: 60
  process-async-wait: 1000
  process-async-limit: 10
  stop-on-failure: false
  ignore-unstaged-changes: false
  skip-success-output: false
  message:
    successfully: successfully.txt
    failed: failed.txt
  extensions: []
  tasks: ~
  git-hooks:
    commit-msg: ~
    pre-commit: ~
    pre-push: ~
  commands: ~
```

## Metadata

Every action has a pre-defined metadata key on which application specific options can be configured.

For example:

```yaml
# checker.yml
parameters:
  git-hooks:
    pre-commit:
      tasks:
        any-task:
          metadata:
            blocking: true # Blocking
            priority: 2 # Second execution
      commands:
        any-command:
          metadata:
            blocking: false # Non-blocking
            priority: 1 # First execution
```

### priority

*Default: 0*

This option can be used to specify the order in which the actions will be executed.
The higher the priority, the sooner the action will be executed.

### blocking

*Default: true*

This option can be used to make a failing action non-blocking.
By default all actions will be marked as blocking.
When a action is non-blocking, the errors will be displayed but the tests will pass.

***
See also:

- [Installation](Installation.md)
- [Parameters](Parameters.md)
- [Tasks](Tasks.md)
- [Commands](Commands.md)
- [Events](Events.md)
- [Contributing](../CONTRIBUTING.md)
- [License](../LICENSE.md)
