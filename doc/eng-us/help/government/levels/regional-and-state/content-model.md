---
title: Content model for regional and state government
audience: government, website-creator, project-manager
category: public-sector
order: 30
summary: Shipped Exponential classes that fit a regional or state site, and recipes for Plan, Funding program, Decision and Office classes built from real datatypes, including the map location datatype and its third-party implication.
keywords: content model, regional, state, plan class, funding program, grant call, decision class, office class, ezgmaplocation, ezdatetime, ezselection, eztags, class editor
type: help
---
# Content model for regional and state government

This page is for website creators and project managers who design the content classes of a state, province, region
or county site. It lists the shipped classes that fit and gives four class recipes. The classes and datatypes named
exist in Exponential ([sevenx_classes](../../../site-packages/sevenx-multisite/sevenx-classes.md), the datatypes of
`kernel/classes/datatypes/` and the extensions named); the class editor is described in
[Content classes for websites](../../../creators/website-creators/content-classes-for-websites.md).

## Contents

1. [Shipped classes that fit](#shipped-classes-that-fit)
2. [Class to add: Plan](#class-to-add-plan)
3. [Class to add: Funding program](#class-to-add-funding-program)
4. [Class to add: Decision](#class-to-add-decision)
5. [Class to add: Office](#class-to-add-office)
6. [Notes](#notes)

## Shipped classes that fit

| Class | Identifier | Use |
|---|---|---|
| Folder | `folder` | themes, departments, landing pages of a section of the site |
| Article | `article` | service pages, news, explanations |
| File | `file` | plans, minutes, decisions as documents |
| Event | `event` / Event calendar `event_calendar` | consultations sessions, information evenings, regional events |
| Geo Article | `geo_article` | an article with a map location (`ezgmaplocation`) |
| Link | `link` | links to online services and to municipal sites |
| Feedback form | `feedback_form` | contact forms |
| Job position | `ng_job_position` | vacancies |

`geo_article` is in the `sevenx_classes` package, but it shows a Google map and needs a Google Maps key
(`site.ini [SiteSettings] GMapsKey`) and the `ezgmaplocation` extension. Loading a Google map sends visitor data to a
third party; decide this with your privacy officer, and consider a plain address with a link to your own map portal.

## Class to add: Plan

| Attribute | Identifier | Datatype | Why |
|---|---|---|---|
| Title | `title` | Text line (`ezstring`) | |
| Summary | `summary` | XML block (`ezxmltext`) | what the plan decides, in plain language |
| Status | `status` | Selection (`ezselection`) | draft for consultation, adopted, revoked |
| Adopted on | `adopted_on` | Date (`ezdate`) | |
| Consultation closes | `consultation_closes` | Date and time (`ezdatetime`) | shown while open |
| Documents | `documents` | Object relations (`ezobjectrelationlist`) | the plan, annexes, maps as File objects |
| Area | `area` | Tags (`eztags`) | the places it covers, from a tag tree of the region's areas |
| Themes | `themes` | Tags (`eztags`) | |

A tag tree of areas (region > district > municipality) lets visitors list every plan for one place; it needs no GIS
([eztags](../../../../features/6.0/extensions/eztags.md)).

## Class to add: Funding program

| Attribute | Identifier | Datatype | Why |
|---|---|---|---|
| Title | `title` | `ezstring` | |
| Who can apply | `eligibility` | `ezxmltext` | |
| Budget | `budget` | `ezstring` | written as published, with currency |
| Opens | `opens` | `ezdatetime` | |
| Closes | `closes` | `ezdatetime` | lists of open calls compare it with the current time |
| How to apply | `how_to_apply` | `ezxmltext` | with a link to the application system |
| Application link | `application_url` | URL (`ezurl`) | the transaction usually lives elsewhere |
| Documents | `documents` | `ezobjectrelationlist` | guidance, forms |
| Results | `results` | `ezxmltext` | published after the decision |

Collect applications in a dedicated system when they carry personal or financial data; the information collector
suits short questions, not grant applications with attachments and scoring.

## Class to add: Decision

| Attribute | Identifier | Datatype | Why |
|---|---|---|---|
| Title | `title` | `ezstring` | |
| Decision number | `number` | `ezstring` | the citable reference |
| Decided by | `body` | `ezselection` | assembly, executive, committee |
| Date | `decision_date` | `ezdate` | |
| Summary | `summary` | `ezxmltext` | |
| Decision document | `document` | File (`ezbinaryfile`) | the signed text |
| Meeting | `meeting` | Object relation (`ezobjectrelation`) | the meeting page where it was taken |

## Class to add: Office

| Attribute | Identifier | Datatype | Why |
|---|---|---|---|
| Name | `name` | `ezstring` | |
| Address | `address` | Text block (`eztext`) | plain text, readable without a map |
| Opening hours | `opening_hours` | `ezxmltext` | a table or list |
| Phone | `phone` | `ezstring` | a service number, not a person's |
| E-mail | `email` | Email (`ezemail`) | a shared mailbox |
| Building accessibility | `access` | `ezxmltext` | step-free entrance, parking, hearing loop |
| Location | `location` | Google Maps Location (`ezgmaplocation`) | optional; third party |

## Notes

| Rule | Reason |
|---|---|
| Dates as date attributes | lists of open calls and expiry depend on them |
| Places as tags or plain text, a map only where it helps | no GIS in the product, maps are a third party |
| One Decision object per decision, related to its meeting | citations link to one stable address |
| Shared contact data in Office objects, related from service pages | changed once |

## Related pages

- [Regional and state government](README.md)
- [Structure, sections and roles for regional and state government](structure-sections-and-roles.md)
- [Choosing datatypes for your fields](../../../creators/website-creators/choosing-datatypes.md)
- [Relations and reuse](../../../creators/website-creators/relations-and-reuse.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
