---
title: Embedding a video or a stream
audience: podcaster
category: podcasting-streaming
order: 24
summary: How to show a player from YouTube, Vimeo, Dailymotion or another video or streaming service on an Exponential page - the External video block of the layouts editor with click-to-load, the HTML snippet block, the NG Video class of media site installations - and what the site's Content Security Policy needs.
keywords: embed video, embed stream, External video block, YouTube, Vimeo, Dailymotion, iframe, player, click to load, youtube-nocookie, layouts editor, HTML snippet, NG Video, Content Security Policy, frame-src
type: help
---
# Embedding a video or a stream

This page is for producers and editors who show a video or a live player hosted elsewhere on a page of the site. The
main tool is the **External video** block of the layouts editor, on installations with Exponential Layouts
(`extension/explayouts`). Pages without layouts have other options, listed at the end.

## Contents

1. [The External video block](#the-external-video-block)
2. [Add the block](#add-the-block)
3. [What visitors see](#what-visitors-see)
4. [Other ways](#other-ways)
5. [When the player does not appear](#when-the-player-does-not-appear)

## The External video block

The block is defined in `extension/explayouts/settings/explayouts.ini.append.php` (`[BlockDefinition_video]`,
*External video*) and handled by `expLayoutsVideoBlockHandler`. Its settings:

| Setting | What to enter |
|---|---|
| **Service** | YouTube, Vimeo, Dailymotion, or *Video address below* |
| **Video ID** | the id of the video at that service |
| **Caption** | a caption shown under the player and used as its title |
| **Video URL or embed src** | for other services: the address of the player (the `src` of its embed code) |
| **Width (px or %)** | default `100%` |
| **Height (px)** | default 360 |
| **Autoplay** | off by default; leave it off |

For the three services, the block builds the player address itself, and it uses their privacy-friendlier variants:
YouTube from `www.youtube-nocookie.com/embed/`, Vimeo with `dnt=1`, Dailymotion from `www.dailymotion.com/embed/video/`.
The id is checked: digits only for Vimeo, letters, digits, `_` and `-` for the others.

For any other service, the address must be `http` or `https` and point to another host than your own site
(`expLayoutsSafeValue::externalEmbedUrl()`); anything else is dropped and the block shows "No video configured."

## Add the block

1. Open the page's layout in the layouts editor ([The layouts editor](../layouts-editor.md)).
2. Add a block of type **External video** to a zone ([Add a block](../layouts-editor.md#add-a-block)).
3. Choose the service and enter the id, or paste the player address.
4. Enter a caption that says what the video is.
5. Publish the layout ([Publish, keep or throw away](../layouts-editor.md#publish-keep-or-throw-away)).

## What visitors see

The player loads **only after a click**. Until then the page shows a **Load video** button and a note that loading
it sends data to the other host, and nothing is fetched from that host
(`extension/explayouts/design/standard/templates/explayouts/block/parts/embed.tpl`). On the click, a script puts the
`<iframe>` in place, sandboxed, with only your site's origin as referrer. This keeps third parties off the page until
the visitor chooses ([Privacy and third-party players](privacy-and-third-party-players.md)).

## Other ways

| Way | When | Notes |
|---|---|---|
| **HTML snippet** block | a service's embed code that the video block cannot take | raw HTML, inserted as written: only for trusted editors, and it loads at once, not after a click |
| *NG Video* item (media site installations) | a video page of its own | **Video type** YouTube, Vimeo, Dailymotion or uploaded file; YouTube loads after a click (`full_ng_video.tpl`) |
| An uploaded file | you own the file and want no third party | [Uploading video](uploading-video.md) |
| Rich text | not for players | rich text cannot hold an `<iframe>`; see [Media in rich text](media-in-rich-text.md) |

## When the player does not appear

| Sign | Likely cause | Fix |
|---|---|---|
| "No video configured." | empty or refused address, wrong id | check the id; use the player address, not the page address |
| The button loads, the frame stays empty or shows a refusal | the site's Content Security Policy is enforced and does not allow the host | add the host with `AdditionalSources[]=frame-src <host>` ([Working with your administrator](working-with-your-administrator.md)) |
| The service says embedding is not allowed | the video's owner disabled embedding at the service | change it at the service |
| The frame works but is tiny | width or height | set **Width** `100%` and a fitting **Height** |

As shipped, `site.ini [ContentSecurityPolicy] Mode=auto` sends no policy on public siteaccesses, and
`Directives[frame-src]='self'` applies only where the policy is on.

## Related pages

- [Live streams and premieres](live-streams-and-premieres.md)
- [Layout types, zones and blocks](../creators/website-creators/layout-types-zones-and-blocks.md)
- [Live stream guide](../press/live-stream-guide.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
