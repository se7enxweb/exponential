---
title: Privacy and third-party players
audience: podcaster
category: podcasting-streaming
order: 43
summary: How embedding players from video and streaming services affects listeners' privacy on an Exponential site - click-to-load in the External video block and the NG Video view, the privacy-friendlier YouTube and Vimeo addresses, the Content Security Policy, the HTML snippet block, your privacy notice, and what your own player and feed send.
keywords: privacy, third-party player, embed, YouTube, youtube-nocookie, Vimeo, dnt, click to load, consent, cookies, Content Security Policy, frame-src, media-src, privacy notice, GDPR
type: help
---
# Privacy and third-party players

This page is for producers and site owners who embed players from other services, and who must tell listeners what
happens with their data. A player from another host is a window into that host: once it loads, the service sees the
visitor. Exponential's layouts blocks are built to delay that until the visitor chooses. This is not legal advice;
ask whoever is responsible for data protection.

## Contents

1. [Your own player](#your-own-player)
2. [Embedded players: click to load](#embedded-players-click-to-load)
3. [The HTML snippet block](#the-html-snippet-block)
4. [Content Security Policy](#content-security-policy)
5. [Your privacy notice](#your-privacy-notice)

## Your own player

An episode played with the media field's HTML5 player loads the file from your own site (`/content/download/...`);
no third party is involved. Your web server logs the request like any other
([Downloads and statistics](downloads-and-statistics.md)). The same goes for podcast apps fetching your feed and
files: they talk to your site only.

## Embedded players: click to load

On installations with Exponential Layouts, the **External video** block does not load the player with the page.
It shows a **Load video** button and the note "Loading it sends data to <host>." Only after the click is the
`<iframe>` inserted, sandboxed and with your site's origin as the only referrer
(`extension/explayouts/design/standard/templates/explayouts/block/parts/embed.tpl`).

When the player is loaded, it uses the privacy-friendlier variants where the service offers them
(`extension/explayouts/classes/explayoutsvideoblockhandler.php`):

| Service | Address used | Note in the code |
|---|---|---|
| YouTube | `https://www.youtube-nocookie.com/embed/<id>` | "without tracking cookies before the video is played" |
| Vimeo | `https://player.vimeo.com/video/<id>?dnt=1` | the same |
| Dailymotion | `https://www.dailymotion.com/embed/video/<id>` | standard address |

After the visitor plays the video, the service's own terms apply. On media site installations, the *NG Video* full
view also loads YouTube only after a click (`full_ng_video.tpl`); its Vimeo and Dailymotion frames load with the page.

## The HTML snippet block

The **HTML snippet** block inserts its HTML as written. An embed code pasted there loads at once, with whatever the
service's code does: no click-to-load, no sandbox. Prefer the External video block; keep the HTML snippet for trusted
editors and code you have read.

## Content Security Policy

The site's policy is set in `settings/site.ini [ContentSecurityPolicy]`. As shipped, `Mode=auto`: report-only in the
administration and **no policy** on public siteaccesses. Where a site enforces one, the defaults allow frames and media
only from the site itself (`frame-src 'self'`, `media-src 'self' blob:`). An embedded player then needs its host
added:

```ini
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
AdditionalSources[]=frame-src https://www.youtube-nocookie.com https://player.vimeo.com
```

If audio or video files are served from another host (a CDN under its own name), `media-src` needs that host too.
See [Content Security Policy](../hosting/security/content-security-policy.md) and the feature description
[Content Security Policy](../../features/6.0/content-security-policy.md).

## Your privacy notice

Say in plain words:

- which services' players the site embeds, and that they load only after a click (where that is true);
- what the site's own server logs record about downloads and feed requests, and how long logs are kept
  ([Log and data retention](../hosting/security/log-and-data-retention.md));
- which analytics service runs on the pages, if any;
- for members: what is stored about their account.

The press section has the matching advice for people on camera: [Privacy on camera](../press/privacy-on-camera.md).

## Related pages

- [Embedding a video or a stream](embedding-a-video-or-stream.md)
- [Compliance and GDPR](../hosting/security/compliance-and-gdpr.md)
- [Visitor statistics and analytics](../creators/website-creators/statistics-and-analytics.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
