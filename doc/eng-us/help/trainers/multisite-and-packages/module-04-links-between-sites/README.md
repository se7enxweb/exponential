---
title: "Module 4: Links between sites"
audience: trainer, admin, developer
category: training
order: 40
summary: The fourth module of Multisite and site packages. Learners explain and configure links from one site to content of another with ezurlmultisite, and run several sites from one administration with roles limited to each site. 2 hours 25 minutes.
keywords: multisite and site packages, module 4, cross-site links, ezurlmultisite, RootNodeList, SiteAccessList, ezurl_roots, SiteList, FrontSiteAccess, Assign with limitation, subtree
type: help
---
# Module 4: Links between sites

Sites that share a content tree link to each other, and the teams that edit them share one administration. This module
closes the multisite part of the course: why links into another site break and how `ezurlmultisite` fixes them, how
to add a new site to its settings, and how to keep each team in its own site.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Duration | 2 hours 25 minutes (with one 10-minute break) |
| Lessons | 3 |
| Level | Intermediate to advanced |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 4.1: Why links to another site break](why-links-to-another-site-break.md) | 35 minutes | Predict broken and resolved cross-site links for a given installation |
| [Lesson 4.2: Configuring cross-site links](configuring-cross-site-links.md) | 50 minutes | Add `site_b` to `ezurlmultisite`, clear the caches, follow a link from the main site |
| [Lesson 4.3: One administration for several sites](one-administration-for-several-sites.md) | 50 minutes | Limit the Editor role to *Site B* for a team and test it with a test user |

## Preparing this module

- Learners' labs come from modules 1 to 3; `site_b` has a `PathPrefix` (lesson 2.1, step 6, not undone). Ask learners
  who undid it to set it again.
- Check on one lab whether the installer wrote `settings/override/ezurlmultisite.ini.append.php` (it does for the
  multisite site packages) and show it once on the projector.
- For lesson 4.2, the search index must contain the folder *News*: run `php bin/php/updatesearchindex.php` on each lab
  beforehand if search results are empty.
- For lesson 4.3, learners need an administrator account; test users get addresses at `example.invalid`, and the
  training installation must not send real mail.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 4.1 | 35 |
| Lesson 4.2 | 50 |
| Break | 10 |
| Lesson 4.3 | 50 |
| **Total** | **145** |

## Related pages

- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
