---
title: Embed a video with captions and transcript
audience: government, editor
category: public-sector
order: 320
summary: For editors - publish a video so everyone can use it - captions, a transcript on the page, a privacy-aware choice between hosting the file and embedding a platform player - with the shipped Video class or a link.
keywords: video, captions, subtitles, transcript, audio description, Video class, ezmedia, embed, YouTube, privacy, third party, accessibility, WCAG 1.2
type: help
---
# Embed a video with captions and transcript

This page is for editors who publish videos: a recorded meeting, an explainer, a message from leadership. Videos need
captions and a transcript to be accessible ([WCAG 2.2](https://www.w3.org/TR/WCAG22/), guideline 1.2). The default
package ships a **Video** class (`video`, with a media file field `ezmedia`) in the Media group
([sevenx_classes](../../../site-packages/sevenx-multisite/sevenx-classes.md)); many sites embed a video platform
instead.

## Contents

1. [Before you start](#before-you-start)
2. [Choosing how to publish](#choosing-how-to-publish)
3. [Steps](#steps)
4. [Check the result](#check-the-result)
5. [Pitfalls](#pitfalls)

## Before you start

- A caption file (for example WebVTT or SRT) for the spoken words, checked by a person.
- A transcript as text: speech, plus important sounds and what is shown when it matters.
- If information is only visual, an audio description or a description in the text.

## Choosing how to publish

| Way | For | Care |
|---|---|---|
| Upload the file into a Video object | short videos, no third party | large files and bandwidth; your template's player must support captions |
| Embed a platform player | long videos, streaming | the platform receives visitor data when the player loads; consent may be needed ([Privacy standards](../../../../standards/privacy.md)); a Content Security Policy must allow it |
| A link to the platform | the simplest, no tracking until clicked | visitors leave the page |

## Steps

1. Create the page that holds the video (an Article, or the event or meeting page).
2. Write an introduction: what the video is, how long it is, that captions and a transcript are available.
3. Add the video:
   - **upload**: create a **Video** object (media library or next to the page), upload the file, and relate or embed
     it in the page;
   - **platform**: insert the platform's embed through the method your site allows (a custom tag or a block), or a link.
4. Add the transcript below the video, under a heading "Transcript", or as a related page if it is long.
5. **Preview**, then **Send for publishing**.

## Check the result

- [ ] Captions can be turned on in the player.
- [ ] The transcript is on the page or one click away, as text.
- [ ] The player works with a keyboard.
- [ ] Signed out, the page loads without errors; with a platform embed, check what loads before the visitor presses
  play.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Automatic captions published unchecked | correct names and terms first |
| A video as the only source of important information | the text says it too |
| Autoplay with sound | do not; it disturbs screen reader users |
| Pasting raw embed code into rich text | the editor may refuse it; use the method your site provides |

## Related pages

- [Publishing tasks](README.md)
- [Accessible event information](../../building/events/accessible-event-information.md)
- [SVG and image formats](../../../creators/website-creators/svg-and-image-formats.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
