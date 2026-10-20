---
title: Finding nearby service points
audience: government, website-creator, developer
category: public-sector
order: 50
summary: How a site builder lists the service points nearest to a place with the ezgmlLocationFilter of ezgmaplocation: the fetch, its distance parameter, accuracy (bounding box versus true distance), and how to offer it to visitors without a map.
keywords: nearby, distance, closest office, ezgmlLocationFilter, extended_attribute_filter, fetch content tree, sort by distance, latitude, longitude, service point finder
type: help
---
# Finding nearby service points

This page is for site builders and developers. The `ezgmaplocation` extension includes an extended attribute filter,
`ezgmlLocationFilter`, that fetches content by distance from a point and sorts it by closeness
([ezgmaplocation](../../../../features/6.0/extensions/ezgmaplocation.md)). It needs no map and no Google service: it is
a database query on stored coordinates.

## Contents

1. [The fetch](#the-fetch)
2. [Accuracy](#accuracy)
3. [Where the visitor's point comes from](#where-the-visitors-point-comes-from)
4. [Presenting the result](#presenting-the-result)

## The fetch

The README's example, adapted to service points below a folder:

```text
{def $nearby = fetch( 'content', 'tree', hash(
        'parent_node_id', <service points folder node id>,
        'limit', 5,
        'sort_by', array( 'distance', true() ),
        'class_filter_type', 'include',
        'class_filter_array', array( 'service_point' ),
        'extended_attribute_filter', hash( 'id', 'ezgmlLocationFilter',
                                           'params', hash( 'latitude', <latitude>,
                                                           'longitude', <longitude>,
                                                           'distance', 0.5 ) ) ) )}
```

## Accuracy

The distance filter uses a "bounding box" for SQL speed; `classes/ezgmllocationfilter.php` documents parameters for a
true ("arccosine") or closer to true ("pythagorean") circular filter. The sort is accurate. To show "2.5 km away",
use the `arccosine` parameter with `as_object`, `false()` to get a value to compute the distance from (README).

## Where the visitor's point comes from

| Source | Note |
|---|---|
| A list of districts or towns with their coordinates kept as content | no personal data; simple |
| A postcode the visitor types, converted with your own table | needs a postcode table you maintain |
| The browser's geolocation | asks the visitor for permission; personal data, used only in the browser unless you send it |

Avoid sending visitors' positions to third-party services; a district list is often enough.

## Presenting the result

Show a list (name, address, distance, opening hours, link), not only a map: a list works for screen readers, on
phones and without a map key.

## Related pages

- [Map locations with ezgmaplocation](map-locations-with-ezgmaplocation.md)
- [Maps and privacy](maps-and-privacy.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
