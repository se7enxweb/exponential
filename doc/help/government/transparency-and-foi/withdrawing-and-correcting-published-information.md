---
title: Withdrawing and correcting published information
audience: government, editor, admin
category: public-sector
order: 180
summary: How to correct or withdraw information a public body has published, in Exponential, so that the public sees what changed and the record stays complete: corrections with notes, hiding instead of removing, the trash, and the special case of data published by mistake.
keywords: correction, withdraw, retract, erratum, hide, unhide, trash, remove, published by mistake, accountability, transparency, notice
type: help
---
# Withdrawing and correcting published information

This page is for editors and administrators who must fix or take back something the body published. Transparency
depends on corrections being visible and the record staying complete. Exponential offers editing with versions,
hiding, and removal to the trash; each has a different effect ([Publishing, versions and the trash](../../publishing-and-versions.md)).

## Contents

1. [Choose the right action](#choose-the-right-action)
2. [Correcting with a visible note](#correcting-with-a-visible-note)
3. [Withdrawing by hiding](#withdrawing-by-hiding)
4. [Removing and the trash](#removing-and-the-trash)
5. [Published by mistake](#published-by-mistake)

## Choose the right action

| Situation | Action | Address | Record |
|---|---|---|---|
| A factual error | Edit, add a dated correction note | stays | old version in the history |
| Superseded by a newer document | Edit: add "Superseded by" with a link | stays | complete |
| Must no longer be public, but is a record | Hide | no longer shown to visitors | content and history stay |
| Created by mistake, never relevant | Remove (to the trash) | stops | in the trash until it is emptied |
| Personal or confidential data published | Replace, remove old versions, report | stays | see below |

## Correcting with a visible note

1. Open the item, **Edit**.
2. Correct the text or replace the file.
3. Add a line at the end (or in a "Corrections" field of your class): "Corrected on <date>: <what changed>."
4. **Send for publishing**.

The previous version stays in the version history for staff, and **Compare two versions** shows the change
(see "Version history as accountability").

## Withdrawing by hiding

Hiding takes an item off the public site without removing it: in the tree, the item's menu, **Advanced** >
**Hide / unhide**; or in a **Sub items** list, tick items and **More actions** > **Hide selected**. Hidden items are
marked in the administration and can be unhidden. Before hiding an item that other objects point at, Exponential
lists those references first (`content.ini [HideSettings] ReverseRelationCheck=warn`).

If visitors should still learn that the item existed, publish a short notice page at a related place instead of, or
in addition to, hiding.

## Removing and the trash

Removing moves content to the trash, where it can be restored until the trash is emptied
([Publishing, versions and the trash](../../publishing-and-versions.md)); emptying it deletes the content and its
history. The audit trail records the removal and the purge ([Audit trail](../../../features/6.0/audit-trail.md)). For
record-relevant content, restrict `content/remove` to administrators and prefer hiding.

## Published by mistake

When personal data or confidential material went public:

1. Act at once: hide the item, or replace the file and publish.
2. Remove the versions that still hold the material on the item's **Versions** page (policy `content/versionremove`).
3. Clear the caches in front of the site ([Caches](../../../guides/caches.md)).
4. Tell your data protection officer or security officer; they decide on notifications.
5. Check the request logs and, if reads are recorded, the audit `read` channel for downloads
   ([Request logs](../../../features/6.0/request-logs.md)).
6. Publish the corrected version with a neutral note if the item must stay public.

## Related pages

- [Publishing, versions and the trash](../../publishing-and-versions.md)
- [Redaction is a manual step](redaction-is-a-manual-step.md)
- [Version history as accountability](version-history-as-accountability.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
