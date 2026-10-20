---
title: "Part 5: Extending, forking ethically and contributing back"
audience: developer, designer, app-creator
category: extension-development
order: 50
summary: Part 5 of the book: build on other people's extensions fairly - extend without forking, fork when you must, keep credit and licenses right, and give your work back with pull requests, issues and reviews.
keywords: book, part 5, fork, extend, contribute, pull request, issue, license, attribution, credit, rebranding, maintainer, community, ethics
type: help
---
# Part 5: Extending, forking ethically and contributing back

This is the fifth part of [Extension development: the book](../README.md). Almost nobody writes an extension alone
and from nothing. You build on the kernel, on other people's extensions and on libraries. This part is about doing
that well: technically (how to change someone else's code without losing their updates) and fairly (how to keep
their credit, respect their license and give your improvements back).

## The chapters

| Group | Chapters |
|---|---|
| Changing someone else's code | [Extending without forking](extending-without-forking.md), [When to fork, and when not to](when-to-fork.md), [Forking on GitHub, step by step](forking-on-github.md), [Keeping a fork in sync with its source](keeping-a-fork-in-sync.md) |
| Fairness | [Rebranding ethically](rebranding-ethically.md), [Licenses and how they combine](licenses-and-compatibility.md), [Attribution and copyright notices](attribution-and-copyright-notices.md) |
| Giving back | [Contributing back with pull requests](contributing-back-with-pull-requests.md), [Pull request etiquette](pull-request-etiquette.md), [Writing good issues](writing-good-issues.md), [Commit messages the project can build a changelog from](commit-messages.md), [Reviewing other people's work](reviewing-other-peoples-work.md) |
| Taking responsibility | [Adopting an abandoned extension](adopting-an-abandoned-extension.md), [The maintainer path](the-maintainer-path.md), [Growing the community through code](growing-the-community-through-code.md) |
| Case studies | [Porting a package from another project with credit](case-study-ported-from-another-project.md), [A pull request to an upstream author](case-study-a-pull-request-to-an-upstream-author.md) |

## Three principles

1. **Change as little of other people's code as you can.** The kernel and most extensions offer ways to change
   behavior from your own extension. A change you do not make to someone else's code is a change you never have to
   carry forward.
2. **Never take credit you did not earn.** Keep every copyright line and author name. Add yours beside them.
3. **Give back what helps others.** A fix you send back is a fix you no longer have to maintain alone, and the next
   person does not have to find the bug again.

**Copyright and control of your contribution (CLA).** 7x and the Exponential Foundation merge a code contribution to
the core kernel (`se7enxweb/exponential`), sent in any form (pull request, patch, issue or mail), only after you have
signed the Contributor Licensing Agreement. **By signing and submitting, you give up control over your
contribution:** 7x and the recipients of its software receive a perpetual, worldwide, irrevocable, royalty-free
license, patents included, to use, change, sublicense, relicense and distribute it in any way, also under other
licenses and in commercial products, without asking or paying you, and you cannot withdraw or restrict it later.
Under the agreement's wording you remain the nominal copyright holder and may still use your own work elsewhere. Extending
the kernel from your own extension, forking an extension, or publishing your own work is not affected. See
[Copyright and control of your contribution (CLA)](../../../../../../CONTRIBUTING.md#copyright-and-control-of-your-contribution-cla).

## Not legal advice

This part explains licenses the way developers need to understand them day to day. It is not legal advice. When
money, a customer contract or a dispute is involved, ask someone qualified.

## Related pages

- Previous: [Case study: small PHP 8 fixes across many extensions](../part-4-upgrading/case-study-small-fixes-across-many-extensions.md)
- Next: [Extending without forking](extending-without-forking.md)
- [Contribute extensions back](../../contribute-extensions-back.md)
- [Reading licenses before you reuse anything](../part-1-from-user-to-developer/reading-licenses-before-reusing.md)
- [Extension development: the book](../README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
