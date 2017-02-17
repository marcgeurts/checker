---
currentMenu: command-line
---

# Command-Line

### run

This command execute the command with name specified.

> **Note:** All files registered in repository are verified.

*Parameter:*

| Name        | Required     | Description
| ----------- | ------------ | -----------
| name        | true         | The command name to be executed

*Options:*

| Name                              | Required  | Description
| --------------------------------- | --------- | ---------------------------
| process-timeout                   | true      | Specify process timeout
| process-async-wait                | true      | Specify process async wait
| process-async-limit               | true      | Specify process async limit
| stop-on-failure                   | ---       | Stop on failure
| no-stop-on-failure                | ---       | Non stop on failure
| ignore-unstaged-changes           | ---       | Ignore unstaged changes
| no-ignore-unstaged-changes        | ---       | No ignore unstaged changes
| strict                            | ---       | Enable strict mode
| no-strict                         | ---       | Disable strict mode
| progress                          | true      | Specify process style
| no-progress                       | ---       | Disable process style
| skip-success-output               | ---       | Skip success output
| no-skip-success-output            | ---       | No skip success output

For example:

```bash
# Locally
php ./vendor/bin/checker run name-of-command [--options]

# Globally
checker run name-of-command [--options]
```

### git:install

This command install git hooks to Checker.

> **Note:** If you have custom git hooks they will be stored backup.

For example:

```bash
# Locally
php ./vendor/bin/checker git:install

# Globally
checker git:install
```

> **Note:** This command is triggered by the composer plugin during installation.

*Options:*

| Name          | Shortcut      | Default value | Description
| ------------- | ------------- | ------------- | -------------
| config        | c             | null          | Custom path to `checker.yml`

For example:

```bash
# Locally
php ./vendor/bin/checker git:install --config=/path/to/checker.yml
php ./vendor/bin/checker git:install -c=/path/to/checker.yml

# Globally
checker git:install --config=/path/to/checker.yml
checker git:install -c=/path/to/checker.yml
```

### git:uninstall

This command uninstall git hooks to Checker.

For example:

```bash
# Locally
php ./vendor/bin/checker git:uninstall

# Globally
checker git:uninstall
```

> **Note:** If you have git hooks stored in backup, they will be restored.

### git:commit-msg

This command will be triggered by git hooks in commit-msg. However, you can run!

> **Note:** Only files changed in repository are verified.

*Argument:*

| Name                              | Required  | Description
| --------------------------------- | --------- | ---------------------------
| commit-message-file               | false     | If not set, Checker find for you!

*Options:*

| Name                              | Required  | Description
| --------------------------------- | --------- | ---------------------------
| git-user-name                     | false     | If not set, Checker find for you!
| git-user-email                    | false     | If not set, Checker find for you!
| process-timeout                   | true      | Specify process timeout
| process-async-wait                | true      | Specify process async wait
| process-async-limit               | true      | Specify process async limit
| stop-on-failure                   | ---       | Stop on failure
| no-stop-on-failure                | ---       | Non stop on failure
| ignore-unstaged-changes           | ---       | Ignore unstaged changes
| no-ignore-unstaged-changes        | ---       | No ignore unstaged changes
| strict                            | ---       | Enable strict mode
| no-strict                         | ---       | Disable strict mode
| progress                          | true      | Specify process style
| no-progress                       | ---       | Disable process style
| skip-success-output               | ---       | Skip success output
| no-skip-success-output            | ---       | No skip success output

For example:

```bash
# Locally
php ./vendor/bin/checker git:commit-msg [commit-message-file] [--options]

# Globally
checker git:commit-msg [commit-message-file] [--options]
```

### git:pre-commit

This command will be triggered by git hooks in pre-commit. However, you can run!

> **Note:** Only files changed in repository are verified.

*Options:*

| Name                              | Required  | Description
| --------------------------------- | --------- | ---------------------------
| process-timeout                   | true      | Specify process timeout
| process-async-wait                | true      | Specify process async wait
| process-async-limit               | true      | Specify process async limit
| stop-on-failure                   | ---       | Stop on failure
| no-stop-on-failure                | ---       | Non stop on failure
| ignore-unstaged-changes           | ---       | Ignore unstaged changes
| no-ignore-unstaged-changes        | ---       | No ignore unstaged changes
| strict                            | ---       | Enable strict mode
| no-strict                         | ---       | Disable strict mode
| progress                          | true      | Specify process style
| no-progress                       | ---       | Disable process style
| skip-success-output               | ---       | Skip success output
| no-skip-success-output            | ---       | No skip success output

For example:

```bash
# Locally
php ./vendor/bin/checker git:pre-commit [--options]

# Globally
checker git:pre-commit [--options]
```

### git:pre-push

This command will be triggered by git hooks in pre-push. However, you can run!

> **Note:** Only files committed in repository are verified.

*Options:*

| Name                              | Required  | Description
| --------------------------------- | --------- | ---------------------------
| process-timeout                   | true      | Specify process timeout
| process-async-wait                | true      | Specify process async wait
| process-async-limit               | true      | Specify process async limit
| stop-on-failure                   | ---       | Stop on failure
| no-stop-on-failure                | ---       | Non stop on failure
| ignore-unstaged-changes           | ---       | Ignore unstaged changes
| no-ignore-unstaged-changes        | ---       | No ignore unstaged changes
| strict                            | ---       | Enable strict mode
| no-strict                         | ---       | Disable strict mode
| progress                          | true      | Specify process style
| no-progress                       | ---       | Disable process style
| skip-success-output               | ---       | Skip success output
| no-skip-success-output            | ---       | No skip success output

For example:

```bash
# Locally
php ./vendor/bin/checker git:pre-push [--options]

# Globally
checker git:pre-push [--options]
```

***
See also:

- [Installation](installation.md)
- [Configuration](configuration.md)
- [Parameters](parameters.md)
- [Tasks](tasks.md)
- [Events](events.md)
- [Contributing](../CONTRIBUTING.md)
- [License](../LICENSE.md)
