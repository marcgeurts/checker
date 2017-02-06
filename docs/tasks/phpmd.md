---
currentMenu: tasks
---

# PhpMd

[See oficinal documentation](https://phpmd.org/)

The PhpMd task will sniff your code for bad coding standards.
It lives under the `phpmd` namespace and has following configurable parameters:

```yml
# checker.yml
parameters:
  tasks:
    phpmd:
      ruleset: ['cleancode', 'codesize', 'controversial', 'design', 'naming', 'unusedcode']
      minimum-priority: null
      exclude: []
      strict: false
```

### ruleset

*Default: ['cleancode', 'codesize', 'controversial', 'design', 'naming', 'unusedcode']*

With this parameter you will be able to configure the rule/rulesets you want to use.
You can use the standard sets provided by PhpMd or you can configure your own xml.

### minimum-priority

*Default: null*

This option specifies rules with lower priority than they will not be used.

### exclude

*Default: []*

This is a list of patterns that will be ignored by phpmd.
With this option you can skip directories like tests.
Leave this option blank to run phpmd for every php file.

### strict

*Default: false*

This option specifies to include in report those nodes with a @SuppressWarnings annotation.
