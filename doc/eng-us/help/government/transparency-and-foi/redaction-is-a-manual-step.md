---
title: Redaction is a manual step
audience: government, editor, project-manager
category: public-sector
order: 80
summary: Exponential has no redaction tool. This page explains where redaction happens (in the program that made the document), the common ways it fails, a checking routine before upload, and how Exponential's versions keep an unredacted file if you are not careful.
keywords: redaction, redact, blackout, PDF, hidden text, text layer, metadata, FOI response, disclosure, exemption, personal data, checking
type: help
---
# Redaction is a manual step

This page is for editors and officers who publish documents with parts removed: names of third parties, security
details, exempt passages. **Exponential has no redaction tool.** It stores and serves the file you upload, exactly as
it is. Redaction happens before upload, in the program that made the document or in a dedicated redaction tool, and
it must be checked by a person.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [How redaction fails](#how-redaction-fails)
2. [A redaction routine](#a-redaction-routine)
3. [Checking the final file](#checking-the-final-file)
4. [Uploading without leaving the original behind](#uploading-without-leaving-the-original-behind)
5. [Web pages instead of files](#web-pages-instead-of-files)

## How redaction fails

| Failure | What happens |
|---|---|
| A black box drawn over text in a PDF | The text underneath is still there; copy and paste reveals it |
| Text colored white or the same as the background | Still in the file and in search results |
| Hidden rows, columns or sheets in a spreadsheet | Anyone can unhide them |
| Tracked changes and comments | Earlier wording and reviewers' names remain |
| Document properties | Author, organization, file paths remain |
| A scanned image with a text layer (OCR) | The image is covered, the text layer is not |
| An unredacted copy uploaded first, then replaced | The first file stays in the version history |

## A redaction routine

1. Work on a copy; keep the original in the case file, not on the website.
2. Use a redaction function that **removes** content, not one that covers it. If your tool cannot do that, print the
   redacted pages and scan them to an image-only file, accepting that it is then not machine-readable.
3. Remove document properties, comments, tracked changes and hidden content.
4. Mark each redaction with the ground (exemption) your law requires.
5. Save as a new file with a neutral name (no names of persons in the file name: it appears in the download address).
6. A second person checks the file (below).

## Checking the final file

- Select all text and paste it into a plain text editor: search for the redacted names and terms.
- Search the file for the redacted terms with the viewer's search.
- Look at the document properties.
- For spreadsheets: unhide everything, check every sheet.
- Record who checked, when, and which file, in the case.

## Uploading without leaving the original behind

Every publish of an object is a version, and older versions keep their files
([Content history](../../../guides/content-history.md)). If an unredacted file is ever uploaded to a published object,
replacing it is not enough:

1. Replace the file and publish.
2. Open the object's **Versions** page (`/content/history/<object ID>`) tick the versions that hold the unredacted file,
   open **Remove selected versions** and click **Remove for good** (policy `content/versionremove`). The published
   version is never removed there; that is why you publish the corrected one first.
3. Clear the caches in front of the site ([Caches](../../../guides/caches.md)).
4. Report it to your data protection officer.

To avoid this, upload only checked files, and use **Preview** or a draft that you do not publish until the check is
done. A draft is visible only to its creator and to people with `content/versionread`.

## Web pages instead of files

When the released information is short, publish it as page text instead of a document: what you write in the
editor is all there is, with no hidden layers. Rich text in Exponential keeps no tracked changes; earlier wording is
in older versions, visible only to staff.

## Related pages

- [Publishing a FOI response](publishing-a-foi-response.md)
- [Privacy checks before publishing data](../open-data/privacy-checks-before-publishing-data.md)
- [Content history](../../../guides/content-history.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
