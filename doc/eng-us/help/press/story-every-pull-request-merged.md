---
title: "Story: every pull request is merged"
audience: press
category: press-media
order: 91
summary: A feature story about Exponential's contribution policy - every pull request is merged, after its author has squashed and rebased it onto main through the review rounds, and main's history is never rewritten after the merge - how that works with security and stability, with sources and the claims to avoid.
keywords: story, pull requests, contributions, merge policy, open source governance, contributors, code review, community, CONTRIBUTING, inclusion
type: help
---
# Story: every pull request is merged

This page is for journalists who cover open source governance. Many projects reject most outside contributions;
Exponential documents the opposite policy.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [Facts you can print](#facts-you-can-print)
3. [What not to claim](#what-not-to-claim)
4. [Angles and headline ideas](#angles-and-headline-ideas)

## The story in one paragraph

Every pull request is merged. While it is open, 7x reviews it in many rounds, and its author squashes it and rebases
it onto `main` as the reviews ask; once it is merged, `main`'s history is never rewritten, and later fixes come as new
pull requests ([How the project grows](../who-is-the-exponential-foundation.md#how-the-project-grows),
[CI](../../bc/6.0/ci.md)). Contributions are released under the same GPL license (CONTRIBUTING.md). Since December 2023
pull requests have come from 7x, from developers such as ekked and fwoldt, and from companies such as Netgen and Mugo
Web; 1,039 merged pull requests appear in the whole history ([Exponential by the numbers](exponential-by-the-numbers.md)).

## Facts you can print

| Fact | Source |
|---|---|
| Every pull request is merged | [How the project grows](../who-is-the-exponential-foundation.md#how-the-project-grows) |
| While a pull request is open, its author squashes and rebases it onto `main` as the reviews ask; after the merge, `main` is never rewritten and later fixes come as new pull requests | same; [Code reviews](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md#code-reviews) |
| The test suite runs on every pull request | [CI](../../bc/6.0/ci.md) |
| Contributions under GPL v2 or later | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md) |
| Code contributions to the kernel need a signed Contributor Licensing Agreement (CLA) before they are merged; by signing and submitting, contributors assign the copyright to 7x and give up control: 7x may use, change, relicense and distribute a contribution in any way, without asking or paying them, and they cannot withdraw it; users' own extensions are not affected | [Copyright assignment (CLA)](../../../../CONTRIBUTING.md#copyright-assignment-cla) |
| 1,039 merged pull requests in the history (18 October 2026) | [Exponential by the numbers](exponential-by-the-numbers.md) |

## What not to claim

| Do not say | Say instead |
|---|---|
| "No code review" | 7x reviews each pull request in many rounds, and its author revises, squashes and rebases it before the merge; the CI tests run on every pull request |
| "Anything goes into the release" | Problems are fixed in the pull request before the merge, or in new pull requests after it |
| "Thousands of contributors" | Count from the history with a date |
| "Contributors keep the copyright of their code" | They do not: kernel contributors assign the copyright to 7x under the CLA; 7x may relicense and distribute it without asking them, and they cannot withdraw it |

## Angles and headline ideas

- "A project that says yes to every contribution, and how it stays stable."
- "Your first pull request will be merged: an open door for newcomers."

## Related pages

- [Story: the community](story-community.md)
- [Mentoring a first contribution](../advocates/mentoring-a-first-contribution.md)
- [Develop with Git](../develop-with-git.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
