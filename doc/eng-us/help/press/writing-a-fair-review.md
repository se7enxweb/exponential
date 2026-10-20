---
title: Writing a fair review
audience: press
category: press-media
order: 167
summary: How creators and reviewers judge Exponential fairly - test on your own installation, state the version and setup, separate what you saw from what the documentation says, report limits and gaps honestly, compare only like with like, and give 7x a chance to correct facts - with a review structure and a fairness checklist.
keywords: review, fair review, evaluation, test, criteria, pros and cons, limitations, comparison, methodology, verdict, right of reply, creator, journalist
type: help
---
# Writing a fair review

This page is for creators and journalists who review Exponential: in a video, a post, a newsletter or an episode. A
fair review can be critical; it must be accurate, tested and open about how you tested. Nobody at 7x or the
Exponential Foundation needs to approve your review, and nothing on this page asks you to be positive.

## Contents

1. [Before you judge: test it yourself](#before-you-judge-test-it-yourself)
2. [State your setup](#state-your-setup)
3. [Separate what you saw from what you read](#separate-what-you-saw-from-what-you-read)
4. [Criteria and where to check them](#criteria-and-where-to-check-them)
5. [Limits and gaps worth reporting](#limits-and-gaps-worth-reporting)
6. [Comparisons](#comparisons)
7. [A review structure and checklist](#a-review-structure-and-checklist)

## Before you judge: test it yourself

- Install it yourself, on a machine you control ([Setting up a demo installation](setting-up-a-demo-installation.md)).
- Do the tasks your audience would do: write and publish, build a page, translate, upgrade, back up.
- Note what failed, with the exact message and the step. If it looks like a bug, report it
  ([When something goes wrong](../when-something-goes-wrong.md)); if it looks like a security problem, report it
  privately to security@se7enx.com and leave the details out of the review until it is fixed.
- Give yourself enough time; a first impression is a first look, so call it that.

## State your setup

```text
Tested: Exponential <6.0.14 | 6.0.15 development line, commit or date>
Installed with: <exp:install | setup wizard | kickstarter>, database <SQLite | ...>
Served by: <Apache + PHP-FPM | nginx | Velocity, engine qbix | FrankenPHP>, PHP <version>
Design: <admin4 | ...>; extensions switched on: <list>
Hardware or hosting: <description, without private addresses>
Time spent: <hours or days>
Disclosure: <line>
```

## Separate what you saw from what you read

| Kind of statement | How to write it |
|---|---|
| You tested it | "In my test, publishing a folder kept the earlier version." |
| The documentation says it | "The documentation says every record of the audit trail carries the hash of the one before." (link) |
| Someone told you | "<Who> told me ..." - and check it ([Claims you must not make](../advocates/claims-you-must-not-make.md)) |
| Nobody says | "The sources do not say." |

Numbers belong to the second kind unless you measured them yourself. The speed figures in the README, for example,
come with their method; quote them as the README's, and if you measure, publish your method too
([Benchmarking](../../guides/benchmarking.md)).

## Criteria and where to check them

| Criterion | Test | Documentation |
|---|---|---|
| Editing and publishing | Drafts, preview, versions, approval | [A tour of the administration](../admin-tour.md) |
| Page building | Layouts, blocks, shared layouts, mappings | [The layouts editor](../layouts-editor.md) |
| Languages | Translate an item; switch the interface | [Languages in Exponential](../../languages.md) |
| Accessibility | Keyboard only; contrast in light and dark | [Keyboard shortcuts](../keyboard-shortcuts.md) |
| Installation | From an empty folder | [Installing Exponential](../../install/README.md) |
| Hosting freedom | Databases, operating systems | [Database support](../database-support.md), [Operating systems](../os-support.md) |
| Security | Two-step sign-in, audit trail, hardening | [Security hardening](../security-hardening.md) |
| Documentation | Find answers in the built-in help | [The help system](../../features/6.0/help-system.md) |
| License | GPL for the code, GNU FDL for the documentation | [Who is 7x?, Open source licenses](../who-is-7x.md#open-source-licenses) |
| Support and continuity | What is committed, and by whom | [The support commitment](../who-is-the-exponential-foundation.md#the-support-commitment) |

## Limits and gaps worth reporting

A fair review reports what the sources themselves admit, with the source:

- 6.0.15 is in development and has no release date; much of what the help describes belongs to it
  ([6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md)).
- Nineteen of the 33 administration translations are complete (19 October 2026); the others show English for missing texts
  ([Languages in Exponential](../../languages.md)).
- Velocity's shipped default engine is PHP's built-in server, for development only; the recommended engine must be
  chosen ([Velocity](../../features/6.0/velocity-persistent-worker-server.md)).
- Exponential Platform DXP does not ship a layouts editor
  ([Knowing the product family](../advocates/knowing-the-product-family.md#common-mix-ups)).
- The sources do not count installations, users or developers.

## Comparisons

- Compare like with like: the same task, the same hosting, the same care in setup.
- Describe other products from their own documentation, and date it.
- No multiples ("10x faster") without your own published measurement.
- Be fair to other projects and their people ([Respecting other projects and companies](../advocates/respecting-other-projects-and-companies.md),
  [Comparing Exponential factually](../business/comparing-exponential-factually.md)).

## A review structure and checklist

```text
1. Disclosure and setup (version, how installed, how served, time spent)
2. What it is, in two sentences, with the source
3. What I tested, task by task: what worked, what did not
4. Strengths, each with evidence
5. Weaknesses and gaps, each with evidence
6. Who it suits, and who it does not
7. Verdict, labeled as my opinion
8. Sources and corrections policy

[ ] Every fact has a source or is labeled as my own test
[ ] Names right: Exponential, 7x, the Exponential Foundation; eZ Publish only as history
[ ] No unsupported numbers, endorsements, release dates or compliance claims
[ ] Facts sent to 7x for checking, if there is time (not the opinion)
[ ] I know how I will correct mistakes
```

Asking for a fact check is optional. The contact addresses are on the press pages
([Press, media and influencers: start here](press-start-here.md)); ask about facts, and keep your opinion yours.

## Related pages

- [Disclosure and sponsorship ethics](disclosure-and-sponsorship-ethics.md)
- [Claims you must not make](../advocates/claims-you-must-not-make.md)
- [Correcting a video or post](correcting-a-video-or-post.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
