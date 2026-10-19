---
title: Set a meta description and social image
audience: government, editor
category: public-sector
order: 550
summary: For editors - fill the Meta Data field of a page (title, description, Open Graph image) so search results and link previews show the right text and picture, and check what a shared link will look like.
keywords: meta description, Meta Data, xrowmetadata, search results, link preview, Open Graph, social image, page title, canonical, SEO
type: help
---
# Set a meta description and social image

This page is for editors who want a page to look right in search results and when people share it. Many classes have
a **Meta Data** field (the `xrowmetadata` datatype) with a title, keywords, a description, a canonical link and, since
1.3.6, an Open Graph image ([xrowmetadata](../../../../features/6.0/extensions/xrowmetadata.md),
[Metadata and social sharing](../../building/news-and-alerts/metadata-and-social-sharing.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Pitfalls](#pitfalls)

## Before you start

- The page's class has a **Meta Data** field (Event, Folder, Frontpage, Feedback form and the `ng_*` classes do; the
  base Article class of a new installation does not, unless your web team added it).
- The page's own title and summary are final.

## Steps

1. **Edit** the page; find the **Meta Data** field (often in a later tab of the form).
2. **Title**: the title for search results, under about 60 characters, the important words first.
3. **Description**: one or two full sentences (under about 160 characters) that say what the page offers.
4. **Image** (Open Graph): an image that represents the page well at small size; with alternative text in its
   object; no personal data visible by accident.
5. Leave **Canonical** empty unless the page is a duplicate of another address.
6. **Send for publishing**.

## Check the result

- [ ] View the page source (signed out): the `<title>` and `<meta name="description">` show your text, if your design
  prints them.
- [ ] A link preview tool or a messenger shows the title, description and image (they may cache old previews for a
  while).

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Copying the title into the description | the description adds information |
| Keywords stuffing | search engines largely ignore the keywords field |
| A different message than the page | the description must match the page |
| An image with text too small to read | link previews are small |

## Related pages

- [Publishing tasks](README.md)
- [SEO basics](../../../creators/website-creators/seo-basics.md)
- [Write a plain-language summary](write-a-plain-language-summary.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
