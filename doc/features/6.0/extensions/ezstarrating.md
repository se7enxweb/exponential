# ezstarrating: star ratings for content

This page is for site builders who let visitors rate content. `ezstarrating` adds a **Star Rating** datatype
(`ezsrrating`). Add the attribute to a content class, and visitors can rate objects of that class from 1 to 5 stars.

- Each user may vote once per session.
- Ratings are stored in the table `ezstarrating_data` and recalculated into a per-object average.
- Templates can show the rating, and the extended attribute filter `ezsrRatingFilter` sorts and fetches by rating.
- It depends on `ezjscore`: the rating call is the ezjscore function `ezstarrating_rate`, with
  `ezstarrating_user_has_rated`.

## Set it up

1. Activate the extension (`ActiveExtensions[]=ezstarrating`) and regenerate autoloads:
   `php bin/php/ezpgenerateautoloads.php -e`.
2. Create the rating table from `extension/ezstarrating/sql/<engine>/` (mysql, postgresql, oracle; a SQLite schema
   was added in 6.0.3).
3. Grant the roles that may vote the policy module `ezjscore`, function `call`, limitation `ezstarrating_rate`.
4. Add a **Star Rating** attribute to the class to be rated.
5. Clear the caches and open an object of that class: the stars appear.

## What changed in the Exponential 6 releases

| Version | Date | Change |
|---|---|---|
| 6.0.3 | 27 September 2026 | See below. A SQLite schema for the rating tables was added. |
| 6.0.4 | | The description names Exponential. |
| 6.0.5 | 1 October 2026 | The star rating uses jQuery 4 (`.on()` and `.off()` instead of the click shorthand and `.unbind()`). |
| 6.0.6 | 2 October 2026 | The YUI 3 rating script is removed. `ezsrrating.tpl` loads `ezstarrating_jquery.js` whatever `ezjscore.ini [...] PreferredLibrary` says, and `ezstarrating_yui3.js` is gone. See [YUI removal](../../../bc/6.0/yui-removal.md). |
| 6.0.7, 6.0.8 | | Copyright notices; English and German translations for every string the admin showed untranslated. |

About 6.0.3:

- The rating classes declared **protected constructors** over the public constructor of `eZPersistentObject`, which
  PHP 8 refuses when the class is loaded. They are public again; `ezsrRatingObjectTreeNode` calls the persistent
  object constructor, and the empty PHP 4 style constructors are gone.
- `ezsrRatingObject::stats()` kept its cache in a function static, which a persistent worker (Velocity) carries from
  one request to the next. It now lives in a static class property, so it stays bounded, shows new votes and never
  crosses databases.

## Related pages

- [sevenx_themes_simple](sevenx_themes_simple.md): the simple theme's rating view loads the jQuery script
- [Backend ezjscore services](../../../bc/6.0/backend_ezjscore_services.md)
- [Velocity engines](../../../bc/6.0/velocity-engines.md)
- [Chronicle](../../../history/extensions/ezstarrating.md) and [release notes](../../../changelogs/extensions/ezstarrating.md)
- [Change ledger](../../../history/ledger/ezstarrating.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2026-09](../../../history/extensions/months/2026-09.md), [2026-10](../../../history/extensions/months/2026-10.md) (all extensions)
