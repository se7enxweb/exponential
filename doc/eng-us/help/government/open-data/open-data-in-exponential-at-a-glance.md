---
title: Open data in Exponential at a glance
audience: government, project-manager, admin, editor
category: public-sector
order: 30
summary: One table of every Exponential feature that helps a public body publish open data, with where it lives and what it does not do, so a project manager can scope the work in minutes.
keywords: open data, features, overview, datasets, files, RSS, Atom, REST API, JSON, CSV export, xrowextract, ezcsvexport, eztags, versions, audit
type: help
---
# Open data in Exponential at a glance

This page is for project managers and administrators who need a quick inventory of what Exponential offers for
publishing open data. Each row names the feature, where it is in the product and the documentation that backs it.
The last section lists what is missing, so you can plan for it.

## Contents

1. [Publishing](#publishing)
2. [Describing](#describing)
3. [Interfaces for machines](#interfaces-for-machines)
4. [Keeping it trustworthy](#keeping-it-trustworthy)
5. [Not included](#not-included)
6. [Where to go next](#where-to-go-next)

## Publishing

| Need | Feature | Where | Documentation |
|---|---|---|---|
| Offer a file for download | File object with an `ezbinaryfile` field; download through `content/download` | Content structure, any folder | [Files and downloads](../../creators/website-creators/files-and-downloads.md) |
| Upload many files at once | Upload files (`ezmultiupload`) | Media library > Upload files | [Media and uploads](../../media-and-uploads.md) |
| Readable, lasting addresses | URL aliases made from the name; old aliases redirect after a rename or move | automatic; URL aliases tab of a node | [Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md) |
| One document from many pages | PDF export of a subtree | Setup > PDF export | [PDF exports](../../../guides/pdf-exports.md) |
| A list of datasets | A folder whose children are dataset pages, shown as a list | your design | [Structuring the content tree](../../creators/website-creators/structuring-the-content-tree.md) |

## Describing

| Need | Feature | Documentation |
|---|---|---|
| Fields for title, description, publisher, license, dates | A content class of your own, built from datatypes such as text line, text block, date, selection, URL, file | [Choosing datatypes](../../creators/website-creators/choosing-datatypes.md) |
| Themes and keywords | Tags with the `eztags` extension | [Tagging with eztags](../../creators/website-creators/tagging-with-eztags.md) |
| Page title, description, canonical link | `xrowmetadata` meta data field | [xrowmetadata](../../../features/6.0/extensions/xrowmetadata.md) |
| Languages of the description | Content languages and translations | [Content languages](../../../guides/content-languages.md) |

## Interfaces for machines

| Need | Feature | Format | Documentation |
|---|---|---|---|
| "What is new" | RSS export of a folder | RSS 1.0, RSS 2.0, Atom | [RSS feeds](../../../guides/rss-feeds.md) |
| Read content by program | REST API v1 and v2 (`ezprestapi`) | JSON | [REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md) |
| Bulk export of content | `xrowextract` Export tab | CSV, JSON, XML | [xrowextract](../../../features/6.0/extensions/xrowextract.md) |
| Bulk export from the shell | `exp:ezcsvexport` | CSV, one file per class | [exp:ezcsvexport](../../../examples/commands/ezcsvexport.md) |
| Sitemaps for search engines | `xrowmetadata` and `bcgooglesitemaps` cronjobs | XML sitemap | [Sitemaps](../../creators/website-creators/sitemaps.md) |

## Keeping it trustworthy

| Need | Feature | Documentation |
|---|---|---|
| See what changed and when | Versions of every object, with compare | [Content history](../../../guides/content-history.md) |
| See who changed it | The audit trail, on by default | [Audit trail](../../../features/6.0/audit-trail.md) |
| Review before publishing | Approval workflow | [Workflows](../../../guides/workflows.md) |
| Only the data team may publish datasets | Roles limited by section or subtree | [Roles and policies](../../../guides/roles-and-policies.md) |

## Not included

Checked in the repository; plan for these yourself:

- No DCAT, DCAT-AP or schema.org Dataset output, and no CKAN API.
- No dataset class, no license field and no data dictionary out of the box.
- No validation of the files you upload (well-formed CSV, schema conformance).
- No data preview widget (table or chart of a CSV) in the shipped designs.
- No API keys for anonymous public use: the REST API requires a signed-in user by default
  (`rest.ini [Authentication] RequireAuthentication=enabled`); opening it to everyone is a deliberate setting
  change with its own review.

## Where to go next

Read "What open data means for a public body" first if the terms are new, then "Designing a dataset class",
"Licensing datasets" and "Stable URLs for datasets" in this section.

## Related pages

- [What open data means for a public body](what-open-data-means.md)
- [REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
