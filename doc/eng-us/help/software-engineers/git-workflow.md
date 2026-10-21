---
title: Git workflow
audience: software-engineer, developer
category: software-engineering
order: 900
summary: How Git is used for Exponential - main as the one development line, branches and pull requests squashed and rebased while open, merge commits, no history rewriting after a merge, commit subjects with Added, Updated, Fixed, Removed or Renamed, the rules for message bodies, the hooks, and working with Git clones of the kernel and extensions.
keywords: git, workflow, branches, main, pull request, squash, rebase, force-with-lease, merge commit, commit message, Added, Updated, Fixed, Removed, Renamed, hooks, clones, software engineer
type: help
---
# Git workflow

This page is for engineers who commit to Exponential, review its history or run installations made of Git clones. The
standard is [Source control and releases](../../standards/source-control-and-releases.md); the hands-on guides are
[Develop with Git](../develop-with-git.md) and [Update with Git](../update-with-git.md).

## Contents

1. [Branches and merges](#branches-and-merges)
2. [Commit messages](#commit-messages)
3. [Hooks](#hooks)
4. [What never goes into a commit](#what-never-goes-into-a-commit)
5. [Installations made of clones](#installations-made-of-clones)
6. [Known gaps](#known-gaps)

## Branches and merges

- **`main` is the development line**, aliased `6.0.x-dev` for Composer; CI runs on it.
- **Work happens on branches** and reaches `main` by pull request.
- **While a pull request is open**, its author squashes it into logical commits (usually one) and rebases it onto
  `main` as the reviews ask:

  ```bash
  git fetch origin
  git rebase -i origin/main
  git push --force-with-lease
  ```

  Force-push only the branch of your own pull request.
- **Pull requests are merged with a merge commit.** Once merged, `main`'s history is never rewritten; later fixes come
  as new pull requests.

## Commit messages

The subject starts with what the change does:

| Prefix | For |
|---|---|
| `Added: ` | a new feature, file, class, setting or capability |
| `Updated: ` | a change to existing behavior, templates, settings or code |
| `Fixed: ` | a bug fix |
| `Removed: ` | something deleted |
| `Renamed: ` | a renamed file, class, setting or extension |

Example from the hook: `Fixed: The redirect keeps the query string on the own host`. Git's own `Merge`, `Revert`,
`fixup!`, `squash!` and `amend!` subjects pass.

The body says why and what, for a reader who does not know the context. Further rules, not checked by any tool: name
the product Exponential (identifiers such as `eZContentObject` stay), no attribution to tools or assistants, no
private or unpublished paths, and do not combine change types in one commit when they can be split.

A project author's bad subject fails the check; a contributor's only warns. To check your branch strictly:

```bash
EXP_COMMIT_MSG_STRICT_ALL=1 sh .githooks/check-commit-range origin/main HEAD
```

## Hooks

```bash
make hooks
```

sets `core.hooksPath` to `.githooks`: the pre-commit hook checks syntax (also with PHP 8.0 when found), the file
manifest and the code style of staged files; the commit-msg hook checks the subject; the pre-push hook runs PHPStan
when the development tools are installed. Skip them only in an emergency (`--no-verify`); CI runs the same checks
([Static analysis and code style](static-analysis-and-code-style.md)).

## What never goes into a commit

`.gitignore` already excludes much of it; check anyway:

- credentials and machine settings: `settings/override/`, `config.env.php`;
- runtime directories: `var/cache/`, `var/log/`, `var/storage/`;
- `vendor/`, `composer.lock`, `.devtools/`;
- private working notes.

Stage files by name rather than with `git add -A`, so nothing untracked slips in.

## Installations made of clones

Composer can install the kernel and the extensions as Git clones. Then you can see what changed, find when and why a
line changed, bisect a regression, use a fix before it is released, and keep up to date with a merge
([Develop with Git](../develop-with-git.md)). Updating such an installation is a seven-step procedure with a
maintenance window, from the command line or from the administration ([Update with Git](../update-with-git.md)).

## Known gaps

The merge and push rules are policy: `main` has no branch protection rule, and commits and tags are not signed.

## Related pages

- [Contributing upstream](contributing-upstream.md)
- [Commit messages](../extension-development/part-5-extend-fork-contribute/commit-messages.md)
- [Running a team on Git](../leadership/running-a-team-on-git.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
