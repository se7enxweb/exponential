---
title: Layering and naming rules
audience: developer, admin
category: standards
order: 210
summary: Which layer of Exponential may depend on which, what each layer is for, and how new classes, modules, settings, templates, CSS classes, tables, translation contexts and commands are named.
keywords: layering, layers, lib, kernel, kernel/private, design, extension, naming, conventions, exp prefix, eZ, ezp, eZINI, eZHTTPTool, templates, fetch, inline CSS, CSP, nonce, AdminStylingRulesTest, translation context, console command
type: reference
---
# Layering and naming rules

This page is for developers who add code to Exponential 6.0.x or to an extension, and for reviewers who check such
changes. It answers where a piece of code belongs, which layers it may call, and what to name it. Each rule says
**must** (a review rejects the change) or **should** (the expected default), shows how the code base implements it,
and lists the places that do not follow it yet. The counts on this page were taken from the repository in October
2026; they are evidence, not limits.

## The layers

```text
 extension/<name>/          plugins: settings, modules, designs, classes, cronjobs
        |  may use everything below, through the documented extension points
 design/<design>/templates  presentation (.tpl), stylesheets, javascript
        |  reads data only through fetch functions and template operators
 kernel/<module>/           controllers: module.php, view entry files, function_definition.php
        |
 kernel/private/            internal API: ezpKernel, views, commands, cronjobs, services, REST, ezpEvent
        |
 kernel/classes/            domain: content, users, roles, datatypes, workflows, caches, persistence
        |
 lib/                       generic libraries: eZDB, eZINI, eZTemplate, eZFile, eZSys, eZModule, sessions
```

A layer may call the layers below it. Calls upward are the exceptions listed per layer.

### lib/: generic libraries

**Purpose.** Infrastructure that does not know about content: `lib/ezdb` (database drivers), `lib/ezdbschema`,
`lib/ezutils` (`eZINI`, `eZSys`, `eZModule`, `eZExtension`, `eZHTTPTool`, `eZFunctionHandler`, `eZDebug`),
`lib/eztemplate` (`eZTemplate` and the base operators), `lib/ezfile`, `lib/ezi18n`, `lib/ezimage`, `lib/ezlocale`,
`lib/ezsession`, `lib/ezsoap`, `lib/ezpdf`, `lib/ezdiff`, `lib/ezmath`.

**Rule.** New code in `lib/` **should not** depend on `kernel/` classes. It **must not** depend on a module or a
design.

**Status of the rule: not met everywhere.** The libraries were written together with the kernel and several call
it. Examples found in the code:

| File in `lib/` | Kernel dependency |
|---|---|
| `lib/eztemplate/classes/eztemplateroleoperator.php` | `eZUser::currentUserID()`, `instanceof eZRole` |
| `lib/eztemplate/classes/eztemplatecacheblock.php` | `eZClusterFileHandler::instance()` (`kernel/classes/ezclusterfilehandler.php`) |
| `lib/ezutils/classes/ezexpiryhandler.php` | `eZClusterFileHandler::instance()` |
| `lib/ezutils/classes/ezdebug.php` | `eZUser::currentUserID()` |
| `lib/ezutils/classes/ezoperationmemento.php`, `ezhttppersistence.php` | `eZPersistentObject` (`kernel/classes/ezpersistentobject.php`) |
| `lib/ezdb/classes/expmongodb.php`, `lib/ezdbschema/classes/expmongoschema.php` | `eZPersistentObject`, `eZUser` |

In total 8 files in `lib/` name `eZUser`, 8 name `eZClusterFileHandler` and 4 name `eZPersistentObject`. Treat
`lib/` as "lower level than the kernel", not as an independent library set. A change **should not** add new
dependencies of this kind; moving the existing ones is open debt.

### kernel/classes/: the domain

**Purpose.** The content model and everything around it: `eZContentObject`, `eZContentObjectTreeNode`,
`eZContentClass`, `eZRole`, `eZPolicy`, `eZPersistentObject`, the datatypes in `kernel/classes/datatypes/`, the
workflow types in `kernel/classes/workflowtypes/`, notification handlers, caches (`eZCache`,
`eZContentCacheManager`), cluster file handlers, and newer subsystems in subdirectories (for example
`kernel/classes/audit/`).

**Rule.** Domain classes **must not** render pages or read request input; they take values as parameters and return
objects or arrays. They **should** be reachable from a view, a command and a cronjob alike.

### `kernel/<module>/`: controllers

**Purpose.** `module.php` (views and policy functions), `function_definition.php` (template fetches), the view entry
files and, for older modules, the view code itself.

**Rule.** A view reads input through `eZHTTPTool` (`eZHTTPTool::instance()`, `hasPostVariable()`,
`postVariable()`), relies on the form token check of the `ezformtoken` extension for POST requests, calls the domain layer, and renders through
`eZTemplate::factory()`. It **must not** print HTML itself except for downloads and JSON responses.

**Exceptions.** 185 of the 255 view classes in `kernel/private/classes/views/` use `eZHTTPTool::instance()`; 10 still
read `$_POST` or `$_GET` directly (for example `kernel/private/classes/views/content/multiedit.php`,
`contentcheck/fixfiles.php`, `contentfinder/action.php`).

### kernel/private/: internal API

**Purpose.** Code that is not a public extension API, or that the kernel owns end to end: `ezpKernel` and
`ezpKernelWeb`, `ezpEvent`, the class-based views, commands and cronjob parts
(`kernel/private/classes/{views,commands,cronjobs}/`, namespaces `Exponential\View\Kernel`,
`Exponential\Command\Kernel`, `Exponential\Cronjob\Kernel`), shared services (`kernel/private/classes/services/`,
namespace `Exponential\Service`), the REST layer (`kernel/private/rest/`, `kernel/private/api/`, OAuth in
`kernel/private/oauth/` and the modules `oauth` and `oauthadmin` in `kernel/private/modules/`), the cluster file
handler `eZDFSFileHandler` and kernel interfaces (`kernel/private/interfaces/`).

**Rule.** Extensions **should** use the documented extension points (settings, `[RunnableSettings]`,
`[Event] Listeners[]`) rather than call `kernel/private` classes directly; their signatures may change between
releases. Work shared by a view, a command and a cronjob part goes into a service class, not into one of them
([Commands, cronjob parts and module views as classes](../../bc/6.0/cli_cronjob_view_abstractions.md)).

### design/: presentation

**Purpose.** Templates, stylesheets, scripts and images per design: `design/standard`, `design/admin`,
`design/admin3`, `design/admin4`, `design/admin4l`, `design/base`, `design/editor`, `design/plain` and others.

**Rules.**

- **Templates do not query the database.** The template language has no SQL construct. Data comes from fetch
  functions (`kernel/<module>/function_definition.php`, run by `eZFunctionHandler`) and template operators, which
  apply permissions in PHP. A template **must not** receive a raw database handle. Exception in practice: an operator
  is PHP and can query, so operators are reviewed like views (`eztemplateroleoperator.php` reads roles through
  `eZRole`).
- **No inline CSS in administration templates.** No `<style>` element and no `style=""` attribute in the templates
  of `admin`, `admin3`, `admin4` and `admin4l`; all CSS lives in the design's `stylesheets/` directory and is loaded
  through the stylesheet settings so the packer bundles it. Colors, radii, spacing and fonts come from the design
  tokens (`--a4-*`), and every color token has a dark-mode value. Enforced by
  `tests/tests/design/AdminStylingRulesTest.php` (DB-less): `ALLOWLIST` is empty, and the single permanent entry in
  `EXCEPTIONS` is `design/admin/templates/pagelayout.tpl` (column widths applied before the page is drawn). The test
  also checks that `stylesheets/modules/*.css` use only tokens and that `components.css` is the same in `admin` and
  `admin4`. Reference: [Styling the administration](../../bc/6.0/admin4-styling.md).
- **No inline script in the administration.** The administration runs under a Content Security Policy without
  `'unsafe-inline'` (`site.ini [ContentSecurityPolicy]`; report-only on the administration by default). Templates
  rendered by `admin4` and `admin4l` carry no inline `<script>` with code, no `on...=` attribute and no
  `javascript:` URL; enforced by `tests/tests/design/AdminContentSecurityPolicyTest.php` and, for the shipped
  extensions, `tests/tests/design/ExtensionInlineScriptsTest.php`. Script code goes in `design/<design>/javascript/`
  (for example `design/admin4/javascript/admin4.js`).
- **Nonce for the scripts that must stay inline.** With `[ContentSecurityPolicy] Nonce=enabled`, each request gets a
  nonce added to the directives of `NonceDirectives[]` (`script-src` shipped); a template prints it with the operator
  `csp_nonce()` (`expContentSecurityPolicyOperator`, registered in `kernel/common/eztemplateautoload.php`):
  `<script nonce="{csp_nonce()}">`. See [Content Security Policy](../../features/6.0/content-security-policy.md).
- **Output is escaped.** Every variable from content or input is printed with `|wash` (or an operator that escapes).

### extension/: plugins

**Purpose.** Everything that is not the kernel: `extension/<name>/` with `ezinfo.php`, `extension.xml`,
`settings/`, `modules/`, `design/`, `classes/`, `autoloads/`, `cronjobs/`, `translations/`, `bin/php/`.

**Rule.** An extension **must not** require changes to kernel files. It is switched on in
`site.ini [ExtensionSettings] ActiveExtensions[]` or `ActiveAccessExtensions[]`, and its classes are found after
`php bin/php/ezpgenerateautoloads.php -e`. Extension class-based code uses the namespaces
`Exponential\Command\Extension\<Ext>`, `Exponential\Cronjob\Extension\<Ext>` and
`Exponential\View\Extension\<Ext>\<Module>`.

## Cross-cutting rules

- **Settings are read through `eZINI` only.** `eZINI::instance( 'file.ini' )->variable( 'Section', 'Setting' )`.
  No code in `kernel/`, `lib/` or `bin/` calls `parse_ini_file()` or `parse_ini_string()`. Settings are written with
  `./console exp:ini` or `eZINI::save()`, never by string replacement in a script
  ([exp:ini](../../bc/6.0/console-exp-ini.md)).
- **Debug output through `eZDebug`.** `eZDebug::writeError()`, `writeWarning()`, `writeNotice()`; no `echo` or
  `var_dump` left in kernel code.
- **Translations through `ezpI18n::tr( $context, $text )`** in PHP and `i18n( $context )` in templates.
- **Persistent workers.** Code must assume it can run in a long-lived Velocity worker (see
  [Kernel architecture](kernel-architecture.md#servers-velocity-php-fpm-and-frankenphp)).

## Naming conventions

### The exp prefix for new code, identifiers kept

The product is Exponential; the code base inherited its identifiers from eZ Publish 4. The rules:

- **New** classes, modules, template operators, templates, settings files and translation contexts **should** use
  `exp` / `Exp`: `expAudit` (`kernel/classes/audit/expaudit.php`), `expContentSecurityPolicy`
  (`kernel/private/classes/expcontentsecuritypolicy.php`), `expInfo` (`lib/ezutils/classes/expinfo.php`),
  `expMongoDB`, the `exphelp` module and `settings/exphelp.ini`. There are more than 330 `exp`-prefixed class
  declarations in `kernel/` and `lib/`.
- **Existing** `eZ*` and `ezp*` identifiers (`eZContentObject`, `eZINI`, `ezpKernel`), table names, file paths and
  setting names **must never be renamed**: extensions and sites depend on them. The 2026 rebranding changed only text
  that people read ([The product is called Exponential](../../features/6.0/rebranding-to-exponential.md)).
- Text for people (UI strings, documentation, commit messages) uses the name Exponential.

**Exceptions (added before the rule was applied consistently).** Kernel modules added in 2026 without the prefix:
`apikey`, `audit`, `contentcheck`, `contentfinder`, `csp`, `mailpreferences`, `uiswitch` (only `exphelp` has it).
Template operators `csp_nonce` and `audit_label` (while `exp_environment` has it). The class `ezpRepairQueue`
(`lib/ezutils/classes/ezprepairqueue.php`) uses the old `ezp` prefix. These stay as they are: renaming them now would
itself be a break.

### Classes and files

- One class per file; the file name is the class name in lower case: `expContentSecurityPolicy` in
  `expcontentsecuritypolicy.php`, `ezpKernelWeb` in `ezpkernelweb.php`.
- Namespaced runnable classes follow the path instead: `Exponential\View\Kernel\Content\View` in
  `kernel/private/classes/views/content/view.php`.
- After adding or renaming a class, regenerate the class maps (`php bin/php/ezpgenerateautoloads.php -e` for
  extensions, `-k` for the kernel with `.autoloadignore` or `--exclude` for working copies inside the root).

### Extensions

Lower-case directory names under `extension/`. New extensions **should** start with `exp`; the extensions shipped in
the repository keep their names: `ezjscore`, `ezoe` and `ezformtoken` the historical ones, `expservices` already the new prefix. The `Info_url` and
`<info_url>` point at the extension's public repository ([extension metadata](../../specifications/6.0/extension-metadata.md)).

### INI files, sections and settings

- File names lower case: `settings/<name>.ini`; overrides `<name>.ini.append.php`.
- Sections in CamelCase, usually ending in `Settings`: `[SiteAccessSettings]`, `[DataTypeSettings]`,
  `[HelpSettings]`, `[DeploySettings]`; dynamic sections use a dash: `[CronjobPart-frequent]`.
- Settings in CamelCase: `MatchOrder`, `AvailableDataTypes[]`, `ExtensionDirectories[]`; arrays end in `[]`
  (`List[]`), hashes use `Name[key]=value`.
- Switches use `enabled`/`disabled` (`ViewCaching=enabled`); some older settings use `true`/`false`
  (`ForceVirtualHost=false`). A new setting **should** use `enabled`/`disabled`.
- Every new setting gets a comment above it in the shipped file and a safe default.

### Templates

- `design/<design>/templates/<module>/<view>.tpl` for module views (`design/admin4/templates/exphelp/viewer.tpl`).
- Reusable pieces in a `parts/` subdirectory (`design/admin4/templates/exphelp/parts/`); page-wide pieces as
  `page_<name>.tpl` (`page_header.tpl`, `page_mainarea.tpl`).
- Datatype templates under `content/datatype/view/` and `content/datatype/edit/` named after the datatype string.
- Overrides under `override/templates/`, mapped in `override.ini`.

### CSS and JavaScript

- Administration CSS classes use the design prefix: `.a4-*` in `admin4` (`.a4-card`, `.a4-btn`, `.a4-table`), one
  extra segment for a module (`.a4-help-*` in `design/admin4/stylesheets/modules/help.css` and `admin4.css`). No
  per-module prefixes outside the design prefix.
- Tokens are CSS custom properties `--a4-*`; module CSS lives in `design/admin4/stylesheets/modules/<group>.css`
  (`content`, `help`, `people`, `setup`, `structure`, `workflow`).
- Script files are named after their purpose in lower case with `_` or `-` (`a4_mega_menu.js`, `exphelp.js`).

### Database tables

Kernel tables keep the `ez` prefix (118 of the tables in `share/db_schema.dba`). Tables added in 2026 use `exp`:
`expaudit_cursor`, `expaudit_event`, `expaudit_file`, `expmail_category`, `expmail_consent_log`, `expmail_pending`,
`expmail_preference`, `expmail_suppression`, `expbookmark_folder`, `expapikey`. Names are lower case with `_`.
Columns added to existing `ez` tables keep the table's style (for example `ezcontentobject_trash.trashed_by`).

### Translation contexts

Contexts are the path of the code that uses them: `kernel/<module>` for PHP views
(`ezpI18n::tr( 'kernel/exphelp', 'Search' )`) and `design/<design>/<module>/<view>` for templates. The translation
files are `share/translations/<locale>/translation.ts` (plus `share/translations/untranslated/`); new strings
**must** be added to the context the code uses so `exp:i18n` and the translation tests find them.

### Console commands

- `bin/php/<name>.php` is `exp:<name>` automatically; a script adds readable names with `@alias` in its docblock,
  using `:` between words: `exp:help` (`bin/php/exphelp.php`), `exp:help:content`, `exp:access:check`,
  `exp:mail:status`. Velocity is `exp:velocity` with subcommands (`exp:velocity deploy`, `exp:velocity cache clear`).
- Extension scripts are `ext:<ext>:<name>`; cronjob parts `cron:<part>`.
- Command names, options and exit codes are part of the compatibility promise
  ([Compatibility, releases and deployment](compatibility-releases-deployment.md)).

## Known gaps

- `lib/` depends on kernel classes in at least the files listed above.
- Seven 2026 kernel modules and two operators do not carry the `exp` prefix.
- Ten view classes read superglobals directly.
- INI switches mix `enabled`/`disabled` and `true`/`false`.
- The rules for designs are enforced by tests for the administration designs only; site designs are not checked for
  inline CSS.

## Related pages

- [Kernel architecture](kernel-architecture.md)
- [Design pattern catalogue](../patterns/README.md)
- [Compatibility, releases and deployment](compatibility-releases-deployment.md)
- [Styling the administration](../../bc/6.0/admin4-styling.md), [Content Security Policy](../../features/6.0/content-security-policy.md)
