---
title: "Static publishing from the command line and CI"
audience: php-developer, developer
category: php-development
order: 511
summary: For software engineers and PHP developers - build, check and publish a static copy with the exp:static:* commands, read their JSON output, and run it in GitHub Actions from the starter template.
keywords: php developer, software engineer, command line, console, CI, GitHub Actions, workflow, exp:static:build, exp:static:publish, exp:static:diff, JSON, exit codes, static site, GitHub Pages, gh-pages, free hosting, free website, Jekyll alternative, Netlify, CNAME, custom domain, deploy, static publishing, starter template
type: help
---
# Static publishing from the command line and CI

Everything the administration does under Setup > Static publishing is a console command, and every command prints
JSON on request. That makes static publishing easy to script and to run in CI.

## What you'll have at the end

- A build, link check and publish you can run from a shell or a pipeline.
- A CI workflow that installs Exponential, builds the static copy and deploys it to GitHub Pages.

## The commands

```bash
php bin/php/expstatictargets.php                       # targets and their checks
php bin/php/expstaticbuild.php demo --dry-run          # what would change
php bin/php/expstaticbuild.php demo --report=var/tmp/report.json
php bin/php/expstaticdiff.php demo --check-links --mount=/ --mount=/my-site/
php bin/php/expstaticserve.php demo                    # preview on 127.0.0.1:8090
php bin/php/expstaticpublish.php demo --message="Spring update"
php bin/php/expstaticrollback.php demo 3 --apply
```

The same commands run as `exp:static:build` and so on through the console. `--format=json` prints one document with
the schema `exp-static/1`. Exit codes: 0 done, 1 failed, 2 usage, 3 database unreachable, 4 internal error.
Reference: the [feature page](../../features/6.0/static-publishing.md#commands) and the generated
[exp:static:build](../../examples/commands/expstaticbuild.md) page.

## Partial builds

`--nodes=12,34` or `--paths=/a,/b` rebuild only those pages and keep the rest; this is what publishing on content
publish uses.

## In CI

The template repository [exponential-static-site-starter](https://github.com/se7enxweb/exponential-static-site-starter)
has a GitHub Actions workflow that installs Exponential with your code, loads a database dump or a site package,
builds the copy and deploys it with GitHub Pages' own action. Click **Use this template** and follow its README.

In your own pipeline:

- set `CrawlURL` to the address the CI web server listens on; links to the live site then use `LiveURL`;
- pass the token as an environment variable and use `Credential=token:env:<VARIABLE>`;
- fail the job on a non-zero exit code of `exp:static:diff --check-links`.

## Things to know

- Builds crawl over HTTP as the anonymous user, so the web server must run during the build.
- The target folder is its own git repository; do not put it inside your code repository.
- Pushes are never forced and tags never moved; a rollback is a new commit.

More: [Links and base path](../static-publishing/links-and-base-path.md),
[When a publish fails](../static-publishing/when-a-publish-fails.md).

## Make yours now

Start with [Make a free website in 15 minutes](../static-publishing/make-a-free-website-in-15-minutes.md), then
move it into CI with the starter.
