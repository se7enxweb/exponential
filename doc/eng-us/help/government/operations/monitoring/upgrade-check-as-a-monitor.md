---
title: Upgrade check as a monitor
audience: government, admin, hosting
category: public-sector
order: 110
summary: Using the file consistency check (checkmanifest.php and Setup > Upgrade check) as a scheduled integrity monitor, so an unauthorized change to the code of a public website - a defacement, a web shell, a hand edit - is noticed.
keywords: file integrity, integrity monitoring, checkmanifest, filelist.md5, file consistency, defacement detection, web shell, unauthorized change, upgrade check, scheduled check
type: help
---
# Upgrade check as a monitor

This page is for administrators and security officers who want to know when the code of a public website changes
outside a deployment. The file consistency check compares the installation's files with the release manifest
`share/filelist.md5`; run on a schedule, it is a simple integrity monitor. Reference:
[File consistency check](../../../../features/6.0/file-consistency-check.md) and
[The upgrade check](../../../../guides/upgrade-check.md).

## Contents
1. [What it catches](#what-it-catches)
2. [Scheduling it](#scheduling-it)
3. [Making the baseline clean](#making-the-baseline-clean)
4. [What it does not catch](#what-it-does-not-catch)
5. [Related pages](#related-pages)

## What it catches

- A kernel or shipped file that was changed (checksum differs) or removed.
- With `--extensions`, the same for every extension directory that carries its own manifest.

That covers a hand edit on the live server, a file replaced by an attacker, and an incomplete deployment.

## Scheduling it

```bash
php bin/php/checkmanifest.php --all --extensions
```

The command needs no kernel, settings or database, so it runs even when the site is broken. It prints notes as
`warning:` lines and problems as `error:` lines and exits 0 when nothing is modified or missing. Schedule it daily
from your monitoring system or the crontab, alerting on a non-zero exit. In the administration the same check is
**Setup > Upgrade check**, **Check file consistency**; each run is recorded as `system.upgrade.run`.

## Making the baseline clean

The monitor is only useful when "nothing modified" is the normal state:

1. Run the check; list each modified file.
2. Move intended changes out of kernel files into your own extension or design (overrides), where they do not count
   as modified kernel files.
3. Keep a short list of accepted differences until they are moved, and compare against it.

## What it does not catch

- Your own extensions and designs without a manifest.
- Files the manifest does not list (a new file dropped into a directory); combine with your server's file monitoring
  or a check of `git status` if you deploy from version control
  ([Configuration under version control](../hosting/configuration-under-version-control.md)).
- Changes in the database (content, settings stored there); the audit trail covers those.
- Uploaded files in `var/storage`.

## Related pages
- [Upgrade check before and after](../upgrades-and-change/upgrade-check-before-and-after.md)
- [Defacement](../incidents/defacement.md)
- [Check upgrade consistency](../../tasks/operations/check-upgrade-consistency.md)
- [Compromised site (hosting runbook)](../../../hosting/runbooks/compromised-site.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
