---
title: Writing about a release
audience: advocate
category: advocacy
order: 53
summary: How to turn an Exponential release into an accurate post - wait for the tag, read the changelog and the history, pick what matters per audience, link the feature pages, never promise dates - with a post template and a checklist.
keywords: release, release notes, changelog, what is new, announcement, post, tag, version, history, features, upgrade, template
type: help
---
# Writing about a release

This page is for advocates who want to tell their audience what a new version of Exponential brings. A release post is
useful when it translates the changelog into "what this means for you", and harmful when it promises things that are
not in the release.

## Contents

1. [Wait for the tag](#wait-for-the-tag)
2. [Where to read what changed](#where-to-read-what-changed)
3. [Pick what matters, per audience](#pick-what-matters-per-audience)
4. [The post template](#the-post-template)
5. [Checklist](#checklist)

## Wait for the tag

A release exists when its tag exists. Before that, the work is "in development" (for example, "6.0.15 is in
development", [Who is 7x?](../who-is-7x.md#the-60x-releases)).

```bash
git fetch --tags
git tag -l 'v6.0.*' --sort=version:refname | tail -3
```

Also check the [releases on GitHub](https://github.com/se7enxweb/exponential/releases). Sort versions with
`--sort=version:refname`, never by name ([Releases and version numbers](../who-is-the-exponential-foundation.md#releases-and-version-numbers)).

You may write about **work in progress** too, if you say so clearly: "In the development line, not released yet".

## Where to read what changed

| Source | What it gives you |
|---|---|
| [Changelogs](../../../../changelogs/README.md), e.g. [6.0.15](../../../../changelogs/6.0/6.0.15.md) | The list of changes of a version |
| [The history](../../history/README.md) | Month pages that explain what each change makes possible |
| Feature pages in `doc/en/us/features/6.0/` | How to use each new feature |
| Notes on backward compatibility in `doc/en/us/bc/6.0/` | What to check when upgrading |
| [Upgrading](../../install/11-upgrading.md) | How to upgrade safely |

## Pick what matters, per audience

| Audience | Look for | Say it as |
|---|---|---|
| Editors | New screens, buttons, editor features | "You can now ..." |
| Administrators | Security, settings, engines, upgrade notes | "After upgrading, check ..." |
| Developers | APIs, template operators, extension points, PHP versions | "Extensions can now ..." |
| Everyone | One or two headline features | A sentence each, with a link |

Pick three to five items. Link each to its feature page. Leave the full list to the changelog.

## The post template

```text
# What's new in Exponential <version>

Exponential <version> was released on <date> (tag v<version>). Here are the changes I think matter most
for <audience>. The full list is in the changelog: <link>.

## <Headline feature 1>
<What it does for the reader, 2-3 sentences.> How to use it: <feature page link>.

## <Feature 2>
...

## Before you upgrade
Back up first and follow the upgrade chapter: <link>. Check the notes on backward compatibility: <link>.
Setup > Upgrade check compares your files and database with the release: <link>.

## Thanks
<Contributors who agreed to be named, or "everyone who sent pull requests, reports and translations".>

Questions: share.exponential.earth/forums, t.me/exponentialcms
<Disclosure line.>
```

## Checklist

```text
[ ] The tag exists (or the post says clearly "in development")
[ ] Every feature mentioned is in the changelog or the history, with a link to its page
[ ] No promised dates for the next release; no features that are not documented
[ ] Upgrade advice links the upgrade chapter; no "just run the installer" (it must never run over an existing site)
[ ] Version written as "Exponential 6.0.x" in text, "v6.0.x" only for the tag
[ ] Credits only for people who agreed, or general thanks
[ ] Shared in the forums and on social media with the right names
```

The upgrade rule comes from [Upgrading](../../install/11-upgrading.md); the version writing rule from the
[Brand and naming rules](../business/brand-and-naming-rules.md#the-product-exponential).

## Related pages

- [Writing history posts](writing-history-posts.md)
- [Social post templates](social-post-templates.md)
- [Testing the development line](testing-the-development-line.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
