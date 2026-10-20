---
title: Logs for owners
audience: customer, admin, businesses
category: customer-success
order: 12
summary: What the logs and the audit trail of an Exponential site can tell an owner, how an error reference connects a visitor's report to the exact log lines, how personal data in logs is protected, and what to ask your administrator or host.
keywords: logs, log viewer, error reference, ERR, audit trail, error digest, masking, personal data, retention, rotation
type: help
---
# Logs for owners

You will rarely read a log yourself, but you should know what the logs can answer, because it changes what you ask
for when something goes wrong. This page is the owner's map. The administrator's tools are described in
[The log viewer](../../../features/6.0/log-viewer.md) and
[Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md).

## Two kinds of record

| Record | Answers | Where |
|---|---|---|
| The logs | What went wrong, technically, and when | **Setup > Log files** in the administration ([The log viewer](../../../features/6.0/log-viewer.md)) |
| The audit trail | Who did what: sign-ins, content changes, settings, deploys, orders | The **Audit** tab ([The audit trail](../../../features/6.0/audit-trail.md)) |

The logs explain an error; the audit trail often explains its cause: "an edit, a publish or a settings change right
before the error is often the cause" ([Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md)).

## The error reference

When something goes wrong on the site, the visitor sees **Something went wrong on our side** with a reference such
as `ERR-0123456789` at the bottom. "The error has already been logged; the reference lets an administrator find
exactly that moment in the logs" ([When something goes wrong](../../when-something-goes-wrong.md)).

What this means for you:

- **Ask for the reference.** Train your support staff and editors to note the reference, the time and the page
  address whenever they or a customer meet an error ([Reporting problems](reporting-problems.md)).
- **Pass it on unchanged.** An administrator pastes it into **Setup > Log files**, field **Error reference**, and
  sees the entry and every line of the same request.
- **Report quickly.** Logs rotate; an old reference may have rotated away (the number of rotations kept is a setting,
  `site.ini [FileSettings] LogFileRotateCount`, same guide).

## Personal data in logs

Logs can hold e-mail and IP addresses. The log viewer masks them by default; only a user with the policy
`setup/unmasklogs` can switch masking off for themselves, and a shared permalink is always masked
([Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md)). The audit trail truncates
addresses, hashes sessions and never records passwords, tokens or secrets
([The audit trail](../../../features/6.0/audit-trail.md)).

Your rule: **never paste unmasked log lines into a ticket**, and never forward logs with customers' data to someone
who does not need it. How long logs and audit records are kept is covered in
[Data retention and deletion](../../businesses/security/data-retention-and-deletion.md) (not legal advice).

## Who may read what

| Right | Gives |
|---|---|
| `setup/logs` | The log viewer, masked |
| `setup/unmasklogs` | Switching masking off for oneself |
| `audit/read`, `audit/manage` | Reading and managing the audit; only the Administrator role holds them in a new installation |

Sources: [Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md),
[The audit guide](../../../bc/6.0/audit.md). Give these rights only to the people who answer problems.

## What to ask your administrator or host

- [ ] Who reads the error digest, and how often ([Monitoring](monitoring.md))?
- [ ] How long are logs kept before they rotate away?
- [ ] Is the audit trail verified regularly, and is it copied off the server?
- [ ] When I send an error reference, how soon can you tell me what it was?
- [ ] Do the people who can unmask logs need to?

## Related pages

- [Logs and error references (for businesses)](../../businesses/reliability/logs-and-error-references.md)
- [Audit trail for operations](../../businesses/reliability/audit-trail-for-operations.md)
- [Log viewer and rotation (for hosts)](../../hosting/operations/log-viewer-and-rotation.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
