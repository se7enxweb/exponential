---
title: Versions of documents
audience: government, website-creator, editor, admin
category: public-sector
order: 40
summary: How document versions work in Exponential - replacing the file of a File object keeps the address and stores the old file in the version history, the version history limit, when to publish a new object instead, and how to remove a file that must not stay.
keywords: document versions, replace file, new version, version history, archived version, DefaultVersionHistoryLimit, VersionHistoryClass, superseded document, remove versions, stable address
type: help
---
# Versions of documents

This page is for website creators, editors and administrators who update documents. Every save of an object is a
version; a File object is no exception. Replacing a file in place keeps its address, and the previous file stays in
the version history. Sources: [Versions of an object](../../../../guides/content-history.md) and
[Publishing, versions and the trash](../../../publishing-and-versions.md).

## Contents

1. [Replacing a file](#replacing-a-file)
2. [How long old versions are kept](#how-long-old-versions-are-kept)
3. [Replace in place or publish a new document?](#replace-in-place-or-publish-a-new-document)
4. [Removing a file that must not stay](#removing-a-file-that-must-not-stay)

## Replacing a file

1. Open the File object (or the Publication's related file), **Edit**.
2. In the **File** field, choose the new file; update the description if needed (for example "Updated <date>:
   corrected table 3").
3. **Send for publishing**. Links to the object keep working; the download now serves the new file.
4. The previous version is archived with its file; **Manage versions** lists it, and **View** opens it.

## How long old versions are kept

| Setting (`settings/content.ini [VersionManagement]`) | Shipped | Meaning |
|---|---|---|
| `DefaultVersionHistoryLimit` | 45 | the most versions an object keeps; at the limit the oldest archived version is removed to make room |
| `VersionHistoryClass[<class id>]` | `VersionHistoryClass[1]=75` (class 1 is Folder) | a limit for one class |

Set your own limits in `settings/override/content.ini.append.php` if documents must keep every version, for example
for the File class or a Publication class, and clear the INI cache. The cleanup command `./console exp:cleanupversions`
removes old versions; read its `--help` first ([Content model and editing](../../../../guides/content-model-and-editing.md#3-create-and-edit-an-object)).

## Replace in place or publish a new document?

| Situation | Do |
|---|---|
| A correction of the same document | replace in place; note the correction |
| A new edition (2026 report after the 2025 report) | a new object; the old one stays published |
| A new version of guidance that replaces the old | a new object, the old one marked superseded (an object state) with a link to the new |
| A translation | a related File object per language, or a translation of the object with its own file |

Publishing a new object for a new edition keeps earlier editions citable; replacing in place keeps a single address
for a living document.

## Removing a file that must not stay

When a file was published by mistake (wrong document, personal data):

1. Replace it at once with the right file, or hide the object (**Hide / unhide**).
2. Remove the versions that still hold the wrong file: **Manage versions**, filter *Archived*, tick them,
   **Remove selected versions**, **Remove for good**. The published version cannot be removed this way, which is why
   you replace first. It needs `content/versionremove`.
3. Clear caches that may hold a copy; a stored PDF export is regenerated or removed separately ([PDF export](pdf-export.md)).
4. Record what was done; tell your data protection officer if personal data was exposed.

Removal of versions is final and recorded in the audit trail.

## Related pages

- [Documents and publications](README.md)
- [Metadata and tagging](metadata-and-tagging.md)
- [Drafts and pending items](../../../../guides/drafts-and-pending.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
