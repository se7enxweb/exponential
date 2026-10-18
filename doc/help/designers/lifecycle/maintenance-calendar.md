---
title: "A maintenance calendar"
audience: designer, project-manager
category: theme-lifecycle
order: 370
summary: A rhythm for theme maintenance - weekly watching, per-release testing and audits, quarterly dependency and license reviews, a yearly review of support policy and compatibility - as a calendar to adapt.
keywords: maintenance calendar, schedule, routine, weekly, quarterly, yearly, rhythm, plan
type: help
---
# A maintenance calendar

This page is for designers who want maintenance to happen without remembering it. Adapt the rhythm to the time you
have; a smaller calendar kept is better than a large one ignored.

## The rhythm

| When | Task | Page |
|---|---|---|
| Weekly | Read new issues; watch Exponential changes | [Following Exponential releases](following-exponential-releases.md) |
| Each Exponential release | Upgrade test, then a theme release if needed | [Upgrade testing, step by step](upgrade-test-step-by-step.md) |
| Each theme release | Accessibility, CSP, visual and performance audits | [Accessibility](accessibility-audit-per-release.md), [CSP](csp-audit-per-release.md) |
| Each security report | Handle at once | [Handling security reports](handling-security-reports.md) |
| Quarterly | Dependencies and their licenses; old deprecations due | [Keeping dependencies up to date](dependency-updates.md), [A deprecation policy](deprecation-policy.md) |
| Yearly | Support policy, compatibility matrix, PHP range, screenshots, README | [A support policy](support-policy-for-your-theme.md), [A compatibility matrix](compatibility-matrix.md) |

## Step by step

1. Put the rows into your calendar or as recurring issues in the theme's repository.
2. Keep a short log of each cycle in an issue: what was checked, what was released.
3. Review the rhythm once a year.

## Related pages

- [Checklist: one maintenance cycle](lifecycle-checklist.md)
- [Maintenance subscriptions](../sales/maintenance-subscriptions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
