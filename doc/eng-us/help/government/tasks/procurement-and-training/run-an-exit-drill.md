---
title: Run an exit drill
audience: government, admin, project-manager
category: public-sector
order: 200
summary: Step by step, test once a year that a public body can really leave - export the whole site with an xrowextract archive, copy the storage, dump the database, check the archive against the admin's counts, and record the result - on a copy, not on production.
keywords: exit drill, export test, xrowextract, site archive, manifest, database dump, storage, verification, lock-in, procurement task
type: help
---
# Run an exit drill

**Who:** the site administrator, observed by the contract manager. **When:** once a year, and before a contract
renewal. The commands come from [Migrating away from Exponential](../../../../install/migration/leaving-exponential.md)
and the trainer lesson [Leaving Exponential](../../../trainers/migration/module-08-leaving-and-capstone/leaving-exponential.md).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Record](#record)
4. [Result](#result)
5. [Pitfalls](#pitfalls)

## Before you start

- A copy of the site (staging or a restored backup), not production.
- Shell access and a working directory, for example `var/tmp/export/`.
- The site's object counts per class from the administration (**Setup > Classes**).

## Steps

1. Create the working directory:

   ```bash
   mkdir -p var/tmp/export
   ```

2. Make a site archive of every class, as a ZIP:

   ```bash
   php bin/php/console ext:xrowextract:archive --set=everything --format=zip --output=var/tmp/export/
   ```

3. Open the archive's `manifest.json` and `README.txt`; pick classes and compare row counts with the admin's counts
   (one row per object per language).
4. Check that no password hash is in the archive; the archive never contains them by design.
5. Copy the storage directory `var/<site>/storage/` next to the archive.
6. Dump the database with the engine's tool ([Database backups per engine](../../../hosting/operations/database-backups-per-engine.md)).
7. If the site uses layouts:

   ```bash
   php bin/php/console exp:layouts export --all --output=var/tmp/export/layouts.zip
   ```

8. Export form submissions you must keep, and the consent records, as CSV.
9. Open a sample of files and rich text from the archive in ordinary tools (a spreadsheet, a text editor).
10. Remove the export when done, or store it under your records and privacy rules; it contains personal data.

## Record

```text
Exit drill - <site> - <date> - <names>
Archive: <file>, classes <n>, counts matched: yes/no (differences: <...>)
Storage copied: <size>     Database dump: <engine, size>     Layouts: <yes/no/none>
Forms / consent exported: <...>     Time taken: <...>
Problems and fixes: <...>     Exit plan updated: yes/no
```

## Result

Evidence that your data can leave in open formats, and a measured time for the exit plan.

## Pitfalls

- Running it on production during office hours.
- Forgetting the storage directory: the archive references files.
- Leaving the export lying around: it contains personal data.

## Related pages

- [Procurement and training tasks](README.md)
- [Write an exit plan](write-an-exit-plan.md)
- [Data export formats](../../procurement/exit-plans/data-export-formats.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
