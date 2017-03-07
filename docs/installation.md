---
currentMenu: installation
---

# Installation

## Via Composer

### Locally

Use the following command to install on your project locally:

```
composer require --dev cknow/checker
```

or add this to `require-dev` section in your `composer.json` file:

```
"cknow/checker": "^[version]"
```

then run ```composer update```

When the package is installed, Checker will attach itself to the git hooks of your project.

### Globally

Use the following command to install globally:

```
composer global require cknow/checker
composer global update cknow/checker
```

Then make sure you have `~/.composer/vendor/bin` in your `PATH` and you're good to go:

```
$ export PATH="$PATH:$HOME/.composer/vendor/bin"
```

That's all! The `checker` command will be available on your CLI and will be used by default.

> **Important:**
To make sure your git project hooks it is using the executable global,
run the command `checker git:install` in the project directory.

> **Note:**
When you globally installed 3rd party tools like e.g. phpunit,
those will also be used instead of the composer executables.

## Using Phar

Use the following command to download
[checker.phar](https://github.com/cknow/checker/releases/download/[version]/checker.phar):

```
wget https://github.com/cknow/checker/releases/download/[version]/checker.phar
```

or with curl:

```
curl -L https://github.com/cknow/checker/releases/download/[version]/checker.phar
```

> **Note:**
As Github is using a DDOS protection system, if using CURL fails,
just manually download the phar file from the Github [releases](https://github.com/cknow/checker/releases) page.

If you want to run `checker` instead of `php checker.phar`, move it to /usr/local/bin:

```
chmod +x checker.phar
mv checker.phar /usr/local/bin/checker
```

Use the following command to upgrade:

```
checker self-update
```

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

```
# Locally
php ./vendor/bin/checker git:install --config=path/to/checker.yml

# Globally
checker git:install --config=path/to/checker.yml
```

***
See also:

- [Configuration](configuration.md)
- [Parameters](parameters.md)
- [Tasks](tasks.md)
- [Command-Line](command-line.md)
- [Events](events.md)
- [Contributing](../CONTRIBUTING.md)
- [License](../LICENSE.md)

[version]: 1.1.0
