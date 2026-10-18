# The help system: the documentation inside the administration

Read this page if you look for help while working in the administration, if you decide who may read the help, or
if you write documentation (in `doc/` or in an extension) that should show up in it.

## In short

- **Help dashboard** (`/exphelp/dashboard`): a search field, the help for each audience (editors, administrators,
  developers, site users, project managers, sales & marketing, partners, advocates, designers, hosting companies, application creators, website creators, supporters and sponsors; the project-manager and sales-marketing cards link their start page), the categories, featured pages, the latest changes to the documentation, and how to
  report an error.
- **Help pages** (`/exphelp/viewer/<page>`): every git-tracked Markdown and text file under `doc/`, rendered with
  headings, a table of contents, tables, callouts, highlighted code and images, with the navigation of its section,
  the previous and next page, and a link to the file on GitHub. `<page>` is the path below `doc/`
  (`/exphelp/viewer/features/6.0/help-system.md`).
- **Search** (`/exphelp/search/(q)/<words>`, see "Finding a page"): finds pages by title, headings and text, with filters for audience, category
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
| `[AudienceSettings] Audiences[]` | `editor`, `admin`, `developer`, `user`, `project-manager`, `sales-marketing`, `partner`, `advocate`, `designer`, `hosting`, `app-creator`, `website-creator`, `supporter`, `sponsor` | the audiences in dashboard order, with `AudienceNames[]` and `AudienceIcons[]` |
| `[AudienceSettings] PathRules[]` | per directory | `<glob>;<audience,...>`, first match wins |
| `[CategorySettings]` | eight categories | names, icons and path rules |
| `[SearchSettings]` | 20 results | result count and the weights of title, headings and text |
| `[FeaturedSettings] PageList[]` | six help pages | the "Featured guides" of the dashboard |
| `[ContextHelp] Map[]` | kernel modules | `Map[<module>/<view>]=<page>#<anchor>` |
| `[IssueTrackers]` | the Exponential tracker | where errors are reported |
| `[NavigationSettings] FolderNames[]` | built-in names | `FolderNames[<path>]=<name>`: the name of a folder in the page tree |
| `[ContextHelp] MenuTopics`, `MenuLinks[]` | 6, five pages | the related topics and the quick links of the header's Help menu |
| `[License]` | GFDL 1.3 or later | the documentation license and its notice; `Enabled=false` hides it |
| `[PdfSettings] Enabled` | `true` | `false` hides "Download as PDF" and makes `exphelp/pdf` a 404 |
| `[PdfSettings] PageSize` | `A4` | `A4` or `Letter` |
| `[PdfSettings] FontFiles[]` and the others | DejaVu Sans, Droid Sans Fallback | TrueType fonts of the PDF, first readable file of each list (see below) |
| `[TitleSettings] Titles[]`, `Summaries[]`, `TitleRules[]` | about 50 inherited pages | a clearer title and summary for a page whose own is unclear (see below) |

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


### Titles of unclear pages

Plain text files have no front matter, and many of the inherited ones begin with a line that makes a poor title: a
source code comment (`doc/standards/header.txt` began with `//`), a bare version (`5.90`), the word `CHANGELOG`, or
the same title as another page. Such files keep their names, because other files refer to them; the help gives them a
clearer title in `[TitleSettings]` instead:

```ini
[TitleSettings]
Titles[doc/standards/header.txt]=Source file header template (inherited, 4.1)
Summaries[doc/standards/header.txt]=The copyright and license header that every PHP file of the inherited 4.x code base started with ...
TitleRules[]=doc/changelogs/*/CHANGELOG-desc-*;Release {fileversion} summary: {title}
TitleRules[]=doc/features/*/event.*;{title} ({dirversion})
```

`Titles[]` and `Summaries[]` are keyed by the path relative to the installation root. A `TitleRules[]` entry is
`<pattern>;<template>` (first match wins, only for pages without a `Titles[]` entry); the template may use `{title}`
(the page's own title), `{name}` (its file name), `{fileversion}` (the version at the end of the file name) and
`{dirversion}` (the nearest version directory). A front matter `title:` or `summary:` always wins. To find unclear
titles, list every page with `./console exp:help index --list` and look for file names, `README`, versions and
duplicates.

## Help from extensions

An extension adds context help in `extension/<ext>/settings/exphelp.ini.append.php`:

```ini
[ContextHelp]
Map[mymodule]=ext/myextension/usage.md
Map[mymodule/edit]=ext/myextension/usage.md#editing
```

and, to show its own `doc/` directory, an administrator adds it to `[IndexSettings] ExtensionDocDirs[]`. Its pages
then have the ids `ext/<extension>/<path>`.

## Finding a page

- **All pages** (`/exphelp/contents`): every help page as one tree, all folders open, with a filter field that
  narrows the tree to the pages whose title or folder has every typed word. The viewer shows the same tree at the
  side of each page, with the folders on the way to the page open. Folders are native disclosure elements, so the
  tree works without JavaScript; a folder's `README.md` or `index.md` is its "Overview" link. A folder is named by
  `exphelp.ini [NavigationSettings] FolderNames[<path>]`, else by the title of its README, else by its directory
  name. The tree is cached per index, siteaccess and language, so a page with 1 200 entries costs one file read.
- **Search and browse addresses** use view parameters, so they can be bookmarked and linked:
  `/exphelp/search/(q)/publish`, `/exphelp/search/(category)/layouts/(audience)/editor`, with `(type)`,
  `(offset)` and `(limit)` as well. Without `(q)` the page lists every page the filters allow (browse mode); with no
  parameter at all it lists all pages. The dashboard's audience cards, topics and chips, the category and audience
  of a page and the search forms all link to these addresses. An old address with `?q=...&category=...` is
  redirected to its canonical form; the JSON answer for the instant search (`format=json` or
  `Accept: application/json`) accepts both forms and is never redirected.
- **Help menu**: the "?" in the header of admin4 and admin4l (and of the classic admin) opens a menu with a search
  field, "Help for this page", up to `[ContextHelp] MenuTopics` related topics, the links of
  `[ContextHelp] MenuLinks[]` and "Open the Help center". Without JavaScript the "?" is a link to the help for the
  current page. The Help center is also linked from the user menu, the footer, the dashboard and the Setup overview.

## Record pages

The release and branch history kept from the original SVN repository (`doc/release-overview`,
`doc/branch-overview`) are shown as tables per series by `expHelpRecordRenderer`: version, name, date, revisions,
addresses and notes, with a filter field over all rows and sort buttons in the column headers (without the script
the tables are complete and in date order). Any plain text file written in the same "name line, then `- Key=value`
lines" shape is shown the same way. Addresses on the old SVN hosts are shown as text marked "historical", never as
links. These pages rank in the search like the pages of the old versions they describe.

## The documentation license

The documentation in `doc/` is free documentation under the GNU Free Documentation License 1.3 or later
(`doc/LICENSE-GFDL-1.3.txt`, `doc/README-LICENSE.md`, and for readers `doc/help/free-documentation.md`). Every help
page, its print view and its PDF end with the license notice, and the dashboard has a "Free documentation" block.
This covers all 6.x documentation, including pages contributed through pull requests; the pages inherited from
earlier versions keep their own notice (`exphelp.ini [License] OtherLicensePathRules[]`). `[License] Enabled=false`
hides the notices.

## Related

- `doc/help/develop-with-git.md`: working from a Git clone with its history, and growing from a user to a developer.
- `doc/specifications/6.0/help-system.md` and `doc/specifications/6.0/help-style-guide.md`: the specification of the
  help as content (help pages edited in the content tree) and the style guide for writing help pages.
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
