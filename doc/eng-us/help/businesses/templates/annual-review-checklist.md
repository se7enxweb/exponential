---
title: Annual review checklist
audience: businesses, project-manager, admin
category: business-solutions
order: 90
summary: A copy-ready yearly review of an Exponential site for owners and managers - goals and figures, costs and ROI, availability, versions and updates, security and access, data protection and retention, backups, partners and contracts, and the plan for the next year.
keywords: annual review, yearly review, checklist, governance, ROI, costs, availability, updates, PHP, security, access review, data protection, retention, backups, contracts, roadmap
type: help
---
# Annual review checklist

This page is for owners and managers who take a step back once a year to ask whether the site still serves the
business, is still safe, and still costs what was planned. It pulls together the yearly tasks from the other pages
of this section into one meeting. Plan about two hours with the site owner, the administrator or partner, and
whoever answers for data protection.

> **Note**
> The review is about your own figures and decisions. Where legal or contractual duties are involved (data
> protection, accessibility, bookkeeping), ask your advisers; this checklist is not legal advice.

## Contents

1. [Before the meeting](#before-the-meeting)
2. [The checklist](#the-checklist)
3. [Outcomes to record](#outcomes-to-record)
4. [How to use the result](#how-to-use-the-result)

## Before the meeting

Collect:

- [ ] The twelve monthly reports ([Monthly operations checklist](monthly-operations-checklist.md#the-report-to-management)).
- [ ] Invoices for hosting, support and services of the year.
- [ ] The figures you named as goals (sales, sign-ups, time saved), from your own systems.
- [ ] The last access review and retention schedule.
- [ ] Incident notes and downtime hours.
- [ ] Key figures the site produces itself ([KPIs from logs and audit](../../leadership/kpis-from-logs-and-audit.md)).

## The checklist

```text
ANNUAL REVIEW - <site>   Year: <year>   Date: <date>
Participants: <names and roles>

1. GOALS AND RESULTS                                                    Status  Action
[ ] Goals from the project brief: met / partly / not                     ____    ______
[ ] Measured figures compared with last year                             ____    ______
[ ] Are the goals still right for next year?                             ____    ______

2. COSTS AND ROI
[ ] Actual costs vs budget (one-time, running, third-party)              ____    ______
[ ] ROI worksheet updated with measured figures                          ____    ______
[ ] Maintenance budget for next year set                                 ____    ______

3. AVAILABILITY AND INCIDENTS
[ ] Hours of downtime, planned and unplanned                             ____    ______
[ ] Incidents reviewed; lessons applied                                  ____    ______
[ ] Service levels met by host/partner                                   ____    ______

4. VERSIONS AND UPDATES
[ ] Site on the newest 6.0.x release                                     ____    ______
[ ] PHP version still supported; upgrade planned if not                  ____    ______
[ ] Extensions current; unused extensions removed                        ____    ______
[ ] Custom code kept in extensions (upgrade check clean)                 ____    ______

5. SECURITY AND ACCESS
[ ] Access review done and signed                                        ____    ______
[ ] Two-step sign-in in place as decided                                 ____    ______
[ ] Audit alerts reached a person all year                               ____    ______
[ ] Content Security Policy: still report-only where enforce is possible ____    ______
[ ] Staff know how to report a vulnerability privately                   ____    ______

6. DATA PROTECTION AND RETENTION (with your adviser)
[ ] Data inventory still complete (new forms, classes, extensions?)      ____    ______
[ ] Retention periods still right; settings match                        ____    ______
[ ] Requests to see or delete data handled; process works                ____    ______
[ ] Privacy notice and mail footer current                               ____    ______

7. BACKUPS AND RECOVERY
[ ] Restore tests done this year (dates: ____)                           ____    ______
[ ] Backup retention matches the retention schedule                      ____    ______

8. ACCESSIBILITY AND CONTENT
[ ] Accessibility checked on main pages and forms                        ____    ______
[ ] Outdated content removed or updated                                  ____    ______

9. PARTNERS AND CONTRACTS
[ ] Host and partner performance scored                                  ____    ______
[ ] Contract terms, prices and exit terms still acceptable               ____    ______
[ ] Renewal or re-tender decision                                        ____    ______

10. NEXT YEAR
[ ] Planned improvements and their budget                                ____    ______
[ ] Planned upgrades (PHP, OS, migrations) and windows                   ____    ______
[ ] Owners named for each action                                         ____    ______

Approved by: ______________   Next review: <date>
```

Where each part is explained:

| Part | Page |
|---|---|
| 1, 2 | [Return on investment of stability](../costs/roi-of-stability.md), [ROI worksheet template](roi-worksheet-template.md) |
| 3 | [Cost of downtime worksheet](../costs/cost-of-downtime-worksheet.md), [Incident response for small teams](../reliability/incident-response-for-small-teams.md) |
| 4 | [Budgeting for upgrades and maintenance](../costs/budgeting-for-upgrades-and-maintenance.md), [Security updates and disclosure](../security/security-updates-and-disclosure.md) |
| 5 | [Access reviews](../security/access-reviews.md), [User accounts and passwords](../security/user-accounts-and-passwords.md), [Content Security Policy](../../../features/6.0/content-security-policy.md) |
| 6 | [Personal data in Exponential](../security/personal-data-in-exponential.md), [Data retention and deletion](../security/data-retention-and-deletion.md) |
| 7 | [Restore drills](../reliability/restore-drills.md) |
| 8 | [Accessibility, security and compliance checklists](../../leadership/accessibility-security-and-compliance-checklists.md) |
| 9 | [Vendor evaluation scorecard](vendor-evaluation-scorecard.md), [Contracts and SLA basics](../choosing/contracts-and-slas-basics.md) |

## Outcomes to record

```text
ANNUAL REVIEW OUTCOMES - <site> - <year>
Overall status: green / amber / red
Top three achievements: 1) ______ 2) ______ 3) ______
Top three risks: 1) ______ 2) ______ 3) ______
Decisions: ______________________________________________
Actions (what - who - by when):
1) ______ - ______ - ______
2) ______ - ______ - ______
3) ______ - ______ - ______
Budget for next year: one-time ______ running ______
```

## How to use the result

- Send the outcomes to management and to your host or partner.
- Turn actions into tickets with owners and dates.
- Use the risks as input for next year's budget and contract talks.
- If the site's story is worth telling and the facts are measured, consider a case study, with approval
  ([Case study template](case-study-template.md)).

## Related pages

- [Monthly operations checklist](monthly-operations-checklist.md)
- [Reliability scorecard](../reliability/reliability-scorecard.md)
- [Leadership reading path](../../leadership/leadership-reading-path.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
