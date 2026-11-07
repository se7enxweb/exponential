# Specification: the expcomponents packages and what the kernel uses them for

This page is the reference for each library of **expcomponents**, Exponential's fork of the Zeta Components
(Apache License 2.0): what the package does, which kernel classes, files and features use it, which upstream package
it replaces and where its repository is. The overview, the history and the reasons for the fork are on the feature
page [Exponential Components (expcomponents)](../../features/6.0/expcomponents.md); what an upgrade means is in the
[upgrade notes](../../bc/6.0/expcomponents.md).

The usage lists were taken from the kernel's own code (`kernel/`, `lib/`, `bin/`, `update/`, `index_webdav.php`) by
searching for each component's class prefix, for example `grep -rlE '\bezcMail' --include=*.php kernel lib`. Repeat
that search to check a list against your release. Extensions are named where a shipped extension depends on a
component.

## Contents

1. [Common rules for every package](#common-rules-for-every-package)
2. [Summary table](#summary-table)
3. [base](#base)
4. [archive](#archive)
5. [authentication](#authentication)
6. [cache](#cache)
7. [configuration](#configuration)
8. [console-tools](#console-tools)
9. [database](#database)
10. [debug](#debug)
11. [event-log](#event-log)
12. [feed](#feed)
13. [image-analysis](#image-analysis)
14. [image-conversion](#image-conversion)
15. [mail](#mail)
16. [mvc-tools](#mvc-tools)
17. [persistent-object](#persistent-object)
18. [signal-slot](#signal-slot)
19. [system-information](#system-information)
20. [webdav](#webdav)
21. [Forks the kernel does not require](#forks-the-kernel-does-not-require)
22. [Upstream packages that are not forked](#upstream-packages-that-are-not-forked)

## Common rules for every package

| Property | Value for every fork |
|---|---|
| Package name | `se7enxweb/expcomponents-<name>`, where `<name>` is the upstream package name without `zetacomponents/` |
| Repository | `https://github.com/se7enxweb/expcomponents-<name>`, a GitHub fork of `https://github.com/zetacomponents/<Name>` |
| `replace` | `"zetacomponents/<name>": "self.version"` (webdav also `se7enxweb/zetacomponents-webdav`) |
| License | `Apache-2.0`; `LICENSE.txt` with the upstream notice lines, a `NOTICE` file with the upstream notices and the fork's name, `CREDITS`, `ChangeLog` |
| Class prefix | `ezc<Component>`, unchanged |
| Autoload | `"classmap": ["src"]` in the package's `composer.json`, loaded by `vendor/autoload.php` |
| Installed at | `vendor/se7enxweb/expcomponents-<name>/` |
| Tags | Without a `v` prefix (`1.5.2`); permanent once pushed |
| Kernel constraint | Tilde form (`~1.5.3`), caret for base and console-tools (`^1.9.6`, `^1.7.6`), in the kernel's `composer.json` `require` |
| Tests | `tests/` with `phpunit.xml.dist`; development dependencies `zetacomponents/unit-test` and PHPUnit |

Version state: every fork named on this page was released on 2026-11-06 (a tag, a GitHub release and the version on
Packagist); archive 1.5.2 and webdav 1.2.2 were released a few days before. Each fork requires the others by their
fork names (`se7enxweb/expcomponents-base ~1.8`, not `zetacomponents/base`).

## Summary table

| Component | Fork package | Replaces | Kernel needs it for | Fork release (kernel constraint) |
|---|---|---|---|---|
| base | `se7enxweb/expcomponents-base` | `zetacomponents/base` | every component; the autoload generator's file walk; exceptions and options of the REST layer | 1.9.6 (`^1.9.6`) |
| archive | `se7enxweb/expcomponents-archive` | `zetacomponents/archive` | package import, export and upload (`.ezpkg`, tar and zip) | 1.5.3 (`~1.5.3`); 1.5.2 on 2026-11-03 |
| authentication | `se7enxweb/expcomponents-authentication` | `zetacomponents/authentication` | REST authentication filters: basic, API key, OAuth | 1.4.2 (`~1.4.2`) |
| cache | `se7enxweb/expcomponents-cache` | `zetacomponents/cache` | REST response and route caching | 1.6.4 (`~1.6.4`) |
| configuration | `se7enxweb/expcomponents-configuration` | `zetacomponents/configuration` | SQLI Import 2.0.x (one exception class) | 1.4.1 (`~1.4.1`) |
| console-tools | `se7enxweb/expcomponents-console-tools` | `zetacomponents/console-tools` | `ezpgenerateautoloads.php`, the help examples generator, an update script | 1.7.6 (`^1.7.6`) |
| database | `se7enxweb/expcomponents-database` | `zetacomponents/database` | the REST layer's database handle; the OAuth client list | 1.5.5 (`~1.5.5`) |
| debug | `se7enxweb/expcomponents-debug` | `zetacomponents/debug` | the REST debug output | 1.3.1 (`~1.3.1`) |
| event-log | `se7enxweb/expcomponents-event-log` | `zetacomponents/event-log` | log levels of the REST router and controller | 1.5.1 (`~1.5.1`) |
| feed | `se7enxweb/expcomponents-feed` | `zetacomponents/feed` | RSS 1.0, RSS 2.0 and Atom exports; REST feed views | 1.4.7 (`~1.4.7`) |
| image-analysis | `se7enxweb/expcomponents-image-analysis` | `zetacomponents/image-analysis` | required by image-conversion; the ezie image editor | 1.2.1 (through image-conversion) |
| image-conversion | `se7enxweb/expcomponents-image-conversion` | `zetacomponents/image-conversion` | the ezie image editor | 1.5.2 (`~1.5.2`) |
| mail | `se7enxweb/expcomponents-mail` | `zetacomponents/mail` | every mail: `eZMail`, SMTP and sendmail transports, the mail gate, bounces | 1.10.2 (`~1.10.2`) |
| mvc-tools | `se7enxweb/expcomponents-mvc-tools` | `zetacomponents/mvc-tools` | the REST API and the REST endpoints of GraphQL, help and migrations | 1.2.5 (`~1.2.5`) |
| persistent-object | `se7enxweb/expcomponents-persistent-object` | `zetacomponents/persistent-object` | OAuth clients, tokens and authorization codes | 1.8.3 (`~1.8.3`) |
| signal-slot | `se7enxweb/expcomponents-signal-slot` | `zetacomponents/signal-slot` | the hooks of the content publishing queue | 1.2.1 (`~1.2.1`) |
| system-information | `se7enxweb/expcomponents-system-information` | `zetacomponents/system-information` | Setup > System information | 1.1.3 (`~1.1.3`) |
| webdav | `se7enxweb/expcomponents-webdav` | `zetacomponents/webdav`, `se7enxweb/zetacomponents-webdav` | the WebDAV server (`index_webdav.php`) | 1.2.3 (`~1.2.3`); 1.2.2 on 2026-11-01 |

## base

- **Purpose:** the infrastructure every component relies on: the base exception classes (`ezcBaseException`,
  `ezcBaseValueException`, `ezcBasePropertyNotFoundException`, `ezcBaseFileNotFoundException` ...), the options
  base class `ezcBaseOptions`, the struct base class, `ezcBaseFile` (recursive directory walks, relative paths) and
  the original autoloader `ezcBase::autoload`.
- **Kernel use:**
  - `kernel/private/classes/ezautoloadgenerator.php`, the engine of `php bin/php/ezpgenerateautoloads.php`, walks the
    directories with `ezcBaseFile::findRecursive()` and `walkRecursive()` and computes paths with
    `calculateRelativePath()`;
  - `lib/ezutils/classes/ezsys.php` throws `ezcBasePropertyNotFoundException` for unknown properties;
  - option and struct classes in `kernel/private/options/` and `kernel/private/structs/`, the public API's content
    classes (`kernel/private/api/content/`), the cluster exceptions (`kernel/private/classes/exceptions/cluster/`),
    the DFS MySQLi backend, the OAuth modules and the REST layer (`kernel/private/rest/classes/`).
  - The setup wizard's check `setup.ini [ezcversion]` (`TestClass=ezcBaseFile`, `TestFunction=walkrecursive`) tests
    that the library is installed.
- **Autoload note:** `autoload.php` registers `ezcBase::autoload` only when `EZCBASE_ENABLED` is defined as `true`;
  with Composer it is not needed and not registered.
- **Fork:** `se7enxweb/expcomponents-base`, [repository](https://github.com/se7enxweb/expcomponents-base), replaces
  `zetacomponents/base`. Every other fork requires it by this name (`se7enxweb/expcomponents-base ~1.8`).

## archive

- **Purpose:** create, change and extract tar archives (ustar, v7, pax, GNU) and zip archives.
- **Kernel use:**
  - `kernel/classes/ezpackage.php`: export writes `.ezpkg` files with `ezcArchive::open( ..., ezcArchive::TAR_USTAR )`
    or `ezcArchive::ZIP`; import extracts them;
  - `kernel/classes/exppackagearchive.php` and `kernel/classes/exppackagearchivemodes.php`: reading package archives
    and the file modes of their entries;
  - `kernel/classes/ezpackageuploadinspector.php`: inspects an uploaded package before import;
  - `kernel/classes/exppackagejson.php`: the JSON package format reads the same archives.
- **Features:** Setup > Packages (import, export, upload), `ezpm` on the command line, site package installation.
- **Fork:** `se7enxweb/expcomponents-archive`, [repository](https://github.com/se7enxweb/expcomponents-archive),
  replaces `zetacomponents/archive`. Required by the kernel since 2026-11-03 (`~1.5.2`, `~1.5.3` since 2026-11-06). Release 1.5.2 (from
  upstream 1.5.1): no deprecations on PHP 8.4 and 8.5, GNU tar entries with long names or long links extracted again,
  tests on PHP 7.4 to 8.5.

## authentication

- **Purpose:** identify and authenticate users with filters for different credentials and providers.
- **Kernel use:** the REST layer's authentication: `kernel/private/rest/classes/auth/` (the authentication style
  base, the native user filter, the password filter, the API key filter and `apikey_rest.php`), the styles in
  `kernel/private/rest/classes/auth/styles/` (`basic_auth.php`, `no_auth.php`, `oauth.php`) and
  `kernel/private/rest/classes/oauth/oauth_filter.php`. Classes used: `ezcAuthentication`, `ezcAuthenticationFilter`,
  `ezcAuthenticationIdCredentials`, `ezcAuthenticationPasswordCredentials`.
- **Features:** REST API v1 and v2 sign-in, API keys, OAuth.
- **Fork:** `se7enxweb/expcomponents-authentication`,
  [repository](https://github.com/se7enxweb/expcomponents-authentication), replaces `zetacomponents/authentication`.

## cache

- **Purpose:** a cache manager with several storage back ends (files, memory, APC and others).
- **Kernel use:** the REST layer's cache: `kernel/private/rest/classes/cache/` (`object.php`, `storage.php`, `apc.php`,
  `cluster.php` with its options and exception, a cluster-aware storage of Exponential's own), the router
  (`kernel/private/rest/classes/router.php`) and the REST controller. Classes used: `ezcCacheManager`,
  `ezcCacheStorage`, `ezcCacheStorageFile`, `ezcCacheStorageMemory`, `ezcCacheStorageOptions`,
  `ezcCacheInvalidDataException`.
- **Features:** cached REST routes and responses.
- **Fork:** `se7enxweb/expcomponents-cache`, [repository](https://github.com/se7enxweb/expcomponents-cache), replaces
  `zetacomponents/cache`.

## configuration

- **Purpose:** read configuration files in several formats (INI and array based).
- **Kernel use:** none; the kernel has its own INI system (`eZINI`). The package stays required because 7x SQLI Import
  2.0.x throws one of its exception classes ([behaviour changes of October 2026](../../bc/6.0/behaviour-changes-2026-10.md)).
- **Fork:** `se7enxweb/expcomponents-configuration`,
  [repository](https://github.com/se7enxweb/expcomponents-configuration), replaces `zetacomponents/configuration`.
  The repository is new with the official fork; first fork release on 2026-11-06.

## console-tools

- **Purpose:** command line input (options and arguments), output with formats, progress bars, status bars and tables.
- **Kernel use:**
  - `kernel/private/classes/commands/ezpgenerateautoloads.php` and `kernel/private/classes/ezpautoloadclioutput.php`:
    the options and progress output of `php bin/php/ezpgenerateautoloads.php` (`ezcConsoleInput`,
    `ezcConsoleOption`, `ezcConsoleOutput`, `ezcConsoleProgressbar`, `ezcConsoleStatusbar`);
  - `kernel/classes/help/exphelpexamplesgenerator.php`: the help examples generator;
  - `update/common/scripts/4.2/fixorphanimages.php`: an update script.
- **Features:** class map generation (also run by `./console exp:velocity deploy` and Composer's post-install hooks).
- **Fork:** `se7enxweb/expcomponents-console-tools`,
  [repository](https://github.com/se7enxweb/expcomponents-console-tools), replaces `zetacomponents/console-tools`.
  The repository is new with the official fork; first fork release on 2026-11-06.

## database

- **Purpose:** a database layer on PDO with a query abstraction that hides differences between SQL dialects.
- **Kernel use:** `kernel/private/rest/classes/lazy.php` creates the REST layer's database handle with
  `ezcDbFactory` and `ezcDbInstance` (including `ezcDbHandlerSqlite`); `kernel/private/modules/oauthadmin/list.php`
  lists OAuth clients with `ezcQuerySelect`. The kernel's own database layer (`eZDB`) does not use it.
- **Features:** REST and OAuth storage; persistent-object needs it.
- **Fork:** `se7enxweb/expcomponents-database`, [repository](https://github.com/se7enxweb/expcomponents-database),
  replaces `zetacomponents/database`.

## debug

- **Purpose:** timers and structured debug output in several formats.
- **Kernel use:** `kernel/private/rest/classes/debug/debug.php` and
  `kernel/private/rest/classes/debug/formatters/php_formatter.php`: the REST layer's debug output
  (`ezcDebug`, `ezcDebugStructure`, `ezcDebugOutputFormatter`, `ezcDebugStacktraceIterator`, `ezcDebugTimerStruct`).
  The page debug output of the kernel (`eZDebug`, the Exp Debug bar) does not use it.
- **Fork:** `se7enxweb/expcomponents-debug`, [repository](https://github.com/se7enxweb/expcomponents-debug), replaces
  `zetacomponents/debug`. The repository is new with the official fork; first fork release on 2026-11-06.

## event-log

- **Purpose:** log events to files or other stores, with severities (`ezcLog::ERROR` ...).
- **Kernel use:** `kernel/private/rest/classes/router.php` and
  `kernel/private/rest/classes/controllers/rest_controller.php` log REST errors with the `ezcLog` severities through
  the REST debug class. The kernel's own logs (`eZLog`, `var/log/`) do not use it.
- **Fork:** `se7enxweb/expcomponents-event-log`, [repository](https://github.com/se7enxweb/expcomponents-event-log),
  replaces `zetacomponents/event-log`. The repository is new with the official fork; first fork release on 2026-11-06.

## feed

- **Purpose:** parse and write RSS 1.0, RSS 2.0 and Atom feeds with their modules (Dublin Core, content, iTunes ...).
- **Kernel use:**
  - `kernel/classes/ezrssexport.php`: `rssXmlContent()` writes RSS 1.0, RSS 2.0 and Atom exports through
    `generateFeed()`, which builds an `ezcFeed`; Apple Podcasts and OPML are written by the kernel's own code;
  - `kernel/private/rest/classes/views/feed_decorator.php`: feed views of the REST layer (`ezcFeedEntryElement`).
- **Features:** RSS exports at `/rss/feed/<address>` ([RSS, Atom and OPML feeds](../../help/integrators/rss-atom-and-opml-feeds.md)).
- **Fork:** `se7enxweb/expcomponents-feed`, [repository](https://github.com/se7enxweb/expcomponents-feed), replaces
  `zetacomponents/feed`. The repository is new with the official fork; first fork release on 2026-11-06.

## image-analysis

- **Purpose:** find the MIME type and properties (size, EXIF for JPEG and TIFF) of an image file.
- **Kernel use:** none directly. `image-conversion` requires it, and the image editor extension ezie uses it
  (`extension/ezie/classes/image_analyzer.php`).
- **Fork:** `se7enxweb/expcomponents-image-analysis`,
  [repository](https://github.com/se7enxweb/expcomponents-image-analysis), replaces `zetacomponents/image-analysis`.
  Installed as a dependency of the image-conversion fork, not listed in the kernel's `require`.

## image-conversion

- **Purpose:** apply filters to images (scale, crop, colors, effects) through GD or ImageMagick.
- **Kernel use:** none in `kernel/` or `lib/`; the kernel requires it for ezie, the image editor of the edit form,
  which does not declare it itself (`extension/ezie/classes/image_converter.php`, `image_pre_action.php`,
  `image_tool_resize.php`) ([ezie](../../features/6.0/extensions/ezie.md)). The kernel's image aliases use its own
  `eZImageManager` handlers, not this library.
- **Fork:** `se7enxweb/expcomponents-image-conversion`,
  [repository](https://github.com/se7enxweb/expcomponents-image-conversion), replaces `zetacomponents/image-conversion`.
  Release 1.5.2 includes upstream's change to run ImageMagick through the argument-list form of `proc_open()` (no
  shell parses the command) and fixes what that change broke: scaling and cropping with ImageMagick failed with
  "unrecognized option `-resize '" because the filters passed option names with a trailing blank. The fix is offered
  upstream as [zetacomponents/ImageConversion pull request #9](https://github.com/zetacomponents/ImageConversion/pull/9).
  The release also shows the command in the error message of a failed ImageMagick call, fails at once when the target
  directory cannot be written, refuses a JPEG that libjpeg reports an error for with every GD build, and prints no
  deprecations on PHP 8.5.

## mail

- **Purpose:** compose, parse and send e-mail: MIME parts, attachments, HTML mail, SMTP (with STARTTLS and
  authentication), sendmail, IMAP, POP3 and mbox.
- **Kernel use:**
  - `lib/ezutils/classes/ezmail.php`: `eZMail` builds every message on `ezcMail`, `ezcMailAddress`, `ezcMailText` and `ezcMailPart`;
  - `lib/ezutils/classes/ezsmtptransport.php`: `eZSMTPTransport` sends through `ezcMailSmtpTransport` with
    `ezcMailSmtpTransportOptions`; in that library `tls` means STARTTLS;
  - `lib/ezutils/classes/ezsendmailtransport.php`: the sendmail transport;
  - `kernel/private/classes/ezpmail.php`, `kernel/classes/mail/expmailtemplatebody.php` (bodies from templates);
  - `kernel/classes/mailpreferences/expmailgate.php` (the mail gate) and `expmailbouncereader.php` (bounces, read
    from a mailbox with `ezcMailImapTransport` or `ezcMailPop3Transport` and `ezcMailTools`).
- **Features:** every mail of the site: notifications, registration, password, forms, newsletters through
  cjw_newsletter (which extends `ezcMail` in `CjwNewsletterMailComposer`), the mail preferences and their gate.
- **Fork:** `se7enxweb/expcomponents-mail`, [repository](https://github.com/se7enxweb/expcomponents-mail), replaces
  `zetacomponents/mail`. The repository is new with the official fork; first fork release on 2026-11-06.
  Release 1.10.2 includes upstream's changes after 1.10.1, among them protection against header injection: an
  address with whitespace or one of `< > , ; "` is refused before any header is written, header values, names and
  addresses can no longer add headers, empty header names and blank continuation lines are refused,
  `ezcMailTools::validateEmailAddress()` no longer accepts a trailing newline, and
  Content-IDs are random. `ezpMailMessageIdTest` (MI-04, MI-05) checks the kernel against it.

## mvc-tools

- **Purpose:** request parsing, routing, dispatching, views and response writing for an MVC application.
- **Kernel use:** the whole REST layer in `kernel/private/rest/classes/` (router, routes, request parser, controllers,
  status objects, views, response writer, the authentication styles' interfaces), and the REST endpoints built on it:
  GraphQL (`kernel/classes/graphql/rest/`), help content (`kernel/classes/help/content/rest/`) and migrations
  (`kernel/classes/migration/rest/`). Classes used most: `ezcMvcRequest`, `ezcMvcResult`, `ezcMvcRoutingInformation`,
  `ezcMvcInternalRedirect`, `ezcMvcController`, `ezcMvcResponseWriter`, `ezcMvcHttpResponseWriter`.
- **Features:** [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md) with the ezprestapi extension.
- **Fork:** `se7enxweb/expcomponents-mvc-tools`, [repository](https://github.com/se7enxweb/expcomponents-mvc-tools),
  replaces `zetacomponents/mvc-tools`. The repository is new with the official fork; first fork release on 2026-11-06.

## persistent-object

- **Purpose:** map PHP objects to database tables and fetch, store and delete them (on the database component).
- **Kernel use:** the OAuth server: `kernel/private/oauth/classes/` (`restclient.php`, `restauthorizedclient.php` and
  the persistent object definitions in `persistentobjects/`), the REST tokens and authorization codes
  (`kernel/private/rest/classes/models/`, `po_maps/`, `oauth/utility.php`), and the OAuth modules
  (`kernel/private/modules/oauth/authorize.php`, `kernel/private/modules/oauthadmin/`). Classes used:
  `ezcPersistentObjectDefinition`, `ezcPersistentObjectProperty`, `ezcPersistentObjectIdProperty`. The kernel's own
  `eZPersistentObject` is a different class and does not use it.
- **Fork:** `se7enxweb/expcomponents-persistent-object`,
  [repository](https://github.com/se7enxweb/expcomponents-persistent-object), replaces `zetacomponents/persistent-object`.

## signal-slot

- **Purpose:** signals and slots: objects emit named signals, connected callbacks run.
- **Kernel use:** `kernel/private/classes/ezpcontentpublishingqueue.php`: `ezpContentPublishingQueue` keeps an
  `ezcSignalCollection`, connects the hooks configured for the asynchronous publishing queue and emits `preQueue`
  before a version is queued.
- **Fork:** `se7enxweb/expcomponents-signal-slot`, [repository](https://github.com/se7enxweb/expcomponents-signal-slot),
  replaces `zetacomponents/signal-slot`. The repository is new with the official fork; first fork release on 2026-11-06.

## system-information

- **Purpose:** read system values: CPU type and speed, memory, the operating system, the PHP accelerator.
- **Kernel use:** `kernel/private/classes/views/setup/info.php`: Setup > System information (`/setup/info`) reads
  `ezcSystemInfo::getInstance()`; when the operating system cannot be read (`ezcSystemInfoReaderCantScanOSException`)
  the page shows no system values and a debug notice is written.
- **Fork:** `se7enxweb/expcomponents-system-information`,
  [repository](https://github.com/se7enxweb/expcomponents-system-information), replaces
  `zetacomponents/system-information`. The repository is new with the official fork; first fork release on 2026-11-06.

## webdav

- **Purpose:** a WebDAV server (RFC 2518 and RFC 4918) with pluggable back ends, locking and authentication.
- **Kernel use:** `index_webdav.php` at the installation root runs `ezcWebdavServer` with the content back end
  `kernel/private/classes/webdav/ezwebdavcontentbackend.php` (`eZWebDAVContentBackend extends ezcWebdavSimpleBackend
  implements ezcWebdavLockBackend`), its authentication `ezwebdavcontentbackendauth.php` and the path factory
  `expwebdavpathfactory.php`.
- **Features:** editing content as files over WebDAV ([WebDAV and SOAP](../../help/integrators/webdav-and-soap.md)).
- **Fork:** `se7enxweb/expcomponents-webdav`, [repository](https://github.com/se7enxweb/expcomponents-webdav),
  replaces `zetacomponents/webdav` and `se7enxweb/zetacomponents-webdav`. Required by the kernel since 1.2.2
  (`~1.2.2`, `~1.2.3` since 2026-11-06). Release 1.2.1 was published as `se7enxweb/zetacomponents-webdav` (WebDAV on PHP 8); release 1.2.2, the
  first under the new name, keeps request paths and the destination of COPY and MOVE inside the back end's root, does
  not follow symbolic links out of it, refuses request bodies with a document type declaration, compares digest
  authentication in constant time and creates random lock tokens.

## Forks the kernel does not require

| Fork | Replaces | Note |
|---|---|---|
| `se7enxweb/expcomponents-authentication-database-tiein` ([repository](https://github.com/se7enxweb/expcomponents-authentication-database-tiein)) | `zetacomponents/authentication-database-tiein` | Not required since October 2026; nothing in the kernel or its required packages uses `ezcAuthenticationDatabase*`. Release 1.2.1 (2026-11-06). |
| `se7enxweb/expcomponents-graph` ([repository](https://github.com/se7enxweb/expcomponents-graph)) | `zetacomponents/graph` | Charts; not used by the kernel or a shipped extension. Release 1.7.3 (2026-11-06). |
| `se7enxweb/expcomponents-workflow` ([repository](https://github.com/se7enxweb/expcomponents-workflow)) | `zetacomponents/workflow` | A workflow engine; the kernel has its own (`eZWorkflow`). Release 1.5.2 (2026-11-06). |

An extension that needs one of them requires the fork by its name.

## Upstream packages that are not forked

| Package | Why |
|---|---|
| `zetacomponents/mvc-authentication-tiein` | Not required since October 2026; nothing uses it. |
| `zetacomponents/php-generator` | Was a development requirement; removed in October 2026. |
| `zetacomponents/unit-test`, `zetacomponents/database-schema`, `zetacomponents/coding-standard` | Development dependencies of the forks' own test suites; not installed on a site. |

## Related pages

- [Exponential Components (expcomponents)](../../features/6.0/expcomponents.md)
- [Upgrade notes: the switch to expcomponents](../../bc/6.0/expcomponents.md)
- [Contributing to expcomponents](../../guides/contributing-to-expcomponents.md)
- [APIs and protocols](../../standards/apis-and-protocols.md)
