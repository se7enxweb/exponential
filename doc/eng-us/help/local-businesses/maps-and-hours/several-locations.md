---
title: Several locations
audience: local-business, businesses, customer, website-creator
category: local-business
order: 34
summary: Showing two or more branches, a market stall or a mobile service on one Exponential site - one location object per address with its own hours and map point, a locations folder, a nearest-location list with the ezgmaplocation filter, and when a second site makes sense.
keywords: several locations, branches, multiple locations, market stall, mobile service, area served, locations folder, ezgmaplocation, distance filter, multisite, local business
type: help
---
# Several locations

This page is for local businesses with more than one address: a second shop, a stall at the market on Saturdays, a
practice with two rooms in different towns, or a service that comes to the customer. The rule from
[Planning your pages](../getting-started/planning-your-pages.md#write-each-fact-once) still holds: each fact is
written once, now once **per location**.

## Contents

1. [One object per location](#one-object-per-location)
2. [A locations page](#a-locations-page)
3. [Nearest location](#nearest-location)
4. [Mobile services and areas served](#mobile-services-and-areas-served)
5. [One site or several](#one-site-or-several)
6. [Checklist](#checklist)

## One object per location

The builder creates a **Location** class (or extends the opening hours class from
[Opening hours without a special datatype](opening-hours-without-a-special-datatype.md#option-2-a-small-opening-hours-class)):

| Field | Datatype | Note |
|---|---|---|
| Name | Text line | "Example Bakery Station Road" |
| Address | Text block | as on the sign |
| Phone | Text line | the location's own number |
| Map point | Google Maps Location (`ezgmaplocation`) | needs an API key for the map ([Showing your location on a map](showing-your-location-on-a-map.md)) |
| Regular hours | Matrix | one row per day |
| Notice | Text block | exceptions for this location |
| Photo | Image | the front, so people recognize it |
| Manager or contact | Text line | a role, not necessarily a person's name |

Each location then has its own page and address (`/Locations/Station-Road`), which is good for local search: one page
per address.

## A locations page

A **folder** "Locations" holding the location objects lists them automatically. The builder can show the list with
name, address, today's hours and a link to each page. On the front page, a short block "Our locations" links to it.

The Map block of the layouts editor shows one point per block; for a page with all locations on one map, the builder
writes a small template from the `ezgmaplocation` fields. That is template work, not a setting.

## Nearest location

`ezgmaplocation` ships an extended attribute filter, `ezgmlLocationFilter`, that fetches objects within a distance of
a point and sorts them by distance (`extension/ezgmaplocation/classes/ezgmllocationfilter.php`,
[ezgmaplocation](../../../features/6.0/extensions/ezgmaplocation.md#example-find-objects-close-to-a-point)). With it a
builder can make "Locations near you" from a postcode or the browser's position. This is a developer task; the
visitor's position must be asked for with consent.

## Mobile services and areas served

A plumber, a mobile hairdresser or a cleaning service has a base and an area, not a shop. Instead of a map pin:

- a page "Areas we serve" listing towns and districts in text;
- the base address only if customers may come there;
- on map platforms, a service area instead of a public address (set on the platform).

A market stall that is there on certain days: a location object with the market's address and only those days in the
hours.

## One site or several

| Situation | Recommendation |
|---|---|
| Same business, same name, two or three addresses | one site, a Locations folder |
| Different brands or very different offers per location | consider separate sites on one installation ([Adding a site](../../customers/growth/adding-a-site.md)) |
| Locations in different countries and languages | one site with languages, or one per country ([Multilingual for tourist areas](../marketing/multilingual-for-tourist-areas.md)) |

Exponential runs several sites from one installation ([Multi-site brands](../../businesses/multisite/README.md)),
but for most small businesses one site is simpler and cheaper to run.

## Checklist

- [ ] One location object per address, each with hours, phone and map point.
- [ ] Each location has its own page.
- [ ] Footer shows the main location or a link to all locations.
- [ ] Each location has its own listing on map platforms, consistent with its page.
- [ ] Holiday notices are set per location.

## Related pages

- [Showing your location on a map](showing-your-location-on-a-map.md)
- [Holiday and seasonal hours](holiday-and-seasonal-hours.md)
- [Business listings outside Exponential](../getting-found/business-listings-outside-exponential.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
