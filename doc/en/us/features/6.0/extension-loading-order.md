# Setup > Extensions: loading order and safe saving

This page is for administrators who activate extensions or change which one wins. The order of `ActiveExtensions`
decides which extension's settings, templates and design files win when two extensions provide the same thing. Until
28 September 2026 you could only change it by editing `settings/override/site.ini.append.php` by hand, and saving the
Extensions page could silently switch extensions off. Both are fixed.

> **Since 6 October 2026** there is no separate Loading order card: the order is changed in the one list of the page,
> together with activation and deactivation, and written only after a review. The save now edits the file as text, so
> comments in it survive. With `ExtensionOrdering=enabled` the declared dependencies decide most of the real loading
> order; each card shows where an extension really loads. See [The Extensions page](../../guides/extensions-page.md).
> The steps below describe the page before that change.

## Change the loading order

1. Open **Setup > Extensions** (`/setup/extensions`).
2. Open the **Loading order** card. It is hidden by default. Opening or closing it with its arrow is remembered as the
   user preference `admin_extensions_loading_order`, across reloads, sessions and browsers. The heading shows how many
   extensions are active even while it is hidden.
3. The card lists every active extension (not a page of them) with its position. Drag an extension to a new place, or
   move it with its arrows from the keyboard or a touch screen. Every change is saved at once.
4. Read the message: saved (with the path of the backup copy), or not changed and why.
5. If you moved a theme extension, check a page of the site. When two extensions override the same template, the
   order decides which one shows.

The table has an **Order** column, sortable like the others, with badges for active, siteaccess-only and inactive
extensions, and a filter for the rows on the page. A summary above counts active, siteaccess and inactive extensions.
The info card shows the position as well. Without sort parameters, the list is sorted by loading order (the same as
`/(sort)/order/(dir)/asc`); older `SortBy` links still work.

## What a save does

The save goes through `ezpActiveExtensions`, which:

- reads `settings/override/site.ini.append.php` from disk (never a cached or kept INI instance);
- changes only `[ExtensionSettings] ActiveExtensions`, and never moves `ActiveAccessExtensions` into it;
- keeps a copy in `var/<site>/backups/settings-override` before writing, reads the file again afterwards, and puts the
  copy back if anything else changed;
- writes duplicate entries once, in the order the kernel reads them;
- accepts a reorder (`ezpActiveExtensions::reorder()`) only for exactly the extensions that are active now. A page
  loaded before the list changed elsewhere is refused and shows the order on disk. A reorder never switches an
  extension on or off;
- clears the INI, template override, design base and active extensions caches afterwards, because they are built on
  the order.

Autoloads are regenerated only after a verified save.

## Saving one page no longer switches off the rest

The extension list is paged, but saving used to treat every active extension that was not posted as checked as
switched off. Saving one page therefore switched off every active extension on the other pages: enabling one extension
left a site with a sixth of its extensions, answering 500. The form now sends the extensions it shows; only those can
be switched off, and the ones on other pages keep their state. Verified by switching one extension on from the third
page: the other 51 stayed unchanged and in order.

## Let one siteaccess borrow another's extension settings

Extensions ship settings per siteaccess name (`extension/<ext>/settings/siteaccess/<name>`, which also switches on the
extension's `settings/override`). A translation siteaccess made from the user siteaccess has a name no extension knows,
so it ran without the theme's template overrides, menus and `PathPrefix`, and its pages failed or answered 404. Fix it
in the translation siteaccess's own `site.ini.append.php`:

```ini
[SiteAccessSettings]
ExtensionSettingsSiteAccess=site
```

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/siteaccess/<name>/site.ini.append.php` | `SiteAccessSettings` | `ExtensionSettingsSiteAccess` | unset | that siteaccess only |

The named siteaccess's extension settings are placed below the ones an extension ships for the siteaccess itself. The
key is read only from the siteaccess's own `site.ini.append.php`, because extension settings are placed before
siteaccess settings are merged.

## Related pages

- [Extension module override](extension-module-override.md), [multi-site INI overrides](multi-site-ini-overrides.md)
- [Template editor: create, order and edit overrides](template-editor-overrides.md), [setup wizard and editor siteaccess](setup-wizard-and-editor-siteaccess.md)
- [Extension list: sort, inspect and download any extension](extension-list-and-downloads.md), [the default extension distribution](default-extension-distribution.md), [extensions, themes and packages](extensions/README.md)
- [Extension metadata: `ezinfo.php` and `extension.xml`](../../specifications/6.0/extension-metadata.md)
- [Behaviour changes, 16 to 30 September 2026](../../bc/6.0/behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
- History: [16 to 30 September 2026](../../history/2026/2026-09b.md), [June 2024](../../history/2024/2024-06.md), [March 2024](../../history/2024/2024-03.md), [February 2024](../../history/2024/2024-02.md), [January 2024, second half](../../history/2024/2024-01b.md), [January 2024, first half](../../history/2024/2024-01a.md)
