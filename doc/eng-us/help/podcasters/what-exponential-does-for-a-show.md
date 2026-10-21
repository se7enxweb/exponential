---
title: What Exponential does for a show
audience: podcaster
category: podcasting-streaming
order: 2
summary: A map of what an Exponential site can do for a podcast, a radio program or a video channel - media hosting, players, an Apple Podcasts feed, embeds of streams, scheduling, rights and access - with where each part lives and which page explains it.
keywords: podcast, radio, streaming, capabilities, overview, media hosting, audio, video, feed, Apple Podcasts, embed, scheduling, what it can do
type: help
---
# What Exponential does for a show

This page is for podcasters, radio producers and streamers who want to know, before they start, what their
Exponential site can do for a show and what it cannot. Each row names the feature, where it lives in the code or the
settings, and the page of this section that explains it. Where something needs an extension, a design change or
another service, the row says so.

## Contents

1. [The short answer](#the-short-answer)
2. [Media and players](#media-and-players)
3. [Feeds](#feeds)
4. [Streams and embeds](#streams-and-embeds)
5. [Release, people and rights](#release-people-and-rights)
6. [Hosting and reach](#hosting-and-reach)

## The short answer

Exponential is a content management system, not a podcast host or a streaming server. It stores your audio and video
files as content, plays them with the browser's own player, writes an **Apple Podcasts feed** from your episodes,
embeds players from video and streaming services, and gives you the tools of a CMS around all of it: drafts,
versions, scheduled publishing, approval, sections, roles, translations and tags. It does not encode, transcode or
stream live, and it does not count individual listeners. The list of gaps is on [What is not built in](what-is-not-built-in.md).

## Media and players

| You want to | What exists | Where | Page |
|---|---|---|---|
| Store an audio or video file | The **media** datatype (`ezmedia`) and the **file** datatype (`ezbinaryfile`) | `kernel/classes/datatypes/ezmedia/`, `kernel/classes/datatypes/ezbinaryfile/` | [The media field and the file field](media-field-and-file-field.md) |
| Play audio on the page | Player type **Html5 Audio**: an `<audio>` element with controls, autoplay and loop options | `design/standard/templates/content/datatype/view/ezmedia.tpl` | [Uploading audio](uploading-audio.md) |
| Play video on the page | Player type **Html5 Video**: a `<video>` element | the same template | [Uploading video](uploading-video.md) |
| Upload many files at once | The upload page of the media library | `extension/ezmultiupload` on installations that have it | [Images, media and uploads](../media-and-uploads.md) |
| Let listeners seek in a long file | Downloads answer HTTP `Range` requests | `kernel/classes/binaryhandlers/ezfilepassthrough/ezfilepassthroughhandler.php` | [Storage and bandwidth](storage-and-bandwidth.md) |

## Feeds

| You want to | What exists | Where | Page |
|---|---|---|---|
| A podcast feed for Apple Podcasts and other apps | The RSS export format **Apple Podcasts (RSS 2.0 + iTunes)** | `kernel/classes/ezrssexport.php` (`generateITunes()`) | [Building a podcast feed](building-a-podcast-feed.md) |
| A feed of show notes for news readers | RSS 1.0, RSS 2.0 and Atom exports (without enclosures) | `settings/site.ini [RSSSettings] AvailableVersionList[]` | [Feeds for readers and networks](feeds-for-readers-and-networks.md) |
| A list of all shows of a network | The OPML 2.0 export | `kernel/classes/ezrssexportopmlitem.php` | [Feeds for readers and networks](feeds-for-readers-and-networks.md) |
| Bring a partner's feed into the site | RSS imports, run by the `rssimport.php` cronjob | `kernel/private/classes/cronjobs/rssimport.php` | [RSS feeds](../../guides/rss-feeds.md) |

## Streams and embeds

| You want to | What exists | Where | Page |
|---|---|---|---|
| Show a YouTube, Vimeo or Dailymotion video | The **External video** block of the layouts editor; the *NG Video* class on media site installations | `extension/explayouts` (block `video`) | [Embedding a video or a stream](embedding-a-video-or-stream.md) |
| Show a live stream | Embed the streaming service's player; Exponential does not receive or send the stream | the same block, or an *HTML snippet* block | [Live streams and premieres](live-streams-and-premieres.md) |
| Put an episode inside an article | **Embed object** in the rich text editor | `extension/ezoe` | [Media in rich text](media-in-rich-text.md) |

## Release, people and rights

| You want to | What exists | Where | Page |
|---|---|---|---|
| Publish at a set time | The *Wait until date* workflow event and the workflow cronjob | `kernel/classes/workflowtypes/event/ezwaituntildate/` | [Scheduling a release](scheduling-a-release.md) |
| Have an editor approve an episode | The *Approve* workflow event | `kernel/classes/workflowtypes/event/ezapprove/` | [Approvals before release](approvals-before-release.md) |
| Track recorded, edited, approved | Object states | [Object states](../../guides/object-states.md) | [Production stages with object states](production-stages-with-object-states.md) |
| Credit guests | Relation fields to person items, the *Authors* datatype | `kernel/classes/datatypes/ezauthor/` | [Guests and credits](guests-and-credits.md) |
| Keep some episodes for members | Sections and roles | [Sections](../../guides/sections.md), [Roles and policies](../../guides/roles-and-policies.md) | [Members-only episodes](members-only-episodes.md) |
| Record the license of a recording | No dedicated field; add one to your class | your class definition | [Rights and licensing](rights-and-licensing.md) |

## Hosting and reach

| You want to | What exists | Page |
|---|---|---|
| Know how much disk and traffic you need | Files are stored below the site's storage directory; every download passes through PHP | [Storage and bandwidth](storage-and-bandwidth.md) |
| Upload large files | PHP limits and a per-field maximum size | [Upload limits and large files](upload-limits-and-large-files.md) |
| Put a CDN in front | Static files and images cache well; downloads are permission-checked | [CDN and external media hosting](cdn-and-external-media-hosting.md) |
| Count downloads | A counter on file fields only | [Downloads and statistics](downloads-and-statistics.md) |
| Publish in several languages | Translations of every item, a language per feed | [Multilingual shows](multilingual-shows.md) |

## Related pages

- [Podcasters, radio and streaming: start here](podcasters-start-here.md)
- [What is not built in](what-is-not-built-in.md)
- [RSS: Apple Podcasts feeds, a paged feed list and safer exports](../../features/6.0/rss-podcast-and-feed-list.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
