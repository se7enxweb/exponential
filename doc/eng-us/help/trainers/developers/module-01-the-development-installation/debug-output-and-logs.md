---
title: "Lesson 1.3: Debug output and logs"
audience: trainer, developer
category: training
order: 13
summary: Learners switch on debug output, used templates and template path comments for their development installation, read the Exp Debug bar, follow the error log and write their own debug lines. 45 minutes.
keywords: developers course, debug output, DebugOutput, ShowUsedTemplates, ShowTemplatePathComments, Exp Debug bar, error.log, eZDebug, eZLog, exp:logs, DebugByIP
type: help
---
# Lesson 1.3: Debug output and logs

A live site hides its errors. A development installation must show them. In this lesson learners switch on the
debug output with `exp:ini`, read the report the Exp Debug bar shows under every page, follow the log files while
they reload, and write their own debug lines from PHP and from a template.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 1: The development installation](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Beginner |
| Audience | PHP developers new to Exponential |
| Prerequisites | [Lesson 1.2](a-development-installation-with-git.md): a running development installation |

## Learning objectives

After this lesson, learners can:

- switch debug output, used templates and template path comments on and off with `exp:ini`;
- read the errors, templates, timing and SQL parts of the debug report;
- follow `var/log/error.log` with `tail` or `exp:logs` while reproducing a problem;
- write debug lines with `eZDebug` and a log file of their own with `eZLog::write()`;
- restrict debug output to their own address or user on a shared machine.

## Trainer's notes

### How to present it

Sources: [Debug output and logs for developers](../../../extension-development/part-1-from-user-to-developer/debug-output-and-logs-for-developers.md),
[The Exp Debug bar](../../../../features/6.0/exp-debug-bar.md) and [The log viewer](../../../../features/6.0/log-viewer.md).

1. **The settings** (4 minutes). All in `site.ini`; show the defaults:

   | Block | Setting | Default | Meaning |
   |---|---|---|---|
   | `[DebugSettings]` | `DebugOutput` | `disabled` | Show the debug report at all |
   | `[DebugSettings]` | `DebugByIP` + `DebugIPList[]` | `disabled` | Only for these addresses |
   | `[DebugSettings]` | `DebugByUser` + `DebugUserIDList[]` | `disabled` | Only for these user IDs |
   | `[TemplateSettings]` | `ShowUsedTemplates` | `disabled` | List every template the page used |
   | `[TemplateSettings]` | `ShowTemplatePathComments` | `disabled` | HTML comments naming each part's template |
   | `[TemplateSettings]` | `TemplateCompile` | `enabled` | Compiled template cache |
   | `[DatabaseSettings]` | `SQLOutput` | `disabled` | Every SQL query with its time |

2. **Switching them on** (3 minutes). Show the `exp:ini set` commands of the exercise and the `--dry-run` option.
   `exp:ini` writes `settings/override/site.ini.append.php` and keeps a backup.
3. **The report** (4 minutes). Reload a page: errors first, then the templates table (requested template, used
   template, override), timing points, SQL. A query that runs a hundred times is a loop that should fetch once.
4. **Logs** (2 minutes). `var/log/error.log` always gets errors (`[DebugSettings] AlwaysLog[]=error`).
   `deprecated.log` is the to-do list for the next PHP version. **Setup** > **Log files** shows the same files in the
   browser.
5. **Your own lines** (2 minutes). `eZDebug::writeDebug()`, `writeNotice()`, `writeWarning()`, `writeError()`,
   with `__METHOD__` as the label; `{debug-log var=$node msg='...'}` in a template.

### Common questions

**Can visitors see the debug output?** Yes, everyone who can open the page, unless `DebugByIP` or `DebugByUser`
limits it. Debug output reveals paths, SQL and settings: never on a live site.

**Why does my template change not show?** With `TemplateCompile=enabled` the compiled version is used until the
template cache is cleared. Developers often set `TemplateCompile=disabled` while editing.

**Where is the log of a command line script?** The same `var/log/` files; `[DebugSettings] ScriptDebugOutput`
adds debug output to scripts.

### Pitfalls

- **Forgetting to clear the INI cache.** After a settings change, run
  `php bin/php/ezcache.php --clear-tag=ini`; otherwise the old values are used.
- **Debug lines left in code.** Ask learners to remove every `writeDebug()` before they commit.
- **Personal data in logs.** Never write passwords, keys or personal data to a log file.

## Hands-on exercise

On the development installation from lesson 1.2, from its root folder.

1. Look where the setting comes from: `php bin/php/console exp:ini where site.ini/DebugSettings/DebugOutput`.
2. Preview the change: `php bin/php/console exp:ini set site.ini/DebugSettings/DebugOutput enabled global --dry-run`.
3. Switch on the three settings:

   ```bash
   php bin/php/console exp:ini set site.ini/DebugSettings/DebugOutput enabled global
   php bin/php/console exp:ini set site.ini/TemplateSettings/ShowUsedTemplates enabled global
   php bin/php/console exp:ini set site.ini/TemplateSettings/ShowTemplatePathComments enabled global
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-tag=template
   ```

4. Reload the front page of the site. Open the debug bar and find the list of used templates.
5. View the page source and search for `START`. Write down the template of the page footer.
6. In a second terminal, follow the error log: `tail -f var/log/error.log`.
7. Open an address that does not exist, for example `/site/content/view/full/999999`, and watch both the page
   and the terminal.
8. List the log files the viewer may open: `php bin/php/console exp:logs --list`.
9. Show the last five entries of the error log through the viewer:
   `php bin/php/console exp:logs --tail=var/log/error.log --lines=5`.
10. Switch `ShowTemplatePathComments` off again with the same command and `disabled`, and clear the template cache.

## Expected result

- Step 1 lists `settings/site.ini` with `DebugOutput=disabled` and, after step 3, an override with `enabled` in
  effect.
- Step 4: the debug bar appears at the bottom of every page, its templates list names `pagelayout.tpl` and the
  node view templates.
- Step 5: comments such as `<!-- START design/.../footer.tpl -->` in the source.
- Step 7: the not-found page; `404.log` or `error.log` gains a line depending on the settings.
- The trainer checks the `exp:ini where` output of two learners.

## Quiz

1. Which setting shows the debug report under every page?
   A. `[TemplateSettings] Debug` B. `[DebugSettings] DebugOutput` C. `[DatabaseSettings] SQLOutput` D. `[DebugSettings] AlwaysLog[]`
2. Which two settings limit the debug output to some people?
3. What should follow every settings change with `exp:ini set`?
4. Which `eZDebug` method writes to `error.log` even without debug output?
5. True or false: template path comments are safe to leave on for a feed or a live siteaccess.

<details>
<summary>Answers</summary>

1. **B.** `DebugOutput=enabled` shows the report, the Exp Debug bar.
2. `DebugByIP` with `DebugIPList[]`, and `DebugByUser` with `DebugUserIDList[]`.
3. Clearing the INI cache, `php bin/php/ezcache.php --clear-tag=ini`.
4. `eZDebug::writeError()`: errors are always logged (`AlwaysLog[]=error`).
5. **False.** They add markup to every template; development only
   ([Template path comments](../../../../features/6.0/template-path-comments.md)).

</details>

## Further reading

- [Debug output and logs for developers](../../../extension-development/part-1-from-user-to-developer/debug-output-and-logs-for-developers.md):
  every setting and log file.
- [The Exp Debug bar](../../../../features/6.0/exp-debug-bar.md): its tabs and its settings tab.
- [The log viewer](../../../../features/6.0/log-viewer.md) and
  [Finding an error by its reference](../../../../guides/finding-an-error-by-its-reference.md).

## Related pages

- [Module 1: The development installation](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
