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
| `[PdfSettings] Enabled` | `true` | `false` hides "Download as PDF" and makes `exphelp/pdf` a 404 |
| `[PdfSettings] PageSize` | `A4` | `A4` or `Letter` |
| `[PdfSettings] FontFiles[]` and the others | DejaVu Sans, Droid Sans Fallback | TrueType fonts of the PDF, first readable file of each list (see below) |

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

## Print and PDF

Every help page has two actions under its facts:

- **Print this page** opens the browser's print dialog (the button is shown by the help's script; without
  JavaScript the browser's own Print command does the same). On paper only the document is printed: no menus,
  tree, contents or buttons, light colors even when the administration is dark, the page's source file, last
  change and address under its title, the address of every outside link after the link, and no break inside a
  code block, table or callout.
- **Download as PDF** (`/exphelp/pdf/<page>`, policy `exphelp/read`) sends the page as a PDF file named after its
  title. It is drawn on the server by the kernel's own PDF library (`lib/ezpdf`); no outside service or program
  is used. Headings (with working links to them), paragraphs, lists, tables, code blocks (monospace), callouts,
  quotes, images from `doc/` (PNG, JPEG, GIF, WebP) and footnotes are kept; the page's license notice ends the
  file; every page has the title and "Page n of N" at its foot. Only the pages the viewer shows can be
  downloaded; anything else is a 404.

The text of the PDF is as good as its fonts. With a TrueType font (`[PdfSettings] FontFiles[]`, `BoldFontFiles[]`,
`ItalicFontFiles[]`, `BoldItalicFontFiles[]`, `MonoFontFiles[]`; DejaVu Sans of the operating system by default)
every character the font has is drawn and the text can be selected and searched; only the characters used are
embedded, so a page is some tens of kilobytes. `FallbackFontFiles[]` (Droid Sans Fallback) takes the characters the
main font lacks, such as Chinese, Japanese and Korean. Right to left text (Hebrew, Arabic) is printed right to left;
Arabic letters are drawn in their basic forms, without joining. Without any TrueType font the PDF core fonts are
used and characters outside Western European are replaced by their closest Latin letters.

PDFs are cached in `var/<site>/cache/exphelp/pdf/`, per page, index signature, address and fonts: a change to the
page or the settings makes a new file.

## On the command line

```bash
./console exp:help index     # rebuild the help index and the search index
./console exp:help search "publish a page"
./console exp:help check     # broken links and images in doc/
./console exp:help pdf help/searching.md --out=searching.pdf --base-url=https://www.example.com
```

The index is cached in `var/<site>/cache/exphelp/` and rebuilt by itself when the tracked files change.

## Languages and accessibility

The labels of the help pages (dashboard, search, page viewer, error report, the Help button) are in the
`kernel/exphelp` context of the administration translations, and every complete catalogue has them: German,
French, Spanish (Spain and Mexico), Ukrainian, Hebrew, Arabic, Latin and Klingon, with English and the
`untranslated` template kept in step. The names in `exphelp.ini` (`[CategorySettings] CategoryNames[]`, the
trackers' `Name` and `Description`) go through the same context, so a new name there needs a message in each of
those catalogues; `tests/tests/lib/ezi18n/expTranslationQualityTest.php` checks that a finished translation keeps
the placeholders (`%count`, `%title` ...) of its source. The help pages themselves are the documentation in
`doc/` and stay in the language they are written in; the diagnostics and the issue text the error report prepares
stay in English on purpose, because the maintainers read them.

In a right-to-left interface language (Hebrew, Arabic) the pages are mirrored: the navigation tree, the contents
and the previous/next links swap sides and their arrows turn, while paths, code and the diagnostics stay left to
right. Every page has one `h1`, named navigation landmarks, labels for each search field and filter, a visible
focus ring on everything that takes the keyboard, and a note on each link that opens a new window; `/` moves to
the help search, and the instant results are announced to screen readers.
