---
title: "Choosing where to publish"
audience: designer, sales-marketing
category: theme-distribution
order: 20
summary: The places a theme for Exponential can be published - a Git host (GitHub), Packagist for Composer, projects.exponential.earth, announcements on share.exponential.earth, and your own website - what each gives you and in which order to set them up.
keywords: publish, GitHub, Packagist, projects.exponential.earth, share.exponential.earth, ezpedia, own website, where to publish, distribution channels
type: help
---
# Choosing where to publish

This page is for designers deciding where their theme should live publicly. Most themes use several places at once;
each has a different job.

## The places and their jobs

| Place | Its job | What users do there |
|---|---|---|
| A Git host (the 7x themes use GitHub) | The source, history, issues, releases | Read code, report issues, send pull requests, download releases |
| [Packagist](https://packagist.org/) | The Composer index | `composer require yourvendor/mytheme` |
| [projects.exponential.earth](https://projects.exponential.earth/) | The Exponential community forge: project pages with news, downloads, forums, Git and Composer integration | Browse projects by type or organization, follow news and RSS feeds |
| [share.exponential.earth](https://share.exponential.earth/) | The community portal: forums (including Extensions and Setup & design), blogs, tutorials | Ask questions, read announcements and tutorials |
| [eZpedia](https://ezpedia.exponential.earth/) | The community wiki under the GNU FDL | Read how-tos and reference articles |
| Your own website | Your product page, demos, support and sales | Decide, buy support, contact you |

What each community site offers was checked on 18 October 2026; see
[Listing on projects.exponential.earth](listing-on-projects-exponential-earth.md) and
[Announcing on share.exponential.earth](announcing-on-share-exponential-earth.md).

## A sensible order

1. **Git host first.** Everything else links to it ([Creating the GitHub repository](creating-the-github-repository.md)).
2. **First release** with a tag ([GitHub releases](github-releases.md)).
3. **Packagist**, once a tagged release exists ([Submitting a theme to Packagist](submitting-to-packagist.md)).
4. **Community listing and announcement**, once the theme can be installed by someone else.
5. **Your own page and demo**, whenever it helps ([Running a demo site](demo-sites.md)).

## Public or private

| Public repository | Private repository |
|---|---|
| Anyone can install, learn and contribute | Only the people you give access |
| Free hosting on most Git hosts and on Packagist | Packagist lists public packages; private ones need a private Composer repository |
| Fits the GPL and the community | Fits work for one client that is not yours to publish |

Work done for a client belongs to whoever the contract says. Publish it only with that right
([Contracts and terms](../sales/contracts-and-terms.md)).

## Site packages: contribute them

If your theme comes with a complete site package (the kind the setup wizard installs a new site from), 7x and the
Exponential Foundation strongly encourage you to submit it by pull request to
[se7enxweb/exponential.packages.exponential.earth](https://github.com/se7enxweb/exponential.packages.exponential.earth),
the repository of the package server the setup wizard uses. Then every new installation can start from it. How:
[Contribute your site package](../../site-packages/contribute-your-site-package.md).

## Related pages

- [Composer package or .ezpkg](../extensions/composer-or-ezpkg.md)
- [Where Exponential themes are found today](../sales/where-themes-are-found.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
