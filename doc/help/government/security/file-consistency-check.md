---
title: File consistency check
audience: government, admin, hosting
category: public-sector
order: 240
summary: For public bodies that must detect unauthorized changes to their web application - how Exponential's file consistency check compares every file with the release's checksum list, in the admin and on the command line, when to run it, and what to do with a finding.
keywords: file consistency, integrity check, filelist.md5, manifest, checksums, Upgrade check, verifyfiles.sh, checkmanifest.php, tampering, defacement, upgrade, incident, change detection, public sector
type: help
---
# File consistency check

This page is for administrators and hosting teams. Every Exponential release ships `share/filelist.md5`, a list of
checksums of its files. Comparing the installation with it shows whether a file was changed, removed or left behind,
whether by an upgrade gone wrong or by an intruder
([File consistency check](../../../features/6.0/file-consistency-check.md)).

## Contents

1. [Running the check](#running-the-check)
2. [When to run it](#when-to-run-it)
3. [Reading the findings](#reading-the-findings)
4. [Limits](#limits)
5. [Related pages](#related-pages)

## Running the check

**In the administration**: **Setup > Upgrade check** (`/setup/systemupgrade`), **Check file consistency**. It reads
`share/filelist.md5` and the manifest of every active extension that carries one, groups the findings (modified,
missing, unreadable; files tracked but not listed; malformed lines), shows the manifest dates, and offers the result as
CSV or text ([The upgrade check](../../../guides/upgrade-check.md)). **Check database consistency** compares the schema.

**On the command line**, from the document root the web server uses:

```bash
bash bin/shell/verifyfiles.sh            # summary and the files that differ
bash bin/shell/verifyfiles.sh --quiet    # exit status only: 0 = all files match
php bin/php/checkmanifest.php --all --extensions           # same class as the admin page
php bin/php/checkmanifest.php --all --extensions --csv     # findings as CSV
```

## When to run it

| Moment | Why |
|---|---|
| after every update | anything left behind or edited by hand shows at once |
| weekly, from monitoring (`--quiet`, alert on exit status 1) | unnoticed changes |
| after an incident or a suspicious alert | to see which files an attacker touched |
| before a penetration test or an audit | to start from a known state |

## Reading the findings

- **Modified kernel file you did not change**: treat as a possible compromise until explained. Compare with the release
  (the tag on GitHub) and look at the audit trail around the file's modification time.
- **Modified file you did change** (a local patch): move the change into an extension or an override; patched kernel
  files are lost at the next update.
- **Missing file**: an incomplete update or deployment.
- **Files git tracks but the manifest does not list** (a note, on an installation that is a git checkout): files
  added after the manifest was made; a file nobody can explain deserves a look.

Releases are immutable tags: a fix arrives as the next version, never as a changed copy, so the manifest of a version
always describes the same files ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)).

## Limits

- It covers the files the manifests list: the kernel and extensions that ship a manifest. Your own extensions,
  `settings/override` and `var/` are not covered.
- An attacker with write access could change the manifest too; keep a copy of the release's manifest off the server, or
  compare with the tag.

## Related pages

- [File consistency check](../../../features/6.0/file-consistency-check.md)
- [The upgrade check](../../../guides/upgrade-check.md)
- [Patching cadence](patching-cadence.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
