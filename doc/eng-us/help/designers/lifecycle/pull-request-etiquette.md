---
title: "Pull requests to someone else's theme"
audience: designer, developer
category: theme-lifecycle
order: 350
summary: Prepare a pull request that a maintainer can merge quickly - small and focused, explained, tested, with screenshots for visual changes and no unrelated or private content - and how to respond to review.
keywords: pull request, PR, etiquette, contribution, review, screenshots, focused change, description
type: help
---
# Pull requests to someone else's theme

This page is for designers sending a change to another maintainer's theme or to an Exponential repository.

## Step by step

1. **Check first**: is there an issue? Comment that you are working on it. Read the repository's contributing notes.
   If what you want to fix is deliberate behavior that other sites rely on, a
   [negative feature](../../project/getting-support.md#negative-features), any maintainer will ask for a clear
   justification and several rounds of review; propose it as an option rather than a changed default.
2. **One topic per pull request.** A fix and a refactoring are two pull requests.
3. **Branch from the current main**: `git switch -c fix-map-popup origin/main`.
4. **Make the change** following the theme's style. No reformatting of untouched lines.
5. **Test** on a current installation; for visual changes, light and dark, phone and zoom.
6. **Commit** with a prefix and a reason ([Changelog entries](../distribution/changelog-prefixes.md)).
7. **Open the pull request** with:
   - what it changes and why;
   - how you tested it;
   - before and after screenshots for visual changes;
   - the issue it closes.
8. **Respond to review** promptly and kindly, with changes on the same branch. On the Exponential kernel, expect many
   rounds: 7x reviews a commit or pull request in many more rounds than usual, sometimes hundreds of revisions, so be
   ready to revise your change until it is simple to accept. 7x also requires you to squash the pull request and
   rebase it onto `main` while it is open (`git fetch origin`, `git rebase -i origin/main`,
   `git push --force-with-lease` to your pull request branch only); after the merge, `main` is never rewritten
   ([Code reviews](../../../../../CONTRIBUTING.md#code-reviews)).
9. **For the Exponential kernel** (`se7enxweb/exponential`), have your signed Contributor Licensing Agreement on file:
   7x and the Exponential Foundation merge a kernel contribution only after it has been received. **By signing and
   submitting, you assign the copyright in your contribution to the Exponential Foundation and 7x, which become its owners, and give up control over it:** The Exponential Foundation and 7x may use, change, relicense and distribute it in any way, also under other licenses and in commercial products, without asking or paying you,
   and you cannot withdraw it or set conditions on it
   ([Copyright assignment (CLA)](../../../../../CONTRIBUTING.md#copyright-assignment-cla)). Other maintainers'
   themes and your own themes are not affected.

## Never include

- Credentials, customer names or data, server paths.
- Changes to version fields or tags (the maintainer releases).
- Generated files the repository does not track.

## Related pages

- [Contributing back upstream](contributing-back-upstream.md)
- [Keeping a fork in sync](keeping-a-fork-in-sync.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
