---
title: Publishing dataset files
audience: government, editor
category: public-sector
order: 70
summary: Step by step for editors: create a dataset page, upload its files as distributions, check the download works for an anonymous visitor, and replace a file with a new version without breaking the address.
keywords: upload, dataset file, CSV, download, content/download, distribution, file class, replace file, ezbinaryfile, open data, publish
type: help
---
# Publishing dataset files

This page is for editors who publish a dataset and its files. It assumes a dataset class and a catalog folder
exist (see "Designing a dataset class"). Downloads in Exponential go through the `content/download` view, which
checks that the visitor may read the object before it sends the file
([Files and downloads](../../creators/website-creators/files-and-downloads.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Create the dataset page](#create-the-dataset-page)
3. [Add the files](#add-the-files)
4. [Check the download as a visitor](#check-the-download-as-a-visitor)
5. [Replace a file with a new version](#replace-a-file-with-a-new-version)
6. [Formats and file names](#formats-and-file-names)
7. [Problems](#problems)

## Before you start

- You can create `dataset` and `distribution` (or `file`) objects in the catalog folder. If **Create new** does
  not offer them, ask an administrator for the policy ([Roles and policies](../../../guides/roles-and-policies.md)).
- The file passed the privacy check of your body (see "Privacy checks before publishing data").
- The license of the dataset is decided (see "Licensing datasets").

## Create the dataset page

1. In **Content structure**, open the catalog folder.
2. Under **Sub items**, press **Create new** and choose **Dataset** (or use **Create here** in the item's menu in the tree).
3. Fill in the fields: title, description, publisher, contact mailbox, license, dates, update frequency.
4. Press **Send for publishing**. With an approval workflow the version waits as pending until a reviewer approves
   it ([Drafts and pending items](../../../guides/drafts-and-pending.md)).

## Add the files

1. Open the new dataset page; under **Sub items**, press **Create new** and choose **Distribution** (or **File**).
2. Give a title that names the format and scope, for example "CSV, all years".
3. Choose the file in the file field and publish.

Several files at once: **Media library > Upload files** (`ezmultiupload`) uploads many files into a folder; move
them below the dataset afterwards, or upload into the dataset if the role allows it
([Media and uploads](../../media-and-uploads.md)).

Upload limits come from PHP (`upload_max_filesize`, `post_max_size`) and from the maximum size set on the file
field in the class. A file that is larger than these limits is refused by the upload.

## Check the download as a visitor

1. Sign out, or open the public site in a private browser window.
2. Open the dataset page and click each file.
3. Check that the browser receives the file with the right name, and that the page shows format and size.

If the file answers "access denied" for visitors, the dataset sits in a section the Anonymous role may not read
([Sections](../../../guides/sections.md)). That protection is correct for drafts and internal data and wrong for open
data; move the catalog to a public section.

Never link the stored file path below `var/.../storage/` directly. It bypasses the permission check and changes
when the file changes ([Files and downloads](../../creators/website-creators/files-and-downloads.md#protected-downloads)).

## Replace a file with a new version

To publish new data for the same distribution:

1. Open the distribution and press **Edit**.
2. Remove the old file in the file field and choose the new one.
3. Update the "Last changed" date on the dataset page.
4. Publish.

The distribution keeps its object ID, so its page address stays the same. The download address contains the
object and attribute IDs and the file name; when the new file has a different name, the last part of the download
address changes. Link to the distribution page, or keep the file name the same, if re-users bookmark downloads (see
"Stable URLs for datasets").

The previous version of the object, with the old file, is kept in the version history until the version limit
removes it ([Content history](../../../guides/content-history.md)).

## Formats and file names

| Choose | Avoid |
|---|---|
| CSV in UTF-8 with a header row | Spreadsheets with merged cells and colors as meaning |
| JSON or XML for nested data | Scanned PDF tables |
| Lower-case file names without spaces: `street-lights-2025.csv` | Names with dates in several formats |
| One file per logical table | Many tables in one sheet |

## Problems

| Symptom | Cause | Fix |
|---|---|---|
| The upload stops without a message | PHP upload limit | Ask the administrator to raise `upload_max_filesize` and `post_max_size` |
| The visitor gets "access denied" | Section not readable by Anonymous | Move the catalog to a public section |
| The old file still downloads | A cache in front of the site | Wait for the cache time or ask the administrator to clear it ([Caches](../../../guides/caches.md)) |

## Related pages

- [Files and downloads](../../creators/website-creators/files-and-downloads.md)
- [Organizing the media library](../../creators/website-creators/organizing-the-media-library.md)
- [Editing content](../../editing-content.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
