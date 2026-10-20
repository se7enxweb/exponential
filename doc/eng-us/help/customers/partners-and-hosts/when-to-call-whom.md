---
title: When to call whom
audience: customer, editor, admin
category: customer-success
order: 11
summary: A lookup table for the owner's team of an Exponential site - which kind of problem goes to the site administrator, the host, the partner, the public issue trackers, the private security address, the community or paid support from 7x.
keywords: who to call, support, host, partner, administrator, issue tracker, security, community, 7x, paid support, escalation
type: help
---
# When to call whom

Problems get solved faster when they reach the right person first. Keep this table with your
[escalation contacts](escalation-contacts-template.md), and adjust it to the split you agreed in
[Who does what](who-does-what.md).

## The table

| Problem | Call first | Then |
|---|---|---|
| The site does not answer at all, or shows "We'll be right back" | Host | Partner, if the host finds the cause in the site ([When the site is down](../healthy/when-the-site-is-down.md)) |
| "Something went wrong on our side" with an `ERR-` reference | Site administrator or partner, with the reference | Host, if it points to the server |
| A certificate warning in the browser | Host | |
| Mail from the site does not arrive (forms, password resets, receipts) | Host | Partner, if the site's mail settings are the cause |
| Slow site | Host | Partner ([Speed and caching](../healthy/speed-and-caching.md)) |
| A page looks wrong, a template or design problem | Partner | |
| An editor cannot do something they should | Site administrator (roles and access) | Partner |
| "How do I ...?" in the administration | The help in the administration, then the site administrator | Community ([Getting support](../../project/getting-support.md)) |
| A suspected break-in: unknown accounts, changed files | Host, at once, as severity 1 | Partner; your data protection officer if personal data may be exposed |
| A security weakness in Exponential itself | Privately to security@se7enx.com, usually through your partner | ([Private security reports](../healthy/private-security-reports.md)) |
| A reproducible bug in Exponential itself | Partner or administrator, who files it | The public tracker ([GitHub issues](../healthy/github-issues.md)) |
| You need guaranteed help and your providers cannot give it | Paid support from 7x ([Working with 7x and the project](working-with-7x-and-the-project.md)) | |

## Before you call

Have the facts ready: page address, time with time zone, error reference, what was done, whether everyone is
affected, recent changes ([Reporting problems](../healthy/reporting-problems.md)). Never give a password on the phone
or in a ticket.

## Severity tells them how fast

Say the severity when you call. A common scale ([Support tiers](../../hosting/business/support-tiers.md)):

| Severity | Meaning |
|---|---|
| 1 Critical | The site is down or data is being lost or exposed |
| 2 High | An important function fails, no workaround |
| 3 Normal | A function fails, a workaround exists |
| 4 Low | Questions, wishes |

## When nobody answers

1. Check the agreed first response time in your support agreement
   ([Support agreement template](support-agreement-template.md)).
2. Use the next step on your escalation list.
3. Write down the times in your [incident notes](../healthy/incident-notes-template.md); they matter for the yearly
   review.
4. For a severity 1 problem, consider a maintenance page while you wait, if someone can switch it on
   ([Maintenance mode and planned work](../healthy/maintenance-mode-and-planned-work.md)).

## Free help has no promised time

Community help in the forums and chats is free, and "nobody promises an answer time"
([When something goes wrong](../../when-something-goes-wrong.md)). Use it for questions, not for an outage.

## Related pages

- [Escalation contacts template](escalation-contacts-template.md)
- [Reporting problems](../healthy/reporting-problems.md)
- [Support tiers (for hosts)](../../hosting/business/support-tiers.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
