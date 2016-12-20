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

I developed this project inspired in GrumPHP because I felt lack of power create and execute commands with their own tasks, not to mention that you can use the Checker and configure any git hoot, not just pre-commit and commit-msg.

## Installation

### Locally (Composer)

Use the following command to install `checker` in your project locally:

```bash
composer require --dev cknow/checker
```

### Globally (Composer)

Use the following command to install `checker` globally:

```bash
composer global require cknow/checker
```

Then make sure you have `~/.composer/vendor/bin` in your `PATH` and you're good to go:

```bash
$ export PATH="$PATH:$HOME/.composer/vendor/bin"
```

## Update

### Locally (Composer)

Use the following command to update `checker` in your project locally:

```bash
composer update cknow/checker
```

### Globally (Composer)

Use the following command to update `checker` globally:

```bash
composer global update cknow/checker
```

## Usage