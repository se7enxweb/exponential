---
title: Small group site planning
audience: businesses, project-manager, editor
category: business-solutions
order: 10
summary: How a club, society or volunteer-run group plans a site on Exponential that survives changing volunteers - what the site is for, the few pages it needs, the shipped classes that cover them, who does what, where the group's accounts and records are kept, and a planning worksheet.
keywords: small group website, club website, association website, society, volunteer-run, planning, site structure, roles, continuity, worksheet
type: help
---
# Small group site planning

This page is for the chair, secretary or volunteer who sets up or rebuilds a small group's site. Small groups rarely
fail for lack of features; they fail when the one person who knew the site leaves. This page plans for that from the
start, and keeps the site small enough for volunteers to run.

## Contents

1. [What the site is for](#what-the-site-is-for)
2. [The few pages a group needs](#the-few-pages-a-group-needs)
3. [Classes you do not have to build](#classes-you-do-not-have-to-build)
4. [Who does what](#who-does-what)
5. [Public, members-only and committee-only](#public-members-only-and-committee-only)
6. [Planning for continuity](#planning-for-continuity)
7. [Keeping it small](#keeping-it-small)
8. [A planning worksheet](#a-planning-worksheet)

## What the site is for

Write down, in one sentence each, the two or three things the site must do. Typical for a group:

- tell newcomers who the group is and how to join;
- announce meetings and events;
- publish news, minutes or newsletters for members;
- collect contact requests, registrations or feedback.

Everything else is optional. The general method is in
[Planning your website](../../creators/website-creators/planning-your-website.md).

## The few pages a group needs

- [ ] Home: who you are, the next event, how to join.
- [ ] About: purpose, committee by role, statutes or rules as a download.
- [ ] News.
- [ ] Events calendar ([Events and news for groups](events-and-news-for-groups.md)).
- [ ] Join or contact form ([Forms and member feedback](forms-and-member-feedback.md)).
- [ ] Members area, if needed ([Members](../members/README.md)).
- [ ] Privacy notice and legal information your law requires.

## Classes you do not have to build

The `ezwebin` site interface ships ready class packages (`extension/ezwebin/packages/`), among them:

| Class package | Use in a group |
|---|---|
| `folder` | Sections of the site |
| `article`, `article_main_page`, `article_sub_page` | News and longer pages |
| `event`, `event_calendar`, `multicalendar` | Meetings and events |
| `feedback_form` | Contact and feedback |
| `file` | Minutes, statutes, reports as downloads |
| `blog`, `blog_post` | A group blog |
| `forums` | Discussion forums |
| `frontpage` | The home page |
| `user_group` | Groups of member accounts |

Using these instead of custom classes means less to explain at every handover. If your site was installed with a
site package, check which classes it already has before adding any
([Site packages and shipped themes](../../creators/website-creators/site-packages-and-shipped-themes.md)).

## Who does what

Name roles, not people, because people change:

| Role | Typical holder | Needs |
|---|---|---|
| Web coordinator | A committee member | Overview, approves structure changes |
| Editors | Volunteers | Edit their own section |
| Form reader | Secretary | Read submissions (`infocollector/read`) |
| Administrator | A skilled volunteer or a partner | Settings, updates, backups, accounts |
| Deputy administrator | A second person | The same, to take over at any time |

Details in [Shared editing with volunteers](shared-editing-with-volunteers.md).

## Public, members-only and committee-only

Decide which content is public, which is for members, and which is for the committee only (draft minutes, contact
lists). Sections and roles keep them apart, and downloads are permission-checked
([Sections and object states](../../creators/website-creators/sections-and-object-states.md),
[Files and downloads](../../creators/website-creators/files-and-downloads.md#protected-downloads)). Keep personal data
of members out of public pages entirely.

## Planning for continuity

Write these down in a place the committee owns, not in one volunteer's mailbox:

- [ ] Who holds the domain name, and the login to the registrar.
- [ ] Who holds the hosting account, and the login to it.
- [ ] Where backups are kept and how to restore them.
- [ ] Which mail address sends forms and newsletters.
- [ ] The administrator and deputy, with their roles in the site.

Store passwords in a password manager the committee controls, never in the site's content or settings files that go
into version control. The handover itself is described in
[Handover when volunteers change](handover-when-volunteers-change.md).

## Keeping it small

- Use shipped classes and one design; avoid custom code a future volunteer cannot maintain.
- Prefer a hosted or managed setup if no volunteer can patch a server
  ([Low-budget operations](../nonprofits/low-budget-operations.md#hosting-choices)).
- SQLite, the default database, needs no database server, which suits a group with a few editors
  ([SQLite database support](../../../features/6.0/sqlite-database.md)).
- Remove features nobody uses at each yearly review.

## A planning worksheet

```text
The site must:              1. ...  2. ...  3. ...
Pages:                      ...
Members area:               yes / no
Web coordinator (role):     ...
Administrator / deputy:     ... / ...
Form reader:                ...
Domain held by:             ...
Hosting held by:            ...
Backups kept at:            ...
Sender address for mail:    ...
Yearly review month:        ...
```

## Related pages

- [Associations and small groups](README.md)
- [Small group checklist](small-group-checklist.md)
- [Non-profit site planning](../nonprofits/nonprofit-site-planning.md)
- [Planning your website](../../creators/website-creators/planning-your-website.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
