---
title: Check upgrade consistency
audience: government, admin, hosting
category: public-sector
order: 210
summary: Step by step - run the file consistency check against the release manifest on Setup > Upgrade check or with checkmanifest.php, download the report, and decide what each modified or missing file means.
keywords: upgrade check, file consistency, checkmanifest, filelist.md5, modified files, missing files, Check file consistency, Download CSV, release manifest, integrity
type: help
---
# Check upgrade consistency

This task is for administrators before and after an upgrade, and monthly as an integrity check. At the end you have a
report of every file that differs from the release you run. Reference: [The upgrade check](../../../../guides/upgrade-check.md).

## Contents
1. [Before you start](#before-you-start)
2. [In the administration](#in-the-administration)
3. [On the shell](#on-the-shell)
4. [What the findings mean](#what-the-findings-mean)
5. [Related pages](#related-pages)

## Before you start

- An administrator account (the page is **Setup > Upgrade check**), or shell access.
- `share/filelist.md5` of the release you run is present (it ships with the release).

## In the administration

1. **Setup > Upgrade check** (`/setup/systemupgrade`).
2. **Check file consistency**.
3. Read the findings; **Download CSV** or **Download text** for the record.

The check only reads; each run and download is recorded as `system.upgrade.run`.

## On the shell

```bash
php bin/php/checkmanifest.php --all
php bin/php/checkmanifest.php --all --extensions
php bin/php/checkmanifest.php --all --extensions --csv > upgrade-check-$(date +%F).csv
```

Exit 0 when nothing is modified or missing. The command needs no kernel, settings or database.

## What the findings mean

| Finding | Do |
|---|---|
| A file you changed on purpose | move the change into an extension or override; until then list it as accepted |
| A file nobody can explain | investigate as a possible compromise ([Defacement](../../operations/incidents/defacement.md)) |
| Missing files | restore them from the release |
| "File share/filelist.md5 does not exist." | copy the manifest of the release this installation runs |

## Related pages
- [Check the database schema](check-the-database-schema.md)
- [Upgrade check before and after](../../operations/upgrades-and-change/upgrade-check-before-and-after.md)
- [Upgrade check as a monitor](../../operations/monitoring/upgrade-check-as-a-monitor.md)
- [File consistency check](../../../../features/6.0/file-consistency-check.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
