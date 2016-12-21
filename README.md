# Checker

[![StyleCI](https://styleci.io/repos/71817499/shield?style=flat)](https://styleci.io/repos/71817499)
[![Build Status](https://img.shields.io/travis/cknow/checker.svg?style=flat)](https://travis-ci.org/cknow/checker)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/638e3fd2-c8bd-4e58-aeb1-76b999abea07.svg?style=flat)](https://insight.sensiolabs.com/projects/638e3fd2-c8bd-4e58-aeb1-76b999abea07)
[![AppVeyor](https://img.shields.io/appveyor/ci/clicknow/checker.svg?style=flat)](https://ci.appveyor.com/project/clicknow/checker)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/cknow/checker.png?style=flat)](https://scrutinizer-ci.com/g/cknow/checker)
[![Code Climate](https://img.shields.io/codeclimate/github/cknow/checker.png?style=flat)](https://codeclimate.com/github/cknow/checker)
[![Coverage Status](https://img.shields.io/coveralls/cknow/checker.png?style=flat)](https://coveralls.io/github/cknow/checker)

[![Total Downloads](https://img.shields.io/packagist/dt/cknow/checker.svg?style=flat)](https://packagist.org/packages/cknow/checker)
[![Latest Stable Version](https://img.shields.io/packagist/v/cknow/checker.svg?style=flat)](https://packagist.org/packages/cknow/checker)
[![License](https://img.shields.io/packagist/l/cknow/checker.svg?style=flat)](https://packagist.org/packages/cknow/checker)

> **Note:** This project is inspired in [GrumPHP](https://github.com./phpro/grumphp)!!!

Checker was developed for the purpose of configuring and executing your tasks in any git hooks,
besides being able to create and execute commands as you wish. 
Because it is considered a task executor, it does not come with any task by default.

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

> **Important:** To make sure your git project hooks it is using the executable global,
run the command `checker git:install` in the project directory.

> **Note:** When you globally installed 3rd party tools like e.g. phpunit,
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
The only downfall is that you will have to initialize the git hook manually:

```bash
php ./vendor/bin/checker git:install --config=path/to/checker.yml
```

## Configuration

Some things in Checker can be configured in a `checker.yml` or `checker.yml.dist`
file in the root of your project (the directory where you run the `checker` command).

```yaml
# cheker.yml
parameters:
    bin_dir: "./vendor/bin"
    git_dir: "."
    hooks_dir: ~
    hooks_preset: local
    stop_on_failure: false
    ignore_unstaged_changes: false
    process_timeout: 60
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

### stop_on_failure

*Default: false*

This parameter will tell to stop running tasks when one of the tasks results in an error.
By default will continue running the configured tasks.

### ignore_unstaged_changes

*Default: false*

By enabling this option, will stash your unstaged changes in git before running the tasks.
This way the tasks will run with the code that is actually committed without the unstaged changes.
Note that during the commit, the unstaged changes will be stored in git stash.
This may mess with your working copy and result in unexpected merge conflicts.

### process_timeout

*Default: 60*

Uses the Symfony Process component to run external tasks.
The component will trigger a timeout after 60 seconds by default.
If you've got tools that run more then 60 seconds, you can increase this parameter.
It is also possible to disable the timeout by setting the value to `null`.

### skip_success_output

*Default: false*

This parameter will tell to skip success output.
By default will continue show success output.

### message

*Default: {successfully: successfully.txt, failed: failed.txt}*

### extensions

*Default: []*

### tasks

*Default: {}*

### hooks

*Default: {}*

### commands

*Default: {}*
