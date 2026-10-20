---
title: Accessible event information
audience: government, website-creator, editor, designer
category: public-sector
order: 80
summary: What every public event page must say in text so everyone can attend - date and time, place and how to get there, venue accessibility, what support is offered, how to ask - with template and editor practices and the WCAG criteria behind them.
keywords: accessible events, event information, venue accessibility, step-free access, hearing loop, sign language, captions, date format, time element, posters, alternative text, plain language
type: help
---
# Accessible event information

This page is for website creators, designers and editors who publish public events. An event is only open to everyone
if everyone can find out what, when and where, and whether they can take part. Much event information still arrives as
a poster image; this page sets out what must be on the page as text. The accessibility status of Exponential itself
is in [Accessibility standards](../../../../standards/accessibility.md): the public templates are yours to test.

## Contents

1. [What every event page says](#what-every-event-page-says)
2. [Venue and support](#venue-and-support)
3. [Dates and times](#dates-and-times)
4. [Posters, images and video](#posters-images-and-video)
5. [Checklist for editors](#checklist-for-editors)

## What every event page says

| Item | Where in the class |
|---|---|
| What it is, in one or two plain sentences | the start of **Text** |
| Date, start and end time | **From Time**, **To Time** |
| Place, with address | a Place relation or text |
| Cost, and whether booking is needed | Cost, Booking |
| Who it is for | Audience |
| Accessibility of the venue and the event | Accessibility at the event |
| How to ask a question or request support | a contact point |

## Venue and support

| Topic | Write |
|---|---|
| Getting in | step-free entrance or not, where; lifts; distances |
| Hearing | hearing loop, captions, sign language interpretation (offered or on request, by when) |
| Seeing | large print materials, guide dogs welcome |
| Other needs | quiet space, seating, accessible toilets |
| Getting there | public transport, accessible parking |

Say what exists, not "accessible" alone; people need details to decide.

## Dates and times

| Practice | Why |
|---|---|
| Write dates in full in text: "Tuesday 14 April 2026, 18:00 to 20:00" | unambiguous for everyone and every screen reader |
| Mark them up with `<time datetime="...">` in the template | machines read the exact value |
| Say the time zone when visitors may be elsewhere | online events |
| Do not rely on a calendar grid alone | grids are hard to use with assistive technology ([Calendar views](calendar-views.md#accessibility-of-calendars)) |

## Posters, images and video

| Item | Practice |
|---|---|
| A poster image | never the only source of information; the same facts in text; the alternative text summarizes the poster's purpose |
| Photos | alternative text required ([Content model and editing, 9](../../../../guides/content-model-and-editing.md#9-required-alternative-text-trimmed-text-lines-and-names-made-by-php)) |
| Video invitations or recordings | captions and a transcript |
| PDF programs | the program on the page too |

Relevant [WCAG 2.2](https://www.w3.org/TR/WCAG22/) criteria: 1.1.1 Non-text content, 1.2.2 Captions (prerecorded),
1.3.1 Info and relationships, 2.4.4 Link purpose, 3.1.5 Reading level (AAA, useful as guidance).

## Checklist for editors

- [ ] Date, time and place are in the text, not only on the poster.
- [ ] The accessibility section says what is available and how to request support.
- [ ] Every image has an alternative text; videos have captions.
- [ ] Links say where they go ("Book a place for <event>").
- [ ] Plain language: short sentences, no unexplained abbreviations.

## Related pages

- [Events](README.md)
- [Accessible forms](../forms/accessible-forms.md)
- [Accessibility for site builders](../../../creators/website-creators/accessibility-for-site-builders.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
