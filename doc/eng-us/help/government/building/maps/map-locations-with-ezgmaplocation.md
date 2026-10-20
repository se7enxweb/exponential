---
title: Map locations with ezgmaplocation
audience: government, admin, website-creator
category: public-sector
order: 30
summary: How to set up the ezgmaplocation extension so a content class (for example a service point) gets a location field: activation, the database table per engine, the API key setting, the Content Security Policy, and adding the field to a class.
keywords: ezgmaplocation, GMap Location, location field, latitude, longitude, ApiKey, GMapSettings, Google Maps key, Content Security Policy, class editor, service point
type: help
---
# Map locations with ezgmaplocation

This page is for administrators and site builders. `ezgmaplocation` is a datatype that stores a latitude and
longitude (decimal degrees) and an address on an object; editors find the point by typing an address under a Google
map ([ezgmaplocation](../../../../features/6.0/extensions/ezgmaplocation.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Activate and create the table](#activate-and-create-the-table)
3. [The API key](#the-api-key)
4. [Content Security Policy](#content-security-policy)
5. [Add the field to a class](#add-the-field-to-a-class)
6. [Upgrading from 6.0.7 or earlier](#upgrading-from-607-or-earlier)

## Before you start

Read "Maps and privacy": with a key, the edit form and the page load Google's script, which is a third party.

## Activate and create the table

1. Activate the extension (**Setup > Extensions**, or `ActiveExtensions[]`) and regenerate the extension autoloads:
   `php bin/php/ezpgenerateautoloads.php -e`.
2. Create the table `ezgmaplocation` from `extension/ezgmaplocation/sql/<engine>/` (`mysql`, `postgresql`, `oracle`,
   `sqlite`); a `share/db_schema.dba` ships too.

## The API key

The extension's README (the current source) says: add your Google Maps JavaScript API key to `ezgmaplocation.ini
[GMapSettings] ApiKey` in `settings/override/ezgmaplocation.ini.append.php`, or per siteaccess. The old `site.ini
[SiteSettings] GMapsKey` is not read any more (the 6.0 feature page still names it). Without a key the edit form and
the view load no Google script and show the coordinates without a map.

## Content Security Policy

A site or administration that sends a Content Security Policy (`site.ini [ContentSecurityPolicy]`) must allow
Google's hosts; the README lists the `AdditionalSources[]` lines for `script-src`, `img-src`, `connect-src`,
`font-src`, `style-src`, `frame-src` and `worker-src`. Without a key nothing has to be allowed
([Content Security Policy](../../../../features/6.0/content-security-policy.md)).

## Add the field to a class

1. **Setup > Classes**, the class (for example "Service point"), **Edit**.
2. Under **Add attribute** choose **GMap Location**, press **Add**, name it "Location", identifier `location`.
3. **OK**.

Editors then type an address and press **Find address**; the map moves the marker there and fills the **Latitude**
and **Longitude** fields, which a click on the map or dragging the marker also update. **Restore** returns to the
values the page was opened with. (The README and the 6.0 feature page still name an "Update Values" button; the
current edit template, `design/standard/templates/content/datatype/edit/ezgmaplocation.tpl`, has none.) The
address is searchable.

## Upgrading from 6.0.7 or earlier

Earlier versions stored the address with HTML entities. The README asks to decode the stored rows once per
database before editors work with the new version: `php extension/ezgmaplocation/bin/php/decodeaddressentities.php -s
<siteaccess>` (a dry run that lists the rows), then the same with `--apply`. Run it once only.

## Related pages

- [What exists for maps](what-exists-for-maps.md)
- [Adding a service point location](adding-a-service-point-location.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
