# Static publishing: CI and GitHub Actions

This guide is for developers who build and publish a static copy from a CI job, and for administrators who let
GitHub Actions deploy what Exponential pushes. It explains the commands' machine-readable output, the workflow file
the build writes for `Delivery=actions`, line by line, and the starter template that builds a whole site on GitHub.

[Guides](README.md) · Related: [Choose branch or GitHub Actions](../help/static-publishing/choose-branch-or-github-actions.md),
[Host static sites on GitHub Pages with Exponential 6.0.15+](host-static-sites-on-github-pages.md#from-ci),
[Static publishing](../features/6.0/static-publishing.md#commands)

## What you'll have at the end

- A CI job that builds, checks and publishes a target and fails when something is wrong.
- The Actions workflow of a target understood, so you know what GitHub runs and why.
- A choice between pushing from your server and building entirely on GitHub.

## Two ways to deliver

| | Branch (`Delivery=branch`) | GitHub Actions (`Delivery=actions`) |
|---|---|---|
| Who builds | Exponential, on your server or in your CI | Exponential, on your server or in your CI |
| What GitHub does | serves the branch as it is | runs the workflow below, which uploads the pushed files and deploys them |
| Pages setting | **Deploy from a branch** | **GitHub Actions** |
| Token needs | Contents: write | Contents: write and Workflows (the workflow file is pushed too) |

In both cases nothing is built on GitHub: the files are published as Exponential wrote them.

## Commands in a CI job

Every `exp:static:*` command takes `--format=json` and prints one JSON document (schema `exp-static/1`). The exit
codes are 0 done, 1 failed, 2 usage, 3 the database unreachable, 4 internal error.

```bash
./console exp:static:targets --format=json
./console exp:static:build github-public --format=json --report=var/log/static-build.json
./console exp:static:diff github-public --verify --check-links --mount=/ --mount=/my-site/ --format=json
GH_PAGES_TOKEN=$TOKEN ./console exp:static:publish github-public --format=json
```

- Keep the token out of the target: `Credential=token:env:GH_PAGES_TOKEN` reads it from the job's environment. It
  reaches git through `GIT_ASKPASS` and never appears on a command line or in a log.
- If the build server cannot reach the site's public address, set `CrawlURL` (or pass `--crawl-url`).
- When `SiteURL` is `localhost` or a loopback address, as in CI, links to pages the copy does not hold stay as
  they are and the build warns; set `LiveURL` to your live site to send them there.
- A failed push stays `pending`; `exp:static:push` sends it again.

## The workflow file, line by line

With `Kind=github-pages` and `Delivery=actions` (or `Extras[]=workflow`), each build writes
`.github/workflows/static-publish-pages.yml` into the copy. It is written only when the build is at the root of the
branch (`Folder` empty), because GitHub reads workflows from there. For `Branch=gh-pages` it reads:

```yaml
# Written by Exponential static publishing: deploys the static copy pushed to the branch
# gh-pages to GitHub Pages. It builds nothing; the files are published as they are.
# Each publish rewrites this file: change the target's settings instead.
name: Deploy the static site to GitHub Pages
on:
  push:
    branches: ["gh-pages"]
  workflow_dispatch:
permissions:
  contents: read
  pages: write
  id-token: write
concurrency:
  group: pages
  cancel-in-progress: false
jobs:
  deploy:
    environment:
      name: github-pages
      url: ${{ steps.deployment.outputs.page_url }}
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/configure-pages@v5
      - uses: actions/upload-pages-artifact@v3
        with:
          path: .
      - id: deployment
        uses: actions/deploy-pages@v4
```

| Lines | Meaning |
|---|---|
| The comments | Who wrote the file. Do not edit it: every publish writes it again from the target's settings |
| `name` | The name shown in the repository's **Actions** tab |
| `on: push: branches` | Runs on every push to the target's `Branch`, that is on every publish and rollback |
| `workflow_dispatch` | Lets you run it by hand from the Actions tab, for example after changing the Pages settings |
| `permissions` | The least the deploy needs: read the files, write to Pages, and an OIDC token that proves the run to Pages |
| `concurrency` | One deploy at a time; a newer one waits instead of cancelling the one that runs |
| `environment: github-pages` | The deployment environment GitHub creates for Pages; `url` shows the published address on the run |
| `runs-on: ubuntu-latest` | A GitHub-hosted runner; nothing of your installation runs there |
| `actions/checkout` | Fetches the pushed branch: the copy as Exponential built it |
| `actions/configure-pages` | Reads the repository's Pages settings |
| `actions/upload-pages-artifact`, `path: .` | Packs the whole checkout as the site |
| `actions/deploy-pages` | Publishes the packed site; its step id `deployment` gives the URL above |

`exp:static:github status` and the box **On GitHub** in History show the last run of this workflow.

## Building on GitHub: the starter template

For a site built entirely on GitHub, without a server of your own, start from the template repository
[exponential-static-site-starter](https://github.com/se7enxweb/exponential-static-site-starter): **Use this template**,
set **Settings > Pages > Source** to **GitHub Actions** and push. Its own workflow installs Exponential with
Composer, brings in your site (a SQLite database and storage from `data/` plus settings, extensions and designs from
`site/`, or a site package installed with `exp:install`), serves it on `127.0.0.1` with PHP's built-in web server,
runs `exp:static:build` and `exp:static:diff --check-links`, and hands the copy to GitHub's Pages actions. No token is
needed; the template's README lists its settings.

## Next step

[Develop in public, publish free](develop-in-public-publish-free.md), a tutorial that combines the starter with
editing in Exponential.
