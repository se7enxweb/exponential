---
title: Covering an extension release
audience: press
category: press-media
order: 107
summary: How to report on a release of one of the extensions around Exponential - what an extension is, where its release notes and version live, how its version relates to the kernel's, and how to credit the projects behind it.
keywords: extension, extension release, eztags, cjw_newsletter, ezjscore, expai, Exponential Layouts, Composer, ezinfo.php, extension.xml, release notes, credit
type: help
---
# Covering an extension release

This page is for technical press, bloggers and creators who write about a release of an Exponential extension rather
than of Exponential itself. Extensions have their own repositories, version numbers and release notes. A good story
keeps the extension's version apart from the kernel's and credits the people whose work it carries.

## Contents

1. [What an extension is](#what-an-extension-is)
2. [Where to find the facts](#where-to-find-the-facts)
3. [Extension versions and kernel versions](#extension-versions-and-kernel-versions)
4. [Credit and naming](#credit-and-naming)
5. [A short template](#a-short-template)

## What an extension is

Extensions add features to the kernel. "Every extension except the four kernel-shipped ones is a Composer package of
type `ezpublish-legacy-extension`", installed into `extension/<name>`, and "Being on disk does not make an extension
active" ([Getting the code](../../install/03-getting-the-code.md#35-extensions-are-composer-packages)). The kernel's
`composer.json` requires 66 se7enxweb packages ([Who is 7x?, 7x in numbers](../who-is-7x.md#7x-in-numbers)); the
[Extensions guide](../../guides/extensions.md) explains how they are found, installed and released.

So "a new version of extension X" usually means: site owners can update that one package; nothing changes for them
until they do.

## Where to find the facts

| Fact | Where |
|---|---|
| Latest tagged release of each extension | The index [Release notes of the extensions ...](../../../changelogs/extensions/README.md) |
| What a release contains | The extension's page in that index, for example [eztags](../../../changelogs/extensions/eztags.md) |
| Notes kept in the extension's own repository | expai (Exponential Intelligence) and expqueue, linked from the index |
| The version installed on a site | `ezinfo.php` and `extension.xml` of the extension, and **Setup > Extensions** ([index, How to find the version you run](../../../changelogs/extensions/README.md#how-to-find-the-version-you-run)) |
| Which version the kernel's development line requires | [6.0.15 changelog, required packages](../../../changelogs/6.0/6.0.15.md#required-packages-root-composerjson-2026-10-10-to-2026-10-12) |
| The story of an extension over time | [Extension chronicles](../../history/extensions/README.md) |

The release rule for extensions is written down: the version is written into `ezinfo.php` and `extension.xml`,
committed, and then tagged; a wrong version is fixed by the next one
([Extensions guide, Release it](../../guides/extensions.md#7-release-it)).

## Extension versions and kernel versions

| Mistake | Why it is wrong | Correct |
|---|---|---|
| "eztags 2.4.11 is part of Exponential 6.0.14" | Extension and kernel versions are independent | "eztags 2.4.11, an extension for Exponential" |
| "Exponential gets a newsletter update" | The kernel did not change | "The cjw_newsletter extension has a new release" |
| "Every site now has feature Y" | Extensions must be updated, and some are inactive until switched on | "Sites that update the extension get Y" |
| Comparing an extension's number with the kernel's | They count separately; the last position keeps counting | Print each number as it is ([How version numbers work](how-version-numbers-work.md)) |

Four extensions ship inside the kernel itself ([Getting the code](../../install/03-getting-the-code.md#35-extensions-are-composer-packages)).
One of them, ezjscore, also has release notes of its own, with 1.5.5 as the latest release in the index on
18 October 2026 ([ezjscore release notes](../../../changelogs/extensions/ezjscore.md)). When in doubt, quote the version
from the extension's release notes page and link it.

## Credit and naming

- Many extensions began in the eZ Publish years and keep historic names such as `eztags` or `ezjscore`. These are
  code identifiers and are never renamed ([Brand and naming rules](../business/brand-and-naming-rules.md#code-identifiers-stay-as-they-are)).
- Credit the origin where the sources name it: `cjw_newsletter` comes from CJW Network; Exponential Layouts brings
  "the Netgen Layouts concept to the Exponential kernel" ([Who is 7x?, Contributors and friends](../who-is-7x.md#contributors-and-friends-of-the-project)).
- Do not name an individual developer unless they agreed; name the project or organization
  ([Attributing statements](attributing-statements.md)).

## A short template

```text
<Extension name> <version> released for Exponential

<Extension name>, an extension for the Exponential content management system, has a new release,
<version>, tagged on <date> (<link to its release notes>). It <one sentence: what the extension does>.
The release <two or three changes, in the words of the release notes>.

Sites update it as one Composer package; the release notes list what to check. The extension's
repository is <link>. <Credit line, if the sources name an origin.>
```

## Related pages

- [Covering a release](covering-a-release.md)
- [Where changelogs and release notes live](where-changelogs-and-release-notes-live.md)
- [Release story templates](release-story-templates.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
