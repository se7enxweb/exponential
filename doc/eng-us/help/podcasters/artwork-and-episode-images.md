---
title: Artwork and episode images
audience: podcaster
category: podcasting-streaming
order: 16
summary: Where show artwork and episode images go in Exponential - the feed's Artwork address written as itunes:image, the RSS 2.0 image of other exports, image fields of episode classes and their variations on the site - and what reaches podcast apps and what does not.
keywords: artwork, cover art, itunes:image, Artwork address, episode image, image field, image variations, alternative text, poster, podcast cover, show image
type: help
---
# Artwork and episode images

This page is for producers and designers who prepare the show's cover art and the images of episodes. Exponential
handles two different things here: the **artwork address** that podcast apps read from the feed, and **images on the
site**, which apps never see.

## Contents

1. [Show artwork in the feed](#show-artwork-in-the-feed)
2. [Hosting the artwork file](#hosting-the-artwork-file)
3. [Episode images](#episode-images)
4. [Images on the show page](#images-on-the-show-page)
5. [Alternative text](#alternative-text)

## Show artwork in the feed

The podcast export has an **Artwork address** field in its **Apple Podcasts** section. It is an address, not an
upload. `generateITunes()` writes it as `<itunes:image href="..."/>` on the channel; a code comment notes that this
element must carry the address in the `href` attribute, and that writing it as text "is the single most common reason a
feed is rejected for having no artwork" (`kernel/classes/ezrssexport.php`). The address is checked when the export is
saved (`eZRSSExportOPMLItem::safeURL()` in `kernel/classes/ezrssexportopmlitem.php`): white space is removed, an
address with a scheme other than `http` or `https` is dropped, and it is held to 255 characters. A relative address
passes the check, but apps fetch the image from elsewhere, so always enter a full `https://` address.

Podcast directories set their own rules for the artwork's size, format and color space. Exponential does not check
the image behind the address; check the current requirements of each directory you submit to.

## Hosting the artwork file

The simplest place for the artwork is the site itself:

1. Upload the cover image to the media library as an *Image* item ([Images, media and uploads](../media-and-uploads.md)).
2. Open the image item on the site and copy the address of the original image file.
3. Paste that address into **Artwork address** of the export.

> **Note**
> Exponential changes an image's address when the image is replaced (the version is part of the URL, as the
> [CDN](../hosting/performance/cdn.md) page explains). If you replace the cover, copy the new address into the
> export again; otherwise the feed keeps pointing to the old file.

The other feed formats (RSS 2.0) have an **Image** of their own on the export form, chosen from the site's images;
it is not used by the podcast format.

## Episode images

The podcast feed writes **no image per episode**: apps show the show artwork for every episode
([What is not built in](what-is-not-built-in.md)). Episode images are for the site:

| Class | Image field |
|---|---|
| Audio (`ng_audio`, media site installations) | **Image** |
| NG Video (media site installations) | **Poster** |
| Video | none |
| Your own episode class | as you define it ([Building an episode class](building-an-episode-class.md)) |

On the site, image fields are shown in sizes called **variations** (aliases), produced from the original by the
image system (`settings/image.ini`); the design decides which size each view uses
([Images and image variations](../creators/website-creators/images-and-image-variations.md)).

## Images on the show page

The show folder can carry an image for the show page, and social networks read the page's metadata when someone
shares a link ([Metadata and social sharing](../creators/website-creators/metadata-and-social-sharing.md)). That is
separate from the feed artwork; keep the two images consistent.

## Alternative text

Every image on the site needs an alternative text that says what it shows. For cover art, name the show and what is
on the image: "Cover of The Example Show: two microphones on a blue background." Where the site requires it, the
upload dialogs ask for it ([Images, media and uploads](../media-and-uploads.md#good-practice)).

## Related pages

- [Channel fields reference](channel-fields-reference.md)
- [SVG and image formats](../creators/website-creators/svg-and-image-formats.md)
- [Titles, thumbnails and descriptions](../press/titles-thumbnails-and-descriptions.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
