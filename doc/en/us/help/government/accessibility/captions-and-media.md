---
title: Captions and media
audience: government, editor, designer, developer
category: public-sector
order: 240
summary: For public bodies that publish video and audio - what Exponential's media datatype renders (HTML5 video and audio among older plugin types, with autoplay, loop and controls options, but no caption track), and how to provide captions, transcripts and audio description anyway.
keywords: video, audio, media, captions, subtitles, transcript, audio description, ezmedia, HTML5 video, track, autoplay, controls, WCAG 1.2.1, 1.2.2, 1.2.5, 1.4.2, embedded players, public meetings
type: help
---
# Captions and media

This page is for editors who publish recordings (council meetings, explainer videos, podcasts), and the designers and
developers who render them. WCAG 2.2 level AA asks for captions on prerecorded and live video with sound, a transcript
or audio description, and a way to stop audio that plays by itself (1.2.1 to 1.2.5, 1.4.2;
<https://www.w3.org/TR/WCAG22/>).

## Contents

1. [What the media field renders](#what-the-media-field-renders)
2. [Providing captions](#providing-captions)
3. [Transcripts and audio description](#transcripts-and-audio-description)
4. [Embedded players from other services](#embedded-players-from-other-services)
5. [A checklist](#a-checklist)
6. [Related pages](#related-pages)

## What the media field renders

The media datatype (`ezmedia`) is rendered by `design/standard/templates/content/datatype/view/ezmedia.tpl`. It knows
several player types; the current ones are **HTML5 video** and **HTML5 audio**, which write `<video>` and `<audio>`
elements with the file from `content/download`. The field's options become attributes:

| Option of the media field | HTML attribute |
|---|---|
| Autoplay | `autoplay` |
| Loop | `loop` |
| Controller | `controls` |

The other types (Flash, QuickTime, Windows Media Player, Real Player, Silverlight) write plugin markup that current
browsers no longer support; do not use them for new content.

**The standard template writes no `<track>` element**, so a caption file cannot be attached through the media field
as shipped.

## Providing captions

Choose one of these, and write it into your editor guidelines:

1. **Open captions**: burn the captions into the video before upload. Works everywhere, cannot be switched off.
2. **A caption track in your design**: a developer overrides the media template for your design and adds a `<track
   kind="captions">` pointing to a WebVTT file, for example from a second file field in a video class of your own.
   This is template work in your design, not a built-in feature.
3. **An external video platform** whose player supports captions, embedded in the page (see below).

Always: turn **Controller** on, and **Autoplay** off (WCAG 1.4.2 and plain courtesy).

## Transcripts and audio description

- Publish a **transcript** as text on the same page, under the player, or as a linked page. For audio-only content
  (podcasts), the transcript is the accessible alternative.
- For videos whose picture carries information not spoken aloud (a slide with figures), provide **audio description**
  or a text description of the visual content.
- For recordings of meetings, the minutes are not a transcript; publish both if both exist.

## Embedded players from other services

Embedding a video platform's player brings its accessibility (and its privacy and Content Security Policy
implications) onto your page. Check that the player can be used with the keyboard, that captions can be switched on,
and that the focus can leave it. Mention the third party in your privacy notice and allow its host in your Content
Security Policy if you enforce one ([Content Security Policy](../../../features/6.0/content-security-policy.md)).

## A checklist

- [ ] Every video with speech has captions.
- [ ] Every audio and video has a transcript on the page or one click away.
- [ ] No media starts playing by itself; controls are shown.
- [ ] Visual-only information is described.
- [ ] The player works with the keyboard and the focus can leave it.

## Related pages

- [Accessible content for editors](accessible-content-for-editors.md)
- [Images, media and uploads](../../media-and-uploads.md)
- [Template overrides](../../creators/website-creators/template-overrides.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
