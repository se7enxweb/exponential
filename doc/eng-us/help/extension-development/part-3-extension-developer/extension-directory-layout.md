---
title: The extension directory layout
audience: developer, app-creator
category: extension-development
order: 10
summary: Every folder and file name an extension can have, what the kernel does with it, which setting points the kernel there, and how many of the installed extensions use it - the map for the rest of Part 3.
keywords: extension layout, directory structure, ezinfo.php, extension.xml, settings, classes, autoloads, modules, design, datatypes, eventtypes, cronjobs, bin, translations, share, sql, update, tests, doc
type: help
---
# The extension directory layout

Part 3, chapter 1. An extension is a folder under `extension/` whose subfolders follow fixed names. The kernel finds
each kind of code by a name plus a setting that points at your extension. This chapter is the map; each row links the
chapter that explains it.

## The full tree

```text
extension/mysite_hello/
    ezinfo.php                     metadata (class mysite_helloInfo)
    extension.xml                  metadata and dependencies (read first)
    composer.json                  for Composer and Packagist
    README.md, LICENSE, doc/       documentation and license
    settings/                      *.ini.append.php (and your own *.ini)
        siteaccess/<sa>/           per-siteaccess settings the extension owns
    classes/                       PHP classes (autoloaded)
    autoloads/                     eztemplateautoload.php: template operators
    modules/<module>/              module.php, view scripts, function_definition.php
    design/<design>/
        templates/                 templates
        override/templates/        override templates
        stylesheets/, javascript/, images/
    datatypes/<datatype>/          <datatype>type.php
    eventtypes/event/<event>/      <event>type.php
    cronjobs/                      cronjob scripts
    bin/php/                       command line scripts (ext:<extension>:<name>)
    translations/<locale>/         translation.ts
    share/                         db_schema.dba, db_data.dba, filelist.md5
    sql/<database>/                schema.sql per database engine
    update/                        upgrade scripts and SQL
    packages/                      packages the extension ships
    tests/                         PHPUnit tests
```

Nothing is required except a reason to exist: an extension with only `settings/` is complete. `ezinfo.php` and
`extension.xml` are strongly recommended for every extension, because the administration and the upgrade checks read
them.

## What the kernel does with each

| Path | Found because | Chapter |
|---|---|---|
| `ezinfo.php`, `extension.xml` | read by `expInfo` for every extension on disk | [ezinfo.php](ezinfo-php.md), [extension.xml](extension-xml.md) |
| `settings/` | added to the settings override directories of an active extension | [Settings in an extension](settings-in-an-extension.md) |
| `classes/` | scanned by `php bin/php/ezpgenerateautoloads.php -e` into `var/autoload/ezp_extension.php` | [Autoloads and class naming](autoloads-and-class-naming.md) |
| `autoloads/eztemplateautoload.php` | `site.ini [TemplateSettings] ExtensionAutoloadPath[]=<ext>` | [Template operators](template-operators.md) |
| `modules/<module>/module.php` | `module.ini [ModuleSettings] ExtensionRepositories[]=<ext>` | [Modules and views](modules-and-views.md) |
| `design/<design>/` | `design.ini [ExtensionSettings] DesignExtensions[]=<ext>` | [A design inside an extension](design-in-an-extension.md) |
| `datatypes/<type>/<type>type.php` | `content.ini [DataTypeSettings] ExtensionDirectories[]=<ext>` and `AvailableDataTypes[]=<type>` | [Datatypes](datatypes.md) |
| `eventtypes/event/<type>/<type>type.php` | `workflow.ini [EventSettings] ExtensionDirectories[]=<ext>` and `AvailableEventTypes[]=event_<type>` | [Workflow event types](workflow-event-types.md) |
| `cronjobs/<script>.php` | `cronjob.ini [CronjobSettings] ExtensionDirectories[]=<ext>` and a part's `Scripts[]` | [Cronjob parts](cronjob-parts.md) |
| `bin/php/<name>.php` | the console finds it as `ext:<extension>:<name>` | [CLI scripts and console commands](cli-scripts-and-console-commands.md) |
| `translations/<locale>/translation.ts` | `site.ini [RegionalSettings] TranslationExtensions[]=<ext>` | [Translations in an extension](translations-in-an-extension.md) |
| `share/db_schema.dba`, `sql/` | the package installer, or your upgrade script | [Database schema, .dba files and upgrades](database-schema-and-dba-files.md) |
| `tests/` | your `phpunit.xml.dist` | [Tests for extensions](tests-for-extensions.md) |

The rule behind the table: **the folder name is a convention, and a setting in your extension's own
`settings/` registers it**. A `datatypes/` folder in an extension that does not add itself to
`ExtensionDirectories[]` is never looked at.

The paths for datatypes and event types are exactly what the kernel builds: `eZDataType::loadAndRegisterType()`
includes `<directory>/<type>/<type>type.php`, and `eZWorkflowType::loadAndRegisterType()` splits an event type string
such as `event_mysite_notify` into the group `event` and the type `mysite_notify` and includes
`<extension>/eventtypes/event/mysite_notify/mysite_notifytype.php`.

## How common is each folder?

On the reference installation (87 extensions on disk) the folders were used this often: `settings/` 80,
`classes/` 68, `design/` 62, `doc/` 60, `translations/` 50, `modules/` 45, `autoloads/` 39, `tests/` 35, `bin/` 28,
`sql/` 19, `datatypes/` 17, `share/` 17, `cronjobs/` 15, `update/` 8, `eventtypes/` 6, `packages/` 4. Count yours:

```bash
for d in settings classes design modules autoloads datatypes eventtypes cronjobs bin translations tests; do
    printf "%-12s %s\n" "$d" "$(ls -d extension/*/$d 2>/dev/null | wc -l)"
done
```

## Naming the extension

- Lower case letters, digits and underscores: `mysite_hello`, not `MySite-Hello`.
- Unique, and yours: a prefix of your organization or project (`mysite_`, `acme_`) avoids collisions with every other
  extension. Extensions from 7x use `sevenx` (7x's official public PHP code prefix; "7x" cannot be one, since a name
  cannot start with a digit) or `exp` (used with intent for work assigned to the Exponential project and the
  Exponential Foundation): see [Naming prefixes: sevenx and exp](../../../standards/naming-prefixes-sevenx-and-exp.md).
  Do not start new names with `ez`, which belongs to the kernel's historical classes.
- The folder name is used everywhere (settings, Composer's `extra`, class names such as `mysite_helloInfo`), so choose
  it once.

## Generated by the RAD tools

Every wizard at **Setup > RAD** writes this structure for the parts you choose and lists every file before writing it.
Comparing a wizard's output with this map is a good way to learn it; see
[The RAD tools tour](tutorials/the-rad-tools-tour.md).

## Related pages

- Previous: [Part 3: The extension developer](README.md)
- Next: [ezinfo.php: who made it and which version](ezinfo-php.md)
- [Extension points](../../../bc/6.0/rad-extension-points.md)
- [What an extension is](../part-1-from-user-to-developer/what-an-extension-is.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
