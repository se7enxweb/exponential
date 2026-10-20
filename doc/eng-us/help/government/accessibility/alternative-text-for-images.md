---
title: Alternative text for images
audience: government, editor, admin, designer
category: public-sector
order: 160
summary: For editors and administrators in public bodies - how to write alternative text, where Exponential stores it, how to make it required on a content class, how decorative images behave in the standard design, and the optional AI suggestions and report.
keywords: alternative text, alt text, alt attribute, images, decorative images, Alternative image text, Alternative text is required, ezimage, WCAG 1.1.1, Missing alt text, Exponential Intelligence, expai, editors
type: help
---
# Alternative text for images

This page is for editors who publish images, administrators who configure content classes, and designers who write
image templates. Alternative text serves people who cannot see an image: screen reader users, people with images
switched off, and search engines. WCAG 2.2 success criterion 1.1.1 requires it for every informative image
(<https://www.w3.org/TR/WCAG22/>).

## Contents

1. [Writing good alternative text](#writing-good-alternative-text)
2. [Where Exponential keeps it](#where-exponential-keeps-it)
3. [Making it required](#making-it-required)
4. [Decorative images](#decorative-images)
5. [Finding images without alternative text](#finding-images-without-alternative-text)
6. [Related pages](#related-pages)

## Writing good alternative text

| Image | Good alternative text | Avoid |
|---|---|---|
| Photo of the town hall on a page about opening hours | Main entrance of the town hall on Market Square | "Image of a building", the file name |
| Chart of budget figures | the conclusion ("Spending on roads rose each year from 2022 to 2025") plus the figures in a table on the page | "Chart" |
| Logo that links to the home page | the destination ("Department of Example, home") | "Logo" |
| Scan of a notice | the text of the notice on the page instead; the image then needs only a short description | the whole text squeezed into alt |
| Pattern, divider, purely decorative photo | empty, if your design writes `alt=""` (see below) | a description |

Keep it short (one sentence or a phrase), do not start with "Image of", do not repeat the caption word for word.

## Where Exponential keeps it

Every image field (datatype `ezimage`) has an **Alternative image text** field in the edit form
(`design/standard/templates/content/datatype/edit/ezimage.tpl`). It is stored with the image of that version and
language, so each translation has its own. Images in the media library are image objects; their alternative text
travels with them when they are embedded.

## Making it required

Since 6.0.15 an image attribute of a class has the option **Alternative text is required**
(`kernel/classes/datatypes/ezimage/ezimagetype.php`). With it on, an editor cannot store an image in that field
without an alternative text; the form shows "(required)" next to the field.

1. Open Setup > Classes, the class (for example Image or Article), **Edit**.
2. On the image attribute, tick **Alternative text is required**.
3. Store the class.

Existing objects keep their images; the rule applies when an image is next stored. A ZIP upload of images skips images
where the site requires alternative text, because a ZIP file carries none ([Images, media and uploads](../../media-and-uploads.md)).

## Decorative images

The standard design's image template (`design/standard/templates/content/datatype/view/ezimage.tpl`) uses the
alternative text and, **when it is empty, the object's name** as `alt`; it also uses the same text as `title`. So an
empty alternative text does not produce `alt=""` in that template. If your guidelines treat some images as
decorative, ask your designer for an override of the image template (or a separate class or view for decorative
images) that writes `alt=""` and no `title`. Then tell editors exactly how to mark an image as decorative.

## Finding images without alternative text

- **Your own review**: a monthly spot check of new pages (see "Monitoring and feedback").
- **Optional, Exponential Intelligence** (extension `expai`, installed by Composer but active only where a site
  activates it): the edit form gets **Write alt text** and **Decorative (empty alt text)** buttons, and the report
  **Missing alt text** (`/expai/alttext`, policy `expai/write`) lists images without alternative text below a node;
  **Queue suggestions as drafts** writes suggestions into new drafts, never published, for editors to review
  ([Exponential Intelligence](../../../features/6.0/extensions/expai.md)). Images are sent to an AI provider, subject
  to the extension's data policy; decide with your data protection officer before enabling it.
- Nothing in the kernel lists images without alternative text across the whole site.

## Related pages

- [Images, media and uploads](../../media-and-uploads.md)
- [Images and image variations](../../creators/website-creators/images-and-image-variations.md)
- [Accessibility standards](../../../standards/accessibility.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
