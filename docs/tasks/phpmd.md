---
currentMenu: tasks
---

# PhpMd

[See oficinal documentation](http://phpmd.org/)

The PhpMd task will sniff your code for bad coding standards.
It lives under the `phpmd` namespace and has following configurable parameters:

```yml
# checker.yml
parameters:
  tasks:
    phpmd:
      ruleset: ['cleancode', 'codesize', 'controversial', 'design', 'naming', 'unusedcode']
      minimum-priority: null
      strict: false
      finder:
        extensions: ['php']
```

### ruleset

*Default: ['cleancode', 'codesize', 'controversial', 'design', 'naming', 'unusedcode']*

This option specifies which rule/rulesets you want to use.
You can use the standard sets provided or you can configure your own xml.

### minimum-priority

*Default: null*

This option specifies rules with lower priority than they will not be used.

### strict

*Default: false*

This option specifies to include in report those nodes with a `@SuppressWarnings` annotation.

### finder

*Default: {extensions: ['php']}*

[See documentation](../tasks.md#finder)
