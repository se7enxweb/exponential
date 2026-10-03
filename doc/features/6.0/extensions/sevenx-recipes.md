# sevenx-recipes: Symfony Flex recipes for the platform packages

`sevenx-recipes` is the **Symfony Flex recipe repository** of the se7enxweb platform packages. When `composer require` installs or updates a package, Flex queries this
repository's `index.json`, looks up the package's registered recipe versions, and serves the files and configuration of the matching **recipe slot** into the project:
configuration, the front controller, the web build setup, the legacy settings and the install scripts. It belongs to the platform (Symfony and Ibexa) side of the project; see the
[ecosystem overview](../../../history/ecosystem.md). Aliases: `exponential-dxp`, `exponential-platform-dxp` and `sevenx-dxp` all mean `se7enxweb/exponential-platform-dxp`.

## Recipes and slots

| Package | Slots (March to April 2026) |
|---|---|
| `se7enxweb/exponential-platform-dxp` | `1.0` (Ibexa DXP 4.6 pure), `1.2` (4.6 with the Legacy Bridge), `1.3` (Ibexa DXP 5.x pure), `1.4` (5.x with the Legacy Bridge), plus the historical `4.6.x-dev` and `5.0` |
| `se7enxweb/exponential-platform-dxp-skeleton` | `dev-master` |
| `se7enxweb/site-bundle` | `3.1` |
| `se7enxweb/tagsbundle` | `5.0` |
| `se7enxweb/ezplatform-solr-search-engine` | `3.3` |
| `se7enxweb/doctrine-dbal-schema` | `1.0` |

How Flex picks the slot: a `dev-` version with a matching `extra.branch-alias` takes the alias (for example `dev-5.x-LB` becomes `1.4.x-dev`), the result is reduced to
`MAJOR.MINOR`, and the registered slots are walked in descending order; the first one not above that version wins. The repository's `MAINTENANCE.md` holds the branch-alias map
and a table of what each slot contains; do not delete the historical slots, older projects pin to them.

## What changed

* 12 March 2026: the first recipe, for `exponential-platform-dxp`, and the `index.json` keys Flex requires (`branch`, `recipe-conflicts`, `versions`, `is_contrib`).
* 17 to 28 March: recipes for the forks of the Netgen packages (`site-bundle` and `tagsbundle`); the Ibexa product name in user-facing templates replaced by logos and
  trademarks of the project; the install welcome message rebranded; a `1.1` nexus recipe was added and then removed in favour of an install folder plus the symlink script.
* 4 to 17 April: support for the 4.6.x branch of the platform: web build fixes (Dart Sass 1.x), favicon and `.htaccess`, REST API 401, missing icons and JavaScript translation
  loading, a security configuration with the password hasher for the Ibexa user class, legacy-bridge session settings and siteaccess, route imports, and the `bin/install-legacy-links` script;
  a first purge of legacy-bridge files from the pure variants (so `1.0` and `1.3` carry none), Symfony 7 and Ibexa 5 configuration compatibility (`storage_factory_id` instead of
  `storage_id`), the clean `1.x` slots, Encore configuration for the React builds, `tsconfig.json` and `controllers.json`, the install command loading the Netgen Layouts schema
  during `exponential:install`, and legacy siteaccess ini settings.
* Releases 1.2.0 to 1.3.23 are tags in this period (12 to 17 April).

## Related

* [Chronicle](../../../history/extensions/sevenx-recipes.md) and [release notes](../../../changelogs/extensions/sevenx-recipes.md)
