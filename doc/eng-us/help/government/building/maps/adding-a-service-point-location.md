---
title: Adding a service point location
audience: government, editor
category: public-sector
order: 40
summary: For editors: how to give a service point, office or facility page its location with the GMap Location field, check it, and write the address so it is useful with and without a map.
keywords: service point, office location, address, GMap Location, Find address, Restore, latitude, longitude, editor, map
type: help
---
# Adding a service point location

This page is for editors who maintain pages of offices, service points or facilities whose class has a location field
(see "Map locations with ezgmaplocation"). The steps come from the extension's README.

## Contents

1. [Before you start](#before-you-start)
2. [Set the location](#set-the-location)
3. [Without a map](#without-a-map)
4. [Write the address for people](#write-the-address-for-people)
5. [Check](#check)

## Before you start

The class of the page has a field of the type **GMap Location**. Whether a map appears depends on the administrator
having set an API key; with a key, Google's map is loaded in the edit form.

## Set the location

1. Open the page and press **Edit**.
2. In the location field, type the address beneath the map.
3. Press **Find address**; the marker moves to the address and the **Latitude** and **Longitude** fields are filled.
4. If the point is not exactly right (for example the entrance), drag the marker or click the right spot on the map;
   the coordinates follow. **Restore** goes back to the values you started with.
5. **Send for publishing**.

The address is saved as you typed it. Avoid **My current location** unless you are at the place: it asks your browser
for your own position.

## Without a map

When no API key is set, the field shows the coordinates without a map. You can type latitude and longitude in decimal
degrees from another source (for example your body's address register), and the address.

## Write the address for people

The location field serves the map and the distance search; people need more. In the page's text give:

- the full postal address and the entrance;
- opening hours, accessibility of the building, public transport;
- how to contact the service.

## Check

Open the page signed out. With a key, the map shows the point; without, the coordinates and address show. On a list
of nearby service points, check the page appears for a nearby place.

## Related pages

- [Map locations with ezgmaplocation](map-locations-with-ezgmaplocation.md)
- [Editing content](../../../editing-content.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
