---
title: Publish a photo gallery
audience: government, editor
category: public-sector
order: 310
summary: For editors - publish a photo gallery with the shipped Gallery class, upload many images at once with their alternative texts and credits, order them, and respect consent for photos of people.
keywords: photo gallery, Gallery class, images, upload many, alternative text, credits, consent, order, image sizes, event photos
type: help
---
# Publish a photo gallery

This page is for editors who publish photos of an event, a building project or a place. The default package ships a
**Gallery** class (`gallery`, a container) that holds **Image** objects ([sevenx_classes](../../../site-packages/sevenx-multisite/sevenx-classes.md));
the upload page puts many images in it at once ([Media and uploads](../../../media-and-uploads.md#upload-files)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Pitfalls](#pitfalls)

## Before you start

- You have the right to publish the photos, and the credits.
- Photos of identifiable people: consent where your rules require it; for children, always check your rules.
- Each photo has a short description you can use as alternative text.
- Large originals are fine: the site makes sizes (image aliases) itself.

## Steps

1. Open the place for the gallery, **Create new**, choose **Gallery**; give it a name and a short description; **Send
   for publishing**.
2. Open the new gallery and press **Upload**. The upload page opens with the gallery as **Target location**.
3. **Choose files...** or drag the photos onto the dashed area.
4. In the queue, for each image: change the **title** if the file name is meaningless, and fill the **alternative
   text** where the site asks for it.
5. Press **Upload all**. **Keep as drafts** first if you want to check them before they are public.
6. Put credits in each Image's caption or description field where your template shows them.
7. Order the images: on the gallery, sort by priority and set priorities ([Reorder pages in a folder](reorder-pages-in-a-folder.md)).

## Check the result

- [ ] Signed out, the gallery shows the images in the right order.
- [ ] Each image has a meaningful alternative text (inspect one with the browser or a screen reader).
- [ ] Opening an image shows its credit.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| ZIP upload when alternative text is required | images in a ZIP are skipped where alternative text is required; upload them individually |
| Alternative text "IMG_1234" | describe the photo |
| Location data in photo files | some cameras store GPS positions in files; remove them before upload if they reveal private places |
| A gallery as the only report of an event | write a short text too |

## Related pages

- [Publishing tasks](README.md)
- [Upload many files at once](upload-many-files-at-once.md)
- [Images and image variations](../../../creators/website-creators/images-and-image-variations.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
