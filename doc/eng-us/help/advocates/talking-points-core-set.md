---
title: "Talking points: the core set"
audience: advocate
category: advocacy
order: 10
summary: The ten talking points every Exponential advocate can use, each as a sentence to say, the source that proves it and how a listener can check it, with guidance on choosing points for your audience.
keywords: talking points, key messages, messaging, pitch, core set, proof, sources, open source, GPL, content model, multisite, languages, security, Velocity, layouts, help, community
featured: true
type: help
---
# Talking points: the core set

This page is for every advocate. It gives the ten talking points that work in almost any conversation about
Exponential. Each has a sentence you can say, the source that proves it, and a way the listener can check it. The
audience pages build on these: [editors](talking-points-for-editors.md), [developers](talking-points-for-developers.md),
[administrators](talking-points-for-administrators.md), [decision makers](talking-points-for-decision-makers.md).

## Contents

1. [How to use talking points](#how-to-use-talking-points)
2. [The ten points](#the-ten-points)
3. [Choosing three for your listener](#choosing-three-for-your-listener)
4. [Turning a point into a story](#turning-a-point-into-a-story)
5. [Before you use them in public](#before-you-use-them-in-public)

## How to use talking points

- **Do not recite them.** Pick the two or three that answer what your listener cares about.
- **Say the source.** "The README says ...", "The security policy says ...". It makes you credible and teaches the
  listener where to look.
- **Offer a check.** A link, a command, a screen in the administration.
- **Stop when you are not sure.** "I do not know; let me find the source" is a strong answer.

## The ten points

| # | Say | Source | Let them check |
|---|---|---|---|
| 1 | "Exponential is a professional PHP application framework with advanced CMS functionality. Its most notable feature is a fully customizable content model." | [README](https://github.com/se7enxweb/exponential/blob/main/README.md), [Exponential 6](../who-is-the-exponential-foundation.md#exponential-6-the-kernel) | **Setup > Classes** on any installation |
| 2 | "It is free software: the code under the GNU GPL version 2 or later, the documentation under the GNU FDL 1.3." | [LICENSE](https://github.com/se7enxweb/exponential/blob/main/LICENSE), [The license of this documentation](../../README-LICENSE.md) | Read the files |
| 3 | "It is actively maintained: fifteen 6.0.x releases since 1 January 2024, with 6.0.15 in development and tests on every push." | [Who is 7x?, The 6.0.x releases](../who-is-7x.md#the-60x-releases), [CI](../../bc/6.0/ci.md) | `git tag -l 'v6.0.*' --sort=version:refname` |
| 4 | "A company and a nonprofit stand behind it: 7x leads it, and the Exponential Foundation operates the project." | [The Foundation and 7x](../who-is-the-exponential-foundation.md#the-foundation-and-7x) | The two websites |
| 5 | "It runs where you want: PHP 8.0 to 8.5, six databases from SQLite to Oracle, and Linux, BSD, macOS or Windows." | [PHP versions](../who-is-the-exponential-foundation.md#php-versions), [Database support](../database-support.md), [Operating system support](../os-support.md) | **Setup > System information** |
| 6 | "One installation can serve several sites and many languages, including right to left." | [Feature highlights](../business/feature-highlights-for-customers.md#several-sites-from-one-installation), [Languages in Exponential](../../languages.md) | The globe button and the language badges in the administration |
| 7 | "Security is a process you can see: private reporting with a first answer within one business day, a Content Security Policy, an audit trail and two-step sign-in." | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md), [Security hardening](../security-hardening.md) | The **Audit** tab, `/user2fa/setup` |
| 8 | "Editors build pages themselves with Exponential Layouts, and get help writing from Exponential Intelligence, which only writes drafts." | [The layouts editor](../layouts-editor.md), [expai](../../features/6.0/extensions/expai.md#in-short) | The **Layouts** tab |
| 9 | "Help is built in: the documentation is inside the administration, with a help button on every page." | [The help system](../../features/6.0/help-system.md) | The "?" in the header |
| 10 | "The community is open: every pull request is merged, and the forums, the Telegram chat and the issue trackers are public." | [How the project grows](../who-is-the-exponential-foundation.md#how-the-project-grows), [Community places](../who-is-the-exponential-foundation.md#community-places) | The channels |

## Choosing three for your listener

| Listener | Start with | Then | Deep page |
|---|---|---|---|
| Editor | 8, 9 | 6 | [Talking points for editors](talking-points-for-editors.md) |
| Developer | 1, 10 | 5 | [Talking points for developers](talking-points-for-developers.md) |
| Administrator or host | 5, 7 | 3 | [Talking points for administrators](talking-points-for-administrators.md) |
| Decision maker | 3, 4 | 2 | [Talking points for decision makers](talking-points-for-decision-makers.md) |
| Open source person | 2, 10 | 4 | [Open source and licensing](talking-points-open-source-and-licensing.md) |
| Accessibility person | 9, then [accessibility](talking-points-accessibility.md) | 6 | [Talking points: accessibility](talking-points-accessibility.md) |
| Security person | 7 | 3 | [Talking points: security](talking-points-security.md) |

## Turning a point into a story

A point is a claim; a story makes it stick. Use the pattern **situation, action, result, source**:

```text
Situation  "A colleague's team had three sites in two languages and three separate installations."
Action     "They moved them into one Exponential installation with one siteaccess per site."   (your experience)
Result     "Editors now sign in once and translate an item side by side."                     (your experience)
Source     "Siteaccesses and translations are described in the help: Languages, and the feature pages."
```

Only tell stories that are yours or that you have permission to tell, and keep people and customers anonymous unless
they agreed ([Protecting people's privacy](protecting-peoples-privacy.md)).

## Before you use them in public

- [ ] Open each source once and check that it still says what the point says (sources change with releases).
- [ ] Read [Claims you must not make](claims-you-must-not-make.md).
- [ ] Use the right names ([Naming rules quick card](naming-rules-quick-card.md)).
- [ ] Have the links ready to paste ([The advocate toolkit](advocate-toolkit.md)).

## Related pages

- [Claims you must not make](claims-you-must-not-make.md)
- [Where the facts live](where-the-facts-live.md)
- [Positioning and value proposition](../business/positioning-and-value-proposition.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
