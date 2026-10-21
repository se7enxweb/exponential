---
title: "Podcasters, radio and streaming: start here"
audience: podcaster
category: podcasting-streaming
order: 1
summary: Where podcasters, radio producers and streamers start. What an Exponential site does for a show - media hosting, HTML5 players, an Apple Podcasts feed, embedded streams, scheduling, approval, rights and access - what it does not do, and a map of every page in this section.
keywords: podcast, podcaster, radio, streaming, streamer, video podcast, live stream, feed, Apple Podcasts, RSS, episodes, show notes, start here, overview, index
featured: true
type: help
---
# Podcasters, radio and streaming: start here

This section of the help is for people who publish shows with Exponential: podcasters, radio producers, video
creators and streamers, and the editors and administrators who work with them. It explains how a show becomes
content, how episodes get onto the site and into podcast apps, how streams and videos from other services are
embedded, and how release dates, approval, rights and access are handled.

Every statement on these pages was checked in the code, the shipped settings or a running installation, and names
its source. Where Exponential does not do something, the pages say so and name the alternative.

> **Note**
> This section is about publishing your own show with Exponential. If you are a journalist or creator who talks
> **about** Exponential on a podcast, a radio show or a stream, start at
> [Press, media and influencers: start here](../press/press-start-here.md).

## Contents

1. [What Exponential does for a show](#what-exponential-does-for-a-show)
2. [Your first hour](#your-first-hour)
3. [Five rules for every show](#five-rules-for-every-show)
4. [Map of the podcaster pages](#map-of-the-podcaster-pages)
5. [Pages from other audiences you will use](#pages-from-other-audiences-you-will-use)
6. [Finding these pages again](#finding-these-pages-again)

## What Exponential does for a show

- **Stores and plays** audio and video with the media datatype and the browser's own player (Html5 Audio, Html5
  Video).
- **Publishes a podcast feed**: an RSS export in the format *Apple Podcasts (RSS 2.0 + iTunes)*, with enclosures,
  stable `guid` values and the channel fields Apple asks for (`kernel/classes/ezrssexport.php`).
- **Embeds players** from YouTube, Vimeo, Dailymotion and other services with the layouts editor's External video
  block, which loads them only after a click.
- **Schedules and approves** releases with the *Wait until date* and *Approve* workflow events.
- **Protects and organizes** with sections, roles, object states, tags and translations.

It does **not** transcode files, stream live, write season or episode numbers into the feed, or count plays in
podcast apps ([What is not built in](what-is-not-built-in.md)).

## Your first hour

1. **Know the scope** (10 minutes): [What Exponential does for a show](what-exponential-does-for-a-show.md) and
   [What is not built in](what-is-not-built-in.md).
2. **Understand the structure** (10 minutes): [How a show maps to content](how-a-show-maps-to-content.md).
3. **Understand the feed** (15 minutes): [The podcast feed explained](the-podcast-feed-explained.md).
4. **See one episode through** (15 minutes): [Publishing an episode](publishing-an-episode.md).
5. **Know whom to ask for what** (10 minutes): [Working with your administrator](working-with-your-administrator.md).

A longer order for each role is on [Podcasters reading path](podcasters-reading-path.md).

## Five rules for every show

1. **Edit, never recreate.** An episode's `guid` is its object's remote id; a recreated item is a new episode for
   every app ([The podcast feed explained](the-podcast-feed-explained.md)).
2. **Keep the feed address.** Choose the access URL once; to move, use **Moved to**
   ([Moving, pausing or ending a show](moving-pausing-or-ending-a-show.md)).
3. **Publish MP3 at a constant bitrate** unless your administrator has added other types
   ([Uploading audio](uploading-audio.md)).
4. **Check signed out.** Open the episode and the feed as a visitor after every release
   ([Checking your feed](checking-your-feed.md)).
5. **Respect people and rights.** Clear music, agree with guests, keep personal data out of public pages
   ([Rights and licensing of what you publish](rights-and-licensing.md), [Guests and credits](guests-and-credits.md)).

## Map of the podcaster pages

### Orientation

| Page | What it helps you do |
|---|---|
| [What Exponential does for a show](what-exponential-does-for-a-show.md) | See every capability with its source and page |
| [What is not built in](what-is-not-built-in.md) | Know the limits before you promise anything |
| [How a show maps to content](how-a-show-maps-to-content.md) | Structure shows, seasons and episodes so the feed works |
| [The media field and the file field](media-field-and-file-field.md) | Choose how episodes store audio and video |
| [The podcast feed explained](the-podcast-feed-explained.md) | Understand every element of the feed and when it changes |
| [Working with your administrator](working-with-your-administrator.md) | Ask for the right settings, precisely |

### Setting up a show

| Page | What it helps you do |
|---|---|
| [Choosing a class for episodes](choosing-a-class-for-episodes.md) | Pick Video, Audio, NG Video, File or your own class |
| [Building an episode class](building-an-episode-class.md) | Build a class with show notes, guests, release date and transcript |
| [Setting up the show structure](setting-up-the-show-structure.md) | Create the show, season and guest folders |
| [Artwork and episode images](artwork-and-episode-images.md) | Put cover art into the feed and images on the site |

### Episodes

| Page | What it helps you do |
|---|---|
| [Uploading audio](uploading-audio.md), [Uploading video](uploading-video.md) | Prepare and upload episode files |
| [Publishing an episode](publishing-an-episode.md) | Take one episode from draft to podcast apps |
| [Writing show notes](writing-show-notes.md), [Show notes template](show-notes-template.md) | Write notes that work on the site and in apps |
| [Guests and credits](guests-and-credits.md) | Credit people and treat their data with care |
| [Transcripts and captions](transcripts-and-captions.md), [Accessible players and pages](accessible-players-and-pages.md) | Make every episode accessible |
| [Tags and topics](tags-and-topics.md) | Organize the back catalog by topic |
| [Multilingual shows](multilingual-shows.md) | Publish a show in several languages |

### Feeds

| Page | What it helps you do |
|---|---|
| [Building a podcast feed](building-a-podcast-feed.md) | Create the Apple Podcasts export step by step |
| [Channel fields reference](channel-fields-reference.md), [Episode mapping reference](episode-mapping-reference.md) | Fill in and map every field correctly |
| [Checking your feed](checking-your-feed.md) | Test the feed and the files yourself |
| [Submitting to podcast directories](submitting-to-podcast-directories.md) | Submit the feed and keep it healthy afterwards |
| [Moving, pausing or ending a show](moving-pausing-or-ending-a-show.md) | Move, finish or withdraw a show without losing subscribers |
| [Feeds for readers and networks](feeds-for-readers-and-networks.md) | Publish RSS, Atom and OPML for readers and networks |

### Streams and embeds

| Page | What it helps you do |
|---|---|
| [Embedding a video or a stream](embedding-a-video-or-stream.md) | Show players from video and streaming services |
| [Live streams and premieres](live-streams-and-premieres.md) | Run the site around a live show or premiere |
| [Media in rich text](media-in-rich-text.md) | Put an episode or a clip inside an article |
| [Privacy and third-party players](privacy-and-third-party-players.md) | Embed players without exposing listeners needlessly |

### Release, rights and access

| Page | What it helps you do |
|---|---|
| [Scheduling a release](scheduling-a-release.md), [A release calendar](a-release-calendar.md) | Release on time and plan the season |
| [Approvals before release](approvals-before-release.md), [Production stages with object states](production-stages-with-object-states.md) | Review episodes and track production stages |
| [Hiding and retiring episodes](hiding-and-retiring-episodes.md) | Take episodes down, temporarily or for good |
| [Rights and licensing of what you publish](rights-and-licensing.md) | Record licenses and handle rights problems |
| [Members-only episodes](members-only-episodes.md) | Keep bonus episodes for members |

### Hosting

| Page | What it helps you do |
|---|---|
| [Storage and bandwidth](storage-and-bandwidth.md) | Plan disk, traffic and backups |
| [Upload limits and large files](upload-limits-and-large-files.md) | Understand and raise upload limits |
| [CDN and external media hosting](cdn-and-external-media-hosting.md) | Know what a CDN and other hosting can do |
| [Downloads and statistics](downloads-and-statistics.md) | Read audience numbers honestly |

### Reference

| Page | What it helps you do |
|---|---|
| [Troubleshooting feeds and media](troubleshooting-feeds-and-media.md) | Fix the most frequent problems |
| [Podcaster FAQ](podcaster-faq.md) | Get quick answers |
| [Podcaster checklists](podcaster-checklists.md) | Check a launch, an episode, a feed, a live event |
| [Podcaster glossary](podcaster-glossary.md) | Look up podcasting and Exponential terms |
| [Podcasters reading path](podcasters-reading-path.md) | Read the section in order for your role |

## Pages from other audiences you will use

| Page | Why podcasters read it |
|---|---|
| [Images, media and uploads](../media-and-uploads.md) | The media library and the upload page, for editors |
| [RSS feeds](../../guides/rss-feeds.md) | The full guide to the RSS page, exports and imports |
| [RSS: Apple Podcasts feeds, a paged feed list and safer exports](../../features/6.0/rss-podcast-and-feed-list.md) | The feature description of the podcast format |
| [RSS and podcast feeds](../customers/growth/rss-and-podcast-feeds.md) | The site owner's view of feeds and podcasts |
| [Scheduled publishing](../customers/day-to-day/scheduled-publishing.md) | Everything a site can do on a date |
| [The layouts editor](../layouts-editor.md) | The page builder that holds embedded players |
| [Captions and media](../government/accessibility/captions-and-media.md) | Captions and transcripts in depth |
| [Podcast and radio guests](../press/podcast-and-radio-guests.md), [Podcast episode outlines](../press/podcast-episode-outlines.md) | Preparing guests and episodes, from the press section |
| [Live stream guide](../press/live-stream-guide.md), [Captions and accessible video](../press/captions-and-accessible-video.md) | Producing a live stream and accessible video |
| [CDN](../hosting/performance/cdn.md), [Storage layout](../hosting/operations/storage-layout.md) | What your host deals with |
| [Free documentation](../free-documentation.md) | What you may do with these pages |

## Finding these pages again

- In the help, filter by the audience **Podcasters, radio and streaming**, or open **All pages** and the folder
  `podcasters`.
- Each page has **Edit on GitHub**. If something here is wrong or out of date, tell us there or on the
  [community issue tracker](https://github.com/se7enxweb/exponential-community/issues).

## Related pages

- [What Exponential does for a show](what-exponential-does-for-a-show.md)
- [Podcasters reading path](podcasters-reading-path.md)
- [Press, media and influencers: start here](../press/press-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
