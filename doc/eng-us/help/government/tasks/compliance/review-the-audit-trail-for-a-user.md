---
title: Review the audit trail for a user
audience: government, admin
category: public-sector
order: 150
summary: Step by step - see everything one user did (sign-ins, failed sign-ins, content moved, hidden or removed, role and setting changes) in Exponential's Audit console or with exp:audit search, narrow it by time and event, and note what you found.
keywords: audit trail, user activity, audit console, exp:audit search, filters, user id, login, access.session, content.node, role changes, investigation, review, audit/read, public sector
type: help
---
# Review the audit trail for a user

For administrators and auditors with `audit/read`: a review when someone leaves, a complaint about a change, or a
suspicion of misuse ([The audit trail for public bodies](../../security/audit-trail-for-public-bodies.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps in the console](#steps-in-the-console)
3. [Steps on the command line](#steps-on-the-command-line)
4. [What you will and will not see](#what-you-will-and-will-not-see)
5. [Related pages](#related-pages)

## Before you start

- The policy `audit/read` (limited by channel if so assigned). Every view of the audit is itself recorded.
- The user's id (Users > Check user accounts, or the user's node) or login.
- For staff matters, follow your procedure (who may look, who must be told).

## Steps in the console

1. Open the **Audit** tab > **Console** (`/audit/console`).
2. Filter by user: `/audit/console/(user)/<id>` (or `(login)/<login>`).
3. Narrow: `(from)/<YYYY-MM-DD>/(to)/<YYYY-MM-DD>`, `(name)/access.session.*` for sign-ins,
   `(name)/content.node.*` for moves, hides and removals, `(name)/access.role.*` for role changes.
4. Open an event (`audit/event/<id>`) for the full record: before and after values, the request, the other events of
   the same request.
5. The filtered address can be bookmarked and shared with someone who also has `audit/read`.

## Steps on the command line

```bash
./console exp:audit search --user=<id> --from=<YYYY-MM-DD> --limit=200 --allow-root-user
./console exp:audit search --user=<id> --name='content.node.*' --allow-root-user
```

Times without an offset are UTC on the command line, and in the site's time zone in the console.

## What you will and will not see

- Records within retention (90 days live; older days after `exp:audit restore`).
- Not recorded by default: publishing and creating content (`content.object.publish`, `content.object.create`), reads.
  For what an object looked like, use its versions page.
- Addresses truncated to /24; after 90 days the index shows pseudonymized values (the export still finds them).

## Related pages

- [Audit guide: the admin interface](../../../../bc/6.0/audit.md#32-the-admin-interface)
- [Export audit records for an investigation](export-audit-records-for-an-investigation.md)
- [Record who published content](record-who-published-content.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
