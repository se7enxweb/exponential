# Guides: the learning path

This page is the table of contents of the guides. Read it if you are new to Exponential, or if you know the system
and want the shortest way to one result.

A guide is a tutorial. You follow it from top to bottom and need no prior knowledge. At the end you have a working
result: an installed site, a published page, a changed template, a site behind a real web server, a cache cleared on
purpose. Every step is a command or a click path, with the output to expect. Where a topic needs more depth, the
guide links to the feature page, the specification or the upgrade note.

## In short

- Start with [Getting started](getting-started.md). Ten to fifteen minutes gives you an installed site, a login and a first template change.
- Then pick your road below: **editors**, **site builders**, **developers** or **operators**.
- Stuck on a word? [The glossary](../glossary.md) explains every term in one line and links to the page that teaches it.
- Want to know why something is the way it is? [The history](../history/README.md) tells the story month by month.

## Check your setup in two minutes

These commands only read. Run them from the root of the installation (the directory with `console`, `index.php` and
`settings/`). Add `--allow-root-user` to a command that refuses to run as root.

```bash
./console --version
```

Expected: the first line reads `console (Exponential) 6.0.15stable`. Below it are the version, the build, the
number of extensions, the web server and the PHP version, and at the end the line
`Written by 7x (se7enx.com) and the Exponential contributors.`

```bash
./console list exp
```

Expected: the list of commands of the `exp:` family, one line each (`exp:cache`, `exp:install`, `exp:ini`,
`exp:maintenance`, `exp:velocity` and many more). `./console help <command>` explains any one of them.

```bash
./console exp:cache clear --all --dry-run --allow-root-user
```

Expected: the list of caches that a real clear would empty, ending in a line that starts with
`DRY RUN PASS` and names the number of caches. Nothing is cleared.

```bash
./console exp:maintenance status --allow-root-user
```

Expected: the line `Maintenance is off.` on a normal site.

If the first command fails, you are not in the installation root, or PHP is missing.
[Getting started](getting-started.md#1-what-you-need) says what is needed.

## The guides

Read them in this order the first time. Each one also stands on its own.

| Guide | You finish with | Time |
|---|---|---|
| [Getting started](getting-started.md) | An installed site, the administrator login, a first page, a first template change | 10 to 15 minutes |
| [The content model and editing content](content-model-and-editing.md) | A content class, an object, the sub-items table, the trash, a content job | 30 minutes |
| [Templates and design](templates-and-design.md) | The design you use, the template that wrote a piece of a page, an override you can undo, the admin4 design | 15 minutes |
| [Extensions](extensions.md) | An extension found, installed, switched on and configured; one of your own built and released | 45 minutes |
| [Deploying](deploying.md) | The site served by Apache with PHP-FPM, by Velocity or by FrankenPHP, with HTTPS | 30 minutes |
| [Operating a site](operating-a-site.md) | The right caches cleared, cronjobs running, static cache and preload on, a backup, a checklist | 30 minutes |
| [Security and audit](security-and-audit.md) | The hardening checked, roles that give only what is needed, the audit trail read, debug output for your address only | 30 minutes |
| [Remote services and apps](remote-services-and-apps.md) | Services called from the shell and from Python, a personal API token, a portal front end | 20 minutes |
| [Upgrading](upgrading.md) | An installation of 4.x, 5.x or an earlier 6.0.x moved to the current 6.0 line, and checked | 20 minutes for a small site |

## Road 1: editors

You write and manage content in the administration interface.

1. [Getting started](getting-started.md): sections 4 to 6 (see the site, first login, first content).
2. [The content model and editing content](content-model-and-editing.md): classes, objects, the online editor, the sub-items table, multi-node edit, the trash, content jobs.
3. Depth, when you need it:
   - [The sub-items table: options and columns](../features/6.0/subitems-table-options.md) and [list paging](../features/6.0/admin-list-paging.md)
   - [Editing several items at once](../bc/6.0/multi-node-edit.md)
   - [Online editor: TinyMCE 8](../features/6.0/online-editor-tinymce8.md)
   - [The trash: who deleted it and where it was](../features/6.0/trash-who-and-where.md)
   - [Large operations as content jobs](../features/6.0/content-jobs.md)
   - [PDF export](../features/6.0/pdf-export.md), [Translations and languages](../features/6.0/translations-and-languages.md)
   - [Store dashboard](../features/6.0/store-dashboard.md) and [order receipts](../features/6.0/order-receipts.md) if you run a shop

## Road 2: site builders

You shape how the site looks and which extensions it uses.

1. [Getting started](getting-started.md): section 7 (first template change, by click path and by file).
2. [The content model and editing content](content-model-and-editing.md): the model your templates render.
3. [Templates and design](templates-and-design.md): find the template, override it, check it, switch the admin design.
   Depth:
   - [Template path comments](../features/6.0/template-path-comments.md): see which file wrote which markup
   - [Template editor](../features/6.0/template-editor-overrides.md) and [override order](../features/6.0/template-override-ordering.md)
   - [The Exp Debug bar](../features/6.0/exp-debug-bar.md)
   - [Exponential Layouts](../bc/6.0/LAYOUTS.md): blocks, zones and rules built in the browser
4. [Extensions](extensions.md): find, install, switch on and configure an extension. Depth:
   - [What a new installation comes with](../features/6.0/default-extension-distribution.md), [the extension list](../features/6.0/extension-list-and-downloads.md), [loading order](../features/6.0/extension-loading-order.md)
   - [Changing settings from the command line](../features/6.0/exp-ini-command.md), [multi-site INI overrides](../features/6.0/multi-site-ini-overrides.md)
   - [Request rules](../features/6.0/request-rules.md), [robots.txt](../features/6.0/robots-txt.md)
   - [Roles and policies in order](../features/6.0/role-policy-order.md), [hidden admin tabs](../features/6.0/hidden-admin-tabs.md)
   - One page per extension: [extension index](../features/6.0/extensions/README.md)

## Road 3: developers

You write extensions, templates with logic, commands and integrations.

1. [Getting started](getting-started.md), then the settings tool: [exp:ini](../features/6.0/exp-ini-command.md).
2. [Extensions](extensions.md), section 6 and 7: build an extension by hand or with the RAD wizards, and release it.
   Extension points (RAD): [RAD tools](../features/6.0/rad-tools.md), [the extension points](../bc/6.0/rad-extension-points.md), [the extension surface](../bc/6.0/rad-extension-surface.md), [RAD security](../bc/6.0/rad-security.md).
3. Commands, cronjobs and module views as classes: [Commands, cronjob parts and module views](../bc/6.0/cli_cronjob_view_abstractions.md) and the [specification](../specifications/6.0/runnable-commands-cronjobs-views.md); [Console](../bc/6.0/console.md).
4. Metadata and services: [Remote services and apps](remote-services-and-apps.md) (the guide), [Extension metadata](../specifications/6.0/extension-metadata.md), [Backend services over ezjscore](../bc/6.0/backend_ezjscore_services.md).
5. Tests and compatibility: [Continuous integration](../specifications/6.0/continuous-integration.md), [PHP 8 support](../bc/6.0/php8.md).
6. Bridges to the Symfony platform: [Legacy bridge](../features/6.0/legacy-bridge.md), [the platform repositories](../history/ecosystem.md).

## Road 4: operators

You run the installation: servers, caches, backups, upgrades, security.

1. [Getting started](getting-started.md), then [Deploying](deploying.md) and [Operating a site](operating-a-site.md).
2. Install on purpose: [Install in one command](../features/6.0/install-in-one-command.md), [Kickstarter](../features/6.0/kickstarter-cli.md) for repeatable installs, [SQLite](../features/6.0/sqlite-database.md), [MongoDB](../features/6.0/mongodb-database-support.md).
3. The web server: [Velocity web server](../features/6.0/velocity-web-server.md), [its control panel](../features/6.0/velocity-control-panel.md), [HTTPS certificates](../features/6.0/velocity-https-certificates.md), [engines compared](../bc/6.0/velocity-engines.md), [FrankenPHP](../bc/6.0/frankenphp.md).
4. Caches and speed: [cache control](../bc/6.0/cache-console.md), [HTTP caching](../bc/6.0/http-caching.md), [SQL query cache](../bc/6.0/sql-query-cache.md), [static cache](../features/6.0/static-cache-generator.md), [preload](../features/6.0/preload-sites-view.md).
5. Keep it healthy: [maintenance mode](../features/6.0/maintenance-mode.md), [repair from the browser](../features/6.0/repair-from-the-browser.md), [file consistency check](../features/6.0/file-consistency-check.md), [cronjobs](../features/6.0/cronjobs-console.md), [audit trail](../features/6.0/audit-trail.md).
6. Security: [Security and audit](security-and-audit.md) (the guide), [Security defaults of September 2026](../specifications/6.0/security-defaults-2026-09.md), [the August 2026 patches](../specifications/6.0/security-hardening-2026-08.md), [the 6.0.13 hardening](../specifications/6.0/security-hardening-6.0.13.md).
7. Upgrading: follow [Upgrading](upgrading.md); read the [6.0.15 changelog](../changelogs/6.0/6.0.15.md), then the behaviour changes of [July and August](../bc/6.0/behaviour-changes-2026-07-08.md), [16 to 30 September](../bc/6.0/behaviour-changes-2026-09b.md) and [October](../bc/6.0/behaviour-changes-2026-10.md), in that order.

## Related pages

- A word you do not know: [Glossary](../glossary.md).
- What changed and why: [History](../history/README.md); proof that nothing is missing: [Coverage](../history/coverage.md).
- Release notes: [changelogs of 6.0](../changelogs/6.0/6.0.15.md) and [of the extensions](../changelogs/extensions/README.md).
- The installation text in one place: [INSTALL](../INSTALL.md).
