# Static publishing: history and rollback

This guide is for administrators of an Exponential 6.0.15+ site that is published as a static copy with
`exp:static:publish`. It shows how to read the history of the publishes and how to put an earlier publish back online
when a publish went wrong, without losing anything: a rollback is a new publish, never a rewrite of the history.

[Guides](README.md) · Related: [Repositories and credentials](static-publishing-targets-and-credentials.md),
[Host static sites on GitHub Pages](host-static-sites-on-github-pages.md), [Static publishing](../features/6.0/static-publishing.md)

## What you'll have at the end

- The list of a target's publishes with their tags, push states and who ran them.
- The public site back at an earlier publish, pushed as a new commit with its own tag.
- A rollback you can roll back again, since nothing was deleted.

## How the history is kept

Every publish that changes something is one commit on the target's branch with an annotated tag
`static/<target>/<n>`, counted from 1. Every rollback is a publish too. Tags are never moved or replaced, and the
push is never forced, so the history on the git host is the same as in the target's folder and only ever grows.

The table `exp_static_run` keeps one row per publish and rollback: the run id, the kind (`publish`, `rollback`), the
state (`done`, `unchanged`, `failed`), the pages, files and bytes, the files added, changed and removed, the commit,
the tag, the push state, who ran it and when.

## 1. Read the history

```bash
./console exp:static:status github-public --limit=20
```

```text
  Run                     Kind      State      Tag                          Push       By
  20261103-091500-77d0e1  publish   done       static/github-public/4       pushed     cli alpha
  20261102-170210-1c9b3a  publish   done       static/github-public/3       pushed     cli alpha
  20261102-101145-a3e872  publish   unchanged  -                            none       cli alpha
  20261101-160000-0b81d2  publish   done       static/github-public/2       pushed     cli alpha
  20261101-120000-4f2a9c  publish   done       static/github-public/1       pushed     cli alpha
```

The tags are in the git host's repository too (GitHub: **Releases > Tags**), and in the folder:

```bash
git -C var/site/static-publish/github-public log --oneline --decorate
```

## 2. See what a rollback would change

Name the publish to go back to, by its number or its tag:

```bash
./console exp:static:rollback github-public 3
```

```text
Static publishing: rollback of github-public, branch gh-pages in var/site/static-publish/github-public
  rolling back to static/github-public/3 would add 1, change 4 and remove 0 file(s); run again with --apply
    A	news/item-1/index.html
    M	.static-publish/manifest.json
    M	index.html
    M	news/index.html
    M	sitemap.xml
PASS
```

Without `--apply` nothing changes: no commit, no tag, no row in `exp_static_run`.

## 3. Roll back

```bash
./console exp:static:rollback github-public 3 --apply
```

```text
  committed 9a7c55e012: the files of static/github-public/3 (1 added, 4 changed, 0 removed)
  tagged static/github-public/5
  pushed gh-pages and the tags static/github-public/* to https://github.com/owner/my-site.git
  Commit: 9a7c55e0124b, tag static/github-public/5
  Push:   pushed (https://github.com/owner/my-site.git, credential token:github_pages_token)
PASS
```

What happened:

- A new commit on top of the branch holds exactly the files of publish 3, the manifest included. The commits of
  publish 4 stay in the history.
- It is tagged with the next number, 5. Tags 1 to 4 are unchanged.
- It is pushed as any publish, as a fast-forward. When the push fails, it waits for `exp:static:push` like any
  publish.
- The pages in `exp_static_page` are those of publish 3, and `exp_static_run` has a row of kind `rollback`.
- The audit records `system.staticpublish.rollback` with the tag rolled back to.

`--message` sets the commit message (default `Roll back github-public to static/github-public/3`); `--no-push`
commits and tags without pushing.

To undo the rollback, roll back to 4.

## After a rollback

The site's content has not changed: the next `exp:static:publish --build` builds the current content again and
publishes it as 6. Fix the content first (or leave the target out of automatic publishing) if the rollback was
because of a page.

A rollback refuses to start while the folder holds a build that was not published (`has changes that are not
committed`): publish it, or build again, first.

## Who may do it

From the console, whoever may run commands as the site's system user. The screens of static publishing in the
administration, which follow, will ask for the policy `staticpublish/publish` and "Verify it's you".

## Next step

[SSH deploy keys](static-publishing-deploy-keys.md): sign in to one repository with a key of its own.
