---
title: What to ask your partner
audience: customer, partner, businesses
category: customer-success
order: 16
summary: The questions a site owner should put to the implementation partner or developer who built or maintains an Exponential site - the code they made, updates, documentation, support, ownership and handover - with what a good answer contains.
keywords: partner, agency, developer, questions, review, custom code, extensions, documentation, ownership, handover, support
type: help
---
# What to ask your partner

Your partner (an agency, a freelance developer or an implementation partner of the Exponential program) built the
site or keeps it changing. This page lists what to ask them, at the start of a contract and once a year. For
choosing a new partner, the detailed interview questions are in
[Questions to ask a partner](../../businesses/choosing/questions-to-ask-a-partner.md).

## The site they built

| Ask | A good answer |
|---|---|
| Which extensions does the site use, and who maintains each? | A list with versions; **Setup > Extensions** shows them ([Extension list](../../../features/6.0/extension-list-and-downloads.md)) |
| Which parts are custom: templates, designs, extensions, content classes? | A list, and where the code is kept |
| Did you change any files of Exponential itself? | Ideally none; any change is kept in version control and shows in the upgrade check ([Upgrade check and file consistency](../../businesses/reliability/upgrade-check-and-file-consistency.md)) |
| Where is the code of our site kept? | A Git repository you own or can copy ([Choosing a partner](../../businesses/choosing/choosing-a-partner.md)) |
| Is there a document that describes the site? | A handover document: content model, roles, settings, contacts ([Project templates](../../leadership/project-templates.md)) |

## Updates and the long term

- How do you keep the site on the maintained 6.0 line ([Release line and support](release-line-and-support.md))?
- Which of our custom parts need work at the next upgrade, or a PHP upgrade?
- Do you contribute general fixes back to the project, so we do not carry them alone
  ([Contributing code back](../../partners/contributing-code-back.md))?
- Who reads the changelog and tells us what changes?

## Support

- How do we report a problem, and what do you need from us ([Reporting problems](reporting-problems.md))?
- What is your first response time per severity, in which hours ([Support agreement template](../partners-and-hosts/support-agreement-template.md))?
- Where is the line between your work and the host's ([Who does what](../partners-and-hosts/who-does-what.md))?
- If the problem is in Exponential itself, do you file the issue upstream and follow it
  ([GitHub issues](github-issues.md))?

## Changes

- How do we request a change, how is it estimated and approved ([Change requests](../partners-and-hosts/change-requests.md))?
- Is each change tried on a copy before it goes live?
- Can we see a dry run of a deploy? The one-command deploy prints every step first
  ([Graceful deploys](../../businesses/reliability/graceful-deploys.md)).

## Training and documentation

- Have our editors been shown the help in their administration, the help dashboard?
- Which help pages should our new staff read first ([Getting started](../../getting-started.md),
  [Editing content](../../editing-content.md))?

## Ownership and exit

- Do we own the designs, templates and extensions you made for us? Is it written down
  ([Contracts and SLA basics](../../businesses/choosing/contracts-and-slas-basics.md), not legal advice)?
- Exponential is under the GPL; a partner who builds a site and hands the code to you "distributes it to you, and
  you receive the GPL rights, including the source" (same page).
- If we end the contract, what do you hand over, and how fast ([Handover checklist](../partners-and-hosts/handover-checklist.md))?

> **Tip**
> A partner of the Exponential program has a level and published support levels of its own with the project
> ([Partner levels](../../partners/partner-levels.md)). The level tells you about their commitment to the program; it
> "is not a guarantee of quality for your project" ([Choosing a partner](../../businesses/choosing/choosing-a-partner.md)).

## Related pages

- [What to ask your host](what-to-ask-your-host.md)
- [Choosing a partner](../partners-and-hosts/choosing-a-partner.md)
- [Reviewing your provider each year](../partners-and-hosts/reviewing-your-provider-each-year.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
