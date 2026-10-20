---
title: Evaluating an old extension before you use it
audience: user, developer, designer, app-creator
category: extension-development
order: 170
summary: A checklist with commands to judge an extension you did not write: license, PHP 8 syntax, removed functions, access checks, escaping, SQL, inline scripts and database changes.
keywords: evaluate, audit, review, old extension, checklist, php -l, php 8, security, access control, wash, sql injection, inline script, csp, quality
type: help
---
# Evaluating an old extension before you use it

Part 1, chapter 17. An extension runs with the full rights of the kernel: it can read every table, every file the web
server can read, and every visitor's request. Before you install one you did not write, or decide to adopt one, spend
half an hour with this checklist. All commands run on a **development installation**, with the extension unpacked but
not yet switched on.

## 1. License and origin (5 minutes)

- [ ] A license file exists and allows what you plan to do ([Reading licenses before you reuse anything](reading-licenses-before-reusing.md)).
- [ ] `ezinfo.php` and `extension.xml` agree on name, version and license.
- [ ] You know where it came from: a repository with history, a Packagist package, a project page.

## 2. Does it parse on PHP 8? (2 minutes)

```bash
find extension/<name> -name '*.php' -print0 | xargs -0 -n1 php -l | grep -v '^No syntax errors'
```

No output means every file parses. Any line printed is a file PHP 8 refuses; typical causes are listed in
[PHP 8 errors that stop old code](../part-4-upgrading/php-8-errors-that-stop-old-code.md).

## 3. Removed and deprecated functions (5 minutes)

```bash
grep -rnE '\b(each|create_function|split|ereg|eregi|mysql_[a-z_]+|money_format|get_magic_quotes_gpc|utf8_decode|utf8_encode)\s*\(' extension/<name> --include=*.php
grep -rnE 'function\s+([A-Za-z_]+)\s*\(' extension/<name> --include=*.php | head -50
```

The first search finds functions PHP 8 removed or deprecates; each has a replacement in
[Removed PHP functions and their replacements](../part-4-upgrading/removed-php-functions.md). The second lists the
methods, so you can spot **old-style constructors**: a method with the same name as its class. PHP 8 no longer calls
such a method as a constructor, so the object is created without being set up. The template operator extension
`str_replace`, still in its original 2005 shape in the kernel tree, is exactly that case: its operator class sets its
operator list in `function MyStrReplaceOperator()`, which PHP 8 never calls, and the template engine reports
"Object problem with operator 'ezstr_replace'". The fix is one line, shown in
[Old-style constructors and static calls](../part-4-upgrading/old-constructors-and-static-calls.md).

## 4. Can every class load? (2 minutes, after switching it on in development)

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/checkclasses.php --quiet-ok
```

`checkclasses.php` loads every class of the installation in a child process and names the ones PHP refuses. Add
`--allow-root-user` when you run it as root.

## 5. Access control (5 minutes)

Open every `modules/<module>/module.php`:

```bash
grep -n "ViewList\|'functions'\|FunctionList" extension/<name>/modules/*/module.php
```

- [ ] Every view that shows or changes data names a `'functions'` entry.
- [ ] `$FunctionList` declares those functions, so they can be granted through roles.
- [ ] Views that change data only act on POST and check the form token (see
      [ezformtoken and CSRF protection](../part-8-reference/ezformtoken-and-csrf.md)).

A view without `'functions'` can be opened by anyone who can reach the site.

## 6. Escaping in templates (5 minutes)

```bash
grep -rnE '\{\$[a-zA-Z_.]+\}' extension/<name>/design | grep -v '|wash' | head -30
```

Variables printed without `|wash` are a cross-site scripting risk when their value can come from a visitor (a view
parameter, a form field, a search word). Not every hit is a bug (a number from the kernel is safe), but each one deserves
a look. See [Security in extensions](../part-3-extension-developer/security-in-extensions.md).

## 7. SQL (5 minutes)

```bash
grep -rnE '(query|arrayQuery)\s*\(' extension/<name> --include=*.php | head -30
```

For each query built from variables:

- [ ] strings go through `$db->escapeString()`,
- [ ] numbers are cast with `(int)`,
- [ ] or better, the code uses `eZPersistentObject` instead of hand-written SQL.

## 8. Inline scripts and styles (2 minutes)

```bash
grep -rnE '<script[^>]*>[^<]|on(click|change|submit|load)=|javascript:|style="' extension/<name>/design | head -30
```

The administration enforces a Content Security Policy: inline scripts, `on...=` handler attributes and `javascript:`
links do not run there. Every hit is work for [Part 4](../part-4-upgrading/README.md) before the extension's admin
pages work.

## 9. What it changes in the system (5 minutes)

- [ ] `ls extension/<name>/settings/` and read each file: which settings does it change? Does it override a kernel
      class (`ezpkerneloverride`-style autoloads), replace a module, or add event listeners?
- [ ] `share/`, `sql/`, `update/`: does it create tables? Do they have upgrade scripts?
- [ ] `cronjobs/`: does it run anything on a schedule?
- [ ] Does it send mail, call external services, write files outside `var/`?

## 10. Decide

| Findings | Decision |
|---|---|
| License fine, parses, access checks present, escaping fine | Use it; keep notes of what you checked |
| License fine, small PHP 8 or CSP problems | Bring it up to date with Part 4, then offer the fixes to its author |
| No access checks, unescaped output, SQL from request values | Do not use it on a live site until fixed |
| No license | Do not copy it; take only the idea |

Write your findings down. If you adopt or fix the extension, they become the first issue in its tracker.

## Related pages

- Previous: [Linking versus copying: citing sources fairly](linking-versus-copying.md)
- Next: [Learning from an extension, step by step](learning-from-an-extension-step-by-step.md)
- [The upgrade checklist](../part-4-upgrading/the-upgrade-checklist.md)
- [Security in extensions](../part-3-extension-developer/security-in-extensions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
