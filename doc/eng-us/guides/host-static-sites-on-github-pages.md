# Host static sites on GitHub Pages with Exponential 6.0.15+

This guide shows how to put a free, fast, secure public copy of an Exponential site on GitHub Pages, while editors
keep working in Exponential. The copy is static: plain HTML pages and files, no PHP and no database behind them, so
there is nothing on the public side to update, patch or attack. You rebuild and publish it when the content changes.

It is for site owners and administrators who want a free public site, and for developers who build and publish
sites from the command line or from CI. This first version covers the command line: a target set up in the settings,
a build, a local preview, a check and the publish with `exp:static:publish`. The same steps have screens in the
administration, in Setup > Static publishing ([Static publishing](../help/static-publishing/README.md)). The automatic
publishing to GitHub comes with a later part of static publishing; the feature page lists what each part adds.

[Guides](README.md) · Related: [Static publishing](../features/6.0/static-publishing.md),
[The build format](../specifications/6.0/static-publishing-build-format.md),
[Static cache generator](../features/6.0/static-cache-generator.md),
[Overview for administrators](static-publishing.md), [CI and GitHub Actions](static-publishing-ci-and-github-actions.md),
[Troubleshooting](static-publishing-troubleshooting.md)

## What you get

- A copy of one front siteaccess in a folder: every page as `<path>/index.html`, every file the pages need, a
  `404.html` made from your site's own 404 page, `robots.txt`, `sitemap.xml` and `.nojekyll`.
- Links that work wherever the folder is served: at `https://<owner>.github.io/<repository>/`, at the root of your
  own domain, or opened from a local preview.
- A copy that holds only what an anonymous visitor sees: no debug output, no form tokens, no session ids. A page
  that cannot be cleaned stops the build.
- Links to what a static copy cannot do (search, login, forms) point to your live site, so they keep working where
  there is one.

## What works and what changes

| On the live site | In the static copy |
|---|---|
| Pages, images, downloads, style sheets, scripts, fonts | The same, as files |
| Menus, layouts, blocks, galleries | The same, as they were rendered when you built |
| Search, login, forms, comments, shop | Links to the live site (`LiveURL`) |
| A page with a query string (`?page=2`) | A link to the live site |
| Links to pages the copy leaves out, links into the administration (`/admin/...`) | Links to the live site, or only their text with `OutsideLinks=unlink` and `AdminLinks=unlink` (wizard step 7) |
| A change an editor publishes | Visible after the next build and push |
| Pages only signed-in users see | Never in the copy |

## Before you start

- Exponential 6.0.15 or newer, and a console on the server (or a copy of the site where you build).
- A front siteaccess that anonymous visitors can read.
- A GitHub account and a repository for the copy, public on the free plan. GitHub Pages allows a site of 1 GB and
  files of 100 MB; the build warns before it reaches either.

## 1. Set up a target

A target says which siteaccess is published and how. Put it in
`settings/override/staticcache.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[StaticPublishSettings]
Enabled=enabled
TargetList[]
TargetList[]=github-public

[Target_github-public]
Name=Public site on GitHub Pages
SiteAccess=site
Links=relative
BasePath=/my-site/
PublicURL=https://owner.github.io/my-site/
Remote=https://github.com/owner/my-site.git
Branch=gh-pages

*/ ?>
```

- `BasePath` is `/<repository>/` for a project site (`owner.github.io/my-site/`), and empty for a user site
  (`owner.github.io`) or your own domain.
- `PublicURL` is where the copy will be. The sitemap, `robots.txt` and the canonical and social card links use it.
- Leave `Links=relative` unless you have a reason: relative links work at any path, so the same build can be
  previewed at the root and published below `/my-site/`.
- If the server cannot reach its own public address, set `CrawlURL` to an address it can reach
  (`http://127.0.0.1:8080/`). Links to that address are rewritten like links to the public one.

Check it:

```bash
./console exp:static:targets
```

```text
Static publishing: switched on, 1 target(s)

  github-public        Public site on GitHub Pages
    siteaccess site, github-pages, links relative
    public at https://owner.github.io/my-site/
    folder var/site/static-publish/github-public, not built yet
PASS
```

An error (a siteaccess that does not exist or is an administration one, a token written into `Remote`) is listed
under the target and the command ends with `FAIL (exit 1)`.

## 2. Build

Try a dry run first. It builds everything into a staging folder, reports what would change and leaves the target's
folder alone:

```bash
./console exp:static:build github-public --dry-run
```

Then build:

```bash
./console exp:static:build github-public
```

The build lists each page as it is stored, then a summary. This is the first build of the demo site, 560 pages, on
a copy of its installation (1 November 2026):

```text
Static publishing: build of github-public (site, full), run 20261101-105351-160345 in 183.2s
  Pages:  560 exported, 0 refused, 0 skipped, 39 excluded
  Files:  499 named by the pages, 2484 of the storage, 4 extras, 15 could not be collected
  Links:  26567 rewritten, 7062 to the live site, 5471 left as they are
  Size:   484.8M in 3547 files
  Taken out: 560 debug
  Diff:   3547 added, 0 changed, 0 removed, 0 unchanged
  Link check under /my-site/: 18534 links in 562 files, 0 broken
  Written to var/site/static-publish/github-public
WARN 15 file(s) named by the pages could not be collected; their links go to the live site (see "files_failed").
PASS
```

Most of the size is the storage images (`Assets=referenced-and-storage`); with `Assets=referenced` only the files
the pages name are copied. The 39 excluded pages are the registration, the basket and the tag views: they start a
session for anonymous visitors, the first build refused them, and the target leaves them out:

```ini
ExcludePaths[]
ExcludePaths[]=/user/*
ExcludePaths[]=/shop/*
ExcludePaths[]=/tags/*
```

- **refused**: a page carried something a public copy must not (a session cookie, debug output or a form token that
  could not be taken out). The build fails and writes nothing; the page is named with the reason. Fix the page, or
  leave it out with `ExcludePaths[]`.
- **skipped**: pages that only exist with a query string, or whose path cannot be a file name on every system.
- **could not be collected**: files a page names that the site does not hand to an anonymous visitor (a missing
  image, a protected download). Their links go to the live site; `--format=json` lists them under `files_failed`.
- **to the live site**: links to the search, the login, forms and the other pages the copy does not hold.

To rebuild a few pages after an edit, name them; the rest of the build stays:

```bash
./console exp:static:build github-public --paths=/about-us,/news
./console exp:static:build github-public --nodes=123
```

## 3. Preview

```bash
./console exp:static:serve github-public
```

```text
Serving /var/www/example/var/site/static-publish/github-public
at http://127.0.0.1:8090/my-site/  (Ctrl+C stops it)
```

Open the address in a browser. The preview serves the folder below the base path as GitHub Pages will: a folder
answers with its `index.html`, a missing page with your `404.html`. `--base-path=/` serves it at the root, as a
custom domain would. The preview listens on this computer only; `--host` and `--port` change that.

## 4. Check

```bash
./console exp:static:diff github-public --verify --check-links --mount=/ --mount=/my-site/
```

`--verify` makes sure the folder still holds exactly what the build wrote. `--check-links` follows every link and
file of every page and style sheet as a browser would, below each `--mount`, and lists what it does not find.
`404.html` is checked below the base path it was built for only.

Without options, `exp:static:diff` lists what the last build added, changed and removed against the one before.

## 5. Publish with git

The folder `var/site/static-publish/github-public` is the site and its own git repository. Store a token for the
repository in Setup > Secrets (`github_pages_token`) and set `Credential=token:github_pages_token` on the target,
then:

```bash
./console exp:static:publish github-public
```

It commits only the build's files to the branch `gh-pages`, tags the commit `static/github-public/1` and pushes both,
never forced. [Repositories and credentials](static-publishing-targets-and-credentials.md) explains how the push
signs in (a token, an SSH deploy key or the host's own credentials) and what happens when a push fails.

Then, on GitHub, open the repository's **Settings > Pages**, choose **Deploy from a branch**, the branch `gh-pages`
and the folder `/ (root)`, and save. After a minute or two the site is at `https://owner.github.io/my-site/`.

From then on one command builds and publishes:

```bash
./console exp:static:publish github-public --build
```

Never write a token into `Remote` or the remote's address; `exp:static:targets` refuses one.

## From CI

The commands print one JSON document with `--format=json` (schema `exp-static/1`) and end with exit 0 when the build
worked, 1 when it failed, 2 for a wrong command line and 3 when the database cannot be reached. A CI job can build,
check and push:

```bash
./console exp:static:build github-public --format=json --report=var/log/static-build.json
./console exp:static:diff github-public --check-links --format=json
GH_PAGES_TOKEN=... ./console exp:static:publish github-public --format=json     # Credential=token:env:GH_PAGES_TOKEN
```

## Start from the template repository

For a site that is built entirely on GitHub, without a server of your own, start from the template repository
[exponential-static-site-starter](https://github.com/se7enxweb/exponential-static-site-starter). Click **Use this template**, set **Settings > Pages > Source**
to **GitHub Actions** and push. Its workflow:

1. installs Exponential with Composer;
2. brings in your site: a SQLite database and the storage folder from `data/` plus your settings, extensions and
   designs from `site/`, or, with no database there, a site package installed with `exp:install`;
3. serves the site on `127.0.0.1` with PHP's built-in web server and runs `exp:static:build` and
   `exp:static:diff --check-links` on it;
4. hands the copy to GitHub's own Pages actions, which publish it at `https://<owner>.github.io/<repository>/`.

No token is needed: the deploy uses the permissions GitHub gives the workflow. The template's README lists its
settings and the limits of GitHub Pages; the same script builds the copy on your own computer.

## See it: the demo site

The Exponential demo site is being published this way as a static copy at
[https://se7enxweb.github.io/exponential-demo-site-static/](https://se7enxweb.github.io/exponential-demo-site-static/) (coming soon), from the repository
[se7enxweb/exponential-demo-site-static](https://github.com/se7enxweb/exponential-demo-site-static). Its pages show
what a static copy keeps (layouts, menus, galleries, images and downloads) and what changes (search runs on a
prebuilt index in the browser; forms, login and the shop are left out).

## Troubleshooting

| Symptom | Cause and fix |
|---|---|
| `Static publishing is switched off` | Set `[StaticPublishSettings] Enabled=enabled` |
| `TargetList[] names no target` | Add the id to `TargetList[]`; the block alone is not enough |
| `No page was exported` | The build could not fetch the site; set `CrawlURL` to an address the server reaches |
| A page is `refused: the response set the session cookie` | Something on that page starts a session for anonymous visitors (a form, a basket); exclude the page or change it |
| A page is `refused: debug output` | Debug output that is not the standard report; switch debug output off for the build's address |
| Style sheets missing below `/my-site/` | `Links=base-path` with the wrong `BasePath`; use `Links=relative` or correct `BasePath` |
| A 404 on `github.io` | GitHub Pages is not set to the branch, or the push has not been published yet |
