# Exponential 6.0 features

This page lists every feature page of the Exponential 6.0 line, so you can find the one you need without searching.
Each page says who it is for, what the feature does and how to use it. The legacy extensions, themes and packages have
an index of their own: [Extensions, themes and packages](extensions/README.md). Every change, with its date, is in the
[6.0.15 changelog](../../../changelogs/6.0/6.0.15.md).

## Start here: Velocity is part of Exponential 6.0.15

As of 6.0.15 the Velocity Internet / Web Application Server is part of Exponential: the root `composer.json` requires
`se7enxweb/exponential-velocity ~0.0.4.54`, so every installation has it, on PHP 8.0 and later. It marks a turning
point, a cornerstone and a capstone for the project: a new level of application system integration, with even greater
features out of the box in the core product, by default. Exponential is one working stack (server, rapid application
development platform, CMS, digital experience and e-commerce), not just a framework. Read
**[Velocity is part of Exponential 6.0.15](../../help/velocity-is-part-of-exponential-6-0-15.md)** first.

Velocity is the recommended engine for every stage, FrankenPHP is the production-ready alternative, and Apache or
nginx with PHP-FPM keep working as before.

## Exponential Velocity, the Internet and web application server

- [Velocity web server](velocity-web-server.md): the overview
- [Velocity: running Exponential in a persistent-worker web server](velocity-persistent-worker-server.md)
- [Velocity response cache](velocity-response-cache.md)
- [Velocity HTTPS and certificates](velocity-https-certificates.md)
- [Velocity static files and image processing](velocity-static-files-and-images.md)
- [Velocity WebSockets, Server-Sent Events and rooms](velocity-websockets-and-events.md)
- [Velocity control panel and dashboard](velocity-control-panel.md)
- [Velocity Q shell](velocity-q-shell.md)
- [Velocity scheduler](velocity-scheduler.md)
- [Velocity discovery, federation and deploy](velocity-discovery-federation-and-deploy.md)
- [Velocity packages, Docker images and binaries](velocity-packages-and-binaries.md)
- [Velocity and the opcode cache: why a page costs what it does, and how to measure it](velocity-opcode-cache-and-profile.md)
- [uwebserver](velocity-uwebserver.md)
- [Server control commands: exp:webserver, exp:frankenphp and exp:solr](web-server-and-solr-commands.md)

## Installing, deploying and operating

- [Installing Exponential in one command](install-in-one-command.md)
- [Kickstarter: install a whole site from one file](kickstarter-cli.md)
- [What a new installation comes with: the default extension distribution](default-extension-distribution.md)
- [A clean installation that fits shared hosting and says Exponential](clean-install-defaults.md)
- [The setup wizard's new look, and the editor siteaccess](setup-wizard-and-editor-siteaccess.md)
- [Settings per environment (EXP_ENV)](environment-settings.md)
- [Configuration managed by the deployment (ManagedByDeployment)](managed-by-deployment.md)
- [Installation name: never confuse staging with production again](installation-name-in-pages.md)
- [Change settings from the command line: exp:ini](exp-ini-command.md)
- [The system card: exp:expfetch, exp:info and exp:about](system-card.md)
- [Benchmark: `exp:benchmark`](benchmark.md)
- [Maintenance mode](maintenance-mode.md)
- [Repair a broken installation from the browser](repair-from-the-browser.md)
- [File consistency check and the release file list](file-consistency-check.md)
- [Quality checks: git hooks, file manifest, code style and PHPStan](quality-checks.md)
- [Cache clears that move directories aside](cache-clear-rename-aside.md)
- [Keep the content and INI caches in Redis or Valkey](valkey-cache-hooks.md)
- [Static cache generator](static-cache-generator.md)
- [Cronjobs console](cronjobs-console.md), [Pausing cronjob parts](cronjob-pause.md),
  [Leaving out a cronjob part](cronjob-part-filter.md)
- [Log rotation and pruning](log-rotation.md), [The log viewer](log-viewer.md),
  [Request logs: 404.log and security.log](request-logs.md)
- [Per-site settings inside extensions (multi-site hosting)](multi-site-ini-overrides.md)
- [Logs, INI cache and expiry timestamps per site (multi-site hosting)](multi-site-log-and-cache-paths.md)

## Databases

- [SQLite database support](sqlite-database.md)
- [MongoDB as the database](mongodb-database-support.md)
- [AdminNeo: the database manager behind the Database Source Editor](adminneo-database-manager.md)
- [Data tables: module tables as part of the system](data-tables.md): the catalogue of persistent classes, named
  database connections, classes on their own connection

## Rapid application development and extensions

- [RAD tools: extension wizards, catalogue, survey and health check](rad-tools.md)
- [Extensions in more than one folder](additional-extension-directories.md)
- [Setup > Extensions: loading order and safe saving](extension-loading-order.md)
- [Extension list: sort, inspect and download any extension](extension-list-and-downloads.md)
- [An extension can replace a kernel module](extension-module-override.md)
- [Access and view cache filters for extensions](access-and-cache-filters.md)
- [Content policy limitations of extensions](content-limitation-handlers.md)
- [Icon themes that live in your extension](icon-themes-in-extensions.md)
- [Remote services: the whole admin as an HTTP API (expservices)](remote-services-expservices.md)
- [Request rules: decide what happens to a request before its view runs](request-rules.md)
- [Role and policy checks in templates](role-and-policy-template-operators.md)
- [PHP string functions as template operators](string-template-operators.md)
- [Template editor: create, order and edit overrides without touching INI files](template-editor-overrides.md)
- [Design > Templates: drag and drop override order](template-override-ordering.md)
- [Template path comments: see which template wrote which markup](template-path-comments.md)
- [Debug output you can read at a glance and style](debug-output-improvements.md)
- [The Exp Debug bar](exp-debug-bar.md)

## Packages

- [ezpm: the package manager on the command line](ezpm-package-manager-cli.md)
- [The package wizard](package-wizard.md)
- [A package installer that survives big packages](package-installer-batching.md)
- [Look inside a package, compare it with your site, import single items](package-compare-and-import.md)
- [Package licenses and Semantic Versioning](package-licenses-and-versions.md)
- [About and Copyright pages](about-and-package-pages.md)

## Content management and the administration

- [The admin4 design](admin4-design.md)
- [The admin4l design: the admin page built from layouts](admin4l.md)
- [The responsive admin design (admin3)](admin3-responsive-admin.md)
- [Left sidebar width and font size](admin3-sidebar-width-and-font-size.md)
- [Exponential Admin UI: the adminui design and siteaccess](exp-adminui.md)
- [Choosing the administration design for your session](ui-switch-design.md)
- [Administration navigation: the top node, tabs and content browse](admin-content-tree-top-node.md)
- [Admin links follow permissions](admin-links-follow-permissions.md)
- [Hide admin tabs: HiddenTabs](hidden-admin-tabs.md)
- [Paging, sorting and page sizes in the administration interface](admin-list-paging.md)
- [Custom items per page](custom-items-per-page.md)
- [Preload Sites (administration interface)](preload-sites-view.md)
- [Quick location buttons](quick-location-buttons.md)
- [Bookmark folders](bookmark-folders.md)
- [The content finder](content-finder.md)
- [Content jobs: large removes, copies and moves in the background](content-jobs.md)
- [The content checks: unreferenced objects, broken relations and missing files](content-checks.md)
- [Content classes: move an attribute to the top or the bottom](class-attribute-move-to-edge.md)
- [The sub-items list: 129 columns, presets and CSV export](subitems-table-options.md)
- [Copy selected sub-items](subitems-copy-selected.md)
- [Removing and hiding: the check of reverse relations](reverse-relation-check.md)
- [The trash shows who trashed an item and where it was](trash-who-and-where.md)
- [Version preview in other view modes](version-preview-view-modes.md)
- [Publish without notification](publish-without-notification.md)
- [The online editor on TinyMCE 8 (opt-in, next to TinyMCE 3)](online-editor-tinymce8.md)
- [Rich text: characters above U+FFFF and TeX source in custom tags](rich-text-supplementary-characters-and-tex-source.md)
- [PDF export](pdf-export.md)
- [RSS: Apple Podcasts feeds, a paged feed list and safer exports](rss-podcast-and-feed-list.md)
- [jQuery 4 in the admin, and YUI gone](jquery4-and-yui-removal.md)
- [The help system: the documentation inside the administration](help-system.md)
- [Help maintained as content](help-content.md)

## Sites, languages and digital experience

- [Links to content of another site of a multisite installation](multisite-cross-site-links.md)
- [Siteaccess by host prefix, and the redirect to the language](siteaccess-host-and-language-matching.md)
- [Translations and languages](translations-and-languages.md)
- [Right-to-left languages in the administration](right-to-left.md)
- [Interface language and the UI switch](ui-switch.md)
- [The interface language list: every language, the most needed first](ui-switch-languages.md)
- [The grouped country list](country-groups.md)
- [A useful robots.txt out of the box](robots-txt.md)
- [The product is called Exponential](rebranding-to-exponential.md)

## E-commerce

- [Store dashboard and order statuses](store-dashboard.md)
- [Permanent order receipts](order-receipts.md)
- [Sort the order list by any column](order-list-sorting.md)

## Users, permissions and security

- [The audit trail: who did what, and proof that nobody changed the record](audit-trail.md)
- [The secret store: passwords and keys of the tools, encrypted, by name](secret-store.md)
- [Content Security Policy](content-security-policy.md)
- [Safe redirects and "back to where you came from"](safe-redirects.md)
- ["This form has expired": form token refusals](form-expired-page.md)
- [SOAP calls over HTTPS: certificates of an internal CA](soap-client-tls.md)
- [Changing your password](modern-password-change.md)
- [Change password at next sign-in](password-change-at-next-sign-in.md)
- [Reset a user password from the command line](reset-user-password.md)
- [Check user accounts](user-hygiene.md)
- [Signed links in the "New user registered" mail](registration-admin-links.md)
- [Roles: policy IDs, sorting and order buttons](role-policy-order.md)
- [The users and groups of a role, a page at a time](role-assignment-paging.md)
- [Permission checks for users with many role assignments](permission-checks-for-many-assignments.md)
- [Faster access arrays for users with many role assignments](access-array-prefetch.md)
- [Notifications: the user's guide](notifications.md)
- [E-mail preferences: the user's guide](mail-preferences.md)

## The Exponential Platform line

- [Exponential Platform DXP skeleton: create-project and a documented install](platform-dxp-skeleton.md)
- [Exponential Platform Nexus: a finished site in minutes](platform-nexus-starter.md)
- [The Exponential Platform administration interface](platform-admin-ui-fork.md)
- [Layouts on the platform: the layouts-core fork](platform-layouts-core-fork.md)
- [Site bundles, metadata and the Twig tools of the platform](platform-site-bundles.md)
- [SQLite for Exponential Platform: no database server needed](platform-sqlite-install.md)
- [PHP 8.4 and 8.5 for the older Symfony stack: the framework forks](platform-php85-framework-forks.md)
- [Legacy bridge: run Exponential 6 inside the Symfony platform](legacy-bridge.md)
