---
title: Twenty-minute talk slide outline
audience: advocate
category: advocacy
order: 42
summary: A slide-by-slide outline for a 20-minute talk about Exponential - 16 slides with what goes on each, what you say, the source, and where a short live demo fits - plus tailoring notes and a question bank.
keywords: talk, presentation, 20 minutes, slides, outline, conference, meetup, user group, speaker notes, live demo, questions
type: help
---
# Twenty-minute talk slide outline

This page is for advocates preparing a full talk: a meetup slot, a user group, a conference session or a university
lecture. It gives 16 slides with speaker notes and sources, room for a five-minute live demo, and a bank of questions
you are likely to hear.

## Contents

1. [Structure](#structure)
2. [The slides](#the-slides)
3. [The live demo segment](#the-live-demo-segment)
4. [Tailoring](#tailoring)
5. [Question bank](#question-bank)
6. [Before you give it](#before-you-give-it)

## Structure

| Part | Minutes | Slides |
|---|---|---|
| Opening and who you are | 1 | 1 to 2 |
| What Exponential is | 4 | 3 to 6 |
| Who stands behind it | 2 | 7 to 8 |
| Live demo | 5 | 9 |
| What is new | 3 | 10 to 12 |
| Open source and community | 3 | 13 to 15 |
| Close | 2 | 16 |

## The slides

| # | Slide title | On the slide | Say | Source |
|---|---|---|---|---|
| 1 | Exponential | Title, your name | Disclose who you are and whether anyone pays you | [Disclosure](disclosure-and-conflicts-of-interest.md) |
| 2 | Why I care | One photo or sentence about your use | Your own story, no customer names without consent | [Protecting people's privacy](protecting-peoples-privacy.md) |
| 3 | What it is | "PHP framework + CMS + shop" | The README's definition | [Exponential 6](../who-is-the-exponential-foundation.md#exponential-6-the-kernel) |
| 4 | Your content, your model | A content class with fields | Content types are defined in the administration | [Content model](../../guides/content-model-and-editing.md) |
| 5 | Many sites, many languages | Siteaccesses, language badges | 368 locale files; administration translations (the current totals are at the top of [Languages in Exponential](../../languages.md)) | [Languages and accessibility](../who-is-the-exponential-foundation.md#languages-and-accessibility) |
| 6 | Runs where you want | PHP 8.0-8.5, six databases, OS tiers | Name SQLite as the default | [Talking points: hosting](talking-points-hosting-databases-and-freedom.md) |
| 7 | A long story | Timeline 1999 to 2026 | The history, with the old name in the past tense | [Talking points: longevity](talking-points-longevity-and-history.md) |
| 8 | Who is behind it | 7x and the Exponential Foundation side by side | Two organizations, one project | [7x and the Foundation](7x-and-the-foundation-for-advocates.md) |
| 9 | Demo | "Live demo" | See below | [Giving demos](giving-demos-as-an-advocate.md) |
| 10 | Velocity | "A PHP web server" | Recommended engine; young (2026) | [Talking points: speed](talking-points-speed-and-velocity.md) |
| 11 | Exponential Intelligence | "Drafts only" | Never publishes; choose the region or a local model | [Talking points: Exponential Intelligence](talking-points-exponential-intelligence.md) |
| 12 | Security you can see | Process diagram | Report privately, first answer within one business day, next release | [Talking points: security](talking-points-security.md) |
| 13 | Free software | GPL + GFDL | Code and docs are free | [Talking points: open source](talking-points-open-source-and-licensing.md) |
| 14 | Every pull request is merged | A merged PR screenshot (public) | The project's rule | [How the project grows](../who-is-the-exponential-foundation.md#how-the-project-grows) |
| 15 | Join | Forums, Telegram, eZpedia, GitHub, Projects | Only documented channels | [The community channels](community-channels.md) |
| 16 | Try it | `./console exp:install` + links | One command, SQLite | [Install in one command](../../features/6.0/install-in-one-command.md) |

## The live demo segment

Five minutes, on your own installation, following one path from [Giving demos as an advocate](giving-demos-as-an-advocate.md#three-ready-demo-paths).
Have a **backup**: three screenshots of the same path on slides 9a to 9c, in case the network or the laptop fails.
Rules: [Live demo safety](live-demo-safety.md).

## Tailoring

| Audience | Swap | For |
|---|---|---|
| PHP developers | Slides 5 and 11 | Extensions and the RAD tools; Git first ([Talking points for developers](talking-points-for-developers.md)) |
| Editors and content teams | Slides 6 and 10 | The layouts editor; versions and preview ([Talking points for editors](talking-points-for-editors.md)) |
| Students | Slide 12 | "Your first pull request" ([Mentoring a first contribution](mentoring-a-first-contribution.md)) |
| Accessibility community | Slides 10 and 11 | Accessibility features ([Talking points: accessibility](talking-points-accessibility.md)) |

## Question bank

| Question | Short answer and page |
|---|---|
| "Is it still maintained?" | Fifteen releases since January 2024 ([Is it still alive?](objection-is-it-still-alive.md)) |
| "How many people use it?" | "The sources don't count users." ([Claims you must not make](claims-you-must-not-make.md)) |
| "Why not CMS X?" | Ask about needs ([Why not another CMS?](objection-why-not-another-cms.md)) |
| "Does it do headless?" | REST API v1 and v2; GraphQL in the Symfony line ([PHP and modern technology](objection-php-and-modern-technology.md)) |
| "Who do I pay for support?" | Any provider; 7x is one; sponsors fund the project ([7x and the Foundation](7x-and-the-foundation-for-advocates.md)) |
| "Is the AI safe?" | Drafts only; controls listed ([What about the AI features?](objection-ai-features.md)) |
| "How do I contribute?" | [Develop with Git](../develop-with-git.md) |

## Before you give it

- [ ] Every number checked against its source the same week.
- [ ] Slides follow [Accessible slides and visuals](accessible-slides-and-visuals.md).
- [ ] Demo installation prepared and rehearsed; screenshots as backup.
- [ ] Slides published afterwards under a license you choose, with sources
      ([Citing sources and licensing your writing](citing-sources-and-licensing-your-writing.md)).

## Related pages

- [Lightning talk script](lightning-talk-script.md)
- [Hands-on workshop outline](hands-on-workshop-outline.md)
- [Demo scripts, The 45-minute demo](../business/demo-scripts.md#the-45-minute-demo)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
