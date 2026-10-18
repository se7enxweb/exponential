---
title: Your first hour as a developer
audience: user, developer, designer, app-creator
category: extension-development
order: 50
summary: A guided hour: see who sets a setting, change and undo it, find the template behind a page, read the debug output and a log, and run PHP with the kernel loaded.
keywords: first hour, tutorial, beginner, exp:ini where, debug output, used templates, template path comments, log, ezexec, undo, backup
type: help
---
# Your first hour as a developer

Part 1, chapter 5. This chapter is a guided hour on the development installation from the previous chapter. You do
six small exercises, each one with something to type and something to see. Nothing here writes code yet; the point
is to learn how to *look* at the system, which is most of a developer's work.

> **Note**
> Do this on your development installation only. Every exercise can be undone, and the last section shows how.

## Exercise 1: where does a setting come from? (10 minutes)

Every behavior of Exponential comes from settings in `.ini` files, and many files can set the same value. The
question "which file wins?" is the first one a developer learns to ask.

```bash
./console exp:ini where site.ini/SiteSettings/SiteName --allow-root-user
```

You see every file that sets `SiteName`, in the order they are read, and the value in effect at the end. The kernel's
default in `settings/site.ini` comes first; overrides, siteaccess settings and extensions come later and win.

Now the same for a setting an extension changes:

```bash
./console exp:ini where design.ini/ExtensionSettings/DesignExtensions --allow-root-user
```

That is the list of extensions whose `design/` folders the template system searches. Each line names the extension
that added itself. This is how extensions plug in: by adding a line to a setting.

## Exercise 2: change a setting, and undo it (10 minutes)

```bash
./console exp:ini set site.ini/SiteSettings/SiteName "My dev site" global --dry-run --allow-root-user
```

`--dry-run` prints the change as a diff and writes nothing. Run it again without `--dry-run`. The command writes
`settings/override/site.ini.append.php`, keeps a copy of the old file under `var/backup/ini/<timestamp>/`, and clears
the settings cache. Reload a page of your site: the name changed.

Undo it:

```bash
./console exp:ini rem site.ini/SiteSettings/SiteName global --allow-root-user
./console exp:ini get site.ini/SiteSettings/SiteName --allow-root-user
```

The value in effect is the one from before. You just did the most common builder's task, safely and reversibly.

## Exercise 3: which template drew this page? (10 minutes)

Switch on the list of used templates (if you did not in the previous chapter):

```bash
./console exp:ini set site.ini/TemplateSettings/ShowUsedTemplates enabled global --allow-root-user
./console exp:ini set site.ini/DebugSettings/DebugOutput enabled global --allow-root-user
```

Open the front page of your site and scroll to the bottom. The debug output now has a table of every template that
was used, with the template that was *requested* and the file that was *used*. When the two differ, an override
replaced the standard template. Find the pagelayout (the template that draws the frame around every page) in the list.

Then switch on the path comments, reload, and view the page source (Ctrl+U):

```bash
./console exp:ini set site.ini/TemplateSettings/ShowTemplatePathComments enabled global --allow-root-user
```

Every part of the HTML is now wrapped in comments that name its template file. When you want to change something on a
page, this is how you find the file to change. More in
[Template path comments](../../../features/6.0/template-path-comments.md).

## Exercise 4: read the debug output (10 minutes)

Look at the rest of the debug output under the page:

- **Errors and warnings**, each with a label that names the part of the system that raised it.
- **Timing points**: how long each phase of the request took.
- **Template usage** from exercise 3.
- **SQL**, when `[DatabaseSettings] SQLOutput` is enabled (try it, then switch it off again: it is long).

Open a page that does not exist, for example `/content/view/full/999999999`. The debug output tells you why you see
an error page. This habit, reading what the system says before guessing, saves more time than any other.

## Exercise 5: read a log file (10 minutes)

Errors are also written to log files under `var/log/` (and `var/<site>/log/` for some installations):

```bash
ls var/log/
tail -n 20 var/log/error.log
```

The administration has a viewer for the same files: **Setup > Logs**, explained in
[The log viewer](../../../features/6.0/log-viewer.md). Every error line carries a reference you can search for; see
[Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md).

## Exercise 6: run a script with the kernel loaded (10 minutes)

Create a file in your own extension folder, `extension/mysite_hello/scripts/hello.php`:

```php
<?php
// Runs with the kernel loaded: php bin/php/ezexec.php extension/mysite_hello/scripts/hello.php
$ini = eZINI::instance( 'site.ini' );
$cli = eZCLI::instance();
$cli->output( 'Site name: ' . $ini->variable( 'SiteSettings', 'SiteName' ) );
$cli->output( 'Active extensions: ' . count( $ini->variable( 'ExtensionSettings', 'ActiveExtensions' ) ) );
```

Check it and run it:

```bash
php -l extension/mysite_hello/scripts/hello.php
php bin/php/ezexec.php extension/mysite_hello/scripts/hello.php
```

`bin/php/ezexec.php` loads the kernel (settings, database, autoloads) and then runs your file. You just used two of
the kernel's classes: `eZINI`, which reads settings, and `eZCLI`, which writes to the terminal. Both are described in
Part 8: eZINI and
eZScript and eZCLI.

## Undo everything

```bash
./console exp:ini rem site.ini/TemplateSettings/ShowTemplatePathComments global --allow-root-user
```

Keep `DebugOutput` and `ShowUsedTemplates` on while you work through the book; switch them off with `exp:ini rem` in
the same way when you are done. Every write made a backup under `var/backup/ini/`, so even a mistake is one copy away.

## What you learned

| You can now | With |
|---|---|
| Find which file sets a value | `exp:ini where` |
| Change and undo a setting safely | `exp:ini set`, `--dry-run`, `exp:ini rem` |
| Find the template behind any part of a page | used templates, template path comments |
| Read why a page failed | debug output, `var/log/error.log`, Setup > Logs |
| Run your own PHP with the kernel loaded | `bin/php/ezexec.php` |

## Related pages

- Previous: [Setting up a development installation](setting-up-a-development-installation.md)
- Next: [Reading the code you already have](reading-the-code-you-have.md)
- Changing settings with exp:ini
- Debugging templates
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
