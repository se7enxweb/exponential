---
title: Debug output and logs for developers
audience: user, developer, designer, app-creator
category: extension-development
order: 190
summary: See what the system does while it builds a page: the Exp Debug bar and its settings, used templates, path comments, the log files under var/log, and your own debug lines and logs from PHP.
keywords: debug, DebugOutput, debug bar, DebugByIP, ShowUsedTemplates, ShowTemplatePathComments, error.log, warning.log, deprecated.log, eZDebug, eZLog, log viewer, troubleshooting
type: help
---
# Debug output and logs for developers

Part 1, chapter 19. When an extension does not do what you expect, the system almost always says why, if you know
where to look. This chapter shows the three places: the debug output under each page, the log files, and the lines you
write yourself.

## The debug output: the Exp Debug bar

With `[DebugSettings] DebugOutput=enabled` in `site.ini`, every page carries a report of how it was made. In
Exponential 6 this report is the **Exp Debug bar**: a bar pinned to the page with a live summary of the request, and a
panel with tabs for the sections of the report (messages, timing, templates, SQL and more). Its **Settings** tab shows
every debug setting, the value in effect and the file it comes from, and can change it in a scope you choose; each
change goes through the same engine as `exp:ini`, is logged, and can be undone from the bar. The details are in
[The Exp Debug bar](../../../bc/6.0/debug-bar.md).

### The settings

All in `site.ini`:

| Block | Setting | Default | Meaning |
|---|---|---|---|
| `[DebugSettings]` | `DebugOutput` | `disabled` | Show the debug report at all |
| `[DebugSettings]` | `DebugByIP` + `DebugIPList[]` | `disabled` | Show it only to these addresses (CIDR ranges allowed) |
| `[DebugSettings]` | `DebugByUser` + `DebugUserIDList[]` | `disabled` | Show it only to these user IDs |
| `[DebugSettings]` | `DebugRedirection` | `disabled` | Stop before a redirect so you can read the report of the page that redirected |
| `[DebugSettings]` | `ScriptDebugOutput` | `disabled` | Debug output for command line scripts too |
| `[DebugSettings]` | `AlwaysLog[]` | `error` | Which message levels are written to log files even without debug output |
| `[DebugSettings]` | `DeprecatedLog` | `enabled` | PHP deprecations go to `deprecated.log`, each distinct message once per request |
| `[TemplateSettings]` | `ShowUsedTemplates` | `disabled` | List every template a page used, and which override chose it |
| `[TemplateSettings]` | `ShowTemplatePathComments` | `disabled` | HTML comments naming the template of each part of the page |
| `[TemplateSettings]` | `Debug` | `disabled` | More detail from the template engine |
| `[DatabaseSettings]` | `SQLOutput` | `disabled` | Every SQL query with its time |

Check any of them with `./console exp:ini where site.ini/<Block>/<Setting>`.

> **Warning**
> Debug output reveals file paths, SQL and settings. On any machine that others can reach, restrict it with
> `DebugByIP` or `DebugByUser`, and switch it off before going live.

### Reading the report

- **Errors and warnings** first: each has a label, usually the class or template that raised it, for example
  `eZTemplate @ design:node/view/full.tpl:12[5]` (file, line and column of the template).
- **Templates**: requested template, used template, and the override rule that picked it. When your override does not
  apply, this table says which template won instead.
- **Timing points**: where the time went.
- **SQL**: with `SQLOutput=enabled`, every query; a query that runs a hundred times is a loop that should fetch once.

## The template path comments

With `ShowTemplatePathComments=enabled`, view the page source: each part of the HTML is wrapped in comments naming its
template. This is the quickest way from "this part of the page" to "this file".
See [Template path comments](../../../features/6.0/template-path-comments.md).

## The log files

Log files are written under `var/log/` (some installations use `var/<site>/log/` for site specific logs). The ones a
developer reads most:

| File | What is in it |
|---|---|
| `error.log` | Errors: always logged (`AlwaysLog[]=error`) |
| `warning.log` | Warnings, when logged |
| `notice.log`, `debug.log`, `strict.log` | The lower levels, when logged |
| `deprecated.log` | PHP deprecations: the to-do list for the next PHP version |
| `storage.log` | File storage operations |
| `404.log` | Pages not found (`[DebugSettings] NotFoundLog`) |
| `csp.log` | Content Security Policy violations: your inline script is listed here |
| `security.log`, `auth.log` | Security events and sign-ins |

```bash
tail -f var/log/error.log            # follow it while you reload the page
grep -c "" var/log/deprecated.log    # how many deprecation lines
```

The administration has a viewer for the same files, with search and permalinks: **Setup > Logs**, described in
[The log viewer](../../../features/6.0/log-viewer.md). Each error carries a reference that leads to every line of its
request: [Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md). The command line
form is `./console exp:explogs`.

## Writing your own debug lines

From PHP, use `eZDebug`. Each method is static and takes the message, an optional label and an optional style class
(signatures from `lib/ezutils/classes/ezdebug.php`):

```php
<?php
eZDebug::writeDebug( 'Value of $nodeID: ' . $nodeID, __METHOD__ );
eZDebug::writeNotice( 'Cache miss for ' . $key, 'mysite_hello' );
eZDebug::writeWarning( 'Setting MySettings/Limit is missing, using 10', __METHOD__ );
eZDebug::writeError( 'Could not load node ' . $nodeID, __METHOD__ );
```

| Method | Level | Use for |
|---|---|---|
| `writeDebug()` | debug | temporary values while you work; remove them afterwards |
| `writeNotice()` | notice | information that helps reading a report |
| `writeWarning()` | warning | something unexpected the code recovered from |
| `writeError()` | error | something failed; always written to `error.log` |

Use `__METHOD__` as the label: the report then names the class and method.

From a template, `{debug-log var=$node msg='node in my template'}` writes a value into the debug report (see
[Debugging templates](../part-2-builder/debugging-templates.md)).

## Writing your own log file

For things you want to keep, independent of debug settings, write a log file of your own with `eZLog::write()`
(`lib/ezfile/classes/ezlog.php`):

```php
<?php
// static function write( $message, $logName = 'common.log', $dir = 'var/log' )
eZLog::write( 'Imported ' . $count . ' items', 'mysite_hello.log' );
```

The file appears under `var/log/`. Never write personal data, passwords or keys into a log.

## A debugging routine

1. Reproduce the problem on your development installation.
2. Read the debug report: errors first, then the template table.
3. `tail` the error log while you reload.
4. Add `eZDebug::writeDebug()` lines around the code you suspect; reload; read.
5. When you understand it, remove the debug lines, fix the code, and check that the log stays quiet.

Debugging extensions goes further: class loading, autoloads,
caches and the RAD health check.

## Related pages

- Previous: [Learning from an extension, step by step](learning-from-an-extension-step-by-step.md)
- Next: [Asking the community for help](asking-the-community.md)
- [The Exp Debug bar](../../../bc/6.0/debug-bar.md)
- eZDebug and logging
- [When something goes wrong](../../when-something-goes-wrong.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
