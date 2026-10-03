# ezpm: the package manager on the command line

`ezpm.php` is the command line front end of the package manager. It creates,
fills, exports, imports, lists and installs packages (`.ezpkg` archives holding
content, classes, templates and files) without a browser. Since July 2026
(release 6.0.15 development, commits `868f7a625c`, `688f48e93f`, `fc338f04b7`,
`f090f202a1` and others) it is reachable through the Exponential Console, shows
how far it has got, installs big packages without locking the database, and can
export a whole content tree.

Use it when you move a site or a part of a site between installations, build a
demo package, or install a package on a server where the browser would time out.

## Run it

All commands start in the project root.

```bash
# list the commands the console knows; ezpm appears under its own namespace
php bin/php/console list ezpm

# the same script, called directly
php ezpm.php help
php ezpm.php help add
```

`php bin/php/console ezpm help add` prints the help of the `add` command; `./console list ezpm`
shows the one entry the console has for it (checked on 6.0.15: `ezpm  Create, import, install, list and delete
Exponential packages`). `ezpm.php` ran as the operating system user `root` without
`--allow-root-user` in the check that wrote this page; other scripts do ask for it, see
[the console](../../bc/6.0/console.md).

Global options (from `php ezpm.php help`):

| Option | Meaning |
|---|---|
| `-s`, `--siteaccess` | Siteaccess to use. Without it the `DefaultAccess` of `site.ini` is used (since `9617bdcea4`). |
| `-q`, `--quiet` | No output except errors. |
| `-d`, `--debug` | Show debug output at the end. |
| `-l`, `--login`, `-p`, `--password` | Log in as this user for all operations. |
| `-r`, `--repos` | Repository to look for packages in. |
| `--db-type`, `--db-name`, `--db-user`, `--db-password`, `--db-host`, `--db-socket` | Override the database connection. |
| `-c`, `--colors` / `--no-colors` | ANSI colours on (default) or plain output. |
| `--logfiles` / `--no-logfiles` | Create log files, or not (default: not). |

Commands: `create`, `add`, `set`, `delete`, `export`, `import`, `install`,
`list`, `info`, `help`.

## Package a part of the content tree

A content tree package is built in three steps: create, add, export.

```bash
# 1. an empty package
php ezpm.php create mypackage 'My package summary' 1.0.1 install

# 2. add a node and everything under it (node id 2 = the content root)
php ezpm.php add mypackage ezcontentobject 2

# 3. write mypackage.ezpkg into a directory
php ezpm.php export mypackage -d var/tmp
```

`add ezcontentobject` takes a node id or a URL path as its starting point and
these options (all in `php ezpm.php help add`):

| Option | Effect |
|---|---|
| `--include-classes` / `--exclude-classes` | Put the content classes in the package (default) or leave them out. |
| `--include-templates` / `--exclude-templates` | Put the template overrides in the package (default) or leave them out. |
| `--node-main` / `--node-selected` | Export only the main node assignment (default) or all selected assignments. |
| `--siteaccess=sa,sa2` | Siteaccesses to collect templates from. |
| `--language=loc1,loc2` | Languages to export (default: all). |
| `--current-version` / `--all-versions` | Current version only (default) or every version. |
| `--minimal-template-set` | Only the minimal set of templates. |

`ezcontentsubtree` is an alias of `ezcontentobject` (`6ec591c375`), which reads
better when you mean "this node and its subtree":

```bash
php ezpm.php add mypackage ezcontentsubtree /content/my-page --exclude-classes --siteaccess=site
```

While the package is built, the status line names the phase: *Collecting subtree
nodes*, *Fetching node objects*, *Serializing content objects*. A missing initial
language or a missing serialized attribute no longer stops the export (August,
`752adad843`, `8af48c1e59`); the item is skipped.

## Install a package

```bash
# install into the content root (node 2, the default)
php ezpm.php install mypackage

# install the content objects under another parent node
php ezpm.php install mypackage -d 61
php ezpm.php install mypackage --destination-node-id 61
```

What the installer does differently since July:

- **One run at a time.** `install` and `add` take a lock (a file named `ezpm.lock` in the var directory).
  A second run while the first is going stops
  with *Another ezpm process is currently running*. This prevented the database
  lock wait timeouts two parallel runs caused.
- **Fast by design.** For `install` and `add` the view cache and the template
  cache are switched off and search indexing is delayed for the run, because no
  page is served from it. When it is over, rebuild the search index with
  `php bin/php/updatesearchindex.php` and clear the caches
  (`php bin/php/ezcache.php --clear-all --allow-root-user`).
- **Batched commits.** The content object handler commits the transaction every
  50 objects, so a package of tens of thousands of objects does not hold locks
  for the whole run. Each object is validated first; a failed object is named by
  its remote id in the error.
- **Progress with an estimate.** A single status line shows the command, a
  percentage with `current/total`, the elapsed time and the expected end time.
  It respects `--quiet`.
- **No duplicates.** Install items are deduplicated, so repeated items do not
  process twice or add versions.
- **Safe without a terminal.** Destructive class replacement is skipped when the
  install is not interactive.
- **Dependencies first.** Packages listed under `requires` in the package are
  installed before the package itself (July 29, `cb57cdd026`), even when the
  package is import only. A missing required package is a warning; a failing one
  stops the install.

## Limits

- The lock is per installation (`var/`), not per host.
- Delayed indexing means search results appear after you rebuild the index.
- Install into a copy first: the installer writes content.

## Related

- [A package installer that survives big packages](package-installer-batching.md): the
  browser installer (June 2026).
- [Kickstarter CLI](kickstarter-cli.md): installs a whole site from `kickstart.ini`.
- [Clean install defaults](clean-install-defaults.md)
- Month pages: [July 2026](../../history/2026/2026-07.md), [August 2026](../../history/2026/2026-08.md) (export fixes).
- Changelog: [6.0.15](../../changelogs/6.0/6.0.15.md); upgrade notes: [Behaviour changes of July and August 2026](../../bc/6.0/behaviour-changes-2026-07-08.md); [Console](../../bc/6.0/console.md).
