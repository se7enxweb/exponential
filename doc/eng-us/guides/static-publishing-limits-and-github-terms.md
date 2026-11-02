# Static publishing: limits and GitHub terms

This guide is for administrators who plan a site on GitHub Pages and want to know, before the first push, whether
it fits: the size limits Exponential checks, the soft limits it cannot check, and the terms that decide which sites
belong there.

[Guides](README.md) · Related: [GitHub Pages limits and terms](../help/static-publishing/github-pages-limits-and-terms.md),
[Is static right for my site?](../help/static-publishing/is-static-right-for-my-site.md)

## What you'll have at the end

A build that fits GitHub Pages, the settings that keep it so, and a clear answer whether your site may be hosted
there at all.

## The limits

| | GitHub's figure | Checked by Exponential |
|---|---|---|
| Site size | 1 GB | `MaxSiteBytes` (default `1000000000`): warning above 90 %, not pushed above it |
| One file | 100 MB | `MaxFileBytes` (default `100000000`): warning above 50 MB, not pushed above it |
| Traffic | about 100 GB a month, soft | no |
| Branch builds | about 10 an hour, soft | no; one publish is one Pages build |
| Private repository | Pages on paid plans only | a warning |

Every push of a GitHub Pages target is checked first. A push over the limits stays **pending** with the reason in
the report and History; nothing reaches GitHub. The preview of a build shows its size against the limits and the
largest files. The figures are GitHub's published ones and GitHub may change them; set `MaxSiteBytes` and
`MaxFileBytes` in `[StaticPublishSettings]` if they do, or if your host has other limits.

## Keep the build small

- `Assets=referenced` copies only the files the pages name, instead of every storage image of published content
  (`referenced-and-storage`, the default). On the demo site most of the 485 MB were storage images.
- `ExcludePaths[]` and `ExcludeSections[]` leave out pages that need not be public.
- Put large videos and downloads on a video platform or a release download, and link them.
- `PostProcess[]=minify` and `precompress` make the text files smaller; `exp:static:diff` shows what a change adds.

## Publish less often

On-publish triggers are debounced (`PublishDebounce`, 120 seconds by default): many edits in a short time make one
build. A `Schedule` publishes at fixed times instead. Both keep you well below the soft build limit.

## The terms

GitHub Pages is for project, personal and organization sites. Its terms say it is not for running an online
business, an e-commerce site or a site mainly for commercial transactions, and not for free file hosting. That
decides more than the size does:

- Good fits: a brochure, blog, documentation, a portfolio, an event, a nonprofit, a school or a user group.
- Poor fits: a shop, a members' area, a site with many forms. Keep those on your own hosting; static publishing
  leaves out the basket and the login anyway.
- On a free plan the repository is public, and so are its files and history. Read
  [Keep private content private](../help/static-publishing/keep-private-content-private.md) before the first push.

Read GitHub's current terms for Pages and its acceptable use policies: they apply to your site, not to Exponential.
Other git hosts have their own; see
[Other git hosts](../help/static-publishing/other-git-hosts-gitlab-gitea-and-your-own-server.md).

## Next step

[Static publishing: troubleshooting](static-publishing-troubleshooting.md).
