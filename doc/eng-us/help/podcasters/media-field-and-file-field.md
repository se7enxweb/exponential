---
title: The media field and the file field
audience: podcaster
category: podcasting-streaming
order: 5
summary: The two field types that hold audio and video in Exponential - the media datatype (ezmedia) with its player types and options, and the file datatype (ezbinaryfile) with its download counter - how each is stored, delivered and used by the podcast feed.
keywords: ezmedia, ezbinaryfile, media field, file field, player type, Html5 Audio, Html5 Video, autoplay, loop, controller, content/download, Range, Content-Disposition, download count, storage
type: help
---
# The media field and the file field

This page is for producers and administrators who choose how episodes store their audio or video. Exponential has
two datatypes for this. Both can be the **enclosure** of a podcast feed: `podcastItemList()` in
`kernel/classes/ezrssexport.php` accepts an `eZMedia` or an `eZBinaryFile` value.

## Contents

1. [Side by side](#side-by-side)
2. [The media field](#the-media-field)
3. [The file field](#the-file-field)
4. [How files are stored](#how-files-are-stored)
5. [How files are delivered](#how-files-are-delivered)
6. [Which one to use](#which-one-to-use)

## Side by side

| | Media (`ezmedia`) | File (`ezbinaryfile`) |
|---|---|---|
| Shows a player on the page | yes, by its player type | no, a download link |
| Options per item | width, height, autoplay, loop, controller | none |
| Maximum size per class attribute | yes, in MB | yes, in MB |
| Counts downloads | no | yes (`download_count`) |
| Usable as a feed enclosure | yes | yes |
| Used by | *Video* class (Html5 Video), *Audio* class on media site installations (Html5 Audio) | *File* class, *NG Video* (video file) |

## The media field

The class attribute has a **Media player type** and a **Max file size** (`design/standard/templates/class/datatype/edit/ezmedia.tpl`).
The player types are Flash, QuickTime, RealPlayer, Silverlight, Windows Media Player, **Html5 Video** and **Html5
Audio**. Only the two HTML5 types work in current browsers; the others write plugin markup for players that no longer
exist, and the site's Content Security Policy blocks `<object>` and `<embed>` when it is enforced
(`settings/site.ini [ContentSecurityPolicy] Directives[object-src]='none'`).

When an editor fills an item, the form offers the file and, for the HTML5 types, **Width**, **Height**, **Autoplay**,
**Loop** and **Controller** (`design/standard/templates/content/datatype/edit/ezmedia.tpl`). The view template
`design/standard/templates/content/datatype/view/ezmedia.tpl` turns them into attributes:

| Option | HTML5 attribute |
|---|---|
| Controller | `controls` |
| Autoplay | `autoplay` |
| Loop | `loop` |
| Width, Height | `width`, `height` (video only) |

The template always writes `preload="none"`, so a page with many players does not start loading every file. It
writes no `<track>` and no `poster`.

## The file field

The file field stores any file and shows a download link with its name and size. Each download through
`content/download` adds one to its `download_count` (`kernel/classes/datatypes/ezbinaryfile/ezbinaryfiletype.php`).
It plays nothing by itself; a design template can still put the file into an `<audio>` or `<video>` element, as the
*NG Video* template does with a `<video>` element for uploaded video on media site installations
(`extension/sevenx_themes_media/design/media/override/templates/node/view/full/full_ng_video.tpl`).

## How files are stored

Uploaded media are stored below the site's storage directory in `original/<mime group>`, for example
`original/audio` or `original/video` (`kernel/classes/datatypes/ezmedia/ezmediatype.php`). The group comes from the
file's MIME type, which comes from its suffix through `settings/mime.ini`. See
[Storage and bandwidth](storage-and-bandwidth.md) for disk planning.

## How files are delivered

Players and podcast apps fetch the file from
`/content/download/<object id>/<attribute id>/<file name>`. That view checks that the visitor may read the item, then
the handler `eZFilePassthroughHandler` sends the file through PHP
(`kernel/classes/binaryhandlers/ezfilepassthrough/ezfilepassthroughhandler.php`):

- it answers `Range` requests with `206 Partial Content`, so players can seek and apps can resume;
- it sends `Accept-Ranges: bytes`, the `Content-Length` and an `Expires` ten minutes ahead
  (`eZFile::downloadHeaders()` in `lib/ezfile/classes/ezfile.php`);
- it sends `Content-Disposition: attachment` unless `settings/file.ini [PassThroughSettings] ContentDisposition[]`
  says `inline` for the type. The shipped list has `inline` for `video/mp4`, `video/webm`, `video/ogg` and Flash, not
  for audio. An `<audio>` element still plays the file; a visitor who opens the link directly gets a download.

## Which one to use

- An episode page with a player: **media** field, player type **Html5 Audio** or **Html5 Video**.
- A download counter matters more than an on-page player: **file** field, plus a player in your design template.
- Both: some sites keep the audio in a media field and offer a separate high-quality download in a file field.

## Related pages

- [Choosing a class for episodes](choosing-a-class-for-episodes.md)
- [Uploading audio](uploading-audio.md)
- [Files and downloads](../creators/website-creators/files-and-downloads.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
