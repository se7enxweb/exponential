---
title: Board and minutes pages
audience: non-profit, editor, website-creator, government
category: non-profits
order: 40
summary: Publishing a non-profit's board, governance and meetings on Exponential - a board page with consent from members, a meeting class of your own (no meeting class ships) with agendas, papers and minutes below it, a structure per year, board-only papers in a section with its own role, scheduled publication, corrections and a checklist.
keywords: board, trustees, directors, governance, minutes, agendas, meetings, meeting class, board papers, board-only section, role, scheduled publication, corrections, bylaws
type: help
---
# Board and minutes pages

This page is for board secretaries, directors and the website creator. Many non-profits publish who sits on the board
and what it decided; some must. Board papers that are not public still benefit from a safe place online for board
members. Exponential handles both with ordinary content, a section and a role.

> **Note**
> Exponential ships **no meeting or board class**. The `ezwebin` site design has articles, folders, files and events,
> and the meeting class below is one you create. The pattern comes from
> [Meeting records, agendas and minutes](../../government/transparency-and-foi/meeting-records-agendas-and-minutes.md).

## Contents

1. [The board page](#the-board-page)
2. [A meeting class](#a-meeting-class)
3. [Structure per year](#structure-per-year)
4. [Before and after a meeting](#before-and-after-a-meeting)
5. [Board-only papers](#board-only-papers)
6. [Corrections](#corrections)
7. [Checklist](#checklist)

## The board page

| Content | Note |
|---|---|
| Names and roles (chair, treasurer, secretary) | With each member's consent |
| A short biography | Optional; written or approved by the member |
| Photo | Only with permission; alternative text with the name |
| Term of office | Start and end |
| How the board works | Meetings per year, committees, link to bylaws |
| Conflicts of interest policy | As a file or page |
| Contact | A role address, not private mail addresses |

Use an article, or a folder with one article per member. Never publish private phone numbers or home addresses.

## A meeting class

Create a container class (identifier for example `board_meeting`) with attributes such as:

| Attribute | Datatype |
|---|---|
| Title (with the date) | Text line |
| Date and time | Date and time |
| Status | Selection: Scheduled, Agenda published, Held, Minutes approved, Cancelled |
| Agenda | Rich text, or a file below |
| Minutes summary | Rich text, after the meeting |

Papers (agenda, reports, minutes) are File objects below the meeting. Source and further attributes:
[Meeting records, agendas and minutes, A meeting class](../../government/transparency-and-foi/meeting-records-agendas-and-minutes.md#a-meeting-class).
Creating a class: [Content classes for websites](../../creators/website-creators/content-classes-for-websites.md).

If you only publish minutes a few times a year, a folder per year with File objects is enough; you need no class.

## Structure per year

```text
Governance
  Board members
  Board meetings
    2026
      2026-03-12 Board meeting
        Agenda (file)
        Minutes (file, after approval)
    2025
  Board papers          <- board-only section
```

Old aliases redirect when folders are renamed ([Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md)).

## Before and after a meeting

| When | What | How |
|---|---|---|
| Before | Publish the date and agenda, if public | Scheduled publication if needed ([Scheduled publishing](../../customers/day-to-day/scheduled-publishing.md)) |
| After | Set status to Held | Edit the meeting |
| After approval of minutes | Upload the approved minutes, set status | File object below the meeting |

Publish only approved minutes. Draft minutes belong in the board-only section.

## Board-only papers

1. Create a section "Board" and assign the Board papers folder to it.
2. Create a role "Board member" with `content/read` limited to the Board section, assign it to a "Board" user group.
3. Give each board member a personal account in that group, with two-factor login
   ([Two-factor login](../../../guides/two-factor-login.md)).
4. Files in the section are downloaded through `content/download`, which checks the permission
   ([Files and downloads, Protected downloads](../../creators/website-creators/files-and-downloads.md#protected-downloads)).

Recipe: [Sections and object states](../../creators/website-creators/sections-and-object-states.md). When a board member's
term ends, remove them from the group the same week ([When volunteers leave](../operations/when-volunteers-leave.md)).

## Corrections

If approved minutes are corrected, publish the correction visibly. The earlier version stays in the version history
([Versions and the audit trail](versions-and-the-audit-trail.md)).

## Checklist

- [ ] Board members consented to what is published about them.
- [ ] Meeting structure per year; class created only if needed.
- [ ] Only approved minutes public; drafts in the board-only section.
- [ ] Board role limited to the Board section; personal accounts with two-factor login.
- [ ] Departing members removed from the group.
- [ ] Bylaws and policies current.

## Related pages

- [Transparency for non-profits](README.md)
- [Decision registers](../../government/transparency-and-foi/decision-registers.md)
- [Members-only content](../../businesses/members/members-only-content.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
