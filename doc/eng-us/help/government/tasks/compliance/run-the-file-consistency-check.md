---
title: Run the file consistency check
audience: government, admin, hosting
category: public-sector
order: 370
summary: Step by step - compare every file of the installation with the release's checksum list in Setup > Upgrade check or on the command line, read the findings, and file the result after updates and incidents.
keywords: file consistency, Upgrade check, filelist.md5, verifyfiles.sh, checkmanifest.php, integrity, modified files, missing files, after update, incident, evidence, public sector
type: help
---
# Run the file consistency check

For administrators and hosting teams, after every update, after an incident, and as a regular check
([File consistency check](../../security/file-consistency-check.md)).

## Contents

1. [Steps in the administration](#steps-in-the-administration)
2. [Steps on the command line](#steps-on-the-command-line)
3. [Reading the result](#reading-the-result)
4. [Related pages](#related-pages)

## Steps in the administration

1. **Setup > Upgrade check** (`/setup/systemupgrade`).
2. **Check file consistency**: the page compares the files with `share/filelist.md5` and the manifests of active
   extensions that carry one, and groups the findings (modified, missing, unreadable, notes).
3. Download the result as CSV or text and file it.
4. **Check database consistency** compares the schema with the release's.

## Steps on the command line

From the document root the web server uses:

```bash
bash bin/shell/verifyfiles.sh                 # summary and differing files; exit 0 when all match
php bin/php/checkmanifest.php --all --extensions --csv > var/tmp/consistency-$(date +%F).csv
```

For monitoring: `bash bin/shell/verifyfiles.sh --quiet || <your alert command>`.

## Reading the result

| Finding | Action |
|---|---|
| nothing differs | file the result |
| a kernel file modified that nobody changed | treat as a possible compromise; compare with the release tag; check the audit around the file's time |
| a file modified by a local patch | move the change into an extension or override |
| missing files | finish the update or deployment |

## Related pages

- [File consistency check](../../security/file-consistency-check.md)
- [The upgrade check](../../../../guides/upgrade-check.md)
- [Incident response](../../security/incident-response.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
