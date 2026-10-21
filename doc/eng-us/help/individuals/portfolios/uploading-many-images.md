---
title: Uploading many images
audience: individual, editor, website-creator
category: personal-sites
order: 1612
summary: How to bring dozens or hundreds of images into a portfolio at once with the multi-upload page of ezmultiupload - which classes offer it, which file types a gallery accepts, the ZIP import, preparing files first, and the work that remains after the upload.
keywords: multi-upload, ezmultiupload, upload many files, bulk upload, ZIP import, gallery, folder, file types, image size, file names, alternative text, portfolio
type: help
---
# Uploading many images

This page is for individuals with a lot of images to put online. Creating one *Image* object at a time is fine for
ten pictures and tedious for three hundred. The `ezmultiupload` extension, part of the default installation, adds an
upload page that creates one object per file.

## Contents

1. [Where the upload appears](#where-the-upload-appears)
2. [Prepare the files first](#prepare-the-files-first)
3. [Uploading](#uploading)
4. [Uploading a ZIP file](#uploading-a-zip-file)
5. [After the upload](#after-the-upload)
6. [When it does not work](#when-it-does-not-work)

## Where the upload appears

Open a *Folder* or a *Gallery* and click **Upload multiple files**. Those two classes offer it by default
(`ezmultiupload.ini [MultiUploadSettings] AvailableClasses[]`); the feature page lists every setting
([ezmultiupload](../../../features/6.0/extensions/ezmultiupload.md)).

| Upload into | Accepted by default |
|---|---|
| A gallery | `*.jpg`, `*.png`, `*.gif`, `*.flv`, `*.mp4`, `*.mov` (and upper case) |
| A folder | any file type |

![The multi-upload page](../../images/multi-upload.webp)

## Prepare the files first

Ten minutes on your computer save hours in the administration:

| Step | Why |
|---|---|
| **Rename files** to what the work is called ("harbor-at-dawn.jpg") | The object name usually comes from the file name |
| **Resize** to a sensible web size (for example the longest side a few thousand pixels at most) | Faster uploads; the site makes smaller copies itself |
| **Remove location data** and other metadata you do not want to share | The original is stored as uploaded |
| **Sort into one folder per gallery** | One upload per gallery |
| **Check the upload limit** of your hosting (`upload_max_filesize`, `post_max_size` in `php.ini`) | Large files fail silently otherwise ([PHP settings for Exponential](../../hosting/basics/php-settings-for-exponential.md)) |

## Uploading

1. Open the target gallery.
2. Click **Upload multiple files**.
3. Choose or drop the files. Each file shows a thumbnail, a counter and a message when it is stored.
4. When all are done, go back to the gallery: each file is now an *Image* object below it.

If you cancel, the files already stored stay; the next files you choose upload normally.

## Uploading a ZIP file

The upload page can also unpack ZIP files into content: each file becomes an image or file object by its type, and
*Keep the folder structure* makes a folder for each folder of the archive. It is off until you switch on *Import
compressed zip file(s) contents*. Settings limit depth, size, entry count and compression ratio. **Images whose class
requires alternative text are skipped**, because a ZIP carries none
([Exponential's import and export tools](../../../install/migration/exponential-tools.md)).

## After the upload

Uploading is half the work. For each image:

- [ ] Give it a proper name if the file name was not one.
- [ ] Write a caption.
- [ ] Write alternative text.
- [ ] Add tags.
- [ ] Crop or rotate where needed ([Editing images with ezie](editing-images-with-ezie.md)).
- [ ] Set the order in the gallery ([Building an image gallery](building-an-image-gallery.md#order)).

Do it in batches of twenty; it is easier to keep the captions consistent.

## When it does not work

| Symptom | Likely cause |
|---|---|
| No **Upload multiple files** button | The class is not in `AvailableClasses[]`, or you lack permission to create content there |
| Some files are refused | Their type is not in the gallery's `FileType[]` list |
| Large files fail | The hosting's upload limits |
| Uploads stop after a few files | Time or memory limits on the server; upload fewer at once |

## Related pages

- [Portfolios](README.md)
- [Building an image gallery](building-an-image-gallery.md)
- [ezmultiupload](../../../features/6.0/extensions/ezmultiupload.md)
- [Media library upkeep](../../customers/day-to-day/media-library-upkeep.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
