---
title: Require alternative text on an image field
audience: government, admin
category: public-sector
order: 420
summary: Step by step - switch on the class attribute option "Alternative text is required" (Exponential 6.0.15 and later) for the image fields editors use, so an image cannot be stored without alternative text, and check it in the edit form.
keywords: alternative text, required, Alternative text is required, ezimage, class attribute, content class, Setup Classes, edit form, WCAG 1.1.1, EN 301 549 11.8, editors, public sector
type: help
---
# Require alternative text on an image field

For administrators. Since 6.0.15 an image attribute of a content class can require alternative text: the edit form then
refuses to store an image without it (`kernel/classes/datatypes/ezimage/ezimagetype.php`, option off by default).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [Related pages](#related-pages)

## Before you start

- Rights to edit classes (Setup > Classes).
- Decide how editors handle decorative images: with the option on, an empty alternative text is not possible in that
  field, so decorative images need another way (a separate class or a design-specific rule).
- Tell editors before you switch it on.

## Steps

1. **Setup > Classes**, open the class group and the class (for example Image, Article).
2. **Edit**.
3. On each image attribute (datatype image), tick **Alternative text is required** (its tooltip: "Editors cannot store
   an image in this field without an alternative text.").
4. **OK** to store the class.
5. Repeat for every class with image fields that editors use for informative images.

## Check that it worked

1. Edit an object of the class; the **Alternative image text** label shows "(required)".
2. Upload an image without alternative text and store: the form answers "An alternative text is required for this
   image."
3. Existing objects keep their images; the rule applies when an image is next stored.
4. ZIP uploads skip images where the site requires alternative text, because a ZIP file has none.

## Related pages

- [Alternative text for images](../../accessibility/alternative-text-for-images.md)
- [Check alternative text across images](check-alt-text-across-images.md)
- [Choosing datatypes](../../../creators/website-creators/choosing-datatypes.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
