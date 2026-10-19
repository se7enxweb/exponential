---
title: Add alternative text to images
audience: government, editor
category: public-sector
order: 100
summary: For editors - write and enter alternative text for images in Exponential, in the image field, the upload page and the online editor's dialogs, decide when an image needs none, and find images that still lack it.
keywords: alternative text, alt text, images, accessibility, WCAG 1.1.1, image field, upload, online editor, decorative images, missing alt text, required
type: help
---
# Add alternative text to images

This page is for editors who publish images. Alternative text is what screen readers read instead of the image, and
what shows when an image does not load ([WCAG 2.2](https://www.w3.org/TR/WCAG22/), criterion 1.1.1). Exponential can
make it required per image field ([Content model and editing, 9](../../../../guides/content-model-and-editing.md#9-required-alternative-text-trimmed-text-lines-and-names-made-by-php)).

## Contents

1. [Before you start](#before-you-start)
2. [Writing it](#writing-it)
3. [Steps](#steps)
4. [Check the result](#check-the-result)
5. [Pitfalls](#pitfalls)

## Before you start

- Know why the image is on the page: to inform, or only to decorate.
- If your site requires alternative text, the field is marked **Alternative image text (required)** and the form
  answers *An alternative text is required for this image.* when it is empty.

## Writing it

| Image | Alternative text |
|---|---|
| A photo that informs ("the new library entrance") | what the reader needs from it: "Step-free main entrance of the library, with automatic doors" |
| A chart | the main message: "Recycling rate rose from 40% to 52% between 2020 and 2025"; the data in a table or text nearby |
| A logo that links | where it goes: "Home" or the organization's name |
| An image with text in it | the text |
| Purely decorative | none, and then it should not be in an image field meant to inform |

Short is usually better; do not start with "Image of".

## Steps

**In an image field (Image object or an Image attribute):**

1. **Edit** the Image object (or the item with the image field).
2. Fill **Alternative image text**.
3. **Send for publishing**.

**When uploading many images** ([Upload many files at once](upload-many-files-at-once.md)): in the queue, fill the
**alternative text** for each image where the site asks for it. Images inside a ZIP file are skipped where the site
requires alternative text, because a ZIP has no place for it.

**From a relation field or the online editor**: the upload dialogs ask for the alternative text when the class
requires it; fill it there.

## Check the result

- [ ] Open the page and inspect the image (browser developer tools, or a screen reader): the `alt` text is yours.
- [ ] Images uploaded by the generic upload (`content/upload`, multi-upload) or by imports are not asked for a text;
  check them afterwards. The expai extension's *Missing alt text* list shows images without one, where expai is
  installed.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| The file name as alternative text | write a description |
| The same text as the caption next to it | screen readers read both; make them complement each other |
| Alternative text in only one language | translate it with the image's translation |
| Information only in the image | put it in the text too |

## Related pages

- [Publishing tasks](README.md)
- [Accessibility for site builders](../../../creators/website-creators/accessibility-for-site-builders.md)
- [Media and uploads](../../../media-and-uploads.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
