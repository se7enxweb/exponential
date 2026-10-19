---
title: The help system for extensions
audience: developer
category: extension-development
order: 360
summary: Putting an extension's documentation into the admin help: ExtensionDocDirs, front matter and audience ids, folder READMEs, context help with Map[] and providers, and exp:exphelp check.
keywords: help system, exphelp, ExtensionDocDirs, front matter, audience, category, summary, featured, hidden, ContextHelp, Map, Providers, expHelpContextProviderInterface, exp:exphelp check
type: help
---
# The help system for extensions

Part 8, reference 32. The administration's help center shows the documentation of the kernel and, when the
installation opts in, of extensions. This page collects the settings and file conventions in one place. The
explanation is in [Documenting your extension](../part-3-extension-developer/documenting-your-extension.md), the
tutorial in [Help pages for your extension, by hand](../part-3-extension-developer/tutorials/by-hand-help-pages-for-your-extension.md),
and the full description in [The help system](../../../features/6.0/help-system.md) and its
[specification](../../../specifications/6.0/help-system.md).

## Serving your doc folder

```ini
[IndexSettings]
ExtensionDocDirs[]=myext
```

in the installation's `settings/exphelp.ini.append.php` (or your extension's). The help then serves the **Git-tracked**
Markdown files of `extension/myext/doc/`, with page ids `ext/myext/<path below doc>`. Untracked files are not shown.

## Front matter

```text
---
title: My extension: settings
audience: developer, admin
category: development
order: 10
summary: One sentence, at most 220 characters.
keywords: myext, settings
type: help
---
# My extension: settings
```

| Key | Meaning |
|---|---|
| `title` | the page title (quote it if it contains a colon followed by a space) |
| `audience` | one or more audience ids, comma separated |
| `category` | one category id |
| `order` | the position among its siblings |
| `summary` | shown in lists and search; cut at 220 characters |
| `keywords` | extra search words |
| `type` | `help`, or one of the documentation types (`reference`, `bc`, `install`, `specification` ...) |
| `featured: true` | put the page on the dashboard |
| `hidden: true` | serve it, but keep it out of lists and search |

Audience ids (`exphelp.ini [AudienceSettings] Audiences[]`): `editor`, `admin`, `developer`, `user`,
`project-manager`, `sales-marketing`, `partner`, `advocate`, `designer`, `hosting`, `app-creator`, `website-creator`,
`supporter`, `sponsor`.

## Folders

A folder's `README.md` is its index page: its title and order are the folder's in the navigation. Link every page
from its folder's README, so nothing is orphaned.

## Context help

The header's help button opens help for the current page:

```ini
[ContextHelp]
Map[myext]=ext/myext/README.md
Map[myext/settings]=ext/myext/settings.md#clearing-the-cache
Providers[]=myExtHelpContextProvider
```

Keys are `module`, `module/view`, or with a design prefix (`admin4l:myext/settings`). A provider implements
`expHelpContextProviderInterface` (`kernel/classes/help/exphelpcontextproviderinterface.php`):
`helpContext( $module, $view, $design )` returns `"path[#anchor]"` or `null`.

## Writing rules the help enforces

- Markdown only: raw HTML is escaped.
- Relative links must point at files that exist; anchors at headings that exist.
- Images in the same folder tree.

## Checking

```bash
./console exp:exphelp check
```

reports broken links, missing anchors and front matter problems, with errors and warnings. Run it before every
release of your documentation.

## Related pages

- Previous: [admin4 components and tokens](admin4-components-and-tokens.md)
- Next: [expservices: remote services for extensions](expservices.md)
- [The help system](../../../features/6.0/help-system.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
