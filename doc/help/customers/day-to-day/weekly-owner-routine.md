---
title: Weekly owner routine
audience: customer, project-manager, admin
category: customer-success
order: 26
summary: One hour a week for the owner or manager of an Exponential site - the dashboard, pending approvals and waiting processes, the trash, accounts, the calendar, search phrases and the help dashboard - with what to hand to whom and a monthly and yearly add-on.
keywords: weekly routine, site owner, checklist, dashboard, approvals, workflow processes, trash, accounts, content calendar, monthly, yearly
type: help
---
# Weekly owner routine

This page is for the owner or manager who wants to keep an eye on the site without doing the editors' work. One hour
a week, at the same time, is enough for most sites. Each step names the page in the administration and what to do
with what you find. Some steps need Setup pages; if your role does not show them, do those steps with your
administrator or ask for read access.

## Contents

1. [The hour](#the-hour)
2. [What to hand to whom](#what-to-hand-to-whom)
3. [Once a month, add](#once-a-month-add)
4. [Once a year, add](#once-a-year-add)
5. [Record](#record)

## The hour

| Minutes | Look at | You are checking | Help |
|---|---|---|---|
| 5 | The dashboard: recent content | what was published matches the plan | [A tour of the administration](../../admin-tour.md) |
| 10 | The [Content calendar](content-calendar.md) | next two weeks have owners and approvers | [Content calendar](content-calendar.md) |
| 5 | Approvers' *My pending items*, or ask them | nothing waits that should be out | [Approvals and review](approvals-and-review.md) |
| 5 | **Setup > Workflow processes** | no process waits for days; the status bar says the workflow cronjob is scheduled | [Workflows](../../../guides/workflows.md) |
| 5 | The trash, filtered by this week | nothing removed by mistake | [Archiving and the trash](archiving-and-the-trash.md) |
| 5 | **Users > Check user accounts**, new accounts and **Locked** | every account belongs to someone current | [Check user accounts](../../../features/6.0/user-hygiene.md) |
| 10 | **Setup > Search statistics**, phrases without results | ideas for content | [Site search and what visitors look for](site-search-and-what-visitors-look-for.md) |
| 5 | Form submissions, if your site collects them | someone answers them | [Collected information](../../../guides/collected-information.md) |
| 5 | The site itself, signed out, on a phone | the front page and one new page look right | [Accessibility upkeep](accessibility-upkeep.md) |
| 5 | Notes for the partner or host | anything slow, broken or confusing, with address and time | [When something goes wrong](../../when-something-goes-wrong.md) |

> **Tip**
> The help has its own dashboard at `/exphelp/dashboard` in the administration, and a report page at
> `/exphelp/report` for errors in the help or the software
> ([When something goes wrong](../../when-something-goes-wrong.md)).

## What to hand to whom

| You found | Hand it to |
|---|---|
| A late or missing item | its owner in the calendar |
| A version waiting too long | the approver, or a replacement approver |
| A process stuck, or "not scheduled in the crontab" | administrator or host ([Cronjobs](../../../guides/cronjobs.md)) |
| A removal by mistake | the editor; restore from the trash |
| An account nobody recognizes | account manager: disable it ([Offboarding staff](offboarding-staff.md)) |
| Phrases without results | editor-in-chief, as calendar topics |
| An error page with a reference | administrator, with the reference ([Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md)) |
| A bug in the software | the issue tracker, see [When something goes wrong](../../when-something-goes-wrong.md) |
| A security problem | never in public: e-mail security@se7enx.com, as `SECURITY.md` describes |

## Once a month, add

- [ ] The [SEO monthly checklist](seo-monthly-checklist.md).
- [ ] Each editor cleans their old drafts ([Drafts, versions and undo](drafts-versions-and-undo.md)).
- [ ] Delete from the trash what is older than your agreed period.
- [ ] The monthly report ([Analytics and what to measure](analytics-and-what-to-measure.md)).
- [ ] If you run a newsletter: the [Newsletter routine](../growth/newsletter-routine.md) review.

## Once a year, add

- [ ] An [access review](../../businesses/security/access-reviews.md) with the [Team roles worksheet](team-roles-worksheet.md).
- [ ] A [content audit](content-audits.md) of one or two areas.
- [ ] The [accessibility spot check](accessibility-upkeep.md), if not done quarterly.
- [ ] A look at the plan for the site: [Growth roadmap worksheet](../growth/growth-roadmap-worksheet.md).
- [ ] The yearly checklist of the businesses section ([Annual review checklist](../../businesses/templates/annual-review-checklist.md)).

## Record

```text
WEEK: __________   Done by: ______________   Time: ______
Calendar on track? yes / no   Waiting approvals: ____   Stuck processes: ____
Trash restores: ____   Accounts disabled: ____   Locked accounts: ____
Phrases without results taken up: ____________________________________
Sent to partner/host: ________________________________________________
```

## Related pages

- [Running your site day to day](README.md)
- [Monthly operations checklist](../../businesses/templates/monthly-operations-checklist.md)
- [Operating a site](../../../guides/operating-a-site.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
