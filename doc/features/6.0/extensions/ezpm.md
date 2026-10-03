# ezpm: private messages

`ezpm` ("eZ PM") adds **private messaging** between users of the site: an inbox, sent items and drafts (`pm/list_inbox`, `pm/list_sent`,
`pm/list_drafts`), writing, replying and editing (`pm/create`, `pm/reply`, `pm/edit`, `pm/add`, `pm/message`). Version 0.9
was the version for the Exponential 4.x kernel line, with many design and security fixes, best with ezwebin and ezflow; 0.9.1 added an **email notification** whose text is
the template `design/standard/templates/pm/notification_email.tpl`, switched on in `ezpm.ini.php`.

## Set it up

1. Activate the extension and create its tables (`sql/update.sql`, or `sql/<engine>/`).
2. Give users the policy module `pm` (full access) and open `/pm/list_inbox`.
3. To integrate with the forums of ezwebin/ezflow, copy `design/standard/override/templates/full/forum_topic.tpl` into the ezwebin override folder and
   add a link to the Private Messaging module in your page layout (see line 156 of `pm_pagelayout.tpl`).

## What changed

* 0.9.1 (28 January 2024): `composer.json`.
* 0.10.0 (22 September 2026): module views no longer declare functions or classes at the top level without a guard, so a persistent worker (Velocity)
  survives the second request ([details](../../../bc/6.0/extensions-behaviour-changes.md#1-persistent-php-workers-module-views-no-longer-declare-at-file-level-without-a-guard)).

## Related

* [Chronicle](../../../history/extensions/ezpm.md) and [release notes](../../../changelogs/extensions/ezpm.md)
