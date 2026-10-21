---
title: Contributing as a student
audience: student
category: learning
order: 39
summary: Why and how a learner contributes to Exponential - the kinds of contribution that fit each learning path, what you need, the rules from CONTRIBUTING.md and the Code of Conduct, how review works, the CLA for kernel code, and how contributions become part of your portfolio.
keywords: contribute, contribution, student, open source, pull request, documentation, translation, bug report, code, review, CONTRIBUTING.md, code of conduct, CLA, portfolio
type: help
---
# Contributing as a student

Exponential is free software developed in the open, and contributions from learners are welcome. A contribution
teaches you more than an exercise, because real people review it and real users benefit. "Real contributions are the
best portfolio a student can have: a merged pull request is public proof of skill"
([Student and education partners](../partners/student-and-education-partners.md#course-and-project-ideas)).

## Contents

1. [Kinds of contribution](#kinds-of-contribution)
2. [What you need](#what-you-need)
3. [The rules](#the-rules)
4. [How review works](#how-review-works)
5. [Copyright and licenses](#copyright-and-licenses)
6. [Where to start](#where-to-start)

## Kinds of contribution

| Contribution | Needs | Fits path | Page |
|---|---|---|---|
| Report a bug well | A GitHub account, a study site | All | [Bugs you find while learning](bugs-you-find-while-learning.md) |
| Fix a typo or an outdated step in the help | A GitHub account | All | [Improving the help as a student](improving-the-help-as-a-student.md) |
| Translate interface texts | A language you write well | Editor, all | [Translating as a student](translating-as-a-student.md) |
| Answer a question in the community | What you learned | All | [Asking for help and study groups](asking-for-help-and-study-groups.md) |
| Write a test that reproduces a bug | PHP, Git | Developer | [Contributing code](../supporters/contributing-code.md) |
| Fix a small bug | PHP, Git | Developer | [Your first contribution, step by step](your-first-contribution-step-by-step.md) |
| Publish an extension or a theme of your own | Your own project | Developer, designer | [Contribute extensions back](../contribute-extensions-back.md) |
| Contribute a site package | A site worth sharing | Administrator, designer | [Contribute your site package](../site-packages/contribute-your-site-package.md) |

The supporters' section lists more ways to help ([Supporters: start here](../supporters/supporters-start-here.md)).

## What you need

- A free **GitHub account**. You need one to open an issue or a pull request
  ([When something goes wrong](../when-something-goes-wrong.md#filing-a-bug-in-exponential)).
- For code: Git and a study installation from Git clones
  ([Installing from Git clones for study](installing-from-git-clones-for-study.md)).
- Time to answer review comments.

## The rules

From `CONTRIBUTING.md` in the root of the kernel ([Contributors guidelines](../../../../CONTRIBUTING.md)):

- Read the [Code of Conduct](../../../../CODE_OF_CONDUCT.md) first; all contributors are expected to keep it.
- Always work on a new branch; keep each pull request focused, one change per pull request.
- Commit subjects start with what the change does: `Added: `, `Updated: `, `Fixed: `, `Removed: ` or `Renamed: `.
- Follow the [coding standards](../../standards/coding-standards.md) and the style of the files around your change.
- Keep the file manifest `share/filelist.md5` current (`make manifest-fix`); `make hooks` installs a pre-commit hook
  that checks it, the PHP syntax and the code style.
- Add tests where you can, and update the documentation if needed.

## How review works

`CONTRIBUTING.md` warns: "Be prepared for many review rounds. 7x does a much larger than usual number of review rounds
before it accepts a commit or pull request, sometimes hundreds of revisions ... it is not a judgment of you."

- Answer every review comment in the pull request, with a change or a reason.
- If a reviewer says the behavior you changed is a
  [negative feature](../project/getting-support.md#negative-features), deliberate or open to interpretation and
  relied on by other sites, explain your use case and offer the change as an option rather than a new default.
- Squash and rebase your pull request onto `main` while it is open, as 7x requires; force-push only to your own pull
  request branch ([Develop with Git](../develop-with-git.md#contribute-a-change-with-a-pull-request)).
- Every review round is a free lesson from experienced developers. Note what you learned in your
  [study journal](keeping-a-study-journal.md).

## Copyright and licenses

- Code contributions are licensed under the GNU GPL version 2 or later, like the rest of Exponential.
- Documentation is free documentation under the GNU FDL 1.3 or later ([Free documentation](../free-documentation.md)).
- A code contribution to the core kernel is merged only after its author has signed the Contributor Licensing
  Agreement, by which the author assigns the copyright to 7x. Read [The CLA for students](the-cla-for-students.md)
  before you send kernel code. Your own extensions are not affected.

## Where to start

1. Pick the smallest real task: a typo you found, a step that did not work for you, a string in your language.
2. Follow [Your first contribution, step by step](your-first-contribution-step-by-step.md).
3. Add the link to your portfolio once it is merged ([Building a portfolio](building-a-portfolio.md)).

The advocates' guide [Mentoring a first contribution](../advocates/mentoring-a-first-contribution.md) is written for
people who help you; reading it shows you what a good first task looks like.

## Related pages

- [Your first contribution, step by step](your-first-contribution-step-by-step.md)
- [The CLA for students](the-cla-for-students.md)
- [Contribute to the project](../project/contribute-to-the-project.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
