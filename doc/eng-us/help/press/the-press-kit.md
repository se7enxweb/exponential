---
title: The press kit
audience: press, partner, advocate
category: press-media
order: 3
summary: What the official Exponential press kit on exponential.earth contains, how to download it, its usage guidelines word for word, which parts were still planned when this page was written, and which facts in it are older than the repository - plus the press material this help adds.
keywords: press kit, media kit, download, logos, screenshots, wallpapers, videos, press brief, usage guidelines, brand colors, tagline, exponential.earth
featured: true
type: help
---
# The press kit

This page is for journalists, editors and creators who want the official press material of Exponential. It describes
the press kit published on the project website, quotes its usage guidelines, and tells you which parts to complete or
correct from this help. It was written after downloading and opening the kit on **18 October 2026**; on **19 October
2026** the page still offered the same download, version 1.0.0, and its text had not changed.

## Contents

1. [Where the kit is](#where-the-kit-is)
2. [What the kit contains](#what-the-kit-contains)
3. [The usage guidelines, word for word](#the-usage-guidelines-word-for-word)
4. [What was still planned](#what-was-still-planned)
5. [Facts in the kit that are out of date](#facts-in-the-kit-that-are-out-of-date)
6. [What this help adds to the kit](#what-this-help-adds-to-the-kit)
7. [Asking for material that is missing](#asking-for-material-that-is-missing)

## Where the kit is

| What | Address |
|---|---|
| The press kit page | <https://exponential.earth/about/press-kit> |
| The download (ZIP, version 1.0.0, June 2026) | <https://exponential.earth/content/download/4252/63267/file/exponential-press-kit-v1-0-0.zip> |
| Screenshot galleries | <https://exponential.earth/about/about-exponential/screenshots> |
| Earlier press releases | <https://exponential.earth/about/in-the-news> |

The page introduces itself as "Everything you need to know about Exponential for media coverage, reviews, and
partnerships". It gives the app name **Exponential**, the tagline "The simple, flexible and reliable website
application engine", the system requirements "Works Everywhere, On Any OS", contact links (e-mail, Discord, X,
WhatsApp, Telegram, Slack) and a button "Download Complete Press Kit".

## What the kit contains

The ZIP file `exponential-press-kit-v1-0-0.zip` (about 43 KB) holds:

| File or folder | What is in it |
|---|---|
| `presskit/README.md` | The kit's text: about Exponential, key facts, the product family, the list of assets, usage guidelines, links and a contact |
| `PRESS_BRIEF.pdf` | A four-page printable media brief |
| `presskit.html` | A stand-alone HTML version of the press kit page |
| `presskit/logos/` | A `LOGOS_README.txt` that lists the planned logo files and the brand colors |
| `presskit/screenshots/` | A `SCREENSHOTS_README.txt` that lists the planned screenshots |
| `presskit/wallpapers/` | A `WALLPAPERS_README.txt` that lists the planned wallpapers |
| `presskit/videos/` | A `VIDEOS_README.txt` that lists the planned demo videos |
| `exponential-presskit.zip` | A nested copy of the kit |

The kit names **support@exponential.earth** as its contact for source files, video files and commercial licensing
inquiries.

## The usage guidelines, word for word

From the kit's `README.md`, section "Usage Guidelines":

> - Logos and brand assets may be used for editorial, review, and partnership coverage.
> - Do not alter logo colors, proportions, or add effects.
> - Do not use assets to imply endorsement without written permission.
> - For commercial licensing inquiries, contact: support@exponential.earth

These are the only published rules for the brand assets. The documentation of the project sets no further logo rules
([Brand and naming rules](../business/brand-and-naming-rules.md#what-the-sources-do-not-decide)). How to apply them is
on [Logos and brand assets](logos-and-brand-assets.md).

## What was still planned

In version 1.0.0 the asset folders hold only their README files: they list the files that are meant to be added, not
the files themselves.

| Folder | Files listed as planned |
|---|---|
| logos | A full-color vector logo, a dark and a white version, app icons of 512, 256, 128 and 64 pixels, a text-only wordmark |
| screenshots | Admin dashboard, content edit, content tree, content classes, media library, front-end home page, article view, mobile view (recommended 2560 x 1600 PNG) |
| wallpapers | Full HD, 2K, 4K, mobile, light and dark backgrounds |
| videos | Six suggested videos: a quick overview, content editing, an admin tour, installing, an Exponential Platform Nexus overview, MongoDB |

The logos README also lists the **brand colors**: Dark Background `#2b2d42`, Accent / Red `#e63946`, Foreground
`#0f0f0f`, Light Surface `#f8f8f8`.

Until the files are in the kit, ask for them by e-mail ([Asking for material that is missing](#asking-for-material-that-is-missing))
or take your own screenshots ([Taking your own screenshots](taking-your-own-screenshots.md)).

## Facts in the kit that are out of date

The kit is dated June 2026. The repository has moved on since, and for release facts the repository wins:

| The kit says | The repository says (19 October 2026) | Source |
|---|---|---|
| Current stable 6.0.13 | The newest release is 6.0.14 (7 June 2026); the current line, 6.0.15, is in development and had no release tag on 19 October 2026 | [The 6.0.x releases at a glance](the-6-0-x-releases-at-a-glance.md) |
| 6.0.14 "Experimental" with MongoDB support | 6.0.14 is a release; MongoDB 8 as a database arrived on 1 June 2026 | [Who is 7x?, timeline](../who-is-7x.md#nearly-thirty-years-the-timeline) |
| "stewarded by 7x (se7enxweb) since 1998" | The copyright notice of 7x and the Foundation starts in 1998; 7x was founded as Brookins Consulting in 2001; its project was launched in December 2023 | [7x in three stages: 2014, 2022, 2023](7x-in-three-stages.md) |
| "Founded 1998 (as eZ Publish steward)" | eZ Systems developed eZ Publish from 1999; 7x took over leadership in 2023 | [Timeline for reporters](timeline-for-reporters.md) |

The kit predates the news of 20 October 2026, which it therefore does not mention: **as of 6.0.15 the Velocity
Internet / Web Application Server is part of Exponential.** The root `composer.json` requires
`se7enxweb/exponential-velocity ~0.0.4.55` (through `se7enxweb/exponential-php-floor`, on PHP 8.0 and later) (until then it was only suggested), and Velocity 0.0.4.54 runs on PHP 8.0
and later. Facts you can print, each with its source:

| Fact | Source |
|---|---|
| Velocity is part of Exponential as of 6.0.15 and installed with every copy by `composer install` | [Velocity is part of Exponential 6.0.15](../velocity-is-part-of-exponential-6-0-15.md); `composer.json` |
| Velocity runs on PHP 8.0 to 8.5, the stock PHP 8.0 of Red Hat Enterprise Linux 9 included | [PHP 8.0 support](../../bc/6.0/php-8.0-support.md) |
| Velocity is the recommended engine for every stage; FrankenPHP is the production-ready alternative; Apache and nginx keep working | [Velocity engines](../../bc/6.0/velocity-engines.md) |
| The project describes it as a turning point, a cornerstone and a capstone: a new level of application system integration, with even greater features out of the box in the core product | [Velocity is part of Exponential 6.0.15](../velocity-is-part-of-exponential-6-0-15.md#why-it-is-a-turning-point) |
| Exponential is one working stack, not just a framework: server, RAD platform, CMS, digital experience and e-commerce | [One stack, not just a framework](../velocity-is-part-of-exponential-6-0-15.md#one-stack-not-just-a-framework) |

Do not call Velocity or FrankenPHP "experimental"; neither is. Print only the performance figures the documentation
already states, with their method ([How Velocity figures are measured](story-how-velocity-figures-are-measured.md)).

The kit's key-facts table also contains two counts ("Active Properties" and "Forge Projects") for which this
documentation has no source. Do not print them without asking how they were counted
([Verifying numbers](verifying-numbers.md)).

## What this help adds to the kit

| You need | Page |
|---|---|
| A sourced fact sheet | [Fact sheet for journalists](fact-sheet-for-journalists.md) |
| Descriptions of several lengths | [Boilerplate descriptions you may copy](boilerplate-descriptions.md) |
| A dated timeline | [Timeline for reporters](timeline-for-reporters.md) |
| The names spelled right | [Naming quick reference for news editors](naming-quick-reference-for-editors.md) |
| Story angles by feature | [Feature stories: index](feature-stories-index.md) |
| Screenshots under a known license | [Screenshots: what you may use](screenshots-what-you-may-use.md) |
| Contacts | [Press contacts](press-contacts.md) |

## Asking for material that is missing

```text
To: support@exponential.earth
Subject: Press kit request - <outlet> - <deadline>

Hello,
I am <name>, <role> at <outlet>, writing about <topic> for publication on <date>.
I downloaded press kit v1.0.0. Could you send me <the logo files / screenshots / video files> named in its
README? I will use them for editorial coverage as the kit's usage guidelines allow.
Thank you,
<name, outlet, link>
```

## Related pages

- [Logos and brand assets](logos-and-brand-assets.md)
- [Press contacts](press-contacts.md)
- [Earlier press releases and coverage](earlier-press-releases-and-coverage.md)
- [Using partner logos and the certified mark](../partners/partner-logos-and-marks.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
