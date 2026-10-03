# File consistency check and the release file list

The administration interface can tell you which files of an installation were
changed after it was installed. Setup > System upgrade > **Check file
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

In the browser use **Setup > System upgrade > Check file consistency**; it reads
the same file.

## Create the list for a release (maintainers)

```bash
cd /path/to/docroot
bash bin/shell/generatefilelist.sh --dry-run   # count what would be listed
bash bin/shell/generatefilelist.sh --list      # what is left out, and why
bash bin/shell/generatefilelist.sh             # write share/filelist.md5
```

The defaults are what a release wants: everything that is part of the
installation is hashed, and everything that is not is left out. Left out by
default:

| Key | What is skipped |
|---|---|
| `git` | `.git`, `.svn`, `.hg` of the root and of every extension that is its own checkout |
| `var`, `tmp` | what the installation writes while it runs |
| `vendor`, `extension_vendor` | `vendor/`, `node_modules/` |
| `caches` | tool caches in the root |
| `local` | per-machine editor settings |
| `work` | working directories that are not part of a release |
| `backups` | `*~`, `#*#`, `.#*`, `*.orig`, `*.rej`, `*.bak`, `*.swp`, `*.swo`, `*.save` |
| `logs` | `*.log` |
| `os` | `.DS_Store`, `Thumbs.db`, `desktop.ini` |

Always excluded: `share/filelist.md5` itself (a list cannot contain its own
checksum).

| Option | Meaning |
|---|---|
| `--dry-run` | Count only, write nothing. |
| `--list` | Show what is excluded (implies `--dry-run`). |
| `--include=<key>` | Put an excluded group back in, for example `--include=vendor` to hash `vendor/`. An unknown key prints the list of known keys. |
| `--exclude=<pattern>` | Add an exclusion; may be repeated. A bare `*.ext` matches file names at any depth; a pattern with a slash or a wildcard is a path anchored at the root. |
| `--extensions` | Also rewrite `extension/*/share/filelist.md5` for extensions that already ship one (off by default). |
| `--untracked` | Also hash files that are not tracked by git. |

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

In the 6.0.14 release (June 2026) the list was regenerated six times in two days
while last fixes landed; that is the symptom of step 1 being done in the wrong
order, and the reason for the rule.

## Related

[Changelog 6.0.14](../../changelogs/6.0/6.0.14.md),
[Rebranding](rebranding-to-exponential.md),
[Chronicle: April 2026](../../history/2026/2026-04.md).
