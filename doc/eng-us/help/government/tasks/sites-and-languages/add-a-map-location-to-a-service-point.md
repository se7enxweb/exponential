---
title: Add a map location to a service point
audience: government, admin, editor
category: public-sector
order: 360
summary: Task for administrators and editors: give the service point class a GMap Location field (ezgmaplocation) and set the location of an office; Google Maps is a third party and needs an API key, without which coordinates show without a map.
keywords: map location, service point, ezgmaplocation, GMap Location, Google Maps, ApiKey, coordinates, Find address
type: help
---
# Add a map location to a service point

This task is for administrators (setup) and editors (locations). The only map feature that ships is
`ezgmaplocation`, which uses Google Maps, a third party; read [Maps and privacy](../../building/maps/maps-and-privacy.md)
first. Background: [Map locations with ezgmaplocation](../../building/maps/map-locations-with-ezgmaplocation.md).

## Steps for administrators

1. Activate `ezgmaplocation`; `php bin/php/ezpgenerateautoloads.php -e`.
2. Create the table from `extension/ezgmaplocation/sql/<engine>/`.
3. Decide on the key: set `[GMapSettings] ApiKey` in `settings/override/ezgmaplocation.ini.append.php` for maps, or
   leave it empty for coordinates without any Google request. With a key and a Content Security Policy, add the
   Google hosts listed in the extension's README.
4. **Setup > Classes**, the service point class, **Edit**, **Add attribute**: **GMap Location**, identifier
   `location`, **OK**.

## Steps for editors

1. Edit the service point; in the location field type the address, press **Find address**.
2. Drag the marker or click the exact spot if needed; the **Latitude** and **Longitude** fields follow.
3. **Send for publishing**.

## Check

The page shows the map (with a key) or the coordinates and address (without). A nearby list built with the distance
filter finds the service point.

## Problems

| Problem | Fix |
|---|---|
| No map in the edit form | no key set, or the CSP blocks Google's script |
| Old addresses show `&uuml;` codes | run the extension's `decodeaddressentities.php` once after upgrading from 6.0.7 or earlier |

## Related pages

- [What exists for maps](../../building/maps/what-exists-for-maps.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
