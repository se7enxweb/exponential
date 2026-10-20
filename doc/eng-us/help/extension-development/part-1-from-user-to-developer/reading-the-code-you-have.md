---
title: Reading the code you already have
audience: user, developer, designer, app-creator
category: extension-development
order: 60
summary: A map of an installation and four reading techniques: grep for a name, follow an address to its view, a page to its template, and ask Git why a line is there.
keywords: code reading, directory layout, kernel, lib, settings, design, extension, var, bin, grep, git log, git blame, module, view, where is
type: help
---
# Reading the code you already have

Part 1, chapter 6. The best teacher of Exponential is the code on your disk: more than twenty years of a working
content management system, with its full history. This chapter gives you a map of the installation and four reading
techniques you will use every day.

## The map

From the installation root (the directory that holds `index.php`):

| Folder | What is in it | Do you change it? |
|---|---|---|
| `kernel/` | The core: one folder per kernel module (`content/`, `user/`, `setup/`, `exphelp/` ...), and `kernel/classes/` with the content model (`eZContentObject`, `eZContentClass`, datatypes, workflow types) | Never. Read it. |
| `lib/` | The libraries the kernel is built on: `ezdb/` (database), `eztemplate/` (template engine), `ezutils/` (settings, debug, HTTP, system), `ezfile/`, `ezi18n/`, `ezimage/`, `ezlocale/` ... | Never. Read it. |
| `settings/` | The default `.ini` files. `settings/override/` and `settings/siteaccess/<name>/` hold this installation's own settings | Only through overrides, siteaccess settings or extensions |
| `design/` | The kernel's designs: `standard`, `base`, `admin`, `admin3`, `admin4`, `admin4l`, `editor` ... | Never. Override in your own design extension |
| `extension/` | Every extension, each in its own folder | Your own extensions, yes |
| `var/` | What the installation writes: caches, compiled templates, logs, uploaded files (`var/<site>/storage`) | Never by hand, except to read logs |
| `bin/php/` | Command line scripts; `./console` runs them by name | Never. Your own go in `extension/<yours>/bin/php/` |
| `share/` | Translations, database schema and data, icons, the file manifest `filelist.md5` | Never |
| `autoload/` | The generated class maps (`ezp_kernel.php`, and the extension map) | Never; regenerate them |
| `doc/` | The documentation, including this book | Improve it with a pull request |
| `tests/` | The kernel's test suites | Read them; they are executable examples |
| `vendor/` | Composer libraries (Zeta Components and others) | Never |

Two rules follow from the table: **you read the kernel, you do not edit it**, and **your work lives in
`extension/`**. Everything in this book respects them.

## Technique 1: grep for the name

When you see a name in a template, a setting or an error message, search for it:

```bash
grep -rn "fetchByNodeID" kernel/classes/ezcontentobjecttreenode.php
grep -rn "\[SiteAccessSettings\]" settings/site.ini
grep -rln "ShowUsedTemplates" kernel/ lib/
```

`-r` searches folders, `-n` prints line numbers, `-l` prints only file names. For a class, the autoload map tells you
the file directly:

```bash
grep -n "'eZContentObjectTreeNode'" autoload/ezp_kernel.php
```

## Technique 2: from an address to the code

Every page of the administration is a **module** and a **view**: `/content/view/full/2` is the module `content`, the
view `view`, with the parameters `full` and `2`. To find the code:

1. The module's definition is `kernel/<module>/module.php` (for kernel modules) or
   `extension/<ext>/modules/<module>/module.php` (for extension modules).
2. In it, `$ViewList['<view>']` names the script (`'script' => 'view.php'`) and the parameters.
3. The script is next to `module.php`.

Try it:

```bash
grep -n "ViewList\['view'\]" -A6 kernel/content/module.php
```

You see the view's script and its parameters. The same three steps work for every module in the system; the chapter
[Modules and views](../part-3-extension-developer/modules-and-views.md) explains what you see.

## Technique 3: from a page to its template

Turn on `ShowTemplatePathComments` (see [Your first hour as a developer](your-first-hour-as-a-developer.md)) and view
the page source. Each comment names a template file, for example `design/standard/templates/node/view/full.tpl`. Open
it; that is the HTML of that part of the page. If the comment names a file inside an extension, the extension
overrides the kernel's template there.

## Technique 4: ask Git why a line is there

The kernel's repository has more than 27,000 commits. Each one says what changed and why. When a line puzzles you:

```bash
git log --oneline -5 -- kernel/classes/ezcontentobjecttreenode.php
git log -p -S "ShowTemplatePathComments" --oneline -- settings/site.ini | head -40
git blame -L 100,120 kernel/content/view.php
```

- `git log -- <file>` lists the commits that touched a file.
- `git log -S "<text>"` finds the commit that added or removed a piece of text.
- `git blame` names the commit behind each line; `git show <sha>` shows the whole change.

Every extension installed as a clone has its own history:

```bash
git -C extension/eztags log --oneline -10
```

Commit messages in this project start with `Added:`, `Updated:`, `Fixed:`, `Removed:` or `Renamed:`, which makes
them easy to scan: `git log --oneline | grep '^[0-9a-f]* Fixed:'` lists bug fixes.

## Reading the tests

`tests/tests/` holds the kernel's PHPUnit tests, organized like the kernel. A test is an executable example: it shows
how a class is meant to be called and what it returns. When the documentation of a class is thin, read its test:

```bash
ls tests/tests/kernel/classes/ | head
grep -rln "eZINI::instance" tests/tests/lib | head -5
```

## Reading the documentation

The `doc/` folder is organized by kind:

| Folder | What it holds |
|---|---|
| `doc/en/us/help/` | Pages like this one, for the help center in the administration |
| `doc/en/us/guides/` | Task guides for administrators and developers |
| `doc/en/us/features/6.0/` | One page per feature of the 6.0 line, with its settings |
| `doc/en/us/bc/6.0/` | Behavior changes and upgrade notes, written for developers |
| `doc/en/us/specifications/6.0/` | Specifications of larger subsystems |
| `doc/en/us/install/` | The installation manual |
| `doc/changelogs/` | What changed in each release |
| `doc/en/us/history/` | The chronicle of the project |

The help center searches all of them; type a class or setting name into its search.

## Exercise

Pick one page of the administration you use often. Find its module and view (technique 2), its template (technique
3), and the last commit that changed its script (technique 4). Write the three file paths down; you have just read
your first piece of the kernel.

## Related pages

- Previous: [Your first hour as a developer](your-first-hour-as-a-developer.md)
- Next: [Finding the extensions installed on your site](finding-the-extensions-on-your-site.md)
- [Develop with Git](../../develop-with-git.md)
- [The extension directory layout](../part-3-extension-developer/extension-directory-layout.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
