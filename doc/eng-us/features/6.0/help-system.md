# The help system: the documentation inside the administration

Read this page if you look for help while working in the administration, if you decide who may read the help, or
if you write documentation (in `doc/` or in an extension) that should show up in it.

## In short

- **Help dashboard** (`/exphelp/dashboard`): the Exponential logo above "Help center" (the header's drawing,
  `exphelp/parts/dashboard_brand.tpl`, per design), a search field, the help for each audience (editors, administrators,
  developers, site users, project managers, sales & marketing, partners, advocates, designers, hosting companies, application creators, website creators, supporters, sponsors, trainers, press, media and influencers, businesses, governments and customers; the project-manager and sales-marketing cards link their start page), the categories, featured pages, the latest changes to the documentation, and how to
  report an error.
- **Help pages** (`/exphelp/viewer/<page>`): every git-tracked Markdown and text file under `doc/`, rendered with
  headings, a table of contents, tables, callouts, highlighted code and images, with the navigation of its section,
  the previous and next page, and a link to the file on GitHub. `<page>` is the path below `doc/`
  (`/exphelp/viewer/features/6.0/help-system.md`).
- **Search** (`/exphelp/search/(q)/<words>`, see "Finding a page"): finds pages by title, headings and text, with filters for audience, category
  and type. The search field of the help pages shows results while you type; press `/` to focus it.
- **User manual** (`/exphelp/viewer/help/manual/README.md`, the category **User manual**): one page per question about
  the work in the Admin 4 administration (the tabs, the daily tasks, the tasks of administrators and site managers),
  each with who may do it, the steps, what can go wrong and the related pages. Exponential Intelligence has its own
  manual in the same form among the extension documentation (`/exphelp/viewer/ext/expai/manual/README.md`).
- **Extensions** (`/exphelp/extensions`, see "Documentation of the extensions"): the documentation every active
  extension brings with it (its `doc/` directory, README, CHANGELOG, LICENSE ...), in the same viewer and search, with
  an index per extension.
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
`jpeg`, `gif`, `webp`), and the tracked project files outside `doc/` (`README.md`, `SECURITY.md` ..., see "Project
files"). Everything else is a 404: other files outside `doc/`, untracked files, paths with `..`, symbolic
links, generated HTML (`doc/eng-us/doxygen/`), SVG files and files over `MaxFileSize`. An installation without `.git` uses
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
| `[IndexSettings] ActiveExtensionDocs` | `enabled` | the documentation of every active extension is shown too ("Documentation of the extensions") |
| `[IndexSettings] ExtensionTopFiles[]` | `README*`, `CHANGELOG*`, `LICENSE*`, `INSTALL*`, `USAGE*`, `COPYING*` | the files at the top of an extension's directory shown besides its `doc/` |
| `[IndexSettings] ExtensionDocDirs[]` | empty | extensions whose documentation is shown whether they are active or not |
| `[IndexSettings] ExcludePatterns[]` | doxygen, HTML, SVG | never shown, even when tracked |
| `[ProjectFiles] Enabled`, `Include[]`, `Exclude[]` | `enabled`, the root `*.md` and the README.md of `bin/`, `design/`, `kernel/` | the tracked Markdown files outside `doc/` that are shown as project files (see "Project files") |
| `[AudienceSettings] Audiences[]` | `editor`, `admin`, `developer`, `user`, `project-manager`, `sales-marketing`, `partner`, `advocate`, `designer`, `hosting`, `app-creator`, `website-creator`, `supporter`, `sponsor`, `trainer`, `press`, `businesses`, `government`, `customer` | the audiences in dashboard order, with `AudienceNames[]` and `AudienceIcons[]` |
| `[AudienceSettings] PathRules[]` | per directory | `<glob>;<audience,...>`, first match wins |
| `[CategorySettings]` | eight categories | names, icons and path rules |
| `[SearchSettings]` | 20 results | result count and the weights of title, headings and text |
| `[SearchSettings] AudienceBoost[]` | `trainer` 0.6 | a factor for the pages of an audience (the lowest of a page's audiences), not applied while the search is filtered to that audience: the trainers' lessons rank below the editor and administrator pages they teach |
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
| `[TreeSettings] PageOrder[]` | the top-level pages | `PageOrder[<page id>]=<integer>`: the place of a page in the page tree, the folder lists and the category lists; wins over the front matter `order` (see "Order of the pages") |

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
source code comment (`doc/eng-us/standards/header.txt` began with `//`), a bare version (`5.90`), the word `CHANGELOG`, or
the same title as another page. Such files keep their names, because other files refer to them; the help gives them a
clearer title in `[TitleSettings]` instead:

```ini
[TitleSettings]
Titles[doc/eng-us/standards/header.txt]=Source file header template (inherited, 4.1)
Summaries[doc/eng-us/standards/header.txt]=The copyright and license header that every PHP file of the inherited 4.x code base started with ...
TitleRules[]=doc/changelogs/*/CHANGELOG-desc-*;Release {fileversion} summary: {title}
TitleRules[]=doc/eng-us/features/*/event.*;{title} ({dirversion})
```

`Titles[]` and `Summaries[]` are keyed by the path relative to the installation root. A `TitleRules[]` entry is
`<pattern>;<template>` (first match wins, only for pages without a `Titles[]` entry); the template may use `{title}`
(the page's own title), `{name}` (its file name), `{fileversion}` (the version at the end of the file name) and
`{dirversion}` (the nearest version directory). A front matter `title:` or `summary:` always wins. To find unclear
titles, list every page with `./console exp:help index --list` and look for file names, `README`, versions and
duplicates.

### Order of the pages

The page tree (`/exphelp/contents` and the sidebar of every page), the folder lists and the category lists sort pages by
their order, then by title. A page's order is its front matter `order:`, else 0 for a folder's `README.md` or
`index.md` and 1000 for any other page. The project files (`README.md`, `CONTRIBUTING.md` ...) and the plain text pages
(`release-overview`, `LICENSE-GFDL-1.3.txt`) cannot carry front matter, so `[TreeSettings] PageOrder[]` places a page by
its id, and wins over its front matter:

```ini
[TreeSettings]
PageOrder[]
PageOrder[project/README.md]=0
PageOrder[INSTALL.md]=10
PageOrder[languages.md]=20
PageOrder[branch-overview.md]=30
PageOrder[release-overview]=40
PageOrder[project/SECURITY.md]=50
PageOrder[project/CONTRIBUTING.md]=100
PageOrder[project/CODE_OF_CONDUCT.md]=105
PageOrder[project/COPYRIGHT.md]=110
PageOrder[README-LICENSE.md]=120
PageOrder[LICENSE-GFDL-1.3.txt]=130
PageOrder[project/LICENSE.md]=140
PageOrder[glossary.md]=9999
```

The key is the page id: the path below the language directory (`INSTALL.md`, `release-overview`), `project/<path>`
for a project file, `ext/<extension>/<path>` for a page of an extension. The id is the same in every language, so one
entry places a page and its translations. The list above is what Exponential ships, and also the built-in default
(`expHelpAudience::PAGE_ORDER`); `PageOrder[]` alone in an override clears it, and an override can add entries for
its own pages. The top-level pages are placed here only (`glossary.md` keeps its front matter `order: 9999`, the same
value). Folders still come before the pages of their level. Check the result on `/exphelp/contents`.

## Help from extensions

An extension adds context help in `extension/<ext>/settings/exphelp.ini.append.php`:

```ini
[ContextHelp]
Map[mymodule]=ext/myextension/usage.md
Map[mymodule/edit]=ext/myextension/usage.md#editing
```

Its own documentation is shown on its own while the extension is active (next section); its pages have the ids
`ext/<extension>/<path>`, which the context help map can name.

## Documentation of the extensions

The help center finds the documentation of every **active** extension when it looks
(`[IndexSettings] ActiveExtensionDocs=enabled`, the default; `eZExtension::activeExtensions()` of the request): an
extension switched on shows up at once, one switched off is gone at once. Of each such extension it shows

- the files below `extension/<name>/doc/` that the extension's repository tracks (its own `.git`, or the
  installation's when the extension is part of it), or that its release lists in its `share/filelist.md5`;
- the files at the top of `extension/<name>/` whose names match `[IndexSettings] ExtensionTopFiles[]`: `README*`,
  `CHANGELOG*`, `LICENSE*`, `INSTALL*`, `USAGE*`, `COPYING*` (compared without case), tracked or listed the same way.

and nothing else of the extension. The rules of "What it shows, and what never" apply, plus:

- only page suffixes (`md`, `markdown`, `txt`, `rst`) and images; PHP, templates, scripts, styles, settings and every
  other source file are never shown, whatever their place (`ExcludePatterns[]` has `*.php`, and other suffixes are no
  page suffix);
- a text file without a suffix (`LICENSE`, `CHANGELOG`) is left out when it starts like a program (`#!` or `<?`);
- nothing of an inactive extension (unless it is named in `ExtensionDocDirs[]`), no untracked file, nothing outside
  the extension's `doc/` directory but the top files, no symbolic link, no path with `..`;
- a file of `doc/` wins over the top file of the same name (`doc/README.md` over `README.md`).

An extension installed without its repository and without a `share/filelist.md5` (a copy) shows nothing: the help
cannot tell its own files from files someone put there.

| | |
|---|---|
| Page id | `ext/<name>/<path below doc/>` (`ext/expai/vector.md`); a top file `ext/<name>/README.md` |
| Address | `/exphelp/viewer/ext/expai/vector.md`, like every page |
| Type, category | "Extension documentation" (`extension`), category and navigation folder **Extensions** |
| Audience | editors, administrators and developers, unless the page's front matter or the path rules say otherwise |
| Overview | the extension's `doc/README.md` or `doc/index.md`, else its top `README` |
| Links | resolved against the file, as GitHub does: `../README.md` in `doc/index.md` opens the top README |
| Edit on GitHub | none: `EditBaseURL` names the installation's repository, not the extension's |
| Breadcrumb | Help > Extensions > the extension's overview (or its index) > ... > the page |

**The Extensions index** (`/exphelp/extensions`) lists every extension that has pages, with the title of its overview
and its number of pages; `/exphelp/extensions/<name>` is one extension's index: its pages by folder, and a search
field limited to them. The **search** takes `(extension)/<name>` as a view parameter
(`/exphelp/search/(q)/vector/(extension)/expai`, in the form as a hidden field and in the JSON request as
`extension=expai`); its page says which extension it is limited to and links to the whole help. The contents page
links to the index, and the dashboard shows the category **Extensions** with the others.

An extension that had its own documentation view can send its old addresses there; expai does
(`expai/help` redirects to `exphelp/extensions/expai`, `expai/help/(page)/<name>` to the page).

Tests: `tests/tests/kernel/classes/help/expHelpExtensionDocsTest.php` (no database: a fixture installation with
extensions in their own repositories and in the installation's, active and inactive; source files, settings, scripts
without a suffix, untracked files and traversal refused; ids, overview, list, search filter, breadcrumb).

## Project files

The Markdown files of the repository that document the project itself, outside `doc/`, are help pages too: the
root's `README.md`, `SECURITY.md`, `CONTRIBUTING.md`, `COPYRIGHT.md`, `LICENSE.md` (and `INSTALL.md`, `CHANGELOG.md`
or any other root `*.md` once one is committed), and the `README.md` files of `bin/`, `design/<design>/` and
`kernel/` when there are any. They are in the category **Project files** (`project-files`, type `project`) in the
search (filter by the category or the type) and on the dashboard. In the page tree of the contents page
(`/exphelp/contents`) and of every help page they are not in a folder: each is a page at the top of the tree, next to
the main folders, and the breadcrumb of one is Help > the page. Their audiences are developers and administrators, and
more for some (`[ProjectFiles] AudienceRules[]`: `README.md` is also for editors, users, partners and hosting
companies); the title is the first heading. In the tree, a title that says no more than "README" gets the directory in
front (`kernel/private README`), so no two entries read the same.

- **Ids**: `project/<path in the repository>`: `/exphelp/viewer/project/README.md`,
  `/exphelp/viewer/project/bin/php/README.md`. An address with the path alone (`/exphelp/viewer/README.md`)
  redirects to the id. The prefix `project/` is reserved for them.
- **Links**: a link of a documentation page to a project file (`[README](../../../../README.md)` from
  `doc/eng-us/features/6.0/`) opens it in the help, not on GitHub; a link of `README.md` into the documentation
  (`doc/eng-us/guides/caches.md`) opens the help page. Links are resolved against the file, as GitHub resolves them,
  so the same Markdown works in both places.
- **Language**: the project files are English only and not translated, like `doc/changelogs/`. A reader of another
  language sees them in English with the note that the page is not translated.
- **License**: they are part of the software and are distributed under its license (`[ProjectFiles] LicenseName`,
  linked to `LicensePage`), not under the documentation license; the notice under them says so.
- **Left out** (`[ProjectFiles] Exclude[]`): test fixtures (`**/fixtures/**`) and the copies of other projects
  (`**/javascript/**`, the README of a bundled JavaScript library such as TinyMCE).

What can never be shown, whatever `Include[]` says and in an override too:

- a file git does not track: the list comes from the git index, `git ls-files` or the release manifest, never from
  the directory, so a private note in the root that is not committed (an untracked `notes.md`) is never shown,
  searched, linked or redirected to;
- a tracked file in a place `.gitignore` or `.git/info/exclude` ignores (added with `git add -f`);
- anything but Markdown (`.md`, `.markdown`), and any path below `doc/` (the documentation itself), `var/`,
  `vendor/`, `settings/override/`, `settings/siteaccess/` or `node_modules/`;
- a hidden file or a file in a hidden directory, `.github/` included (the pull request template is not a page);
- anything below `extension/`: the documentation of the extensions is the category **Extensions**
  (`exphelp/extensions`), not project files;
- anything below a `tests/` directory, wherever it is (`tests/`, `kernel/classes/tests/`,
  `extension/ezoe/tests/tinymce8/`): tests and their fixtures are not documentation;
- symbolic links, files outside the installation root and files over `MaxFileSize`, as for every help file.

```ini
[ProjectFiles]
Enabled=enabled
Include[]
Include[]=*.md
Include[]=bin/**/README.md
Exclude[]
Exclude[]=**/fixtures/**
```

`*` stays inside one directory and `**` crosses directories. `Enabled=disabled` takes them out of the help. After a
change, `./console exp:help index --rebuild` builds the index and search again (a commit that adds a file does it on
its own, because the git index changes). `tests/tests/kernel/classes/help/expHelpProjectFilesTest.php` covers it (no
database), among others that an untracked root note is never indexed.

## Landing pages of the audiences

Each audience has its own landing page, `exphelp/audience/<audience>` (for example `exphelp/audience/developer`).
The title of each audience card on the dashboard opens it. The page is made of blocks, in the order that
`[LandingSettings] Blocks[]` gives, or `[Landing_<audience>] Blocks[]` for one audience:

| Block | What it shows |
|---|---|
| `hero` | The audience's name, a summary, its number of pages, a search box limited to the audience, and buttons: **Start here**, **All pages for ...** and **All audiences** |
| `start` | The reading path: `ReadingPath[]`, else the start page and the audience's help pages in their order |
| `boxes` | Info boxes: the title and summary of `InfoPages[]`, else of the next pages that have a summary |
| `categories` | A search button for each category with pages for the audience (`Categories[]` limits them); each opens `exphelp/search/(category)/<id>/(audience)/<audience>` |
| `key` | Key pages: `KeyPages[]`, else the next pages in order |
| `latest` | Recently updated pages of the audience, from the git history |
| `related` | The landing pages of `Related[]` audiences |
| `commits` | The latest commits of the project, page by page (developers) |
| `support` | The Support Exponential block (supporters and sponsors) |

A page that a list names but that is not in the help (not written yet, hidden) is left out, so a landing page never
links a missing page. Every list comes from the cached help index; nothing else is read to draw the page.
`[LandingSettings] Enabled=disabled` turns the pages off; the cards then open the search, as before.

### The commit log

The developers' landing page lists the latest commits of `se7enxweb/exponential` (`[CommitFeedSettings]`). The
server reads the repository's Atom feed (`FeedURL`), never the browser, so the admin's Content Security Policy is
kept and no visitor's address reaches GitHub. It reads at most once every `CacheTTL` seconds (600), with a timeout of
`Timeout` seconds (4), and one process at a time. The feed holds the newest 20 commits; each read is added to the
cached list (at most `MaxCommits`, 500), which the page shows `PageSize` (10) at a time with the shared pager.
Merge commits are hidden unless **Show merge commits too** is chosen (`/(merges)/1`). When GitHub cannot be
reached, the page keeps the cached commits and says when they were read; a failed read is retried after
`RetryAfter` seconds (120). Titles, messages and authors are shown as escaped text, the links are built from each
commit's hash (`CommitBaseURL`), and no avatars or other remote images are shown.

### Support Exponential

The bottom of the dashboard, and the landing pages of supporters and sponsors, ask readers to fund the work with a
monthly sponsorship through GitHub Sponsors, to 7x (<https://github.com/sponsors/se7enxweb>) and to the Exponential
Foundation (<https://github.com/sponsors/exponentialcms>), and say what each organization's funding pays for.
`[SupportSettings]` holds the title, the text and the order of the organizations; each organization's
`[SupportOrganization_<id>]` group holds its name, its sponsor address (https only), the button text, its page in the
help, its mission and `Funds[]`. `Enabled=disabled` removes the block.

## Finding a page

- **All pages** (`/exphelp/contents`): every help page as one tree, its folders closed (each shows its title and
  number of pages), with a filter field that narrows the tree to the pages whose title or folder has every typed
  word and opens their folders; emptied (with **Clear filter** or Esc) it puts the folders back as they were.
  **Expand all** and **Collapse all** open or close every folder; they sit in a small panel at the end of the page,
  sticky at the bottom of the window at the end side of the help column (the left side in a right-to-left language),
  so they stay at hand while scrolling and never lie over the side menus, and appear only with JavaScript. The
  filter text and the folders the reader opened or closed (by their path, `data-nav-path`) are kept in the browser's
  `localStorage` (key `exphelp-nav`), so the contents page and the navigation at the side of every help page show
  the same filter and folders from page to page; the folders of the page in view are always open, and without
  storage (blocked or private browsing) everything still works for the page in view.
  The panel stays above the debug bar, and above its minimised tab, through the custom property
  `--exp-debug-clearance` that `expdebugbar.css` sets (the room the bar keeps free at the bottom of the window; unset
  without the bar); another fixed control at the bottom of a page can use it the same way:
  `bottom: calc(var(--exp-debug-clearance, 0px) + <gap>)`. Each folder has an id from its path, so
  `/exphelp/contents#exphelp-folder-help-editors` opens the folder `doc/eng-us/help/editors`. The viewer shows the same tree at the
  side of each page, with the folders on the way to the page open. Folders are native disclosure elements, so the
  tree works without JavaScript; a folder's `README.md` or `index.md` is its "Overview" link. A folder is named by
  `exphelp.ini [NavigationSettings] FolderNames[<path>]`, else by the title of its README, else by its directory
  name. The tree is cached per index, siteaccess and language, so a page with 1 200 entries costs one file read.
  Folders listed in `[NavigationSettings] FoldedFolders[]` (shipped: the generated reference examples below
  `doc/eng-us/examples/`) show only their "Overview" link and page count; their pages, and those of the folders below them,
  are listed while a page inside is open. Without folding, the 2 400 generated pages would put about 2 400 more links
  on every page of the help.
- **Search and browse addresses** use view parameters, so they can be bookmarked and linked:
  `/exphelp/search/(q)/publish`, `/exphelp/search/(category)/layouts/(audience)/editor`, with `(type)`,
  `(extension)` (the documentation of one extension), `(offset)` and `(limit)` as well. Without `(q)` the page lists every page the filters allow (browse mode); with no
  parameter at all it lists all pages. The dashboard's audience cards, topics and chips, the category and audience
  of a page and the search forms all link to these addresses. An old address with `?q=...&category=...` is
  redirected to its canonical form; the JSON answer for the instant search (`format=json` or
  `Accept: application/json`) accepts both forms and is never redirected.
- **Help menu**: the "?" in the header of admin4 and admin4l (and of the classic admin) opens a menu with a search
  field, "Help for this page", up to `[ContextHelp] MenuTopics` related topics, the links of
  `[ContextHelp] MenuLinks[]` and "Open the Help center". Without JavaScript the "?" is a link to the help for the
  current page. The Help center is also linked from the user menu, the footer, the dashboard and the Setup overview.

## Record pages

The release history (`doc/eng-us/release-overview`) and any other plain text list of version records
are shown as tables per series by `expHelpRecordRenderer`: version, name, date, revisions,
addresses and notes, with a filter field over all rows and sort buttons in the column headers (without the script
the tables are complete and in date order). Any plain text file written in the same "name line, then `- Key=value`
lines" shape is shown the same way. Addresses on the old SVN hosts are shown as text marked "historical", never as
links. These pages rank in the search like the pages of the old versions they describe.

## The documentation license

The documentation in `doc/` is free documentation under the GNU Free Documentation License 1.3 or later
(`doc/eng-us/LICENSE-GFDL-1.3.txt`, `doc/eng-us/README-LICENSE.md`, and for readers `doc/eng-us/help/free-documentation.md`). Every help
page, its print view and its PDF end with the license notice, and the dashboard has a "Free documentation" block.
This covers all 6.x documentation, including pages contributed through pull requests; the pages inherited from
earlier versions keep their own notice (`exphelp.ini [License] OtherLicensePathRules[]`). `[License] Enabled=false`
hides the notices.

## Related

- `doc/eng-us/help/develop-with-git.md`: working from a Git clone with its history, and growing from a user to a developer.
- `doc/eng-us/specifications/6.0/help-system.md` and `doc/eng-us/specifications/6.0/help-style-guide.md`: the specification of the
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

The examples below `doc/eng-us/examples/` that are generated from the code (template operators, fetch functions, module
views, policies, INI settings, datatypes, workflow events, cronjobs, console commands, extension points) are written
by `./console exp:help:examples`; `--check` reports pages that are not current. See
[Examples](../../examples/README.md#how-the-generated-pages-are-made). They have the page type `generated`, which ranks
below the written pages (`[SearchSettings] TypeBoost[generated]`) and stays off the audience cards of the dashboard.

The index is cached in `var/<site>/cache/exphelp/` and rebuilt by itself when the tracked files change. A rebuild
also removes what the older index left: the other `index-<signature>.php` files and the cached navigation trees
(`nav/`) and rendered pages (`render/`) written before it, so the cache holds only what the current index uses.
The signature of the index covers the listed help files (their paths, sizes and times), the commit each repository
is at and the settings, not the git index file itself: `git status`, or a commit or checkout that changes no help
file, keeps the index, the search index and the rendered pages. A new index is built by one process at a time; the
other requests use the newest index already in the cache until it is ready (the search index likewise), instead
of each building its own. Removed versions are also dropped from OPcache, so its memory is reclaimed.

## Languages and accessibility

The labels of the help pages (dashboard, search, page viewer, error report, the Help button) are in the
`kernel/exphelp` context of the administration translations, and every complete catalogue has them: German,
French, Spanish (Spain and Mexico), Ukrainian, Hebrew, Arabic, Latin and Klingon, with English and the
`untranslated` template kept in step. The names in `exphelp.ini` (`[CategorySettings] CategoryNames[]`, the
trackers' `Name` and `Description`) go through the same context, so a new name there needs a message in each of
those catalogues; `tests/tests/lib/ezi18n/expTranslationQualityTest.php` checks that a finished translation keeps
the placeholders (`%count`, `%title` ...) of its source. The diagnostics and the issue text the error report
prepares stay in English on purpose, because the maintainers read them.

### The pages in the reader's language

The help pages themselves are the documentation, one directory per language below `doc/`, named by the
Exponential locale code in lower case: `doc/eng-us/` (`eng-US`) is the original, a translation is `doc/<locale>/`
(`doc/ger-de/` for `ger-DE`; a future French one `doc/fre-fr/`), and `doc/changelogs/` is not translated
(`expHelpLanguage`; [Translated documentation](../../bc/6.0/translated-documentation.md)). The reader's language is
the interface language (`site.ini [RegionalSettings] Locale`, which the language switch sets): an alias of
`exphelp.ini [DocLanguages] Map[]` (`eng-GB` to `eng-us`, `ger-AT` and `ger-CH` to `ger-de`), else the locale in
lower case when `Directories[]` lists it, both tried for the locale and the locale without its variant; else the first
directory of its three-letter language (any other `ger-*` reads `ger-de`), else `Default=eng-us`
(`expHelpLanguage::forLocale()`). A page id is the path below `doc/eng-us/` in every language, so an
address shows the same page in each; a translation may translate its file names and list them in
`doc/<locale>/.paths.json`. A page the language does not have yet is shown in English with a note ("This page is not
translated into German yet"); the index, the search, the navigation and the rendered pages are kept per language
(`lang-<directory>/` in the help cache, `lang-ger-de/`), and `./console exp:help index` builds them all. An address
that names a file (`/exphelp/viewer/doc/eng-us/guides/caches.md`, `/exphelp/viewer/ger-de/<translated path>`) is
redirected to the page id, and so are the names of before the rename to the locale codes
(`/exphelp/viewer/en/us/guides/caches.md`, `/exphelp/viewer/doc/en/us/...`, `/exphelp/viewer/de/<translated path>`),
which `[DocLanguages] Legacy[]` maps to today's directories (`en/us` to `eng-us`, `de` to `ger-de`; only for
addresses, no file is read from an old directory).

The start page and every page show the reader's language next to the search: "Reading in: English / Deutsch"
(`exphelp/parts/reading_language.tpl`, `expHelp::reading()`). It offers each language of `[DocLanguages]
Directories[]` that has pages and that the interface language switch can choose for the user; a button posts to
`uiswitch/set` like the language switch of the top bar, so it changes the interface language of the whole
administration, and comes back to the same page. Below it, how much of the help each translation has ("German: 120
of 7,265 pages", `expHelpDocumentIndex::languageCoverage()`, counted from the file list without reading a page). A
translated page links to its English original, and so does the note of a page that is not translated yet: the
original is the same address with `?lang=eng-us` (the directory name; the old keys `en-us` and `de` are still
accepted), which shows the page in that language for the one request, with a link back to the reader's language.

In a right-to-left interface language (Hebrew, Arabic) the pages are mirrored: the navigation tree, the contents
and the previous/next links swap sides and their arrows turn, while paths, code and the diagnostics stay left to
right. Every page has one `h1`, named navigation landmarks, labels for each search field and filter, a visible
focus ring on everything that takes the keyboard, and a note on each link that opens a new window; `/` moves to
the help search, and the instant results are announced to screen readers.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
