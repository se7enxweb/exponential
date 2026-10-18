---
title: "Keeping Packagist up to date automatically"
audience: designer, developer
category: theme-distribution
order: 200
summary: Make Packagist read every new tag as soon as it is pushed - through the GitHub integration or a webhook - and check that the hook works after a release.
keywords: Packagist webhook, auto update, GitHub hook, packagist api github, update package, new version not shown, hooks
type: help
---
# Keeping Packagist up to date automatically

This page is for designers who published a theme on Packagist. Without an update hook, Packagist does not learn about
new tags promptly, and users see an old version as the newest.

## Two ways

| Way | How |
|---|---|
| The GitHub integration | Sign in to Packagist with your GitHub account and let Packagist manage the hooks of your repositories; it sets the hook for packages it can access |
| A manual webhook | In the repository's **Settings > Webhooks** add Packagist's GitHub hook URL (`https://packagist.org/api/github?username=<your Packagist user>`) with your Packagist API token as the secret, content type `application/json`, event "push" |

Packagist's own page **Profile > Settings > API Token** shows your token and explains the hook for your account; follow
its current text, it is the authority.

## Step by step: check the hook

1. List the hooks of the repository (needs admin rights):

   ```bash
   gh api repos/yourvendor/mytheme/hooks --jq '.[].config.url'
   ```

   Expected: a URL on `packagist.org`. No output means there is no hook.
2. After the next release, check Packagist within a few minutes:

   ```bash
   curl -s https://repo.packagist.org/p2/yourvendor/mytheme.json | grep -o '"version":"[^"]*"' | head -3
   ```

   Expected: the new version first.
3. In GitHub, **Settings > Webhooks > the Packagist hook > Recent Deliveries** shows each call and its response; a
   red mark tells you the hook failed and why.

## If the hook cannot be set up yet

On the package's Packagist page, a maintainer can press **Update** to make Packagist read the repository now. Do this
after every release until the hook works.

## Related pages

- [Packagist shows an old version](stale-packagist-version.md)
- [Submitting a theme to Packagist](submitting-to-packagist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
