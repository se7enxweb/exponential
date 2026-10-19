---
title: Features customers often miss
audience: customer, editor, admin
category: customer-success
order: 60
summary: Tools that ship with Exponential and save a team time, but that owners and editors often do not know they have - in content work, tidying up, the store, administration and help - each with where to find it and whether a setting or permission is needed.
keywords: hidden features, time savers, content finder, content checks, sub-items CSV export, publish without notification, keyboard shortcuts, help center, permanent receipts, order sorting, user hygiene
type: help
---
# Features customers often miss

This page is for owners and senior editors who want more from the site they already have. Each tool below ships with
Exponential, and each one replaces manual work that teams often do by hand. Some need a setting or a permission; the
table says so. Show this page to your team, and to your partner when you ask what to switch on.

## Content work

| Tool | What it saves | Where | Needs |
|---|---|---|---|
| **Copy selected** in the sub-items list | Copying many items one by one | The sub-items list of a node ([Copy selected sub-items](../../../features/6.0/subitems-copy-selected.md)) | Content permissions |
| Sub-items **Table options** with columns, presets and CSV export | Building lists by hand for a report | The sub-items list ([The sub-items list](../../../features/6.0/subitems-table-options.md)) | Nothing extra |
| Content jobs for large removes, copies and moves | Operations that failed on time limits | Offered by the administration for big subtrees ([Content jobs](../../../features/6.0/content-jobs.md)) | Nothing extra |
| **Publish without notification** | Mails to every subscriber for a typo fix | Next to the publish button ([Publish without notification](../../../features/6.0/publish-without-notification.md)) | `notification.ini` setting and the policy `content/publish_without_notification` |
| Version preview in other view modes | Publishing just to see a print or PDF layout | `content/versionview` with a view mode ([Version preview in other view modes](../../../features/6.0/version-preview-view-modes.md)) | Nothing extra |
| Bookmark folders | Searching for the same places every day | The sidebar and browse dialog ([Bookmark folders](../../../features/6.0/bookmark-folders.md)) | Nothing extra |
| Keyboard shortcuts, such as `/` for search | Mouse trips | Everywhere in the administration ([Keyboard shortcuts](../../keyboard-shortcuts.md)) | Works in the Admin 4 design; the other designs have most of them |

## Tidying up

| Tool | What it saves | Where | Needs |
|---|---|---|---|
| The content finder | Hunting for the content behind an id, an address or a file path | **Setup > Content finder** ([The content finder](../../../features/6.0/content-finder.md)) | `contentfinder/search` |
| Content checks: unreferenced assets, broken relations, missing files | Guessing which images and files nobody uses | `/contentcheck/...` ([The content checks](../../../features/6.0/content-checks.md)) | `contentcheck/unreferenced`, `contentcheck/brokenrelations` |
| The reverse relation check before remove or hide | Broken links after a removal | Shown on the remove page, shipped as `warn` ([Removing and hiding](../../../features/6.0/reverse-relation-check.md)) | Nothing extra |
| The trash shows who trashed an item and where it was | Asking around who removed something | The trash ([The trash view](../../../bc/6.0/trash.md)) | Nothing extra |
| **Check user accounts** | Opening accounts one by one to find unused or duplicate ones | **Users > Check user accounts** ([Check user accounts](../../../features/6.0/user-hygiene.md)) | User administration rights |

## The store

| Tool | What it saves | Where | Needs |
|---|---|---|---|
| Sorting the order list by any column, kept in the address | Scrolling for the largest or oldest orders | **Store > Orders** ([Order list sorting](../../../features/6.0/order-list-sorting.md)) | `shop/administrate` |
| The dashboard's "How your shop works" | Explaining the checkout to a new colleague or partner | **Store** ([Store dashboard and order statuses](../../../features/6.0/store-dashboard.md)) | `shop/administrate` |
| Permanent order receipts | "I lost my order" calls from guests | One setting ([Invoices and receipts](../commerce/invoices-and-receipts.md)) | `shop.ini [OrderViewSettings] OrderLinkView=orderreceipt` |
| Limits on status changes | Mistakes by staff who should only pack and ship | Roles with `shop/setstatus` limited by status ([Orders day to day](../commerce/orders-day-to-day.md)) | An administrator builds the role |

## Administration and help

| Tool | What it saves | Where | Needs |
|---|---|---|---|
| The help center inside the administration | Searching the internet for how-tos | `/exphelp/dashboard`, and the **Help** button on each page ([The help system](../../../features/6.0/help-system.md)) | The policy `exphelp/read` |
| The help's report page | Writing a bug report from scratch | `/exphelp/report`, with a prefilled issue and diagnostics to copy (same page) | `exphelp/read` |
| Signed links in the "New user registered" mail | Searching for a new account to approve it | The mail itself ([Signed links in the registration mail](../../../features/6.0/registration-admin-links.md)) | Registration feedback by mail |
| Maintenance mode with a preview | A blank or broken site during planned work | **Setup > Maintenance**, or one command ([Maintenance mode](../../../guides/maintenance-mode.md)) | Administrator |
| Pausing cron job parts | Editing the crontab on every server during a release | **Setup > Cronjobs** ([Pausing cronjob parts](../../../features/6.0/cronjob-pause.md)) | Administrator |
| PDF export with your own footer | Copying content into a word processor for a PDF | **Setup > PDF export** ([PDF export](../../../features/6.0/pdf-export.md)) | The policy `pdf/edit` |

## How to bring them in

1. Pick three tools from this page that match work your team does by hand today.
2. Ask your administrator or partner which need a setting or a policy, and to switch them on for the right roles.
3. Show them to the team in ten minutes at a regular meeting.
4. After a month, ask whether they are used. Keep what helps.

> **Tip**
> The Store dashboard's "Next steps" and the help center's search are the two places where the software itself
> suggests what to do next. Make reading them a habit.

## Related pages

- [Feature tour](feature-tour.md)
- [Cost of ownership for owners](cost-of-ownership-for-owners.md)
- [Admin tour](../../admin-tour.md)
- [Keyboard shortcuts](../../keyboard-shortcuts.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
