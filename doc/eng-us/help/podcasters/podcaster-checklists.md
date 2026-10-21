---
title: Podcaster checklists
audience: podcaster
category: podcasting-streaming
order: 46
summary: Ready-to-use checklists for a show on Exponential - launching a show, publishing each episode, checking the feed, a live event, retiring an episode and a monthly health check - each item linked to the page that explains it.
keywords: checklist, launch checklist, episode checklist, feed checklist, live event checklist, monthly check, podcast launch, quality, release, review
type: help
---
# Podcaster checklists

This page collects the checklists of this section in one place, to print or copy into your team's tools. Each item
links to the page that explains it.

## Contents

1. [Launching a show](#launching-a-show)
2. [Every episode](#every-episode)
3. [The feed after changes](#the-feed-after-changes)
4. [A live event](#a-live-event)
5. [Retiring an episode](#retiring-an-episode)
6. [Once a month](#once-a-month)

## Launching a show

- [ ] The episode class exists and has the fields the show needs ([Building an episode class](building-an-episode-class.md)).
- [ ] The media field uses Html5 Audio or Html5 Video ([The media field and the file field](media-field-and-file-field.md)).
- [ ] The show folder, season folders and guests folder exist ([Setting up the show structure](setting-up-the-show-structure.md)).
- [ ] Upload limits fit the largest episode ([Upload limits and large files](upload-limits-and-large-files.md)).
- [ ] Hosting covers disk, traffic and backups for a year ([Storage and bandwidth](storage-and-bandwidth.md)).
- [ ] The podcast export is built, active, with all channel fields ([Building a podcast feed](building-a-podcast-feed.md)).
- [ ] The artwork address opens the cover image ([Artwork and episode images](artwork-and-episode-images.md)).
- [ ] The owner e-mail is a role address of the show ([Channel fields reference](channel-fields-reference.md)).
- [ ] Scheduling and approval are set up if needed ([Scheduling a release](scheduling-a-release.md), [Approvals before release](approvals-before-release.md)).
- [ ] Test episodes are removed and the feed is checked ([Checking your feed](checking-your-feed.md)).
- [ ] The privacy notice mentions embedded players and logs ([Privacy and third-party players](privacy-and-third-party-players.md)).
- [ ] The feed is submitted to the directories you chose ([Submitting to podcast directories](submitting-to-podcast-directories.md)).

## Every episode

- [ ] Final MP3, constant bitrate, readable file name ([Uploading audio](uploading-audio.md)).
- [ ] Created in the right show or season folder ([Publishing an episode](publishing-an-episode.md)).
- [ ] Title with number, short description, show notes ([Show notes template](show-notes-template.md)).
- [ ] Guests related and credited as agreed ([Guests and credits](guests-and-credits.md)).
- [ ] Sponsors disclosed ([Writing show notes](writing-show-notes.md)).
- [ ] Music and clips cleared and credited ([Rights and licensing of what you publish](rights-and-licensing.md)).
- [ ] Transcript on the page or linked ([Transcripts and captions](transcripts-and-captions.md)).
- [ ] Controller on, Autoplay and Loop off ([Accessible players and pages](accessible-players-and-pages.md)).
- [ ] Release date set, or published now ([Scheduling a release](scheduling-a-release.md)).
- [ ] After release: page plays signed out; feed lists it with the right enclosure.

## The feed after changes

Run after changing the export, the class, the host name, HTTPS or a proxy:

- [ ] The feed answers `200` with `application/rss+xml`.
- [ ] Links and enclosures use the public host and `https://`.
- [ ] Every item has an enclosure with an audio or video type and the real length.
- [ ] `Range` requests to a file answer `206`.
- [ ] Existing episodes kept their `guid`.

Details: [Checking your feed](checking-your-feed.md).

## A live event

- [ ] The stream is set up at the streaming service, with a fixed player address ([Live streams and premieres](live-streams-and-premieres.md)).
- [ ] The live page exists, with time and time zone, and the External video block ([Embedding a video or a stream](embedding-a-video-or-stream.md)).
- [ ] The page is announced or scheduled.
- [ ] A layout rule switches the show page for the event, if wanted.
- [ ] Afterwards: replay uploaded or embedded, notes and transcript added, audio published as an episode.

The production side: [Live stream guide](../press/live-stream-guide.md).

## Retiring an episode

- [ ] Hide (reversible) or remove to the trash, not delete ([Hiding and retiring episodes](hiding-and-retiring-episodes.md)).
- [ ] No other page, embed or newsletter links to it.
- [ ] The feed no longer lists it after the cache time.
- [ ] The reason and date are recorded.

## Once a month

- [ ] The RSS list shows no **Needs attention** marker ([RSS feeds](../../guides/rss-feeds.md)).
- [ ] **Number of objects** still covers the back catalog.
- [ ] Disk usage is within the plan ([Storage and bandwidth](storage-and-bandwidth.md)).
- [ ] Download trends noted, with their definition ([Downloads and statistics](downloads-and-statistics.md)).
- [ ] Broken links in show notes fixed ([Link and content checks](../creators/website-creators/link-and-content-checks.md)).
- [ ] Tags tidied ([Tags and topics](tags-and-topics.md)).
- [ ] Stale drafts removed ([A release calendar](a-release-calendar.md)).

## Related pages

- [Press checklists](../press/press-checklists.md)
- [Podcaster FAQ](podcaster-faq.md)
- [Podcasters reading path](podcasters-reading-path.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
