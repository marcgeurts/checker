---
currentMenu: events
---

# Events

It is possible to hook in to Checker with events. Internally the Symfony event dispatcher is being used.

The following events are triggered during execution:

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

Configure events just like you would in Symfony:

```yaml
# checker.yml
services:   
  listener.some-listener:
    class: MyNamespace\EventListener\MyListener
    tags:
      - { name: checker.event-listener, event: checker.runner.run }
      - { name: checker.event-listener, event: checker.runner.run, method: customMethod, priority: 10 }
  listener.some-subscriber:
    class: MyNamespace\EventSubscriber\MySubscriber
    tags:
      - { name: checker.event-subscriber }
```

***
See also:

- [Installation](installation.md)
- [Configuration](configuration.md)
- [Parameters](parameters.md)
- [Tasks](tasks.md)
- [Command-Line](command-line.md)
- [Contributing](../CONTRIBUTING.md)
- [License](../LICENSE.md)
