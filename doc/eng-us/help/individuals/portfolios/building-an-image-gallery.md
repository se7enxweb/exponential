---
title: Building an image gallery
audience: individual, website-creator, editor
category: website-creation
order: 1611
summary: Step by step, how one person builds a portfolio gallery with the shipped Gallery and Image classes - the tree, the cover image, captions and alternative text, sort order, tags and ratings, and what to check before showing it to anyone.
keywords: gallery, image gallery, portfolio, Gallery class, Image class, caption, alternative text, cover image, sort order, tags, rating, ezstarrating, series, photography
type: help
---
# Building an image gallery

This page is for individuals who want a gallery of their work. It uses the *Gallery* and *Image* classes of the
default site package; the gallery's full view lists the images below it (templates `full/gallery.tpl` in the
`ezwebin` design and its successors).

## Contents

1. [The classes](#the-classes)
2. [Step by step](#step-by-step)
3. [Captions and alternative text](#captions-and-alternative-text)
4. [Order](#order)
5. [Tags and ratings](#tags-and-ratings)
6. [Before you show it](#before-you-show-it)

## The classes

| Class | Fields (identifiers in the class definition) | Role |
|---|---|---|
| *Gallery* | `name`, `short_description`, `description`, `image`, `tags`, `rating`, `metadata` | The gallery page; `image` is its cover |
| *Image* | `name`, `caption`, `image`, `tags`, `rating`, `metadata` | One work |

Source: `class-gallery.xml` and `class-image.xml` in the `sevenx_classes` package. If your work needs more fields
(year, medium, dimensions), add them to *Image* or make your own class
([Designing your content model](../../creators/website-creators/designing-your-content-model.md)).

## Step by step

1. **Make a home for the portfolio**: a *Folder* called "Work" or "Portfolio" under the home page.
2. **Create a *Gallery*** below it for the first series. Fill in the name, a one-line short description and a longer
   description of the series.
3. **Choose a cover image** for the gallery's `image` field. Lists of galleries show it.
4. **Publish the gallery.** It is empty for now.
5. **Add images below it**, one *Image* per work: either one by one (**Create here** > *Image*) or many at once
   ([Uploading many images](uploading-many-images.md)).
6. **Fill in each image's name and caption**; crop or rotate if needed with `ezie`
   ([Editing images with ezie](editing-images-with-ezie.md)).
7. **Publish** and look at the gallery as a visitor.
8. Repeat for the next series.

## Captions and alternative text

They do different jobs; fill in both.

| Field | For | Example |
|---|---|---|
| Name | The title of the work | "Harbor at dawn" |
| Caption | Context for every reader: year, place, technique | "Oil on canvas, 2025, 60 x 80 cm" |
| Alternative text (on the image field) | People who cannot see the image, and search engines | "A small fishing harbor under an orange morning sky, boats in silhouette" |

Describe what is in the picture, not that it is a picture. See
[Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md).

## Order

Galleries list their images in the sort order of the gallery node. In the administration, set the gallery's
sorting (by name, by date published, or by priority) and, with priority, give each image a number to place it by
hand ([Structuring the content tree](../../creators/website-creators/structuring-the-content-tree.md)). Put your
strongest work first; most visitors see three to five images before they decide.

## Tags and ratings

- **Tags** (the keyword field, or `eztags` if you add it) let a visitor see every work about "harbors" or in "watercolor" across galleries. Agree a short
  list of tags with yourself before you start; ten good tags beat a hundred one-off ones
  ([Tags and categories](../blogs/tags-and-categories.md)).
- **Rating** is a star-rating field (`ezstarrating`): visitors can rate between 1 and 5 stars, once per session
  (its `README`). Leave it out of your templates if you do not want public ratings.

## Before you show it

- [ ] Every image has a name, a caption and alternative text.
- [ ] Covers chosen for every gallery.
- [ ] Order puts the best first.
- [ ] Location data removed from originals you did not want to share.
- [ ] Only work you may publish; people in photos agreed.
- [ ] Looks right on a phone, and loads quickly ([Image variations for portfolios](image-variations-for-portfolios.md)).

## Related pages

- [Portfolios](README.md)
- [Uploading many images](uploading-many-images.md)
- [Images and image variations](../../creators/website-creators/images-and-image-variations.md)
- [Media and uploads](../../media-and-uploads.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
