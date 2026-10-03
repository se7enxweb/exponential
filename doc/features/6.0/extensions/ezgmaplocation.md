# ezgmaplocation: a map location datatype

This page is for site builders who store places on content, such as offices, events or users. `ezgmaplocation` is a
datatype that stores a latitude and longitude (decimal degrees) on an object. The editor finds the point by typing an
address under a Google map. The address is stored with the coordinates and is searchable.

## Set it up

1. Get a Google Maps key and set it in `site.ini [SiteSettings] GMapsKey=<key>` (per siteaccess if you wish).
2. Activate the extension and regenerate the extension autoloads: `php bin/php/ezpgenerateautoloads.php -e`.
3. Create the table `ezgmaplocation` from `extension/ezgmaplocation/sql/<engine>/` (a SQLite schema ships since 6.0.3).
4. Add the **Google Maps Location** datatype to a class.

## Edit a location

1. Type an address under the map and press **Find address**.
2. Press **Update Values** to take the coordinates.
3. Move the marker to refine. If you do not, the address is saved as typed.

## Example: find objects close to a point

The included filter fetches by distance, sorted by closeness:

```
{def $users_close_by = fetch( 'content', 'tree', hash(
        'parent_node_id', 12,
        'limit', 3,
        'sort_by', array( 'distance', true() ),
        'class_filter_type', 'include',
        'class_filter_array', array( 'user' ),
        'extended_attribute_filter', hash( 'id', 'ezgmlLocationFilter',
                                           'params', hash( 'latitude', 59.917,
                                                           'longitude', 10.729,
                                                           'distance', 0.5 ) ) ) )}
```

## Limits

- The distance filter uses a bounding box for SQL speed. `classes/ezgmllocationfilter.php` documents the parameters
  that give true "arccosine" or closer-to-true "pythagorean" circular accuracy. The sort is accurate.
- To show the distance ("2.5 km away"), use the `arccosine` parameter with `as_object`, `false()` to get a value from
  which to compute it.

## What changed in the Exponential 6 releases

| Version | Change |
|---|---|
| 6.0.2 | Latitude and longitude normalisation works with the stricter `implode()` handling of PHP 8.5. |
| 6.0.3 | SQLite schema for the location table; `ezinfo.php` states version, license and website. |
| 6.0.4 | Every visible text is a translation string with German; the two JavaScript alerts come from translated messages with placeholders. |
| 6.0.5, 6.0.6 | The description names Exponential; copyright notices name 1998 - 2026 7x & Exponential Foundation first. |

## Related pages

- [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
- [Chronicle](../../../history/extensions/ezgmaplocation.md) and [release notes](../../../changelogs/extensions/ezgmaplocation.md)
- [Change ledger](../../../history/ledger/ezgmaplocation.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2026-04](../../../history/extensions/months/2026-04.md), [2026-09](../../../history/extensions/months/2026-09.md) (all extensions)
