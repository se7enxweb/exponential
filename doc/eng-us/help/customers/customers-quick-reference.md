---
title: Quick reference for site owners
audience: customer, admin
category: customer-success
order: 7
summary: Where the things a site owner looks at live in the administration of Exponential, with the menu path and the address: dashboard, content, store, users, workflows, logs, audit, maintenance, upgrade check and the help.
keywords: quick reference, administration, menu, address, URL, dashboard, store dashboard, check user accounts, workflow processes, log files, audit, maintenance, upgrade check, content finder, help, report
type: help
---
# Quick reference for site owners

A one-page list of the places a site owner visits in the administration, with the menu path and the address after
the administration's address (for example `https://www.example.com/admin`). What you see depends on your role: an
editor sees fewer tabs than an administrator ([A tour of the administration](../admin-tour.md)).

Each line names the page in the repository where it is described, so you can read more or point your partner to it.

## Every day

| What | Menu | Address | Described in |
|---|---|---|---|
| The dashboard: your drafts, pending items, latest content | **Dashboard** | `/content/dashboard` | [Admin tour](../admin-tour.md#the-dashboard) |
| The content tree | **Content structure** | | [Admin tour](../admin-tour.md#the-left-column) |
| Images and files | **Media** | | [Media and uploads](../media-and-uploads.md) |
| Open an item by its number, path or address | **Content finder** below the tree, or **Setup > Content finder** | `/contentfinder/search` | `doc/en/us/features/6.0/content-finder.md` |
| The store at a glance | **Store** (first entry) | `/shop/dashboard` | [Store dashboard](../../features/6.0/store-dashboard.md) |
| Search all content | **Search...** in the header (press `/`) | | [Searching](../searching.md) |

## Every week or month

| What | Menu | Address | Described in |
|---|---|---|---|
| Processes that wait (approvals, scheduled publishing) | **Setup > Workflow processes** | `/workflow/processlist` | [Workflows](../../guides/workflows.md) |
| Old, locked and duplicate user accounts | **Users > Check user accounts** | `/user/hygiene` | [Check user accounts](../../features/6.0/user-hygiene.md) |
| Assets nobody uses, broken relations, unused files | Content checks | `/contentcheck/unreferenced/<node id>`, `/contentcheck/brokenrelations/<node id>`, `/contentcheck/storage/<node id>` | [Content checks](../../features/6.0/content-checks.md) |
| Who did what | **Audit** | `/audit/dashboard` | [Audit trail](../../features/6.0/audit-trail.md) |
| The log files | **Setup > Log files** | `/setup/logs` | [Log viewer](../../features/6.0/log-viewer.md) |
| Printable exports | **Setup > PDF export** | `/pdf/list` | [PDF export](../../features/6.0/pdf-export.md) |

## When something changes

| What | Menu | Address | Described in |
|---|---|---|---|
| Close the site for planned work | **Setup > Maintenance** | `/setup/maintenance` | [Maintenance mode](../../features/6.0/maintenance-mode.md) |
| Check the installation after an upgrade | **Setup > Upgrade check** | `/setup/systemupgrade` | [Upgrade check](../../guides/upgrade-check.md) |
| Workflows and their triggers | **Setup > Workflows** | `/workflow/grouplist` | [Workflows](../../guides/workflows.md) |

These pages are usually used by your administrator or partner. As an owner it helps to know that they exist, so you
can ask the right question: "Is the upgrade check clean?", "Is maintenance mode off again?".

## The help

| What | Address | Described in |
|---|---|---|
| The help dashboard | `/exphelp/dashboard` | [Help system](../../features/6.0/help-system.md) |
| The landing page of an audience, such as this one | `/exphelp/audience/customer` | same |
| How to report an error | `/exphelp/report` | [The help's report page](healthy/the-help-report-page.md) |

## Your own account

The person button in the header opens your user menu: **Change information**, **Change password** and **My e-mail
preferences** ([Admin tour](../admin-tour.md#the-header)). The left column of the dashboard lists **My pending
items**, **My drafts**, **My bookmarks** and **My notification settings**.

## Related pages

- [A tour of the administration](../admin-tour.md)
- [Customers: start here](customers-start-here.md)
- [Keyboard shortcuts](../keyboard-shortcuts.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
