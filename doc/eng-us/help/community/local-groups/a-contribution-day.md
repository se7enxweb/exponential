---
title: A contribution day
audience: community, advocate, trainer, developer
category: community
order: 72
summary: How a local group or company runs a contribution day for Exponential - preparing tasks and test installations, a timetable, pairing newcomers with experienced members, the rules every contribution follows, and following up so the work gets merged.
keywords: contribution day, sprint, hackathon, group contribution, tasks, pull requests, translations, help pages, test installation, pairing, follow-up
type: help
---
# A contribution day

A contribution day is a few hours in which a group makes real contributions together: bug confirmations,
translations, help page fixes, small pull requests.

## Contents

1. [Before the day](#before-the-day)
2. [Timetable](#timetable)
3. [Task list](#task-list)
4. [Rules for every contribution](#rules-for-every-contribution)
5. [After the day](#after-the-day)

## Before the day

- Collect 10 to 20 small tasks, each finishable in an hour or two (see [Task list](#task-list)).
- Ask participants to create a GitHub account and, for code, a test installation from a Git clone on SQLite
  ([Develop with Git](../../develop-with-git.md)).
- Tell participants who will send kernel code about the CLA in advance; it takes time to sign and deliver
  ([The CLA explained](../contributing/the-cla-explained.md)).
- Name a contact person for problems ([Community safety and privacy](community-safety-and-privacy.md)).

## Timetable

| Time | Part |
|---|---|
| 0:00 | Welcome, code of conduct, how the day works |
| 0:15 | Setup help; pairs formed (one experienced, one new) |
| 0:45 | Work on tasks |
| 2:45 | Each pair shows what they did |
| 3:15 | Open pull requests and issues are listed for follow-up |

## Task list

| Kind | Example | Page |
|---|---|---|
| Confirm bugs | Reproduce three open issues on a fresh installation | [Reproducing and confirming issues](../../advocates/reproducing-and-confirming-issues.md) |
| Translations | Finish the untranslated messages of one screen | [Contributing translations](../contributing/contributing-translations.md) |
| Help pages | Follow a help page step by step and fix what is wrong | [Contributing documentation and help pages](../contributing/contributing-documentation-and-help-pages.md) |
| Packaging | Add a `composer.json` to an extension that lacks one, a call on the roadmap | [Roadmap](../../project/roadmap.md) |
| Code | A small fix with a test | [Contributing code](../contributing/contributing-code.md) |
| Testing | Install `main` on a different PHP version or database | [Testing releases](../contributing/testing-releases.md) |

## Rules for every contribution

- Prefixed commit messages and the manifest line (`CONTRIBUTING.md`).
- No secrets or personal data in issues or pull requests.
- AI-assisted work is checked by the person who submits it, and labeled when a substantial part was generated
  (`CODE_OF_CONDUCT.md`).
- One change per pull request.

## After the day

- Pull requests get review rounds after the day; agree who follows up on each one.
- Publish a short note of what was done, without names unless people agreed.
- Thank participants publicly once the work is merged.

Larger, multi-day events: [Proposing a contribution sprint](../../advocates/proposing-a-contribution-sprint.md).

## Related pages

- [Local groups and community events](README.md)
- [Ways to contribute](../contributing/ways-to-contribute.md)
- [Mentoring](../helping-others/mentoring.md)
- [Hands-on workshop outline](../../advocates/hands-on-workshop-outline.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
