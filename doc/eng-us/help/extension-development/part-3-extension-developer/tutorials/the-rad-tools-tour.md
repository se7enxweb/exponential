---
title: "The RAD tools tour: Setup > RAD"
audience: developer, app-creator, designer
category: extension-development
order: 10
summary: A tour of Setup > RAD: the catalogue of extension points and its filters, the survey, the health check, and how every wizard works, from fields to preview to create or download.
keywords: RAD, rapid application development, setup/rad, catalogue, extension points, survey, radsurvey, health check, wizard, preview, download as zip, create in extension
type: help
---
# The RAD tools tour: Setup > RAD

Tutorial 1. **Setup > RAD** (`/setup/rad`, policy `setup/setup`) is the administration's toolbox for extension
developers. It is three things: a **catalogue** of every place the system can be extended, a **survey** of what this
installation really exposes, and a set of **wizards** that write working extensions. This tour shows each; the facts
come from `kernel/setup/expradcatalogue.php`, `expradsurvey.php`, `expradhealth.php` and the wizard classes beside them.

## 1. The catalogue

Open `/setup/rad`. The page's title is "Rapid Application Development Tools", and it says what it is: "Every point this
system can be extended at is listed below: what it is for, where the code goes, and what registers it."

Each entry shows:

| Field | Meaning |
|---|---|
| Code | where the code goes in your extension |
| Registered by | the setting or convention that plugs it in |
| Contract | the class to extend or the interface to implement |
| Kernel | the kernel file the mechanism lives in |
| tool available / no tool yet | whether a wizard writes it |

The entries are in seven groups: Content; Templates and design; Modules and views; Workflow, events and jobs; Storage
and infrastructure; Packaging and shop; Users, access and language. Three filters (from `expRADCatalogue::filters()`):

| Filter | Address |
|---|---|
| Everything | `/setup/rad` |
| With a tool | `/setup/rad/(show)/tools` |
| Documentation only | `/setup/rad/(show)/docs` |

The **Find** field narrows the list as you type (a name, a setting, a class, a file). The same catalogue, as text, is
[Extension points](../../../../bc/6.0/rad-extension-points.md).

## 2. The survey

The catalogue is written by hand; the **Extension point survey** (`/setup/radsurvey`) is read off disk on every
request. It counts, with a link to the entries behind every number:

| Group | What it lists |
|---|---|
| settings that name a class | every INI value that names a class, across all INI files |
| places the kernel looks | directories searched for handlers, datatypes, events... |
| interfaces and abstract classes | the contracts |
| module views, policy limitations | every view and limitation |
| template operators and functions | everything a template can call |
| events | everything that announces an event |
| template overrides | every override rule |
| kernel classes replaced | kernel overrides in effect |
| commands, cronjob parts and views as classes | the runnables a subclass can replace |
| registry entries | entries of registries declared in `rad.ini` |

Use it to answer "is there already a hook for this?" before you write one. A section can be linked:
`/setup/radsurvey/(show)/<section>`. The generated reference is [The extension surface](../../../../bc/6.0/rad-extension-surface.md).

## 3. The health check

The RAD page also runs `expRADHealth::findings()`: settings that name a class that cannot be loaded, a directory
searched that is not there, a design extension without a design, a view without a script, a datatype offered and not
found, and more, each with how to fix it. After installing or writing an extension, read it.

## 4. How every wizard works

All wizards share one flow (`kernel/setup/expextensionwizard.php`):

1. **The extension**: extension name, class name, title, summary, author, Composer vendor, version, license.
2. **What goes in it**: the parts to write (for a datatype: editing, class settings, default value, naming objects,
   searchable, sortable, text in and out, information collector, files, packages, version differences, on publish and
   more; each part is a group of methods that only make sense together).
3. **What will be written**: the methods or templates, each with a note on what the kernel calls it for.
4. **Switching it on**: the exact lines to add to `settings/override/site.ini.append.php`, and **Then** (regenerate the
   autoloads, clear the caches).
5. **Every file, before it is written**: the full content of every file, with line and byte counts. **Refresh preview**
   after a change.
6. **Create in extension/** writes the files, or **Download as zip** gives you the same files as an archive. When the
   web server cannot write into `extension/`, the page says so and offers only the archive.

Guards you can rely on ([RAD tools: security](../../../../bc/6.0/rad-security.md)): nothing is written outside
`extension/<name>`, an existing extension is never overwritten, typed values cannot break out of comments or INI lines,
generated templates escape output and generated views validate input.

## 5. The wizards

| Wizard | Address | Tutorial |
|---|---|---|
| Template extension | `/setup/templateoperator` | [A template operator in five minutes](rad-template-operator.md) |
| Datatype | `/setup/datatype` | [A datatype](rad-datatype.md) |
| Module wizard / Module over existing tables | `/setup/modulewizard`, `/setup/moduleextension` | [A module with views](rad-module.md) |
| Workflow event | `/setup/workflowevent` | [A workflow event](rad-workflow-event.md) |
| Design extension | `/setup/designextension` | [A design extension](rad-design-extension.md) |
| Settings extension | `/setup/settingsextension` | [A settings-only extension](rad-settings-extension.md) |
| Content extension | `/setup/contentextension` | a content class as an install script |
| Handler extension | `/setup/handlerextension/<kind>` | [Handlers and kernel overrides](rad-handlers-and-kernel-overrides.md) |
| Kernel override | `/setup/kerneloverride` | [Handlers and kernel overrides](rad-handlers-and-kernel-overrides.md) |

## 6. After any wizard

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' <name> global
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
php bin/php/checkclasses.php --quiet-ok
```

Then read the generated `README.md`: every wizard writes one describing what it wrote and how to continue, plus
`doc/examples.php` with ready-to-run examples for most kinds.

## Related pages

- Previous: [Tutorials: build real extensions step by step](README.md)
- Next: [With RAD: a template operator in five minutes](rad-template-operator.md)
- [RAD tools](../../../../features/6.0/rad-tools.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
