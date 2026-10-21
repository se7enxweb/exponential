---
title: Titles and descriptions with xrowmetadata
audience: local-business, businesses, customer, editor
category: local-business
order: 22
summary: Writing the title and description that appear in search results for each page of a local business site - what xrowmetadata adds to the edit form, what is shown when the fields are empty, writing tips with examples, and the sharing image for social networks.
keywords: xrowmetadata, meta title, meta description, search result snippet, Open Graph, sharing image, keywords, editor, local business
type: help
---
# Titles and descriptions with xrowmetadata

This page is for the editor of a local business site who fills in what search engines show for each page. The
`xrowmetadata` extension is part of the standard distribution (`composer.json`, `se7enxweb/xrowmetadata`) and adds a
**meta data** field to the content classes your builder chooses
([xrowmetadata](../../../features/6.0/extensions/xrowmetadata.md)).

## Contents

1. [What you see in the edit form](#what-you-see-in-the-edit-form)
2. [What happens when the fields are empty](#what-happens-when-the-fields-are-empty)
3. [Writing a good title](#writing-a-good-title)
4. [Writing a good description](#writing-a-good-description)
5. [The sharing image](#the-sharing-image)
6. [If xrowmetadata is not switched on](#if-xrowmetadata-is-not-switched-on)
7. [Checklist](#checklist)

## What you see in the edit form

Once your builder has added the meta data datatype to a class (**Setup > Classes**), every object of that class has a
metadata field with a title, keywords, a description and an Open Graph image
([Page metadata and social sharing](../../creators/website-creators/metadata-and-social-sharing.md#with-xrowmetadata)).
The pagelayout then prints them into the page head.

## What happens when the fields are empty

It depends on the template your builder wrote. The usual pattern, documented in
[Page metadata and social sharing](../../creators/website-creators/metadata-and-social-sharing.md), falls back to the
site's name for the title and the page's intro for the description. Ask your builder what your site does, and write it
here:

| Field empty | Our site shows instead |
|---|---|
| Title | |
| Description | |
| Sharing image | |

## Writing a good title

- Put the most important words first: the service and the town.
- Add the business name at the end.
- Keep it short; search engines cut long titles.

| Page | Weak title | Better title |
|---|---|---|
| Home | Home | Bike repair in Riverton - Example Cycles |
| Service | Services 3 | Puncture repair while you wait - Example Cycles |
| Contact | Contact | Opening hours and directions - Example Cycles, Riverton |

(Business names and towns here are invented examples.)

## Writing a good description

The description is the line or two under the title in search results. It does not decide the ranking much, but it
decides whether people click.

- Say what the page offers and why to choose you, in one or two sentences.
- Include a concrete fact: "Open Saturdays", "Same-day repair", "Free parking".
- Do not repeat the title word for word.
- Write a different description for every page.

## The sharing image

When someone shares a page in a social network or a chat app, the network shows an image. With `xrowmetadata` you can
choose one per page, and your builder can set a default per class. Use a clear photo of the shop front, the dish or
the work, not a logo with lots of white space. Social networks need the image at a size large enough for previews,
which your builder sets as an image variation
([Images and image variations](../../creators/website-creators/images-and-image-variations.md)).

## If xrowmetadata is not switched on

Your builder may instead add one or two fields of their own, for example a `meta_description` text field, and use the
intro when it is empty ([With your own fields](../../creators/website-creators/metadata-and-social-sharing.md#with-your-own-fields)).
The writing advice above is the same.

## Checklist

- [ ] The classes for your main pages (front page, folders, articles, products) carry the metadata field.
- [ ] Every main page has its own title and description.
- [ ] Titles name the service and the town.
- [ ] A default sharing image is set.
- [ ] You know what the site shows when a field is empty.

## Related pages

- [Local SEO basics](local-seo-basics.md)
- [Sitemaps and robots.txt](sitemaps-and-robots-txt.md)
- [SEO checks](../../customers/day-to-day/seo-checks.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
