---
title: Transcripts and captions
audience: podcaster
category: podcasting-streaming
order: 33
summary: How to publish transcripts of audio episodes and captions for video with Exponential - the transcript as text on the episode page or as a file, the missing caption track in the media template and three ways around it, WebVTT files, and why the feed carries no transcript.
keywords: transcript, captions, subtitles, WebVTT, vtt, srt, track, accessibility, deaf, hard of hearing, WCAG, audio description, searchable, ezmedia, video
type: help
---
# Transcripts and captions

This page is for producers who make their episodes accessible to people who cannot hear them, and findable by
search. For audio, the accessible alternative is a **transcript**; for video with speech, **captions**. The public
sector section has the background on the accessibility rules ([Captions and media](../government/accessibility/captions-and-media.md)).

## Contents

1. [Transcripts for audio](#transcripts-for-audio)
2. [Where the transcript goes](#where-the-transcript-goes)
3. [Captions for video](#captions-for-video)
4. [Caption files](#caption-files)
5. [The feed and transcripts](#the-feed-and-transcripts)
6. [Checklist](#checklist)

## Transcripts for audio

A transcript is the full text of what is said, with speaker names, and a description of important sounds. Make it
from your edited audio, not the raw recording, and correct it by listening; automatic transcripts get names and
technical terms wrong.

Format it for reading:

```text
Host: Welcome to episode 42. Today with us is <guest>.
Guest: Thank you for having me.
[Music fades out]
Host: Let's start with ...
```

## Where the transcript goes

| Place | How | Good for |
|---|---|---|
| On the episode page, below the player | a rich text field **Transcript** in the episode class | most shows: one page, searchable by the site search |
| A page of its own, linked from the episode | an article related to the episode | very long transcripts |
| A downloadable file | a file field (`ezbinaryfile`), for example a PDF or a text file | an extra, not instead of the page |

A rich text field marked searchable makes the episode findable by what was said in it
([Searching](../searching.md)). Add the field as described on [Building an episode class](building-an-episode-class.md).

## Captions for video

The media field's view template writes a `<video>` element with no `<track>` element
(`design/standard/templates/content/datatype/view/ezmedia.tpl`), so a caption file cannot be attached through the
media field as shipped. Three ways around it:

1. **Open captions**: burn the captions into the video before upload. They work everywhere and cannot be switched
   off.
2. **A caption track in your design**: a developer overrides the media template for your design and adds
   `<track kind="captions" srclang="en" src="...">`, pointing to a WebVTT file in a second file field of your video
   class ([Template overrides](../creators/website-creators/template-overrides.md)). This is design work, not a
   built-in feature.
3. **A video service with captions**, embedded on the page ([Embedding a video or a stream](embedding-a-video-or-stream.md)).

For a live stream, captions come from the streaming service, if it offers them.

## Caption files

WebVTT (`.vtt`) and SubRip (`.srt`) are not listed in the shipped `settings/mime.ini`. Uploaded as they are, they get
the type `application/octet-stream`, and browsers do not accept such a file as a `<track>`. If your design uses a
caption track, the administrator adds the type first:

```ini
# settings/override/mime.ini.append.php
[vtt]
Types[]=text/vtt
```

See [Working with your administrator](working-with-your-administrator.md).

## The feed and transcripts

The podcast feed writes no transcript element ([What is not built in](what-is-not-built-in.md)). Podcast apps
therefore cannot fetch your transcript from the feed. Link the transcript in the show notes ("Transcript: <address>"),
so it is one tap away in apps that show the description.

## Checklist

- [ ] Every audio episode has a transcript on the page or one click away.
- [ ] Every video with speech has captions.
- [ ] Speakers are named in the transcript.
- [ ] Important sounds and visual information are described.
- [ ] The transcript is linked from the show notes.
- [ ] The player has controls and does not start by itself ([Accessible players and pages](accessible-players-and-pages.md)).

## Related pages

- [Captions and accessible video](../press/captions-and-accessible-video.md)
- [Captions and media](../government/accessibility/captions-and-media.md)
- [Accessible players and pages](accessible-players-and-pages.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
