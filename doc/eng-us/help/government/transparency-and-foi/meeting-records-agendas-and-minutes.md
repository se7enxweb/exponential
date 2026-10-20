---
title: Meeting records, agendas and minutes
audience: government, editor, website-creator
category: public-sector
order: 120
summary: How to publish the meetings of councils, boards and committees with Exponential: a meeting class with date, body, agenda, papers and minutes, a structure per body and year, timely publication, corrections and language versions.
keywords: meetings, agenda, minutes, council, board, committee, meeting papers, public meetings, decisions, transparency, schedule
type: help
---
# Meeting records, agendas and minutes

This page is for clerks, editors and site builders who publish the meetings of a council, board or committee:
announcements, agendas, papers and minutes. Many jurisdictions set deadlines for publishing agendas before a meeting
and minutes after it; those rules come from your law. In Exponential each meeting is one content object with its
papers below it.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [A structure per body and year](#a-structure-per-body-and-year)
2. [A meeting class](#a-meeting-class)
3. [Before the meeting](#before-the-meeting)
4. [After the meeting](#after-the-meeting)
5. [Corrections](#corrections)
6. [Languages and accessibility](#languages-and-accessibility)

## A structure per body and year

```
Meetings
  Council
    2026
      2026-03-12 Council meeting
        Agenda (file)
        Report on item 4 (file)
        Minutes (file, after the meeting)
  Planning committee
    2026
```

Old aliases redirect when folders are renamed, so the structure can grow without broken links
([Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md)).

## A meeting class

| Attribute | Datatype | Note |
|---|---|---|
| Title | Text line | name pattern, for example "Council meeting" |
| Body | Selection | Council, Planning committee ... |
| Date and time | Date and time | |
| Place | Text line | or "online" with the link field below |
| Public link (stream) | URL | when meetings are streamed |
| Status | Selection | Scheduled, Agenda published, Held, Minutes approved, Cancelled |
| Agenda | Rich text | the items, or the agenda as a file below |
| Minutes summary | Rich text | filled in after the meeting |

Make it a container so the papers are children. Object name pattern such as `<title>` with the date in the title,
because the name becomes the address.

## Before the meeting

1. Create the meeting object when the date is set; status *Scheduled*.
2. Publish the agenda and papers by the legal deadline; status *Agenda published*. If papers must appear at an exact
   time, use the *Wait until date* workflow event (see "Scheduled publication and embargoes").
3. Papers that are not public (exempt items) are not uploaded to the public site; list the item with the ground for
   exemption.
4. An RSS export of the "Meetings" folder with subnodes tells subscribers about new agendas ([RSS feeds](../../../guides/rss-feeds.md)).

## After the meeting

1. Upload the draft or approved minutes as your rules require, and set the status.
2. Add the decisions to the decision register and link them (see "Decision registers").
3. Keep recordings and transcripts according to your records policy.

## Corrections

When approved minutes differ from the draft, publish the approved file as a new File object ("Minutes, approved on
<date>") instead of overwriting the draft silently, or replace it and add a dated note. The version history keeps the
earlier file for staff ([Content history](../../../guides/content-history.md)).

## Languages and accessibility

- In a site with several official languages, translate the meeting object; papers in each language are separate
  files, or one file per language below the meeting ([Content languages](../../../guides/content-languages.md)).
- Give papers as accessible documents or as page text; scanned PDFs without text are not accessible. The site's
  accessibility status is documented in [Accessibility standards](../../../standards/accessibility.md).

## Related pages

- [Decision registers](decision-registers.md)
- [Scheduled publication and embargoes](scheduled-publication-and-embargoes.md)
- [Files and downloads](../../creators/website-creators/files-and-downloads.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
