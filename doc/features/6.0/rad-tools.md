# RAD tools: extension wizards, catalogue, survey and health check

Exponential is extended in a few repeating ways: a class in a directory the
kernel scans, a class named by an ini setting, a file in a place found by
convention, or a template in a design. The RAD tools put that knowledge in one
place and write the boilerplate for you. Open them at **Setup > RAD**
(`/setup/rad`).

## The catalogue: 64 extension points, 64 tools

`kernel/setup/expradcatalogue.php` lists every point the system can be extended
at, how it is registered (directory, handler, autoload, file, design, ini or
override), the kernel file the mechanism lives in, and the tool that writes it.
Each was checked against this installation's source. Read the curated text in
[Extension points](../../bc/6.0/rad-extension-points.md).

## Wizards

Each wizard builds a **working extension**, not a snippet to paste, and **shows
every file it would write before it writes any of them**. Nothing is written
outside `extension/<name>`, an existing extension is never overwritten, and
generated templates escape what they print; see
[RAD tools security](../../bc/6.0/rad-security.md).

| Wizard | Writes |
|---|---|
| Design extension | A design (templates, stylesheets, override set). |
| Template extension | Template operators, fetch functions, attribute operators, template functions. |
| Handler extension | The handler extension points, including the four shop handlers (VAT, shipping, basket totals, exchange rates) and login handlers. |
| Settings extension | An extension that only carries ini settings, including the extension root and siteaccess customisation points that arrived with 6.0. |
| Content extension | A content class written as a script, not an afternoon of clicking. |
| Datatype extension | A datatype. |
| Workflow event extension | Workflow event types and triggers. |
| Module extension / Module wizard | A module with views, and a module over existing tables. |
| Handler wizard, filter points | The eight filter extension points; seven have a generator. |
| Kernel override | A kernel class replaced through the override autoload path, the heaviest mechanism and the last resort. |

The first two RAD tools in the old system had not changed since 2003 and handed
back one php file; they were rewritten to write a complete extension.

## Extension surface survey

**Setup > RAD > Extension point survey** (`/setup/radsurvey`) reads what this
installation really exposes off disk: settings that name a class, what a
template can call, what announces an event, and what has already been replaced.
It counts 1737 points in the reference installation (924 before it also swept
template calls, events and replacements). Every count is a link to the entries
behind it, and every finding says how to fix it. An alias is told apart from a
broken registration, so a healthy alias is not reported as a fault. The
generated reference is [The extension surface](../../bc/6.0/rad-extension-surface.md).

## Health check: what is configured and cannot work

The health check (`kernel/setup/expradhealth.php`) reports settings that name a
class that cannot be loaded. Its first run found a kernel fault that stopped
every payment gateway loading (`ezpaymentgatewaytype.php`), fixed in the same
change.

From the command line, `bin/php/checkclasses.php` loads every class this
installation declares and lists the ones php refuses. It loads them in a child
process that prints each name before trying it, so a class that kills php is
identified as the last name printed and the run carries on:

```bash
php bin/php/checkclasses.php
```

A class that cannot be loaded is not a quiet problem: the fatal takes the whole
request with it, and it stays invisible until something touches that class.

## Related

- [Extension points](../../bc/6.0/rad-extension-points.md)
- [The extension surface](../../bc/6.0/rad-extension-surface.md)
- [RAD tools security](../../bc/6.0/rad-security.md)
