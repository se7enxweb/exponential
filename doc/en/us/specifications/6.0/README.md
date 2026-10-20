# Exponential 6.0 specifications

The specifications are the reference pages of Exponential 6.0: data models, classes and interfaces, every
setting with its default and scope, extension points, events, APIs and commands. Use them when you need an exact
answer. For a first walk through the product, start with [the guides](../../guides/README.md); for terms, use
[the glossary](../../glossary.md).

Every page starts with what it is for and who should read it, gives its settings as tables (file, block, key,
default, scope) and ends with "Related pages".

## Kernel and content

| Page | What it covers |
|---|---|
| [Commands, cronjob parts and module views as classes](runnable-commands-cronjobs-views.md) | The runnable classes, entry points, events, re-implementation and listeners |
| [INI override directories and placements](ini-override-placements.md) | How `eZINI` orders settings files; per-siteaccess settings inside extensions |
| [Audit event model](audit-event-model.md) | Event names, records, channels, hash chains, `audit.ini`, extension points |
| [Extension metadata](extension-metadata.md) | `ezinfo.php`, `extension.xml`, `expInfo`, the release rule |
| [Installer logs and seed data](installer-logs-and-seed-data.md) | Seed files, the content tree of a new installation, installer logs |
| [Notifications](notifications.md) | Events, handlers, rules, digest, the service, lock and run record, views, templates |
| [Notification settings](notifications-ini.md) | `notification.ini` and the related `site.ini`, `cronjob.ini`, `menu.ini` settings with defaults |
| [Notification commands](notifications-cli.md) | `exp:notification:status`, `run`, `events`, `subscriptions`: options, exit codes, sample output |
| [E-mail preferences and consent](mail-preferences.md) | Categories, preferences, the consent log, suppression, the mail gate, links, tables, flows, settings |
| [E-mail preference commands](mail-preferences-cli.md) | `exp:mail:status`, `preferences`, `suppression`, `consent`, `gate`: actions, options, exit codes, sample output |
| [E-mail preferences: the law checklist](mail-preferences-compliance.md) | CAN-SPAM, GDPR and ePrivacy, CCPA/CPRA, CASL, RFC 8058 mapped to code and tests; the gaps |
| [Help system](help-system.md) | Help and manuals as content: DITA topic classes, sections, object states, roles, validator, web, context help and e-book delivery |
| [Help style guide](help-style-guide.md) | Editorial rules for help topics: topic types with templates, steps, notices, figures, `help_id`, fields, terminology |
| [Help standards](help-standards.md) | DITA 1.3, IEC/IEEE 82079-1, ISO/IEC/IEEE 26514, ISO 3864-2, EPUB Accessibility 1.1, WCAG 2.2 AA: what the help adopts, does differently, and how far |
| [The help classes as a package](help-content-package.md) | The package `exp_help_classes`: contents, what it leaves out, building, checking, version, handoff to the package server |

## Databases

| Page | What it covers |
|---|---|
| [SQLite3 database driver](sqlite3-database-driver.md) | `eZSQLite3DB`, PRAGMAs, transactions and the single writer |
| [Database drivers and installers](database-drivers-2026-09.md) | SQLite, PostgreSQL, MySQL and Oracle changes of September and October 2026 |
| [SQLite and Oracle driver behaviour](database-drivers-sqlite-oracle.md) | Moved; points to the two pages above |

## Security

| Page | What it covers |
|---|---|
| [Security defaults of September 2026](security-defaults-2026-09.md) | Security headers, session cookie, login, served files, caching |
| [Datatype and input hardening](datatype-input-hardening.md) | What each datatype and input helper refuses |
| [The August 2026 security patches](security-hardening-2026-08.md) | Six findings, jQuery 3.7.1, the account check |
| [The 6.0.13 hardening](security-hardening-6.0.13.md) | Seven findings and the PHP 8 stability fixes |
| [Continuous integration and the test suite](continuous-integration.md) | The PHPUnit suites and the GitHub Actions workflow |

## Velocity web server

| Page | What it covers |
|---|---|
| [Velocity engine settings and programs](velocity-engine-settings.md) | `Q.*` settings, programs, logging, branding, event loop |
| [Velocity worker pool and compatibility layer](velocity-worker-pool.md) | Workers, pool size, zygote, reset between requests |
| [Velocity HTTP/2, request handling and security](velocity-http2-and-security.md) | HTTP/2, framing, body limits, document root, headers |

## Extensions

| Page | What it covers |
|---|---|
| [bccie](bccie.md) | Collected information export: views, files, commands, settings |
| [cjw_newsletter](cjw_newsletter.md) | Newsletter dashboard, commands, cronjob parts, background runs and settings |
| [expservices](expservices.md) | Remote services over ezjscore |
| [explayouts_ui_api](explayouts-ui-api.md) | The layout editor's JSON API |
| [syndication](syndication.md) | Feed export and import |
| [xrowextract](xrowextract.md) | Content export, import, packages and schedules |

## Exponential Platform (Symfony based)

| Page | What it covers |
|---|---|
| [Platform package map](platform-package-map.md) | Which se7enxweb package replaces which upstream package |
| [Platform console command names](platform-console-commands.md) | `exponential:` command names and their aliases |
| [Platform SQLite installer](platform-sqlite-installer.md) | Install types and SQLite seed data on the platform |
| [Legacy bridge bundle](legacy-bridge-bundle.md) | Running Exponential 6 inside the platform |

## Related pages

- [Glossary](../../glossary.md), [Guides](../../guides/README.md), [History](../../history/README.md)
- [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md)
