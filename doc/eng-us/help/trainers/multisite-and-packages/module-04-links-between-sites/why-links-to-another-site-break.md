---
title: "Lesson 4.1: Why links to another site break"
audience: trainer, admin, developer
category: training
order: 41
summary: Learners explain why a link from one site of a multisite to content of another answers 404 without help, and how the ezurlmultisite extension chooses the siteaccess that serves a node. 35 minutes.
keywords: multisite and site packages, cross-site links, ezurlmultisite, PathPrefix, ezurl, URL alias, 404, kernel (20), RootNodeList, SiteAccessList, SiteURL, multisite demo
type: help
---
# Lesson 4.1: Why links to another site break

When several sites share one content tree, pages of one site regularly list content of another: a tag page, a search
result, a related item. Without help those links point into the wrong site and answer "not found". This lesson
explains the cause and the rule the `ezurlmultisite` extension uses to fix it, before lesson 4.2 configures it.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Module | [Module 4: Links between sites](README.md) |
| Duration | 35 minutes (20 presenting, 15 exercise) |
| Level | Advanced |
| Audience | Administrators and developers |
| Prerequisites | [Lesson 2.1](../module-02-matching-requests/matching-by-url-path.md): `PathPrefix` |

## Learning objectives

After this lesson, learners can:

- explain how `PathPrefix` and the template operator `ezurl` together produce a broken link to another site;
- describe how `ezurlmultisite` finds the site that serves a node or a URL alias;
- state which links the extension leaves unchanged;
- predict the address of a cross-site link on the same host and on another host.

## Trainer's notes

### How to present it

Source: [Links to content of another site of a multisite installation](../../../../features/6.0/multisite-cross-site-links.md).

1. **The example** (5 minutes). The multisite demo serves Fit & Healthy on `/site` (or `/`) and Bold Agency on
   `/bold`, from one tree. Each site hides its subtree's path with `PathPrefix`. A node of the other site keeps that
   site's path in its URL alias, and `ezurl` puts the current siteaccess in front: `/bold/fit-healthy/video/...`,
   which does not exist (`kernel (20)`, module not found).
2. **The rule** (8 minutes). With the extension active, a link made on a participating siteaccess is resolved so:
   1. for a node or object, the site is the one whose root node is the deepest on the node's path (`RootNodeList`);
      for a URL alias string, the site whose `PathPrefix` the alias starts with, the longest prefix first;
   2. when that is another site, the address is built from that siteaccess's `SiteURL` with its `PathPrefix`
      removed: host-relative on the current host (`/bold/services`, keeping scheme and port), absolute on another host
      (`https://bold.example.com/services`);
   3. a site and its translations share a subtree: the current siteaccess wins, then one in the current locale, then
      the first in `SiteAccessList[]`.
3. **What stays the same** (3 minutes). Links within the current site, module links (`user/login`,
   `content/search`), files and design paths, and every link made on a siteaccess that does not take part, which
   includes every administration siteaccess.
4. **Cost** (2 minutes). No database query per link; the siteaccess table is read once per request (once per worker
   under Exponential Velocity).

### Common questions

**Do I need it if every site has its own content?** Only if pages link across sites. A site that never lists content
of another does not need it, but it costs nothing to have.

**Is it part of the kernel?** It is the extension `ezurlmultisite` (`se7enxweb/ezurlmultisite`), required by the root
`composer.json`, so it is present on a standard installation.

**What about the media theme's own link operators?** `exp_path`, `exp_url`, `content_link` and the like give the
address on the serving siteaccess as `ezurl` does.

**Does it change links in the administration?** No: administration siteaccesses do not take part.

### Pitfalls

- **Testing in the administration.** Cross-site links show on public pages only; open the public site.
- **Blaming the content.** A 404 on a cross-site link is a configuration question, not a broken object. Check the
  extension's settings first (lesson 4.2).
- **Cached pages.** Compiled templates hold the kernel's `ezurl` code until they are compiled again; after
  activating the extension, clear the template and content caches.

## Hands-on exercise

A reading and prediction exercise in pairs, with the feature page open. A lab check follows in lesson 4.2.

Given an installation with the siteaccesses `site` (`PathPrefix=fit-healthy`, root node 97, `SiteURL` on
`www.example.com/site`), `bold` (`PathPrefix=bold-agency`, root node 81, `SiteURL` `www.example.com/bold`) and
`bold_ger` (the German translation of `bold`, same root node):

1. Without the extension: a page on `/bold` lists a video whose URL alias is `fit-healthy/video/morning-run`. Write the
   link `ezurl` makes.
2. Explain why that address answers 404.
3. With the extension: write the link for the same video on the same page.
4. A page on `/site` links to the node `bold-agency/services`. Write the link.
5. The same page links to `user/login`. Write the link.
6. `bold` moves to its own host `bold.example.com`. Write the link of step 4 again.
7. A visitor on `/bold_ger` sees a link to a node below node 81. Which siteaccess serves it, and why?
8. Compare with another pair and settle disagreements with the feature page.

## Expected result

- Step 1: `/bold/fit-healthy/video/morning-run`; step 2: the `bold` siteaccess has no such alias and no such module.
- Step 3: `/site/video/morning-run` (the address on the siteaccess that serves it, its prefix removed).
- Step 4: `/bold/services`; step 5: `/site/user/login`, unchanged.
- Step 6: `https://bold.example.com/services`, absolute because the host differs.
- Step 7: `bold_ger`, because the current siteaccess wins when the site and its translations share a subtree.
- The trainer checks by collecting the answers of steps 3, 4 and 6 on the board.

## Quiz

1. Which two things together make the broken link?
2. Which links does `ezurlmultisite` leave unchanged?
   A. Links within the current site B. Module links such as `user/login` C. Links made in the administration
   D. All three
3. How is the site of a node found?
4. When is a cross-site link absolute?
5. Does the extension run a database query per link?

<details>
<summary>Answers</summary>

1. The other site's `PathPrefix` stays in the node's URL alias, and `ezurl` puts the current siteaccess in front.
2. **D.** ([Links to content of another site](../../../../features/6.0/multisite-cross-site-links.md#in-short))
3. The site whose root node is the deepest on the node's path, from `RootNodeList`.
4. When the other site's `SiteURL` is on another host; on the current host it is host-relative.
5. No; the siteaccess table is read once per request (once per worker under Velocity).

</details>

## Further reading

- [Links to content of another site of a multisite installation](../../../../features/6.0/multisite-cross-site-links.md).
- [Setting up several sites in one installation: links between the sites](../../../creators/website-creators/multisite-setup.md#links-between-the-sites).
- [URL aliases and nice URLs](../../../creators/website-creators/url-aliases-and-nice-urls.md).
- [Relations and reuse](../../../creators/website-creators/relations-and-reuse.md): where cross-site links come from.

## Related pages

- [Module 4: Links between sites](README.md)
- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
