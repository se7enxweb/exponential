---
title: Accessible players and pages
audience: podcaster
category: podcasting-streaming
order: 34
summary: How to make episode pages and players usable for everyone - controls on and autoplay off in the media field, what the HTML5 player template gives you, embedded players, headings, link texts with format and size, images and transcripts - with a checklist to run per episode.
keywords: accessibility, accessible player, controls, autoplay, keyboard, screen reader, WCAG, 1.4.2, audio control, link text, file size, headings, alternative text, embedded player
type: help
---
# Accessible players and pages

This page is for producers and editors who want every listener and viewer to be able to use an episode page:
people using a keyboard or a screen reader, people who cannot hear or see, people on slow connections. Much of it is
in your hands as an editor; some of it depends on the design.

## Contents

1. [The player](#the-player)
2. [Embedded players](#embedded-players)
3. [The page around the player](#the-page-around-the-player)
4. [Downloads](#downloads)
5. [Checklist per episode](#checklist-per-episode)

## The player

With player type **Html5 Audio** or **Html5 Video**, the media field becomes the browser's own `<audio>` or
`<video>` element (`design/standard/templates/content/datatype/view/ezmedia.tpl`). Browsers make their native controls
usable with the keyboard and announce them to screen readers. Your part is the options of each item:

| Option | Set it to | Why |
|---|---|---|
| **Controller** | on | without `controls`, there is no way to play, pause or change the volume |
| **Autoplay** | off | sound that starts by itself interferes with screen readers; WCAG 2.2 success criterion 1.4.2 asks for a way to stop it |
| **Loop** | off | an episode that never ends is confusing |

The template writes `preload="none"`: nothing is downloaded until the visitor presses play, which helps people on
slow or metered connections.

The older player types (Flash, QuickTime, RealPlayer, Silverlight, Windows Media Player) produce plugin markup that
current browsers and assistive technology cannot use; never choose them for new content.

## Embedded players

A player from a video or streaming service brings its own accessibility. Before you rely on it:

- check that it can be operated with the keyboard and that the focus can leave it;
- check that captions can be switched on;
- give the block a **Caption** that says what the video is; the External video block uses it as the player's title.

On installations with Exponential Layouts, the External video block shows a **Load video** button first; the button is
a real button, so keyboard users can reach it ([Embedding a video or a stream](embedding-a-video-or-stream.md)).

## The page around the player

- **A clear title** with the episode number and topic.
- **Headings** from the editor's paragraph styles for the parts of the show notes, so screen reader users can jump.
- **Link texts** that make sense alone: "Read the transcript", not "click here".
- **Images** with an alternative text that says what they show ([Artwork and episode images](artwork-and-episode-images.md)).
- **The transcript** on the page or one click away ([Transcripts and captions](transcripts-and-captions.md)).
- **Language**: an episode in another language than the site is a translation or an item in that language, so the
  page carries the right language ([Multilingual shows](multilingual-shows.md)).

## Downloads

For a download link, say the format and size before people click. The file field's view shows the name and size, and the example template on
[Files and downloads](../creators/website-creators/files-and-downloads.md#download-links-in-templates) adds the MIME
type; for audio in a media field, write them in the link text yourself, for example "Download the episode (MP3,
48 MB)".

## Checklist per episode

- [ ] **Controller** on, **Autoplay** and **Loop** off.
- [ ] The player works with the keyboard alone: Tab to it, play, pause, seek.
- [ ] A transcript or captions are available.
- [ ] Headings structure the notes; links have meaningful text.
- [ ] Every image has an alternative text.
- [ ] Download links state format and size.

For a fuller site check, see [Testing accessibility](../creators/website-creators/testing-accessibility.md) and
the [Accessibility checklist for editors](../customers/day-to-day/accessibility-checklist-for-editors.md).

## Related pages

- [Transcripts and captions](transcripts-and-captions.md)
- [Making your coverage accessible](../press/making-your-coverage-accessible.md)
- [Accessibility for site builders](../creators/website-creators/accessibility-for-site-builders.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
