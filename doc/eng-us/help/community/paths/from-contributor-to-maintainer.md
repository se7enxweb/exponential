---
title: From contributor to maintainer
audience: community, developer, designer, supporter
category: community
order: 62
summary: How a regular Exponential contributor takes on maintenance - of their own extension, of an abandoned one, or of an area of the project - what maintainers do, the release discipline they follow, and how to hand over when they stop.
keywords: maintainer, maintenance, extension, abandoned extension, adopt, releases, tags, versioning, handover, responsibility
type: help
---
# From contributor to maintainer

A maintainer is someone who takes responsibility for keeping a piece of the Exponential world working over time:
answering issues, reviewing changes and making releases.

## Contents

1. [Kinds of maintainership](#kinds-of-maintainership)
2. [What a maintainer does](#what-a-maintainer-does)
3. [Release discipline](#release-discipline)
4. [Handing over](#handing-over)

## Kinds of maintainership

| Kind | How it starts | Page |
|---|---|---|
| Your own extension, theme or site package | You publish it | [Sharing extensions and themes](../contributing/sharing-extensions-and-themes.md) |
| An abandoned extension | Its author has stopped; you fork or adopt it | [Adopting an abandoned extension](../../extension-development/part-5-extend-fork-contribute/adopting-an-abandoned-extension.md) |
| An area of the project (a translation, a group of help pages) | You have contributed there for a long time | [From user to contributor](from-user-to-contributor.md) |

The sources describe no formal appointment of maintainers for areas of the kernel; merges into the kernel are made
by the project's maintainers ([Who decides](../how-it-works/who-decides.md)).

## What a maintainer does

- Answers issues, even with "not planned".
- Reviews pull requests and helps contributors through the rounds
  ([Reviewing and being reviewed](../contributing/reviewing-and-being-reviewed.md)).
- Tests against each new 6.0.x release and PHP version.
- Keeps the README, the changelog and the version fields current.
- Follows the code of conduct as one of the "community leaders" of their space (`CODE_OF_CONDUCT.md`).

The full description: [The maintainer path](../../extension-development/part-5-extend-fork-contribute/the-maintainer-path.md)
and [The maintenance life cycle](../../extension-development/part-7-releasing/the-maintenance-life-cycle.md).

## Release discipline

| Rule | Page |
|---|---|
| A pushed tag is permanent; a mistake is fixed by the next version | [Tags are permanent](../../extension-development/part-7-releasing/tags-are-permanent.md) |
| Choose the next number from what is published, sorted by version | [Choosing the next version](../../extension-development/part-7-releasing/choosing-the-next-version.md) |
| `ezinfo.php` and `extension.xml` carry the release's version | [Bumping ezinfo.php and extension.xml](../../extension-development/part-7-releasing/bumping-ezinfo-and-extension-xml.md) |
| Packagist must learn about new tags | [The Packagist webhook](../../extension-development/part-7-releasing/the-packagist-webhook.md) |
| Run through a checklist before every release | [The release checklist](../../extension-development/part-7-releasing/the-release-checklist.md) |

## Handing over

When you can no longer maintain something, say so in its README and in the forums, and name a successor if there is
one. An honest "unmaintained" note is better for users than silence.

## Related pages

- [Paths through the community](README.md)
- [Mentoring](../helping-others/mentoring.md)
- [Keeping a fork in sync](../../extension-development/part-5-extend-fork-contribute/keeping-a-fork-in-sync.md)
- [Growing the community through code](../../extension-development/part-5-extend-fork-contribute/growing-the-community-through-code.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
