---
title: How do I relate other items to the one I am editing?
audience: editor
category: manual
order: 455
summary: The Related objects box below the edit form adds existing items (Add existing) or uploads a new file (Upload new) as related objects, shows the code that embeds or links each one in rich text, and Remove selected takes relations away without deleting the items.
keywords: related objects, relations, add existing, upload new, remove selected, embed code, XML code, common relation, embedded, linked, attribute, reverse related
type: help
---
# How do I relate other items to the one I am editing?

A relation records that one item uses another. Exponential keeps four kinds, shown on the **Relations** tab of an
item:

| Relation type | How it is made |
|---|---|
| **Common** | added by hand in the **Related objects** box of the edit form |
| **Embedded** | an item embedded in a rich text field |
| **Linked** | a link to an item in a rich text field |
| **Attribute** | a relation field of the class, such as *Image* of an article |

This page is about the **Related objects** box. Templates may show common relations, for example as a list of
related pages; whether they do depends on the site.

## Who can do this

Editors who may edit the item and read the items they relate. **Upload new** also needs permission to create the
new item where uploads go.

## Steps

1. Edit the item and scroll below the fields to **Related objects**.
   It lists **Related images**, **Related files** and **Related content**, each with its count.
2. To relate an item that exists, click **Add existing**, find it in the browser, select it and click **Select**.
3. To relate a file from your computer, click **Upload new**, choose the file and confirm.
   A new item is made from the file and related at once.
4. To place a related item in a rich text field, copy the code from its **XML code** column and paste it into the
   text, or use the editor's own embed and link buttons.
5. To take relations away, tick them and click **Remove selected**. The items themselves are not deleted.
6. Click **Send for publishing**.

## What can go wrong

- **An embedded or linked item cannot be removed here.** Those relations follow the text. Remove the embed or the link
  from the rich text field and the relation goes with it.
- **The relation shows nowhere on the site.** The site's templates do not show common relations. Use a relation field
  or an embed instead, or ask whoever builds the templates.
- **You want to see who uses this item.** The edit form does not show that. Open the item and look at **Reverse related
  objects** on its **Relations** tab.
- **The uploaded file landed in an unexpected folder.** Uploads go to the place your site sets for each kind of file,
  usually a folder of the media library.

## Related pages

- [Creating and editing content](../editing-content.md#relations-and-images)
- [How do I reuse an image or file?](how-do-i-reuse-an-image-or-file.md)
- [How do I embed another object in a text?](how-do-i-embed-another-object.md)
- [Relations and embeds](../cms-developers/relations-and-embeds.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
