# Version preview in other view modes

Read this page if editors or approvers need to see a draft the way it will be printed or exported (a PDF layout of a
circular letter before it is published), not only in the full view of the site.

## In short

`content/versionview` shows a version in a view mode given as the unordered parameter `(view_mode)`:

```
content/versionview/<object>/<version>/<language>/(view_mode)/print
```

renders the version with `node/view/print.tpl`. Only the view modes listed in `content.ini [VersionView]
ViewModes[]` are allowed; any other answers "not available" (kernel error 3). Without the parameter the preview is
`full`, as before.

## Set it up

In `settings/override/content.ini.append.php` (or an extension's `content.ini.append.php`):

```ini
[VersionView]
ViewModes[]
ViewModes[]=full
ViewModes[]=print
```

Keep `full` in the list: it is what the preview shows without the parameter, and what the admin offers first.

`design/standard` has `node/view/print.tpl`: the name of the node and every attribute that has content, without
navigation, buttons or children. A site gives a class a print layout of its own with an override:

```ini
# design/<site design>/override.ini.append.php
[print_article]
Source=node/view/print.tpl
MatchFile=print/article.tpl
Subdir=templates
Match[class_identifier]=article
```

The view mode is the `viewmode` design key of the preview, so `Match[viewmode]` in an override works as for
`content/view`, and the template variable `view_mode` tells a `content/view/versionview.tpl` which view mode to
render.

## In the admin

With more than one view mode listed, the **View control** box of the version preview (admin, admin3 and admin4
designs) offers them next to the translation, location and siteaccess; **Update view** shows the version in the
chosen one and keeps it when another language or siteaccess is chosen. The preview frame and its full screen link
carry the view mode. With only `full` listed nothing changes on the page.

## Safety

- A view mode names a template, so only a listed one is used, and only a name of letters, digits, `_` and `-` counts
  as one, even when the setting lists something else (`Versionview::viewMode()`).
- A view mode posted with **Update view** that is not listed is ignored; the current one stays
  (`Versionview::changedViewMode()`).
- The preview needs `content/versionread`, as before; the view mode changes nothing about who may see a version.

## Site designs with their own preview template

The preview of a version in a siteaccess (`/site_access/<name>`) renders the site design's
`content/view/versionview.tpl`. `design/standard`'s template renders the node in the chosen view mode; a site design
that has its own copy (ezwebin, ezdemo and the media theme do) shows `full` until its template uses the variable
too:

```
{node_view_gui view=cond( is_set( $view_mode ), $view_mode, 'full' ) content_node=$node ...}
```

The page around the preview is the site's page layout. For a print without the site's header and menu, let the
site's print stylesheet (`@media print`) hide them, or print the preview frame on its own (its full screen link).

## How it works

`\Exponential\View\Kernel\Content\Versionview::viewMode()` decides the view mode of the address,
`::changedViewMode()` the one of the form, `::viewModes()` lists the setting. `kernel/content/module.php` declares
`(view_mode)` and the form field `SelectedViewMode` of the `ChangeSettings` action.

Tests (no database): `eZContentVersionviewViewModeTest`.

## Related pages

- [Access and view cache filters](access-and-cache-filters.md)
- [PDF export](pdf-export.md)
