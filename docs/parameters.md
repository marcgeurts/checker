---
currentMenu: parameters
---

# Parameters

### bin-dir

*Default: ./vendor/bin*

This parameter will tell you where to find external commands.
It defaults to the default composer bin directory.

### git-dir

*Default: .*

This parameter will tell in which folder it can find the .git folder.
This parameter is used to create the git hooks at the correct location.
It defaults to the working directory.

### hooks-dir

*Default: null*

This parameter will tell in which folder it can find the git hooks template folder.
It is used to find the git hooks at a custom location so that you can write your own git hooks.
It defaults to null, which means that the default folder `resources/hooks `is used.

### hooks-preset

*Default: local*

This parameter will tell which hooks preset to use.
Presets are only used when you did NOT specify a custom `hooks-dir`.
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

### process-timeout

*Default: 60*

Uses the Symfony Process component to run external actions.
The component will trigger a timeout after 60 seconds by default.
If you've got tools that run more then 60 seconds, you can increase this parameter.
It is also possible to disable the timeout by setting the value to `null`.

### process-async-wait

*Default: 1000*

This parameter controls how long will wait (in microseconds)
before checking the status of all asynchronous processes.

### process-async-limit

*Default: 10*

This parameter controls how many asynchronous processes will run simultaneously. 
Please note that not all external tasks uses asynchronous processes,
nor would they necessarily benefit from using it.

### stop-on-failure

*Default: false*

This parameter will tell to stop running actions when one of the actions results in an error.
By default will continue running the configured actions.

### ignore-unstaged-changes

*Default: false*

By enabling this option, will stash your unstaged changes in git before running the actions.
This way the actions will run with the code that is actually committed without the unstaged changes.
Note that during the commit, the unstaged changes will be stored in git stash.
This may mess with your working copy and result in unexpected merge conflicts.

### strict

*Default: false*

This parameter will tell to use strict mode.
This means that warnings will be considered as errors.
By default will strict mode is disabled.

### skip-success-output

*Default: false*

This parameter will tell to skip success output.
By default will continue show success output.

### message

*Default: { successfully: successfully.txt, failed: failed.txt }*

This parameter will tell where can locate ascii images or display simple text.
If path is not specified default image from `resources/ascii/` folder are used.
Currently, only two images `successfully` and were `failed`.

For example:

```yaml
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

```yaml
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

```yaml
# checker.yml
parameters:
  tasks:
    foo:
      bar: foobar # Default configuration
```

## git-hooks

This parameter will tell which for tasks and commands are executed on the git hooks.
Below is the list available of git hooks:

- commit-msg
- pre-commit
- pre-push

You can also override these configurations:

- process-timeout
- process-async-wait
- process-async-limit
- stop-on-failure
- ignore-unstaged-changes
- strict
- skip-success-output

For example:

```yaml
# checker.yml
parameters:
  git-hooks:
    pre-commit:
      process-timeout: 30
      process-async-wait: 1000
      process-async-limit: 30
      stop-on-failure: true
      ignore-unstaged-changes: true
      strict: true
      skip-success-output: true
      tasks:
        foo: ~ # Use default configuration
    pre-push:
      process-timeout: 60
      process-async-wait: 2000
      process-async-limit: 60
      stop-on-failure: false
      ignore-unstaged-changes: false
      strict: false
      skip-success-output: false
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

- process-timeout
- process-async-wait
- process-async-limit
- stop-on-failure
- ignore-unstaged-changes
- strict
- skip-success-output

For example:

```yaml
# checker.yml
parameters:
  commands:
    name-of-command1:
      process-timeout: 30
      process-async-wait: 1000
      process-async-limit: 30
      stop-on-failure: true
      ignore-unstaged-changes: true
      strict: true
      skip-success-output: true
      tasks:
        foo: ~ # Use default configuration
    name-of-command2:
      process-timeout: 60
      process-async-wait: 2000
      process-async-limit: 60
      stop-on-failure: false
      ignore-unstaged-changes: false
      strict: false
      skip-success-output: false
      tasks:
        foo:
          bar: value # Custom configuration
      commands:
        name-of-command1: ~ # Execute other command
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

- [Installation](installation.md)
- [Configuration](configuration.md)
- [Tasks](tasks.md)
- [Command-Line](command-line.md)
- [Events](events.md)
- [Contributing](../CONTRIBUTING.md)
- [License](../LICENSE.md)
