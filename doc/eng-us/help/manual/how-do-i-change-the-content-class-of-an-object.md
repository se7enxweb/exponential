---
title: How do I change the content class of an object?
audience: admin
category: manual
order: 340
summary: Choose Change content class in the object's context menu, pick the destination class, map each old field to a new one and confirm; the object keeps its place, id and versions.
keywords: change class, convert, content class, expchangeclass, changeclass/convert, attributes mapping, article to page
type: help
---
# How do I change the content class of an object?

When an object was created with the wrong class (an article that should be a page), you can convert it instead of
copying the text by hand. Each field of the old class is mapped to a field of the new one.

## Who can do this

Users whose role has `changeclass/convert` (the extension expchangeclass, in a new installation). Usually
administrators only, because a wrong mapping loses field content.

## Steps

1. In **Content structure**, open the object's context menu and choose **Change content class**.
2. Under **Destination content class**, choose the new class and click **Select**.
   The page **Attributes mapping** opens, with the **Source class** on the left and the **Destination class** on the
   right.
3. For each field of the destination class, choose which field of the source class fills it.
4. Read **Warnings**: a field without a match is emptied.
5. Click **Modify Object**.
6. Click **Continue to new object** and check the result.

## What can go wrong

- **Attention: a source field that is not mapped is lost.** Check the warnings and test on a copy first if the object
  is important.
- **The menu entry is missing.** Your role lacks `changeclass/convert`.
- **Templates of the site look wrong.** The new class has different templates; that is expected and a matter for the
  designer.

## Related pages

- [Content classes for websites](../creators/website-creators/content-classes-for-websites.md)
- [Content model and editing](../../guides/content-model-and-editing.md)
- [User manual](README.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
