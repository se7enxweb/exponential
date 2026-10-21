---
title: Building an integration as an extension
audience: integrator, developer
category: integration
order: 410
summary: Packaging an integration as an Exponential extension - the directory layout of a typical integration, settings with secrets kept in settings/override, classes, commands, cronjob parts, workflow events, schema, tests, composer.json, ezinfo.php and extension.xml, activation, and releasing it.
keywords: extension, integration extension, directory layout, settings, classes, bin/php, cronjobs, eventtypes, login_handler, schema, dba, sql, tests, composer.json, ezinfo.php, extension.xml, ActiveExtensions, autoloads, release
type: help
---
# Building an integration as an extension

This page is for integration developers. Everything an integration needs fits into one **extension**: settings,
classes, commands, cronjob parts, workflow events, login handlers, REST controllers, server functions, tables and
tests. An extension is versioned in its own Git repository, installed with Composer, activated with one line, and can
be released and contributed back. The complete guide to extensions is
[Extension development: the book](../extension-development/README.md).

## Contents

1. [The layout of an integration extension](#the-layout-of-an-integration-extension)
2. [Settings and secrets](#settings-and-secrets)
3. [Tables of your own](#tables-of-your-own)
4. [Metadata and Composer](#metadata-and-composer)
5. [Activating and deploying](#activating-and-deploying)
6. [One extension or several](#one-extension-or-several)

## The layout of an integration extension

```text
extension/myintegration/
    composer.json                       package name, requirements, license
    ezinfo.php                          name, version, license, website (the about page)
    extension.xml                       the same metadata in XML
    README.md, CHANGELOG.md, LICENSE
    settings/
        myintegration.ini               your settings with safe defaults, no secrets
        site.ini.append.php             [Event] Listeners[], [UserSettings] ExtensionDirectory[] ...
        cronjob.ini.append.php          [CronjobSettings] ExtensionDirectories[], [CronjobPart-...]
        workflow.ini.append.php         [EventSettings] ExtensionDirectories[], AvailableEventTypes[]
        ezjscore.ini.append.php         [ezjscServer_...] for server functions or an expservices domain
        rest.ini.append.php             [ApiKeySettings] RouteScopes[] for REST routes of your own
    classes/                            the work: client of the other system, mapping, importer, sender
        runnable/cronjobs/              cronjob parts as classes
    bin/php/                            commands (ext:myintegration:<name>), including the monitoring check
    cronjobs/                           one-line files that call the cronjob classes
    eventtypes/event/<type>/            workflow event types
    login_handler/                      login handlers, if any
    share/db_schema.dba, sql/<engine>/  tables of your own
    tests/                              unit tests without a database, test fixtures
    doc/                                the integration's documentation and runbook
```

Only the parts the integration uses are needed. Names follow the kernel's rules: classes `myIntegration...` in files
of the same name in lower case ([Autoloads and class naming](../extension-development/part-3-extension-developer/autoloads-and-class-naming.md)).
New names use your own prefix, never `ez`.

## Settings and secrets

| Kind | Where |
|---|---|
| Defaults that are safe to publish (endpoint of the test system, batch sizes, parent node identifiers) | `extension/myintegration/settings/myintegration.ini` |
| Values per installation (production endpoint, node ids) | `settings/override/myintegration.ini.append.php` or a siteaccess's settings |
| Secrets (API keys, passwords, signing secrets) | `settings/override/myintegration.ini.append.php`, outside Git; or environment variables |

Read them with `eZINI::instance( 'myintegration.ini' )->variable( 'Block', 'Name' )`. Document every setting in the
shipped INI file with a comment line above it ([Settings in an extension](../extension-development/part-3-extension-developer/settings-in-an-extension.md)).

## Tables of your own

Outbox, inbox, watermarks and mappings live in tables of your extension, defined once in `share/db_schema.dba` (and
`sql/<engine>/schema.sql` where needed), with a small install command that creates them idempotently
([Database schema and dba files](../extension-development/part-3-extension-developer/database-schema-and-dba-files.md)).
Access them through `eZPersistentObject` classes or `eZDB` with cast ids and escaped strings.

## Metadata and Composer

- `ezinfo.php` and `extension.xml` state the name, the version (the same in both, raised with every release), the
  license and the website; the about page reads them ([ezinfo.php](../extension-development/part-3-extension-developer/ezinfo-php.md),
  [extension.xml](../extension-development/part-3-extension-developer/extension-xml.md)).
- `composer.json` names the package, its type, the license and its requirements on the kernel and other extensions
  ([composer.json for extensions](../extension-development/part-3-extension-developer/composer-json-for-extensions.md)).
- A customer-specific integration lives in a private repository; a reusable connector can be published
  ([Contributing integrations back](contributing-integrations-back.md)).

## Activating and deploying

```ini
# settings/override/site.ini.append.php
[ExtensionSettings]
ActiveExtensions[]=myintegration
```

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/console exp:velocity deploy --allow-root-user     # or: clear the INI cache, reload PHP-FPM, restart Velocity
```

Activate integration extensions globally (`ActiveExtensions`) when cronjobs and commands need them on every
siteaccess; an access extension (`ActiveAccessExtensions`) is seen only by the siteaccesses that list it, and every
cron line must then name such a siteaccess with `-s`
([Activating an extension](../extension-development/part-3-extension-developer/activating-an-extension.md)).

## One extension or several

| One extension | Several |
|---|---|
| One customer, one other system, one team | A reusable connector (for example "a CRM client") used by several customer extensions |
| Simple release and handover | The generic part is published; the customer part stays private |

Splitting the generic connector from the customer mapping is what makes contributing back possible.

## Related pages

- [Extension development: the book](../extension-development/README.md)
- [Extension directory layout](../extension-development/part-3-extension-developer/extension-directory-layout.md)
- [Packaging as an extension for app creators](../creators/app-creators/packaging-as-an-extension.md)
- [Develop with Git](../develop-with-git.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
