---
title: Annual publications as downloads
audience: non-profit, editor, website-creator, government
category: business-solutions
order: 20
summary: Publishing a non-profit's annual review, annual filings, financial statements and policies on an Exponential site as file downloads - the file class and content/download, a folder per year, stable addresses, format and size shown, searchable PDFs, an HTML summary, scheduled publication, corrections without silent replacement, and old years kept findable.
keywords: annual review, annual publications, filings, 501(c)(3), Form 990, charity accounts, file class, ezbinaryfile, content/download, downloads, PDF, search inside files, stable URLs, corrections, archive
type: help
---
# Annual publications as downloads

This page is for the editor who publishes the yearly documents and the website creator who sets up the structure. It
covers the annual review, the financial statements, regulatory filings (for example a US non-profit's annual
information return, or a charity regulator's annual return elsewhere), and policies. Which documents you must publish
is a legal question; ask your accountant.

## Contents

1. [How a download works](#how-a-download-works)
2. [Structure and addresses](#structure-and-addresses)
3. [Publishing a document step by step](#publishing-a-document-step-by-step)
4. [Making documents findable](#making-documents-findable)
5. [Publishing on a date](#publishing-on-a-date)
6. [Corrections](#corrections)
7. [Old years](#old-years)
8. [Checklist](#checklist)

## How a download works

- A **File** object (class `file`) holds the document in its `file` attribute (datatype `ezbinaryfile`). Uploads of
  non-image files become File objects.
- The download address is `content/download/<object ID>/<attribute ID>`. The view checks that the visitor may read the
  object, then sends the file.
- Do not link files directly from the storage directory: that bypasses the permission check.

Source: [Files and downloads](../../creators/website-creators/files-and-downloads.md#how-a-download-works).

## Structure and addresses

Use a folder per year under an "Accountability" folder, as in the
[Annual publications page outline](../templates/annual-publications-page-outline.md). Addresses then read like
`/About-us/Accountability/2026/Annual-review-2026` and stay stable. Keep the names: renaming changes addresses, and
moved content needs redirects ([URLs, redirects and moved pages](../../customers/day-to-day/urls-redirects-and-moved-pages.md),
[Preserving URLs](../../government/records-and-archiving/preserving-urls.md)).

## Publishing a document step by step

1. Prepare an accessible PDF ([Accessible campaigns and documents](../accessibility/accessible-campaigns-and-documents.md#documents-and-pdfs)).
2. Check it for personal data that must not be public (signatures, home addresses, beneficiaries' names). Redaction
   is a manual step done before upload ([Redaction is a manual step](../../government/transparency-and-foi/redaction-is-a-manual-step.md)).
3. In the year folder, create a **File** object; name it with the title and year ("Annual review 2026").
4. Upload the file; write a one or two sentence description.
5. Publish, or send for approval if your workflow requires it.
6. Link it from the landing page ("Latest") and from the giving page ([The giving page](../donations/the-giving-page.md)).
7. Open the download while logged out to check it works.

## Making documents findable

- Show the format and size next to the link (same source as above).
- Add an HTML summary page with the key figures, linked to the file.
- PDF and Word contents can be indexed for search: `settings/binaryfile.ini` has handlers that extract text using
  external programs you install, and with Solr (`ezfind`) file contents can be indexed too
  ([Files and downloads](../../creators/website-creators/files-and-downloads.md#searching-inside-files)).
- Tag documents by year and type with `eztags` if you have many ([Tagging with eztags](../../creators/website-creators/tagging-with-eztags.md)).

## Publishing on a date

If the annual review must appear on a fixed date (an annual meeting), prepare it in advance and let the site publish
it: the Wait until date workflow publishes at a date ([Scheduled publishing](../../customers/day-to-day/scheduled-publishing.md),
[Scheduled publication and embargoes](../../government/transparency-and-foi/scheduled-publication-and-embargoes.md)).
Check afterward that it worked; it depends on cron.

## Corrections

- Do not replace a financial document silently. Upload the corrected file as a new version and say "corrected on
  (date): (what changed)" in the description, or publish a separate corrected document.
- The earlier version stays in the version history, and the audit trail records who published the change
  ([Versions and the audit trail](versions-and-the-audit-trail.md)).
- See [Withdrawing and correcting published information](../../government/transparency-and-foi/withdrawing-and-correcting-published-information.md).

## Old years

- Keep earlier years online, newest first; supporters and researchers compare years.
- Hide or move only what you are no longer allowed to publish.
- For long-term keeping, prefer formats suited to preservation ([File formats for preservation](../../government/records-and-archiving/file-formats-for-preservation.md)).

## Checklist

- [ ] Accessible PDF, checked for personal data.
- [ ] File object in the right year folder, clear name.
- [ ] Format and size shown; HTML summary linked.
- [ ] Download tested logged out.
- [ ] Landing page and giving page updated.
- [ ] Corrections visible, never silent.

## Related pages

- [Transparency for non-profits](README.md)
- [Financial documents](financial-documents.md)
- [PDF and document formats for disclosures](../../government/transparency-and-foi/pdf-and-document-formats-for-disclosures.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
