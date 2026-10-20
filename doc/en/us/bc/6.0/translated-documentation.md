# Translated documentation: doc/eng-us/ and a directory per language (6.0.15)

The documentation of Exponential moved from `doc/` to `doc/en/us/` in October 2026, so that it can be translated,
and on 20 October 2026 the language directories were renamed after the locale codes of the interface:
`doc/en/us/` became `doc/eng-us/` and `doc/de/` became `doc/ger-de/` (the locale in lower case, `eng-US` ->
`eng-us`).
Each language has a directory of its own below `doc/` with the same relative paths: `doc/eng-us/` is the original
(American English), and a translation lives in `doc/<language>/`, for example `doc/ger-de/` for German. The changelogs
stay in `doc/changelogs/`: they are not translated.

The help center of the administration (Help in the top bar) reads the language of the interface and shows each page
in it, and the English page where no translation exists yet, with a note that says so.

## Contents

1. [What moved](#what-moved)
2. [What stays at doc/](#what-stays-at-doc)
3. [What to change](#what-to-change)
4. [The help center and the reader's language](#the-help-center-and-the-readers-language)
5. [Page ids and addresses](#page-ids-and-addresses)
6. [Adding a translation](#adding-a-translation)
7. [Translated file names: .paths.json](#translated-file-names-pathsjson)
8. [Settings](#settings)
9. [The command line](#the-command-line)
10. [Several translators: merging .paths.json and .dirnames.json](#several-translators-merging-pathsjson-and-dirnamesjson)
11. [Tests](#tests)

## What moved

Every tracked file below `doc/` moved to `doc/en/us/` with the same relative path, and from there to `doc/eng-us/`
(the German pages from `doc/de/` to `doc/ger-de/`), with `git mv`, so the history of each file follows it
(`git log --follow doc/eng-us/guides/caches.md`). The table shows the paths before the first move and now:

| Before | After |
|--------|-------|
| `doc/INSTALL.md` | `doc/eng-us/INSTALL.md` |
| `doc/guides/...`, `doc/features/...`, `doc/bc/...` | `doc/eng-us/guides/...`, `doc/eng-us/features/...`, `doc/eng-us/bc/...` |
| `doc/help/...`, `doc/install/...`, `doc/examples/...`, `doc/standards/...` | the same below `doc/eng-us/` |
| `doc/history/...`, `doc/specifications/...`, `doc/shop/...`, `doc/doxygen/...` | the same below `doc/eng-us/` |
| `doc/glossary.md`, `doc/languages.md`, `doc/branch-overview.md`, `doc/release-overview` | the same below `doc/eng-us/` |
| `doc/README-LICENSE.md`, `doc/LICENSE-GFDL-1.3.txt` | the same below `doc/eng-us/` |
| `doc/changelogs/...` | unchanged |

Every reference to the old paths in the repository was changed with it: the links of the root files (`README.md`,
`INSTALL.md`, `CONTRIBUTING.md`, `SECURITY.md`), the templates' links to the guides and their GitHub links, the
settings (`exphelp.ini` path rules, titles and license rules, and the `Doc=` entries of the other INI files), the
code comments and the paths in PHP, the command line scripts, the tests, the CI workflows, `composer.json` and the
translations whose source text names a guide. The relative links inside the moved tree did not change; the links
from it to `doc/changelogs/` and to files outside `doc/`, and the changelogs' links to the guides, were rewritten.

Not changed, on purpose:

- the released changelogs (`doc/changelogs/`, except the one of 6.0.15 that is still being written) and the history
  pages (`doc/eng-us/history/`): they record the commits and paths of their time;
- the `doc/` directories of extensions and other repositories (`extension/ezfind/doc/INSTALL.md`), and URLs of other
  projects.

## What stays at doc/

- `doc/changelogs/`: not translated, so in no language directory.
- `doc/README.md`: a short page for readers of the repository (GitHub shows it for `doc/`) that lists the language
  directories. The help center leaves it out (`[IndexSettings] ExcludePatterns[]`), because it chooses the language
  itself.

## What to change

- **A link or bookmark to a file on GitHub** (`.../blob/main/doc/guides/caches.md`, or
  `.../blob/main/doc/en/us/guides/caches.md` from before the rename): write `doc/eng-us/`
  (`.../blob/main/doc/eng-us/guides/caches.md`); German pages are below `doc/ger-de/`.
- **A help address** (`/exphelp/viewer/guides/caches.md`): nothing. Page ids did not change.
- **Settings of your own that name a documentation file** (an override of `exphelp.ini` `[AudienceSettings]`,
  `[CategorySettings]` or `[TypeSettings] PathRules[]`, `[TitleSettings] Titles[]`, `[License] OtherLicensePathRules[]`,
  `[IndexSettings] ExcludePatterns[]`): write `doc/eng-us/` where they said `doc/` (`doc/changelogs/` stays). Page ids in
  `[ContextHelp] Map[]`, `[FeaturedSettings] PageList[]` and `[Landing_*]` do not change; a value that names the file
  (`doc/guides/x.md`) still works.
- **Scripts that read a documentation file** (`doc/INSTALL.md` or `doc/en/us/INSTALL.md` in a deploy script):
  write `doc/eng-us/INSTALL.md`.
- **The help caches** (`var/<site>/cache/exphelp`): nothing; the index is built again on its own. Run
  `./console exp:help index --rebuild` after the deploy to build every language's index and search at once.

## The help center and the reader's language

The reader's language is the language the administration is shown in: `site.ini [RegionalSettings] Locale` of the
request, which the language switch of the top bar sets for the signed-in user. The directory is the locale in lower
case; `exphelp.ini [DocLanguages] Map[]` only lists aliases for locales without a directory of their own:

| Locale | Directory |
|--------|-----------|
| `eng-US`, `eng-GB` (alias), any other `eng-*` | `eng-us` |
| `ger-DE`, `ger-AT` (alias), any other `ger-*` (`ger-CH`) | `ger-de` |
| any other | `eng-us` (the default) |

The index, the search, the navigation tree, the rendered pages and the context help are then those of that language:

- a page that the language has is shown in it;
- a page it does not have yet is shown in English, with a note above it: "This page is not translated into German
  yet. It is shown in English." (translated, `kernel/exphelp`);
- the changelogs are always English (they have the same note for a reader of another language);
- the article carries the page's language (`lang="de"`, `lang="en-US"`), so screen readers pronounce it correctly.

The search of a language finds the translated pages and the English pages that are not translated, never the
English original of a translated page. Each language has its own index and search index, in
`var/<site>/cache/exphelp/` for the default and `var/<site>/cache/exphelp/lang-<directory>/` (`lang-ger-de`) for the
others.

## Page ids and addresses

A page is named by its id: its path below `doc/eng-us/` (`guides/caches.md`), the same in every language. The address
of a page is therefore the same in every language (`/exphelp/viewer/guides/caches.md`), and when a reader switches
the interface language, the page they are on stays open and is shown in the new language. A page of
`doc/changelogs/` has the id `changelogs/<path>`, as before.

An address that names a file instead of an id is redirected to the id: `/exphelp/viewer/eng-us/guides/caches.md`,
`/exphelp/viewer/doc/eng-us/guides/caches.md`, a translated path such as
`/exphelp/viewer/ger-de/anleitungen/caches.md`, and the same addresses with the directories from before the rename
(`/exphelp/viewer/en/us/...`, `/exphelp/viewer/doc/en/us/...`, `/exphelp/viewer/de/...`; `?lang=en-us` as well as
`?lang=eng-us`). Every help address from before the moves keeps working unchanged, because the ids did not change.

Links inside a page are resolved against the file the page was read from, as GitHub resolves them. A German page
that links to `../hilfe/suchen.md` opens the page that file translates, in the reader's language.

An anchor in a link is written for one language's version of the target (`guides/x.md#cross-engine-notes` in an
English page), but the reader may see the other version, whose headings have other anchors. A translation keeps the
headings of its original in the same order, so the help maps the anchor by position: the n-th heading of the version
the link was written for is the n-th heading of the version shown (`expHelpDocumentIndex::anchorOf()`). It does so
when the page is rendered and in `exp:help check`, from English to a translation and back; an anchor the shown page
has itself, or versions with a different number of headings, are left as they are.

## Adding a translation

1. Add the directory to `exphelp.ini [DocLanguages] Directories[]` (in an override, or in the kernel's file for a
   translation that ships with Exponential), map its locales in `Map[]`, and give its English name in `Names[]`,
   with a translation of that name in `kernel/exphelp`.
2. Copy the pages you translate from `doc/eng-us/` to `doc/<language>/` with the same relative paths, translate them
   and commit them: the help shows only git-tracked files.
3. Translate as many pages as you like; every page you have not translated yet is shown in English.
4. Images: an image the translation does not have is taken from `doc/eng-us/`. Put a translated screenshot at the same
   relative path to use it instead. A page with a translated file name links the original's image by its path from
   the file, as GitHub does (`![...](../../eng-us/help/images/x.webp)` in `doc/ger-de/hilfe/x.md`): links and images of a
   page are resolved from the file it was read from, so they may climb above the page's id.

A directory below `doc/` named by a locale that `Directories[]` does not list (`doc/fre-fr/`) is not
read, so a translation can be prepared in the repository before it is switched on.

## Translated file names: .paths.json

A translation may keep the English file names (`doc/ger-de/guides/caches.md`), which needs nothing else. If it translates
them, it lists each translated path in `doc/<language>/.paths.json`, a JSON object of "translated path below the
language directory" to "path below `doc/eng-us/`":

```json
{
    "anleitungen/caches.md": "guides/caches.md",
    "hilfe/suchen.md": "help/searching.md"
}
```

The page `doc/ger-de/anleitungen/caches.md` then has the id `guides/caches.md`: the same address, the same context help,
landing pages and featured pages as the English page, and the settings' path rules see it as
`doc/eng-us/guides/caches.md` (a translation has the audience, category and license of the page it translates). An
entry whose paths are not clean relative paths is left out; the file is read when it is a regular file of at most
1 MB.

## Settings

`exphelp.ini`:

```ini
[DocLanguages]
Default=eng-us
Directories[]
Directories[]=eng-us
Directories[]=ger-de
Map[]
Map[eng-GB]=eng-us
Map[ger-AT]=ger-de
Names[]
Names[eng-us]=English
Names[ger-de]=German

[IndexSettings]
ExcludePatterns[]=doc/README.md
```

A locale is looked up as itself (`ger-de`), without its `@variant`, through `Map[]`, and then by its three letter
language (`ger-CH` finds `ger-de`). A `Map[]`
entry that names a directory `Directories[]` does not list is ignored.

## The command line

- `./console exp:help index` builds the index and search index of every language and reports, per language, how
  many pages are translated and how many are shown in English. `--language=ger-de` builds only that language.
- `./console exp:help search`, `show`, `check` and `pdf` read the default language, or the one of `--language=`.
- `./console exp:help pdf doc/eng-us/guides/caches.md` accepts a file name as well as an id.
- `./console exp:help maps --language=ger-de` checks the path map and the directory name map of a translation (see
  below); `--fix` writes them back merged and sorted.

## Several translators: merging .paths.json and .dirnames.json

A translation that translates its file names keeps two maps in its directory:

- `.paths.json`: translated path => path below `doc/eng-us/` (read by the help, see above);
- `.dirnames.json`: English directory name => translated directory name (`{"help": "hilfe", "guides":
  "anleitungen"}`). The help does not read it; it keeps translators who work in parallel on one name per directory,
  so the pages of `help/` all land in `hilfe/`.

Both are written the same way: a JSON object, keys sorted, one entry per line, a final newline
(`expHelpLanguage::encodePathMap()`). Two translators who add entries in different places then merge without a
conflict, and when git does stop at one, the rule is the union of both sides:

```bash
git merge origin/main                                  # conflict in doc/ger-de/.paths.json
./console exp:help maps --language=ger-de --fix            # both sides of the conflict, merged and sorted
./console exp:help maps --language=ger-de --fix --paths=my-paths.json --dirnames=my-dirnames.json   # and a list of your own
./console exp:help maps --language=ger-de                  # check: PASS, or what is wrong
git add doc/ger-de/.paths.json doc/ger-de/.dirnames.json
```

`--fix` reads both sides of the conflict markers git left in the file (also with a diff3 base section, and when the
comma of a side's last line was lost or doubled), adds the maps of `--paths=` and `--dirnames=`, and writes the union
(`expHelpLanguage::mergePathMaps()`). An entry is never lost silently: a path that the two sides give different
values keeps the file's own value (our side) and is reported as a `CONFLICT`, and so is a page that two translated
paths name (its id would name two files). The check reports, besides, a translated path that is not a file, an
original that is not a page, and a translated path whose directories are not named as `.dirnames.json` says (each
directory either has its translated name or keeps its English one). It exits with 1 when anything is wrong.

## Tests

`tests/tests/kernel/classes/help/expHelpTranslatedDocumentationTest.php` (no database):

- TD-01: every tracked file below `doc/` is in a language directory, in `doc/changelogs/` or `doc/README.md`;
- TD-02: no tracked text file names a path of before the move, apart from the history (released changelogs,
  history pages) and other repositories' `doc/` directories;
- TD-03 and TD-04: locales map to their directory, with the variant and language fallbacks, and the shipped settings
  match the built-in defaults;
- TD-05: the path map in both directions, malformed entries left out, file names turned into ids;
- TD-06 to TD-09: the index of a translation serves translated pages and marks the fallbacks, the original's index
  never reads a translation, the same id names the same page in every language, links resolve against the file, the
  search is per language, and a translation is classified like its original;
- TD-10: the path maps merge as a union (sorted, the same file whatever the order, a conflict kept from the first map
  and reported, a git conflict read as its two sides) and directory names follow `.dirnames.json`;
- TD-11: how much of the help each language has, the choices of the "Reading in" switch, and `?lang=` for the
  original;
- TD-12: a translated page is rendered from its file, so its links and images reach the original's directory
  (`../../eng-us/help/images/x.webp` from `doc/ger-de/hilfe/x.md`);
- TD-13: an anchor written for another language's version of a page maps to the shown page's heading at the same
  position, when the page is rendered and in the link check, in both directions.
