---
title: Record who published content
audience: government, admin, project-manager
category: public-sector
order: 200
summary: Step by step - switch on the audit events content.object.create and content.object.publish, which Exponential leaves off by default, so the audit trail answers "who published this, and when?", and check that publishing is recorded.
keywords: audit, publishing, content.object.publish, content.object.create, AuditEventSettings, Enabled, accountability, who published, records, evidence, public sector
type: help
---
# Record who published content

For administrators and project managers. "Who published this?" is the question the audit guide says is asked most,
and the shipped default leaves it off: `settings/audit.ini [AuditEventSettings] Enabled[]` records moves, hides,
removals, states and sections, but not creating and publishing ([Audit guide, 6.2 Busy site](../../../../bc/6.0/audit.md#62-busy-site)).
Public bodies usually want it on.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [Related pages](#related-pages)

## Before you start

- More records: one per publish. On a busy site the guide also suggests larger audit buffers.
- The versions page already shows each version's creator and dates; the audit adds a tamper-evident, searchable
  record across all content.

## Steps

1. In `settings/override/audit.ini.append.php` (no empty `Enabled[]` line first, or the shipped list is cleared):

   ```ini
   [AuditEventSettings]
   Enabled[]=content.object.create
   Enabled[]=content.object.publish
   ```

2. Clear the INI cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`; reload PHP-FPM and restart
   Velocity (`./console exp:velocity deploy --allow-root-user`).

## Check that it worked

1. Publish a test article in a test folder.
2. `./console exp:audit tail --channel=content --name='content.object.*' --lines=5 --allow-root-user` shows the publish
   with the user.
3. On **Setup > Audit** (`/audit/settings`) the event list shows the new entries and the file they come from.

The setting write itself is recorded as `system.setting.write` when made with `exp:ini`.

## Related pages

- [The audit trail for public bodies](../../security/audit-trail-for-public-bodies.md)
- [Versions and content history](../../records-and-archiving/versions-and-content-history.md)
- [Audit guide, 8 Event reference](../../../../bc/6.0/audit.md#8-event-reference)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
