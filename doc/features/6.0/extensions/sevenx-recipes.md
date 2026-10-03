# sevenx-recipes: Symfony Flex recipes for the platform packages

`sevenx-recipes` is the **Symfony Flex recipe repository** of the se7enxweb platform packages. When `composer require` installs or updates a package, Flex queries this
repository's `index.json`, looks up the package's registered recipe versions, and serves the files and configuration of the matching **recipe slot** into the project:
configuration, the front controller, the web build setup, the legacy settings and the install scripts. It belongs to the platform (Symfony and Ibexa) side of the project; see the
[ecosystem overview](../../../history/ecosystem.md). Aliases: `exponential-dxp`, `exponential-platform-dxp` and `sevenx-dxp` all mean `se7enxweb/exponential-platform-dxp`.

## Recipes and slots

| Package | Slots (March to April 2026) |
|---|---|
| `se7enxweb/exponential-platform-dxp` | `1.0` (Ibexa DXP 4.6 pure), `1.2` (4.6 with the Legacy Bridge), `1.3` (Ibexa DXP 5.x pure), `1.4` (5.x with the Legacy Bridge), plus the historical `4.6.x-dev` and `5.0` |
| `se7enxweb/exponential-platform-dxp-skeleton` | `dev-master` (the directory and its `.json` manifest exist, but **it is not listed under `recipes` in `index.json`**, so Flex does not serve it) |
| `se7enxweb/site-bundle` | `3.1` |
| `se7enxweb/tagsbundle` | `5.0` |
| `se7enxweb/ezplatform-solr-search-engine` | `3.3` |
| `se7enxweb/doctrine-dbal-schema` | `1.0` |

Check the table against the code at any time: open `index.json` in a clone of the repository and read the `recipes` object; the slot directories are
`se7enxweb/<package>/<slot>/`, each with a `manifest.json`, and the flattened `<package>.<slot>.json` files at the root are what `recipe_template` points to. The directory
`4.6.x-LB-dev` exists too but is not registered either.

## Use it in a project

Add the repository as a Flex endpoint next to the upstream one, in the `extra.symfony` block of the project's `composer.json`. The live platform sites of this installation do it
this way (the order matters: endpoints are asked in turn):

```json
"extra": {
    "symfony": {
        "allow-contrib": true,
        "endpoint": [
            "https://api.github.com/repos/ibexa/recipes/contents/index.json?ref=flex/main",
            "https://api.github.com/repos/se7enxweb/sevenx-recipes/contents/index.json?ref=master",
            "flex://defaults"
        ]
    }
}
```

Then `composer require se7enxweb/exponential-platform-dxp` (or `composer recipes se7enxweb/exponential-platform-dxp` to see what is installed and `composer recipes:update <package>` to
re-apply a changed recipe) copies the recipe's files. The version the project requires decides the slot, for example `dev-5.x-LB` of the platform package resolves to `1.4` through
its `branch-alias`. Which branch maps to which slot is the table in `MAINTENANCE.md`: `4.6` to `1.0`, `4.6.x-LB` to `1.2`, `master` to `1.3`, `5.x-LB` to `1.4`.
(The third endpoint line is the Flex default and is an assumption of this page: confirm it in your project with `composer config extra.symfony.endpoint`.)

Why it helps: a new project gets the working configuration of the Exponential platform (security, siteaccess, legacy settings, the web build, the legacy link script) from one
`composer require` instead of hand copying it from a reference project.

How Flex picks the slot: a `dev-` version with a matching `extra.branch-alias` takes the alias (for example `dev-5.x-LB` becomes `1.4.x-dev`), the result is reduced to
`MAJOR.MINOR`, and the registered slots are walked in descending order; the first one not above that version wins. The repository's `MAINTENANCE.md` holds the branch-alias map
and a table of what each slot contains; do not delete the historical slots, older projects pin to them.

## What changed

Note: `MAINTENANCE.md` describes a helper `php bin/build-recipe-manifest` for regenerating a slot manifest; the repository has no `bin/` directory at its root, so that step is
done by hand (or with the tool you keep next to the clone). Treat the step as stale until the script is added.

* 12 March 2026: the first recipe, for `exponential-platform-dxp`, and the `index.json` keys Flex requires (`branch`, `recipe-conflicts`, `versions`, `is_contrib`).
* 17 to 28 March: recipes for the forks of the Netgen packages (`site-bundle` and `tagsbundle`); the Ibexa product name in user-facing templates replaced by logos and
  trademarks of the project; the install welcome message rebranded; a `1.1` nexus recipe was added and then removed in favour of an install folder plus the symlink script.
* 4 to 17 April: support for the 4.6.x branch of the platform: web build fixes (Dart Sass 1.x), favicon and `.htaccess`, REST API 401, missing icons and JavaScript translation
  loading, a security configuration with the password hasher for the Ibexa user class, legacy-bridge session settings and siteaccess, route imports, and the `bin/install-legacy-links` script (present in every slot except `1.3`; check with `ls se7enxweb/exponential-platform-dxp/*/bin` in a clone);
  a purge of legacy-bridge and Netgen files and service definitions from the pure Ibexa 5 slot (`1.3` carries none; the `1.0` slot, although it is the pure 4.6 variant, still contains `bin/install-legacy-links` and `src/LegacySettings` and `src/LegacyRoot`, so do not assume it is bridge-free), Symfony 7 and Ibexa 5 configuration compatibility (`storage_factory_id` instead of
  `storage_id`), the clean `1.x` slots, Encore configuration for the React builds, `tsconfig.json` and `controllers.json`, the install command loading the Netgen Layouts schema
  during `exponential:install`, and legacy siteaccess ini settings.
* Releases 1.2.0 to 1.3.23 are tags in this period (12 to 17 April).

## Related

* [Chronicle](../../../history/extensions/sevenx-recipes.md) and [release notes](../../../changelogs/extensions/sevenx-recipes.md)
