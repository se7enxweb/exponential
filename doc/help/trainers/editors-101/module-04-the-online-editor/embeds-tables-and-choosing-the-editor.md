---
title: "Lesson 4.3: Embeds, tables and choosing your editor"
audience: trainer, editor
category: training
order: 43
summary: Learners embed an image in a text, change its size and alignment, build a small table, and choose their own editor engine on the Online editor page. 60 minutes.
keywords: editors 101, online editor, embed, embed object, image in text, upload tab, table, insert table, rows, columns, caption, editor engine, TinyMCE 8, TinyMCE 3, Tiptap, ezoe engine
type: help
---
# Lesson 4.3: Embeds, tables and choosing your editor

A rich text field can hold more than words: images and other items of the site can be **embedded** in it, and
information can be set out in a **table**. This lesson teaches both, and ends with the page where each editor
chooses the online editor they prefer.

## At a glance

| | |
|---|---|
| Course | [Editors 101](../README.md) |
| Module | [Module 4: The Online Editor](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Beginner |
| Audience | New editors and authors |
| Prerequisites | [Lesson 4.2](links-and-anchors.md); at least one image in the training site's media library |

## Learning objectives

After this lesson, learners can:

- embed an existing image in a rich text field with the embed dialog's search;
- change an embed's size and alignment by double-clicking it;
- insert a table, add a row and a column, and give it a caption;
- open the **Online editor** page and choose their editor engine;
- explain that switching engines keeps the stored text.

## Trainer's notes

### How to present it

Source: [The online editor on TinyMCE 8](../../../../features/6.0/online-editor-tinymce8.md).

1. **Embedding** (6 minutes). In **Body**, put the cursor in an empty paragraph and press the embed button
   (**Embed object**). The dialog searches the site's items, with a class filter and a preview. Choose an image and
   confirm. Then double-click the embed: you can change its view, class, size (for images), alignment and whether it
   is inline. Mention the **Upload** tab, which uploads a new file and embeds it at once, choosing "From your
   computer" or, where the site allows it, "From a URL". Module 5 covers uploads properly.
2. **Tables** (6 minutes). Press the table button. With the shipped settings the dialog shows a size grid, the width
   and border (in px or %), a class, a summary and a caption. Insert a 3 by 3 table, type into it, then use the
   table buttons to add a row after and a column after, and to delete a row.
3. **Choosing the editor** (5 minutes). Open the **Online editor** page (address
   `/ezoe/engine` after the administration's address). It lists the editors with their version, description and
   status, and the list **Editor for text fields**, whose first entry is the site's default (for example *Default of
   this site (TinyMCE 8)*). Choose and press **Save**. Where the site has switched it on, a switch named **Editor**
   below each rich text field does the same during editing; a click stores the draft with the text typed so far and
   switches.
4. **Why switching is safe** (3 minutes). The engines write the same stored text, so an article written with one can
   be edited with the other.

### Common questions

**Can I embed a document or a video, not only an image?** Yes, the embed dialog embeds other items too; how they
appear on the site depends on the site's design.

**Where does an image uploaded through the Upload tab go?** It becomes an item of its own, in the place the site has
set for such uploads; it then appears in the media library or the place named in the dialog.

**Which editor should I choose?** The one your team trains with. All of them store the same text; the difference is
in the toolbar and the dialogs.

**Why does my list of editors show only one or two entries?** The list shows the engines your installation offers.

### Pitfalls

- **Embedding without alternative text.** The image's alternative text comes from the image item. If it is missing,
  fix it on the image (lesson 5.3), not in the article.
- **Huge tables.** Tables are for tabular data, not for layout. Keep them small and give them a caption.
- **Switching engines mid-edit.** The switch stores the draft before switching; teach learners to switch at the start
  of an edit anyway.

## Hands-on exercise

Use the training installation and your article *My first article*.

1. Open your article and press **Edit**. Go to **Body** and put the cursor in a new, empty paragraph.
2. Press the embed button. In the dialog, search for an image the trainer named, choose it and confirm.
3. Double-click the embedded image. Change its size to a smaller one and its alignment to right, and confirm.
4. In a new paragraph below, press the table button. Insert a table of 3 columns and 3 rows, and type the caption
   `My course plan`.
5. Fill in the first row with `Day`, `Topic`, `Done`, and two more rows with your own entries.
6. Put the cursor in the last row and add a row after it with the table buttons. Then delete it again.
7. Press **Send for publishing** and check the image and the table with **View on site**.
8. Open the **Online editor** page at the address the trainer gives you (`/ezoe/engine` after the administration's
   address).
9. Read the table of editors and write down the default of this site from the list **Editor for text fields**.
10. Choose another editor from the list (if there is one), press **Save**, and open your article with **Edit**: the
    toolbar looks different but your text is the same.
11. Press **Discard draft**, go back to the **Online editor** page, choose the site's default again and press
    **Save**.

## Expected result

- The published article shows the image, smaller and aligned right, and a table with the caption *My course plan*.
- The learner wrote down the site's default editor (on a current training installation, often *TinyMCE 8*; it is a
  setting of the site).
- In step 10 the same text opened in another editor, and the learner switched back in step 11.
- The trainer checks by opening one learner's article on the site and the **Online editor** page on the projector.

![Below a rich text field, the switch between the editors the site offers, outlined](../../../images/trainers/e101-editor-switch.webp)

## Quiz

1. How do you change the size of an image already embedded in a text?
2. What does the embed dialog's **Upload** tab do?
3. True or false: an article written with TinyMCE 8 cannot be edited with TinyMCE 3.
4. Name three things the table dialog lets you set.
5. Where do you choose your own editor engine?
   A. In the right column's **Interface** box B. On the **Online editor** page C. In the article's **Details** tab
   D. Only an administrator can

<details>
<summary>Answers</summary>

1. Double-click the embed and change its size in the dialog.
2. It uploads a new file, creates an item from it, and embeds it in the text.
3. **False.** Both engines store the same text
   ([The online editor on TinyMCE 8](../../../../features/6.0/online-editor-tinymce8.md)).
4. Any three of: size, width, border, class, summary, caption.
5. **B.** The **Online editor** page (`/ezoe/engine`), or the **Editor** switch below the field where the site shows it.

</details>

## Further reading

- [The online editor on TinyMCE 8](../../../../features/6.0/online-editor-tinymce8.md): embeds, tables, engines and
  settings.
- [ezoe and TinyMCE 8](../../../../bc/6.0/ezoe-tinymce8.md): the reference for administrators.
- [Images, media and uploads](../../../media-and-uploads.md): where uploaded files go.

## Related pages

- [Module 4: The Online Editor](README.md)
- [Editors 101: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
