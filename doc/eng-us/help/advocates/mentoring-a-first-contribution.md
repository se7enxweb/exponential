---
title: Mentoring a first contribution
audience: advocate
category: advocacy
order: 94
summary: How an advocate guides someone through their first contribution to Exponential - choosing a small real task, the documentation or translation path, the code path with Git, the commit message, the pull request, and celebrating it - with a mentoring checklist.
keywords: mentoring, first contribution, first pull request, newcomers, good first task, documentation, translation, Git, fork, commit message, pull request, review
type: help
---
# Mentoring a first contribution

This page is for advocates who help another person make their first contribution. A first pull request that is
welcomed turns a user into a contributor. In Exponential every pull request is merged, under its author's name, once
its author has squashed and rebased it onto `main` as the reviews ask
([How the project grows](../who-is-the-exponential-foundation.md#how-the-project-grows)), which makes it an
especially good place to start.

## Contents

1. [Choose a small, real task](#choose-a-small-real-task)
2. [Path A: documentation](#path-a-documentation)
3. [Path B: translation](#path-b-translation)
4. [Path C: code](#path-c-code)
5. [The pull request](#the-pull-request)
6. [After it is merged](#after-it-is-merged)
7. [Mentoring checklist](#mentoring-checklist)

## Choose a small, real task

| Good first tasks | Why |
|---|---|
| A typo or an outdated step in a help page they read | They already found it |
| A missing link to a deeper page | Small and useful |
| Translating a handful of strings in their language | Clear and measurable |
| A small bug they hit themselves, with a clear fix | Motivating |

Avoid "practice" pull requests with no real change, and avoid large tasks; the first one should be finished in an hour
or two.

## Path A: documentation

1. On the help page, press **Edit on GitHub**.
2. Edit; write a commit message such as `Fixed: The languages help names the Apply button`.
3. Propose the change and open the pull request.

Details: [Improving the help](improving-the-help.md). The change is accepted under the GNU FDL 1.3 or later
([Contributing to the documentation](../../README-LICENSE.md#contributing-to-the-documentation)).

## Path B: translation

Follow [Helping with translations](helping-with-translations.md): the translation files are in
`share/translations/<code>/translation.ts`, edited with Qt Linguist or an XML editor
([Languages in Exponential, section 8](../../languages.md#8-adding-or-completing-an-administration-translation)).

## Path C: code

Follow [Develop with Git, Contribute a change with a pull request](../develop-with-git.md#contribute-a-change-with-a-pull-request):

```bash
# in the clone of the kernel or an extension
git remote add fork https://github.com/<them>/<repository>.git
git switch -c fix-<short-name>
# change, test on their own site
git commit -am "Fixed: <what the change does>"
git push -u fork fix-<short-name>
```

For the kernel, read CONTRIBUTING.md together: the commit hook (`make hooks`) and the file manifest
(`make manifest-fix`) ([CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md)).
Tell them early that a code contribution to the kernel is merged only once they have signed the Contributor
Licensing Agreement that the Exponential Foundation and 7x require, and explain plainly what it means: **by signing
and submitting, they assign the copyright in the contribution to the Exponential Foundation and 7x, which become its owners, and give up control over it.** The Exponential Foundation and 7x may use, change, relicense and distribute it in any way, also under other licenses and in commercial products, without asking or paying them, and
they cannot withdraw it or set conditions on it
([Copyright assignment (CLA)](../../../../CONTRIBUTING.md#copyright-assignment-cla)). Their own extensions are
not affected.

## The pull request

Help them write:

```text
What: <one sentence>
Why: <the problem they had>
How I tested: <steps, version>
Fixes #<issue number> (if there is one)
```

Explain that review rounds asking for changes are normal and not a rejection. For the kernel, prepare them for many
review rounds: 7x reviews a commit or pull request in many more rounds than usual, sometimes hundreds of revisions,
until the change is simple to accept. While the pull request is open, they squash it and rebase it onto `main` as the
reviews ask (`git fetch origin`, `git rebase -i origin/main`, `git push --force-with-lease` to their pull request
branch only); after the merge, `main` is never rewritten
([Code reviews](../../../../CONTRIBUTING.md#code-reviews)).

Steer a first contribution away from changing deliberate behavior that other sites rely on, a
[negative feature](../project/getting-support.md#negative-features): such a change needs a clear justification and
takes even longer to review. If it cannot wait, suggest an option that keeps today's default.

## After it is merged

- Celebrate: a thank-you in the forum or chat, with their consent ([Recognition ideas](recognition-ideas.md)).
- Show them the next step on [the advocacy ladder](the-advocacy-ladder.md).
- Ask what was hard, and improve the docs for the next person.

## Mentoring checklist

```text
[ ] The task is real and small
[ ] They have a GitHub account (with two-factor authentication)
[ ] They chose the path (docs, translation, code) and read the page for it
[ ] Commit message starts with Added:/Updated:/Fixed:/Removed:/Renamed:
[ ] For a kernel code change: they know the CLA gives control of it to the Exponential Foundation and 7x, and their signed CLA is on file
[ ] No secrets or personal data in the change
[ ] Pull request text: what, why, how tested
[ ] They know how to squash and rebase their pull request onto main
[ ] Celebrated, with consent
```

## Related pages

- [Welcoming newcomers](welcoming-newcomers.md)
- [Develop with Git](../develop-with-git.md)
- [Becoming a leader in the Exponential community](../leadership/becoming-a-community-leader.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
