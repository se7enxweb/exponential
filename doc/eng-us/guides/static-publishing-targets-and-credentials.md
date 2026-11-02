# Static publishing: repositories and credentials

This guide is for administrators and developers who publish a static copy of an Exponential 6.0.15+ site to a git
host: GitHub Pages, GitLab Pages, Gitea, or a repository on your own server. It explains where the copy's git
repository lives, what one publish commits, how the push signs in (a token, an SSH deploy key, or the host's own
credentials) and how a push that failed is sent again. At the end, `./console exp:static:publish <target>` builds,
commits, tags and pushes your site in one step.

[Guides](README.md) · Related: [Host static sites on GitHub Pages](host-static-sites-on-github-pages.md),
[History and rollback](static-publishing-history-and-rollback.md),
[SSH deploy keys](static-publishing-deploy-keys.md), [Static publishing](../features/6.0/static-publishing.md),
[Secret store](../features/6.0/secret-store.md)

## What you'll have at the end

- The target's folder `var/<site>/static-publish/<target>/` as a git repository of its own, on the target's branch,
  with the target's `Remote` as `origin`.
- Every publish as one commit with only the build's files, tagged `static/<target>/1`, `static/<target>/2` and on.
- A push that signs in without a token ever reaching a command line, a remote address or a log.
- A record of every publish in the table `exp_static_run`, shown by `exp:static:status`.

## Before you start

- A target that builds: [Host static sites on GitHub Pages](host-static-sites-on-github-pages.md), steps 1 to 4.
- git on the server (`git --version`), and OpenSSH for a deploy key (`ssh -V`).
- The tables of static publishing: `./console exp:upgrade --apply` runs the step `6.0.15-static-publish-tables`.
  Publishing works without them, but no run is recorded and `exp:static:push` finds nothing to retry.
- An empty repository on the git host, or one whose publishing branch you are happy for Exponential to commit to.

## 1. Name the repository

Add `Remote`, `Branch` and, for a site in `/docs`, `Folder` to the target in
`settings/override/staticcache.ini.append.php`:

```ini
[Target_github-public]
SiteAccess=site
BasePath=/my-site/
PublicURL=https://owner.github.io/my-site/
Remote=https://github.com/owner/my-site.git
Branch=gh-pages
Folder=
Credential=token:github_pages_token
```

| Setting | What it means |
|---|---|
| `Remote` | The git address: `https://...`, `git@host:owner/repo.git`, `ssh://...`, or a path on this server. Never a password or token in it; `exp:static:targets` refuses one. |
| `Branch` | The branch the site is published to: `gh-pages`, or `main` with `Folder=docs`. |
| `Folder` | Empty: the site is the whole branch. `docs`: the site is in `docs/` of the branch, next to your other files (GitHub Pages serves either). |
| `Credential` | How the push signs in, below. Only a name; the value stays in the secret store or the environment. |

The repository is the target's folder itself. It is never inside your site's own repository, so a publish can never
commit your code, settings or `var/`.

## 2. Choose how the push signs in

| Credential | When to use it | Remote |
|---|---|---|
| `token:<secret name>` | A fine-grained GitHub token, or a GitLab or Gitea access token, stored in Setup > Secrets | `https://...` |
| `token:env:<VARIABLE>` | The same token from an environment variable, e.g. in CI | `https://...` |
| `sshkey:<secret name>` | A deploy key for this one repository ([SSH deploy keys](static-publishing-deploy-keys.md)) | `git@host:owner/repo.git` |
| `none` | The host's own credentials: an SSH agent, a credential helper, or a remote on this server | any |
| `app:<installation>` | A GitHub App installation: a token made for each push from the App set in `GitHubAppId` and `GitHubAppKey` ([Connect to GitHub](../help/static-publishing/connect-to-github-token-key-or-app.md)) | `https://github.com/...` |

### A token

1. On GitHub, open **Settings > Developer settings > Personal access tokens > Fine-grained tokens** and make a token
   for the one repository with **Contents: Read and write**. On GitLab, a project access token with `write_repository`.
2. In Exponential, open **Setup > Secrets**, add a secret named `github_pages_token` and paste the token. Or put it
   in the environment of the process that publishes: `Credential=token:env:GH_PAGES_TOKEN`.
3. Set `Credential=token:github_pages_token` on the target.

How it reaches git: the push runs with `GIT_ASKPASS` set to `bin/shell/expstaticaskpass.sh`, and the token in the
environment variable `EXP_STATIC_GIT_TOKEN` of that one git process. git asks the helper for the user name and the
password; the helper answers from the environment. Credential helpers of the host are switched off for that call so
no stored password answers instead. The token is never part of a command line (where `ps` would show it), of the
remote address in `.git/config`, of a report or of a log line, and any git output that quotes it is redacted to
`[secret token:github_pages_token]` before it is shown or stored.

### None

With `Credential=none` git signs in as the system user that runs the command would: its SSH agent and keys, or a
credential helper set up for it. git never waits for a password (`GIT_TERMINAL_PROMPT=0`, ssh in `BatchMode`); a
missing credential makes the push fail at once, and the push waits for a retry.

## 3. Publish

```bash
./console exp:static:publish github-public --build
```

```text
Building github-public
...
Static publishing: publish of github-public, branch gh-pages in var/site/static-publish/github-public
  made var/site/static-publish/github-public a git repository on the branch gh-pages
  the remote has no branch gh-pages yet: the first push makes it
  committed 4e1f0c9a2b on gh-pages: 3547 added, 0 changed, 0 removed
  tagged static/github-public/1
  pushed gh-pages and the tags static/github-public/* to https://github.com/owner/my-site.git
  Commit: 4e1f0c9a2b7d, tag static/github-public/1
  Push:   pushed (https://github.com/owner/my-site.git, credential token:github_pages_token)
PASS
```

- `--build` builds first (as `exp:static:build`); without it the last build in the folder is published. When the
  build does not finish (a page refused, for example), nothing is committed or pushed.
- `--message="New opening hours"` sets the commit message. The default names the target, the pages and files, the
  run and when it was built.
- `--no-push` commits and tags only; `exp:static:push` pushes later. `[StaticPublishSettings] Push=disabled` does the
  same for every publish.
- `--format=json` prints the report (`exp-static/1`): `state`, `commit_id`, `tag_name`, `push_state`, the counts.

The first publish makes the folder a repository. When the remote has the branch already (an existing site, a branch
with a README), its history is taken over: the publish becomes a commit on top of it, and the files of that branch
that no build wrote stay.

### What one publish commits

Only the build's files: every file the manifest `.static-publish/manifest.json` lists, the manifest itself, and the
deletion of files the last committed manifest listed and this one does not. A file you add to the branch yourself (a
README, a `CNAME` you manage by hand, a workflow file) is never committed, changed or removed by a publish. The
manifest of the build before (`.static-publish/manifest.previous.json`) stays local.

A publish with nothing new commits and tags nothing (`state: unchanged`).

### The committer

The commits name the committer `CommitName` and `CommitEmail` of `[StaticPublishSettings]`
(`Exponential static publishing`, `static-publish@<host name>`), unless the repository's own git configuration names
one (`git -C var/site/static-publish/github-public config user.email you@example.com`).

## 4. When a push fails

A push that fails (the host unreachable, a token expired, a secret missing) keeps the commit and the tag in the
target's folder. The run's push state is `pending`, the command ends with exit 1 and says why:

```text
FAIL: the push failed (unable to access 'https://github.com/owner/my-site.git/': Could not resolve host: github.com): the commit stays local and exp:static:push or the cronjob part staticpublish tries again
```

Fix the cause, then push again:

```bash
./console exp:static:push github-public
./console exp:static:push                 every target with a waiting push
```

A push sends the branch with every commit and all of the target's tags, so one push sends every publish that waited.
To have it done for you, add the cronjob part `staticpublish` to a cronjob group the site runs, for example every 15
minutes, in `settings/override/cronjob.ini.append.php`:

```ini
[CronjobPart-frequent]
Scripts[]=staticpublish.php
```

or run it on its own: `php runcronjobs.php staticpublish`.

The push states of a run: `pushed`, `pending` (waits for a retry), `disabled` (`--no-push` or `Push=disabled`),
`no-remote` (the target has no `Remote`), `none` (nothing was committed).

### Never forced

A publish never rewrites the remote's history: no `--force`, no `+` refspec, no deletion (the code refuses them before
git runs). When someone else pushed to the publishing branch, the push is refused as not a fast-forward and waits; the
branch belongs to static publishing, so do not commit to it by hand. Tags are never moved or replaced.

## 5. See the state

```bash
./console exp:static:status github-public
```

```text
Static publishing: github-public
  Folder:     var/site/static-publish/github-public
  Remote:     https://github.com/owner/my-site.git, branch gh-pages
  Credential: token:github_pages_token (readable)
  Repository: 4e1f0c9a2b7d "Publish github-public: 560 pages, 3547 files", 1 tag(s), last static/github-public/1; 0 change(s) not published, 0 commit(s) not pushed
  Pages:      560 published, 0 stale; 0 run(s) wait for a push

  Run                     Kind      State      Tag                          Push       By
  20261101-120000-4f2a9c  publish   done       static/github-public/1       pushed     cli alpha
PASS
```

"readable" means the secret can be read; the value is never shown.

## One at a time

A build, publish, rollback or push of a target takes the target's lock (`var/<site>/static-publish/.locks/<target>.lock`).
A second one started meanwhile, from the console, a cronjob or later the administration, stops with
`another process works on the target github-public (publish by process 4711 since ...)`. The lock goes with the
process, so a build that crashed never blocks the next.

## Files and owners

Run the commands as the system user that owns the site's files (`sudo -u <user> ./console ...`). A folder or `.git`
that belongs to another user is refused, since files git wrote as that user would break the next build. On Windows
the commands run as they are: git is started without a shell, paths use `/`, and the deploy key file is written with
the rights of the current user.

## Take targets to another installation

`./console exp:static:targets --export=all` (or `--export=github-public,docs`) prints the targets as an INI
migration: their `[Target_<id>]` blocks and `TargetList[]`. Save it in the migrations folder of the other
installation (`YYYYMMDDHHMMSS_static_targets.ini`) or in a site package, and run it there. A credential travels as
its secret name (`token:github_pages_token`), never as its value: store the value in Setup > Secrets of the other
installation. The migration does not switch static publishing on.

A package carries targets too: `php bin/php/ezpm.php -r <repository> add <package> expstatictarget github-public`
(ids separated by commas, or `all`) adds each one as `statictargets/target-<id>.xml`. Only the wizard's settings
travel; the credential travels as its name, a user or password in the git address is left out, and so is the webhook,
whose address is often a secret itself. Installing the package writes a target the installation does not have yet
into `settings/override/staticcache.ini.append.php` (audited like a save in the wizard) and keeps one it has as it
is. Uninstalling the package leaves the settings alone: remove a target in Setup > Static publishing.

## Troubleshooting

| Symptom | Cause and fix |
|---|---|
| `holds no build (no manifest)` | Build first, or publish with `--build` |
| `is on the branch main, not gh-pages` | Someone switched the branch in the folder; `git -C <folder> checkout gh-pages`, or set `Branch` |
| `has staged changes that are not the build's` | Something was staged by hand in the folder; commit or unstage it there |
| `the secret github_pages_token is not set` | Add it in Setup > Secrets, or use `token:env:VARIABLE` |
| `Credential=app:... needs the GitHub integration` | PHP lacks curl or openssl, or `[StaticPublishSettings] GitHubAPI=disabled`; use a token or a deploy key |
| `the GitHub App installation ... gave no token` | `GitHubAppId` or `GitHubAppKey` is not set, the key is not the App's, or the App is not installed on the repository |
| `not pushed: the build is ..., more than the ... GitHub Pages allows a site` | Every push of a GitHub Pages target is checked against GitHub's limits; make the build smaller (`Assets=referenced`, `ExcludePaths[]`) ([limits](../help/static-publishing/github-pages-limits-and-terms.md)) |
| Push `pending`, `rejected (fetch first)` | The branch has commits that were not published from here; do not commit to the publishing branch by hand |
| `another process works on the target` | Wait for the other build or publish to finish |

## Next step

[History and rollback](static-publishing-history-and-rollback.md): go back to an earlier publish without losing the
history.
