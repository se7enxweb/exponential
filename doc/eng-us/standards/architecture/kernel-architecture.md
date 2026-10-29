---
title: Kernel architecture
audience: developer, admin
category: standards
order: 200
summary: How a request flows through the Exponential kernel and the rules for each building block: modules, policies, datatypes, persistence, settings, designs, templates, caches, events and the servers.
keywords: architecture, kernel, ezpKernel, ezpKernelWeb, module, view, policy, role, limitation, datatype, eZPersistentObject, eZINI, siteaccess, override, template, operator, fetch, extension, cache, workflow, cronjob, notification, eZDB, ezpEvent, console, Velocity, PHP-FPM, FrankenPHP
type: reference
---
# Kernel architecture

This page is for developers who extend or change Exponential 6.0.x, and for administrators who need to know which
part of the system a setting or a problem belongs to. It answers two questions: what happens between a browser
request and the HTML that comes back, and, for each building block of the kernel, where it lives, how it is
extended and which rule a change has to follow. The rules use **must** for what a change is not allowed to break
and **should** for the expected default. Each section ends with the known exceptions.

The code base was inherited from eZ Publish 4 and keeps its identifiers (`eZINI`, `eZContentObject`,
`eZPersistentObject`); everything added since uses the `exp` prefix (see [Layering and naming rules](layering-and-naming.md)).

## The layers at a glance

```text
 Browser / CLI / cron
        |
 +------v-------------------------------------------------------------+
 | Server: Apache or nginx + PHP-FPM  |  Velocity (qbix)  |  FrankenPHP |
 +------+-------------------------------------------------------------+
        |  index.php  /  bin/php/console  /  runcronjobs.php
 +------v-------------------------------------------------------------+
 | ezpKernel -> ezpKernelWeb  (kernel/private/classes/)               |
 |   settings (eZINI) -> extensions -> siteaccess -> session -> module |
 +------+-------------------------------------------------------------+
        |  eZModule::run()  -> view class  (kernel/<module>/, extensions)
 +------v-------------------------------------------------------------+
 | Domain: kernel/classes  (content, users, roles, datatypes, caches)  |
 |   eZPersistentObject  |  eZDataType  |  eZWorkflowEventType  | ...  |
 +------+-----------------------------+-------------------------------+
        |                             |
 +------v------------+   +-----------v----------------------------------+
 | lib/ezdb (eZDB)   |   | Presentation: lib/eztemplate (eZTemplate),    |
 | lib/ezutils, ...  |   | design/<design>/templates, override.ini       |
 +-------------------+   +----------------------------------------------+
```

## The request lifecycle

What `index.php` does, in order (all paths are in the repository root):

1. **Maintenance and repair hooks.** If `var/maintenance.json` exists, `expMaintenance::check()`
   (`kernel/classes/expmaintenance.php`) may answer the request. A request with `exp_repair` is handed to
   `ezpRepairQueue` (`lib/ezutils/classes/ezprepairqueue.php`).
2. **Autoload.** `autoload.php` loads the generated class maps (`autoload/ezp_kernel.php` and the extension map).
3. **Kernel.** `new ezpKernel( new ezpKernelWeb() )` and `$kernel->run()->getContent()`. `ezpKernel`
   (`kernel/private/classes/ezpkernel.php`) is a thin wrapper around a handler that implements
   `ezpWebBasedKernelHandler` (`kernel/private/interfaces/`). The web handler is `ezpKernelWeb`
   (`kernel/private/classes/ezpkernelweb.php`); it returns an `ezpKernelResult`.
4. **Constructor of `ezpKernelWeb`.** Reads `i18n.ini` and `site.ini`, sets the time zone and file permissions,
   initializes `eZSys`, activates the global extensions (`eZExtension::activateExtensions( 'default' )`), matches
   the siteaccess (`eZSiteAccess::match()` on `eZURI::instance( eZSys::requestURI() )`, unless one is injected),
   switches to it (`eZSiteAccess::change()`), then activates the siteaccess extensions
   (`eZExtension::activateExtensions( 'access' )`) and resets the INI instances so siteaccess settings apply.
5. **`run()` and `dispatchLoop()`.** Session and request initialization (`sessionInit()`, `requestInit()`), the
   events `request/preinput` and `request/input`, URL alias and wildcard translation (`eZURLAliasML`,
   `eZURLWildcard`), the module check (`eZModule::accessAllowed()`), `fetchModule()`, the siteaccess login check
   (`user/login` policy), the view access check (`eZUser::hasAccessToView()`), and finally
   `$module->run( $functionName, $params, ... )`. A view can ask for a rerun with another URI or a redirect
   (`eZModule::STATUS_RERUN`, `STATUS_REDIRECT`); the loop handles both.
6. **Page layout and output.** The module result is placed into the page layout (`pagelayout.tpl` unless the view
   sets another), passed through `eZDisplayResult()` and the output filters `response/preoutput` and
   `response/output` (`kernel/private/classes/global_functions.php`), and returned.
7. **Shutdown.** `shutdown()` writes pending data; under Velocity the worker resets its state for the next request.

Rules:

- A change in the lifecycle **must** keep working under all three servers (PHP-FPM, Velocity, FrankenPHP). Code in
  `ezpKernelWeb` guards newer classes with `class_exists()` because a long-running Velocity worker may have loaded its
  class map before the class existed; new kernel hooks **should** do the same.
- Per-request state **must not** be kept in static properties without a reset, because Velocity reuses the process
  ([worker pool, reset between requests](../../specifications/6.0/velocity-worker-pool.md)).

Command line scripts and cronjobs do not use `ezpKernelWeb`: they boot through `eZScript` (see [The console](#the-console)
and [Cronjobs](#cronjobs)).

## Modules, views and functions

**What it is.** A module is a URL namespace (`content`, `user`, `exphelp`); a view is one page of it
(`content/view`); a function is a permission the views require.

**Where it lives.** `kernel/<module>/module.php` defines `$Module`, `$ViewList` (each view: `script`, `params`,
`functions`, `default_navigation_part`, ...) and `$FunctionList` (policy functions and their limitations). The
repositories searched for modules are `settings/module.ini [ModuleSettings] ModuleRepositories[]` (`kernel`,
`kernel/private/modules`), and only modules in `ModuleList[]` are reachable. Since 6.0.15 the code of a view is a
class: `kernel/content/view.php` is one line that calls `\Exponential\View\Kernel\Content\View::main()` in
`kernel/private/classes/views/content/view.php`, a subclass of `Exponential\Runnable\ModuleView`
(`kernel/private/classes/runnable/moduleview.php`).

**Extension point.** An extension adds modules by listing itself in
`module.ini [ModuleSettings] ExtensionRepositories[]` and shipping `extension/<ext>/modules/<module>/module.php`.
A view class can be replaced through `site.ini [RunnableSettings] Implementation[]`, and a whole kernel module can be
replaced by an extension ([extension module override](../../features/6.0/extension-module-override.md)).

**Rule.**

- Every view **must** name the policy functions it needs in `functions`; a view without them is open to every user
  who passes the siteaccess check.
- New views **should** be written as `ModuleView` subclasses, not as plain script files
  ([Commands, cronjob parts and module views as classes](../../bc/6.0/cli_cronjob_view_abstractions.md),
  [specification](../../specifications/6.0/runnable-commands-cronjobs-views.md)).

**Exceptions.** Plain-file views still run (the kernel keeps that path for extensions). Views listed in
`site.ini [RoleSettings] PolicyOmitList[]` skip the policy check by design.

## Policies, roles and limitations

**What it is.** A role (`eZRole`, `kernel/classes/ezrole.php`) is a set of policies (`eZPolicy`,
`kernel/classes/ezpolicy.php`); a policy grants one module function, optionally restricted by limitations
(`eZPolicyLimitation`, `eZPolicyLimitationValue`). Roles are assigned to users or groups, optionally limited to a
subtree or section.

**Where it lives.** The limitations a function accepts are declared in `$FunctionList` of the module: for example
`kernel/content/module.php` defines `$ClassID = array( 'name' => 'Class', 'class' => 'eZContentClass',
'function' => 'fetchList', ... )` and uses it in `$FunctionList['read']`. The check is
`eZUser::hasAccessTo( $module, $function )` and `eZUser::hasAccessToView()`
(`kernel/classes/datatypes/ezuser/ezuser.php`); templates use `fetch( 'user', 'has_access_to', ... )`
(`kernel/user/function_definition.php`).

**Extension point.** New functions and limitation definitions in an extension's `module.php`; custom content
limitations through limitation handlers ([content limitation handlers](../../features/6.0/content-limitation-handlers.md)).
Kernel code that needs a decision from outside uses `ezpEvent` filters such as `content/edit/access`
(`kernel/classes/ezcontentobject.php`) and `content/download/access`.

**Rule.** Access **must** be decided by policies, never by checking a user or group id in code or templates. A new
module **should** add its function to an installer or update script so existing sites get it
(for example `bin/php/addexphelpreadpolicy.php` for `exphelp/read`). Guide: [Roles and policies](../../guides/roles-and-policies.md).

## Datatypes

**What it is.** A datatype stores, validates, edits and indexes one kind of content attribute (text line, image,
relation list).

**Where it lives.** Subclasses of `eZDataType` (`kernel/classes/ezdatatype.php`) in
`kernel/classes/datatypes/<name>/<name>type.php`; each file registers itself, e.g.
`eZDataType::register( eZStringType::DATA_TYPE_STRING, 'eZStringType' )`. Lookup is configured in
`settings/content.ini [DataTypeSettings]`: `RepositoryDirectories[]=kernel/classes/datatypes`,
`ExtensionDirectories[]` and `AvailableDataTypes[]` (35 entries shipped). The view and edit templates are in
`design/standard/templates/content/datatype/`.

**Extension point.** An extension adds its name to `ExtensionDirectories[]` and its datatype to
`AvailableDataTypes[]` in its own `content.ini.append.php`.

**Rule.** A datatype **must** validate input in `validateObjectAttributeHTTPInput()` and escape output in its
templates; it **must not** change the stored format of existing attributes without an update script.
Background: [datatype input hardening](../../specifications/6.0/datatype-input-hardening.md).

## Persistent objects

**What it is.** The kernel's object-relational layer: one PHP class per table.

**Where it lives.** `eZPersistentObject` (`kernel/classes/ezpersistentobject.php`). A subclass returns its table
description from `static function definition()` (`fields`, `keys`, `increment_key`, `class_name`, `name`,
`function_attributes`); reads use `eZPersistentObject::fetchObject()` and `fetchObjectList()`, writes use
`store()` and `remove()`.

**Rule.** New tables **should** get a persistent class rather than hand-written SQL; where SQL is needed, values
**must** be escaped with `$db->escapeString()` and integers cast. Schema changes **must** reach `share/db_schema.dba`,
the update scripts in `update/database/<driver>/6.0/` and every supported driver (see
[Compatibility, releases and deployment](compatibility-releases-deployment.md)).

## The INI settings cascade

**What it is.** All configuration is INI files read through `eZINI` (`lib/ezutils/classes/ezini.php`).

**Where it lives and in which order.** The defaults are `settings/<file>.ini`. `eZINI::overrideDirs()` then merges
the override directories by scope in this order, a later file winning:

| Order | Scope | Directories |
|---|---|---|
| 1 | `sa-extension` | `extension/<ext>/settings/` of the extensions in `ActiveAccessExtensions[]` |
| 2 | `siteaccess` | per-siteaccess directories inside extensions (`extension/<ext>/settings/siteaccess/<sa>/` and related), then `settings/siteaccess/<sa>/` |
| 3 | `extension` | `extension/<ext>/settings/` of each active extension |
| 4 | `override` | `settings/override/` |

Override files are named `<file>.ini.append.php` (the `.php` suffix keeps them from being served as text).
Extensions are switched on in `site.ini [ExtensionSettings] ActiveExtensions[]` (global) and
`ActiveAccessExtensions[]` (per siteaccess). Note the order: the `settings/` of an extension in
`ActiveExtensions[]` wins over `settings/siteaccess/<sa>/` (one in `ActiveAccessExtensions[]` does not), and `settings/override/` wins over everything. The full reference is
[INI override directories and placements](../../specifications/6.0/ini-override-placements.md); per-site settings in
extensions are described in [multi-site INI overrides](../../features/6.0/multi-site-ini-overrides.md).

**Extension point.** Any extension can ship `settings/*.ini.append.php`; `./console exp:ini` reads and writes every
scope ([exp:ini](../../bc/6.0/console-exp-ini.md)).

**Rule.** Settings **must** be read through `eZINI::instance( '<file>.ini' )`; no kernel, library or command code
parses INI files itself (a search for `parse_ini_file` and `parse_ini_string` in `kernel/`, `lib/` and `bin/` finds
none). A new setting **must** have its default in `settings/<file>.ini` with a comment. Secrets belong in
`settings/override/`, which is never committed. `ActiveExtensions[]` and `ActiveAccessExtensions[]` cannot be set from
inside an extension.

## Siteaccess

**What it is.** A siteaccess is a named configuration (a site, the administration, a language version) selected per
request.

**Where it lives.** `eZSiteAccess` (`kernel/classes/ezsiteaccess.php`) with the match types `TYPE_URI`,
`TYPE_HTTP_HOST`, `TYPE_PORT`, `TYPE_HTTP_HOST_URI`, `TYPE_SERVER_VAR` and others. Configuration in
`site.ini [SiteAccessSettings]`: `MatchOrder` (shipped `uri;host;port`), `AvailableSiteAccessList[]`,
`RelatedSiteAccessList[]`, `HostMatchMapItems[]`, `AnonymousAccessList[]`. Host and language matching:
[siteaccess host and language matching](../../features/6.0/siteaccess-host-and-language-matching.md).

**Rule.** Only siteaccesses in `AvailableSiteAccessList[]` are matched. Code **must** take the current siteaccess
from `eZSiteAccess::current()` rather than parsing the URL itself.

## Design and override system

**What it is.** A design is a directory of templates, stylesheets and images; designs are stacked so a site only
contains what differs from the base.

**Where it lives.** `site.ini [DesignSettings]`: `SiteDesign`, `AdditionalSiteDesignList[]`, `StandardDesign`
(`standard`). Extensions add design directories through `design.ini [ExtensionSettings] DesignExtensions[]`.
`eZTemplateDesignResource` (`kernel/common/eztemplatedesignresource.php`) builds the chain (extension design
directories first, then `design/<design>/`) and resolves overrides from `override.ini` blocks (`Source`,
`MatchFile`, `Subdir`, `Match[...]`). Overrides are ordered by `Priority` (lower first) since July 2026
([template override ordering](../../features/6.0/template-override-ordering.md)). The admin page Design > Templates
writes only `settings/siteaccess/<sa>/override.ini.append.php` (`ezpTemplateOverrides`,
`kernel/private/classes/ezptemplateoverrides.php`).

**Rule.** A site or extension **should** override a template through `override.ini` or its own design directory and
**must not** edit files in `design/standard/` or `design/admin*/` of the kernel. Guide:
[Templates and design](../../guides/templates-and-design.md).

## The template engine, operators and functions

**What it is.** `eZTemplate` (`lib/eztemplate/classes/eztemplate.php`) compiles `.tpl` files to PHP and runs them.

**Where it lives.** Operators and functions are PHP classes registered in `eztemplateautoload.php` files, as
entries of `$eZTemplateOperatorArray` and `$eZTemplateFunctionArray`. The engine loads them from the paths in
`site.ini [TemplateSettings] AutoloadPathList[]` (`lib/eztemplate/classes/`, `kernel/common/`, `lib/ezpdf/classes/`,
`kernel/private/eztemplate/`) and from extensions listed in `ExtensionAutoloadPath[]`
(`extension/<ext>/autoloads/eztemplateautoload.php`). Examples registered in `kernel/common/eztemplateautoload.php`:
`ezurl`, `ezdesign`, `i18n`, `audit_label` (`expAuditTemplateOperator`), `exp_environment`, `csp_nonce`.

**Rule.** An operator **must** escape or return data that the template escapes with `|wash`; HTML built in PHP
**must** be escaped there. Templates **must not** contain SQL; data comes from fetch functions and operators.

## Fetch functions

**What it is.** The `fetch( 'module', 'function', hash( ... ) )` call of templates.

**Where it lives.** `kernel/<module>/function_definition.php` maps each fetch to a `call_method` (for example
`eZContentFunctionCollection` in `kernel/content/function_definition.php`) and declares its parameters.
`eZFunctionHandler` (`lib/ezutils/classes/ezfunctionhandler.php`) executes them; `settings/fetchalias.ini` defines
shortcuts for `fetch_alias()`.

**Rule.** A fetch that returns content **must** apply the user's permissions (the content fetches do, through
`eZContentObjectTreeNode` and the limitation SQL). An extension adds fetches in
`extension/<ext>/modules/<module>/function_definition.php`.

## Extensions

**What it is.** A plugin directory `extension/<name>/` that can carry settings, modules, designs, datatypes,
operators, workflow events, cronjobs, translations and classes.

**Where it lives.** `eZExtension` (`lib/ezutils/classes/ezextension.php`) activates them;
`site.ini [ExtensionSettings] ActiveExtensions[]` and `ActiveAccessExtensions[]` decide which are on and in which
order (dependencies can decide it with `ExtensionOrdering=enabled`; see
[extension loading order](../../features/6.0/extension-loading-order.md)). Identity is in `ezinfo.php` and
`extension.xml`, read by `expInfo` (`lib/ezutils/classes/expinfo.php`;
[extension metadata](../../specifications/6.0/extension-metadata.md)). Classes are found through the extension class
map generated by `php bin/php/ezpgenerateautoloads.php -e`.

**Rule.** An extension **must** carry both metadata files with the same version, **must not** modify kernel files to
work, and **should** add behavior through the extension points on this page. Guide: [Extensions](../../guides/extensions.md).

## Caches

**What it is.** Several caches, each with an id and tags, handled by `eZCache` (`kernel/classes/ezcache.php`).

**Where it lives.** The cache ids in `eZCache` include `content` (view cache), `template` (compiled templates),
`template-block`, `template-override`, `ini`, `global_ini`, `translation`, `design_base`, `active_extensions`,
`classid`, `urlalias`, `imagealias`, `user_info_cache`, `exphttpcache`, `querycache` and more; the tags are
`content`, `template`, `ini`, `i18n`, `image` and `user`. Content view caching is `site.ini [ContentSettings]
ViewCaching` (`enabled`), cleared per node by `eZContentCacheManager` (`kernel/classes/ezcontentcachemanager.php`,
which also emits the `content/cache` event). Cache expiry timestamps are kept in `expiry.php` in the cache directory
(`eZExpiryHandler`, `lib/ezutils/classes/ezexpiryhandler.php`). All cache files are written through the cluster file
handler: `eZClusterFileHandler` (`kernel/classes/ezclusterfilehandler.php`) with `file.ini [ClusteringSettings]
FileHandler=eZFSFileHandler` shipped, or `eZDFSFileHandler` (`kernel/private/classes/clusterfilehandlers/`) for
several web servers.

**Rule.** Cache files **must** be written through `eZClusterFileHandler::instance()`, not with direct file calls, so
clustered installations work. Clearing is done with `bin/php/ezcache.php` or `./console exp:cache`; use
`--clear-tag=ini` for settings ([Caches guide](../../guides/caches.md)).

## Workflows and triggers

**What it is.** Workflows run event types before or after an operation (publish, add to basket, checkout).

**Where it lives.** Event types extend `eZWorkflowEventType` (`kernel/classes/ezworkfloweventtype.php`), found via
`settings/workflow.ini [EventSettings] RepositoryDirectories[]=kernel/classes/workflowtypes`,
`ExtensionDirectories[]` and `AvailableEventTypes[]` (`event_ezapprove`, `event_ezwaituntildate`, ...). Operations
that can be hooked are `[OperationSettings] AvailableOperationList[]`; triggers (`eZTrigger`,
`kernel/classes/eztrigger.php`) connect an operation to a workflow. Deferred workflows run from the `workflow.php`
cronjob part. Guide: [Workflows](../../guides/workflows.md).

## Cronjobs

**Where it lives.** `runcronjobs.php` runs the parts configured in `settings/cronjob.ini`: `[CronjobSettings]
Scripts[]` for the default part and `[CronjobPart-<name>] Scripts[]` (for example `frequent`, `infrequent`), looked up
in `ScriptDirectories[]=cronjobs` and the extensions in `ExtensionDirectories[]`. `eZRunCronjobs`
(`kernel/classes/ezruncronjobs.php`) runs a script; the code of a part is an `Exponential\Runnable\CronjobPart` class
in `kernel/private/classes/cronjobs/`. The console runs a part as `cron:<part>`.

**Rule.** A cronjob part **must** be safe to run twice and to be interrupted. Guide: [Cronjobs](../../guides/cronjobs.md),
[cronjobs from the console](../../features/6.0/cronjobs-console.md).

## Notifications and event handlers

**Where it lives.** `settings/notification.ini`: `[NotificationEventTypeSettings]` (event types such as `ezpublish`,
`eznewcontent`, `ezcollaboration`) and `[NotificationEventHandlerSettings]` (handlers `ezgeneraldigest`,
`ezcollaborationnotification`, `ezsubtree` in `kernel/classes/notification/handler/`), each with
`ExtensionDirectories[]`. Handlers extend `eZNotificationEventHandler`
(`kernel/classes/notification/eznotificationeventhandler.php`); the `notification.php` cronjob part sends the mail.
Specification: [Notifications](../../specifications/6.0/notifications.md).

## The database layer (eZDB)

**Where it lives.** `lib/ezdb/classes/`: `eZDB` (factory, `eZDB::instance()`), `eZDBInterface`, and the drivers
`eZMySQLiDB`, `eZPostgreSQLDB`, `eZSQLite3DB`, `expMongoDB`, `eZNullDB`, plus `eZDBQueryCache`. The driver is
`site.ini [DatabaseSettings] DatabaseImplementation` (shipped `ezmysqli`) resolved through `ImplementationAlias[]`.
Transactions: `Transactions=enabled`. The Oracle driver comes from the separate extension `ezoracle`. Schema files are handled by
`lib/ezdbschema`. Drivers: [database drivers](../../specifications/6.0/database-drivers-2026-09.md).

**Rule.** Kernel code **must** go through `eZDB::instance()` and portable SQL, wrap multi-statement writes in
`begin()`/`commit()`, and **must not** rely on driver-specific features without a fallback for the other drivers.

## Events (ezpEvent)

**What it is.** A small in-process event bus: `ezpEvent::getInstance()->notify( $name, $params )` for notifications
and `filter( $name, $value )` for values listeners may change.

**Where it lives.** `kernel/private/classes/ezpevent.php` (not in `lib/`). Global listeners are configured in
`site.ini [Event] Listeners[]=<event>@<callback>`; the file marks the API as private and subject to change. Events
emitted by the kernel include `request/preinput`, `request/input`, `response/preoutput`, `response/output`,
`content/cache`, `content/cache/all`, `content/class/cache`, `image/removeAliases`, the access filters
`content/edit/access`, `content/download/access`, `collaboration/item/access`, and `runnable/<kind>/before|after`.

**Rule.** Registrations **must** be idempotent: under Velocity a listener attached once per request in a long-lived
worker accumulates. Listener names **should** be `Class::method` strings so they can be configured.

## The console

**Where it lives.** `bin/php/console` (installations usually add a `./console` shortcut in the root). It discovers
commands at run time: `bin/php/<name>.php` becomes `exp:<name>`, `bin/shell/<name>.sh` becomes `shell:<name>`,
`extension/<ext>/bin/php/<name>` becomes `ext:<ext>:<name>`, cronjob parts are `cron:<part>`. A script can declare
extra names with an `@alias` tag in its docblock (`bin/php/exphelp.php` declares `@alias exp:help`). The code of a
kernel command is an `Exponential\Runnable\Command` class in `kernel/private/classes/commands/`.
Reference: [Exponential Console](../../bc/6.0/console.md).

**Rule.** A command **must** keep its `--help` text and exit codes stable (scripts and cron depend on them) and
**should** offer `--dry-run` when it changes data.

## Servers: Velocity, PHP-FPM and FrankenPHP

The kernel runs under three servers, all driven by `./console exp:velocity` and `settings/velocity.ini`
([Velocity engines](../../bc/6.0/velocity-engines.md), [Deploying](../../guides/deploying.md)):

| Server | Engine value | Process model |
|---|---|---|
| Apache or nginx with PHP-FPM | (not an engine) | one request per PHP process run, nothing kept |
| Exponential Velocity | `qbix` (recommended) | a parent loads the application once (warm-up), forks persistent workers that serve many requests and are reset after each; a response cache answers repeat public requests before a worker is used |
| FrankenPHP | `frankenphp` | Caddy with embedded PHP, one binary |

Details: [Velocity web server](../../features/6.0/velocity-web-server.md),
[persistent workers](../../features/6.0/velocity-persistent-worker-server.md),
[worker pool](../../specifications/6.0/velocity-worker-pool.md),
[response cache](../../features/6.0/velocity-response-cache.md), [FrankenPHP](../../bc/6.0/frankenphp.md).

Rules that follow from the persistent model:

- Code **must not** assume a fresh process: no per-request data in statics without reset, no `exit` for flow control
  except through `eZExecution::cleanExit()`.
- A class or INI change reaches Velocity only after a restart (`./console exp:velocity restart` or
  `exp:velocity deploy`), because the workers inherit what the warm-up loaded.
- Cached pages **must** be invalidated through the kernel's cache clear (`eZContentCacheManager`, `eZCache`), and
  Velocity's response cache is cleared with `./console exp:velocity cache clear --apply` after a deploy.

## Known exceptions and debt

- `lib/` is not free of kernel dependencies (examples in [Layering and naming rules](layering-and-naming.md#lib-generic-libraries)).
- `ezpEvent` is documented in `site.ini` as private API, yet extensions rely on it.
- Some views still read `$_POST` and `$_GET` directly instead of `eZHTTPTool` (10 of the 255 view classes in
  `kernel/private/classes/views/` at the time of writing).
- Plain-file views, cronjob parts and commands still run beside the class-based form.

## Related pages

- [Design pattern catalogue](../patterns/README.md): every factory, handler, persistent object, datatype, operator, fetch function, view and plugin point, generated from the code
- [Layering and naming rules](layering-and-naming.md)
- [Compatibility, releases and deployment](compatibility-releases-deployment.md)
- [Templates and design](../../guides/templates-and-design.md), [Extensions](../../guides/extensions.md),
  [Caches](../../guides/caches.md)
