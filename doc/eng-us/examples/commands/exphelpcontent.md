---
title: exp:exphelpcontent
audience: developer, admin
category: examples
summary: "Help maintained as content: create the help classes, sections, states, tree and roles (idempotent), check the topics against the style guide, seed the glossary and topics, report and uninstall (the changing actions are a dry run unless --apply)."
keywords: exp:exphelpcontent, exphelpcontent, console, command, CLI, exphelpcontent.php, exp:help:content
type: generated
generated: exp:help:examples
---
# exp:exphelpcontent

Help maintained as content: create the help classes, sections, states, tree and roles (idempotent), check the topics against the style guide, seed the glossary and topics, report and uninstall (the changing actions are a dry run unless --apply).

Script `bin/php/exphelpcontent.php`, code in `kernel/private/classes/commands/exphelpcontent.php`. Run it from the installation root, as the user the web server runs as:

```bash
./console exp:exphelpcontent --help
./console exp:help:content --help
php bin/php/exphelpcontent.php --help
```

## Help text

```text
Help maintained as content (topics, manuals, glossary in the content tree)

./console exp:help:content install -s <admin siteaccess> [--apply]   tables, classes, tree and roles (all four steps)
./console exp:help:content tables -s ...     the tables exphelp_help_id (released help_ids) and exphelp_feedback
                                              ("Was this helpful?"), when an older installation lacks them
./console exp:help:content classes -s ...    the eight help_* classes in the class group Help (create_classes.php)
./console exp:help:content tree -s ...       sections, the state group help_status, the folder tree (create_tree.php)
./console exp:help:content roles -s ...      the roles Help editor, Help release and the Anonymous policy (create_roles.php)
./console exp:help:content check -s ... [--fix --apply]   the validator (check_help.php); --fix sets the sections from the audience
./console exp:help:content seed-glossary -s ... [--file=] [--update]   the glossary (seed_glossary.php)
./console exp:help:content seed-topics -s ... [--file=] [--update] [--release]   topics and manuals (seed_topics.php)
./console exp:help:content build-manuals -s ... [--manual=<key>] [--language=ger-DE,...] [--release] [--queue] [--apply]
                                              the manuals of the Markdown help (exphelp.ini [ManualSettings]):
                                              made, updated and removed so they match the pages (help-gen-...)
./console exp:help:content report -s ...     what is installed, the roles' assignments, the content by state
./console exp:help:content export -s ... --manual=help-manual-editors --format=epub|pdf [--output=file] [--language=] [--check]
                                              an e-book of the Released topics of a manual (export_manual.php);
                                              --check runs epubcheck and Ace by DAISY where the site has them
./console exp:help:content dita -s ... [--manual=<remote_id>] [--output=dir] [--dry-run] [--commit]
                                              the DITA 1.3 export (bookmaps, DITAVAL, topics, glossary) into a
                                              directory, checked after writing (also: export --format=dita);
                                              --commit records it in git (the cronjob part exphelp does it regularly)
./console exp:help:content feedback -s ... [--prune --apply] [--json]   the answers to "Was this helpful?" per topic
./console exp:help:content ebooks -s ... [--manual=<remote_id>] [--language=ger-DE] [--apply]   build the e-books of the
                                              Released manuals and store them as file objects under E-books
./console exp:help:content review -s ... [--json]   the topics whose review is overdue
./console exp:help:content cache -s ... [--json]   fill the cache of the help rows and search words of the
                                              siteaccess (after a deploy or ezcache --clear-id=exphelpcontent)
./console exp:help:content package -s ... [--output=dir] [--version=6.0.15]   the eight classes as the content
                                              class package exp_help_classes (a directory and an .ezpkg)
./console exp:help:content verify-package -s ... --file=<.ezpkg or directory>   compare a package with the
                                              classes of this release (no database)
./console exp:help:content uninstall -s ... [--with-content] [--apply]   take the roles away (and the tree, classes,
                                              state group and sections with --with-content)

-s (--siteaccess) is required: one tree may serve several installations with different databases. Every
step is idempotent: a second run prints "No change.". The actions that change the installation (install,
tables, classes, tree, roles, check --fix, seed-glossary, seed-topics, build-manuals, ebooks, feedback --prune,
uninstall) only show what would change unless --apply is given; --dry-run means the same as no --apply, both
together are a usage error (exit 2). export, dita, package and cache write their files as before (--dry-run
still shows the plan of export and dita); report, review and verify-package only read.
Exit code: 0 fine, 1 check found an ERROR (or a step could not be done), 2 error.
```

## Options

| Option | Value | Help |
|---|---|---|
| `--apply` |  | install, tables, classes, tree, roles, check --fix, seed-*, build-manuals, ebooks, feedback --prune, uninstall: make the changes (the default is a dry run) |
| `--dry-run` |  | Show what would change, change nothing (the default of the changing actions; export and dita: their plan) |
| `--check` |  | export: check the EPUB with epubcheck and Ace by DAISY ([ContentSettings] EpubCheckCommand, AceCommand); exit 1 when one fails |
| `--prune` |  | feedback: remove the answers older than [ContentSettings] FeedbackKeepDays |
| `--commit` |  | dita: commit the written files when the directory is a git repository (the history of delivery path 4) |
| `--fix` |  | check: set the section of each topic, figure, chapter and manual from the audience, and record the help_ids of Released topics |
| `--file` | required | seed-glossary, seed-topics: the data file (default share/help/seed-glossary.json, share/help/seed-topics.json) |
| `--update` |  | seed-*: also update existing objects that differ from the file |
| `--release` |  | seed-topics: set new topics, chapters and manuals to Released; build-manuals: set every generated one to Released |
| `--with-content` |  | uninstall: also the help tree (into the trash), the classes, the state group and the sections |
| `--login` | required | The user who creates the classes and objects (default admin) |
| `--json` |  | Print one JSON document instead of the text |
| `--manual` | required | export, dita, ebooks: the remote_id of the manual (help-manual-...); dita and ebooks without it: every manual; build-manuals: the key of one manual of [ManualSettings] Manuals[] |
| `--format` | required | export: epub, pdf or dita |
| `--output` | required | export: the file to write (default <cache>/exphelp/ebooks/<remote_id>.<format>); dita: the directory (default <cache>/exphelp/dita/all or /<remote_id>); package: the directory (default <cache>/exphelp/packages) |
| `--version` | required | package: the release, MAJOR.MINOR.PATCH (default: the version of this installation) |
| `--language` | required | build-manuals: the translations to write (default [ManualSettings] Languages[]; "none" for none); ebooks, export: the language of the edition (default: the default language) |
| `--queue` |  | build-manuals: queue the build as a background content job (helpmanuals) and return at once; nothing is queued while one runs |

Read from the option definitions in the code (`startup()`, `getOptions()`, `options()` or `ezcConsoleOption`). Every script also takes the standard options of the script framework (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Related

- [All console commands](README.md)

---

Generated by `./console exp:help:examples` from `bin/php/exphelpcontent.php`, `kernel/private/classes/commands/exphelpcontent.php`. Do not edit this page: change the source and generate again ([how](../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../help/free-documentation.md).
