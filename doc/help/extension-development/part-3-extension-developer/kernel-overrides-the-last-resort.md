---
title: "Kernel class overrides: the last resort"
audience: developer, app-creator
category: extension-development
order: 300
summary: Replacing a kernel class through the override autoload map: how it works, what it costs, the wizard and its drift check, and the lighter mechanisms to try first.
keywords: kernel override, override autoload, EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE, ezp_override.php, ezpgenerateautoloads -o, kernel_override, checkdrift, last resort, replace class
type: help
---
# Kernel class overrides: the last resort

Part 3, chapter 30. When nothing else reaches the behavior you must change, an extension can replace a **kernel class**
with its own copy, under the same name. It works, and it is the most expensive thing you can do to an installation.
This chapter explains how it works and why you should avoid it.

## How it works

1. `config.php` allows overrides: `define( 'EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE', true );` (`config.php-RECOMMENDED`
   ships it commented out, `false`).
2. Your extension holds a file that declares the kernel class, for example
   `extension/mysite_override/kernel_override/ezcontentobjecttreenode.php` declaring `class eZContentObjectTreeNode`.
3. `php bin/php/ezpgenerateautoloads.php -o` writes `var/autoload/ezp_override.php`, a map of the classes your
   extensions declare that the kernel also declares.
4. The autoloader (`autoload.php`) merges that map over the kernel's when the constant is true, so your file is loaded
   instead of the kernel's.

## What it costs

The kernel override wizard's README says it plainly: "An override is not a subclass. There is no parent to call and
nothing is inherited: this file **is** the class as far as the rest of the system is concerned." So:

- every fix the kernel makes to that class after you copied it is a fix your site does not get, until someone notices,
- two extensions overriding the same class do not combine; whichever the generator finds last wins, silently,
- an upgrade can change what other classes expect from it, and your copy breaks without a sign until it is used.

## The wizard

**Setup > RAD > Kernel override** (`/setup/kerneloverride`, `kernel/setup/expkerneloverridewizard.php`) writes the
override as a complete extension: the copy in `kernel_override/`, a README, and `bin/checkdrift.php`, which compares
the kernel's current file with the one that was copied. Run it after every upgrade:

```bash
php extension/<name>/bin/checkdrift.php
```

Exit status `0` means the kernel file is unchanged, `1` that it changed and someone must decide which changes the
override needs, `2` that the file is gone. The wizard refuses to write a second override of a class that is already
overridden.

## Before you do it

The wizard's README ends with the right advice: "Almost everything in this system has a lighter way in - a handler
named by a setting, an event listener, a filter, a template override." In order of preference:

| Instead of overriding | Try |
|---|---|
| a template | a template override ([Template overrides: the basics](../part-2-builder/template-overrides-basics.md)) |
| a mail, login, session, search ... class | a handler ([Handlers named by settings](handlers-named-by-settings.md)) |
| behavior around publishing, caches, output | an event listener or filter ([Events, listeners and filters](events-and-filters.md)) |
| a kernel module's view | a view re-implementation (`site.ini [RunnableSettings] Implementation[]`) or an extension module that replaces the module ([An extension can replace a kernel module](../../../features/6.0/extension-module-override.md)) |
| a datatype's behavior | your own datatype |
| a bug in the kernel | a pull request to the kernel ([Contributing back with pull requests](../part-5-extend-fork-contribute/contributing-back-with-pull-requests.md)) |

A fix sent upstream helps everyone and costs you nothing at the next upgrade. An override is the answer only for the
time it takes the fix to be released, and should then be removed.

## Seeing overrides

The RAD survey (`/setup/radsurvey`) counts "kernel classes replaced" on an installation, and eZpedia's
["Overriding kernel classes within an extension"](https://ezpedia.exponential.earth/en/solution/overriding_kernel_classes_within_an_extension)
describes the mechanism from the earlier years.

## Related pages

- Previous: [Handlers named by settings](handlers-named-by-settings.md)
- Next: [Security in extensions](security-in-extensions.md)
- [With RAD: handlers and kernel overrides](tutorials/rad-handlers-and-kernel-overrides.md)
- [RAD tools](../../../features/6.0/rad-tools.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
