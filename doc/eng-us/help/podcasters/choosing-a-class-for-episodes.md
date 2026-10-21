---
title: Choosing a class for episodes
audience: podcaster
category: podcasting-streaming
order: 7
summary: Which content class to use for podcast and video episodes - the Video class, the Audio and NG Video classes of media site installations, the File class, or a class of your own - with the fields each one has and what that means for the player and the feed.
keywords: content class, episode class, Video, Audio, ng_audio, ng_video, File, ezmedia, html5_audio, html5_video, upload.ini, MimeClassMap, choose class
type: help
---
# Choosing a class for episodes

This page is for producers and site builders who decide which kind of item an episode is. Which classes exist depends
on the site packages your site was installed with; ask your administrator, or open **Setup > Classes**. The field
lists below were read from a running installation with the shipped packages.

## Contents

1. [The candidates](#the-candidates)
2. [Video](#video)
3. [Audio (media site installations)](#audio-media-site-installations)
4. [NG Video (media site installations)](#ng-video-media-site-installations)
5. [File](#file)
6. [A class of your own](#a-class-of-your-own)
7. [What the upload page picks](#what-the-upload-page-picks)

## The candidates

| Class | Identifier | Where it comes from | Media field | Good for |
|---|---|---|---|---|
| Video | `video` | the classes package (`class-video.xml`) | media, Html5 Video | video episodes with a player |
| Audio | `ng_audio` | the media site demo content package | media, Html5 Audio | audio episodes with show notes and an image |
| NG Video | `ng_video` | the media site package | file field, or a YouTube, Vimeo or Dailymotion id | video pages that mix uploads and hosted video |
| File | `file` | every installation | file field | downloads, or audio when nothing else exists |
| Your own | any | built by your administrator | your choice | a show with its own needs |

## Video

Fields: **Name**, **File** (media, player type Html5 Video), **Caption** (rich text), **Tags** (keywords), **Star
Rating** and **Meta Data** on installations with the rating and metadata extensions. It has no separate show notes
field; the caption serves for that. Map **File** as the feed enclosure.

## Audio (media site installations)

Fields: **Title** (required), **Teaser title**, **Teaser intro**, **Description** (rich text), **File** (media,
player type Html5 Audio), **Image** and **Meta Data**. This is the closest to a ready episode class: map **Title**,
**Description** and **File** in the feed. The design templates for it are on installations with the media theme
(`extension/sevenx_themes_media/design/media/templates/content/views/full/ng_audio.tpl` and its siblings).

## NG Video (media site installations)

Described as "HTML5, YouTube, Vimeo and Dailymotion video". Fields include **Title**, **Video type** (YouTube,
Vimeo, Dailymotion or an uploaded file), **Video file** (file field), **Video identifier** for the hosted video,
**Poster**, **Teaser intro text**, **Description**, **Main topic** and **Tags** (eztags), **Sponsored content
disclosure** and **Autoplay**. The full view plays YouTube only after a click
(`extension/sevenx_themes_media/design/media/override/templates/node/view/full/full_ng_video.tpl`).

For the feed, only items with an uploaded **Video file** can carry an enclosure; an item that points at YouTube has
nothing to enclose and is left out of a podcast feed.

## File

Fields: a name, a description and the **File** (file field). It shows a download link, not a player, unless your
design adds one. It counts downloads ([Downloads and statistics](downloads-and-statistics.md)). As shipped, MP3
uploads become *File* items (see below), so many sites start with this class without choosing it.

## A class of your own

When none of these fits, your administrator builds an episode class with exactly the fields your show needs: show
notes, guests, a release date for scheduling, a transcript, a license. A suggested set is on
[Building an episode class](building-an-episode-class.md).

## What the upload page picks

When files are uploaded through the upload page, `settings/upload.ini [CreateSettings] MimeClassMap[]` decides the
class by MIME type. Shipped: images to `image`, `video/quicktime` to `quicktime`, `video/x-msvideo` to
`windows_media`, Real video to `real_video`, Flash to `flash`, **`video/mp4` to `video`**, and everything else to
`DefaultClass=file`. There is no entry for `audio/mpeg`, so an uploaded MP3 becomes a **File** item. To get *Audio*
items, the administrator adds `MimeClassMap[audio/mpeg]=ng_audio` (or your own class) in an override, with the class's
upload settings ([Working with your administrator](working-with-your-administrator.md)).

Creating an item with **Create new** in the folder and choosing the class avoids the question altogether.

## Related pages

- [The media field and the file field](media-field-and-file-field.md)
- [Content classes for websites](../creators/website-creators/content-classes-for-websites.md)
- [Choosing datatypes](../creators/website-creators/choosing-datatypes.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
