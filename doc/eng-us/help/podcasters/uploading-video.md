---
title: Uploading video
audience: podcaster
category: podcasting-streaming
order: 12
summary: How to put a video episode on the site - when to upload the file and when to host it on a video service, the Video class and its Html5 Video player, MP4 and WebM, sizes and the checks after publishing.
keywords: upload video, MP4, WebM, video file, Video class, Html5 Video, video podcast, video episode, poster, width, height, video hosting, YouTube, Vimeo
type: help
---
# Uploading video

This page is for producers of video podcasts, video episodes and clips. Exponential can store and play a video file,
and it can embed a video from a video service. Which is right depends on the file size, the audience and what the
player must do.

## Contents

1. [Upload or embed](#upload-or-embed)
2. [Prepare the file](#prepare-the-file)
3. [Upload a video](#upload-a-video)
4. [Player options](#player-options)
5. [Check the result](#check-the-result)

## Upload or embed

| Need | Upload to the site | Embed from a video service |
|---|---|---|
| A video podcast feed with the file as enclosure | yes: the feed needs a file on the site | no: there is no file to enclose |
| Captions the viewer can switch on | not as shipped (no `<track>` in the template) | yes, if the service supports them |
| Adaptive quality for slow connections | no: one file, as uploaded | yes, usually |
| Very large files, many viewers | possible, but every view goes through your server | the service carries the load |
| No third party on the page | yes | no |

For embedding, see [Embedding a video or a stream](embedding-a-video-or-stream.md).

## Prepare the file

Exponential plays the file you upload as it is. Export it at the size you want viewers to download.

| Choice | Recommendation | Why |
|---|---|---|
| Format | MP4 (`video/mp4`) or WebM | both are in `settings/mime.ini` and play in current browsers |
| Opens in the browser | MP4, WebM and Ogg video | `settings/file.ini [PassThroughSettings]` sends them `inline` |
| Size | as small as the quality allows | see [Upload limits and large files](upload-limits-and-large-files.md) |
| File name | short, lowercase, no spaces | it is part of the address |

## Upload a video

**In the edit form.** Create a **Video** item (or your video episode class) in the show folder. In **File** choose the
video under **New file for upload**, fill in the name and caption, and press **Send for publishing**. The *Video*
class's file field uses the player type **Html5 Video** (`class-video.xml` in the shipped classes package).

**On the upload page.** `settings/upload.ini [CreateSettings]` maps `video/mp4` to the class `video`, with
`[video_ClassSettings] FileAttribute=file`. So an uploaded MP4 becomes a *Video* item with the file in its player
field. Older formats are mapped to the old plugin classes (`video/quicktime` to *Quicktime*, `video/x-msvideo` to
*Windows media*); those classes write plugin markup that current browsers do not play, so convert such files to MP4
first. See [Images, media and uploads](../media-and-uploads.md#upload-files).

**On media site installations**, the *NG Video* class takes either an uploaded **Video file** or the identifier of a
YouTube, Vimeo or Dailymotion video, with a **Poster** image
([Choosing a class for episodes](choosing-a-class-for-episodes.md)).

## Player options

| Option | Effect | Recommendation |
|---|---|---|
| **Width**, **Height** | the player's size in pixels; 0 leaves them out | set them, or leave the design to size the player |
| **Controller** | `controls` | always on |
| **Autoplay** | starts on page load | off, for accessibility and courtesy |
| **Loop** | starts again at the end | off for episodes |

The player gets `preload="none"`: nothing is downloaded until play is pressed, and no frame is shown before that
(`design/standard/templates/content/datatype/view/ezmedia.tpl`). There is no poster field in the *Video* class; a
design override can add one.

## Check the result

- [ ] Open the page signed out and on a phone: the video plays and seeking works.
- [ ] The field shows **MIME type** `video/mp4` (or `video/webm`) and the expected **Size**.
- [ ] If the video has speech, captions or a transcript are on the page ([Transcripts and captions](transcripts-and-captions.md)).
- [ ] For a video podcast feed, the enclosure carries the same type and length ([Checking your feed](checking-your-feed.md)).

## Related pages

- [Uploading audio](uploading-audio.md)
- [Embedding a video or a stream](embedding-a-video-or-stream.md)
- [Storage and bandwidth](storage-and-bandwidth.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
