---
title: Content model for emergency services
audience: government, website-creator, project-manager
category: public-sector
order: 30
summary: Shipped Exponential classes that fit an emergency service site and recipes for Incident update, Preparedness guide and Station classes - few fields, fast to fill on a phone, with an end time for automatic hiding.
keywords: content model, emergency services, incident update class, warning, preparedness guide, station class, hide date, ezdatetime, ezselection, eztags, class editor
type: help
---
# Content model for emergency services

This page is for website creators and project managers who design the classes of an emergency service site. Incident
content is written under pressure, so its class has few fields. The classes and datatypes named exist in Exponential
([sevenx_classes](../../../site-packages/sevenx-multisite/sevenx-classes.md), `kernel/classes/datatypes/`); the class
editor is in [Content classes for websites](../../../creators/website-creators/content-classes-for-websites.md).

## Contents

1. [Shipped classes that fit](#shipped-classes-that-fit)
2. [Class to add: Incident update](#class-to-add-incident-update)
3. [Class to add: Preparedness guide](#class-to-add-preparedness-guide)
4. [Class to add: Station](#class-to-add-station)
5. [Design rules for incident content](#design-rules-for-incident-content)

## Shipped classes that fit

| Class | Identifier | Use |
|---|---|---|
| Folder | `folder` | incidents, prevention, services, about |
| Article | `article` | news, press releases |
| File | `file` | reports, plans, leaflets |
| Job position | `ng_job_position` | recruitment, with an application deadline |
| Event | `event` | open days, community safety events |
| Image, Gallery | `image`, `gallery` | photos with alternative text |
| Link | `link` | warning services, partner agencies |

## Class to add: Incident update

| Attribute | Identifier | Datatype | Required | Why |
|---|---|---|---|---|
| Headline | `title` | Text line (`ezstring`) | yes | what and where |
| Status | `status` | Selection (`ezselection`) | yes | ongoing, under control, ended |
| What to do | `action` | Text block (`eztext`) | yes | instructions, plain text, works on every device |
| Details | `body` | XML block (`ezxmltext`) | no | road closures, shelters, background |
| Area | `area` | Tags (`eztags`) | no | a tag tree of areas |
| Updated | `updated` | Date and time (`ezdatetime`) | yes | shown at the top |
| Hide after | `hide_date` | Date and time | no | used by `hide.php` |

Text blocks rather than rich text for the key fields: they are quick to type on a phone and render as plain text
everywhere, including in RSS readers. Configure automatic hiding in `settings/override/content.ini.append.php`:

```ini
[HideSettings]
RootNodeList[]=<node id of the Incidents folder>
HideDateAttributeList[incident_update]=hide_date
```

Clear the INI cache afterwards (`php bin/php/ezcache.php --clear-tag=ini`); `hide.php` runs in the global cronjob part
([Cronjobs](../../../../guides/cronjobs.md)).

## Class to add: Preparedness guide

| Attribute | Identifier | Datatype |
|---|---|---|
| Title | `title` | Text line |
| In short | `summary` | Text block |
| Steps | `body` | XML block (numbered steps) |
| Checklist to print | `checklist` | Object relation to a File (an accessible PDF) |
| For whom | `audience` | Tags (households, businesses, schools, people with disabilities) |
| Next review | `review_date` | Date (`ezdate`) |

## Class to add: Station

| Attribute | Identifier | Datatype |
|---|---|---|
| Name | `name` | Text line |
| Address | `address` | Text block |
| Non-emergency contact | `contact` | Text block |
| Services | `services` | Tags |
| Image | `image` | Image (`ezimage`) |

## Design rules for incident content

| Rule | Why |
|---|---|
| Few required fields | faster publishing under pressure |
| No required image | an image slows publishing; add one later if useful |
| Dates as dates | ordering, "updated" display and automatic hiding |
| One object per incident, updated in place | one address that people share; history in the versions |
| Separate objects for separate incidents | a clear list of what is ongoing |

## Related pages

- [Emergency services](README.md)
- [Alerts, availability, maintenance mode and static cache](alerts-availability-maintenance-and-static-cache.md)
- [Choosing datatypes for your fields](../../../creators/website-creators/choosing-datatypes.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
