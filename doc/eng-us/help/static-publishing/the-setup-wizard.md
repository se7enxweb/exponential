---
title: "The setup wizard"
audience: admin, website-creator, developer, hosting
category: administration
order: 5
summary: Every step of Setup > Static publishing > New target - the site, the host, signing in, the repository, links and base path with a live example, your own domain, what the build holds, when it is published - then Review and Save.
keywords: static publishing, setup wizard, new target, target settings, staticcache.ini, Target_, siteaccess, GitHub Pages, git host, token, deploy key, Remote, branch, gh-pages, docs folder, base path, relative links, custom domain, CNAME, extras, nojekyll, 404.html, sitemap, exclusions, triggers, Review, Save, verify it's you
type: help
---
# The setup wizard

A **target** is one site of your installation published to one git repository. The wizard in **Setup > Static
publishing > New target** makes one in nine short steps; **Settings** on a target opens the same wizard with its
settings. Nothing is built or published here: **Save** only writes the settings.

## What you'll have at the end

A target with its settings in `settings/override/staticcache.ini.append.php`, ready to build.

## Before you start

- The policy `staticpublish/setup` without a Target limitation (Administrator has it).
- A repository on the host. An empty one is fine. On GitHub: **New repository**, public for a free plan.
- For a token: the token stored in **Setup > Secrets** under a name of your choice (for GitHub a fine-grained token
  with **Contents: Read and write** on that repository). For a deploy key you need nothing yet.

![The wizard at step 1: the id, the name and the site to publish](../images/static-publishing/wizard-light.webp)

The step list at the top shows where you are; **Back** and **Next** move between steps, and a step can be opened
from the list. Without JavaScript all steps show on one page.

## 1. The site

- **Id**: lower case letters, digits, `_` and `-`, for example `github-public`. It names the target's folder and its
  tags (`static/<id>/<n>`) and cannot be changed later.
- **Name**: shown in lists.
- **Site to publish**: one of the front siteaccesses. Administration and editing siteaccesses are never offered: a
  static copy is always what a visitor who is not signed in sees.

## 2. The host

**GitHub Pages** or **another git host** (GitLab, Gitea, your own server). The box lists GitHub's limits: a site of
up to 1 GB, a file of up to 100 MB, about 100 GB of traffic a month, a public repository on a free plan, and no shops.

## 3. Signing in to the repository

| Choice | For | What is stored |
|---|---|---|
| A token from Setup > Secrets | an `https://` address | `Credential=token:<name>` |
| A deploy key made for this target | an SSH address (`git@github.com:owner/repo.git`) | `Credential=sshkey:staticpublish.<id>.deploy_key` |
| None | the server signs in by itself, or the repository is on this server | `Credential=none` |

Only the name is stored with the target; the token or key stays in Setup > Secrets and never appears in a log or a
report. With a deploy key, **Save** opens the target's **Deploy key** page, where **Make the key** creates it and shows
the public half to add to the repository ([Deploy keys](../../guides/static-publishing-deploy-keys.md)). Signing in
with a GitHub App comes with the GitHub integration.

## 4. The repository

- **Git address**: the repository's address, never with a password or token in it.
- **Branch**: `gh-pages`, or `main` with the folder `docs`.
- **Folder**: the root of the branch or `/docs` (GitHub Pages serves one of the two).
- **How GitHub Pages receives the build**: from the branch (the default) or by a GitHub Actions workflow.

## 5. Links and base path

A GitHub **project** site lives below `/<repository>/` (`https://owner.github.io/my-site/`). **Relative** links work
there and below any other path, so they are the default. **Below a base path** writes every link starting with the
base path. The box shows, as you type, how a link from the page `/news/` to `/about-us/team` will be written.
**Public address** is the address of the published site; the sitemap and the canonical links need it.

## 6. Your own domain

Optional: `www.example.com`. It is written to the file `CNAME`, and the site is then served at the root of the
domain, so leave the base path empty. Point the domain's DNS at the host as it explains; HTTPS can take a while.

## 7. What the build holds

- **Files**: what the pages name, and every image of published content (the default), or only what the pages name.
- **Extras**: `.nojekyll` (needed on GitHub Pages), `404.html`, `robots.txt`, `sitemap.xml`. The manifest is always
  written.
- **Post-processing**: debug output, form tokens, session ids and nonces are always taken out. A page that still
  carries one is refused, and nothing is published.
- **Search page** and **forms**, **most pages**, pages and sections to **leave out**, and where the pages are
  **fetched from** and where links to the **live site** go.

## 8. When it is published

By hand here and on the console. Publishing when content is published and on a schedule, notifications and the webhook
are stored now and come into effect with a later release.

## 9. Review and save

**Review** checks every setting and shows the lines that will be written. Fix what it lists and press Review again.
When static publishing is still switched off, **Switch static publishing on** is ticked for you. **Save** writes the
settings (with a backup of the file and an entry in the audit log) and asks **Verify it's you**. Then the target's
**Build** page opens.

![The review: the settings and the lines that will be written](../images/static-publishing/review-light.webp)

## Next step

[Build, preview and publish](build-preview-and-publish.md).
