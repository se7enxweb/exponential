---
title: "More functions of the administration"
audience: admin, builder
category: books
order: 310
summary: The administrator functions the Learning book lists after content (trash, URL addresses, link management, RSS, caches, search statistics, system information, sections, PDF export, extensions, packages, notifications) matched to today's pages and guides.
keywords: administrator, trash, restore, URL translator, URL aliases, links, RSS, cache, search statistics, system information, sections, PDF export, extensions, packages, notification, Learning book
type: help
---
# More functions of the administration

After the content functions, the book walks through the other administrator functions of the **Setup** area. Almost every one has a full guide today. This table is the map from the book's heading to the page to read.

| The book's heading | What it is | Read |
|---|---|---|
| Removing and restoring objects | A removed object goes to the **Trash**; from there it can be restored (back to where it was, or to another place) or removed for good. Emptying the trash needs its own permission (`cleantrash`). | [The trash](../../guides/content-model-and-editing.md#7-the-trash), [Emptying the trash](../manual/how-do-i-empty-the-trash.md), [Trash: who and where](../../features/6.0/trash-who-and-where.md) |
| URL translation | The addresses of content come from the object names. **Global URL aliases** give a module view an address of your choosing, and **wildcards** map a whole group. | [Links, URL aliases, wildcards](../../guides/urls-and-aliases.md), [Giving a page an extra address](../manual/how-do-i-give-a-page-an-extra-address.md) |
| URL management | The list of every link used in content, with a check of whether it works. | [Finding and fixing broken links](../manual/how-do-i-find-and-fix-broken-links.md) |
| RSS export and import | Publish a branch as a feed, or import a feed into content. | [RSS feeds](../../guides/rss-feeds.md) |
| Cache administration | Clear the caches by name after a change that does not show. | [Caches](../../guides/caches.md), [Which cache do I clear?](../manual/which-cache-do-i-clear.md) |
| Search statistics | What visitors searched for and whether they found it. | [Links, URL aliases, wildcards and search statistics](../../guides/urls-and-aliases.md) |
| System information | PHP, database and the active extensions. | [System information](../../guides/system-information.md) |
| Section setup | Create sections and assign content. | [Sections](../../guides/sections.md) |
| PDF export | Export content as a PDF through a class. | [PDF exports](../../guides/pdf-exports.md) |
| Rapid Application Development (RAD) | The book's group of Setup tools for building: **Classes**, **Extensions**, **Packages**, **Notification** and others. | below |
| Extension setup | Turn extensions on and see which are active. | [Extensions page](../../guides/extensions-page.md), [Activating an extension](../extension-development/part-3-extension-developer/activating-an-extension.md) |
| Packages | Install and export content, classes and designs as packages. | [Packages](../../guides/packages.md) |
| Notification | Send notifications of content changes by e-mail. | [Notifications for administrators](../../guides/notifications-administrator.md) |
| Personal | The user's own pages: bookmarks, drafts, preferences, notification settings. | [What is on my account page](../manual/what-is-on-my-account-page.md) |
| Shop | Orders, products, prices. | [E-commerce](../e-commerce/e-commerce-reading-path.md) |

## What is new since the book

- Several administration designs, and a menu that differs by role ([A tour of the administration](../admin-tour.md)).
- Content checks, API keys, two-factor sign-in and the audit pages, which have no equivalent in the book
  ([Security and audit](../../guides/security-and-audit.md)).
- The command line has most of what the pages have: `./console list` ([CLI scripts and console commands](../extension-development/part-3-extension-developer/cli-scripts-and-console-commands.md)).

Next: [planning an example site](planning-an-example-site.md).

## Source and license

Adapted from chapter 2, "Content Management with eZ publish", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rearranged and shortened; settings, labels, datatypes, tags and URLs checked against Exponential 6 and corrected; links to the current guides added.
