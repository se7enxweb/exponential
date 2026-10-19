---
title: "Worked example: an intranet"
audience: businesses, project-manager, admin
category: business-solutions
order: 50
summary: A step-by-step cost and benefit calculation for a staff intranet on Exponential, where the return comes from time saved rather than sales, using only labeled example assumptions and a realistic discount for time that is not really freed.
keywords: intranet, worked example, time saved, productivity, TCO, ROI, payback, employees, roles, two-step sign-in, LDAP, budget
type: help
---
# Worked example: an intranet

This page is for managers who plan an intranet, the internal site where staff find news, documents and procedures,
and must show what it is worth. An intranet sells nothing, so its return is time: minutes saved every week by
every employee. The page shows how to turn that into a figure finance will accept.

> **Note**
> The organization, its staff numbers, times and costs are **Example assumptions**: round numbers in a neutral
> currency unit. They are not survey results or market data. Replace each one with your own figures.

## Contents

1. [What the intranet uses](#what-the-intranet-uses)
2. [The inputs](#the-inputs)
3. [Step 1: the costs](#step-1-the-costs)
4. [Step 2: the value of time saved](#step-2-the-value-of-time-saved)
5. [Step 3: ROI and payback](#step-3-roi-and-payback)
6. [How to measure the time saved](#how-to-measure-the-time-saved)
7. [What to change for your intranet](#what-to-change-for-your-intranet)

## What the intranet uses

| Need | What Exponential provides | Source |
|---|---|---|
| Who sees what | "Fine grained role based permissions system", sections | `README.md`, "Main Exponential features"; [Roles and policies](../../../guides/roles-and-policies.md), [Sections](../../../guides/sections.md) |
| Approval before publishing | "Content approval and scheduled publication", workflows | `README.md`; [Workflows](../../../guides/workflows.md) |
| Safer sign-in | Two-step sign-in with an authenticator app or e-mail codes | [Two-step sign-in](../../../guides/two-factor-login.md) |
| Company directory sign-in | An LDAP login handler (`settings/ldap.ini`, `kernel/classes/datatypes/ezuser/ezldapuser.php`) | [Directory sign-in and LDAP](../intranets/directory-sign-in-and-ldap.md) |
| Documents | File uploads, version control, search | `README.md`; [Files and downloads](../../creators/website-creators/files-and-downloads.md) |
| Staff notifications | Subscriptions to new content by e-mail | [Notifications](../../../guides/notifications-administrator.md) |
| Proof of who changed what | The audit trail | [The audit trail](../../../features/6.0/audit-trail.md) |

No per-user license applies, so the cost does not grow with the number of staff accounts
([What you do not pay for](what-you-do-not-pay-for.md)).

## The inputs

| Input | Value |
|---|---|
| Example assumption: employees using the intranet | 100 |
| Example assumption: minutes saved per employee per week | 15 |
| Example assumption: working weeks per year | 40 |
| Example assumption: average staff cost per hour | 40 currency units |
| Example assumption: share of saved time that turns into useful work | 50 % |
| Example assumption: setup by a partner | 25,000 currency units, once |
| Example assumption: training for editors and staff | 2,000 currency units, once |
| Example assumption: hosting | 150 currency units a month |
| Example assumption: administration | 50 hours a year at 50 currency units an hour |
| License fee, also per user | 0 (fact: `README.md`, "License"; `LICENSE.md` section 0) |

## Step 1: the costs

1. One-time: 25,000 + 2,000 = **27,000**.
2. Hosting per year: 150 x 12 = **1,800**.
3. Administration per year: 50 x 50 = **2,500**.
4. Running cost per year: 1,800 + 2,500 = **4,300**.
5. First year: 27,000 + 4,300 = **31,300**.
6. Five years: 27,000 + (4,300 x 5) = 27,000 + 21,500 = **48,500**.

## Step 2: the value of time saved

1. Minutes saved per year: 100 x 15 x 40 = **60,000** minutes.
2. In hours: 60,000 / 60 = **1,000** hours.
3. At staff cost: 1,000 x 40 = **40,000** currency units.
4. Only part of saved time becomes useful work, so apply the 50 % assumption: 40,000 x 50 % = **20,000** a year.

> **Tip**
> The discount in step 4 is what makes the case believable. Finance teams know that ten minutes saved here and there
> do not all become productive time. State your factor openly and explain how you chose it.

## Step 3: ROI and payback

First year: 20,000 - 31,300 = **-11,300**. The first year does not pay back; that is normal for an intranet.

Five years:

1. Benefits: 20,000 x 5 = **100,000**.
2. Costs: **48,500**.
3. Net benefit: 100,000 - 48,500 = **51,500**.
4. ROI: 51,500 / 48,500 x 100 % = about **106 %**.

Payback:

1. Monthly net benefit after running costs: (20,000 - 4,300) / 12 = about **1,308**.
2. Payback of the one-time cost: 27,000 / 1,308 = about **20.6 months**.

## How to measure the time saved

Do not guess the minutes; measure them before and after:

- [ ] Before the project, ask a sample of staff to note for one week how long they search for documents, phone
      numbers and procedures.
- [ ] Count repeated questions to the help desk or HR that an intranet page could answer.
- [ ] After launch, compare the same notes and counts after three months.
- [ ] Use the site's own figures: searches and their results, and the audit trail's record of publishing activity
      ([KPIs from logs and audit](../../leadership/kpis-from-logs-and-audit.md)).
- [ ] Write the measured value back into the calculation at the [annual review](../templates/annual-review-checklist.md).

## What to change for your intranet

- [ ] Your staff numbers, working weeks and cost per hour from HR.
- [ ] A realization factor you can defend.
- [ ] Lines for directory integration, single sign-in or document migration if your project needs them.
- [ ] A line for an access review each year ([Access reviews](../security/access-reviews.md)).
- [ ] A rollout plan: [Intranet rollout checklist](../intranets/intranet-rollout-checklist.md).

## Related pages

- [Intranets](../intranets/README.md)
- [Intranet planning](../intranets/intranet-planning.md)
- [Total cost of ownership](total-cost-of-ownership.md)
- [ROI worksheet template](../templates/roi-worksheet-template.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
