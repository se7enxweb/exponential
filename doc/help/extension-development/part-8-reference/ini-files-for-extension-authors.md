---
title: INI files an extension author meets
audience: developer
category: extension-development
order: 300
summary: Which kernel INI file and section an extension adds to for each purpose - site, module, content, workflow, cronjob, design, override, ezjscore, menu, admininterface, rad - and how append files work.
keywords: INI files, site.ini, module.ini, content.ini, workflow.ini, cronjob.ini, design.ini, override.ini, ezjscore.ini, menu.ini, admininterface.ini, rad.ini, i18n, append.php, extension settings
type: help
---
# INI files an extension author meets

Part 8, reference 26. The kernel's settings live in `settings/*.ini` (about 70 files). An extension never edits them;
it adds `settings/<file>.ini.append.php` files of its own, which the kernel merges in for every active extension
([Settings in an extension](../part-3-extension-developer/settings-in-an-extension.md)). This page lists the files and
sections extensions add to most, by purpose.

## The file format of an extension's settings

```php
<?php /* #?ini charset="utf-8"?

[ModuleSettings]
ExtensionRepositories[]=myext

*/ ?>
```

The PHP comment wrapper keeps the file from being served as text. `Name[]=value` appends to a list; an empty
`Name[]` line first clears it.

## By purpose

| Purpose | File | Section and settings |
|---|---|---|
| Activate extensions | `site.ini` | `[ExtensionSettings] ActiveExtensions[]` (in `settings/override`), `ActiveAccessExtensions[]` (per siteaccess) |
| Extra extension directories | `site.ini` | `[ExtensionSettings] AdditionalExtensionDirectories[]` |
| Event listeners and filters | `site.ini` | `[Event] Listeners[]` |
| Template operators and functions | `site.ini` | `[TemplateSettings] ExtensionAutoloadPath[]` |
| Translations | `site.ini` | `[RegionalSettings] TranslationExtensions[]` |
| Mail | `site.ini` | `[MailSettings]` (the site's; read it, do not set it) |
| Modules | `module.ini` | `[ModuleSettings] ExtensionRepositories[]`, `ModuleList[]` |
| Datatypes | `content.ini` | `[DataTypeSettings] ExtensionDirectories[]`, `AvailableDataTypes[]` |
| Custom tags | `content.ini` | `[CustomTagSettings] AvailableCustomTags[]` and a section per tag |
| Workflow event types | `workflow.ini` | `[EventSettings] ExtensionDirectories[]`, `AvailableEventTypes[]` |
| Cronjob parts | `cronjob.ini` | `[CronjobSettings] ExtensionDirectories[]`, `[CronjobPart-<name>] Scripts[]` |
| Designs | `design.ini` | `[ExtensionSettings] DesignExtensions[]` |
| Stylesheets and scripts | `design.ini` | `[StylesheetSettings] FrontendCSSFileList[]`, `BackendCSSFileList[]`; `[JavaScriptSettings] FrontendJavaScriptList[]`, `BackendJavaScriptList[]` |
| Template overrides | `override.ini` | one section per rule (`Source`, `MatchFile`, `Subdir`, `Match[...]`) |
| Ajax server functions | `ezjscore.ini` | `[ezjscServer_<name>]` |
| Admin menus | `menu.ini` | `[Leftmenu_<name>] Links[]`, `LinkNames[]`, `PolicyList_<key>[]` |
| Context menus and node tabs | `admininterface.ini` | `[AdditionalMenuSettings] ContextMenuTemplateArray[]` ...; `[WindowControlsSettings] AdditionalTabs[]` and `[AdditionalTab_<id>]` |
| Registries of the RAD catalogue | `rad.ini` | `[RegistrySettings]` and `[Registry_<name>]` |
| Fetch aliases | `fetchalias.ini` | a section per alias |
| Help pages | `exphelp.ini` | the extension's documentation folder ([The help system for extensions](help-system-for-extensions.md)) |
| Your own settings | `myext.ini` | in `settings/myext.ini`, your own sections |

## Other files you may read

| File | Holds |
|---|---|
| `image.ini` | image aliases (sizes) and the image handler |
| `file.ini` | the cluster file handler |
| `viewcache.ini` | which views are cleared on publish |
| `i18n.ini` | character set and translation settings |
| `template.ini` | template engine settings (wash, auto-link) |
| `debug.ini` | debug output conditions |
| `rest.ini` | the REST interface's providers and filters |
| `velocity.ini` | the application server, when used |

## Rules

- Your own settings go in your own file, never into a kernel file's sections you do not need.
- Keys and section names are case-sensitive.
- After changing settings, clear the INI cache with `--clear-tag=ini`.
- Never ship `settings/override` files or passwords in an extension.

## Related pages

- Previous: [Built-in fetch functions by module](fetch-functions-reference.md)
- Next: [Console commands for developers](console-commands-for-developers.md)
- [eZINI: reading settings](ezini.md)
- [How INI settings work](../part-2-builder/how-ini-settings-work.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
