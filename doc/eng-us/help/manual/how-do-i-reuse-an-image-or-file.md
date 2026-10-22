---
title: How do I reuse an image or file from the media library?
audience: editor
category: manual
order: 460
summary: Keep shared images and files as items in the media library and point to them from content (a relation field, Add existing, or an embed); one edit then changes every page that uses them, so check the Relations tab before you change or remove a shared item.
keywords: media library, media tab, reuse, shared image, shared file, relation, reverse related objects, banner, images folder, files folder, section
type: help
---
# How do I reuse an image or file from the media library?

The **Media** tab holds images, files and other media as items of their own, outside the pages of the site. An item
there can be used by any number of pages: they point to it rather than holding a copy. This keeps one logo, one price
list or one photo in one place.

A file uploaded straight into a file field of an article is different: it belongs to that article only and cannot be
picked from the library.

## Who can do this

Editors who may read the media library. Adding items there needs `content/create` in its folders; using an item in a
page needs permission to edit that page.

## Steps

1. Put the file in the library first: open the **Media** tab, open the right folder (for example **Images** or
   **Files**) and upload it. See [Images, media and uploads](../media-and-uploads.md#upload-files).
2. Give it a clear name and, for an image, an alternative text, so others find and understand it.
3. Edit the page that should use it.
4. Point to the item in one of three ways:
   - in a relation field such as *Image*, click **Add existing** and choose the item;
   - in the **Related objects** box below the form, click **Add existing**;
   - in a rich text field, embed it with the editor's embed button.
5. In the browser, go up from the content tree to the top level to reach the media library, open the folder, select
   the item and click **Select**.
6. Click **Send for publishing**.

## Before you change or remove a shared item

1. Open the item in the **Media** tab and click its **Relations** tab.
2. Read **Reverse related objects**: every page listed there uses the item.
3. Edit or replace the item only if the change suits all of them. To change one page only, upload a new item for it.

## What can go wrong

- **Visitors do not see the image.** The media library is often a section of its own. If anonymous visitors may not
  read that section, pages show the image only to signed-in users. Ask an administrator to check the section's rights.
- **A shared image changed on pages you did not touch.** Someone edited the shared item. Use
  **Manage versions** on it to go back if needed.
- **Pages show missing images after a removal.** The item was removed while pages still used it. Restore it from the
  trash, or point those pages to another item.
- **You moved an item from Media into the content tree.** It may take on another section and other rights. Check the
  **Details** tab.

## Related pages

- [Images, media and uploads](../media-and-uploads.md)
- [Media library upkeep](../customers/day-to-day/media-library-upkeep.md)
- [How do I relate other items to the one I am editing?](how-do-i-relate-other-items-while-editing.md)
- [How do I remove an item?](how-do-i-remove-an-item.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
