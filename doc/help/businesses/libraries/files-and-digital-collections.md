---
title: Files and digital collections
audience: businesses, editor, website-creator
category: business-solutions
order: 50
summary: How a library or archive offers digitized items on Exponential - file objects and permission-checked downloads, open and restricted collections, stating a license per item, files sent in by visitors with enhancedezbinaryfile, large files, Writer imports, and finding missing or unused files.
keywords: digital collection, digitized, downloads, files, ezbinaryfile, enhancedezbinaryfile, protected download, license, Creative Commons, public domain, xrowmetadata, missing files, storage
type: help
---
# Files and digital collections

This page is for librarians, archivists and collection managers who publish digitized material, and for the website
creator who sets it up. It explains how files are stored and delivered, how to keep some material restricted, how to
state the license of each item, and how to keep the file store tidy.

## Contents

1. [How files are stored and delivered](#how-files-are-stored-and-delivered)
2. [Open and restricted material](#open-and-restricted-material)
3. [Stating the license of each item](#stating-the-license-of-each-item)
4. [Files sent in by visitors](#files-sent-in-by-visitors)
5. [Large files and formats](#large-files-and-formats)
6. [Documents written in Writer](#documents-written-in-writer)
7. [Keeping the store tidy](#keeping-the-store-tidy)
8. [Collection checklist](#collection-checklist)

## How files are stored and delivered

- A **File** object (class `file`) holds a document in its `file` attribute (datatype `ezbinaryfile`). Uploads of
  non-image files become File objects (`DefaultClass=file` in `settings/upload.ini`).
- A catalog record class can have its own file field, so the record and its digitized copy are one object
  ([Modeling catalog records](modeling-catalog-records.md#a-record-field-by-field)).
- The download address is `content/download/<object ID>/<attribute ID>`. The view checks that the visitor may read
  the object, then sends the file.

Source: [Files and downloads, How a download works](../../creators/website-creators/files-and-downloads.md#how-a-download-works).
Tell readers the format and size before they click: "it is also an accessibility requirement for many sites" (same
page).

## Open and restricted material

| Goal | How |
|---|---|
| Open to everybody | A section the anonymous user may read |
| Members or registered readers only | A section only members may read |
| Staff only (internal copies, master scans) | A section only staff roles may read |
| Available for a limited time | Hide it, or move it to a section visitors cannot read, when the time is over |

Source: [Files and downloads, Protected downloads](../../creators/website-creators/files-and-downloads.md#protected-downloads).

> **Warning**
> "Do not link files directly from `var/.../storage/`: that bypasses the permission check" (same page). Always link
> through `content/download`, which the file field's own template does.

Reader accounts and members-only areas are explained in Members and
[Member registration and areas](../../creators/website-creators/member-registration-and-areas.md).

## Stating the license of each item

The `xrowmetadata` extension, required by `composer.json`, lets an editor state under which license a page's content is
published. Its guide (`extension/xrowmetadata/doc/licenses.md`) describes:

- a catalogue of licenses including "every Creative Commons license ever published", public domain tools, and
  non-free terms such as "all rights reserved";
- three levels: per object, per class default, and the installation default; the first that applies wins;
- output in the page head as `rel="license"`, schema.org `license` in JSON-LD, and Dublin Core `dcterms.license`,
  which "is read by library and repository harvesters";
- marks such as the Public Domain Mark are treated as labels, not licenses.

> **Note**
> The installation default in `xrowmetadata.ini [LicenseSettings] DefaultLicense` is shipped as `GPL-2.0-or-later`,
> the license of the software. For a collection, set your own default in
> `settings/override/xrowmetadata.ini.append.php`, and a default per record class where it differs (same guide).
> Which license applies to an item is a decision for the rights holder; this page is not legal advice.

## Files sent in by visitors

When readers or donors send files through a form (a scan for identification, a document offered to the archive), use
the `enhancedezbinaryfile` datatype ("Enhanced File" in the class editor), built for information collection. It keeps
uploaded files in a download folder with a limit (`[RemoveFiles] MaxFiles`, default 25, "0 keeps an unlimited number,
which is not advisable for a site with anonymous uploads") and mails collected information as a multi-part mail when
a file is attached ([enhancedezbinaryfile](../../../features/6.0/extensions/enhancedezbinaryfile.md)). Protect such
forms against spam ([Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md)).

## Large files and formats

- PHP's `upload_max_filesize` and `post_max_size` limit what editors can upload; they are set in the web PHP's
  `php.ini`. A file field can also have a maximum size in the class.
- "Very large files (videos) are better hosted on a service built for them and embedded."

Source: [Files and downloads, Large files](../../creators/website-creators/files-and-downloads.md#large-files). For
images and their sizes, see [Images and image variations](../../creators/website-creators/images-and-image-variations.md);
for which image formats work, [SVG and image formats](../../creators/website-creators/svg-and-image-formats.md). Plan
disk space and backups with your host ([Sizing memory, CPU and disk](../../hosting/basics/sizing-memory-cpu-and-disk.md)).

## Documents written in Writer

If staff write reports in LibreOffice or OpenOffice Writer, the `ezodf` extension imports an `.odt` file as a content
object (title, headings, paragraphs, lists, tables and images mapped onto class attributes) and exports objects back
to Writer. It needs OpenOffice or LibreOffice 2.4 or later for conversions
([ezodf](../../../features/6.0/extensions/ezodf.md)).

## Keeping the store tidy

The **content checks** (Setup menu) help a collection stay sound
([Content checks](../../../features/6.0/content-checks.md)):

| Check | Finds |
|---|---|
| Unreferenced objects | Published images, files and media below a node that nothing uses |
| Broken relations | Published content that refers to deleted, trashed, hidden or unpublished objects |
| Storage files | Files in storage no content uses, and file-system problems of files content uses |
| Missing files | Files whose stored file is missing, in published versions and drafts |

Orphan storage files can be moved to a quarantine and restored; "nothing is ever deleted from the storage itself"
(same page). For a digital collection, run these checks after migrations and before a backup is archived. Link checks
for external links are described in [Link and content checks](../../creators/website-creators/link-and-content-checks.md).

## Collection checklist

- [ ] Each record class that carries a digitized copy has a file field.
- [ ] Open, member and staff material separated by sections; downloads tested while logged out.
- [ ] Download links show format and size.
- [ ] A default license set for the installation and for each record class; per-item licenses where they differ.
- [ ] Upload limits fit the largest file; very large media hosted elsewhere and embedded.
- [ ] Visitor upload forms use `enhancedezbinaryfile` with a sensible `MaxFiles` and spam protection.
- [ ] Content checks run after each migration; missing files followed up.
- [ ] Storage included in backups and a restore tested ([Backups and restores](../../creators/website-creators/backups-and-restores.md)).

## Related pages

- [Libraries and catalogs](README.md)
- [Files and downloads](../../creators/website-creators/files-and-downloads.md)
- [Organizing the media library](../../creators/website-creators/organizing-the-media-library.md)
- [Media and uploads (for editors)](../../media-and-uploads.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
