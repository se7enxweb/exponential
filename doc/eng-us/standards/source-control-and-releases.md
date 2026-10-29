---
title: Source control and releases
audience: developer, admin
category: standards
order: 100
summary: The Git conventions of Exponential - branches, merges, commit messages and changelogs - and its release policy - permanent tags, how version numbers are chosen, extension metadata and Packagist.
keywords: git, source control, version control, branch, main, merge commit, pull request, squash, force push, commit message, Added, Updated, Fixed, Removed, Renamed, changelog, release, tag, annotated tag, versioning, Semantic Versioning, Packagist, webhook, ezinfo.php, extension.xml, gitattributes, gitignore, signed commits, Git LFS
type: reference
---
# Source control and releases

This page is for developers who commit to Exponential or to one of its extensions, for maintainers who cut releases,
and for administrators who want to know what a version number promises. It answers: how the Git history is kept,
how commit messages are written and checked, where changelogs live, and how releases are numbered, tagged and
published. If you are new to Git, start with [Develop with Git](../help/develop-with-git.md).

## Summary

| Practice | Status | Evidence (short) |
|---|---|---|
| One development branch, `main` | **Complies** | default branch `main`; CI runs on `main`/`master` |
| Pull requests squashed and rebased onto `main` by their authors while open, then merged with merge commits | **Complies** (by policy) | [Continuous integration](../bc/6.0/ci.md), "The commit message policy"; merge commits in `git log` |
| Signed CLA before a kernel contribution is merged | **Complies** (by policy) | `CONTRIBUTING.md`, "Copyright assignment (CLA)"; CLA checkbox in `.github/PULL_REQUEST_TEMPLATE.md`; no automated check |
| No force push, no rewriting of published history | **Partly complies** | a policy; `main` has no branch protection rule (GitHub API, 2026-10-18) |
| Commit subject prefixes | **Complies** | `.githooks/commit-msg`, `.githooks/check-commit-range`, `quality.yml` |
| Other commit message rules (product name, no tool attribution, no private paths) | **Unverified** | policy only, no automated check |
| Changelogs per version | **Complies** | `doc/changelogs/<series>/<version>.md`, `doc/changelogs/extensions/` |
| Permanent tags and releases | **Complies** (by policy) | [Extensions guide](../guides/extensions.md), "Release it"; [Velocity changelog](../../changelogs/extensions/exponential-velocity.md) |
| Version numbering: last position counts on | **Complies** | `0.0.4.9` followed by `0.0.4.10` in the Velocity history |
| Extension metadata matches the release | **Complies** (by policy) | `ezinfo.php` `'Version'` and `extension.xml` `<version>` |
| Packagist publication | **Complies** | Packagist webhook on the repository; push gate checks constraints |
| Signed commits or tags | **Does not comply** | 199 of the last 200 commits on `main` carry no signature |
| Git LFS | **Not applicable** | no LFS configuration in `.gitattributes` |

## Branches and merges

- **`main` is the development line.** It is the default branch on GitHub, `composer.json` aliases it as `6.0.x-dev`
  (`extra.branch-alias`), and the CI workflows run on pushes and pull requests to `main` (and `master`).
- **Work happens on branches** and reaches `main` by pull request or by merge (`CONTRIBUTING.md`, "Making
  Changes": "Always create a new branch for your work").
- **Pull requests are squashed and rebased by their authors while open, then merged with a merge commit.** 7x
  strongly requests and requires contributors to squash their pull request into logical commits (usually one) and
  rebase it onto `main` while it is open (`git rebase -i origin/main`, `git push --force-with-lease` to the pull
  request branch only), so the merge is clean ([Code reviews](../../../CONTRIBUTING.md#code-reviews)). Once merged,
  `main`'s history is never rewritten, and later fixes come as new pull requests
  ([Continuous integration](../bc/6.0/ci.md), "The commit message policy"). A contributor's commit with a
  non-standard subject is only a warning, so a merged pull request never turns `main` red. `git log --merges` on
  `main` shows merge commits.
- **A kernel contribution is merged once its author's CLA is on file.** 7x and the Exponential Foundation require a
  signed Contributor Licensing Agreement for every code contribution to the core kernel, whether it arrives as a
  pull request, a patch, an issue or by mail ([Copyright assignment (CLA)](../../../CONTRIBUTING.md#copyright-assignment-cla)).
  By signing and submitting, the contributor assigns the copyright in the contribution to the Exponential Foundation and 7x, which become its owners, and gives up control over it: the Exponential Foundation and 7x may use, change, relicense and distribute it in any way, and the contributor cannot withdraw it or set conditions on it. The pull request template has a checkbox for it. Users' own custom extensions
  are not affected.
- **No force push and no rewriting of published history.** A commit on `main` is public; a rewrite would move every
  later commit and strand any tag that points into the range. The one exception is a contributor's own pull request
  branch while the pull request is open: it is squashed, rebased and pushed with `git push --force-with-lease`, as
  above.

> **Warning** These merge and push rules are policy. The repository settings on GitHub still allow squash and rebase
> merges, and `main` has no branch protection rule or ruleset (checked through the GitHub API on 2026-10-18).
> Nothing technical stops a force push by someone with write access.

## Commit messages

**The subject starts with what the change does:**

| Prefix | For |
|---|---|
| `Added: ` | a new feature, file, class, setting or capability |
| `Updated: ` | a change to existing behavior, templates, settings or code |
| `Fixed: ` | a bug fix |
| `Removed: ` | something deleted |
| `Renamed: ` | a renamed file, class, setting or extension |

Example: `Fixed: The redirect keeps the query string on the own host` (from `.githooks/commit-msg`).

- Checked by `.githooks/commit-msg` at `git commit` (after `make hooks`) and by `.githooks/check-commit-range` in CI
  (`quality.yml`, step "Commit messages") and in the push gate. Git's own `Merge`, `Revert`, `fixup!`, `squash!` and
  `amend!` subjects pass. Merge commits are not checked.
- A **project author** (an address in `.githooks/commit-msg-authors`) fails the check with a bad subject; a
  **contributor** gets a warning. `EXP_COMMIT_MSG_STRICT_ALL=1` checks every author strictly.
- **Do not combine change types** in one commit when they can be split; if one change touches several areas, list
  them in the body.

**The body says why and what**, written for a reader who does not know the context.

Further rules of the project, not checked by any tool:

- **Name the product Exponential.** Kernel identifiers such as `eZContentObject`, `eZINI` or `bin/php/ezcache.php` are
  names of code and stay as they are; only the product name is written as Exponential.
- **No attribution to tools or assistants**: no generated-by footers, no co-author trailers for tools.
- **No private or unpublished paths** in a commit message, tag message, release note or pull request description:
  describe what was verified and how, not which private helper did it.
- **No credentials** and no machine-specific settings in commits: `.gitignore` excludes `/config.env.php`,
  `/composer.lock`, `/var/storage/`, `/var/log/`, `/.devtools/` and similar.

## Changelogs

- Kernel changelogs: `doc/changelogs/<series>/<version>.md`, for example
  [6.0.15](../../changelogs/6.0/6.0.15.md) (in development; "Not released yet" on its first line). Older series back to
  2.9 are kept in `doc/changelogs/`.
- Extension changelogs: `doc/changelogs/extensions/<name>.md`, for example
  [exponential-velocity.md](../../changelogs/extensions/exponential-velocity.md).
- Entries are assembled from the commit history and grouped by the same prefixes (Added, Updated, Removed, Renamed;
  fixes are listed under Updated in the Velocity changelog).
- Upgrade-relevant changes get a page in `doc/eng-us/bc/6.0/` linked from the changelog.

## Releases

### Tags are permanent

A tag, and the GitHub release made from it, is immutable once pushed. Composer and Packagist read a tag once and
cache the version they found; a moved tag would give two different packages under one version number.

- **Never delete, move or re-cut a pushed tag**, and never replace the assets of a published GitHub release.
- **A bad release is followed by the next version**, whose notes say what was wrong with the previous one.
- **Check before you push**: a clean working tree, passing tests, and build artifacts that match their sources.

This is written down in the [Extensions guide](../guides/extensions.md), "Release it" ("A published tag can never be
corrected") and in the [Velocity changelog](../../changelogs/extensions/exponential-velocity.md) ("A tag is
permanent: a bad release is corrected by the next one, never re-cut").

### Choosing the next version number

Always from what is already published, never from memory:

```bash
git fetch --tags
git tag -l 'v*' --sort=version:refname | tail -5
gh release list --limit 10
```

- Sort by version: by name, `v0.0.4.10` sorts before `v0.0.4.8`.
- Compare with `gh release list`: a tag can exist without a release, and a release can exist for a tag the local clone
  has not fetched.
- **The last position counts on**: `0.0.4.9` is followed by `0.0.4.10`, never `0.0.5.0`. Raising a higher position is
  a separate decision about what the release means. Composer and Packagist compare each position numerically, and a
  tilde constraint such as `~0.0.4.9` (`>=0.0.4.9 <0.0.5.0`) already accepts `0.0.4.10`.

### The kernel's versions

- The kernel version is in `lib/version.php` (`ExponentialSDK`: `VERSION_MAJOR = 6`, `VERSION_MINOR = 0`,
  `VERSION_RELEASE = 15`). The root `composer.json` carries no `version` field; Composer takes the version from the
  tag, and `main` is `6.0.x-dev`.
- Published tags `v6.0.0` to `v6.0.14` exist; GitHub releases exist for `v6.0.10` to `v6.0.14` (the latest,
  "Exponential 6.0.14", on 2026-06-07). `v6.0.10` to `v6.0.12` are lightweight tags, `v6.0.13` and `v6.0.14` annotated
  (checked with `git for-each-ref`).

### Extension releases

Every release of an extension writes its number into the extension, in the commit that the tag points to:

1. Choose the number as above.
2. Set `'Version'` in `ezinfo.php` and `<metadata><version>` in `extension.xml` to it, without the `v` prefix (tag
   `v1.8.4` gives `1.8.4`). Never leave `//autogentag//` or `dev-master`. Both files also state the license and an
   `info_url` on `https://github.com/se7enxweb/<repository>`. Example: `extension/ezjscore/ezinfo.php` and
   `extension/ezjscore/extension.xml` both say `1.8.4`.
3. Move any other version field the repository keeps (`version` in its `composer.json`, the header of its own
   `share/filelist.md5`) and refresh that manifest's checksums.
4. Check: `php -l ezinfo.php` and `xmllint --noout extension.xml`.
5. Commit ("Updated: Version 1.8.4" or as part of the last commit), **then** tag that commit, then push both. Never
   tag first and bump afterwards: the tag would carry the old number forever.

The procedure with commands is in the [Extensions guide](../guides/extensions.md), section "Release it". How the
about page reads the two files is in [About and package pages](../features/6.0/about-and-package-pages.md).

### Packagist

- Each `se7enxweb/*` repository is a Composer package on Packagist. A webhook on the GitHub repository tells Packagist
  about new tags; the root repository has one (GitHub API, 2026-10-18). A package without the webhook stays at an old
  version on Packagist until someone updates it by hand.
- Repinning the root `composer.json` follows the release: release the extension, see the version on Packagist, then
  commit the new constraint. The push gate refuses a constraint Packagist cannot satisfy yet
  ([Continuous integration](../bc/6.0/ci.md), "Packagist").

## Repository files

- **`.gitattributes`**: one line, `*.js linguist-detectable=false`, so GitHub's language statistics show the
  repository as PHP. No `export-ignore`, no `eol` rules, no Git LFS filters.
- **`.gitignore`**: runtime and cache directories (`/var/cache/`, `/var/log/`, `/var/storage/`, `/var/autoload/`),
  `/vendor` and `/composer.lock`, development tools (`/.devtools/`), the CI's temporary Composer overlay
  (`/composer.ci-phpunit.json`), machine settings (`/config.env.php`), editor folders and private working notes.
- **`share/filelist.md5`**: every committed file must have its current checksum here; see
  [Testing and continuous integration](testing-and-ci.md).
- **Signed commits**: not required. 199 of the last 200 commits on `main` have no signature (`git log --format=%G?`).
- **Git LFS**: not used.

## Known gaps

1. Merge style, no force push and no history rewriting are policy only: no branch protection, squash and rebase
   merges are allowed in the repository settings.
2. The commit message rules beyond the prefix (product name, no tool attribution, no private paths) are not checked
   by any tool.
3. Commits and tags are not signed.
4. Older kernel tags (`v6.0.10` to `v6.0.12`) are lightweight; tag messages exist only from `v6.0.13` on.
5. `doc/changelogs/README.md` still points to the releases page of the old upstream repository for newer releases.

## Related pages

- [Develop with Git](../help/develop-with-git.md)
- [Extensions guide](../guides/extensions.md)
- [Continuous integration](../bc/6.0/ci.md)
- [Testing and continuous integration](testing-and-ci.md)
- [PHP, PHP-FIG and Composer standards](php-and-composer.md)
- [Coding standards](coding-standards.md)
