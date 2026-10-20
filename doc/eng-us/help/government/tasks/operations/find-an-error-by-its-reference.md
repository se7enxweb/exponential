---
title: Find an error by its reference
audience: government, admin, developer
category: public-sector
order: 170
summary: Step by step - when a citizen or colleague reports an error page with a reference such as ERR-0123456789, find the log line and all lines of that request in the log viewer or with exp:logs, and hand it to developers.
keywords: error reference, ERR-, error page, citizen report, service desk, log viewer, exp:logs --reference, request lines, support ticket
type: help
---
# Find an error by its reference

This task is for service-desk staff and administrators. An Exponential error page shows a reference such as
`ERR-0123456789`; the same reference is in the log line, so a report from a citizen leads straight to the cause.
Guide: [Finding an error by its reference](../../../../guides/finding-an-error-by-its-reference.md).

## Contents
1. [Before you start](#before-you-start)
2. [In the administration](#in-the-administration)
3. [On the shell](#on-the-shell)
4. [Next steps](#next-steps)
5. [Related pages](#related-pages)

## Before you start

- The reference from the report (ask the citizen for it and the time; a screenshot helps).
- `setup/logs`, or shell access.

## In the administration

1. **Setup > Log files**.
2. Search for the reference (the search accepts an error reference) across the kernel logs, with rotations if the
   report is older.
3. Open the line: the viewer shows the message, file and line, and the other lines of the same request, linked to the
   audit records of the same minute.
4. **Copy permalink** for the ticket.

## On the shell

```bash
./console exp:logs --reference=ERR-0123456789
```

It says where the reference is and prints the lines of its request. Exit 0 found, 1 not found.

## Next steps

| Line says | Do |
|---|---|
| a template or code error | developer ticket with the permalink |
| a database error | [Database unreachable](../../operations/incidents/database-unreachable.md) |
| a missing file or permission | [Check file and directory permissions](check-file-and-directory-permissions.md) |
| not found at all | the report may be from another server or engine; search there, or with `--rotations` for older logs |

Reply to the citizen without technical detail; personal data in the report stays in your ticket system.

## Related pages
- [Read the error digest](read-the-error-digest.md)
- [The log viewer](../../operations/monitoring/the-log-viewer.md)
- [Site down](../../operations/incidents/site-down.md)
- [When something goes wrong (help)](../../../when-something-goes-wrong.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
