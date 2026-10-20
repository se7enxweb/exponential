---
title: Content model for municipal and local government
audience: government, website-creator, project-manager
category: public-sector
order: 30
summary: Shipped Exponential classes that fit a municipal site and recipes for Service, Meeting, Public notice and Contact point classes with real datatypes, kept small enough for a small team.
keywords: content model, municipality, council, service class, meeting class, public notice class, contact point, hide date, unpublish_date, ezdatetime, ezselection, class editor
type: help
---
# Content model for municipal and local government

This page is for website creators and project managers who design the classes of a city, town or council site. A
small team maintains the site, so the model stays small: four added classes beside the shipped ones. Every class and
datatype named exists in Exponential ([sevenx_classes](../../../site-packages/sevenx-multisite/sevenx-classes.md),
`kernel/classes/datatypes/`, the extensions named); the class editor is described in
[Content classes for websites](../../../creators/website-creators/content-classes-for-websites.md).

## Contents

1. [Shipped classes that fit](#shipped-classes-that-fit)
2. [Class to add: Service](#class-to-add-service)
3. [Class to add: Meeting](#class-to-add-meeting)
4. [Class to add: Public notice](#class-to-add-public-notice)
5. [Class to add: Contact point](#class-to-add-contact-point)
6. [Keeping the model small](#keeping-the-model-small)

## Shipped classes that fit

| Class | Identifier | Use |
|---|---|---|
| Folder | `folder` | service areas (Waste, Parking ...), the council, about us |
| Article | `article` | news, explanations |
| File | `file` | agendas, minutes, forms to print |
| Event, Event calendar | `event`, `event_calendar` | the events calendar |
| Feedback form | `feedback_form` | contact and simple report forms |
| Poll | `poll` | quick opinion questions (not consultations) |
| Link | `link` | links to the online services of other systems |
| Image, Gallery | `image`, `gallery` | photos |

## Class to add: Service

| Attribute | Identifier | Datatype | Required |
|---|---|---|---|
| Name of the service | `title` | Text line (`ezstring`) | yes |
| In short | `summary` | Text block (`eztext`) | yes |
| Who can use it | `eligibility` | XML block (`ezxmltext`) | no |
| What it costs | `cost` | XML block | no |
| How to apply | `how_to_apply` | XML block | yes |
| Apply online | `online_link` | URL (`ezurl`) | no |
| How long it takes | `processing_time` | Text line | no |
| Contact | `contact` | Object relation (`ezobjectrelation`) to a Contact point | no |
| Related documents | `documents` | Object relations (`ezobjectrelationlist`) | no |
| Topics and life events | `tags` | Tags (`eztags`) | no |
| Next review | `review_date` | Date (`ezdate`) | yes |

The required review date is a reminder that service information goes stale. A list of services past their review
date can be fetched in a template for the web team's dashboard page.

## Class to add: Meeting

| Attribute | Identifier | Datatype | Why |
|---|---|---|---|
| Body | `body` | Selection (`ezselection`) | council, a committee |
| Date and time | `meeting_date` | Date and time (`ezdatetime`) | |
| Place | `place` | Text line | the room and address |
| Agenda | `agenda` | XML block | the items, with links to papers |
| Papers | `papers` | Object relations | File objects |
| Minutes | `minutes` | Object relation | the File object once approved |
| Status | `status` | Selection | scheduled, agenda published, held, minutes approved, cancelled |
| Watch | `webcast` | URL | a recording or stream, if any |

See [Council meetings and local services](council-meetings-and-local-services.md) for the publishing cycle.

## Class to add: Public notice

| Attribute | Identifier | Datatype | Why |
|---|---|---|---|
| Title | `title` | Text line | |
| Notice type | `notice_type` | Selection | planning, road closure, statutory, other |
| Text | `text` | XML block | |
| Valid from | `valid_from` | Date | |
| Hide after | `hide_date` | Date and time | used by `hide.php` |
| Documents | `documents` | Object relations | |

To hide notices automatically, name the class and its date attribute in `content.ini`:

```ini
# settings/override/content.ini.append.php
[HideSettings]
RootNodeList[]=<node id of the Notices folder>
HideDateAttributeList[public_notice]=hide_date
```

Then clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini`) and make sure the cronjob runs `hide.php` (it is
in the global part, `[CronjobSettings] Scripts[]`). Hidden notices stay in the administration for the record
([Cronjobs](../../../../guides/cronjobs.md)). The other cronjob, `unpublish.php`, moves objects with an attribute
named exactly `unpublish_date` to the trash; prefer hiding for notices you may have to prove were published.

## Class to add: Contact point

| Attribute | Identifier | Datatype |
|---|---|---|
| Name (a team or desk, not a person) | `name` | Text line |
| Address | `address` | Text block |
| Opening hours | `opening_hours` | XML block |
| Phone | `phone` | Text line |
| E-mail | `email` | Email (`ezemail`) |
| Accessibility of the building | `access` | XML block |

## Keeping the model small

| Temptation | Better |
|---|---|
| A class per department | one Service class; the department is a section |
| A class per notice type | one Public notice class with a Selection |
| Free-text dates | date attributes, so lists and expiry work |
| Named officials as contacts | team contact points; people change, desks stay |

## Related pages

- [Municipal and local government](README.md)
- [Structure, sections and roles for municipal and local government](structure-sections-and-roles.md)
- [Choosing datatypes for your fields](../../../creators/website-creators/choosing-datatypes.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
