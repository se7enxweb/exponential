---
title: Rights and licensing of what you publish
audience: podcaster
category: podcasting-streaming
order: 35
summary: How to handle the rights in episodes you publish on an Exponential site - your own license, music and clips, guest releases, artwork and photos, embedded third-party players - which fields Exponential has for it and which you add, and how to react when a rights problem appears.
keywords: rights, license, licensing, copyright, Creative Commons, music clearance, guest release, consent, artwork rights, photo credit, third-party content, takedown, GPL, ownership
type: help
---
# Rights and licensing of what you publish

This page is for producers who publish episodes and want to be clear about who owns what, what listeners may do
with it, and what was cleared. It explains what Exponential records and what you add. It is not legal advice; for
contracts and disputes, ask a lawyer.

## Contents

1. [Your content is yours](#your-content-is-yours)
2. [Where rights information goes](#where-rights-information-goes)
3. [Music, clips and sound effects](#music-clips-and-sound-effects)
4. [Guests and contributors](#guests-and-contributors)
5. [Images and artwork](#images-and-artwork)
6. [Embedded players](#embedded-players)
7. [When a rights problem appears](#when-a-rights-problem-appears)

## Your content is yours

Exponential is free software under the GNU General Public License, version 2 or later. That license covers the
software, not what you publish with it: your episodes, notes and artwork are yours, under the terms you choose
([Licenses explained for reporters](../press/licenses-explained-for-reporters.md)). This help is under the GNU FDL 1.3
([Free documentation](../free-documentation.md)); that, too, says nothing about your show.

## Where rights information goes

Exponential has no dedicated license field. What exists:

| Place | What it holds | Where it appears |
|---|---|---|
| The feed's **Copyright** channel field | one line for the whole show | `copyright` element of the podcast feed |
| A field you add to the episode class | the license of each episode, for example a **Selection** (`ezselection`) with your licenses, or a text line | the episode page, if the design shows it |
| The *Person* class's **Copyright** field (media site installations) | the credit of a person's photo | the person page |
| The show notes | music credits and licenses, sources | the episode page and the feed description |
| A **Tags** field (eztags, where installed) | a license as a tag, so episodes can be listed by license | lists and search |

Suggested episode fields: **License** (selection), **Music credits** (text block), **Rights notes** (an internal text
block your design does not show). See [Building an episode class](building-an-episode-class.md).

An internal field is still stored in the database and visible to everyone who may edit or read the item in the
administration. Keep contracts and releases in your own records, not in public content.

## Music, clips and sound effects

- Use only material you may publish: your own, licensed for podcasts and the web, or under a license that allows it.
- Note the license terms: some licenses forbid commercial use or require attribution in a set form.
- Credit in the show notes and, where the license asks, in the audio.
- Keep a record of what was used in which episode, with the license and its date; you need it when a claim comes years
  later. The press section's [Third-party music, fonts and assets](../press/third-party-music-fonts-and-assets.md#keeping-a-record)
  gives a pattern.

## Guests and contributors

- Agree in advance where the recording will be published (site, feed, directories, clips), for how long, and whether
  it may be edited.
- Agree how the guest is named and which photo and links are used ([Guests and credits](guests-and-credits.md)).
- Contributors who are not employees (freelance editors, composers) should have the same clarity in writing.

## Images and artwork

- The show artwork and episode images need the same clearance as audio.
- Photos of guests: use photos the guest supplied or that you took with their consent, and credit the photographer.
- Image items in the media library have a caption or description where your classes have one; put the credit there.

## Embedded players

A video embedded from a service remains the service's and the uploader's content. Embed only videos whose owner allows
embedding; the service usually enforces this itself. Embedding does not give you the right to download and re-upload
the video ([Embedding a video or a stream](embedding-a-video-or-stream.md)).

## When a rights problem appears

1. **Act quickly on the site**: hide the episode, which takes it off the site and out of the feed at the next cache
   refresh ([Hiding and retiring episodes](hiding-and-retiring-episodes.md)).
2. **Check every place** it appears: other pages, embeds, layouts, the newsletter, partner feeds.
3. **Decide**: edit (replace the music, cut a segment) and republish the same item so its `guid` stays, or remove it.
4. **Record** what happened and what you did.

Copies already downloaded by listeners' apps are outside your control.

## Related pages

- [Third-party music, fonts and assets](../press/third-party-music-fonts-and-assets.md)
- [Guests and credits](guests-and-credits.md)
- [Members-only episodes](members-only-episodes.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
