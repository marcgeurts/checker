# Command-line Interface (CLI)

## run

*Parameter:*

| Name        | Required     | Description
| ----------- | ------------ | -----------
| name        | true         | The command name to be executed

For example:

```bash
# Locally
php ./vendor/bin/checker run name-of-command

# Globally
checker run name-of-command
```

You can also override these configurations:

- process-timeout
- process-async-wait
- process-async-limit
- stop-on-failure
- ignore-unstaged-changes
- skip-success-output

For example:

```bash
# Locally
php ./vendor/bin/checker run name-of-command --process-timeout=30 --process-async-wait=1000 --process-async-limit=30 --stop-on-failure=1 --ignore-unstaged-changes=1 --skip-success-output=1

# Globally
checker run name-of-command --process-timeout=0 --process-async-wait=2000 --process-async-limit=60 --stop-on-failure=0 --ignore-unstaged-changes=0 --skip-success-output=0
```

## git:install

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

## git:uninstall

This command uninstall git hooks to Checker.

> **Note:** If you have git hooks stored in backup, they will be restored.

For example:

```bash
# Locally
php ./vendor/bin/checker git:uninstall

# Globally
checker git:uninstall
```

## git:commit-msg

This command will be triggered by git hooks in commit-msg. However, you can run!

For example:

```bash
# Locally
php ./vendor/bin/checker git:commit-msg

# Globally
checker git:commit-msg
```

You can also override these configurations:

- commit-message-file `If not set, Checker find for you!`
- git-user-name `If not set, Checker find for you!`
- git-user-email `If not set, Checker find for you!`
- process-timeout
- process-async-wait
- process-async-limit
- stop-on-failure
- ignore-unstaged-changes
- skip-success-output

For example:

```bash
# Locally
php ./vendor/bin/checker git:commit-msg commit-message-file --git-user-name=name --git-user-email=email --process-timeout=30 --process-async-wait=1000 --process-async-limit=30 --stop-on-failure=1 --ignore-unstaged-changes=1 --skip-success-output=1

# Globally
checker git:commit-msg commit-message-file --git-user-name=name --git-user-email=email --process-timeout=0 --process-async-wait=2000 --process-async-limit=60 --stop-on-failure=0 --ignore-unstaged-changes=0 --skip-success-output=0
```

## git:pre-commit

This command will be triggered by git hooks in pre-commit. However, you can run!

For example:

```bash
# Locally
php ./vendor/bin/checker git:pre-commit

# Globally
checker git:pre-commit
```

You can also override these configurations:

- process-timeout
- process-async-wait
- process-async-limit
- stop-on-failure
- ignore-unstaged-changes
- skip-success-output

For example:

```bash
# Locally
php ./vendor/bin/checker git:pre-commit --process-timeout=30 --process-async-wait=1000 --process-async-limit=30 --stop-on-failure=1 --ignore-unstaged-changes=1 --skip-success-output=1

# Globally
checker git:pre-commit --process-timeout=0 --process-async-wait=2000 --process-async-limit=60 --stop-on-failure=0 --ignore-unstaged-changes=0 --skip-success-output=0
```

## git:pre-push

This command will be triggered by git hooks in pre-push. However, you can run!

For example:

```bash
# Locally
php ./vendor/bin/checker git:pre-push

# Globally
checker git:pre-push
```

You can also override these configurations:

- process-timeout
- process-async-wait
- process-async-limit
- stop-on-failure
- ignore-unstaged-changes
- skip-success-output

For example:

```bash
# Locally
php ./vendor/bin/checker git:pre-push --process-timeout=30 --process-async-wait=1000 --process-async-limit=30 --stop-on-failure=1 --ignore-unstaged-changes=1 --skip-success-output=1

# Globally
checker git:pre-push --process-timeout=0 --process-async-wait=2000 --process-async-limit=60 --stop-on-failure=0 --ignore-unstaged-changes=0 --skip-success-output=0
```

***
See also:

- [Installation](Installation.md)
- [Configuration](Configuration.md)
- [Parameters](Parameters.md)
- [Tasks](Tasks.md)
- [Events](Events.md)
- [Contributing](../CONTRIBUTING.md)
- [License](../LICENSE.md)
