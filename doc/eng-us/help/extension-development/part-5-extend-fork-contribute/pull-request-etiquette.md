---
title: Pull request etiquette
audience: developer, designer, app-creator
category: extension-development
order: 90
summary: The habits that get pull requests merged and make maintainers glad to see your name - small and focused, explained, tested, patient, open to change - and what to avoid.
keywords: pull request, etiquette, maintainer, review, small changes, patience, respect, communication, open source
type: help
---
# Pull request etiquette

Part 5, chapter 9. The mechanics of a pull request are in the previous chapter. This one is about the people at the
other end. A maintainer reads your pull request in the time they have left after their own work, often unpaid. These
habits make that time count, for them and for you.

## Do

- **Keep it small.** A pull request that changes one thing in fifty lines is reviewed today. One that changes five
  things in two thousand lines waits for a free weekend that may not come.
- **Explain the why.** The diff shows what changed. Only you can say why, and what you tried that did not work.
- **Show how you checked it.** "Tested" means nothing; "a new test that fails without the change, the full suite with
  the same result as main, and the sign-in tried by hand in two browsers" means a lot.
- **Follow the project's style**, even where you disagree with it. Propose a style change separately, if at all.
- **Answer every review comment**, with a change or a reason. "Done" is a fine answer.
- **Say thank you.** For the review, for the merge, for the project.
- **Mention what is not yours.** A test that already fails on main, a warning that was there before. Name it, so
  nobody thinks you caused it.
- **Expect many review rounds on the kernel.** 7x reviews a commit or pull request in many more rounds than usual,
  sometimes hundreds of revisions; be ready to revise your change until it is simple to accept
  ([Code reviews](../../../../../CONTRIBUTING.md#code-reviews)).
- **Squash and rebase while the pull request is open.** 7x requires a kernel pull request to be squashed into
  logical commits (usually one) and rebased onto `main` before the merge: `git fetch origin`,
  `git rebase -i origin/main`, `git push --force-with-lease` to your pull request branch only. After the merge,
  `main` is never rewritten; a later fix is a new pull request.
- **Know what the CLA gives away before a kernel pull request.** The kernel (`se7enxweb/exponential`) merges a code
  contribution only once its author's signed Contributor Licensing Agreement has been received. By signing and
  submitting, you assign the copyright in your contribution to 7x, which becomes its owner, and give up control over it: 7x may use, change, relicense and distribute
  it in any way, without asking or paying you, and you cannot withdraw it. Your own extensions are not affected. Tick the checkbox in the pull request template ([Copyright assignment (CLA)](../../../../../CONTRIBUTING.md#copyright-assignment-cla)).

## Do not

- **Do not mix in unrelated changes.** No reformatting of files you did not otherwise touch, no renaming "while I was
  there". They hide the real change.
- **Do not argue about taste in a review.** If the maintainer prefers another name, use it. It is their project.
- **Do not ping daily.** A friendly reminder after two or three weeks is enough. Maintainers have holidays.
- **Do not open the same pull request again** after it was closed. Ask why it was closed, in a comment on it.
- **Do not take a rejection personally.** A project may decline a good change because it does not fit its direction.
  That is a fork's legitimate reason to exist ([When to fork, and when not to](when-to-fork.md)), not a reason to be
  angry.
- **Do not put credit for yourself into the code** (a comment "fixed by ..."). Your name is in the Git history.

## When you are the maintainer

The same habits apply the other way round:

- Answer within a reasonable time, even if only to say "I will look at this next week".
- Explain what needs to change, with an example where you can.
- Fix small things yourself on top of a contributor's branch, if they allow edits, instead of a round of comments
  about a typo.
- Say thank you, especially to first-time contributors.

See also [Reviewing other people's work](reviewing-other-peoples-work.md).

## Language

Many maintainers and contributors write in a second language. Write short, plain sentences. Avoid irony; it does not
survive translation. Assume good intent in what you read.

## Related pages

- Previous: [Contributing back with pull requests](contributing-back-with-pull-requests.md)
- Next: [Writing good issues](writing-good-issues.md)
- [Case study: a pull request to an upstream author](case-study-a-pull-request-to-an-upstream-author.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
