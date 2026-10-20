---
title: "Story: multisite"
audience: press
category: press-media
order: 65
summary: A feature story about running several websites from one Exponential installation - siteaccesses, per-site settings in extensions, host and language matching, links between sites - with printable facts, sources, the isolation caveat and headline ideas.
keywords: story, multisite, several sites, siteaccess, one installation, host matching, language redirect, cross-site links, multi-site hosting, press
type: help
---
# Story: multisite

This page is for journalists and creators who write about organizations that run many websites. Exponential has
served several sites from one installation for a long time; the 6.0.x line made it easier to host and to link
between them. This page gives the story, the facts with sources, and one caveat you should print.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [What is new, and when](#what-is-new-and-when)
3. [Facts you can print](#facts-you-can-print)
4. [What not to claim](#what-not-to-claim)
5. [Explaining it to readers](#explaining-it-to-readers)
6. [Angles and headline ideas](#angles-and-headline-ideas)
7. [Who to ask](#who-to-ask)
8. [Related pages](#related-pages)

## The story in one paragraph

"Multi-site support" is one of the main features listed in the README
([README, Main Exponential features](https://github.com/se7enxweb/exponential/blob/main/README.md#main-exponential-features)). One installation can serve
several sites, "each a siteaccess with its own host, path, language and design"
([Who is 7x?, A CMS, a CMF and a shop in one](../who-is-7x.md#a-cms-a-cmf-and-a-shop-in-one)). Since January 2026 a
site that lives in its own extension can carry settings that apply only while its siteaccesses run; in October 2026
the 6.0.15 development line added matching by the start of a host name, a redirect to the visitor's language and
correct links from one site's pages to another site's content.

## What is new, and when

| What | When | Source |
|---|---|---|
| Per-site settings inside extensions; one INI cache clear for every project | January 2026 | [Per-site settings inside extensions](../../features/6.0/multi-site-ini-overrides.md) |
| Host matching by the start of a host, and a redirect to the language | 6 October 2026 | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#6-october-2026-host-matching-by-the-start-of-a-host-and-a-redirect-to-the-language-for-addresses-without-one) |
| Links to content of another site of a multisite installation | 13 October 2026 | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#13-october-2026-links-to-content-of-another-site-of-a-multisite-installation) |
| The site menu of the administration: the other sites a user may sign in to | 15 October 2026 | [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md#15-october-2026-the-site-menu-of-the-administration) |

The October changes belong to the 6.0.15 line, which is not released.

## Facts you can print

| Fact | Source |
|---|---|
| Several sites run from one installation, each a siteaccess with its own host, path, language and design. | [Who is 7x?](../who-is-7x.md#a-cms-a-cmf-and-a-shop-in-one) |
| Before January 2026, hosting ten sites meant one big settings directory or ten siteaccess directories; now each site's extension carries its own. | [Per-site settings inside extensions](../../features/6.0/multi-site-ini-overrides.md) |
| A host map can match `www.example.com.test.local` to the siteaccess of `www.example.com` (`HostMatchMethod=start`). | [Siteaccess by host prefix](../../features/6.0/siteaccess-host-and-language-matching.md#in-short) |
| The start page can redirect to the language a browser asks for, once, keeping the address and query. | same |
| Without the `ezurlmultisite` extension, a link to another site's content could point into the wrong site and answer 404; the extension fixes that. | [Links to content of another site](../../features/6.0/multisite-cross-site-links.md#in-short) |
| Editors sign in once and see the other sites they may use in the header's site menu. | [The admin4 design, What you get](../../features/6.0/admin4-design.md#what-you-get) |

## What not to claim

- "Each site is isolated from the others." For Velocity the README says a single server process serving several sites
  "is a routing convenience, not a security boundary"; it recommends one instance per site, each as its own
  operating-system user, when customers must not read each other's data
  ([README](https://github.com/se7enxweb/exponential/blob/main/README.md#when-a-traditional-server-is-still-the-right-answer)).
- "Unlimited sites." The sources give no maximum and no tested count.
- Customer names of multisite installations: none are published with consent in the sources.

## Explaining it to readers

```text
A "siteaccess" is one door into the same installation: a website, a language version of it, or the
administration. Each door can have its own address, design and language, while the content lives in one
shared tree. That is how one team can run several sites without several installations.
```

## Angles and headline ideas

```text
"Ten websites, one installation: multisite in Exponential"
"The right language at the front door: Exponential's language redirect"
"One content tree, many sites - and links that land on the right one"
```

## Who to ask

| Question | Ask | Where |
|---|---|---|
| Multisite design and hosting | 7x | info@se7enx.com, or <https://exponential.earth/about/contact-us> |
| Experiences from site builders | the community | <https://share.exponential.earth/forums> |

## Related pages

- [Multisite and multilingual planning](../leadership/multisite-and-multilingual-planning.md)
- [Story: languages and right to left](story-languages-and-right-to-left.md)
- [Story: hosting freedom](story-hosting-freedom.md)
- [Feature stories: index](feature-stories-index.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
