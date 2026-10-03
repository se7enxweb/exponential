# Behaviour changes of July and August 2026 (6.0.15 development)

This is the upgrade checklist for the changes made to the root repository between
1 July and 31 August 2026. The release 6.0.15 had no tag then; everything below is
part of the 6.0.15 line. For September and October see
[Behaviour changes of October 2026](behaviour-changes-2026-10.md). The month
stories are [July](../../history/2026/2026-07.md) and [August](../../history/2026/2026-08.md).

## Quick checklist

1. Make sure every user account has an `ezuser_setting` row
   ([why](../../specifications/6.0/security-hardening-2026-08.md)).
2. If you use the extensions `bciconextensions` or `bciconextensions_share_icons`, remove them
   from `ActiveExtensions[]`.
3. If you have templates that rely on XHTML self closing tags or `type="text/css"`, see "HTML5 markup".
4. Review your `override.ini` files if some blocks have a `Priority` and others not.
5. Run `php bin/php/ezpgenerateautoloads.php -e` and clear all caches.

## Changes that can break something

### Accounts without an `ezuser_setting` row cannot sign in (16 August)

`kernel/classes/datatypes/ezuser/ezuser.php` treated a user with no settings row
as enabled. It now treats it as disabled. Accounts made in the admin always have
the row; accounts made by SQL, packages or migrations may not. Find and repair
them with the queries in the
[security specification](../../specifications/6.0/security-hardening-2026-08.md#f-06-find-accounts-that-now-cannot-sign-in).

### Override order follows `Priority` (12 July)

The template engine now sorts override blocks by the key `Priority` of
`override.ini`: the lowest number is checked first and a block without `Priority`
comes last. A block created in the [template editor](../../features/6.0/template-editor-overrides.md)
gets `Priority=0`. If you mix hand written blocks without `Priority` with editor
blocks, the editor's blocks now win. Add `Priority` to the hand written blocks to
set the order you want.

### HTML5 markup in the kernel designs (18 July)

Templates of `design/standard` and the markup written by PHP in `lib` and `kernel` (the `nl2br` template operator, the debug and statistics output, the kernel web entry point), and in
`ezjscore` and `ezoe`, were changed so the output passes HTML validators: `<br />` became `<br>`,
`<input ... />` became `<input ...>`, and `<style type="text/css">` became
`<style>`. Browsers treat both forms the same. Check only if you compare markup
byte for byte (snapshot tests) or post process it as XML. Your own overrides are
not changed by the kernel.

### jQuery 3.7.1 in ezjscore (16 August)

Pages that load `ezjsc::jquery` get jQuery 3.7.1 and jQuery Migrate 3.4.1 (muted).
Code that still needs jQuery 1.x behaviour usually works through Migrate. This
was superseded in October by jQuery 4, see the
[6.0.15 changelog](../../changelogs/6.0/6.0.15.md).

### Kickstart and the setup wizard

- `kickstart.ini` is no longer shipped active; the example is `kickstart.ini--example`,
  the template is `kickstart.ini-dist`. If you relied on a shipped `kickstart.ini`, copy the
  example to `kickstart.ini` and edit it. See [Kickstarter](../../features/6.0/kickstarter-cli.md).
- A kickstart install always creates the siteaccesses `site` and `admin`.
- A new installation defaults to `eng-US` and the Exponential content of
  `share/db_data.dba`; existing installations are not touched.
  See [Clean install defaults](../../features/6.0/clean-install-defaults.md).

### The autoload generator

`kernel/private/classes/ezautoloadgenerator.php` compares real paths and its
exclusion filter (`var`, `settings`, `bin`, `autoload`, `tmp`, `lib/ezc` ...) now ends
at a directory boundary: a folder called `settings_backup` is no longer skipped by
the filter `settings`. The "Scan complete" log line is gone, so the generator stays as quiet as designed.
If a class went missing after you renamed or symlinked extension folders, run
`php bin/php/ezpgenerateautoloads.php -e` again.

## New settings

| File | Block | Key | Default | Scope | Meaning |
|---|---|---|---|---|---|
| `site.ini` | `[TemplateSettings]` | `ShowTemplatePathComments` | `disabled` | global/siteaccess | START/STOP comments naming each template. [Feature](../../features/6.0/template-path-comments.md). |
| `site.ini` | `[ExtensionSettings]` | `AdditionalExtensionDirectories[]` | empty | global | More extension roots. [Feature](../../features/6.0/additional-extension-directories.md). |
| `site.ini` | `[SiteSettings]` | `EzInstallationName`, `ProductionInstallationList[]` | not set | global | Installation name filter. [Feature](../../features/6.0/installation-name-in-pages.md). |
| `ezscript.ini` | `[eZScriptSettings]` | `RootDelay` | `10` | global | Seconds a script run as root with `--allow-root-user` waits so you can press Ctrl-C. `0` removes the delay. |
| `icon.ini` | `[ExtensionSettings]` | `IconExtensions[]` | empty | global/siteaccess | Extensions whose `icons/` folder is searched. [Feature](../../features/6.0/icon-themes-in-extensions.md). |
| `menu.ini` | `[Leftmenu_design]` | `Links[template_editor]` | `visual/templatelist` | global | Template Editor in the Design menu. |
| `transform.ini` | `[search]` | `Commands[]=lowercase` | added | global | The lowercase transformation in the search group. |

Example for the root delay in an automated deployment:

```ini
# settings/override/ezscript.ini.append.php
[eZScriptSettings]
RootDelay=0
```

## Fixes you may notice

- **Console output**: every `bin/php/console` entry shows a description; `ezpm` is a
  console command ([ezpm](../../features/6.0/ezpm-package-manager-cli.md)).
- **PHP 7.4 to 8.5**: string offsets with curly braces (ISBN13 datatype, WebDAV backend),
  `#[AllowDynamicProperties]` on `eZINI`, `eZPersistentObject` and `eZWorkflowProcess`
  (PHP 8.2), nullable array type hints (`generateUrl`, `fetchRelatedObjects`),
  an integer cast for the next order number (PHP 8.4), the removed `imagedestroy()` call (PHP 8.5),
  guards for null values in `eZSiteAccess::load`, `eZCharsetInfo`, `eZMail`, the
  GD colour functions, `eZLocale`, the content cache manager, XHTML output of embedded images
  and URL alias caches.
- **INI files**: saving in round trip mode leaves every untouched key as it was on
  disk, including bare `Key[]` reset lines. Extension activation and package install
  no longer reformat unrelated settings (16 August, `0a0f4f876b`). See
  [eZINI preserves comments](eZINI_PRESERVES_COMMENTS.md).
- **Debug handler** honours `error_reporting` (`3ce2a1e76c`); the eng-US translation file is
  not looked up, which removes "missing translation" noise (`e3c8d078f4`).
- **Permissions SQL**: an empty class or section limitation list no longer produces
  invalid SQL (`acb8e002c5`).
- **Imported Ibexa docbook**: `ezembed`, `para` with a CSS class and `literallayout`
  render in XHTML; the class whitelist is enforced only for tags of the eZXML schema
  (`5857c5baa8`, `d7bebcb39b`).
- **Image aliases**: a missing source file is a notice that names the object, the
  attribute, the missing alias and the existing aliases, not a fatal error (`52277db85f`).
- **Object not found** errors name the id and the file and line of the caller (`c7a5eac571`).
- **SQLite**: connection debug messages are readable and only appear with SQL output on;
  the schema generator emits `INTEGER PRIMARY KEY AUTOINCREMENT` and no second primary key
  constraint (`dfba7274e8`, `24c03393db`, `9678728ccc`).
- **MongoDB**: the driver reports errors through `eZDebug::writeError()` with the prefix
  `expMongoDB`, not `error_log` (`888297a0ca`).
- **Package tools**: see [ezpm](../../features/6.0/ezpm-package-manager-cli.md) for the install
  lock, delayed indexing and batched commits.

## Smaller changes, explained

Each of these has a commit in the [ledger](../../history/ledger/exponential-root.md) and a small, visible effect.

| Change | What you see | What to do |
|---|---|---|
| Script descriptions in the console (`4ce57a7224`) | `./console list` shows one line per script. The console reads it from the `'description'` key of the array given to `eZScript::instance()` (strategy 1 in the comment block of `bin/php/console`), or from an `@description` docblock tag. | Give your own scripts a `'description'` key (or `// @description ...`) and they appear in the list. Check: `./console list ezpm`. |
| `RootDelay` (`11095f902f`) | A script run as the operating system root user with `--allow-root-user` prints "With great power comes great responsibility." and waits `RootDelay` seconds so you can press Ctrl-C (`kernel/classes/ezscript.php`). | `0` in `ezscript.ini [eZScriptSettings]` removes the wait in deployments; default `10`. |
| Layouts tab in the admin node view (`8df2654e3f`) | The window controls of `design/admin3` take an extra tab beside Ordering; `design/admin/javascript/node_tabs.js` marks the window controls with the class `layouts-active` while the tab `node-tab-layouts` is open. The tab itself is supplied by the layouts extensions. | Nothing; see [Exponential Layouts](LAYOUTS.md). |
| Nice URL update script revert (`9c4946075d`) | `bin/php/updateniceurls.php` is back to its earlier, stable version after a change made during testing proved unstable. | Nothing. |
| Autoload generator (`f46c08c18e`, `62c14b36dd`) | Real paths are compared and the exclusion filter stops at a directory boundary; the "Scan complete" line is gone. | Described above under "The autoload generator". |
| Media theme templates (`c013093b52`) | Templates of the media theme guard `false` and missing values; the Google Tag Manager template parses. | Nothing; the theme is an ordinary directory of the repository since 6 August. |

## Composer and extensions

The root `composer.json` changed as follows (suggestions are only listed in the
`suggest` block and never installed):

| Date | Change |
|---|---|
| 12 July | `git_manager ~2.0.1` required |
| 19 July | suggestions for `owsimpleoperator`, `adminaid`, the private message extension, `hcaptcha`, `syndication` |
| 20 July | `sevenx_dse ~1.0.0` and `syndication ~1.2.0` required; `sevenx_valkey_cache` suggested |
| 21 July | `powercontent` required |
| 30 July | the explayouts packages (12), the expsite packages, `expquery-translator` and `sevenx-themes-media`, all `~1.0.0`, declared as runtime requirements |
| 31 July | `powercontent ~2.0.0`; `exppayment-stripe` removed from the requirements |
| 15 August | `exp_enhanced_link ~1.0.1` (the media theme needs it) |
| 22 August | `eztags ~2.3.3` and `cjw_newsletter ~4.0.0.0` required |
| 31 August | `ngclasslist ~1.1` required (the datatype of the `ng_menu_item` class) |

The table is the history of what changed in July and August. The constraints have moved on since (checked in the
`composer.json` of 2 October 2026, for example `git_manager ~2.0.14`, `eztags ~2.4.11`, `cjw_newsletter ~4.1.16`); list
the current ones with `grep -n '"se7enxweb/' composer.json`.

After pulling these changes, run Composer yourself in the way you always do, and
then `php bin/php/ezpgenerateautoloads.php -e`.

## README note

The README header explains that a downloaded copy of Exponential 6.0 needs
`composer install` before it runs (`04d17780ae`, `84ebe2f59f`).

## Related

- [Security specification](../../specifications/6.0/security-hardening-2026-08.md), [CI specification](../../specifications/6.0/continuous-integration.md)
- [Console](console.md), [php8](php8.md)
- Features of the period: [ezpm](../../features/6.0/ezpm-package-manager-cli.md), [Template editor](../../features/6.0/template-editor-overrides.md), [Template path comments](../../features/6.0/template-path-comments.md), [Kickstarter](../../features/6.0/kickstarter-cli.md), [Clean install defaults](../../features/6.0/clean-install-defaults.md), [Reset a user password](../../features/6.0/reset-user-password.md), [Redis and Valkey caches](../../features/6.0/valkey-cache-hooks.md), [Extension list](../../features/6.0/extension-list-and-downloads.md), [Additional extension directories](../../features/6.0/additional-extension-directories.md), [Icon themes](../../features/6.0/icon-themes-in-extensions.md), [Installation name](../../features/6.0/installation-name-in-pages.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
