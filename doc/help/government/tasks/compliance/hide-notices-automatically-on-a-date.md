---
title: Hide notices automatically on a date
audience: government, admin, developer
category: public-sector
order: 510
summary: Step by step - let editors give each notice a hide date and have Exponential's hide.php cronjob hide it when the date has passed, by adding a date attribute to the class and configuring content.ini [HideSettings].
keywords: hide date, scheduled hide, hide.php, HideSettings, RootNodeList, HideDateAttributeList, cronjob, notices, expiry, archiving, date attribute, public sector
type: help
---
# Hide notices automatically on a date

For administrators and developers. The kernel's cronjob script `hide.php` (in the default cronjob part) hides objects of
chosen classes whose date attribute has passed; it does nothing until configured. Hidden notices stay in the tree with
their history and can be revealed ([Archiving old notices](../../records-and-archiving/archiving-old-notices.md#pattern-hide-on-a-date)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [Related pages](#related-pages)

## Before you start

- The class of the notices (for example identifier `notice`) and the node id of the folder that holds them.
- Rights to edit classes; access to settings.
- The default cronjob part runs on the server (`php runcronjobs.php` from the crontab).

## Steps

1. **Setup > Classes**, edit the notice class, add an attribute of the date (or date and time) datatype with the
   identifier `hide_date`, name "Hide on". Store.
2. In `settings/override/content.ini.append.php`:

   ```ini
   [HideSettings]
   RootNodeList[]
   RootNodeList[]=<node id of the notices folder>
   HideDateAttributeList[]
   HideDateAttributeList[notice]=hide_date
   ```

3. Clear the INI cache.
4. Tell editors to fill "Hide on" when they publish a notice.

Do not confuse with `unpublish.php` and `[UnpublishSettings]`, which move objects with a passed `unpublish_date` to the
**trash**; for records, hiding is the safer choice.

## Check that it worked

1. Publish a test notice with a hide date in the past, in a test folder under the configured root.
2. Run the default part by hand: `php runcronjobs.php -q`, or only the script: the output lists "Hiding node: ...".
3. The notice shows **Hidden** in the administration and is gone from the public site; the audit records
   `content.node.hide`.

## Related pages

- [Archiving old notices](../../records-and-archiving/archiving-old-notices.md)
- [Hiding content](../../records-and-archiving/hiding-content.md)
- [Cronjobs](../../../../guides/cronjobs.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
