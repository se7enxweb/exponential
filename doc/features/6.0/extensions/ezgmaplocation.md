# ezgmaplocation: a map location datatype

`ezgmaplocation` is a datatype that stores a latitude and longitude (decimal degrees) on an object and lets the editor find
the point by typing an address under a Google map. The address is stored with the coordinates and is searchable.

## Use it

1. Get a Google Maps key and put it in `site.ini [SiteSettings] GMapsKey=<key>` (per siteaccess if you wish).
2. Activate the extension, regenerate the extension autoloads, and create the table `ezgmaplocation`
   (`extension/ezgmaplocation/sql/<engine>/`; a SQLite schema ships since 6.0.3).
3. Add the **Google Maps Location** datatype to a class.
4. Editing: type an address under the map, press **Find address**, then **Update Values** to take the coordinates. Move the
   marker to refine; unless you do, the address is saved as typed.
5. Fetching by distance with the included filter, sorted by closeness to a point:

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

The distance filter uses a bounding box for SQL speed (see `classes/ezgmllocationfilter.php` for the parameters that give
true "arccosine" or closer-to-true "pythagorean" circular accuracy); the sort is accurate. Use the `arccosine` parameter with
`as_object`, `false()` to get a value from which to compute the distance to show ("2.5 km away").

## What changed in the Exponential 6 releases

* 6.0.2: latitude and longitude normalisation works with the stricter `implode()` handling of PHP 8.5.
* 6.0.3: SQLite schema for the location table; `ezinfo.php` states version, license and website.
* 6.0.4: every visible text is a translation string with German; the two JavaScript alerts come from translated messages
  with placeholders.
* 6.0.5, 6.0.6: description names Exponential; copyright notices name 1998 - 2026 7x & Exponential Foundation first.

## Related

* [Chronicle](../../../history/extensions/ezgmaplocation.md) and [release notes](../../../changelogs/extensions/ezgmaplocation.md)
* [Change ledger](../../../history/ledger/ezgmaplocation.md)
* [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
* [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
* [Month: 2026-04 (all extensions)](../../../history/extensions/months/2026-04.md)
* [Month: 2026-09 (all extensions)](../../../history/extensions/months/2026-09.md)
