---
title: "Lesson 5.3: Images in articles and alternative text"
audience: trainer, editor
category: training
order: 53
summary: Learners give an article its main image through the Image relation field, upload a new image straight from the edit form, and write good alternative texts. 45 minutes.
keywords: editors 101, image, relation, add an existing object, upload a file, remove object, alternative image text, alt text, accessibility, ezimage, article image
type: help
---
# Lesson 5.3: Images in articles and alternative text

An article usually has a main image, shown at the top or in lists of articles. On a standard installation that image
is not uploaded into the article: the article's **Image** field points to an *Image* item. This lesson teaches that
field, uploading from the edit form, and the most important text an image has: its alternative text.

## At a glance

| | |
|---|---|
| Course | [Editors 101](../README.md) |
| Module | [Module 5: Images and media](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Beginner |
| Audience | New editors and authors |
| Prerequisites | [Lesson 5.2](the-upload-page.md): each learner has three images in their course folder |

## Learning objectives

After this lesson, learners can:

- choose an existing image for an article's **Image** field with **Add an existing object**;
- upload a new image from the edit form with **Upload a file**;
- remove the image from the field with **Remove object** without deleting the image item;
- write an alternative text that describes what an image shows, and fill in **Alternative image text** on an image.

## Trainer's notes

### How to present it

Sources: [Relations and images](../../../editing-content.md#relations-and-images) and
[Required alternative text](../../../../guides/content-model-and-editing.md#alternative-text-is-required-image-fields).

1. **A relation field** (4 minutes). Open an article with **Edit** and go to **Image**. Its kind in the corner is a
   relation: it links to another item. The buttons:
   - **Add an existing object** opens a browser in which you pick an item that is already there;
   - **Upload a file** uploads a file from your computer, creates an item from it, and links it;
   - **Remove object** takes the image out of the field; the image item itself stays.
2. **Where uploads land** (2 minutes). An image uploaded from the form becomes an item in the place your site has set
   for such uploads. Show where it landed after the demonstration.
3. **The image item's fields** (3 minutes). Open an image item with **Edit**: **Name**, **Caption** and **Image**,
   which shows **Current image** with its file name, MIME type and size, **Alternative image text** and
   **New image file for upload**. Where the site requires it, the label reads **Alternative image text (required)**
   and the form refuses an image without it: *An alternative text is required for this image.*
4. **Good alternative text** (6 minutes). Say what the image shows and why it is there, in a sentence: "Three
   runners crossing the finish line in the rain", not "image" or "photo123". A purely decorative image needs no long
   text. Write two examples on the board and let the class improve them.

### Common questions

**Is the alternative text the same as the caption?** No. The caption is visible to everyone; the alternative text is
for people who cannot see the image and for when it cannot load.

**Why does the multi-upload not ask for the alternative text?** It does where the site asks for it in the queue
(lesson 5.2). Imports and the generic upload create images without asking; fill in the text afterwards.

**If I remove the image from the article, is it deleted?** No. **Remove object** only takes it out of the field.

### Pitfalls

- **Copying the file name as alternative text.** `DSC_0042.jpg` helps nobody. Insist on a description.
- **Alternative text that repeats the caption word for word.** Screen reader users then hear it twice.
- **Changing a shared image.** Editing the image item changes it in every article that uses it. Check its
  **Relations** tab first (lesson 5.1).

## Hands-on exercise

Use the training installation, your course folder and one more sample image on your computer.

1. Open your article *News one (updated)* and press **Edit**.
2. Go to the **Image** field with the field bar.
3. Press **Add an existing object**. In the browser that opens, go to your course folder and choose one of the images
   you uploaded in lesson 5.2. Confirm.
4. Press **Send for publishing** and look at the article with **View on site**.
5. Open *News two (updated)* and press **Edit**. In **Image**, press **Upload a file**, choose your extra sample image
   and fill in its name and, where asked, its alternative text.
6. Press **Send for publishing**. Find out where the uploaded image landed (open the image from the article's
   **Relations** tab).
7. Open that image item and press **Edit**. Read **Current image**, then improve the **Alternative image text** so it
   describes what the image shows.
8. Press **Send for publishing**.
9. Open *News one (updated)* again with **Edit**, press **Remove object** in **Image**, and then **Discard draft**.
   The image is still in the published article.

## Expected result

- Both news articles show a main image on the site.
- The image uploaded in step 5 exists as an *Image* item in the place the site sets for such uploads, with a
  descriptive alternative text.
- After step 9 the published article still has its image, because the draft was discarded.
- The trainer checks by reading two learners' alternative texts aloud and discussing them.

![The relation field of an article with Add existing objects and Upload a file](../../../images/trainers/e101-image-field.webp)

## Quiz

1. What does **Add an existing object** do?
2. You press **Remove object** in an article's **Image** field and publish. What happens to the image item?
   A. It is deleted B. It goes to the trash C. It stays; only the link is removed D. It is hidden
3. Write a better alternative text for an image currently described as `photo`.
4. What does the label **Alternative image text (required)** tell you?
5. Where does an image uploaded with **Upload a file** from the edit form go?

<details>
<summary>Answers</summary>

1. It opens a browser to pick an existing item and links it in the field
   ([Creating and editing content](../../../editing-content.md#relations-and-images)).
2. **C.** The image item stays; only the relation is removed.
3. A sentence that says what the image shows and why it is there, for example "The training room with ten people at
   laptops".
4. The site requires an alternative text: the image cannot be stored without it.
5. To the place the site has set for such uploads; it becomes an *Image* item there.

</details>

## Further reading

- [Creating and editing content](../../../editing-content.md#relations-and-images): relation fields.
- [The content model and editing content](../../../../guides/content-model-and-editing.md#alternative-text-is-required-image-fields):
  the required alternative text option.
- [Images, media and uploads](../../../media-and-uploads.md#good-practice): good practice with images and files.

## Related pages

- [Module 5: Images and media](README.md)
- [Editors 101: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
