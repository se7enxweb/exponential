---
title: Uploading audio
audience: podcaster
category: podcasting-streaming
order: 11
summary: How to put an episode's audio on the site - prepare the file, upload it in the episode's edit form or on the upload page, choose the player options, and check the file type and size that the podcast feed will announce.
keywords: upload audio, MP3, audio file, media field, Html5 Audio, upload page, multiupload, file type, audio/mpeg, m4a, file size, controls, autoplay, episode audio
type: help
---
# Uploading audio

This page is for producers and editors who add the audio of an episode. It covers preparing the file, the two ways
to upload it, and the checks that keep the feed valid. Video is on [Uploading video](uploading-video.md).

## Contents

1. [Prepare the file](#prepare-the-file)
2. [Upload in the edit form](#upload-in-the-edit-form)
3. [Upload on the upload page](#upload-on-the-upload-page)
4. [Player options](#player-options)
5. [Check the file](#check-the-file)
6. [Replacing a file](#replacing-a-file)

## Prepare the file

Exponential stores the file exactly as you upload it; it does not convert or compress audio
([What is not built in](what-is-not-built-in.md)). So prepare the final file in your editing software:

| Choice | Recommendation | Why |
|---|---|---|
| Format | MP3 | `settings/mime.ini` maps `.mp3` to `audio/mpeg`; every podcast app plays it |
| Bitrate | constant (CBR) | the feed's `itunes:duration` is worked out only for CBR MP3 |
| File name | short, lowercase, no spaces, e.g. `example-show-042.mp3` | the name is part of the download address |
| Size | within your site's upload limit | the upload page shows the limit |

Formats such as M4A, AAC or Opus are not in the shipped `mime.ini`; uploaded as they are, they get the type
`application/octet-stream`, which podcast apps reject. Ask your administrator to add them first
([Working with your administrator](working-with-your-administrator.md)).

## Upload in the edit form

This is the usual way for an episode:

1. Create the episode in the show (or season) folder, or edit an existing one.
2. In the audio field (a media field), choose the file under **New file for upload**.
3. Fill in the other fields and press **Send for publishing**.

The file is uploaded with the form. A large file takes as long as your connection needs; keep the page open until
the form comes back. If the upload is refused, the field says why, for example that the file is larger than the
`upload_max_filesize` of PHP allows (`kernel/classes/datatypes/ezmedia/ezmediatype.php`).

## Upload on the upload page

The upload page takes many files at once ([Images, media and uploads](../media-and-uploads.md#upload-files)). Each
file becomes an item of the class that `settings/upload.ini [CreateSettings] MimeClassMap[]` names for its type. As
shipped there is no entry for `audio/mpeg`, so an MP3 becomes a **File** item (`DefaultClass=file`): a download, not
a player. Use the upload page for audio only when:

- your administrator has mapped `audio/mpeg` to your episode class ([Building an episode class](building-an-episode-class.md)), or
- *File* items are what you want, for example as a download archive.

The option **Keep as drafts** keeps the new items unpublished, so you can add show notes before anything appears.

## Player options

For a media field with player type **Html5 Audio**, the form offers:

| Option | Effect on the page | Recommendation |
|---|---|---|
| **Controller** | `controls`: play, pause, seek, volume | always on |
| **Autoplay** | starts playing when the page opens | off, for accessibility and courtesy |
| **Loop** | starts again at the end | off for episodes |

The template writes `preload="none"`, so the file is fetched only when someone presses play
(`design/standard/templates/content/datatype/view/ezmedia.tpl`).

## Check the file

After publishing, open the episode signed out and:

- [ ] press play: the audio starts, and seeking works (downloads answer `Range` requests);
- [ ] look at the field in the administration: **MIME type** says `audio/mpeg`, **Size** matches your file;
- [ ] open the feed when it is built: the item's `enclosure` has the same type and length
  ([Checking your feed](checking-your-feed.md)).

## Replacing a file

To fix the audio of a published episode, edit the episode and upload the new file in the same field, then publish.
The episode keeps its remote id, so podcast apps treat it as the same episode with a new file. Do not create a new
episode for a corrected file; apps would list it twice ([The podcast feed explained](the-podcast-feed-explained.md)).

The earlier version stays in the item's history, so you can go back to it; see
[Publishing, versions and the trash](../publishing-and-versions.md#go-back-to-an-earlier-version).

## Related pages

- [The media field and the file field](media-field-and-file-field.md)
- [Upload limits and large files](upload-limits-and-large-files.md)
- [Publishing an episode](publishing-an-episode.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
