# RAD tools: extension wizards, catalogue, survey and health check

Exponential is extended in a few repeating ways: a class in a directory the
kernel scans, a class named by an ini setting, a file in a place found by
convention, or a template in a design. The RAD tools put that knowledge in one
place and write the boilerplate for you. Open them at **Setup > RAD**
(`/setup/rad`).

## Why it helps

Extending Exponential used to mean reading the kernel to learn which ini line, directory or class name a hook
needs. A mistake in any of them fails quietly: the extension loads, nothing happens. The RAD tools turn that
knowledge into a list you can read, wizards that write a working extension, and checks that tell you what is
configured and cannot work. A first extension takes minutes, not an afternoon of source reading.

## Use it in five minutes

1. Open **Setup > RAD** (`/setup/rad`). You need the `setup/setup` policy.
2. Pick the point you want to extend in the catalogue. Use `/setup/rad/(show)/tools` to list only the points that have a wizard.
3. Press its tool. The wizard asks for a name and what to generate, then lists every file it would write under
   `extension/<name>` before writing any of them.
4. Confirm. Enable the extension (`site.ini [ExtensionSettings] ActiveExtensions[]=<name>`), run
   `php bin/php/ezpgenerateautoloads.php -e` and clear the caches.
5. Open **Setup > RAD > Extension point survey** to see your registration counted, and the health check to see it load.

## The catalogue: 68 extension points, 64 with a tool

`kernel/setup/expradcatalogue.php` lists every point the system can be extended
at, how it is registered (directory, handler, autoload, file, design, ini or
override), the kernel file the mechanism lives in, and the tool that writes it.
On this installation it holds 68 points in seven groups (content, templates, modules, workflow, storage, packaging, access); 64 have a tool and 4 are documentation only (`/setup/rad/(show)/docs`). The mechanisms are `directory`, `handler`, `autoload`, `file`, `design`, `ini`, `override` and `runnable`. The 14 September change brought it from fifteen points to sixty-four of sixty-four; four points were added after that. Each was checked against this installation's source. Read the curated text in
[Extension points](../../bc/6.0/rad-extension-points.md).

## Wizards

Each wizard builds a **working extension**, not a snippet to paste, and **shows
every file it would write before it writes any of them**. Nothing is written
outside `extension/<name>`, an existing extension is never overwritten, and
generated templates escape what they print; see
[RAD tools security](../../bc/6.0/rad-security.md).

| Wizard (URL) | Writes |
|---|---|
| Design extension (`/setup/designextension`) | A design (templates, stylesheets, override set). |
| Template extension (`/setup/templateoperator`) | Template operators, fetch functions, attribute operators, template functions. |
| Handler extension (`/setup/handlerextension/<point>`) | The handler extension points, including the four shop handlers (VAT, shipping, basket totals, exchange rates) and login handlers. |
| Settings extension (`/setup/settingsextension`) | An extension that only carries ini settings, including the extension root and siteaccess customisation points that arrived with 6.0. |
| Content extension (`/setup/contentextension`) | A content class written as a script, not an afternoon of clicking. |
| Datatype extension (`/setup/datatype`) | A datatype. |
| Workflow event extension (`/setup/workflowevent`) | Workflow event types and triggers. |
| Module extension / Module wizard (`/setup/moduleextension`, `/setup/modulewizard`) | A module with views, and a module over existing tables. |
| Handler wizard, filter points | The eight filter extension points; seven have a generator. |
| Kernel override (`/setup/kerneloverride`) | A kernel class replaced through the override autoload path, the heaviest mechanism and the last resort. |

The first two RAD tools in the old system had not changed since 2003 and handed
back one php file; they were rewritten to write a complete extension.

## Extension surface survey

**Setup > RAD > Extension point survey** (`/setup/radsurvey`) reads what this
installation really exposes off disk: settings that name a class, what a
template can call, what announces an event, and what has already been replaced.
It counts 1737 points in the reference installation on 14 September (924 before it also swept
template calls, events and replacements); the same survey at HEAD on this installation counts 3940 across ten groups (settings that name a class 624, places the kernel looks 263, interfaces and abstract classes 64, module views 452, template operators and functions 424, events 40, template overrides 411, kernel classes replaced 3, commands, cronjob parts and views as classes 485, registry entries that name no class 1174), because the installation and the sweep both grew. A survey page can be linked to with `/setup/radsurvey/(show)/<section>/(offset)/<n>`. Every count is a link to the entries
behind it, and every finding says how to fix it. An alias is told apart from a
broken registration, so a healthy alias is not reported as a fault. The
generated reference is [The extension surface](../../bc/6.0/rad-extension-surface.md).

## Health check: what is configured and cannot work

The health check (`kernel/setup/expradhealth.php`, shown on the RAD page) reports settings that name a
class that cannot be loaded and, more widely, these kinds of finding: `setting-names-no-class`, `directory-searched-and-not-there`, `design-extension-with-no-design`, `view-with-no-script`, `datatype-offered-and-not-found` and `nothing-implements-it`. At HEAD it reports 32 findings on this installation (`expRADHealth::findings()`), each with how to fix it. Its first run found a kernel fault that stopped
every payment gateway loading (`ezpaymentgatewaytype.php`), fixed in the same
change.

From the command line, `bin/php/checkclasses.php` loads every class this
installation declares and lists the ones php refuses. It loads them in a child
process that prints each name before trying it, so a class that kills php is
identified as the last name printed and the run carries on:

```bash
php bin/php/checkclasses.php --allow-root-user
php bin/php/checkclasses.php --kernel --quiet-ok --allow-root-user
```

`--kernel` also checks the kernel classes (slower), `--tests` includes classes under tests directories, and `--quiet-ok` prints nothing when everything loads. (Options from `php bin/php/checkclasses.php --help --allow-root-user`.)

A class that cannot be loaded is not a quiet problem: the fatal takes the whole
request with it, and it stays invisible until something touches that class.

## Settings

The RAD pages read no settings of their own; they are drawn from the code catalogue and from the installation's ini files.
The one settings entry is the menu link in `settings/menu.ini` (`Links[rad]=setup/rad`, `PolicyList_rad[]=setup/setup`).
Check with `grep -n -B1 -A3 "Links\[rad\]" settings/menu.ini`.

## Limits

- A wizard never overwrites an existing extension and writes nothing outside `extension/<name>`.
- The survey and health check read the installation as it is on disk; after a change run the autoload and cache steps first.
- Four catalogue points have no wizard: follow the curated text in [Extension points](../../bc/6.0/rad-extension-points.md).

## Related

- [September 2026, first half: 14 September](../../history/2026/2026-09a.md#14-september-pdf-rss-and-the-rad-tools)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
- [Runnable commands, cronjobs and views (specification)](../../specifications/6.0/runnable-commands-cronjobs-views.md)
- [Extension points](../../bc/6.0/rad-extension-points.md)
- [The extension surface](../../bc/6.0/rad-extension-surface.md)
- [RAD tools security](../../bc/6.0/rad-security.md)
