---
title: Check alternative text across images
audience: government, editor, admin
category: public-sector
order: 410
summary: Step by step - find images without alternative text on a public site: with the optional Exponential Intelligence report "Missing alt text" where that extension is active, or with an accessibility checker on page samples where it is not, then fix them in drafts.
keywords: alternative text, alt text, images, Missing alt text, expai/alttext, Exponential Intelligence, accessibility checker, WCAG 1.1.1, drafts, editors, review, public sector
type: help
---
# Check alternative text across images

For editors and accessibility coordinators, as a monthly check or before publishing an accessibility statement.
Exponential's kernel has no site-wide list of images without alternative text; there are two ways to find them
([Alternative text for images](../../accessibility/alternative-text-for-images.md)).

## Contents

1. [Way 1: the Missing alt text report](#way-1-the-missing-alt-text-report)
2. [Way 2: a checker on page samples](#way-2-a-checker-on-page-samples)
3. [Fixing](#fixing)
4. [Related pages](#related-pages)

## Way 1: the Missing alt text report

Only where the Exponential Intelligence extension (`expai`) is active ([Exponential Intelligence](../../../../features/6.0/extensions/expai.md)).
It sends images to an AI provider under its data policy; use it only if your organization has approved that.

1. Open **Missing alt text** (`/expai/alttext`, policy `expai/write`).
2. Enter the **Node ID of the subtree** to check (for example your news folder) and **Show**.
3. The list has one row per object, language and image field with a stored image and an empty alternative text, among
   the objects you may read.
4. To get suggestions, tick rows and **Queue suggestions as drafts**: each becomes a job that writes the suggestion
   into a **new draft**, never published. An optional budget limits the cost of the job group.
5. Editors review each draft (correct the text, or mark the image decorative where your design supports it) and publish.

## Way 2: a checker on page samples

1. Take a sample of pages (one per class and listing, plus recent pages).
2. Run an axe-based checker; it reports images without `alt`.
3. Note that the standard design's image template writes the object's name as `alt` when the field is empty, so a
   checker may not flag those: also look for alternative texts that are just the image's name.

## Fixing

- Edit the object, fill **Alternative image text** per language, publish.
- Prevent new gaps: "Require alternative text on an image field".

## Related pages

- [Alternative text for images](../../accessibility/alternative-text-for-images.md)
- [Require alternative text on an image field](require-alt-text-on-an-image-field.md)
- [Images, media and uploads](../../../media-and-uploads.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
