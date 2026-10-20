---
title: Change requests
audience: customer, partner, project-manager
category: customer-success
order: 13
summary: How the owner of an Exponential site asks a partner or host for a change - what to write, how it is estimated and approved, testing on a copy, the dry run, when it goes live, and how it is recorded - with a request form to copy.
keywords: change request, request a change, estimate, approval, staging, dry run, deploy, change log, scope, partner
type: help
---
# Change requests

A change request is how your team asks a partner or host for something new or different on the site: a new page
type, a design change, a new language, a settings change. A clear request gets a clear estimate and fewer surprises.
This page describes the owner's side; the project managers' templates are in
[Project templates](../../leadership/project-templates.md).

## Is it a change, or something you can do yourselves?

Many things need no provider at all: content, menus that are content, images, translations, roles and accounts. The
editor pages show how ([Editing content](../../editing-content.md), [Languages](../../languages.md),
[Layouts editor](../../layouts-editor.md)). A change request is for templates, designs, extensions, the content model,
settings and the server.

## What to write

| Item | Why |
|---|---|
| What you want, in your own words | The need, not only a solution |
| Why: the business reason | Helps the provider suggest a simpler way |
| Where: pages, sites, languages affected | Sets the scope |
| Who uses it: visitors, editors, staff | Sets the tests |
| When it is needed, and any fixed date | A campaign, a law, an event |
| How you will know it is done | The acceptance check |
| Examples or sketches | Saves a round of questions |

## How it should go

1. **Estimate in writing**, with what is and is not included. "A change request is a new row with its own estimate"
   ([Estimating and scoping with extensions](../../leadership/estimating-and-scoping-with-extensions.md)).
2. **Approval** by the person named in your contract.
3. **Built and tested on a copy** of the site, never first on the live one
   ([Graceful deploys](../../businesses/reliability/graceful-deploys.md)).
4. **You check it** on the copy against your acceptance check.
5. **A dry run of the deploy.** The deploy command can print every step without changing anything; the business guide
   says to "ask for it to be part of every change request" (same page).
6. **Live at an agreed time**, not right before a peak and not at the end of the week (same page).
7. **Checked after** going live: front page, a deep page, the administration, and what the change touched.
8. **Recorded.** The audit trail records deploys in its `system` channel; keep a line in your own change log too
   (same page).

## When a change needs a maintenance window

Most changes go live without visitors noticing. Some, such as a database change or a large import, may need the site
offline for a short time; ask whether yours does, and plan it ([Maintenance mode and planned work](../healthy/maintenance-mode-and-planned-work.md)).

## Every change can be undone

Ask before approval: "How do we go back?" The business rule is that "the previous version of the code is in Git, and
a backup exists when the database changes" ([Graceful deploys](../../businesses/reliability/graceful-deploys.md)).

## A request form to copy

```text
CHANGE REQUEST                              Number: ____    Date: ____
Requested by:        ____ (role)
Approver:            ____ (role)
What we want:        ____
Why:                 ____
Where:               ____ (sites, languages, page types)
Who uses it:         ____
Needed by:           ____ (and why that date)
Done when:           ____ (acceptance check)
Attachments:         ____
-- provider --
Estimate:            ____   Included: ____   Not included: ____
Needs a maintenance window: yes / no
Way back:            ____
Approved:            ____ (name, date)
Tested on the copy:  ____ (date, by)
Live:                ____ (date, time)   Checked by: ____
```

## Related pages

- [Who does what](who-does-what.md)
- [What to ask your partner](../healthy/what-to-ask-your-partner.md)
- [Preview before you publish](../../businesses/reliability/preview-before-you-publish.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
