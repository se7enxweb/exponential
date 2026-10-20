---
title: Content model for public libraries
audience: government, website-creator, project-manager
category: public-sector
order: 30
summary: The shipped Event and Event calendar classes and their attributes, other classes that fit a library site, and recipes for Branch, Recommendation and Digital resource classes with real datatypes.
keywords: content model, library, event class, event calendar, branch class, opening hours, recommendation, reading list, digital resource, ezdatetime, ezkeyword, eztags
type: help
---
# Content model for public libraries

This page is for website creators and project managers who design the content classes of a library site. The events
classes ship with Exponential; branches and recommendations are added in the class editor
([Content classes for websites](../../../creators/website-creators/content-classes-for-websites.md)). Class
definitions are taken from the `sevenx_classes` package ([sevenx_classes](../../../site-packages/sevenx-multisite/sevenx-classes.md))
and the class view of the demonstration server.

## Contents

1. [The events classes](#the-events-classes)
2. [Other shipped classes that fit](#other-shipped-classes-that-fit)
3. [Class to add: Branch](#class-to-add-branch)
4. [Class to add: Recommendation](#class-to-add-recommendation)
5. [Class to add: Digital resource](#class-to-add-digital-resource)
6. [Extending the Event class](#extending-the-event-class)

## The events classes

**Event** (`event`), not a container:

| Attribute | Identifier | Datatype | Required |
|---|---|---|---|
| Full title | `title` | Text line (`ezstring`) | no |
| Short title | `short_title` | Text line | yes |
| Text | `text` | XML block (`ezxmltext`) | no |
| Category | `category` | Keywords (`ezkeyword`) | no |
| From Time | `from_time` | Date and time (`ezdatetime`) | yes |
| To Time | `to_time` | Date and time | no |
| Meta Data | `metadata` | Metadata (`xrowmetadata`) | no |

**Event calendar** (`event_calendar`) is a container with a Selection (`ezselection`) that chooses its view; the
ezwebin design shows the choices as a calendar (`event_view_calendar.tpl`) or a program list
(`event_view_program.tpl`). In the default package's roles, editors may create `event` below `event_calendar`
([Roles and policies of sevenx_multisite](../../../site-packages/sevenx-multisite/roles-and-policies.md)).

## Other shipped classes that fit

| Class | Identifier | Use |
|---|---|---|
| Folder | `folder` | branches folder, services, themes |
| Article | `article` | news, service explanations |
| Gallery, Image | `gallery`, `image` | photos of events and buildings |
| Link | `link` | the catalog, digital resource providers |
| Feedback form | `feedback_form` | contact, suggestions |
| Poll | `poll` | quick questions to readers |

## Class to add: Branch

| Attribute | Identifier | Datatype | Why |
|---|---|---|---|
| Name | `name` | Text line | |
| Address | `address` | Text block (`eztext`) | |
| Opening hours | `opening_hours` | XML block | a table per weekday |
| Exceptions | `closures` | XML block | holiday closures, short-notice changes |
| Facilities | `facilities` | Tags (`eztags`) | computers, printing, study rooms, children's area |
| Accessibility | `access` | XML block | entrance, toilets, hearing loop, parking |
| Phone | `phone` | Text line | |
| E-mail | `email` | Email (`ezemail`) | a branch mailbox |
| Image | `image` | Image (`ezimage`) | with alternative text |

Events relate to their branch through an `ezobjectrelation` attribute added to Event (see below), so a branch page
can list its events.

## Class to add: Recommendation

| Attribute | Identifier | Datatype | Why |
|---|---|---|---|
| Title | `title` | Text line | the theme of the list |
| Introduction | `intro` | XML block | |
| Titles | `items` | XML block or Matrix (`ezmatrix`) | title, author and a link to the catalog record |
| Audience | `audience` | Tags | children, teens, adults, easy reading |
| Language | — | the object's translations | |

A matrix gives a tidy table; rich text gives more freedom. Either way each item links to the catalog record rather
than copying it.

## Class to add: Digital resource

| Attribute | Identifier | Datatype |
|---|---|---|
| Name | `name` | Text line |
| What it offers | `description` | XML block |
| How to sign in | `access` | XML block |
| Link | `url` | URL (`ezurl`) |
| Provider's privacy notice | `privacy_url` | URL |

## Extending the Event class

Adding attributes to the shipped Event class is safe (existing events get empty values,
[Content classes for websites](../../../creators/website-creators/content-classes-for-websites.md#changing-a-class-on-a-live-site)).
Useful additions for libraries:

| Attribute | Datatype | Why |
|---|---|---|
| Branch | Object relation (`ezobjectrelation`) | the place |
| Audience | Tags or Selection | children, families, adults |
| Booking | Selection | free entry, booking required, full, cancelled |
| Booking link | URL | when bookings run elsewhere |

Change templates to show the new fields; the shipped ezwebin templates do not know them
([Template overrides](../../../creators/website-creators/template-overrides.md)).

## Related pages

- [Public libraries](README.md)
- [Events and catalogs linked rather than built](events-and-linked-catalogs.md)
- [Choosing datatypes for your fields](../../../creators/website-creators/choosing-datatypes.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
