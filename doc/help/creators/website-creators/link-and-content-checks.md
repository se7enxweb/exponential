---
title: Broken links and content checks
audience: website-creator, editor
category: website-creation
order: 730
summary: Find broken links, unused media, broken relations and missing files - Setup > Link management and the link check cronjob, and the three content checks with what each finds and what to do.
keywords: broken links, link check, linkcheck, link management, content checks, unreferenced objects, broken relations, missing files, storage files, orphan files, website creator
type: help
---
# Broken links and content checks

This page is for website creators who keep a site tidy. Exponential checks the links in your content and looks for
content and files that are broken or unused.

## Contents

1. [Broken links](#broken-links)
2. [Unreferenced objects](#unreferenced-objects)
3. [Broken relations](#broken-relations)
4. [Storage files and missing files](#storage-files-and-missing-files)
5. [A monthly routine](#a-monthly-routine)

## Broken links

**Setup > Link management** (`/url/list`) lists every address published content links to, with its state (valid,
invalid, never checked) and the objects that use it. The **link check** (`linkcheck.php`, cron part `infrequent`)
requests the links politely, never requests private network addresses, and judges links to content by their node or
object ([1. The link list](../../../guides/urls-and-aliases.md#1-the-link-list),
[2. How links are checked](../../../guides/urls-and-aliases.md#2-how-links-are-checked)). Settings are in
`cronjob.ini [linkCheckSettings]`.

## Unreferenced objects

`/contentcheck/unreferenced/<node id>` lists published images, files and media below a node that nothing uses: no
object, layout, setting, template, role, shop or newsletter entry. Tick and move them to the trash after a confirmation
([The content checks](../../../features/6.0/content-checks.md)).

## Broken relations

`/contentcheck/brokenrelations/<node id>` lists published content that refers to something deleted, in the trash,
hidden or never published, with a link to edit the source; and layouts whose blocks name such content. Read only.

## Storage files and missing files

`/contentcheck/storage/<node id>` lists files in `var/<site>/storage` no content uses (they can go to a quarantine) and
file problems (owner, mode, empty files). Missing files lists images and files whose file is gone
([Missing files](../../../guides/missing-files.md)).

The checks are in the Setup menu and the context menu of the content tree (**Advanced**). Their policies are
`contentcheck/unreferenced`, `trash` and `brokenrelations`.

## A monthly routine

1. Link management: filter **invalid**, fix or remove each link.
2. Broken relations from the content root: fix each source.
3. Unreferenced objects in Media: review and trash what is truly unused.
4. Storage: quarantine orphan files; delete from quarantine after a while.

## Related pages

- [Maintaining your website](maintaining-your-website.md)
- [Relations and content reuse](relations-and-reuse.md)
- [Reverse relation check](../../../features/6.0/reverse-relation-check.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
