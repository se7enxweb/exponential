---
title: "Lesson 5.2: The upload page"
audience: trainer, editor
category: training
order: 52
summary: Learners upload several files at once on the upload page, choose the target location, set titles and alternative texts, use Keep as drafts, and act on the uploaded items. 60 minutes.
keywords: editors 101, upload, upload files, multiupload, ezmultiupload, target location, quick locations, queue, upload all, keep as drafts, zip, alternative text, title
type: help
---
# Lesson 5.2: The upload page

The upload page takes one file or hundreds, by button, by dragging or by pasting an image. Editors who know it save
hours. In this lesson learners upload a handful of images into their own folder, give them proper titles and
alternative texts, and use the actions after the upload.

## At a glance

| | |
|---|---|
| Course | [Editors 101](../README.md) |
| Module | [Module 5: Images and media](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Beginner |
| Audience | New editors and authors |
| Prerequisites | [Lesson 5.1](the-media-library.md); three or four small sample images on each learner's computer |

## Learning objectives

After this lesson, learners can:

- open the upload page from the dashboard, from a folder's **Upload** button or from the item menu;
- choose the **Target location** with a quick location or **Change location...**;
- add files by **Choose files...**, by dragging or by pasting, and check the queue before uploading;
- set a title and an alternative text per image, and use **Keep as drafts**;
- use **Upload all**, read the result and act on the uploaded items.

## Trainer's notes

### How to present it

Source: [Images, media and uploads](../../../media-and-uploads.md#upload-files). The page's own description is
`extension/ezmultiupload/doc/upload-page.md` in the installation.

1. **Opening the page** (3 minutes): **Upload files** in the dashboard's quick actions, **Upload** on a folder's
   page (uploads into that folder), or **Upload multiple files** in the item menu of the tree.
2. **Target location** (3 minutes). The page shows **Upload files**, then **Target location**: the path, marked
   *Uploads allowed* when files can go there. **Quick locations** (such as **Media**, **Images**, **Files**) and
   **Change location...** choose another place. The page remembers the last place.
3. **Add files** (3 minutes): **Choose files...**, **Choose a folder...**, dragging onto the dashed area, or pasting
   an image. The page shows the largest file it accepts.
4. **The queue** (5 minutes). Nothing is sent yet. Every file shows its name, size, type, a preview, the item type
   it becomes, where it goes and a **title** (the file name by default). Fill in the **alternative text** where the
   site asks for it. A file that cannot be uploaded says why. A file with the same name and size already in the
   location is marked; you choose to skip it, rename the new one, or upload anyway.
5. **Options** (3 minutes). **Keep as drafts**: *The new items are not published; they wait in My drafts until they
   are edited and published.* **Import compressed zip file(s) contents**: off, a ZIP file is uploaded as one file;
   on, the files inside it are uploaded one by one.
6. **Upload and after** (3 minutes). **Upload all** sends three files at a time with a progress bar; **Cancel all**,
   **Retry failed** and **Clear finished** manage the queue. The **Uploaded** list shows each new item with **View**
   and **Edit**; tick items to use **Move to...**, **Add location...** or **Remove...**.

### Common questions

**Which file types can I upload?** Your site decides. A row in the queue says when a type is not allowed there.

**Why were the images from my ZIP file skipped?** Where the site requires an alternative text, images from a ZIP
file are skipped, because a ZIP file has no place for it. Upload them one by one instead.

**What is the size limit?** The page shows it, for example *At most 64 MB per file*. It is a setting of the server.

### Pitfalls

- **Uploading into the wrong place.** The page remembers the last location, which may be someone else's folder on a
  shared training site. Ask learners to read **Target location** before **Upload all**.
- **Personal images.** Ask learners to bring neutral sample images (landscapes, objects). Never upload photos of
  real people without their consent, not even on a training site.
- **Huge photos.** Very large camera images take long to upload on a classroom network. Prepare small samples.

## Hands-on exercise

Use the training installation, your course folder and the sample images on your computer.

1. Open your course folder and press **Upload**. The upload page opens with your folder as **Target location**.
2. Check that the location shows *Uploads allowed*. If it does not, tell the trainer.
3. Press **Choose files...** and choose three sample images.
4. In the queue, read for each file the item type it becomes and where it goes.
5. Change each **title** to a clear name, for example `Training - lake at sunset`.
6. Fill in the **alternative text** of each image with a short description of what it shows.
7. Tick **Keep as drafts**.
8. Press **Upload all** and watch the progress bars.
9. In the **Uploaded** list, press **View** on one item.
10. Open **My drafts** on the dashboard: your three images are listed as drafts.
11. Open one image draft with **Edit**, check its fields and press **Send for publishing**. Publish the other two the
    same way.
12. Back in your course folder, check that the three images are in the **Sub items** list.

## Expected result

- Three *Image* items with clear titles and alternative texts are published in the learner's course folder.
- **My drafts** listed them after the upload and is empty for them after step 11.
- The trainer checks by opening one learner's folder and one image's **View** tab on the projector.

## Quiz

1. Name two ways to open the upload page.
2. What does **Keep as drafts** do?
3. Two files in the queue cannot be uploaded. Where do you see why?
   A. In an e-mail B. In the row of each file in the queue C. Only in the server log D. Nowhere
4. What happens to a ZIP file when **Import compressed zip file(s) contents** is off?
5. Why should you check **Target location** before pressing **Upload all**?

<details>
<summary>Answers</summary>

1. Any two of: **Upload files** on the dashboard, **Upload** on a folder's page, **Upload multiple files** in the item
   menu ([Images, media and uploads](../../../media-and-uploads.md#upload-files)).
2. The new items are not published; they wait in **My drafts** until they are edited and published.
3. **B.** The row of each file says why.
4. It is uploaded as one file.
5. The page remembers the last location, which may not be the place you want.

</details>

## Further reading

- [Images, media and uploads](../../../media-and-uploads.md): the upload page and its options.
- [My drafts and my pending items](../../../../guides/drafts-and-pending.md): where kept drafts wait.
- [Quick location buttons](../../../../features/6.0/quick-location-buttons.md): how the quick locations are set.

## Related pages

- [Module 5: Images and media](README.md)
- [Editors 101: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
