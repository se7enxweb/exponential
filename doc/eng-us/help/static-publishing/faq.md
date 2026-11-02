---
title: "Static publishing FAQ"
audience: admin, website-creator, editor, non-technical, non-profit, local-business, individual, student, developer, hosting
category: administration
order: 25
summary: Short answers to the questions people ask about static publishing - cost, hosting, editors, comments, going back, Exponential Layouts, Smarty and Twig sites, languages, part of a site, private repositories and custom domains.
keywords: static publishing, FAQ, questions, is it free, free hosting, free website, GitHub Pages, github.io, hosting, editors, comments, rollback, Exponential Layouts, Smarty, Twig, multilingual, private repository, custom domain, CNAME, Jekyll alternative, Netlify, static site generator
featured: true
type: help
---
# Static publishing FAQ

## What you'll have at the end

An answer to your question, or a link to the page that has it.

## Cost and hosting

**Is it really free?** GitHub Pages is free for public repositories on GitHub's free plan. Exponential's static
publishing is part of Exponential. What you still need is a place where Exponential itself runs.

**Do I still need hosting?** For Exponential, yes: editors work there. But it can be small and need not be public:
a server in the office, a laptop, a cheap virtual machine or a CI job. Only the static copy faces the public.

**What does my own domain cost?** Only the domain itself at your registrar. GitHub serves it with a free HTTPS
certificate ([Use your own domain](use-your-own-domain.md)).

**Are there limits?** 1 GB a site, 100 MB a file and about 100 GB of traffic a month on GitHub Pages
([GitHub Pages limits and terms](github-pages-limits-and-terms.md)).

**Can I use a private repository?** GitHub serves Pages from private repositories only on paid plans. Other hosts
have their own rules ([Other git hosts](other-git-hosts-gitlab-gitea-and-your-own-server.md)).

**Can I sell things on it?** No: GitHub's terms rule out shops, and a basket needs a server. Keep a shop on your own
hosting.

## Editors and content

**What changes for my editors?** Nothing in how they work. Their changes reach the public site with the next publish,
by hand, on publish or on a schedule ([Publish automatically](publish-automatically.md)).

**How fast do changes go live?** A build takes minutes; GitHub Pages adds a minute or two.

**Can I publish just one page?** Yes: the **Static publishing** panel of the website sidebar publishes the page you are on
([The website sidebar panel](the-website-sidebar-panel.md)), and `exp:static:build --nodes=` builds chosen pages.

**Can I go back?** Yes, to any earlier publish, in one step ([History and rollback](history-and-rollback.md)).

**Can I publish only part of the site?** Yes: leave out pages by path or section in step 7 of the wizard.

## What works on a static site

**Search?** A search page that runs in the browser, when switched on.
**Forms?** They can post to your live site, or show a note with a link.
**Comments and login?** Not on the copy; they are left out with a short note.
Details: [Search, forms and comments on a static site](search-forms-and-comments-on-a-static-site.md).

**Does it work with Exponential Layouts?** Yes. The build saves what a visitor sees, whatever renders it.

**With Smarty or Twig templates?** Yes, for the same reason.

**With a multilingual site?** Yes: one target per language siteaccess, with the
`hreflang` links kept ([Languages on a static site](languages-on-a-static-site.md)).

**With a multisite installation?** One target per site ([Static publishing for multisite installations](../../guides/static-publishing-for-multisite-installations.md)).

## Safety

**Can private content leak?** Builds run as a visitor who is not signed in, and pages that are not clean are refused.
Read [Keep private content private](keep-private-content-private.md).

**Where is my GitHub token kept?** In Setup > Secrets. The target holds only its name; the token never shows in a log.

**Who may publish?** Whoever has the policy `staticpublish/publish`, after **Verify it's you**.

## Compared with others

**Why not Jekyll or Hugo?** They turn files into a site; Exponential gives you a full editing interface, roles,
versions and workflows, and publishes the result the same way. See the comparison for web framework developers in the
help center.

**Can I publish to Netlify or Cloudflare Pages?** Both can deploy from a git repository: publish to GitHub or GitLab
and connect the host to that repository.

## Next step

[Make a free website in 15 minutes](make-a-free-website-in-15-minutes.md).
