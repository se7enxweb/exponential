---
title: Hide content after a closing date
audience: government, admin
category: public-sector
order: 430
summary: Task for administrators: hide consultations, vacancies or calls automatically when their closing date passes, with the hide.php cronjob and content.ini HideSettings, keeping the content as a record (unlike unpublish.php, which removes it).
keywords: closing date, hide, hide.php, HideSettings, HideDateAttributeList, consultations, vacancies, cronjob, records
type: help
---
# Hide content after a closing date

This task is for administrators. The `hide.php` cronjob (in the global part) hides content whose date has passed, as
**Hide** in the administration does; the content and its history stay. Background:
[Scheduled publication and embargoes](../../transparency-and-foi/scheduled-publication-and-embargoes.md) and
[Cronjobs](../../../../guides/cronjobs.md).

## Steps

1. Make sure the class (for example `consultation`) has a date field, for example `close_date`.
2. In `settings/override/content.ini.append.php`:

   ```ini
   <?php /*
   [HideSettings]
   RootNodeList[]
   RootNodeList[]=<node id of the consultations folder>
   HideDateAttributeList[]
   HideDateAttributeList[consultation]=close_date
   */ ?>
   ```

3. Clear the INI cache; make sure the global cronjob part runs (`php runcronjobs.php` without a part name).

Do not use `unpublish.php` (`[UnpublishSettings]`) for this: it removes the node when an `unpublish_date` has passed.

## Check

After the closing date and the next cronjob run, the item is marked hidden in the administration and no longer shown
to visitors; search engines that keep visibility (Solr) are told as well.

## Problems

| Problem | Fix |
|---|---|
| Nothing is hidden | the cronjob does not run, or the folder is not in `RootNodeList[]` |
| The item must be public again (extended deadline) | change the date and unhide it |

## Related pages

- [Withdrawing and correcting published information](../../transparency-and-foi/withdrawing-and-correcting-published-information.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
