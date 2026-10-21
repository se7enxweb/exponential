---
title: Showing your location on a map
audience: local-business, businesses, customer, website-creator
category: business-solutions
order: 31
summary: Two ways to show a local business's location on an Exponential site - the ezgmaplocation datatype (a point stored on content, Google Maps with an API key) and the Map block of the layouts editor (Google with a key, OpenStreetMap without) - with setup steps, what needs an API key, privacy and a checklist.
keywords: map, location, ezgmaplocation, Google Maps, API key, OpenStreetMap, map block, layouts editor, latitude, longitude, directions, privacy, Content Security Policy
type: help
---
# Showing your location on a map

This page is for the owner of a local business and whoever builds the site. Exponential has two ways to put a map on
a page. Both show a point; neither is a route planner. For directions, visitors open the address in their own map app.

## Contents

1. [Two ways compared](#two-ways-compared)
2. [The ezgmaplocation datatype](#the-ezgmaplocation-datatype)
3. [The Map block of the layouts editor](#the-map-block-of-the-layouts-editor)
4. [API keys and privacy](#api-keys-and-privacy)
5. [A directions link without any key](#a-directions-link-without-any-key)
6. [Checklist](#checklist)

## Two ways compared

| | `ezgmaplocation` datatype | Map block (layouts editor) |
|---|---|---|
| What it is | a field on a content class that stores latitude, longitude and the address | a block placed on a layout page |
| Editors set the point by | typing an address and pressing **Find address**, or dragging the marker | typing latitude, longitude and a zoom level, or pasting a map embed address |
| Map provider | Google Maps | Google Maps Embed with a key, otherwise OpenStreetMap |
| Without an API key | coordinates shown, no map | OpenStreetMap map |
| Also useful for | finding objects near a point (`ezgmlLocationFilter`) | landing pages and the front page |
| Source | `extension/ezgmaplocation/`, [ezgmaplocation](../../../features/6.0/extensions/ezgmaplocation.md) | `extension/explayouts/classes/explayoutsmapblockhandler.php`, `explayouts.ini [BlockDefinition_map]` |

Both extensions are in the standard `composer.json` (`se7enxweb/ezgmaplocation`, `se7enxweb/explayouts`).

## The ezgmaplocation datatype

Setup, from `extension/ezgmaplocation/README` and [ezgmaplocation](../../../features/6.0/extensions/ezgmaplocation.md):

1. Get a Google Maps JavaScript API key (an account with Google; outside Exponential).
2. Set it in `ezgmaplocation.ini`, block `[GMapSettings]`, `ApiKey=<key>`, in `settings/override/` or per siteaccess.
3. Activate the extension, regenerate the autoloads and create the `ezgmaplocation` table (SQL files for each database
   ship in the extension, including SQLite).
4. Add a **Google Maps Location** attribute to the class that holds your address, for example the contact object.

Editing, for the owner:

1. Open the contact object for editing.
2. Type the address under the map and press **Find address**: the map centers and the latitude and longitude are
   filled in.
3. Drag the marker to the exact entrance if needed. **Restore** brings back the stored point.
4. Publish.

If the key is empty, the edit form and the page load no Google script and show the coordinates without a map
(`ezgmaplocation.ini.append.php`, comment on `ApiKey`).

## The Map block of the layouts editor

On a page built with the layouts editor ([Layouts editor](../../layouts-editor.md)), add a **Map** block and fill in:

| Parameter | Meaning |
|---|---|
| Latitude, Longitude | the point (copy them from any map app) |
| Zoom (0-20) | how much area is shown |
| Map type | Roadmap or Satellite (and Terrain, Hybrid with Google) |
| Show marker | a pin on the point |
| Map embed URL | the older way: the address of a map to embed, used when no point is given |
| Width, Height | the size |

The handler builds the map address: with `explayouts.ini [MapSettings] GoogleMapsApiKey` it uses Google's Maps Embed
API; without a key it uses an OpenStreetMap embed. The map **loads after a click** (the block template uses the shared
embed part with a "Load map" button), so visitors do not contact the map provider until they choose to.

## API keys and privacy

| Item | Needs a key? | Who holds it |
|---|---|---|
| `ezgmaplocation` map in edit form and page | yes, Google Maps JavaScript API | the business, set by the builder |
| Map block, Google | yes, `GoogleMapsApiKey` | the business |
| Map block, OpenStreetMap | no | - |

Things to know:

- A Google key belongs to an account with Google, which may bill above a free allowance. Read Google's current
  terms; this page quotes no prices.
- Restrict the key to your site's domain in the provider's console.
- A map loads content from another company. Mention it in your privacy note. The Map block's click-to-load helps.
- If your site sends a Content Security Policy, the builder must allow the map provider's hosts
  ([Content Security Policy](../../../features/6.0/content-security-policy.md)).

## A directions link without any key

The simplest, most private option: under the address, a plain link that opens the address in the visitor's map app.
An editor can add it in rich text. No key, no script, works on every phone. Many local businesses use this together
with, or instead of, an embedded map.

## Checklist

- [ ] The address is on the page as text.
- [ ] Map approach chosen: datatype, block, plain link, or a combination.
- [ ] If Google is used: key created in the business's account, restricted to the domain, set by the builder.
- [ ] The marker sits on the entrance, not the middle of the block.
- [ ] Privacy note mentions the map provider.
- [ ] Tested on a phone.

## Related pages

- [Several locations](several-locations.md)
- [Opening hours without a special datatype](opening-hours-without-a-special-datatype.md)
- [Landing pages with layouts](../../creators/website-creators/landing-pages-with-layouts.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
