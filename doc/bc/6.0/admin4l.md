# admin4l: the admin page is built by Exponential Layouts

Read this page if you run the admin siteaccess on `admin4l`, if you override admin4's `pagelayout.tpl` or the page
parts, or if you edit admin layouts. The admin page is no longer one template: with `SiteDesign=admin4l` the page is
resolved to an admin layout and drawn from its zones and blocks. See [the feature page](../../features/6.0/admin4l.md)
for how to use it.

## In short

| | |
|---|---|
| What changed | New design `design/admin4l` (page layout, zone template, 18 block templates). New admin layout types `admin_3col`, `admin_2col`, `admin_full` (`Group=admin`), block definitions `admin_*` in `[AdminBlockSettings]`, rule targets `module` and `module_view`, `[AdminLayoutSettings]` in `explayouts.ini`. New fetch functions `resolve_admin_layout`, `admin_layout_cache_key`, `admin_layouts_enabled` in the `explayouts` module. |
| Who is affected | Admin siteaccesses with `SiteDesign=admin4l`. Sites that override `pagelayout.tpl` or `page_*.tpl` in `admin4` get the override only when no admin layout resolves. Editors of layouts see a `group` parameter in the editor API (default `site`). |
| How to check | `php vendor/bin/phpunit --testsuite explayouts_admin`; load the admin pages and compare with `SiteDesign=admin4`. |
| How to fix | `[AdminLayoutSettings] Enabled=disabled` in the admin siteaccess's `explayouts.ini.append.php` returns every page to plain admin4 (clear the ini and template-block caches). |

## What is not the same as admin4

The markup, classes and styles are those of admin4, compared by screenshot (390 and 960 px at 2x, 1440 px at 1x, light
and dark, Chromium and Firefox) and by the page's element skeleton on the dashboard, content view, content edit,
setup, user and shop pages. The differences that exist:

- A block that errors partway keeps its partial output; only an empty zone falls back to admin4's template.
- A cache-block keyed by the layout: a design that adds its own cache block around a zone must include the admin
  generation (`fetch( 'explayouts', 'admin_layout_cache_key', hash( 'module', ..., 'view', ... ) )`) in its keys.
- The editor API lists site layouts by default; ask for `?group=admin` or `all` to see the admin ones.

## Separation from the site

Site layouts never reach the admin and admin layouts never reach the site. The site resolver skips admin layout types;
`module` and `module_view` targets never match without a request context; admin blocks are not in the site's block list.

## Reinstalling

The stock admin layouts and rules are created on this installation by an idempotent seed. They are **not** in the
installer's seed data yet: a fresh install has the layout types, blocks and settings, and the page runs as plain admin4
(nothing resolves) until the layouts are seeded.
