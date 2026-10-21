---
title: Class naming and the exp prefix
audience: php-developer, developer
category: php-development
order: 150
summary: How to name PHP classes, files, functions, settings and tables in Exponential - your own prefix, exp for code contributed to the project, sevenx for 7x, why kernel eZ identifiers are never renamed, file names, namespaces of the runnable classes, and what the old product name may and may not appear in.
keywords: naming, class names, prefix, exp prefix, sevenx prefix, eZ prefix, ezp, file names, namespaces, Exponential\Command, Exponential\View, Exponential\Cronjob, layering, rename, identifiers
type: help
---
# Class naming and the exp prefix

This page is for PHP developers who add classes, functions, settings or tables. Names in Exponential follow a few
rules that keep thousands of classes from colliding in one class map, and that keep existing extensions working. The
canonical statement is [Naming prefixes: sevenx and exp](../../standards/naming-prefixes-sevenx-and-exp.md).

## Contents

1. [The rules in one table](#the-rules-in-one-table)
2. [Your prefix](#your-prefix)
3. [exp and sevenx](#exp-and-sevenx)
4. [Kernel eZ identifiers stay](#kernel-ez-identifiers-stay)
5. [Files and class names](#files-and-class-names)
6. [Namespaced classes](#namespaced-classes)
7. [Other identifiers](#other-identifiers)

## The rules in one table

| You are writing | Prefix | Example |
|---|---|---|
| your own extension or project code | your organization's or extension's prefix | `acmeReportService`, `acmereport` |
| code you contribute to the Exponential project | `exp` | `expHelpAudience`, `expContentSecurityPolicy` |
| code 7x publishes as the company | `sevenx` (or `exp`, as 7x decides) | `sevenx_dse`, `sevenx_valkey_cache` |
| a change to an existing kernel class | keep its name | `eZContentObject`, `eZINI`, `eZDB` |

## Your prefix

All classes share one class map, so two classes with the same name in two extensions break each other: the generator
warns about a duplicate, and one of them wins. Start every class, function, template operator, fetch function,
setting block and table of your extension with a prefix that is yours: `acmeReportService`, `acme_report` (table),
`acmereport_total` (operator), `[AcmeReportSettings]` (block).

## exp and sevenx

- `exp` is the project's prefix. New code in the kernel and in project extensions uses it: `explayouts`, `expai`,
  `expqueue`, `expHelpAudience`, the `exp:` console commands. Use `exp` only for code you contribute to the project.
- `sevenx` is 7x's official public PHP code prefix; "7x" cannot be one, because a PHP identifier cannot start with a
  digit (`class 7xFoo` is a parse error).
- Existing `sevenx_*` and `exp*` names are not renamed to make the prefixes look uniform; the folder name of an
  extension is its identity in settings.

## Kernel eZ identifiers stay

`eZContentObject`, `eZINI`, `eZDB`, `eZPersistentObject`, `ezpKernelWeb`, the `ez*` file names, module names, template
operators, settings and database tables are **real identifiers**. They are never renamed: renaming would break every
extension and template written for them. `eZ` is the prefix of those identifiers, not the name of the product.

Two consequences for your code:

- Do not create new classes with an `eZ` or `ezp` prefix; they look like kernel classes and may collide with future
  ones.
- In comments, messages and documentation the product is **Exponential**. The old product name appears only as past
  history; a class name such as `eZContentObject` is not the product name and stays as it is.

## Files and class names

| Item | Rule | Example |
|---|---|---|
| class file | one class per file, lower case file name of the class name | `classes/acmereportservice.php` holds `acmeReportService` |
| datatype | `<datatypestring>type.php` in `datatypes/<datatypestring>/` | `datatypes/acmecolor/acmecolortype.php` |
| workflow event | `<typestring>type.php` in `eventtypes/event/<typestring>/` | `eventtypes/event/acmenotify/acmenotifytype.php` |
| module | directory name = module name | `modules/acmereport/module.php` |
| tests | `<Class>Test.php` | `tests/acmeReportServiceTest.php` |

The class map generator finds classes by scanning files, so a class in a file of another name is found too; the rule
is for people, who look for `acmereportservice.php` when they read `acmeReportService`.

## Namespaced classes

The kernel uses namespaces only for the runnable classes (commands, cronjob parts, module views) and their services:

| Kind | Namespace |
|---|---|
| kernel command | `Exponential\Command\Kernel\<Name>` |
| extension command | `Exponential\Command\Extension\<Ext>\<Name>` |
| extension cronjob part | `Exponential\Cronjob\Extension\<Ext>\<Name>` |
| extension module view | `Exponential\View\Extension\<Ext>\<Module>\<View>` |
| kernel service | `Exponential\Service\...` |

Everything else is global, prefixed class names ([Module views as classes](module-views-as-classes.md)).

## Other identifiers

- **INI files and blocks**: your own file (`acmereport.ini`) with your own blocks; changes to kernel files go into
  `*.ini.append.php` of your extension.
- **Tables**: `acme_*`, lower case, unquoted names that work on every supported database
  ([Code for every supported database](code-for-every-supported-database.md)).
- **Template operators and fetch functions**: prefixed (`acmereport_money`, `fetch( 'acmereport', 'items' )`).
- **Translation contexts**: `extension/acmereport`.
- **CSS classes and JavaScript globals**: your prefix as well.

## Related pages

- [Layering and naming](../../standards/architecture/layering-and-naming.md)
- [Autoloads and class maps](autoloads-and-class-maps.md)
- [Naming and branding during an upgrade](../extension-development/part-4-upgrading/naming-and-branding-during-an-upgrade.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
