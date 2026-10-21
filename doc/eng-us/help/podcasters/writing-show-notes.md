---
title: Writing show notes
audience: podcaster
category: podcasting-streaming
order: 14
summary: How to write show notes in Exponential's rich text editor so they serve listeners on the site and in podcast apps - structure, links, chapter times, credits, sponsor disclosure, and how the notes reach the feed's description.
keywords: show notes, episode description, rich text, online editor, links, chapters, timestamps, credits, sponsor disclosure, feed description, SEO, accessibility
type: help
---
# Writing show notes

This page is for hosts and producers who write the text that goes with an episode. Show notes have two readers: the
visitor on the episode page, and the podcast app that shows the feed's description. A ready structure is on
[Show notes template](show-notes-template.md).

## Contents

1. [Where the notes go](#where-the-notes-go)
2. [What good notes contain](#what-good-notes-contain)
3. [Links](#links)
4. [Chapter times](#chapter-times)
5. [Credits and disclosure](#credits-and-disclosure)
6. [How notes reach the feed](#how-notes-reach-the-feed)

## Where the notes go

Write the notes in the episode's rich text field (*Description*, *Caption*, *Show notes*, depending on the class).
The rich text editor offers paragraph styles, bold, italic, lists, links, tables and **Embed object**
([Creating and editing content](../editing-content.md#rich-text)). Use headings from the paragraph styles rather than
bold lines, so screen readers and search engines see the structure.

## What good notes contain

1. **One or two sentences** on what the episode is about, first, because apps often cut the rest.
2. **Guests** with their role and a link each.
3. **Topics or chapters** with times.
4. **Links** to everything mentioned.
5. **Credits**: host, producer, editor, music.
6. **Disclosure** of sponsors and paid mentions.
7. **Where to find the transcript**, if it is on another page ([Transcripts and captions](transcripts-and-captions.md)).

## Links

- Link with meaningful text: "the 6.0 release notes", not "here".
- Link to other episodes with the editor's link dialog, which can pick an item of the site; such links follow the
  episode when it moves.
- Check external links before publishing. The site's link check can report broken links later
  ([Link and content checks](../creators/website-creators/link-and-content-checks.md)).

## Chapter times

Exponential does not write chapter markers into the audio or the feed. Write chapter times as plain text in the notes:

```text
00:00 Introduction
04:30 Interview with the guest
38:10 Listener questions
```

Use `MM:SS` or `HH:MM:SS` consistently. Some apps recognize such times in the description; on the site they are plain
text.

## Credits and disclosure

- Name everyone who worked on the episode and wants to be named. Ask guests how they want to be credited
  ([Guests and credits](guests-and-credits.md)).
- State sponsorship at the top of the notes as well as in the audio. The press section explains the ethics
  ([Disclosure and sponsorship ethics](../press/disclosure-and-sponsorship-ethics.md)).
- Credit music and other third-party material with its license ([Rights and licensing](rights-and-licensing.md)).

## How notes reach the feed

In the podcast export, each episode's `description` comes from the field mapped as **Description** for its source
([Episode mapping reference](episode-mapping-reference.md)). For a rich text field the feed gets the field's rendered
output as text (`podcastText()` in `kernel/classes/ezrssexport.php`); the markup is escaped into the XML, as RSS
requires. Many apps show it with links; some show plain text.

If the feed should show only a short summary, give the class a separate short description field and map that as
**Description**, and keep the full notes on the episode page.

## Before publishing

- [ ] The first sentence works on its own.
- [ ] Every guest is named and linked as agreed.
- [ ] Sponsors are disclosed.
- [ ] Links are tested.
- [ ] No private data: no phone numbers, private addresses or e-mail addresses of guests unless they asked for it
  ([Privacy in your coverage](../press/privacy-in-coverage.md)).

## Related pages

- [Show notes template](show-notes-template.md)
- [Podcast and radio guests](../press/podcast-and-radio-guests.md)
- [SEO basics](../creators/website-creators/seo-basics.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
