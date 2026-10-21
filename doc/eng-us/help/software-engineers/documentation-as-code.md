---
title: Documentation as code
audience: software-engineer, developer
category: software-engineering
order: 920
summary: How Exponential treats its documentation like code - Markdown files in doc/ versioned with the kernel, served by the help system inside the administration, front matter that drives audiences and search, a link and image checker on the command line, documentation shipped with extensions, translations, the GNU FDL, and the rules for writing a page with a change.
keywords: documentation as code, docs as code, help system, exphelp, exp:help check, front matter, Markdown, doc/, link check, audiences, extension documentation, GNU FDL, DITA, translations, software engineer
type: help
---
# Documentation as code

This page is for engineers who write documentation with their change, or who maintain the documentation of an
extension. In Exponential the documentation lives in the repository, is reviewed in the same pull request as the code,
is checked by a command, and is read inside the product. The feature page is [The help system](../../features/6.0/help-system.md).

## Contents

1. [Where the documentation lives](#where-the-documentation-lives)
2. [How it is served](#how-it-is-served)
3. [Front matter](#front-matter)
4. [Checking pages](#checking-pages)
5. [Documentation of extensions](#documentation-of-extensions)
6. [Writing rules](#writing-rules)
7. [License](#license)

## Where the documentation lives

| Kind | Directory |
|---|---|
| Help pages by audience | `doc/eng-us/help/` (this page is in `doc/eng-us/help/software-engineers/`) |
| Feature pages | `doc/eng-us/features/6.0/` |
| Upgrade notes | `doc/eng-us/bc/6.0/` |
| Guides | `doc/eng-us/guides/` |
| Standards and specifications | `doc/eng-us/standards/`, `doc/eng-us/specifications/` |
| Installation book | `doc/eng-us/install/` |
| Changelogs | `doc/changelogs/` |
| Other languages | `doc/ger-de/` and further directories of `exphelp.ini [DocLanguages]` |

## How it is served

The help system shows every **git-tracked** Markdown and text file below `doc/` in the administration
(`/exphelp/viewer/<path>`), with headings, a table of contents, tables, highlighted code, the navigation of its
section and a link to the file on GitHub; a search finds pages by title, headings and text with filters for audience,
category and type. Reading the help needs the policy `exphelp/read`. A file that git does not track is not a page, so
a new page appears in the help once it is committed.

## Front matter

Help pages start with a YAML-style block:

| Key | Purpose |
|---|---|
| `title` | the page title, also the H1 |
| `audience` | one or more audiences, which drive the filters and the dashboard cards |
| `category` | the category in the help |
| `order` | the order within the folder |
| `summary` | the description in search results and listings |
| `keywords` | extra search terms |
| `featured` | shown among the featured pages |
| `type` | the kind of page, for example `help` |

## Checking pages

```bash
./console exp:help check --below=help/software-engineers/   # links and images of the pages below a path
./console exp:help search "clear cache"                      # what readers will find
./console exp:help show guides/caches.md                     # the metadata and headings of one page
./console exp:help index --rebuild                           # rebuild the index
```

The check reports errors (a link to a missing file, a link that leaves the installation, an unsafe scheme, a broken
image), warnings (an anchor no heading has, an absolute link) and notices (a link to a file that is not a help page).
`--strict` makes warnings fail too. Use relative links only, and link anchors to headings that exist.

## Documentation of extensions

The help shows the documentation every active extension brings (its `doc/` directory, README, CHANGELOG, LICENSE)
under `/exphelp/extensions`, in the same viewer and search ([Help system for extensions](../extension-development/part-8-reference/help-system-for-extensions.md),
[Documenting your extension](../extension-development/part-3-extension-developer/documenting-your-extension.md)).

## Writing rules

- Write what exists. Verify every claim in the code, the settings or another page, and say so where the sources do
  not answer.
- Name the product Exponential. Identifiers such as `eZContentObject` stay as they are; the old product name appears
  only as history.
- Real commands only in code blocks; no host names, accounts or passwords of a real installation.
- American English in the interface and the documentation.
- A behavior change gets its upgrade note, a feature its page, a release its changelog
  ([Changelogs and upgrade notes](changelogs-and-bc-notes.md)).

Help topics maintained as content in the administration follow their own style guide and review states
([Help style guide](../../specifications/6.0/help-style-guide.md), [Keeping the help up to date](../maintaining-help-topics.md)).

## License

The documentation is free under the GNU Free Documentation License 1.3; the code is under the GNU GPL, version 2 or
later ([Free documentation](../free-documentation.md), [Documentation and publishing standards](../../standards/documentation-and-publishing.md)).

## Related pages

- [The help system](../../features/6.0/help-system.md)
- [Help standards](../../specifications/6.0/help-standards.md)
- [Contributing upstream](contributing-upstream.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
