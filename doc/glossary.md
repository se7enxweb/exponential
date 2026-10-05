# Glossary

Every term the documentation uses, in one line, with a link to the page that teaches it. Terms are in alphabetical
order. A term in **bold** inside a definition has its own entry. For a first walk through the product, use
[the guides](guides/README.md); for the story behind a term, use [the history](history/README.md).

## In short

- One line per term; follow the link for the how-to, the settings and the examples.
- Names that start with `exp` or `sevenx` are Exponential extensions or classes; names that start with `ez` are older extensions and kernel identifiers that keep their names.
- Commands are shown as you type them in the installation root: `./console exp:<name>` is the same program as `php bin/php/<name>.php`.

## Contents

[A](#a) · [B](#b) · [C](#c) · [D](#d) · [E](#e) · [F](#f) · [G](#g) · [H](#h) · [I](#i) · [J](#j) · [K](#k) · [L](#l) · [M](#m) · [N](#n) · [O](#o) · [P](#p) · [Q](#q) · [R](#r) · [S](#s) · [T](#t) · [U](#u) · [V](#v) · [W](#w) · [X](#x) · [Y](#y) · [Z](#z)

## A

- **admin, admin2, admin3, admin4, classic**: the administration designs. `admin3` works on phones and wide screens; `admin4` is a complete design with a light and a dark mode; `classic` is the grey interface without YUI. See [admin3](features/6.0/admin3-responsive-admin.md), [admin4](features/6.0/admin4-design.md), [admin4l](features/6.0/admin4l.md) (admin4 built from layouts), [admin classic theme](features/6.0/extensions/sevenx_themes_admin_classic.md).
- **AdminAid**: an extension with a debugging helper for administrators. [AdminAid](features/6.0/extensions/AdminAid.md).
- **adminneo**: a database manager (a fork of Adminer), embedded in the admin by the DSE extension. [AdminNeo](features/6.0/adminneo-database-manager.md).
- **Apache**: the web server that can run the installation together with **PHP-FPM**. [Deploying](guides/deploying.md).
- **attribute**: one field of a **content class**, typed by a **datatype**. [Content model](guides/content-model-and-editing.md).
- **audit channel**: one of the five hash-chained streams of the **audit trail** (`content`, `access`, `system`, `commerce`, `read`). [Audit event model](specifications/6.0/audit-event-model.md).
- **audit trail**: a tamper-evident log of security-relevant events (sign-ins, content changes, settings writes, cache clears). [Audit trail](features/6.0/audit-trail.md), [event model](specifications/6.0/audit-event-model.md).
- **autoload**: the generated class maps (`var/autoload`) that tell PHP where each class lives; regenerate with `php bin/php/ezpgenerateautoloads.php`. [Operating a site](guides/operating-a-site.md).

## B

- **bc note** (behaviour change note): a page that says what changed in behaviour and what to check when you upgrade. [Behaviour changes, July and August 2026](bc/6.0/behaviour-changes-2026-07-08.md).
- **bcgooglesitemaps**: an extension that generates search engine **sitemaps** as a **cronjob**. [bcgooglesitemaps](features/6.0/extensions/bcgooglesitemaps.md).
- **bcwebsitestatistics**: an extension that sends page views and shop purchases to an analytics service. [bcwebsitestatistics](features/6.0/extensions/bcwebsitestatistics.md).
- **block (layout)**: a unit of content placed in a **zone** of a **layout**. [Exponential Layouts](bc/6.0/LAYOUTS.md).
- **bridge**: see **legacy bridge**.

## C

- **cache**: stored results that make pages faster; Exponential has many (content view, template, INI, HTTP, static, SQL query, response). [Cache control](bc/6.0/cache-console.md), [Operating a site](guides/operating-a-site.md).
- **cache block**: a template tag that caches the markup inside it; the Valkey variant is `{valkey-block}`. [Redis and Valkey caches](features/6.0/extensions/sevenx_valkey_cache.md).
- **changelog**: the release notes of a version, in Added, Updated, Removed and Renamed lines. [6.0.15](changelogs/6.0/6.0.15.md), [extensions](changelogs/extensions/README.md).
- **chronicle**: the month by month story of the history. [History](history/README.md).
- **CI (continuous integration)**: the GitHub Actions workflow `.github/workflows/phpunit.yml` that runs the PHPUnit suites on PHP 8.1 to 8.5 for every change. [Continuous integration](specifications/6.0/continuous-integration.md).
- **cjw_newsletter**: a complete newsletter system. [cjw_newsletter](features/6.0/extensions/cjw_newsletter.md).
- **class**: see **content class**.
- **classic**: see **admin**.
- **cluster (file cluster)**: file storage in the database or a shared store so several servers see the same files. [Operating a site](guides/operating-a-site.md).
- **collected information**: answers visitors submit through forms (contact, polls), stored per object and exportable. [bccie](features/6.0/extensions/bccie.md).
- **Composer**: the PHP package manager; the installation's `composer.json` decides which extensions arrive. [Default extension distribution](features/6.0/default-extension-distribution.md).
- **console**: the command `./console`, which finds and runs every command of an installation (`exp:`, `bin:`, `cron:`, `ext:`). [Console](bc/6.0/console.md).
- **consent log**: the record of every change of a person's e-mail preferences, with the time, the source, the exact text the person saw and the IP address; anonymised on erasure. [E-mail preferences](features/6.0/mail-preferences.md), [specification](specifications/6.0/mail-preferences.md).
- **content class**: the definition of a kind of content (for example Article) as a list of **attributes**. [Content model](guides/content-model-and-editing.md).
- **content job**: a large remove, copy or move run in the background instead of one web request. [Content jobs](features/6.0/content-jobs.md).
- **content object**: one piece of content, an instance of a **content class**, with versions and translations; it appears in the tree through one or more **nodes**. [Content model](guides/content-model-and-editing.md).
- **content tree**: the hierarchy of **nodes** shown in the admin; node 1 is its root. [Content model](guides/content-model-and-editing.md).
- **content view cache**: the stored result of rendering a node's view; cleared by `exp:cache`. [Cache control](bc/6.0/cache-console.md).
- **coverage**: the generated proof that every change is accounted for in the documentation. [Coverage](history/coverage.md).
- **cron / cronjob part**: a scheduled maintenance task (`cron:<name>`, run by `runcronjobs.php`). [Cronjobs in the console](features/6.0/cronjobs-console.md), [Operating a site](guides/operating-a-site.md).

## D

- **database drivers**: MySQL and MariaDB, PostgreSQL, SQLite, MongoDB and Oracle. [SQLite](features/6.0/sqlite-database.md), [MongoDB](features/6.0/mongodb-database-support.md), [Oracle and SQLite](specifications/6.0/database-drivers-sqlite-oracle.md).
- **datatype**: the code that stores, validates, edits and shows one kind of **attribute** (text, image, relation, tags). [Datatype and input hardening](specifications/6.0/datatype-input-hardening.md).
- **design**: a set of templates, styles and images; the **siteaccess** chooses which designs are used. [Getting started](guides/getting-started.md).
- **design.ini**: the settings file that lists designs, stylesheets and scripts. [Multi-site INI overrides](features/6.0/multi-site-ini-overrides.md).
- **DFS**: the database file system flavour of the **cluster** handler. [Operating a site](guides/operating-a-site.md).
- **digest (notification)**: one e-mail that collects the notification messages of a day, a week or a month instead of one mail per change. [Notifications](features/6.0/notifications.md), [specification](specifications/6.0/notifications.md).
- **double opt-in**: switching a kind of e-mail on (newsletters, offers) or using a new address only after the link sent to the address is confirmed. [E-mail preferences](features/6.0/mail-preferences.md).
- **DSE** (`sevenx_dse`): the Database Source Editor, which embeds **adminneo** in the admin. [sevenx_dse](features/6.0/extensions/sevenx_dse.md).
- **DXP skeleton**: a starter project for the Symfony based platform. [Platform DXP skeleton](features/6.0/platform-dxp-skeleton.md).

## E

- **e-mail preferences**: the page where each person turns each optional kind of e-mail on or off, sees the history and downloads the data (`/mailpreferences/settings`). [User's guide](features/6.0/mail-preferences.md), [administrator's guide](guides/mail-preferences-administrator.md), [law checklist](specifications/6.0/mail-preferences-compliance.md).
- **engine (web server engine)**: the program that serves the site: `php` (built-in server), `qbix` (**Velocity**) or `frankenphp`. [Velocity engines](bc/6.0/velocity-engines.md).
- **engine.phar**: the archive of `kernel/`, `lib/` and `autoload/` that Velocity can load. [Engine archive](bc/6.0/phar.md).
- **Exp Debug bar**: a bar on pages that shows queries, time and template information to administrators. [Exp Debug bar](features/6.0/exp-debug-bar.md).
- **exp:** the prefix of the console commands that run `bin/php/*.php` scripts. [Console](bc/6.0/console.md).
- **exp:ini**: the command that reads and writes **INI** settings in every scope. [exp:ini](features/6.0/exp-ini-command.md).
- **exp_enhanced_link**: the extension that provides the enhanced link field type. [exp_enhanced_link](features/6.0/extensions/exp_enhanced_link.md).
- **explayouts**: the extensions that bring layouts, blocks, zones and rules to the admin. [Exponential Layouts](bc/6.0/LAYOUTS.md), [explayouts_ui](features/6.0/extensions/explayouts_ui.md), [API specification](specifications/6.0/explayouts-ui-api.md).
- **Exponential Platform**: the Symfony based product line (`bin/console`, `exponential:*` commands) that can run Exponential 6 through the **legacy bridge**. [Platform package map](specifications/6.0/platform-package-map.md), [command names](specifications/6.0/platform-console-commands.md).
- **expservices**: the library of remote services built from **ezjscore** server functions. [Remote services](features/6.0/remote-services-expservices.md), [specification](specifications/6.0/expservices.md).
- **expui** (Exponential UI): the JavaScript component layer (`exp::*`) that replaced YUI in the admin. [jQuery 4 and YUI removal](features/6.0/jquery4-and-yui-removal.md).
- **extension**: a folder under `extension/` that adds settings, classes, modules, templates or designs; activated in `ActiveExtensions`. [Extension list](features/6.0/extension-list-and-downloads.md), [loading order](features/6.0/extension-loading-order.md).
- **extension metadata**: version, license and website of an extension, read by the about page. [Extension metadata](specifications/6.0/extension-metadata.md).
- **ezautosave**: automatic saving of the draft in the edit form. [ezautosave](features/6.0/extensions/ezautosave.md).
- **ezdemo**: the demo website of Exponential. [ezdemo](features/6.0/extensions/ezdemo.md).
- **ezflow**: the page builder of the classic design (a page datatype with zones and blocks). [ezflow](features/6.0/extensions/ezflow.md).
- **ezformtoken**: the kernel extension that protects forms with a token; see **form token**. [Form expired page](features/6.0/form-expired-page.md).
- **ezgmaplocation**: a datatype for latitude and longitude. [ezgmaplocation](features/6.0/extensions/ezgmaplocation.md).
- **ezie**: the image editor inside the edit form. [ezie](features/6.0/extensions/ezie.md).
- **ezjscore**: the JavaScript foundation: packs scripts and styles, loads jQuery, hosts server functions. [ezjscore](features/6.0/extensions/ezjscore.md).
- **ezmbpaex**: password expiry rules. [ezmbpaex](features/6.0/extensions/ezmbpaex.md).
- **ezmultiupload**: upload many files at once into folders and galleries. [ezmultiupload](features/6.0/extensions/ezmultiupload.md).
- **ezodf**: import and export of OpenDocument text. [ezodf](features/6.0/extensions/ezodf.md).
- **ezoe**: the online rich text editor (TinyMCE 3 and TinyMCE 8). [ezoe](features/6.0/extensions/ezoe.md), [TinyMCE 8](features/6.0/online-editor-tinymce8.md).
- **ezpaypal**: the PayPal payment gateway. [ezpaypal](features/6.0/extensions/ezpaypal.md).
- **ezpkg**: the package archive format (`.ezpkg`) holding content, classes, templates and files. [ezpm](features/6.0/ezpm-package-manager-cli.md), [Package compare and import](features/6.0/package-compare-and-import.md).
- **ezpm** (command): the package manager on the command line (`ezpm.php`). [ezpm](features/6.0/ezpm-package-manager-cli.md). Not to be confused with the **ezpm extension**, which provides private messages: [ezpm extension](features/6.0/extensions/ezpm.md).
- **ezprestapi**: the REST API provider for remote content updates. [ezprestapi](features/6.0/extensions/ezprestapi.md).
- **ezstarrating**: a star rating datatype. [ezstarrating](features/6.0/extensions/ezstarrating.md).
- **eztags**: a tag datatype with a managed tag tree. [eztags](features/6.0/extensions/eztags.md).
- **ezupdate**: the package and update checker of an installation. [ezupdate](features/6.0/extensions/ezupdate.md).
- **ezwebin**: the classic ready-made website. [ezwebin](features/6.0/extensions/ezwebin.md).
- **ezwt**: the website toolbar editors see on the public site. [ezwt](features/6.0/extensions/ezwt.md).
- **ezxmlexport**: scheduled XML export with FTP delivery. [ezxmlexport](features/6.0/extensions/ezxmlexport.md).

## F

- **feature page**: a how-to page for one user-facing feature, with settings, examples and limits. [Features](features/6.0/extensions/README.md).
- **fetch function**: a template function that reads content or system data (`fetch('content','list',...)`). [Content model](guides/content-model-and-editing.md).
- **file consistency check**: Setup > Upgrade check, which compares installed files with the shipped checksums. [File consistency check](features/6.0/file-consistency-check.md).
- **form token**: the hidden value that protects a form against cross-site request forgery. [Form expired page](features/6.0/form-expired-page.md).
- **FrankenPHP**: a production-ready web engine (Caddy with PHP built in), selectable beside **Velocity**. [FrankenPHP](bc/6.0/frankenphp.md).

## G

- **git_manager**: web admin tools for the Git side of an installation. [git_manager](features/6.0/extensions/git_manager.md).
- **guide**: a tutorial that gets you a working result in minutes. [Guides](guides/README.md).

## H

- **hCaptcha**: a captcha datatype. [hcaptcha](features/6.0/extensions/hcaptcha.md).
- **hook**: a point where the kernel calls an extension if its class exists, for example the Valkey cache hooks. [Redis and Valkey caches](features/6.0/extensions/sevenx_valkey_cache.md).
- **HTTP cache (role-aware)**: whole rendered pages kept per permission context and served before the kernel starts. [Role-aware HTTP cache](bc/6.0/httpcache.md), [HTTP caching](bc/6.0/http-caching.md).
- **HTTP/2**: the protocol Velocity speaks over TLS. [HTTP/2 and cache warming](bc/6.0/http2-and-cache-warming.md), [specification](specifications/6.0/velocity-http2-and-security.md).

## I

- **INI file**: a settings file (`site.ini`, `content.ini`, ...) in blocks of `Key=Value`; read in order: defaults, extensions, siteaccess, override. [INI override placements](specifications/6.0/ini-override-placements.md), [exp:ini](features/6.0/exp-ini-command.md).
- **install type**: a named installer of **Exponential Platform** (`exponential-oss`, `ibexa-oss`, `exponential-media`, `exponential-cjw`) run by `exponential:install <type>`. [Platform SQLite installer](specifications/6.0/platform-sqlite-installer.md).
- **installation name**: the name shown on non-production pages so staging is never taken for production. [Installation name](features/6.0/installation-name-in-pages.md).
- **installer / install**: `./console exp:install` installs a whole site in one command. [Install in one command](features/6.0/install-in-one-command.md).

## J

- **jQuery 4**: the JavaScript library of the admin since October 2026. [jQuery 4 and YUI removal](features/6.0/jquery4-and-yui-removal.md).

## K

- **kernel**: the core code in `kernel/` and `lib/` that every installation runs. [Rebranding to Exponential](features/6.0/rebranding-to-exponential.md).
- **Kickstarter**: the installer that answers the setup wizard from `kickstart.ini`. [Kickstarter](features/6.0/kickstarter-cli.md), [reference](bc/6.0/kickstartercli.md).

## L

- **layout (Exponential Layouts)**: a page structure made of **zones** that hold **blocks**, chosen by **rules**. [Exponential Layouts](bc/6.0/LAYOUTS.md).
- **ledger**: the complete record of every commit of every repository. [Ledger](history/ledger/README.md).
- **legacy bridge**: the package that runs Exponential 6 inside the Symfony platform. [Legacy bridge](features/6.0/legacy-bridge.md), [specification](specifications/6.0/legacy-bridge-bundle.md).
- **loading order**: the order in which active **extensions** are read; later wins. [Extension loading order](features/6.0/extension-loading-order.md).
- **location**: another word for a **node**. [Content model](guides/content-model-and-editing.md).

## M

- **mail category**: the kind a mail belongs to (`content`, `newsletter`, `security`, ...), declared with `eZMail::setCategory()`; essential categories are always sent, optional ones only to people who turned them on. [Developer's guide](guides/mail-preferences-developer.md).
- **mail gate**: the check in `eZMailTransport::send()` that every mail passes: it blocks optional mail people did not ask for and adds the footer and the unsubscribe headers. [Specification](specifications/6.0/mail-preferences.md).
- **maintenance mode**: takes the site offline with a notice (`exp:maintenance on|off|status`). [Maintenance mode](features/6.0/maintenance-mode.md).
- **module**: a set of **views** behind a URL such as `content/view`; extensions can add or override modules. [Extension module override](features/6.0/extension-module-override.md).
- **MongoDB**: a supported database. [MongoDB](features/6.0/mongodb-database-support.md).
- **multi-node edit**: editing several items in one form. [Editing several items at once](bc/6.0/multi-node-edit.md).
- **multisite package**: the site package that `exp:install` installs by default (`sevenx_multisite`). [Install in one command](features/6.0/install-in-one-command.md).

## N

- **Nexus**: the starter projects of the Symfony based platform. [Platform Nexus starter](features/6.0/platform-nexus-starter.md).
- **ngclasslist**: a datatype that stores a list of content classes. [ngclasslist](features/6.0/extensions/ngclasslist.md).
- **ngsymfonytools**: lets legacy templates include Twig templates and run Symfony code. [ngsymfonytools](features/6.0/extensions/ngsymfonytools.md).
- **node**: one place of a **content object** in the **content tree**; has an id, a parent, a sort order and a visibility. [Content model](guides/content-model-and-editing.md).
- **notification**: an e-mail that tells a user that followed content changed or that a collaboration item needs attention; made from an event by a handler when the notification cronjob runs. [Notifications](features/6.0/notifications.md), [administrator's guide](guides/notifications-administrator.md), [specification](specifications/6.0/notifications.md).
- **nxc_powercontent**: an extension that extends how code creates, updates and removes content. [nxc_powercontent](features/6.0/extensions/nxc_powercontent.md).

## O

- **one-click unsubscribe**: the link at the end of optional mail and the `List-Unsubscribe` and `List-Unsubscribe-Post` headers of RFC 8058, which let a mail program unsubscribe without a page. [E-mail preferences](features/6.0/mail-preferences.md).
- **OPcache**: PHP's compiled code cache; Velocity keeps rewritten cache files in it. [Opcode cache and profile](features/6.0/velocity-opcode-cache-and-profile.md).
- **operator (template)**: a function applied with a pipe (`$text|wash`). [String template operators](features/6.0/string-template-operators.md), [role and policy operators](features/6.0/role-and-policy-template-operators.md), [owsimpleoperator](features/6.0/extensions/owsimpleoperator.md), [swark](features/6.0/extensions/swark.md).
- **OPML**: a list of feeds, exportable from the RSS module. [OPML exports](bc/6.0/opml.md).
- **Oracle**: a supported database, through the `ezoracle` extension; install with `./console exp:install --db=oracle`. [Database drivers and installers](specifications/6.0/database-drivers-2026-09.md#oracle).
- **override**: a rule that says "for this class, node or section use this template instead of the default", kept in `override.ini`. [Template editor](features/6.0/template-editor-overrides.md), [override order](features/6.0/template-override-ordering.md).

## P

- **package**: a set of content, classes and design that can be exported and installed; also a Composer package. [ezpm](features/6.0/ezpm-package-manager-cli.md), [Platform package map](specifications/6.0/platform-package-map.md).
- **page layout** (`pagelayout.tpl`): the outer template that wraps every page. [Templates and design](guides/templates-and-design.md).
- **phar**: see **engine.phar**.
- **PHP-FPM**: the PHP process manager that serves the site behind **Apache**. Reload the one that serves your site after a PHP change. [Deploying](guides/deploying.md).
- **placement (INI)**: the name of the place a setting's value came from (`default`, `override`, `extension:<ext>`, `siteaccess`, ...). [INI override placements](specifications/6.0/ini-override-placements.md).
- **policy**: one permission (module, function, limitation) inside a **role**. [Roles and policies in order](features/6.0/role-policy-order.md).
- **PostgreSQL**: a supported database. [Install in one command](features/6.0/install-in-one-command.md).
- **preload**: requesting every published page so no visitor pays for a render (`exp:preload`, `exp:warm`, Setup > Preload). [Preload view](features/6.0/preload-sites-view.md), [Preloader](bc/6.0/preload.md).

## Q

- **Q settings**: the Velocity engine's own settings, JSON under the `Q` key; from Exponential they are normally set through `settings/velocity.ini`. [Velocity engine settings](specifications/6.0/velocity-engine-settings.md).
- **Q shell**: the drop-down console in the Velocity control panel. [Q shell](features/6.0/velocity-q-shell.md).
- **qbix**: the engine name of **Velocity** inside Exponential's settings and the upstream project it is based on. [Velocity engines](bc/6.0/velocity-engines.md).
- **query cache (SQL)**: answers repeated `SELECT` queries without asking the database until a write makes them stale. [SQL query cache](bc/6.0/sql-query-cache.md).

## R

- **RAD** (rapid application development): the extension points that let code add operators, views, commands and datatypes without editing the kernel. [RAD tools](features/6.0/rad-tools.md), [extension points](bc/6.0/rad-extension-points.md), [extension surface](bc/6.0/rad-extension-surface.md).
- **reCAPTCHA**: a captcha datatype. [recaptcha](features/6.0/extensions/recaptcha.md).
- **recipe**: a Symfony Flex recipe that configures a package when it is installed. [sevenx-recipes](features/6.0/extensions/sevenx-recipes.md).
- **Redis / Valkey**: servers that can hold the content view and INI caches. [Redis and Valkey caches](features/6.0/extensions/sevenx_valkey_cache.md).
- **repair page**: the page that explains missing libraries and lets an administrator repair them. [Repair from the browser](features/6.0/repair-from-the-browser.md), [Repair](bc/6.0/repair.md).
- **request rule**: a rule in `requestrules.ini` that allows or refuses a request by module, view and URL. [Request rules](features/6.0/request-rules.md), [securing content/view/full](bc/6.0/view_full_security.md).
- **response cache (Velocity)**: the server's cache of rendered pages. [Response cache](features/6.0/velocity-response-cache.md).
- **role**: a named set of **policies** assigned to users and groups. [Roles and policies in order](features/6.0/role-policy-order.md).
- **RSS**: feeds exported and imported by the RSS module; see also **syndication**. [RSS, podcast and feed list](features/6.0/rss-podcast-and-feed-list.md).
- **runnable**: the class behind a command, cronjob part or module view (`Exponential\Runnable\*`); can be replaced or listened to through `[RunnableSettings]`. [Commands, cronjob parts and views as classes](specifications/6.0/runnable-commands-cronjobs-views.md).

## S

- **section**: a label on content that selects a design and permissions. [Content model](guides/content-model-and-editing.md).
- **security defaults**: the settings that make a default installation safe without extra work (security headers, session cookie, login, served files, caching). [Security defaults](specifications/6.0/security-defaults-2026-09.md), [datatype and input hardening](specifications/6.0/datatype-input-hardening.md).
- **seed data**: the tables and content a new installation starts with. [Installer logs and seed data](specifications/6.0/installer-logs-and-seed-data.md).
- **setup wizard**: the browser installer. [Setup wizard](features/6.0/setup-wizard-and-editor-siteaccess.md).
- **site package**: the content and design set installed at setup. [Clean install defaults](features/6.0/clean-install-defaults.md).
- **siteaccess**: one way of reaching the site (by URL, host or port) with its own settings and design, such as `site` and `admin`. [Getting started](guides/getting-started.md), [Multi-site INI overrides](features/6.0/multi-site-ini-overrides.md).
- **Smaller changes**: the section of a chronicle page that lists changes without a story of their own. [History](history/README.md).
- **Solr**: the search server controlled by `exp:solr`. [Web server and Solr commands](features/6.0/web-server-and-solr-commands.md).
- **specification**: a reference page for a subsystem: data model, classes, settings, APIs. [Specifications](specifications/6.0/README.md).
- **SQLite**: the database in one file; the default of `exp:install`. [SQLite](features/6.0/sqlite-database.md), [driver specification](specifications/6.0/sqlite3-database-driver.md).
- **state (object state)**: a label from an object state group, for example the lock state "Not locked", that policies can test; not the same as the status of a **version** (draft, published). [Installer seed data](specifications/6.0/installer-logs-and-seed-data.md).
- **static cache**: pages written to files and served without PHP. [Static cache generator](features/6.0/static-cache-generator.md), [defaults](bc/6.0/static-cache-defaults.md).
- **suppression list**: the addresses that get no optional e-mail (bounces, complaints, requests), stored only as salted hashes (`exp:mail:suppression`). [Administrator's guide](guides/mail-preferences-administrator.md).
- **swark**: an extension with many template operators and workflow events. [swark](features/6.0/extensions/swark.md).
- **syndication**: the extension for export and import feeds. [syndication](features/6.0/extensions/syndication.md), [specification](specifications/6.0/syndication.md).

## T

- **template**: a `.tpl` file in Exponential's template language; resolved by design and **overrides**. [Template path comments](features/6.0/template-path-comments.md).
- **template path comments**: `START` and `STOP` comments naming each template in the page source. [Template path comments](features/6.0/template-path-comments.md).
- **test suite**: the PHPUnit suites of `phpunit.xml`, run with `php vendor/bin/phpunit`. [Continuous integration and the test suite](specifications/6.0/continuous-integration.md).
- **TinyMCE**: the editor engine behind **ezoe**, in versions 3 and 8. [TinyMCE 8](features/6.0/online-editor-tinymce8.md).
- **translation**: a language version of a content object, or a language file of the interface. [Translations and languages](features/6.0/translations-and-languages.md).
- **trash**: where removed objects wait to be restored or purged. [Trash](features/6.0/trash-who-and-where.md).
- **two-factor authentication**: a second proof at sign-in, with OAuth social login. [sevenx_authentication_2fa](features/6.0/extensions/sevenx_authentication_2fa.md).

## U

- **URL alias**: a readable address mapped to a node; `exp:updateniceurls` rebuilds them. [Content model](guides/content-model-and-editing.md).
- **uwebserver**: a small C web server shipped with Velocity, used for static files and benchmarks. [uwebserver](features/6.0/velocity-uwebserver.md).

## V

- **Valkey**: see **Redis / Valkey**.
- **Velocity**: the PHP application server bundled with Exponential, run with `exp:velocity`, recommended for every stage. [Velocity web server](features/6.0/velocity-web-server.md), [persistent workers](features/6.0/velocity-persistent-worker-server.md), [control panel](features/6.0/velocity-control-panel.md), [engine settings](specifications/6.0/velocity-engine-settings.md), [chronicle](history/velocity/README.md).
- **version (content)**: one saved state of a content object (draft, published, archived). [Content model](guides/content-model-and-editing.md).
- **view**: a function of a **module** reached by a URL; also the way a node is rendered (full, line, embed). [Module views as classes](specifications/6.0/runnable-commands-cronjobs-views.md), [Templates and design](guides/templates-and-design.md).

## W

- **warm-up**: preparing the Velocity parent process before it forks its workers; also the same word for **preload**. [Velocity worker pool](specifications/6.0/velocity-worker-pool.md).
- **WebSocket**: a long-lived connection between browser and server, supported by Velocity. [WebSockets and events](features/6.0/velocity-websockets-and-events.md).
- **worker**: one long-lived PHP process of Velocity that answers many requests. [Velocity worker pool](specifications/6.0/velocity-worker-pool.md).

## X

- **xrowextract**: an extension that exports and packages content on schedules. [xrowextract](features/6.0/extensions/xrowextract.md), [specification](specifications/6.0/xrowextract.md).
- **xrowmetadata**: a meta data attribute for page title and keywords. [xrowmetadata](features/6.0/extensions/xrowmetadata.md).

## Y

- **YUI**: the old JavaScript library, removed in October 2026. [YUI removal](bc/6.0/yui-removal.md).

## Z

- **zygote**: a helper process of **Velocity** that forks new **workers** so they inherit no visitor connections. [Velocity worker pool](specifications/6.0/velocity-worker-pool.md).

## Related pages

- [Guides: the learning path](guides/README.md)
- [History](history/README.md) and [coverage](history/coverage.md)
- [Install in one command](features/6.0/install-in-one-command.md)
- [Specifications](specifications/6.0/README.md): the reference pages
