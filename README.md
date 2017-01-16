# Checker

[![StyleCI](https://styleci.io/repos/71817499/shield?style=flat)](https://styleci.io/repos/71817499)
[![Build Status](https://img.shields.io/travis/cknow/checker.svg?style=flat)](https://travis-ci.org/cknow/checker)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/638e3fd2-c8bd-4e58-aeb1-76b999abea07.svg?style=flat)](https://insight.sensiolabs.com/projects/638e3fd2-c8bd-4e58-aeb1-76b999abea07)
[![AppVeyor](https://img.shields.io/appveyor/ci/cknow/checker.svg?style=flat)](https://ci.appveyor.com/project/cknow/checker)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/cknow/checker.png?style=flat)](https://scrutinizer-ci.com/g/cknow/checker)
[![Code Climate](https://img.shields.io/codeclimate/github/cknow/checker.png?style=flat)](https://codeclimate.com/github/cknow/checker)
[![Coverage Status](https://img.shields.io/coveralls/cknow/checker.png?style=flat)](https://coveralls.io/github/cknow/checker)

[![Total Downloads](https://img.shields.io/packagist/dt/cknow/checker.svg?style=flat)](https://packagist.org/packages/cknow/checker/stats)
[![Latest Stable Version](https://img.shields.io/packagist/v/cknow/checker.svg?style=flat)](https://packagist.org/packages/cknow/checker)
[![License](https://img.shields.io/packagist/l/cknow/checker.svg?style=flat)](LICENSE)

> **Note:** This project is inspired in [GrumPHP](https://github.com./phpro/grumphp)!!!

Checker was developed for the purpose of configuring and executing your actions in any git hooks,
besides being able to create and execute commands as your wish.

## Installation

### Locally (Composer)

Use the following command to install on your project locally:

```bash
composer require-dev cknow/checker
```

When the package is installed, Checker will attach itself to the git hooks of your project.

### Globally (Composer)

Use the following command to install globally:

```bash
composer global require cknow/checker
composer global update cknow/checker
```

Then make sure you have `~/.composer/vendor/bin` in your `PATH` and you're good to go:

```bash
$ export PATH="$PATH:$HOME/.composer/vendor/bin"
```

That's all! The `checker` command will be available on your CLI and will be used by default.

> **Important:**
To make sure your git project hooks it is using the executable global,
run the command `checker git:install` in the project directory.

> **Note:**
When you globally installed 3rd party tools like e.g. phpunit,
those will also be used instead of the composer executables.

## Installation with an exotic project structure

When your application has a project structure that is not covered by the default configuration settings,
you will have to create a `checker.yml` before installing the package and add next config 
into your application's `composer.json`:

```json
# composer.json
{
    "extra": {
        "checker": {
            "config": "path/to/checker.yml"
        }
    }
}
```

You can also change the configuration after installation.
The only downfall is that you will have to initialize the git hooks manually:

```bash
# Locally
php ./vendor/bin/checker git:install --config=path/to/checker.yml

# Globally
checker git:install --config=path/to/checker.yml
```

## Configuration

Some things in Checker can be configured in a `checker.yml` or `checker.yml.dist`
file in the root of your project (the directory where you run the `checker` command).
You can specify a custom config filename and location in `composer.json`
or in the `--config` option of the console commands.

```yml
# cheker.yml
parameters:
    bin_dir: "./vendor/bin"
    git_dir: "."
    hooks_dir: ~
    hooks_preset: local
    process_timeout: 60
    process_async_wait: 1000
    process_async_limit: 10
    stop_on_failure: false
    ignore_unstaged_changes: false
    skip_success_output: false
    message:
        successfully: successfully.txt
        failed: failed.txt
    extensions: []
    tasks: ~
    hooks: ~
    commands: ~
```

## Parameters

### bin_dir

*Default: ./vendor/bin*

This parameter will tell you where to find external commands.
It defaults to the default composer bin directory.

### git_dir

*Default: .*

This parameter will tell in which folder it can find the .git folder.
This parameter is used to create the git hooks at the correct location.
It defaults to the working directory.

### hooks_dir

*Default: null*

This parameter will tell in which folder it can find the git hooks template folder.
It is used to find the git hooks at a custom location so that you can write your own git hooks.
It defaults to null, which means that the default folder `resources/hooks `is used.

### hooks_preset

*Default: local*

This parameter will tell which hooks preset to use.
Presets are only used when you did NOT specify a custom `hooks_dir`.
Comes with following presets:

- `local`: All checks will run on your local computer.
- `vagrant`: All checks will run in your vagrant box.

> **Note:**
When using the vagrant preset, you are required to set the vagrant SSH home folder to your working directory.
This can be done by altering the `.bashrc` or `.zshrc` inside your vagrant box:

```sh
echo 'cd /remote/path/to/your/project' >> ~/.bashrc
```

You can also add the `.bashrc` or `.zshrc` to your vagrant provision script.
This way the home directory will be set for all the people who are using your vagrant box.

### process_timeout

*Default: 60*

Uses the Symfony Process component to run external actions.
The component will trigger a timeout after 60 seconds by default.
If you've got tools that run more then 60 seconds, you can increase this parameter.
It is also possible to disable the timeout by setting the value to `null`.

### process_async_wait

*Default: 1000*

This parameter controls how long will wait (in microseconds)
before checking the status of all asynchronous processes.

### process_async_limit

*Default: 10*

This parameter controls how many asynchronous processes will run simultaneously. 
Please note that not all external tasks uses asynchronous processes,
nor would they necessarily benefit from using it.

### stop_on_failure

*Default: false*

This parameter will tell to stop running actions when one of the actions results in an error.
By default will continue running the configured actions.

### ignore_unstaged_changes

*Default: false*

By enabling this option, will stash your unstaged changes in git before running the actions.
This way the actions will run with the code that is actually committed without the unstaged changes.
Note that during the commit, the unstaged changes will be stored in git stash.
This may mess with your working copy and result in unexpected merge conflicts.

### skip_success_output

*Default: false*

This parameter will tell to skip success output.
By default will continue show success output.

### message

*Default: { successfully: successfully.txt, failed: failed.txt }*

This parameter will tell where can locate ascii images or display simple text.
If path is not specified default image from `resources/ascii/` folder are used.
Currently, only two images `successfully` and were `failed`.

For example:

```yml
# checker.yml
parameters:
    message:
        successfully: ~ # To disable
        failed: FAILED!!! # To display simple text
```

### extensions

*Default: []*

This parameter will tell which extensions to load.

You will probably have some custom actions or event listeners that are not included in the default project.
It is possible to group this additional configuration in an extension.
This way you can easily create your own extension package and load it whenever you need it.

The configuration looks like this:

```yml
# checker.yml
parameters:
    extensions:
        - My\Project\CheckerExtension
```

The configured extension class needs to implement `ClickNow\Checker\Extension\ExtensionInterface`.
Now you can register the actions or events from your own package in the service container.

For example:

```php
<?php

namespace My\Project;

use ClickNow\Checker\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CheckerExtension implements ExtensionInterface
{
    public function load(ContainerBuilder $container)
    {
        // Register your own stuff to the container!
    }
}
```

### tasks

*Default: null*

This parameter will tell which default config to tasks.
This configuration is merged in default task configuration.

For example:

```yml
# checker.yml
parameters:
    tasks:
        foo:
            bar: foobar # Default configuration
```

### hooks

*Default: null*

This parameter will tell which for tasks and commands are executed on the git hooks.
Below is the list available of git hooks:

- applypatch-msg
- pre-applypatch
- post-applypatch
- pre-commit
- prepare-commit-msg
- commit-msg
- post-commit
- pre-rebase
- post-checkout
- post-merge
- pre-push
- pre-receive
- update
- post-receive
- post-update
- push-to-checkout
- pre-auto-gc
- post-rewrite

You can also override these configurations:

- process_timeout
- process_async_wait
- process_async_limit
- stop_on_failure
- ignore_unstaged_changes
- skip_success_output

For example:

```yml
# checker.yml
parameters:
    hooks:
        pre-commit:
            process_timeout: 30
            process_async_wait: 500
            process_async_limit: 30
            stop_on_failure: true
            ignore_unstaged_changes: true
            skip_success_output: true
            tasks:
                foo: ~ # Use default configuration
        pre-push:
            process_timeout: ~
            process_async_wait: 2000
            process_async_limit: 60
            stop_on_failure: false
            ignore_unstaged_changes: false
            skip_success_output: false
            tasks:
                foo:
                    bar: value # Custom configuration
            commands:
                example: ~ # Execute command already created
```

### commands

*Default: null*

This parameter will tell which for custom commands. 
You can create as many commands as you want with custom names.

> **Note:** The command name can not be the same as a task!

You can also override these configurations:

- process_timeout
- process_async_wait
- process_async_limit
- stop_on_failure
- ignore_unstaged_changes
- skip_success_output

For example:

```yml
# checker.yml
parameters:
    commands:
        name_of_command1:
            process_timeout: 30
            process_async_wait: 500
            process_async_limit: 30
            stop_on_failure: true
            ignore_unstaged_changes: true
            skip_success_output: true
            tasks:
                foo: ~ # Use default configuration
        name_of_command2:
            process_timeout: ~
            process_async_wait: 2000
            process_async_limit: 60
            stop_on_failure: false
            ignore_unstaged_changes: false
            skip_success_output: false
            tasks:
                foo:
                    bar: value # Custom configuration
            commands:
                name_of_command1: ~ # Execute other command
```

## Metadata

Every action has a pre-defined metadata key on which application specific options can be configured.

For example:

```yml
# checker.yml
parameters:
    hooks:
        pre-commit:
            tasks:
                any_task:
                    metadata:
                        blocking: true # Blocking
                        priority: 2 # Second execution
            commands:
                any_command:
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

## Command-line Interface (CLI)

### run

*Parameter:*

| Name        | Required     | Description
| ----------- | ------------ | -----------
| name        | true         | The command name to be executed

For example:

```bash
# Locally
php ./vendor/bin/checker run name_of_command

# Globally
checker run name_of_command
```

You can also override these configurations:

- process_timeout
- process_async_wait
- process_async_limit
- stop_on_failure
- ignore_unstaged_changes
- skip_success_output

For example:

```bash
# Locally
php ./vendor/bin/checker run name_of_command --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true

# Globally
checker run name_of_command --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
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

> **Note:** If you have git hooks stored in backup, they will be restored.

For example:

```bash
# Locally
php ./vendor/bin/checker git:uninstall

# Globally
checker git:uninstall
```

### Git hooks

These commands will be triggered with git hooks.
However, you can run following commands:

```bash
# Locally
php ./vendor/bin/checker git:applypatch-msg
php ./vendor/bin/checker git:pre-applypatch
php ./vendor/bin/checker git:post-applypatch
php ./vendor/bin/checker git:pre-commit
php ./vendor/bin/checker git:prepare-commit-msg
php ./vendor/bin/checker git:commit-msg
php ./vendor/bin/checker git:post-commit
php ./vendor/bin/checker git:pre-rebase
php ./vendor/bin/checker git:post-checkout
php ./vendor/bin/checker git:post-merge
php ./vendor/bin/checker git:pre-push
php ./vendor/bin/checker git:pre-receive
php ./vendor/bin/checker git:update
php ./vendor/bin/checker git:post-receive
php ./vendor/bin/checker git:post-update
php ./vendor/bin/checker git:push-to-checkout
php ./vendor/bin/checker git:pre-auto-gc
php ./vendor/bin/checker git:post-rewrite

# Globally
checker git:applypatch-msg
checker git:pre-applypatch
checker git:post-applypatch
checker git:pre-commit
checker git:prepare-commit-msg
checker git:commit-msg
checker git:post-commit
checker git:pre-rebase
checker git:post-checkout
checker git:post-merge
checker git:pre-push
checker git:pre-receive
checker git:update
checker git:post-receive
checker git:post-update
checker git:push-to-checkout
checker git:pre-auto-gc
checker git:post-rewrite
```

You can also override these configurations:

- process_timeout
- process_async_wait
- process_async_limit
- stop_on_failure
- ignore_unstaged_changes
- skip_success_output

For example:

```bash
# Locally
php ./vendor/bin/checker git:applypatch-msg --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
php ./vendor/bin/checker git:pre-applypatch --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
php ./vendor/bin/checker git:post-applypatch --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
php ./vendor/bin/checker git:pre-commit --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
php ./vendor/bin/checker git:prepare-commit-msg --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
php ./vendor/bin/checker git:commit-msg --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
php ./vendor/bin/checker git:post-commit --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
php ./vendor/bin/checker git:pre-rebase --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
php ./vendor/bin/checker git:post-checkout --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
php ./vendor/bin/checker git:post-merge --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
php ./vendor/bin/checker git:pre-push --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
php ./vendor/bin/checker git:pre-receive --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
php ./vendor/bin/checker git:update --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
php ./vendor/bin/checker git:post-receive --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
php ./vendor/bin/checker git:post-update --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
php ./vendor/bin/checker git:push-to-checkout --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
php ./vendor/bin/checker git:pre-auto-gc --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
php ./vendor/bin/checker git:post-rewrite --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true

# Globally
checker git:applypatch-msg --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
checker git:pre-applypatch --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
checker git:post-applypatch --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
checker git:pre-commit --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
checker git:prepare-commit-msg --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
checker git:commit-msg --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
checker git:post-commit --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
checker git:pre-rebase --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
checker git:post-checkout --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
checker git:post-merge --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
checker git:pre-push --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
checker git:pre-receive --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
checker git:update --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
checker git:post-receive --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
checker git:post-update --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
checker git:push-to-checkout --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
checker git:pre-auto-gc --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
checker git:post-rewrite --process-timeout=30 --stop-on-failure=true --ignore-unstaged-changes=true --skip-success-output=true
```

## Creating a custom task

It is very easy to configure your own project specific task.
You just have to create a class that implements the `ClickNow\Checker\Task\TaskInterface`.
Next register it to the service manager and add your task configuration.

For example:

```yml
# checker.yml
parameters:
    tasks:
        myConfigKey:
            config1: config-value

services:
    task.myCustomTask:
        class: My\Custom\Task
        arguments:
          - '@config'
        tags:
          - { name: checker.task, config: myConfigKey }
```

> **Note:** 
You do NOT have to add the main and task configuration.
This example just shows you how to do it.

## Events

It is possible to hook in to Checker with events. Internally the Symfony event dispatcher is being used.

Following events are triggered during execution:

| Event name                  | Event class           | Triggered
| --------------------------- | --------------------- | ----------
| checker.action.run          | ActionEvent           | before a action is executed
| checker.action.successfully | ActionEvent           | when a action succeeds
| checker.action.failed       | ActionEvent           | when a action fails
| checker.runner.run          | RunnerEvent           | before the actions are executed
| checker.runner.successfully | RunnerEvent           | when all actions succeed
| checker.runner.failed       | RunnerEvent           | when one action failed
| console.command             | ConsoleCommandEvent   | before a CLI command is ran
| console.terminate           | ConsoleTerminateEvent | before a CLI command terminates
| console.exception           | ConsoleExceptionEvent | when a CLI command throws an unhandled exception.

Configured events just like you would in Symfony:

```yml
# checker.yml
services:   
    listener.some_listener:
        class: MyNamespace\EventListener\MyListener
        tags:
            - { name: checker.event_listener, event: checker.runner.run }
            - { name: checker.event_listener, event: checker.runner.run, method: customMethod, priority: 10 }
    listener.some_subscriber:
        class: MyNamespace\EventSubscriber\MySubscriber
        tags:
            - { name: checker.event_subscriber }
```

## Contributing

If you're having problems, spot a bug, or have a feature suggestion, please log and issue on Github.
If you'd like to have a crack yourself, fork the package and make a pull request.
Please include tests for any added or changed functionality. If it's a bug, include a regression test.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
