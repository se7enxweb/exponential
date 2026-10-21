---
title: "Maps and hours: where and when you are open"
audience: local-business, businesses, customer
category: local-business
order: 30
summary: The pages for showing a local business's location and opening hours on an Exponential site - the ezgmaplocation datatype and the layouts map block, API keys and privacy, modeling opening hours without a special datatype, holiday and seasonal hours, and several locations.
keywords: map, location, ezgmaplocation, map block, Google Maps, OpenStreetMap, API key, opening hours, holiday hours, seasonal hours, several locations, branches
type: help
---
# Maps and hours: where and when you are open

This group of pages is for local businesses that want visitors to see at a glance **where** they are and **when**
they are open. Both look simple and both have a catch: maps come from an outside provider, and Exponential has no
opening-hours field type.

## Contents

1. [The pages in this group](#the-pages-in-this-group)
2. [What was checked](#what-was-checked)
3. [The short version](#the-short-version)

## The pages in this group

| Page | One line |
|---|---|
| [Showing your location on a map](showing-your-location-on-a-map.md) | the `ezgmaplocation` datatype, the layouts map block, API keys and privacy |
| [Opening hours without a special datatype](opening-hours-without-a-special-datatype.md) | three ways to model opening hours with what ships |
| [Holiday and seasonal hours](holiday-and-seasonal-hours.md) | editing exceptions by hand, scheduling notices, keeping platforms in step |
| [Several locations](several-locations.md) | branches, stalls and mobile services |

## What was checked

On 20 October 2026, in the repository and the extensions of the standard distribution:

| Question | Finding | Source |
|---|---|---|
| Is there a map datatype? | yes, `ezgmaplocation` stores latitude, longitude and the address | `extension/ezgmaplocation/README`, [ezgmaplocation](../../../features/6.0/extensions/ezgmaplocation.md) |
| Does it need an API key? | yes, a Google Maps JavaScript API key for the map; without one the coordinates are shown without a map | `ezgmaplocation.ini [GMapSettings] ApiKey` |
| Is there a map block for landing pages? | yes, the **Map** block of the layouts editor; Google with a key, otherwise OpenStreetMap | `extension/explayouts/classes/explayoutsmapblockhandler.php` |
| Is there an opening-hours datatype? | no; the kernel datatypes are listed in `kernel/classes/datatypes/` and none of the extensions adds one | [Choosing datatypes](../../creators/website-creators/choosing-datatypes.md) |
| Are holiday hours automatic? | no; they are edited by hand, optionally with scheduled publishing | [Scheduled publishing](../../customers/day-to-day/scheduled-publishing.md) |

## The short version

- Put your address as **text** first. A map is a bonus; the text address is what people copy into their phone.
- Choose a map with or without a Google key ([Showing your location on a map](showing-your-location-on-a-map.md)).
- Keep opening hours in **one** object and show it everywhere
  ([Opening hours without a special datatype](opening-hours-without-a-special-datatype.md)).
- Plan holiday hours for the year in January ([Holiday and seasonal hours](holiday-and-seasonal-hours.md)).

## Related pages

- [Local businesses: start here](../local-businesses-start-here.md)
- [Opening hours content template](../templates/opening-hours-content-template.md)
- [Business listings outside Exponential](../getting-found/business-listings-outside-exponential.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
