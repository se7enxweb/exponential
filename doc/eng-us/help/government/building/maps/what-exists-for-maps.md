---
title: What exists for maps
audience: government, project-manager, website-creator
category: public-sector
order: 20
summary: An honest inventory of map features for public sites built with Exponential: one map location datatype (ezgmaplocation, Google Maps), a distance filter, and nothing else; no GIS, no self-hosted map tiles, no geodata export, with the alternatives.
keywords: maps, map, location, ezgmaplocation, Google Maps, GIS, OpenStreetMap, geodata, coordinates, service points, inventory
type: help
---
# What exists for maps

This page is for managers and site builders who plan map features, for example a map of service points. It lists what
is really available, checked in the installed extensions, so you can plan the rest.

## Contents

1. [What there is](#what-there-is)
2. [What there is not](#what-there-is-not)
3. [Choosing an approach](#choosing-an-approach)

## What there is

| Feature | Where | Note |
|---|---|---|
| A location field on content (latitude, longitude, address) | the `ezgmaplocation` extension, datatype "GMap Location" | installed with Exponential, inactive until activated |
| A map in the edit form to find the point by address | same, with a Google Maps API key | Google is a third party |
| A map on the page | same, with a Google Maps API key | without a key, coordinates and address are shown without a map |
| Fetch content by distance to a point, sorted by closeness | `ezgmlLocationFilter` extended attribute filter | in templates |

Documentation: [ezgmaplocation](../../../../features/6.0/extensions/ezgmaplocation.md) and the extension's README in
`extension/ezgmaplocation/`.

## What there is not

Checked: none of these ship with Exponential or the extensions it installs.

- No GIS functions (layers, polygons, areas, routing).
- No integration of OpenStreetMap or self-hosted map tiles.
- No export of locations as GeoJSON, KML or a geo web service.
- No map of many objects at once in the shipped templates (the view shows one location per object).
- No geocoding without Google (the "Find address" button uses Google Maps).

## Choosing an approach

| Need | Approach |
|---|---|
| Show where one office is | an address in text, a link to a map service, optionally the location field |
| A list of service points near the visitor | the location field and the distance filter in a template |
| A map of all service points | your own template and a map library of your choice, fed with the coordinates of the location fields |
| Open geodata | export the coordinates (see "Publishing locations as open data") |
| Real GIS | a GIS product next to the site; link or embed it |

## Related pages

- [Map locations with ezgmaplocation](map-locations-with-ezgmaplocation.md)
- [Maps and privacy](maps-and-privacy.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
