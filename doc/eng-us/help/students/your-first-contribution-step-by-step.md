---
title: Your first contribution, step by step
audience: student
category: learning
order: 40
summary: A learner's first contribution to Exponential in three paths - a documentation fix through Edit on GitHub, a translation, or a small code fix through a fork and a pull request - with the commands from Develop with Git, the commit message rule, what to write in the pull request, and what happens afterwards.
keywords: first contribution, pull request, fork, branch, commit, Edit on GitHub, documentation fix, translation, code fix, review, squash, rebase, learner
type: help
---
# Your first contribution, step by step

This page walks you through a first contribution. Choose the path that fits what you found. All paths end in a pull
request that is reviewed in public.

## Contents

1. [Choose a small, real task](#choose-a-small-real-task)
2. [Path A: a documentation fix](#path-a-a-documentation-fix)
3. [Path B: a translation](#path-b-a-translation)
4. [Path C: a code fix](#path-c-a-code-fix)
5. [Writing the pull request](#writing-the-pull-request)
6. [After you send it](#after-you-send-it)

## Choose a small, real task

Good first tasks, from [Mentoring a first contribution](../advocates/mentoring-a-first-contribution.md#choose-a-small-real-task):

- a typo or an outdated step in a help page you read;
- a missing link to a deeper page;
- a handful of strings in your language;
- a small bug you hit yourself, with a clear fix.

Avoid "practice" pull requests with no real change, and large tasks. The first one should be finished in an hour or
two. Search the issue tracker first: if your problem is known, add what you know to that issue.

## Path A: a documentation fix

1. On the help page, press **Edit on GitHub** ([Contributing documentation](../supporters/contributing-documentation.md#fix-a-page-in-two-minutes)).
2. Change the text in the browser. Keep the style of the page: American English, "Exponential" for the product, a
   source for every fact.
3. Write a commit message such as `Fixed: The languages help names the Apply button`.
4. Propose the change and open the pull request.

The help pages are the files in `doc/` of the kernel, so a documentation fix is a pull request to
[se7enxweb/exponential](https://github.com/se7enxweb/exponential). More: [Improving the help as a student](improving-the-help-as-a-student.md).

## Path B: a translation

1. Check the language's completeness in `doc/eng-us/languages.md`.
2. Fork the repository and open `share/translations/<locale>/translation.ts`.
3. Translate untranslated or unfinished messages; keep placeholders such as `%1` exactly.
4. Switch the administration to your language and look at the screens you translated.
5. Commit with a subject such as `Updated: German translation of the help dashboard` and open a pull request.

Source: [Contributing translations](../supporters/contributing-translations.md#how-to-translate). More:
[Translating as a student](translating-as-a-student.md).

## Path C: a code fix

From [Develop with Git](../develop-with-git.md#contribute-a-change-with-a-pull-request), for an extension such as
`eztags` (the kernel works the same way):

1. On GitHub, open the repository and press **Fork**.
2. Add your fork as a second remote and start a branch:

   ```bash
   cd extension/eztags
   git remote add fork https://github.com/<you>/eztags.git
   git switch -c fix-tag-search
   ```

3. Make the change, test it on your study site, and commit it:

   ```bash
   git commit -am "Fixed: The tag search finds tags with an apostrophe"
   git push -u fork fix-tag-search
   ```

4. Follow the link GitHub prints to open a pull request (or `gh pr create`).

For the kernel itself, read `CONTRIBUTING.md` first: run `make hooks` once, keep `share/filelist.md5` current with
`make manifest-fix`, and make sure your signed CLA is on file before the merge ([The CLA for students](the-cla-for-students.md)).

## Writing the pull request

```text
Title:    Fixed: <what the change does, in one line>

What:     <the problem, in two sentences>
Why:      <who it affects>
How I tested it:
          <release, PHP version, steps, what I saw before and after>
CLA:      <signed / not needed (documentation fix in an extension of my own, etc.)>
```

The kernel's pull request template has a checkbox for the CLA. Keep personal data, passwords and server details out
of the description and the screenshots.

## After you send it

- **Expect review rounds.** 7x reviews in many more rounds than usual; it "is not a judgment of you"
  ([Contributors guidelines](../../../../CONTRIBUTING.md#code-reviews)).
- **Answer every comment** in the pull request.
- **Squash and rebase** onto `main` when asked: `git fetch origin`, `git rebase -i origin/main`,
  `git push --force-with-lease`, only to your pull request branch.
- **When it is merged**, add it to your portfolio and your journal. Then pick the next task.

## Related pages

- [Contributing as a student](contributing-as-a-student.md)
- [Using Git as a student](using-git-as-a-student.md)
- [Develop with Git](../develop-with-git.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
