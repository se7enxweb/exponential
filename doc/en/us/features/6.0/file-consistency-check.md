# File consistency check and the release file list

This page is for administrators who want to know whether the files of an installation were changed after it was
installed, and for maintainers who cut releases. Setup > System upgrade > **Check file
consistency** compares every file with a list of checksums that ships with each
release, `share/filelist.md5`. Since April 2026 two small scripts create and
verify that list, so a release always carries a list that matches its files and
you can run the same check from the command line.

## Why you want it

- After an upgrade, to see at once whether anything was left behind or edited by
  hand.
- After an incident, to see whether a file was modified without anyone
  noticing.
- As a release gate for your own fork: if the list is not refreshed the admin
  check shows every changed file as "modified".

## Check an installation

Run it from the document root that the web server uses, because the paths in
the list are relative to it:

```bash
cd /path/to/docroot
bash bin/shell/verifyfiles.sh            # summary and the files that differ
bash bin/shell/verifyfiles.sh --quiet    # exit status only (0 = all files match)
```

Exit status `0` means every file matches. `1` means something changed, went
missing, or the list could not be read. The script runs `md5sum --check` on
`share/filelist.md5`.

In the browser use **Setup > Upgrade check > Check file consistency**; it reads
the same file, and the `share/filelist.md5` of every active extension that carries
one. Since October 2026 it groups what it finds (modified, missing, unreadable;
files git tracks but the manifest does not list, malformed and unsorted lines as
notes), shows the manifest's dates and which extensions it covers, has a search
and filters, says how to deal with each finding and offers the result as CSV or
text. The guide: [The upgrade check](../../guides/upgrade-check.md).

`php bin/php/checkmanifest.php` reads the manifests with the same class as the
page (`expFileConsistencyReport`), so both agree on the same tree:

```bash
php bin/php/checkmanifest.php --all                        # the manifest of Exponential
php bin/php/checkmanifest.php --all --extensions           # and every extension's own manifest
php bin/php/checkmanifest.php --all --extensions --csv     # the findings as CSV
```

A manifest line is "32 hex digits, two spaces, a path relative to the manifest's
directory". A line ending in CRLF is read like one ending in LF. A line that is
not in that form, names a path outside the installation or repeats a path is a
malformed line: it is skipped and reported, never followed. An extension's
manifest may start with `name:`, `version:` and `files_count:` lines.

## Create the list for a release (maintainers)

```bash
cd /path/to/docroot
bash bin/shell/generatefilelist.sh --help      # every option and exclusion key (instant)
bash bin/shell/generatefilelist.sh --dry-run   # count what would be listed
bash bin/shell/generatefilelist.sh --list      # what is left out, and why
bash bin/shell/generatefilelist.sh             # write share/filelist.md5
```

On a large installation with many extension checkouts `--dry-run` and `--list`
walk the whole tree and can take minutes (measured: more than two minutes on a
large development installation); run them from a shell without a short timeout.

The defaults are what a release wants: everything that is part of the
installation is hashed, and everything that is not is left out. Left out by
default:

| Key | What is skipped |
|---|---|
| `git` | `.git`, `.svn`, `.hg` of the root and of every extension that is its own checkout |
| `var`, `tmp` | what the installation writes while it runs |
| `vendor`, `extension_vendor` | `vendor/`, `node_modules/` |
| `caches` | tool caches in the root (`.phpunit.cache/`, `.sass-cache/`, `.pytest_cache/`) |
| `local` | per-machine editor and tool settings (`.idea/`, `.vscode/`, `.cursor/` and similar) |
| `work` | a private working directory of the maintainers that is not part of a release (the exact name is printed by `--help`) |
| `backups` | `*~`, `#*#`, `.#*`, `*.orig`, `*.rej`, `*.bak`, `*.swp`, `*.swo`, `*.save` |
| `logs` | `*.log` |
| `os` | `.DS_Store`, `Thumbs.db`, `desktop.ini` |

Always excluded: `*.pyc` files, the temporary `filelist.md5.tmp.*` and `share/filelist.md5` itself (a list cannot contain its own
checksum).

| Option | Meaning |
|---|---|
| `--dry-run` | Count only, write nothing. |
| `--list` | Show what is excluded (implies `--dry-run`). |
| `--include=<key>` | Put an excluded group back in, for example `--include=vendor` to hash `vendor/`. An unknown key prints the list of known keys. |
| `--exclude=<value>` | Add an exclusion; may be repeated and combined with `--include`. Three forms: a bare name such as `scratch` is a directory wherever it sits; a `find -path` pattern such as `./scratch/*` is used as given; a file glob such as `*.log` matches file names at any depth. |
| `--extensions` | Also rewrite `extension/*/share/filelist.md5` for extensions that already ship one (off by default). |
| `--untracked` | In a git checkout only tracked files are hashed by default; this also hashes files git does not track. |

### Run it from the right directory

If your public site is served from a docroot that is built from symbolic links
to the real installation, generate the list in the directory the web server
actually uses, not in the real installation: PHP's current directory during a
web request is the docroot, and that is where the admin check looks for the
files.

## Release rules for maintainers

1. Finish every change first, then generate the list; any later edit makes the
   file show as modified.
2. Commit the list in the same change that bumps the version.
3. An extension with its own `share/filelist.md5` needs its own refresh in its
   own release.

In the 6.0.14 release (June 2026) the list was committed seven times between 5 and 7 June (`git log --format="%ad %h" --date=short v6.0.13..v6.0.14 -- share/filelist.md5`)
while last fixes landed; that is the symptom of step 1 being done in the wrong
order, and the reason for the rule.

The full detail of the options is the script's own help: `bash bin/shell/generatefilelist.sh --help`.

## Related pages

- [Package licenses and Semantic Versioning](package-licenses-and-versions.md), [the product is called Exponential](rebranding-to-exponential.md)
- [Changelog 6.0.14](../../changelogs/6.0/6.0.14.md)
- History: [April 2026](../../history/2026/2026-04.md), [May 2026](../../history/2026/2026-05.md), [June 2026, first half](../../history/2026/2026-06a.md), [June 2026, second half](../../history/2026/2026-06b.md)
