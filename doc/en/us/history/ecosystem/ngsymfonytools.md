# ngsymfonytools: platform repository history

The history of `ngsymfonytools`, one of the platform repositories around Exponential (group: Legacy bridge and site bundles). Read it to learn what the repository gives you, how it relates to Exponential and when it changed. The ledger records 7 changes from 2026-03-02 to 2026-04-16, all made by the se7enxweb team.

## What it is

Legacy extension that includes Twig templates and Symfony sub-requests from legacy templates.

## How it relates to Exponential

Updated to the Twig service id and an Ibexa repository interface alias.

## What a user gets

A legacy .tpl template can embed a Twig template or a Symfony route.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/ngsymfonytools
```

## Template operators

Registered in `autoloads/eztemplateautoload.php` of the extension; each is described with an example in the extension's `doc/USAGE.md`. They work only inside a project that runs the Symfony platform with the legacy bridge, not in this stand-alone installation.

| Operator | Use |
|---|---|
| `symfony_include` | Include a Twig template from a `.tpl` template; content objects and nodes in the parameters are converted to the platform value objects. |
| `symfony_render` | Render a Symfony controller (or a route) inside a legacy template. |
| `symfony_render_esi`, `symfony_render_hinclude` | Emit an ESI or Hinclude tag for a controller or URL (falls back to a plain render when no reverse proxy is detected). |
| `symfony_controller` | Names the controller to render; used as the argument of `symfony_render`. |
| `symfony_path`, `symfony_url` | Relative or absolute URL of a route, as the Twig `path` and `url` functions. |
| `symfony_is_granted` | Ask the Symfony security layer whether the current user has an attribute. |

```smarty
{symfony_include( 'NetgenTestBundle:Test:test.html.twig', hash( 'theAnswer', 42 ) )}
```

To check: read `doc/USAGE.md` and `classes/ngsymfonytools*operator.php` in the extension.

## Counts by kind

| Kind | Changes |
|---|---|
| Features | 4 |
| Behaviour and upgrade changes | 1 |
| Tooling | 1 |
| No user benefit | 1 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2026-04-05 | 4.0.0.0, 4.x | `1e1a0c6` | Added replace section to ensure clean override with dependencies. Bugfix. |

## Changes made by the se7enxweb team, by theme

### Design and templates (3)

- 2026-04-05 `979547e` feature: replace removed 'templating' service with 'twig' in symfony_include operator
- 2026-04-16 `8a0c480` feature: use Twig\Environment::class instead of 'twig' service ID
- 2026-04-16 `07b2d1c` feature: Revert: restore 'twig' string ID in include operator

### Composer requirements (1)

- 2026-04-05 `6ba9dc1` tooling: Update package name and license in composer.json

### Replace declarations for the upstream package (1)

- 2026-04-05 `1e1a0c6` bc: Added replace section to ensure clean override with dependencies. Bugfix.

### Exponential branding (1)

- 2026-04-14 `f7e6d9a` feature: class_alias shim for eZ→Ibexa Repository interface (Ibexa DXP 5.0)

Also: 1 merge or funding-metadata commits by the team (no user benefit; see the coverage file notes).

## Related pages

- [Site bundles](../../features/6.0/platform-site-bundles.md)
- [Extension page](../../features/6.0/extensions/ngsymfonytools.md)
- [Release changelog](../../changelogs/extensions/ngsymfonytools.md)
- [Package map](../../specifications/6.0/platform-package-map.md)
- [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [Ecosystem overview](../ecosystem.md)
- [Complete ledger of this repository](../ledger/ngsymfonytools.md)
- [Platform console commands](../../specifications/6.0/platform-console-commands.md)
- Platform ecosystem by month: [2026-03](months/2026-03.md), [2026-04](months/2026-04.md)
