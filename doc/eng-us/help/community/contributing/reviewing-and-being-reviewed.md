---
title: Reviewing and being reviewed
audience: community, developer, supporter
category: community
order: 30
summary: How review works in the Exponential community - why 7x does many review rounds, how to get through them, squash and rebase, how community members can review others' pull requests, and how to give and take feedback within the code of conduct.
keywords: code review, review rounds, pull request, feedback, squash, rebase, force-with-lease, reviewing others, etiquette, code of conduct
type: help
---
# Reviewing and being reviewed

Review is where most of the learning in an open source project happens, in both directions.

## Contents

1. [Being reviewed](#being-reviewed)
2. [Squash and rebase](#squash-and-rebase)
3. [Reviewing others](#reviewing-others)
4. [Feedback within the code of conduct](#feedback-within-the-code-of-conduct)

## Being reviewed

> "**Be prepared for many review rounds.** 7x does a much larger than usual number of review rounds before it accepts
> a commit or pull request, sometimes hundreds of revisions. [...] This is how the kernel stays maintainable; it is
> not a judgment of you." (`CONTRIBUTING.md`, "Code Reviews")

To get through review faster (same source):

- Keep pull requests small and focused: one change per pull request.
- Follow the coding standards and the style of the files around your change.
- Add tests that fail without your change and pass with it.
- Answer every review comment in the pull request, with a change or a reason.
- Keep your branch current: rebase it on `main` before asking for the next round.
- Do not take many rounds personally.

Every pull request is merged in the end: its author revises it as the reviews ask
([How the project grows](../../who-is-the-exponential-foundation.md#how-the-project-grows)).

## Squash and rebase

7x requires that you "squash and rebase your pull request onto `main` while it is open":

```bash
git fetch origin
git rebase -i origin/main          # fold fixups into logical commits, usually one
git push --force-with-lease        # only to your own pull request branch, never to main
```

Once a pull request is merged, `main`'s history is never rewritten; a later fix comes as a new pull request.

## Reviewing others

Community members can review pull requests too, and a careful review from a user of the affected feature helps the
maintainers:

| Check | How |
|---|---|
| Does it do what it says? | Check out the branch on a test installation and try it |
| Does it break something nearby? | Try the related screens and your own extensions |
| Is it tested? | Look for a test that fails without the change |
| Does it follow the rules? | Commit message prefix, manifest line, naming prefixes, no inline styles in admin templates |
| Is it documented? | A help page or feature page for changed behavior |

Write what you tested and on what setup. "Tested on PHP 8.3, SQLite, Velocity: works" is useful;
"Looks good" is not. More: [Reviewing other people's work](../../extension-development/part-5-extend-fork-contribute/reviewing-other-peoples-work.md).

## Feedback within the code of conduct

- Comment on the code, never on the person.
- Explain why, and suggest an alternative.
- Disagree "on technical merits, in public, and politely" (`CODE_OF_CONDUCT.md`, "The contributor agreement (CLA)").
- Thank people for their work, also when you ask for changes.

## Related pages

- [Contributing code](contributing-code.md)
- [Pull request etiquette](../../extension-development/part-5-extend-fork-contribute/pull-request-etiquette.md)
- [Mentoring](../helping-others/mentoring.md)
- [The code of conduct](../joining/the-code-of-conduct.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
