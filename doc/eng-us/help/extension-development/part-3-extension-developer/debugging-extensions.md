---
title: Debugging extensions
audience: developer, app-creator
category: extension-development
order: 350
summary: A routine for an extension that does nothing or breaks a page: is it active, are its settings read, do its classes load, are caches or workers stale, with the RAD health check, checkclasses and the logs.
keywords: debugging, troubleshooting, extension not working, class not found, module not found, template not found, RAD health check, checkclasses, exp:ini where, cache, Velocity restart, error.log, debug output
type: help
---
# Debugging extensions

Part 3, chapter 35. Most extension problems are not in the code you wrote but in how it is plugged in: a setting not
read, a class not in the autoload map, a cache, a worker that still runs yesterday's code. This chapter is a routine
that finds those quickly, then the tools for real bugs.

## The routine

| Step | Command | Looking for |
|---|---|---|
| 1. Is it active? | `./console exp:ini get site.ini/ExtensionSettings/ActiveExtensions` (add `-s <siteaccess>`) | your extension in the list |
| 2. Are its settings read? | `./console exp:ini where <file>/<Block>/<Variable>` | your file in the chain, and nothing later overriding it |
| 3. Do its classes load? | `php bin/php/ezpgenerateautoloads.php -e -v`, then `php bin/php/checkclasses.php --quiet-ok` | your classes found; nothing PHP refuses |
| 4. What does the RAD health check say? | **Setup > RAD** (`/setup/rad`) | settings that name a class that cannot load, a datatype offered and not found, a view with no script, a design extension with no design |
| 5. Caches? | `php bin/php/ezcache.php --clear-all` | the problem gone means a cache |
| 6. Workers? | `./console exp:velocity deploy` (or reload PHP-FPM) | the problem gone means old code in memory |
| 7. Logs | `tail -n 50 var/log/error.log` and the debug output | the actual error |

Step 4 is underrated: `expRADHealth::findings()` (`kernel/setup/expradhealth.php`) checks the whole installation for
configuration that cannot work, and names the fix for each finding.

## Typical symptoms

| Symptom | Usual cause |
|---|---|
| "The requested module does not exist" | `ExtensionRepositories[]` missing, or caches |
| A page with the layout but an empty content area | a new template file and an old template override cache |
| `Class '...' not found` | autoloads not regenerated, or workers not restarted |
| `Operator '...' is not registered` | `ExtensionAutoloadPath[]` missing or the extension inactive |
| `Object problem with operator '...'` | the operator class has an old-style constructor or fails to construct |
| A datatype missing from the class editor | `AvailableDataTypes[]` or `ExtensionDirectories[]` missing; the file path |
| Works with `php` on the command line, not in the browser | a different PHP, or PHP-FPM / Velocity not reloaded |
| A button does nothing in the administration | a Content Security Policy violation: open the browser console |
| Access denied on your page | the view's `'functions'`, and the user's roles: `./console exp:accesscheck` |

## Real bugs

- **Debug output**: errors with their template file and line, timing, SQL ([Debug output and logs for developers](../part-1-from-user-to-developer/debug-output-and-logs-for-developers.md)).
- **`eZDebug::writeDebug()`** lines around the suspect code; remove them afterwards.
- **A script with the kernel loaded** to call your class directly: `php bin/php/ezexec.php <script.php>`. Most of the
  examples in this book were checked that way.
- **A test** that reproduces the bug before you fix it ([Tests for extensions](tests-for-extensions.md)).
- **`git bisect`** in your extension's repository finds the commit that broke it.
- **The error reference** on an error page leads to every log line of that request
  ([Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md)).

eZpedia's ["HOWTO Troubleshoot eZ Publish Extensions"](https://ezpedia.exponential.earth/en/learning/howto_troubleshoot_ez_publish_extensions)
covers the same ground from the earlier years.

## Related pages

- Previous: [Tests for extensions](tests-for-extensions.md)
- Next: [Performance in extensions](performance-in-extensions.md)
- [RAD tools](../../../features/6.0/rad-tools.md)
- [When something goes wrong](../../when-something-goes-wrong.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
