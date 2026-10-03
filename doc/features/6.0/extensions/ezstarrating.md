# ezstarrating: star ratings for content

`ezstarrating` adds a **Star Rating** datatype (`ezsrrating`): add the attribute to a content class and visitors can rate
objects of that class from 1 to 5 stars. Each user may vote once per session. Ratings are stored in the table
`ezstarrating_data`, recalculated into a per-object average, and available to templates and to an extended attribute filter
(`ezsrRatingFilter`) to sort and fetch by rating. It depends on `ezjscore` (the rating call is the ezjscore function
`ezstarrating_rate`, with `ezstarrating_user_has_rated`).

## Set it up

1. Activate the extension (`ActiveExtensions[]=ezstarrating`) and regenerate autoloads.
2. Create the rating table from `extension/ezstarrating/sql/<engine>/` (mysql, postgresql, oracle; a SQLite schema was
   added in 6.0.3).
3. Grant the roles that may vote the policy module `ezjscore`, function `call`, limitation `ezstarrating_rate`.
4. Add a **Star Rating** attribute to the class to be rated. Clear caches.

## What changed in the Exponential 6 releases

* 6.0.3 (27 September 2026): the rating classes declared **protected constructors** over the public constructor of
  `eZPersistentObject`, which PHP 8 refuses when the class is loaded; they are public again, `ezsrRatingObjectTreeNode` calls
  the persistent object constructor and the empty PHP 4 style constructors are gone. `ezsrRatingObject::stats()` kept its cache
  in a function static, which a persistent worker (Velocity) carries from one request to the next; it lives in a static class
  property now, so it stays bounded, shows new votes and never crosses databases. A SQLite schema for the rating tables was added.
* 6.0.4: the description names Exponential.
* 6.0.5 (1 October): the star rating uses jQuery 4 (`.on()` and `.off()` instead of the click shorthand and `.unbind()`).
* 6.0.6 (2 October): the YUI 3 rating script is removed. `ezsrrating.tpl` loads `ezstarrating_jquery.js` whatever
  `ezjscore.ini [...] PreferredLibrary` says, and `ezstarrating_yui3.js` is gone. See [YUI removal](../../../bc/6.0/yui-removal.md).
* 6.0.7, 6.0.8: copyright notices; English and German translations for every string the admin showed untranslated.

## Related

* [sevenx_themes_simple](sevenx_themes_simple.md): the simple theme's rating view loads the jQuery script
* [Backend ezjscore services](../../../bc/6.0/backend_ezjscore_services.md)
* [Chronicle](../../../history/extensions/ezstarrating.md) and [release notes](../../../changelogs/extensions/ezstarrating.md)
