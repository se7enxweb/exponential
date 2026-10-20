---
title: Find who changed a page
audience: government, admin, editor
category: public-sector
order: 310
summary: Task for administrators and lead editors: find out who changed, moved or removed a page and what changed, with the Versions page (compare two versions) and the audit trail (console or exp:audit).
keywords: who changed, version history, compare versions, audit trail, audit console, exp:audit search, content.node.remove, accountability
type: help
---
# Find who changed a page

This task is for lead editors (versions) and administrators (audit). Background:
[Version history as accountability](../../transparency-and-foi/version-history-as-accountability.md) and
[The audit trail for who changed what](../../transparency-and-foi/the-audit-trail-for-who-changed-what.md).

## The content: versions

1. Open the page; in the editor **Manage versions**, or go to `/content/history/<object ID>`.
2. Each version card shows its creator and dates.
3. Under **Compare two versions**, choose **Older version**, **Newer version** and **Translation**: the fields that
   differ are shown.

## The action: audit trail

For moves, removals, hiding, section or state changes, and when the page is gone:

1. **Audit** tab (`/audit/dashboard`), **Console**; filter **Channel** `content`, **Event name** `content.*` or
   `content.node.remove`, **Object**, **From** and **To**.
2. Or on the command line:

   ```bash
   ./console exp:audit search --channel=content --name='content.node.*' --from=2026-10-01
   ./console exp:audit show <event id>
   ```

The audit needs the policy `audit/read`; administrators have it.

## Check

You can name the version, its creator and time, and the audit event that matches.

## Problems

| Problem | Note |
|---|---|
| Old versions are missing | the version limit removed them (see "Raise the version history limit for records") |
| No audit record | reads are not recorded unless switched on; content changes are |

## Related pages

- [Content history](../../../../guides/content-history.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
