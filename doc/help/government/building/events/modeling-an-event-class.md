---
title: Modeling an event class
audience: government, website-creator
category: public-sector
order: 30
summary: Which attributes to add to the shipped Event class for public events - place, venue accessibility, audience, booking status, booking link, organizer, tags - how to add them safely on a live site, and when to make a class of your own.
keywords: event class, class editor, add attribute, place, venue, accessibility, audience, booking status, organizer, eztags, ezobjectrelation, ezselection, ezurl, template update
type: help
---
# Modeling an event class

This page is for website creators who adapt the Event class for public-sector events. The shipped class
([What ships](what-ships.md)) has a title, text, category keywords and start and end times. Public events usually need
more structured information so it can be shown consistently and filtered.

## Contents

1. [Attributes to add](#attributes-to-add)
2. [Adding them safely](#adding-them-safely)
3. [A Place class](#a-place-class)
4. [Extend or create your own class](#extend-or-create-your-own-class)

## Attributes to add

| Attribute | Identifier | Datatype | Why |
|---|---|---|---|
| Place | `place` | Object relation (`ezobjectrelation`) to a Place object | one place, reused by many events |
| Accessibility at the event | `access` | XML block | step-free access, hearing loop, sign language, quiet space |
| Audience | `audience` | Tags (`eztags`) or Selection | residents, businesses, families |
| Topics | `tags` | Tags | replace or complement the Keywords category |
| Booking | `booking` | Selection: free entry, booking required, fully booked, cancelled, postponed | shown at the top |
| Booking link | `booking_url` | URL (`ezurl`) | when bookings run elsewhere |
| Organizer | `organizer` | Text line | a department or partner, not a person |
| Cost | `cost` | Text line | "Free" or the amount |
| Image | `image` | Object relation to an Image | optional, with alternative text |

## Adding them safely

Adding attributes to a class that has objects is safe: existing objects get empty values
([Content classes for websites](../../../creators/website-creators/content-classes-for-websites.md#changing-a-class-on-a-live-site)).

1. Make the change on a test installation first; move it with a package ([Packages](../../../../guides/packages.md)).
2. **Setup > Classes**, Content group, **Event**, **Edit**; add each attribute; set **Translatable** off for Booking,
   Booking link and Place; **OK**.
3. Update the templates (`full/event.tpl`, the calendar and program views, your lists) to show the new fields; the
   shipped ezwebin templates do not know them ([Template overrides](../../../creators/website-creators/template-overrides.md)).
4. Clear the caches: `php bin/php/ezcache.php --clear-tag=template` and `php bin/php/ezcache.php --clear-id=content`
   ([Caches](../../../../guides/caches.md)).

Large classes take a while to change; do it outside busy hours and with a backup.

## A Place class

| Attribute | Identifier | Datatype |
|---|---|---|
| Name | `name` | Text line |
| Address | `address` | Text block |
| Getting there | `directions` | XML block |
| Accessibility of the venue | `access` | XML block |
| Map location | `location` | Google Maps Location (`ezgmaplocation`), optional, a third-party service |

Events relate to a Place; a template on the Place page lists its upcoming events with a reverse relation fetch.

## Extend or create your own class

| Extend the shipped Event | Create your own class |
|---|---|
| you use the ezwebin calendar views | your design has its own templates anyway |
| the shipped fields fit | you need a different structure (for example meeting dates of a council) |
| a package import of `sevenx_classes` later should update the same class | your class must not be touched by package imports |

A class of your own works with the same fetches if it has a Date and time start attribute; adapt the class and
attribute identifiers in templates.

## Related pages

- [Events](README.md)
- [Accessible event information](accessible-event-information.md)
- [Choosing datatypes for your fields](../../../creators/website-creators/choosing-datatypes.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
