---
currentMenu: installation
---

# Installation

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

***
See also:

- [Configuration](Configuration.md)
- [Parameters](Parameters.md)
- [Tasks](Tasks.md)
- [Commands](Commands.md)
- [Events](Events.md)
- [Contributing](../CONTRIBUTING.md)
- [License](../LICENSE.md)
