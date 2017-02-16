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
  strict: false
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

***
See also:

- [Installation](installation.md)
- [Parameters](parameters.md)
- [Tasks](tasks.md)
- [Command-Line](command-line.md)
- [Events](events.md)
- [Contributing](../CONTRIBUTING.md)
- [License](../LICENSE.md)
