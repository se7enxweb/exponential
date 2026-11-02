---
title: "Other git hosts: GitLab, Gitea and your own server"
audience: admin, website-creator, developer, hosting, government
category: administration
order: 23
summary: Publish the static copy to GitLab Pages, Codeberg or Gitea pages, or a git repository on your own server - the target with Kind=git, signing in with a token or a deploy key, and what each host needs to serve the files.
keywords: static publishing, other git host, GitLab, GitLab Pages, Gitea, Forgejo, Codeberg, Codeberg Pages, own server, self-hosted, Kind=git, deploy key, token, git push, static site, free hosting, Netlify, Cloudflare Pages, rsync
featured: true
type: help
---
# Other git hosts: GitLab, Gitea and your own server

Static publishing pushes to any git repository. GitHub Pages is only the most common place; the same build goes to
GitLab, to a Gitea or Forgejo server, to Codeberg, or to a bare repository on a server of your own.

## What you'll have at the end

A target that publishes to the git host of your choice, and that host serving the files.

## The target

In step 2 of the wizard choose **another git host** (`Kind=git`). Then:

- **Git address**: `https://gitlab.com/<group>/<project>.git`, `git@codeberg.org:<owner>/pages.git`, or
  `ssh://git@server.example.com/srv/git/site.git`. Never with a password in it.
- **Signing in**: a **token** in Setup > Secrets (most hosts accept a project or personal access token over
  `https://`), or a **deploy key** for an SSH address. Make the key on the target's **Deploy key** page and add its
  public half to the repository with write access. **None** works for a repository on the same server.
- **Branch** and **folder**: what the host serves from (see below).

The GitHub steps (creating the repository, switching Pages on, the DNS check against GitHub) are not offered for
`Kind=git`; you do these on the host. Build, preview, publish, history and rollback work the same.

## What each host needs

| Host | Repository and branch | Notes |
|---|---|---|
| **GitLab Pages** | any project; a pipeline publishes the folder `public` | add a `.gitlab-ci.yml` with a `pages` job that copies the files to `public/`; keep that file in the branch, the build leaves files it did not write alone |
| **Codeberg Pages** | a repository `pages` (or the branch `pages` of any repository) | served at `https://<owner>.codeberg.page/` |
| **Gitea / Forgejo** | any repository | serving depends on the server; many use a pages server or a hook |
| **Your own server** | a bare repository | a `post-receive` hook that checks the branch out into the web server's folder, or a web server that serves a checkout |

Hosts other than GitHub may have their own size limits and terms; read them before you publish.

## Links

Check where the host puts the site: below `/<project>/` like a GitHub project site, or at the root of a domain.
Relative links work in both cases; set the **public address** for the sitemap ([Links and base path](links-and-base-path.md)).

## Next step

[Build, preview and publish](build-preview-and-publish.md). The push and its credentials in depth:
[Targets and credentials](../../guides/static-publishing-targets-and-credentials.md).
