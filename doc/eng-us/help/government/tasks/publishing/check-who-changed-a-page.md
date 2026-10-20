---
title: Check who changed a page
audience: government, editor
category: public-sector
order: 450
summary: For editors and team leads - find out who changed a page and when, from the versions page (creator and dates of each version) and the node's Audit tab (every recorded action), and what each source can and cannot tell you.
keywords: who changed, history, versions, Manage versions, creator, audit trail, Audit tab, audit/read, accountability, changes, timeline
type: help
---
# Check who changed a page

This page is for editors and team leads who need to know who changed a page and when: to ask a question, to answer a
complaint, or for accountability. Two sources exist: the page's versions, and the audit trail
([Versions of an object](../../../../guides/content-history.md), [Audit trail](../../../../features/6.0/audit-trail.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps with the versions page](#steps-with-the-versions-page)
3. [Steps with the Audit tab](#steps-with-the-audit-tab)
4. [What each source tells you](#what-each-source-tells-you)
5. [Pitfalls](#pitfalls)

## Before you start

- To open the versions page you need edit or read rights on the object.
- The audit needs the policy `audit/read`; without it there is no Audit tab. Ask the web team if you need it.

## Steps with the versions page

1. Open the page, **Manage versions** (in the edit form's left column, or the item's menu).
2. Each card shows the version number, status (*Published*, *Archived*, *Draft* ...), the creator, created and
   modified times, and the translation changed.
3. Filter by **Creator** or **Status**; **Compare** shows what a version changed
   ([Compare two versions](compare-two-versions.md)).

## Steps with the Audit tab

1. Open the page in the administration; choose the **Audit** tab on the node.
2. It lists recorded events for this content: publishing, moves, hiding, removal, state changes, with the user and the
   time.
3. For more, the **Audit** area of the administration has a timeline with filters and search (`/audit/dashboard`).

## What each source tells you

| Question | Versions | Audit |
|---|---|---|
| Who wrote this text | yes (creator of the version) | yes (who published) |
| Who moved, hid or removed it | no | yes |
| Who approved it | the collaboration item | the publish event |
| What changed in the text | yes (**Compare**) | no (records actions, not texts) |
| Older than the version limit | no (old versions are removed at the limit) | yes, within the audit's retention |

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Expecting text in the audit | the audit records actions, never content values |
| Publishing personal conclusions about a colleague | use the information for the purpose you were given it for |
| Versions removed | removal is final; the audit still records that it happened |

## Related pages

- [Publishing tasks](README.md)
- [Compare two versions](compare-two-versions.md)
- [Security and audit](../../../../guides/security-and-audit.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
