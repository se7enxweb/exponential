---
title: Keep trash items for 90 days
audience: government, admin
category: public-sector
order: 500
summary: Step by step - empty Exponential's trash on a schedule while keeping each item for a set number of days, with content.ini [TrashSettings] KeepItemsForDays and the trashpurge cronjob part (in no part by default), and check with the command line.
keywords: trash, trash purge, KeepItemsForDays, trashpurge, cronjob part, bin/php/trashpurge.php, trashed-days, retention, storage limitation, public sector
type: help
---
# Keep trash items for 90 days

For administrators. Removed content waits in the trash until someone empties it. A purge on a schedule keeps the trash
from holding personal data forever, while leaving time to restore mistakes ([The trash view](../../../../bc/6.0/trash.md#keeping-items-in-the-trash-for-a-while)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [Related pages](#related-pages)

## Before you start

- Agree the period with the records manager; check for legal holds first ("Legal holds").
- Items trashed before 6.0 have no trash time (`trashed = 0`) and are purged on the first run whatever the setting; see
  the trash guide to keep them.

## Steps

1. In `settings/override/content.ini.append.php` (the trash is one per database, so set it there):

   ```ini
   [TrashSettings]
   KeepItemsForDays=90
   ```

   Empty or 0 purges everything; a value that is not a whole number of days up to 36500 purges nothing and reports an
   error, so a typo cannot empty the trash.
2. Add the cronjob part, which is in no part by default, in `settings/override/cronjob.ini.append.php`:

   ```ini
   [CronjobPart-trashpurge]
   Scripts[]
   Scripts[]=trashpurge.php
   ```

3. Schedule it in the site user's crontab, for example nightly: `php runcronjobs.php -q trashpurge`.

## Check that it worked

- On the command line the same purge for a test: `php bin/php/trashpurge.php --trashed-days=90` on a copy first.
- `var/log/trashpurge.log` (Setup > Log files) shows each run; the trash keeps items younger than 90 days.
- The audit records what was purged.

## Related pages

- [The trash and restoring content](../../records-and-archiving/trash-and-restore.md)
- [Restore an item from the trash](restore-an-item-from-the-trash.md)
- [Cronjobs](../../../../guides/cronjobs.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
