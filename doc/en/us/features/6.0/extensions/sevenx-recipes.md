# sevenx-recipes: Symfony Flex recipes for the platform packages

This page is for developers who start or maintain a project on the Symfony based Exponential platform.
`sevenx-recipes` is the **Symfony Flex recipe repository** of the se7enxweb platform packages. When `composer require`
installs or updates a package, Flex reads this repository's `index.json`, looks up the package's registered recipe
versions, and copies the files and configuration of the matching **recipe slot** into the project: configuration, the
front controller, the web build setup, the legacy settings and the install scripts.

Why it helps: a new project gets the working configuration of the Exponential platform (security, siteaccess, legacy
settings, the web build, the legacy link script) from one `composer require`, instead of copying it by hand from a
reference project.

It belongs to the platform (Symfony and Ibexa) side of the project; see the
[ecosystem overview](../../../history/ecosystem.md). The aliases `exponential-dxp`, `exponential-platform-dxp` and
`sevenx-dxp` all mean `se7enxweb/exponential-platform-dxp`.

## Use it in a project

1. Add the repository as a Flex endpoint next to the upstream one, in the `extra.symfony` block of the project's
   `composer.json`. The live platform sites of this installation do it this way. The order matters: endpoints are
   asked in turn.

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

   The third endpoint line is the Flex default and is an assumption of this page. Confirm it in your project with
   `composer config extra.symfony.endpoint`.

2. Require the platform package; Flex copies the recipe's files:

```bash
composer require se7enxweb/exponential-platform-dxp
```

3. See what is installed with `composer recipes se7enxweb/exponential-platform-dxp`, and re-apply a changed recipe
   with `composer recipes:update <package>`.

The version the project requires decides the slot. For example `dev-5.x-LB` of the platform package resolves to `1.4`
through its `branch-alias`. The branch to slot map is in `MAINTENANCE.md` of the repository: `4.6` to `1.0`, `4.6.x-LB`
to `1.2`, `master` to `1.3`, `5.x-LB` to `1.4`.

**How Flex picks the slot:** a `dev-` version with a matching `extra.branch-alias` takes the alias (for example
`dev-5.x-LB` becomes `1.4.x-dev`). The result is reduced to `MAJOR.MINOR`, and the registered slots are walked in
descending order; the first one not above that version wins. `MAINTENANCE.md` holds the branch-alias map and a table of
what each slot contains. Do not delete the historical slots: older projects pin to them.

## Recipes and slots

| Package | Slots (March to April 2026) |
|---|---|
| `se7enxweb/exponential-platform-dxp` | `1.0` (Ibexa DXP 4.6 pure), `1.2` (4.6 with the Legacy Bridge), `1.3` (Ibexa DXP 5.x pure), `1.4` (5.x with the Legacy Bridge), plus the historical `4.6.x-dev` and `5.0` |
| `se7enxweb/exponential-platform-dxp-skeleton` | `dev-master` (the directory and its `.json` manifest exist, but **it is not listed under `recipes` in `index.json`**, so Flex does not serve it) |
| `se7enxweb/site-bundle` | `3.1` |
| `se7enxweb/tagsbundle` | `5.0` |
| `se7enxweb/ezplatform-solr-search-engine` | `3.3` |
| `se7enxweb/doctrine-dbal-schema` | `1.0` |

Check the table against the code at any time: open `index.json` in a clone of the repository and read the `recipes`
object. The slot directories are `se7enxweb/<package>/<slot>/`, each with a `manifest.json`; the flattened
`<package>.<slot>.json` files at the root are what `recipe_template` points to. The directory `4.6.x-LB-dev` exists too
but is not registered either.

## What a slot installs (checked in the `1.2` and `1.4` slots)

| Folder or file in the slot | What the project gets |
|---|---|
| `config/packages/*.yaml`, `config/routes/*.yaml`, `config/services.yaml` | Configuration of the platform bundles (security, caches, assets, HTTP cache, GraphQL, Solr, JWT), the routes (admin UI, REST, search, user, layouts, tags, information collection) and `ez_publish_legacy.yaml`, the legacy bridge parameters |
| `public/` | The front controllers `index.php`, `index_cluster.php` (cluster file serving) and `index_rest.php` (the legacy REST entry), `.htaccess`, `favicon.ico`, `favicon.png` |
| `src/EventListener/LegacyRequestListener.php`, `LegacyRestListener.php` | Request handling for the legacy kernel and its REST calls |
| `src/EventSubscriber/LegacyInjectedSettingsSubscriber.php` | Injects INI settings into the legacy kernel at boot, see below |
| `src/LegacySettings/` | INI files for the legacy siteaccesses `legacy_site`, `legacy_admin`, `ngadminui` and `site`, and a global `override/site.ini.append.php` |
| `src/LegacyRoot/var/site/storage/` | An empty storage folder for the legacy `var` directory |
| `src/Installer/ExponentialOssInstaller.php` | The installer class of `exponential:install`; a change on 14 April returned it to the simple empty form |
| `encore/` | The web build setup (`webpack.config.js`, `ibexa.webpack.config.manager.js`, `package.json`, `ibexa.tsconfig.json`; `tsconfig.json` from `1.4`) |
| `bin/install-legacy-links` | A script that creates the symlinks tying the legacy tree to the project (the custom `app` extension, the global override folder, the `legacy_admin` siteaccess settings and the `ngadminui` delegations); Composer runs it on install and update |

`LegacyInjectedSettingsSubscriber` injects INI settings, global or per siteaccess, from the container parameters
`app.legacy.injected_settings`, `app.legacy.injected_merge_settings`, `app.legacy.siteaccess_injected_settings` and
`app.legacy.siteaccess_injected_merge_settings` in `config/packages/ez_publish_legacy.yaml`. The subscriber and the
`legacy_site` siteaccess arrived on 4 April 2026; since 5 April the `legacy_admin` siteaccess is resolved from the
request attribute (a bug fix); the listeners and `index_rest.php` arrived on 6 April; the SiteAccess namespace moved
from `eZ` to `Ibexa` on 13 April.

To see exactly what a slot carries before you require the package, in a clone:

```bash
cat se7enxweb/exponential-platform-dxp/1.4/manifest.json    # files and where each is copied
find se7enxweb/exponential-platform-dxp/1.4 -type f | sort
```

The slots differ in the details a project must pin: `1.0` and `1.2` are the Ibexa 4.6 slots, `1.3` and `1.4` the Ibexa
5 slots (see `MAINTENANCE.md`). `1.3` has no `config/packages/ez_publish_legacy.yaml`, no `LegacySettings` and no
`bin/install-legacy-links`.

## Limits

`MAINTENANCE.md` describes a helper `php bin/build-recipe-manifest` for regenerating a slot manifest, but the repository
has no `bin/` directory at its root. Do that step by hand (or with the tool you keep next to the clone) until the
script is added.

## What changed

- **12 March 2026:** the first recipe, for `exponential-platform-dxp`, and the `index.json` keys Flex requires
  (`branch`, `recipe-conflicts`, `versions`, `is_contrib`).
- **17 to 28 March:** recipes for the forks of the Netgen packages (`site-bundle` and `tagsbundle`); the Ibexa product
  name in user-facing templates replaced by logos and trademarks of the project; the install welcome message
  rebranded; a `1.1` nexus recipe was added and then removed in favour of an install folder plus the symlink script.
- **4 to 17 April:** support for the 4.6.x branch of the platform:
  - web build fixes (Dart Sass 1.x), favicon and `.htaccess`, REST API 401, missing icons and JavaScript translation
    loading;
  - a security configuration with the password hasher for the Ibexa user class, legacy-bridge session settings and
    siteaccess, route imports;
  - the `bin/install-legacy-links` script, present in every slot except `1.3` (check with
    `ls se7enxweb/exponential-platform-dxp/*/bin` in a clone);
  - a purge of legacy-bridge and Netgen files and service definitions from the pure Ibexa 5 slot (`1.3` carries none;
    the `1.0` slot, although it is the pure 4.6 variant, still contains `bin/install-legacy-links`,
    `src/LegacySettings` and `src/LegacyRoot`, so do not assume it is bridge-free);
  - Symfony 7 and Ibexa 5 configuration compatibility (`storage_factory_id` instead of `storage_id`), the clean `1.x`
    slots, Encore configuration for the React builds, `tsconfig.json` and `controllers.json`, the install command
    loading the Netgen Layouts schema during `exponential:install`, and legacy siteaccess INI settings.
- **13 to 17 April, smaller fixes with a user-visible effect:**
  - the registered recipe versions were reordered so that Flex's `version_compare` selects `5.0` correctly (the order
    of the `recipes` list in `index.json` matters to Flex);
  - the Netgen Layouts route file of the `5.0` slot is `routing.yaml`, not `routing.yml`;
  - `imagemagick.pre_parameters` and `imagemagick.post_parameters` are declared as container parameters (an undefined
    parameter otherwise stops the container build);
  - the `4.6.x-dev` manifest was regenerated after the legacy bridge files were removed from it, and points to the
    commit that drops `ez_publish_legacy.yaml`;
  - the `1.4` slot's `site.ini.append.php` gained the translator extension and a corrected section order;
  - the `legacy_site` design settings name the right CSS and JavaScript files (`main`, not `main-un`).
- Releases 1.2.0 to 1.3.23 are tags in this period (12 to 17 April).

## Related pages

- [Platform DXP skeleton](../platform-dxp-skeleton.md)
- [Legacy bridge](../legacy-bridge.md)
- [Chronicle](../../../history/extensions/sevenx-recipes.md) and [release notes](../../../changelogs/extensions/sevenx-recipes.md)
- [Change ledger](../../../history/ledger/sevenx-recipes.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2026-03](../../../history/extensions/months/2026-03.md), [2026-04](../../../history/extensions/months/2026-04.md) (all extensions)
