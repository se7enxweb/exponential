---
title: Image and file guidelines
audience: customer, editor
category: customer-success
order: 15
summary: House rules for images and files on an Exponential site that an owner can hand to editors - which format for what, what the site does with an upload, sizes, names, alternative texts, documents for download and rights - based on the shipped image settings.
keywords: image guidelines, file guidelines, formats, JPEG, PNG, WebP, SVG, alternative text, file names, image sizes, downloads, PDF, house rules
type: help
---
# Image and file guidelines

This page is for the owner who writes the house rules for images and files, and for the editors who follow them.
Copy the parts that fit into your own editor guide. The facts about what the site accepts come from the shipped
settings; your administrator may have changed them, so ask once and note the answer at the bottom.

## Contents

1. [What happens to an upload](#what-happens-to-an-upload)
2. [Which format for what](#which-format-for-what)
3. [Size and quality](#size-and-quality)
4. [Names, titles and alternative texts](#names-titles-and-alternative-texts)
5. [Documents for download](#documents-for-download)
6. [Rights and people](#rights-and-people)
7. [Your site's values](#your-sites-values)

## What happens to an upload

- An image becomes an *Image* item, most other files a *File* item; an MP4 file a *Video* item
  ([Organizing the media library](../../creators/website-creators/organizing-the-media-library.md)).
- The site keeps the uploaded file as the `original` and makes smaller **variations** for pages, such as `reference`
  (fits 600 x 600), `medium` and `large`. Variations are converted: WebP and GIF to PNG, everything else to JPEG
  ([Images and image variations](../../creators/website-creators/images-and-image-variations.md)).
- An SVG is never stored as uploaded: it is cleaned of script and unsafe parts first, and never scaled
  ([SVG and image formats](../../creators/website-creators/svg-and-image-formats.md)).

So you do not need to prepare every size by hand. Upload one good image; the site does the rest.

## Which format for what

From [SVG and image formats](../../creators/website-creators/svg-and-image-formats.md):

| Image | Format |
|---|---|
| Logo, icons, simple illustrations | SVG |
| Photos | JPEG, or WebP/AVIF uploads |
| Screenshots, images with text or transparency | PNG |
| Animations | a short video rather than GIF where possible |

The shipped list of image types editors may upload is JPEG, PNG, GIF, WebP, AVIF, TIFF, BMP and SVG
(`settings/image.ini [ImageUploadSettings] AllowedMIMETypes[]`).

## Size and quality

- Upload images large enough for the biggest place they appear, but not straight from a camera. Agree a maximum
  width with your designer, who knows the largest variation your design uses.
- The upload page shows the largest file it accepts, for example *At most 64 MB per file*
  ([Images, media and uploads](../../media-and-uploads.md)). Large files slow down editors and backups.
- If an upload is refused as too big, make it smaller (fewer pixels or stronger compression) rather than asking for a
  higher limit.

## Names, titles and alternative texts

| Field | Rule | Example |
|---|---|---|
| File name | what it shows, lowercase, hyphens | `town-hall-front.jpg` |
| Title | short and clear; editors see it in lists and searches, visitors often as link text | Town hall, front |
| Alternative text | what the image shows, for someone who cannot see it; empty only for pure decoration | The town hall entrance with the new ramp |

The rules behind this: [Images, media and uploads](../../media-and-uploads.md#good-practice) and
[Accessibility checklist for editors](accessibility-checklist-for-editors.md). Images from a ZIP upload are skipped
where the site requires an alternative text, because a ZIP file has no place for it.

## Documents for download

- Prefer a web page over a PDF for information people read on a phone.
- When a PDF is needed, give it a clear title ("Annual report 2026 (PDF)") and say what it is in the link text.
- Replace an outdated document by editing its *File* item and uploading the new file, so every link keeps working;
  do not upload a second copy.
- Put documents in the folder agreed in [Media library upkeep](media-library-upkeep.md).

## Rights and people

- Upload only images you may use on the site; note the source or photographer in the item if your rules ask for it.
- Ask before publishing photos in which people can be recognized, according to your organization's rules. This is
  not legal advice.
- Remove images when a license ends; the content checks show where an image is still used
  ([The content checks](../../../features/6.0/content-checks.md)).

## Your site's values

```text
Largest upload accepted: ________   Image types accepted: ______________________
Preferred photo width: ________ px   Where images go: ______________________
Who to ask: ______________ (role)
```

## Related pages

- [Images, media and uploads](../../media-and-uploads.md)
- [Media library upkeep](media-library-upkeep.md)
- [Files and downloads](../../creators/website-creators/files-and-downloads.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
