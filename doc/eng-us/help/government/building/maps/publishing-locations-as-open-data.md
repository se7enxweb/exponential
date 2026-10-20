---
title: Publishing locations as open data
audience: government, admin, editor
category: public-sector
order: 70
summary: How to publish the service points or facilities kept as content in Exponential as an open dataset of locations (CSV with coordinates), since no GeoJSON or geo web service ships, and what to check before publishing.
keywords: open data, locations, coordinates, CSV, geodata, service points, facilities, xrowextract, export, GeoJSON, dataset
type: help
---
# Publishing locations as open data

This page is for administrators and data teams. When service points or facilities are kept as content with a
location field, their list is a valuable dataset. Exponential ships no GeoJSON, KML or geo web service output (see
"What exists for maps"); a CSV with coordinates, made from the content, covers most re-use.

## Contents

1. [What the dataset contains](#what-the-dataset-contains)
2. [Making the file](#making-the-file)
3. [Publishing it](#publishing-it)
4. [Checks](#checks)

## What the dataset contains

One row per location: a stable identifier (the object ID or your own code), name, address, latitude, longitude
(decimal degrees, WGS 84 as Google Maps uses), opening hours, accessibility notes, a link to the page.

## Making the file

- With `xrowextract`: **Export > One class of content export**, the class (for example `service_point`), the folder,
  CSV; preview the columns and check how the location field appears, then save the settings as a preset for the next
  update ([xrowextract](../../../../features/6.0/extensions/xrowextract.md)).
- With the kernel command: `./console exp:ezcsvexport --storage-dir=var/export/csv <node id>` writes one CSV per class;
  check the location columns before publishing ([exp:ezcsvexport](../../../../examples/commands/ezcsvexport.md)).
- A developer can also write a template view that prints the coordinates as GeoJSON; that is your own code.

## Publishing it

Publish the file as a distribution of a dataset in your catalog, with license, update frequency and the date of the
export (see the [open data section](../../open-data/README.md), "Publishing dataset files").

## Checks

- [ ] Only public locations; no addresses of individuals (for example home-based services).
- [ ] Coordinates use a dot as decimal separator and the same precision throughout.
- [ ] Every row has a stable identifier, so re-users can follow changes.
- [ ] The file is refreshed when locations change.

## Related pages

- [What exists for maps](what-exists-for-maps.md)
- [Exporting content as CSV, JSON or XML](../../open-data/exporting-content-as-csv-json-xml.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
