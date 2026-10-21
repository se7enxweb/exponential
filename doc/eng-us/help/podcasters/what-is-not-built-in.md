---
title: What is not built in
audience: podcaster
category: podcasting-streaming
order: 3
summary: The honest list of what an Exponential site does not do for a podcast or a stream - no transcoding, no live ingest, no season and episode numbers in the feed, no transcript tag, no per-listener statistics, some audio formats not recognized - and what to do instead for each.
keywords: limitations, gaps, not supported, transcoding, live streaming, HLS, itunes:episode, itunes:season, transcript, chapters, m4a, aac, opus, captions, statistics, what is missing
type: help
---
# What is not built in

This page is for podcasters and producers who plan a show on Exponential and want to know the limits before they
promise anything. Every item below was checked in the code of the 6.0 line; each one says what to do instead. When a
later release closes a gap, this page changes.

## Contents

1. [Media processing](#media-processing)
2. [The podcast feed](#the-podcast-feed)
3. [Formats](#formats)
4. [Players, captions and embeds](#players-captions-and-embeds)
5. [Audience figures](#audience-figures)

## Media processing

| Not there | Why it matters | Do instead |
|---|---|---|
| No transcoding or encoding | The file you upload is the file listeners get, at its size and bitrate | Export the final MP3 or MP4 from your editing software at the bitrate you want, then upload it |
| No live ingest, no HLS or DASH | Exponential cannot receive a live stream from your encoder or deliver one | Stream through a streaming service and embed its player ([Live streams and premieres](live-streams-and-premieres.md)) |
| No waveform, chapter or clip tools | Nothing cuts or marks up audio in the browser | Do this in your audio editor; write chapter times into the show notes as text |

No code in `kernel/`, `lib/` or the shipped extensions calls an encoder such as ffmpeg.

## The podcast feed

The Apple Podcasts export (`eZRSSExport::generateITunes()` in `kernel/classes/ezrssexport.php`) writes the channel
and item elements listed on [The podcast feed explained](the-podcast-feed-explained.md). It does **not** write:

| Not written | Effect | Do instead |
|---|---|---|
| `itunes:episode`, `itunes:season`, `itunes:episodeType` | Apps do not show episode or season numbers from the feed | Put the number in the episode title ("Episode 42: ..."), and use folders for seasons on the site |
| A per-episode `itunes:explicit` | Every episode carries the channel's value | Set **Contains explicit content** for the whole show if any episode needs it |
| A per-episode `itunes:image` | Apps show the show artwork for every episode | Keep episode images on the site pages ([Artwork and episode images](artwork-and-episode-images.md)) |
| Transcript or chapter tags | Apps cannot fetch a transcript from the feed | Publish the transcript on the episode page ([Transcripts and captions](transcripts-and-captions.md)) |
| A chosen episode author | `itunes:author` of an item is the **user who created** the item | Credit guests and hosts in the show notes ([Guests and credits](guests-and-credits.md)) |

Two more rules come from the code: an episode whose enclosure field is empty is left out of the feed, and the
duration (`itunes:duration`) is worked out only for constant bitrate MPEG-1 Layer III audio, that is a CBR MP3
(`audioDuration()`). For a VBR MP3 or another format the duration is simply missing, which directories allow.

## Formats

`settings/mime.ini` lists `mp3` and `mp2` as `audio/mpeg`, `oga` as `audio/ogg`, `webm` as `video/webm` and
`audio/webm`, `mp4` as `video/mp4`, and `wav`. It has **no entries for `m4a`, `aac`, `opus`, `vtt` or `srt`**. A file
whose suffix is not listed gets the default type `application/octet-stream` (`eZMimeType::defaultMimeType()` in
`lib/ezutils/classes/ezmimetype.php`), and that type then appears in the feed's enclosure, which podcast apps do not
play.

Do instead: publish MP3, or ask your administrator to add the missing suffix in an override of `mime.ini`
([Working with your administrator](working-with-your-administrator.md)).

## Players, captions and embeds

| Not there | Do instead |
|---|---|
| A caption track: the media template writes no `<track>` element | Burn in captions, override the template in your design, or use a video service with captions ([Transcripts and captions](transcripts-and-captions.md)) |
| A poster image for the HTML5 video player of the media field | The *NG Video* class on media site installations has a *Poster* field; otherwise a design override |
| Arbitrary `<iframe>` code in rich text | Use the layouts editor's *External video* or *HTML snippet* block ([Embedding a video or a stream](embedding-a-video-or-stream.md)) |
| A modern video custom tag in the shipped ezwebin design | Its `video` tag writes Flash markup; do not use it ([Media in rich text](media-in-rich-text.md)) |

## Audience figures

| Not there | Do instead |
|---|---|
| Per-listener or per-episode play counts | Use the statistics your podcast directories give you |
| A download counter on the media field | The **file** field (`ezbinaryfile`) counts downloads; the media field does not ([Downloads and statistics](downloads-and-statistics.md)) |
| Private, per-subscriber feed addresses | Not available; see [Members-only episodes](members-only-episodes.md) |

## Related pages

- [What Exponential does for a show](what-exponential-does-for-a-show.md)
- [Troubleshooting feeds and media](troubleshooting-feeds-and-media.md)
- [Podcaster FAQ](podcaster-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
