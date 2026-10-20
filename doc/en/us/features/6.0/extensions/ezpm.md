# ezpm: private messages

This page is for site builders who want users of the site to send each other messages. `ezpm` ("eZ PM") adds
**private messaging**: an inbox, sent items and drafts, and writing, replying and editing messages.

| View | Purpose |
|---|---|
| `pm/list_inbox`, `pm/list_sent`, `pm/list_drafts` | inbox, sent items, drafts |
| `pm/create`, `pm/reply`, `pm/edit`, `pm/add`, `pm/message` | write, reply, edit and read |

Version 0.9 was the version for the Exponential 4.x kernel line, with many design and security fixes, and works best
with ezwebin and ezflow. Version 0.9.1 added an **email notification**: its text is the template
`design/standard/templates/pm/notification_email.tpl`, and it is switched on in `ezpm.ini.php`.

## Set it up

1. Activate the extension and create its tables (`sql/update.sql`, or `sql/<engine>/`).
2. Give users the policy module `pm` (full access).
3. Open `/pm/list_inbox`. The inbox appears.
4. Optional, to integrate with the forums of ezwebin or ezflow: copy
   `design/standard/override/templates/full/forum_topic.tpl` into the ezwebin override folder and add a link to the
   Private Messaging module in your page layout (see line 156 of `pm_pagelayout.tpl`).

## What changed

| Version | Date | Change |
|---|---|---|
| 0.9.1 | 28 January 2024 | `composer.json`. |
| 0.10.0 | 22 September 2026 | Module views no longer declare functions or classes at the top level without a guard, so a persistent worker (Velocity) survives the second request ([details](../../../bc/6.0/extensions-behaviour-changes.md#1-persistent-php-workers-module-views-no-longer-declare-at-file-level-without-a-guard)). |

## Related pages

- [Velocity engines](../../../bc/6.0/velocity-engines.md)
- [Chronicle](../../../history/extensions/ezpm.md) and [release notes](../../../changelogs/extensions/ezpm.md)
- [Change ledger](../../../history/ledger/ezpm.md)
- [Month: 2026-09 (all extensions)](../../../history/extensions/months/2026-09.md)
