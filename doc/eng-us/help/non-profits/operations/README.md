---
title: Operations for non-profits
audience: non-profit, businesses, admin, hosting
category: business-solutions
order: 10
summary: Running a non-profit's Exponential site for years on a small budget - hosting choices, SQLite and Velocity for small teams, protecting donor and volunteer data, a backup and update routine, handling volunteers who leave, and working with a partner (the partner program publishes no non-profit pricing) - with who usually does each job.
keywords: operations, running costs, hosting, SQLite, Velocity, backups, updates, data protection, donor data, volunteer data, offboarding, partner, maintenance, small team, non-profit
type: help
---
# Operations for non-profits

This folder is for the person who keeps the site running: a staff member, a technical volunteer, a hosting company or
a partner, and the director who is responsible in the end. Most of a website's cost and risk is not in building it,
but in running it for years. A small, written routine with a name next to every task is what keeps a non-profit's site
healthy.

## Contents

1. [The pages of this folder](#the-pages-of-this-folder)
2. [Who does what](#who-does-what)
3. [The minimum routine](#the-minimum-routine)
4. [When something goes wrong](#when-something-goes-wrong)

## The pages of this folder

| Page | What it answers |
|---|---|
| [Low-budget hosting](low-budget-hosting.md) | Hosting models, what each costs in money and time, what to ask a host |
| [SQLite and Velocity for small teams](sqlite-and-velocity-for-small-teams.md) | The two choices that make a site cheap to run, and their limits |
| [Donor and volunteer data protection](donor-and-volunteer-data-protection.md) | Where personal data lives, who sees it, how long you keep it |
| [Backups and updates routine](backups-and-updates-routine.md) | The routine that prevents expensive failures |
| [When volunteers leave](when-volunteers-leave.md) | Accounts, content ownership and knowledge when people go |
| [Working with a partner](working-with-a-partner.md) | Choosing and working with a partner; no non-profit pricing is published |

The compact version is [Low-budget operations](../../businesses/nonprofits/low-budget-operations.md).

## Who does what

| Task | Staff or trained volunteer | Technical volunteer, host or partner |
|---|---|---|
| Writing and publishing | Yes | |
| Processing forms and the newsletter | Yes | |
| Account reviews | Yes, with **Check user accounts** | |
| Backups | Checks they exist | Sets them up, tests restores |
| Updates of Exponential, extensions and PHP | | Yes |
| Server security | | Yes |
| Incidents | Notices and reports | Fixes |

Write the names into the [Roles and permissions worksheet](../templates/roles-and-permissions-worksheet.md). The
general split between owner, host and partner is described in
[Who does what](../../customers/partners-and-hosts/who-does-what.md).

## The minimum routine

| How often | What | Page |
|---|---|---|
| Weekly | Forms answered; donations checked; spam removed | [Processing volunteer applications](../volunteers/processing-volunteer-applications.md) |
| Monthly | Backups exist; updates checked; logs glanced at; accounts of leavers closed | [Monthly checklist](../checklists/monthly-checklist.md) |
| Quarterly | Restore test; access review | [Backups and updates routine](backups-and-updates-routine.md) |
| Yearly | Budget, partner and host review; statement and policies updated | [Annual checklist](../checklists/annual-checklist.md) |

## When something goes wrong

- The site is down: [When the site is down](../../customers/healthy/when-the-site-is-down.md).
- An error page with a reference: [When something goes wrong](../../when-something-goes-wrong.md).
- A security problem in Exponential itself: report it privately
  ([Private security reports](../../customers/healthy/private-security-reports.md)).
- Write down what happened: [Incident notes template](../../customers/healthy/incident-notes-template.md).
- Small teams: [Incident response for small teams](../../businesses/reliability/incident-response-for-small-teams.md).

## Related pages

- [Non-profits: start here](../non-profits-start-here.md)
- [Keeping the site healthy (Customers)](../../customers/healthy/README.md)
- [Hosting companies: start here](../../hosting/hosting-companies-start-here.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
