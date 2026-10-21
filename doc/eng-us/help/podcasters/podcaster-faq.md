---
title: Podcaster FAQ
audience: podcaster
category: podcasting-streaming
order: 45
summary: Short answers to the questions podcasters, radio producers and streamers ask most about Exponential - feeds, formats, players, live streams, scheduling, statistics, members, costs and limits - each with the page that explains it.
keywords: FAQ, questions, answers, podcast, radio, streaming, feed, Apple Podcasts, Spotify, MP3, live, schedule, statistics, members, video podcast
type: help
---
# Podcaster FAQ

Short answers, each with the page that has the details. If your question is not here, start at
[Podcasters, radio and streaming: start here](podcasters-start-here.md).

## Contents

1. [Feeds](#feeds)
2. [Files and players](#files-and-players)
3. [Streams and video](#streams-and-video)
4. [Publishing](#publishing)
5. [Audience and money](#audience-and-money)

## Feeds

**Can Exponential publish a podcast feed?**
Yes. An RSS export in the format **Apple Podcasts (RSS 2.0 + iTunes)** writes RSS 2.0 with the iTunes elements and an
enclosure per episode ([Building a podcast feed](building-a-podcast-feed.md)).

**Does it work with apps other than Apple Podcasts?**
The feed is standard RSS 2.0 with enclosures, which podcast apps in general read. Each directory has its own
submission process outside Exponential ([Submitting to podcast directories](submitting-to-podcast-directories.md)).

**Can my normal RSS 2.0 feed carry the audio?**
No. The RSS 1.0, RSS 2.0 and Atom exports write no enclosures; use the Apple Podcasts format
([Feeds for readers and networks](feeds-for-readers-and-networks.md)).

**Are season and episode numbers in the feed?**
No; `itunes:season`, `itunes:episode` and `itunes:episodeType` are not written. Put the number in the title
([What is not built in](what-is-not-built-in.md)).

**Why is my new episode not in the feed yet?**
The feed is cached for 1200 seconds as shipped, and apps fetch on their own schedule
([Checking your feed](checking-your-feed.md#when-a-change-does-not-show)).

**How many episodes does the feed hold?**
As many as the export's **Number of objects**, up to 1000 with the shipped choices, newest first
([How a show maps to content](how-a-show-maps-to-content.md)).

## Files and players

**Which audio format should I use?**
MP3, constant bitrate. It is mapped to `audio/mpeg`, and the feed can work out its duration
([Uploading audio](uploading-audio.md)).

**Can I upload M4A or Opus?**
Only after the administrator adds the suffix to `mime.ini`; otherwise the file is typed `application/octet-stream`
([Working with your administrator](working-with-your-administrator.md)).

**Does Exponential convert or compress my files?**
No. The file you upload is the file listeners get.

**Which player does the site use?**
The browser's own `<audio>` or `<video>` element, from the media field's player types Html5 Audio and Html5 Video
([The media field and the file field](media-field-and-file-field.md)).

**How large can an episode be?**
As large as the smallest of PHP's limits, the field's maximum and the upload page's limit allow
([Upload limits and large files](upload-limits-and-large-files.md)).

## Streams and video

**Can I stream live from Exponential?**
No. Stream through a streaming service and embed its player; the site carries the page, the announcement and the replay
([Live streams and premieres](live-streams-and-premieres.md)).

**Can I embed YouTube or Vimeo?**
Yes, with the External video block of the layouts editor, which loads the player only after a click
([Embedding a video or a stream](embedding-a-video-or-stream.md)).

**Can I publish a video podcast?**
Yes, if the video file is uploaded to the site and mapped as the enclosure. Embedded videos have no file to enclose.

**Can I add captions to an uploaded video?**
Not through the media field as shipped; burn them in, add a caption track in your design, or use a video service
([Transcripts and captions](transcripts-and-captions.md)).

## Publishing

**Can an episode go out at a set time?**
Yes, with the *Wait until date* workflow event, once an administrator has set it up; it appears at the first cron run
after the date ([Scheduling a release](scheduling-a-release.md)).

**Can someone approve episodes first?**
Yes, with the *Approve* workflow event ([Approvals before release](approvals-before-release.md)).

**I made a mistake in a published episode. Delete and re-create it?**
No: edit it. A re-created item gets a new `guid`, and apps list it as a new episode
([The podcast feed explained](the-podcast-feed-explained.md)).

**Can I publish in several languages?**
Yes, as translations of each episode or as separate shows, with one feed per language
([Multilingual shows](multilingual-shows.md)).

## Audience and money

**How many people listen?**
The site cannot count plays in apps. It counts downloads of file fields, page views and server log requests; the
directories have their own figures ([Downloads and statistics](downloads-and-statistics.md)).

**Can I sell premium episodes?**
Members-only episodes work on the site through sections and roles. A private feed per subscriber does not exist
([Members-only episodes](members-only-episodes.md)).

**What does hosting a show cost?**
Mostly disk space and data transfer, both of which grow with each episode and each subscriber
([Storage and bandwidth](storage-and-bandwidth.md)).

**Is Exponential free to use for a commercial show?**
The software is free and open source under the GNU GPL, version 2 or later; your episodes remain yours
([Rights and licensing of what you publish](rights-and-licensing.md)).

## Related pages

- [Podcaster glossary](podcaster-glossary.md)
- [Troubleshooting feeds and media](troubleshooting-feeds-and-media.md)
- [Creator FAQ](../press/creator-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
