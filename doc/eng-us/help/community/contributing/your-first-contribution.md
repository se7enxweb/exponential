---
title: Your first contribution
audience: community, supporter, developer, editor
category: community
order: 22
summary: Three small first contributions to Exponential, step by step - a corrected help page through Edit on GitHub, a well-made bug report through the Report an error page, and a first pull request with the right commit message and manifest line.
keywords: first contribution, Edit on GitHub, pull request, bug report, Report an error, commit message, manifest, filelist.md5, fork, newcomer
type: help
---
# Your first contribution

Choose one of the three. Each takes less than an hour the first time.

## Contents

1. [Option A: fix a help page](#option-a-fix-a-help-page)
2. [Option B: report a bug](#option-b-report-a-bug)
3. [Option C: your first pull request with code](#option-c-your-first-pull-request-with-code)
4. [After your first contribution](#after-your-first-contribution)

## Option A: fix a help page

1. Create a free GitHub account if you have none.
2. On the help page with the mistake, follow **Edit on GitHub**.
3. GitHub offers to fork the repository; accept. Correct the text in the browser.
4. Write a short commit message that starts with what the change does, for example
   `Updated: The help page on searching names the search filters correctly` (the prefixes are `Added:`,
   `Updated:`, `Fixed:`, `Removed:`, `Renamed:`; `CONTRIBUTING.md`, "Making Changes").
5. Propose the change as a pull request and describe in one sentence what was wrong.

The repository keeps a manifest of all files, `share/filelist.md5`, and CI checks it. If the check fails on your pull
request, its summary "shows the lines to change" (`CONTRIBUTING.md`, "Submitting Changes"); a reviewer can help.

## Option B: report a bug

1. Open **Report an error** in the help of your administration (`exphelp/report`). It "fills in a summary of the
   installation for you and opens a new issue with a prepared title and text"
   ([When something goes wrong](../../when-something-goes-wrong.md)).
2. Search the tracker first; if the bug is known, add what you know to the existing issue.
3. Write the steps, what you expected and what happened. Remove anything private.
4. Watch the issue and answer questions from maintainers.

[Filing a bug](filing-a-bug.md) has the details.

## Option C: your first pull request with code

1. Fork [se7enxweb/exponential](https://github.com/se7enxweb/exponential) and clone your fork.
2. Run `composer install` and set up a test installation ([Develop with Git](../../develop-with-git.md)).
3. Run `make hooks` once: the hooks check the commit message, the PHP syntax, the code style and the manifest
   (`CONTRIBUTING.md`, "Submitting Changes").
4. Create a branch, for example `git checkout -b fix/typo-in-setup-message`.
5. Make one small change, test it, and commit with a prefixed message.
6. Update the manifest with `make manifest-fix` if your change adds, changes or removes a file.
7. Push to your fork and open a pull request. Tick the CLA checkbox of the template once your signed CLA is on file
   ([The CLA explained](the-cla-explained.md)).

Good first changes: a wrong message text, a missing translation string, a broken link in a template, a test for a
bug that is already fixed.

## After your first contribution

- Expect questions and several review rounds; that is normal ([Reviewing and being reviewed](reviewing-and-being-reviewed.md)).
- Note what you did ([Tracking your contributions](../../advocates/tracking-your-contributions.md)).
- Do a second one in the same area; repetition is how people become trusted contributors
  ([From user to contributor](../paths/from-user-to-contributor.md)).

## Related pages

- [Contributing as a community member](README.md)
- [Mentoring a first contribution](../../advocates/mentoring-a-first-contribution.md)
- [Develop with Git](../../develop-with-git.md)
- [When something goes wrong](../../when-something-goes-wrong.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
