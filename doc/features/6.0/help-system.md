# The help system: the documentation inside the administration

Read this page if you look for help while working in the administration, if you decide who may read the help, or
if you write documentation (in `doc/` or in an extension) that should show up in it.

## In short

- **Help dashboard** (`/exphelp/dashboard`): a search field, the help for each audience (editors, administrators,
  developers, site users), the categories, featured pages, the latest changes to the documentation, and how to
  report an error.
- **Help pages** (`/exphelp/viewer/<page>`): every git-tracked Markdown and text file under `doc/`, rendered with
  headings, a table of contents, tables, callouts, highlighted code and images, with the navigation of its section,
  the previous and next page, and a link to the file on GitHub. `<page>` is the path below `doc/`
  (`/exphelp/viewer/features/6.0/help-system.md`).
- **Search** (`/exphelp/search?q=...`): finds pages by title, headings and text, with filters for audience, category
  and type. The search field of the help pages shows results while you type; press `/` to focus it.
- **Context help**: the **Help** button in the header of the administration opens the help page that belongs to the
  page you are on (the module and view), or the dashboard when there is none.
- **Report an error** (`/exphelp/report`): which tracker to use, what to write, a prefilled issue and the
  diagnostics to copy (versions, PHP, engine, design; never passwords, keys, database hosts or personal data), and
  where to report a security issue privately.

## Who may read it

The policy function `exphelp/read` opens every help view. The Administrator role has it (it holds every policy); the
Anonymous role does not, and the help is never shown to visitors who are not signed in. A new installation gives the
Editor role `exphelp/read`. On an installation from before the help system, grant it with

```sh
php bin/php/addexphelpreadpolicy.php --dry-run      # lists what it would grant
php bin/php/addexphelpreadpolicy.php                # the Editor role
php bin/php/addexphelpreadpolicy.php --role=Editor --role=5
```

(`./console exp:help-grant` runs the same command.) Roles that already read the help are left alone, so it can run
again. By hand: Roles and policies, the role, New policy, module `exphelp`, function `read`.
The Help button appears only for users who have it.

## What it shows, and what never

The help shows only files that are **tracked by git** below the directories of `exphelp.ini [IndexSettings] Roots[]`
(`doc/` by default), with a page suffix (`md`, `markdown`, `txt`, `rst`, or none) or an image suffix (`png`, `jpg`,
`jpeg`, `gif`, `webp`). Everything else is a 404: files outside `doc/`, untracked files, paths with `..`, symbolic
links, generated HTML (`doc/doxygen/`), SVG files and files over `MaxFileSize`. An installation without `.git` uses
the file manifest `share/filelist.md5` as the list of tracked files.

Markdown is rendered safely: raw HTML is escaped, links with `javascript:` and similar schemes are dropped, links to
other pages become help links, external links open with `rel="noopener"`, and images come only from `doc/` or from
`https://` addresses. The pages need no inline script or style, so they work under the administration's Content
Security Policy.

## Settings: exphelp.ini

| Setting | Default | Meaning |
|---|---|---|
| `[HelpSettings] Enabled` | `true` | `false` turns every help view into a 404 and hides the Help button |
| `[HelpSettings] EditBaseURL` | GitHub, `main` | the "Edit on GitHub" link of a page; empty for none |
| `[IndexSettings] Roots[]` | `doc` | the directories whose tracked files are shown |
| `[IndexSettings] ExtensionDocDirs[]` | empty | extensions whose `doc/` directory is shown too (opt-in) |
| `[IndexSettings] ExcludePatterns[]` | doxygen, HTML, SVG | never shown, even when tracked |
| `[AudienceSettings] PathRules[]` | per directory | `<glob>;<audience,...>`, first match wins |
| `[CategorySettings]` | eight categories | names, icons and path rules |
| `[SearchSettings]` | 20 results | result count and the weights of title, headings and text |
| `[FeaturedSettings] PageList[]` | empty | pages shown first on the dashboard |
| `[ContextHelp] Map[]` | kernel modules | `Map[<module>/<view>]=<page>#<anchor>` |
| `[IssueTrackers]` | the Exponential tracker | where errors are reported |

### Front matter

A page can name its audience, category and order in a front matter block at its top:

```markdown
---
title: Publishing content
audience: editor, admin
category: content
order: 20
---
```

Without it, `[AudienceSettings] PathRules[]` and `[CategorySettings] PathRules[]` decide.

## Help from extensions

An extension adds context help in `extension/<ext>/settings/exphelp.ini.append.php`:

```ini
[ContextHelp]
Map[mymodule]=ext/myextension/usage.md
Map[mymodule/edit]=ext/myextension/usage.md#editing
```

and, to show its own `doc/` directory, an administrator adds it to `[IndexSettings] ExtensionDocDirs[]`. Its pages
then have the ids `ext/<extension>/<path>`.

## On the command line

```bash
./console exp:help index     # rebuild the help index and the search index
./console exp:help search "publish a page"
./console exp:help check     # broken links and images in doc/
```

The index is cached in `var/<site>/cache/exphelp/` and rebuilt by itself when the tracked files change.
