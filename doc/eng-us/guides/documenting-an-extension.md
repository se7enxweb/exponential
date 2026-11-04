---
title: Documenting an extension
audience: developer, admin
summary: Where an extension's documentation goes (README.md and INSTALL.md on top, every other page as Markdown in doc/, in English), how the help center shows it, and how to turn old plain-text documents into Markdown.
keywords: extension documentation, doc folder, README.md, INSTALL.md, Markdown, plain text, help center, ext/, ExtensionTopFiles, ActiveExtensionDocs, ExtensionDocDirs, front matter, context help
---

# Documenting an extension

Every extension documents itself in the same place, by the same convention, so that anyone who installs it finds the
documentation without searching, and the help center of every installation shows it without any setup. This guide is
the reference for that convention. The other pages that mention an extension's documentation link here.

At the end of this guide you will have an extension whose README, installation notes and manual appear in the help
center under **Extensions**, in the right order, with working links, images and context help.

## Contents

1. [The convention in short](#1-the-convention-in-short)
2. [The directory layout](#2-the-directory-layout)
3. [Why Markdown: from plain text to .md](#3-why-markdown-from-plain-text-to-md)
4. [Converting a plain-text document](#4-converting-a-plain-text-document)
5. [How the help center shows the pages](#5-how-the-help-center-shows-the-pages)
6. [Order, titles and context help](#6-order-titles-and-context-help)
7. [What is never shown](#7-what-is-never-shown)
8. [Check it](#8-check-it)
9. [Troubleshooting](#9-troubleshooting)

## 1. The convention in short

- **On top of the extension:** `README.md` and `INSTALL.md`, plus `CHANGELOG.md`, `LICENSE`, `USAGE.md` and
  `COPYING` where the extension has them, directly in `extension/<extensionName>/`.
- **Everything else:** as Markdown (`.md`) files in `extension/<extensionName>/doc/`, with `doc/README.md` (or
  `doc/index.md`) as the start page of the manual.
- **In English.** English is the language every reader of an extension can be expected to read, so an English
  original keeps the documentation usable by all. The help center does not pair translations of extension pages
  with their English pages, as it does for the kernel's `doc/eng-us/` and `doc/ger-de/`.
- **Committed.** Only files the extension's repository tracks (or that its release lists in `share/filelist.md5`) are
  shown. A file that is only on disk is not.

Nothing needs to be configured: the help center shows the documentation of every active extension as soon as it is
active.

## 2. The directory layout

```text
extension/myextension/
├── README.md            what it does, requirements, installation in short, a first example, license
├── INSTALL.md           the installation step by step
├── CHANGELOG.md         what changed in each release
├── LICENSE              the license text
├── extension.xml, ezinfo.php, settings/, design/, modules/, classes/ ...   (never shown)
└── doc/
    ├── README.md        start page of the manual: what is in it, links to every page
    ├── settings.md      the settings with their defaults
    ├── templates.md     templates, variables, operators
    ├── upgrading.md     what to do between versions
    ├── images/
    │   └── edit-form.webp
    └── howto/
        └── first-steps.md
```

- **Links between pages** are relative, as on GitHub: `[Settings](settings.md)` from `doc/README.md`,
  `[the README](../README.md)` from a page in `doc/` to the top README.
- **Images** go next to the pages (`doc/images/`) and are linked relatively: `![The edit form](images/edit-form.webp)`.
  The help center serves PNG, JPEG, GIF and WebP images. SVG and HTML are never served.
- **A page in a subfolder** of `doc/` is shown in that folder: `doc/howto/first-steps.md` becomes the page
  `ext/myextension/howto/first-steps.md`.
- **A file in `doc/` wins over a top file of the same name:** with both `doc/README.md` and `README.md`, the help
  center shows `doc/README.md` as `ext/myextension/README.md`.

`expai` is a complete example: `README.md`, `INSTALL.md` and `USAGE.md` on top, and more than forty Markdown pages in
its `doc/`.

## 3. Why Markdown: from plain text to .md

Extension documentation started out as plain text files in English. The extensions of the eZ Publish years, which
Exponential continued, shipped a `README` and an `INSTALL` without a suffix, a `CHANGELOG`, or files such as
`doc/INSTALL`, `doc/FAQ` and `doc/*.txt`; a few used reStructuredText (`README.rst`) or office documents (`.odt`,
`.pdf`). You can still see this in the extensions that came from that time: `ezjscore` has `README`, `CHANGELOG`,
`doc/INSTALL` and `doc/FAQ`, `ezoe` has `README.rst` and `changelog.txt`.

The documentation has since evolved to full Markdown documents, and **Markdown (`.md`) is the convention for new and
updated extensions.** Markdown gives the reader what plain text cannot:

- **headings** that become the page's outline and anchors (`settings.md#clearing-the-cache`);
- **links between pages** that the help center follows and checks;
- **tables** for settings and their defaults;
- **code blocks** with highlighting for INI, PHP, templates and shell commands;
- **images** next to the text;
- **front matter** for the page's title, order, audience and summary ([section 6](#6-order-titles-and-context-help)).

The help center renders Markdown as a page with its own table of contents, highlighted code, a search entry and a
place in the sidebar. A plain text file is shown as it is, in a monospaced block, without links or headings.

**Existing plain-text documentation keeps working.** The help center still shows `.txt`, `.rst` and suffix-less text
files (`README`, `INSTALL`, `CHANGELOG`) as plain text pages, so nothing breaks when an old extension is installed.
Office documents (`.odt`, `.pdf`) are not shown; convert what they say to Markdown pages.

## 4. Converting a plain-text document

1. **Rename** the file with `git mv`, so its history stays: `git mv INSTALL INSTALL.md`, `git mv doc/FAQ doc/faq.md`.
2. **Add headings:** a `#` title on the first line, `##` for each section. Underlined headings (`=====`, `-----`) become
   `#` and `##` lines.
3. **Turn references into links:** "see doc/settings.txt" becomes `see [the settings](doc/settings.md)`, a bare URL
   becomes `[its name](https://...)`. Dead addresses (ez.no, share.ez.no) are replaced by the current page or described
   in words.
4. **Fence the code:** indented commands and settings go into fenced blocks with their language (`ini`, `bash`, `php`).
5. **Keep it English,** and keep the content: the conversion changes the form, not what the document says.
6. **Commit** and check it ([section 8](#8-check-it)).

Before, `extension/myextension/INSTALL`:

```text
MyExtension installation
========================

1. Copy the extension to extension/myextension
2. Enable it in settings/override/site.ini.append.php:

   [ExtensionSettings]
   ActiveExtensions[]=myextension

3. Clear the caches. See doc/settings.txt for the settings.
```

After, `extension/myextension/INSTALL.md`:

````markdown
# Installing MyExtension

## 1. Get the code

```bash
composer require vendor/myextension
```

## 2. Switch it on

In `settings/override/site.ini.append.php`:

```ini
[ExtensionSettings]
ActiveExtensions[]=myextension
```

## 3. Clear the caches

```bash
php bin/php/ezcache.php --clear-all
```

The settings are described in [Settings](doc/settings.md).
````

## 5. How the help center shows the pages

The help center (`/exphelp/dashboard` in the administration) lists the documentation of every **active** extension
(`[IndexSettings] ActiveExtensionDocs=enabled` in `settings/exphelp.ini`, the default). An extension switched on shows
up at once, one switched off is gone at once.

| | |
|---|---|
| Which files | the tracked files below `extension/<name>/doc/`, and the tracked files on top of `extension/<name>/` whose names match `[IndexSettings] ExtensionTopFiles[]` (`README*`, `CHANGELOG*`, `LICENSE*`, `INSTALL*`, `USAGE*`, `COPYING*`, compared without case) |
| Page suffixes | `.md`, `.markdown`, `.txt`, `.rst` (`DocumentSuffixes[]`), and text files without a suffix |
| Page id | `ext/<name>/<path below doc/>`, for example `ext/expai/vector.md`; a top file is `ext/<name>/README.md` |
| Address | `/exphelp/viewer/ext/<name>/<path>` |
| Where | the folder and category **Extensions**, and the Extensions index `/exphelp/extensions`, one extension at `/exphelp/extensions/<name>` |
| Start page | `doc/README.md` or `doc/index.md`, else the top `README` |

To show the documentation of an extension that is **not** active, name it in `ExtensionDocDirs[]`:

```ini
[IndexSettings]
ExtensionDocDirs[]=myextension
```

The full rules are in [The help system](../features/6.0/help-system.md#documentation-of-the-extensions).

## 6. Order, titles and context help

A Markdown page can start with a front matter block. The help center reads these keys:

```markdown
---
title: Settings of MyExtension
summary: Every setting of myextension.ini with its default.
audience: developer, admin
order: 20
keywords: settings, cache
---
```

- `title` replaces the page's first heading in lists and the sidebar; `summary` is shown on cards and in search
  results.
- `order` places the page among its siblings; without it a page sorts by title after the folder's `README.md`.
- `audience` (editor, admin, developer ...) decides on which landing pages it is listed.

**Context help:** the "?" button of your module's pages can open your page. Add to
`extension/<name>/settings/exphelp.ini.append.php`:

```ini
[ContextHelp]
Map[mymodule]=ext/myextension/README.md
Map[mymodule/edit]=ext/myextension/settings.md#clearing-the-cache
```

## 7. What is never shown

- PHP, templates, scripts, styles, settings and every other source file, wherever they are (`ExcludePatterns[]`
  has `*.php`, `*.html` and `*.svg`, and other suffixes are no page suffix);
- a text file without a suffix that starts like a program (`#!` or `<?`);
- anything of the extension outside `doc/` except the top files;
- untracked files, symbolic links, paths with `..`, and files larger than `MaxFileSize` (2 MB);
- the whole documentation of an extension installed as a plain copy, without its repository and without a
  `share/filelist.md5`: the help center cannot tell its own files from files someone put there.

## 8. Check it

1. Commit the pages in the extension's repository.
2. Open **Help > Extensions** (`/exphelp/extensions`): your extension is listed with its start page's title and its
   number of pages. Open `/exphelp/extensions/myextension` for its pages by folder.
3. Check the links and images of your pages:

```bash
./console exp:help check --below=ext/myextension/
```

4. Open a page from the "?" button of your module to test the context help.

## 9. Troubleshooting

| Symptom | Cause and fix |
|---|---|
| The extension is not listed under Extensions | It is not active, or nothing of it is committed. Activate it, or add it to `ExtensionDocDirs[]`; commit the pages. |
| A new page is missing | It is not committed yet, or the index is older than the commit: run `./console exp:help index`. |
| A page shows as plain text without links | It has no `.md` suffix. Rename it ([section 4](#4-converting-a-plain-text-document)). |
| A PDF or ODT manual is missing | Office documents are not shown. Convert their content to Markdown pages. |
| An image does not show | SVG is never served; use WebP or PNG next to the page and link it relatively. |
| A link opens "not found" | The target is outside `doc/` and not a top file, or the path is wrong. `exp:help check` names it. |
| A German page appears as a separate page | Translations of extension pages are not paired with the English page. Keep one English page. |

## Related

- [Extending Exponential 6.0](../EXTENDING.md): the ways to extend Exponential, including the help pages of an extension.
- [The help system](../features/6.0/help-system.md): every rule of the help center.
- [Extensions](extensions.md): find, install, build and release an extension.
- [Documenting your extension](../help/extension-development/part-3-extension-developer/documenting-your-extension.md),
  the chapter of the extension development course.
