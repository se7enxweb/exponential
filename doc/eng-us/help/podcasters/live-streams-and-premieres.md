---
title: Live streams and premieres
audience: podcaster
category: podcasting-streaming
order: 25
summary: What an Exponential site can and cannot do for a live show, a radio broadcast or a video premiere - it embeds the player of a streaming service, announces the stream, switches a page's layout for the time of the event and keeps the replay - while the stream itself runs through the streaming service.
keywords: live stream, live show, radio, simulcast, premiere, streaming service, embed, layout rule, time condition, announcement, replay, schedule, event page
type: help
---
# Live streams and premieres

This page is for radio producers, streamers and podcasters who go live or premiere a video. Exponential does not
receive, encode or deliver a live stream: there is no ingest, no HLS or DASH and no live player of its own in the
kernel or the shipped extensions ([What is not built in](what-is-not-built-in.md)). What the site does is everything
around the stream: the page people come to, the announcement, the player of your streaming service, and the replay
afterwards.

## Contents

1. [The parts of a live event](#the-parts-of-a-live-event)
2. [Before: announce](#before-announce)
3. [During: the live page](#during-the-live-page)
4. [A page that switches by itself](#a-page-that-switches-by-itself)
5. [After: the replay](#after-the-replay)
6. [A radio station's live stream](#a-radio-stations-live-stream)

## The parts of a live event

| Part | Where it lives | Page |
|---|---|---|
| The stream | your streaming service (or your radio playout system) | outside Exponential |
| The live page | an item on your site, with the service's player embedded | [Embedding a video or a stream](embedding-a-video-or-stream.md) |
| The announcement | an article or a news item, published or scheduled | [Scheduling a release](scheduling-a-release.md) |
| The switch at the start | a layout rule with a time condition | [Layout rules](../creators/website-creators/layout-rules.md) |
| The replay | the recording, uploaded or embedded, plus show notes | [Uploading video](uploading-video.md) |

For the production side (scenes, run sheet, chat, privacy on camera), read the press section's
[Live stream guide](../press/live-stream-guide.md).

## Before: announce

1. Create the live page in advance, for example an article "Live: <title>, <date and time>" with what the show is
   about, when it starts in which time zone, and the guests.
2. Embed the streaming service's player with the **External video** block (YouTube, Vimeo, Dailymotion, or the
   player address of another service). Many services give a fixed player address for a scheduled stream; use it.
3. Publish the page now, or schedule it with the *Wait until date* event if your site has it.
4. Link the page from the show page, the newsletter and your channels.

## During: the live page

- The player loads after the visitor clicks **Load video**, so the page itself sends nothing to the service until
  then ([Privacy and third-party players](privacy-and-third-party-players.md)).
- Keep the page's text short and stable. Editing the page during the stream is possible, but every published change
  is a new version; prepare corrections as a draft.
- If the stream fails, publish a short notice on the page; the edit is live at once on most sites.

## A page that switches by itself

With Exponential Layouts, a layout rule can carry a `time` condition with `from` and `to`. Map a "live" layout to the
show page for the time of the event, at a higher priority than the page's normal rule; before and after, the normal
layout shows. The format is JSON, for example `{"from":"2026-10-01","to":"2026-10-31"}`, and an empty range always
matches ([Layout rules](../creators/website-creators/layout-rules.md#conditions)).

Check the page signed out shortly after the start and after the end. Caches can keep the previous layout for a
while; if it does not switch, ask your administrator which caches apply to that page.

## After: the replay

1. Download the recording from the streaming service.
2. Upload it as a video episode, or embed the service's replay with the External video block.
3. Edit the live page into the replay page: change the title, add show notes, chapters and the transcript
   ([Show notes template](show-notes-template.md), [Transcripts and captions](transcripts-and-captions.md)).
4. If the show also runs as a podcast, publish the audio as an episode so it reaches the feed
   ([Publishing an episode](publishing-an-episode.md)).

Keep the same page and address: links shared before the event keep working.

## A radio station's live stream

A station's continuous stream is the same case: the stream comes from the station's streaming server, and the site
embeds or links its player. The site can carry the program schedule as content (an event calendar class exists on
installations with the shipped classes package, `event_calendar` and `event`), the show pages, the presenters and the
on-demand episodes with their podcast feeds.

## Related pages

- [Embedding a video or a stream](embedding-a-video-or-stream.md)
- [Live stream guide](../press/live-stream-guide.md)
- [Landing pages with layouts](../creators/website-creators/landing-pages-with-layouts.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
