---
title: What a regional or state government publishes
audience: government, project-manager, website-creator
category: public-sector
order: 20
summary: The content regional and state governments typically publish - services by theme, assembly decisions, plans, funding programs, news, information for municipalities - and what each asks of Exponential.
keywords: regional website, state website, services by theme, assembly decisions, regional plans, funding programs, grants, calls, municipalities, scope
type: help
---
# What a regional or state government publishes

This page is for project managers and website creators at a state, province, region or county who set the scope of
a site. It lists what such organizations typically publish (general public-sector practice; responsibilities differ
between countries) and what each kind of content needs from the system.

## Contents

1. [The content](#the-content)
2. [What the CMS must do](#what-the-cms-must-do)
3. [Content for municipalities and partners](#content-for-municipalities-and-partners)
4. [Questions to settle before building](#questions-to-settle-before-building)

## The content

| Content | Audience | Shape |
|---|---|---|
| Services by theme (transport, environment, health, education, culture, economy ...) | citizens, businesses | stable pages, reviewed yearly, with links to online forms |
| The regional assembly: members' public role information, committees, agendas, minutes, decisions | citizens, press, municipalities | documents on a meeting calendar, kept permanently |
| Plans and strategies: spatial plans, mobility plans, environmental programs | professionals, citizens | long documents with maps, consultation periods |
| Funding programs: calls, criteria, deadlines, results | applicants | a page per call with dates and documents |
| News and press releases | press, citizens | frequent |
| Road works, closures, disruptions | travelers | short-lived, urgent |
| Offices and contact points | everyone | addresses, opening hours, accessibility of the building |
| Information and toolkits for municipalities | municipal staff | guidance, templates, sometimes behind a sign-in |
| Jobs and procurement | applicants, suppliers | with deadlines |

## What the CMS must do

| Need | Exponential feature | Read |
|---|---|---|
| A page per call with opening and closing dates | a class with `ezdatetime` attributes and lists sorted by date | [Choosing datatypes](../../../creators/website-creators/choosing-datatypes.md) |
| Decisions and minutes as permanent documents | File objects, relations, versions | [Files and downloads](../../../creators/website-creators/files-and-downloads.md) |
| Short-lived notices that go away by themselves | the `unpublish.php` or `hide.php` cronjob with `content.ini [UnpublishSettings]` or `[HideSettings]` | [Cronjobs](../../../../guides/cronjobs.md) |
| Themes that cut across departments | tags | [Tagging with eztags](../../../creators/website-creators/tagging-with-eztags.md) |
| A point on a map for an office or a site | the `ezgmaplocation` datatype (Google Maps, a third party) | [ezgmaplocation](../../../../features/6.0/extensions/ezgmaplocation.md) |
| An area for municipal staff only | a section that Anonymous cannot read, and a role for municipal accounts | [Member registration and members-only areas](../../../creators/website-creators/member-registration-and-areas.md) |
| A feed of disruptions for other sites and apps | RSS export of a folder | [RSS feeds](../../../../guides/rss-feeds.md) |
| Several sites (region, tourism, a program) | siteaccesses on one installation | [Multisite setup](../../../creators/website-creators/multisite-setup.md) |

The two cronjobs work differently: `unpublish.php` moves objects of the classes in `[UnpublishSettings] ClassList[]`
whose `unpublish_date` attribute has passed to the trash, below the nodes of `RootNodeList[]`; `hide.php` hides
nodes whose date attribute (named per class in `[HideSettings] HideDateAttributeList[]`) has passed. Hiding keeps the
content for the record; prefer it for anything that may be asked for later.

## Content for municipalities and partners

Regional bodies often publish for the municipalities in their territory: model documents, guidance, data. Decide
whether this is public (it often can be, and should be when it explains public decisions) or restricted. A restricted
area uses a section and a role, and every municipal user gets an account in a dedicated user group
([Sections, 13.1](../../../../guides/sections.md#131-a-members-area)). Accounts of people outside your organization
need an owner and a review cycle: **Users > Check user accounts** lists accounts not seen for a number of days
([Check user accounts](../../../../features/6.0/user-hygiene.md)).

## Questions to settle before building

- [ ] Which services are the region's and which belong to municipalities or national government? Link the others.
- [ ] Where do assembly documents live today, and must the site be their official place of publication?
- [ ] Which languages have legal status in the region?
- [ ] Which content is about places, and is a link to an existing map portal enough?
- [ ] Will the region host sites for municipalities (see the shared services page)?

## Related pages

- [Regional and state government](README.md)
- [Content model for regional and state government](content-model.md)
- [Site map and content inventory](../../../creators/website-creators/site-map-and-content-inventory.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
