---
title: The log viewer
audience: government, admin, developer
category: public-sector
order: 30
summary: Reading Exponential's logs in the administration (Setup > Log files) and on the shell (exp:logs) - which logs exist, permalinks, following a file live, searching, finding an error by its reference, masking of personal data, and who may use it.
keywords: log viewer, Setup Log files, logs, error.log, exp:logs, permalink, follow, search, error reference, masking, personal data, setup/logs, setup/unmasklogs
type: help
---
# The log viewer

This page is for administrators and developers who read the logs of a public website, and for data protection staff
who want to know what the viewer shows. The full reference is [The log viewer](../../../../features/6.0/log-viewer.md);
the hosting view is [Log viewer and rotation (hosting)](../../../hosting/operations/log-viewer-and-rotation.md).

## Contents
1. [What it shows](#what-it-shows)
2. [Reading and searching](#reading-and-searching)
3. [An error reference from a citizen](#an-error-reference-from-a-citizen)
4. [Personal data](#personal-data)
5. [On the shell](#on-the-shell)
6. [Who may use it](#who-may-use-it)
7. [Related pages](#related-pages)

## What it shows

**Setup > Log files** (`/setup/logs`) lists `error.log`, `warning.log`, `notice.log`, `debug.log`, `deprecated.log`,
`auth.log`, the request logs `404.log` and `security.log`, `csp.log`, the cronjob logs, each site's logs
(`var/<site>/log/*.log`), extensions' logs and the rotated archives. A navigator groups them as Kernel, Site,
Extensions, Web server and Rotated archives, with size, last write and lines a minute. Web server logs appear only when
`logviewer.ini` names them; they are never guessed.

## Reading and searching

- Every view has a **permalink** such as `/setup/logs/view/error/(level)/error/(search)/timeout`; **Copy permalink**
  puts it on the clipboard for a ticket.
- **Follow** a file live, with **Pause** and **Resume**; nothing holds a server process open.
- **Search** one file, every kernel log or every file, with rotations: text or a bounded regular expression, levels,
  a time range, siteaccess, request, client address, error reference.
- Lines are parsed, colored by level (never by color alone), grouped by request and linked to the audit records of
  the same minute and to nodes, objects and users in the administration.
- **Copy messages** copies the shown lines as text.

## An error reference from a citizen

When a visitor reports an error page with a reference such as `ERR-0123456789`, search for it: the viewer finds the
line and the other lines of the same request. Guide: [Finding an error by its reference](../../../../guides/finding-an-error-by-its-reference.md)
and [Find an error by its reference](../../tasks/operations/find-an-error-by-its-reference.md).

## Personal data

By default the viewer masks e-mail addresses, IP addresses, passwords, tokens, keys, session ids and cookies; the
values of secret settings are never shown. **Show unmasked** (only with `setup/unmasklogs`) shows them for that viewer
while switched on. Every view and every action is audited. Downloads are masked and compressed. This makes the viewer
suitable for staff who need to diagnose without seeing citizens' data; keep unmasking for investigations. Retention of
logs is covered on [Log retention and rotation](log-retention-and-rotation.md).

## On the shell

```bash
./console exp:logs --list
./console exp:logs --tail=var/log/error.log --lines=50
./console exp:logs --search=timeout --file=var/log/error.log --rotations --level=error,warning
./console exp:logs --reference=ERR-0123456789
```

The command line applies the same masking and limits but checks no policy (shell access is the control); each run
that reads a file is recorded as `system.log.read`.

## Who may use it

| Function | Allows |
|---|---|
| `setup/logs` | the page, follow, search, download a range (when downloads are on) |
| `setup/managelogs` | Rotate now, empty, remove an archive, each only when enabled in `logviewer.ini [ActionSettings]` |
| `setup/unmasklogs` | the **Show unmasked** switch |

Only Administrator holds them in a new installation; see [Give log access without admin rights](../../tasks/people-and-permissions/give-log-access-without-admin-rights.md).

## Related pages
- [Error digest and log health](error-digest-and-log-health.md)
- [Request logs: 404 and security](request-logs-404-and-security.md)
- [Log retention and rotation](log-retention-and-rotation.md)
- [Log viewer feature](../../../../features/6.0/log-viewer.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
