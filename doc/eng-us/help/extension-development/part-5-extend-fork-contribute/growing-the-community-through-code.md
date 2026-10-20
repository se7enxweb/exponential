---
title: Growing the community through code
audience: developer, designer, app-creator, user
category: extension-development
order: 150
summary: How code itself grows a community - publishing what you built, good first issues, documentation and translations, help pages inside your extension, and the community sites where people look for extensions.
keywords: community, open source, publish, good first issue, documentation, translations, help pages, share.exponential.earth, projects.exponential.earth, ezpedia.exponential.earth
type: help
---
# Growing the community through code

Part 5, chapter 15. A community does not grow from announcements. It grows when people find something useful, can
understand it, and can see where to help. Every extension you publish can do that. This chapter collects the habits
that turn code into a place people come back to.

## Publish what you build

The most effective contribution is the extension you already wrote for yourself. If it solves a problem for you, it
solves it for someone else. The steps (clean it up, license, metadata, GitHub, Packagist) are in
[Contribute extensions back](../../contribute-extensions-back.md), and the release procedure in
[Part 7](../part-7-releasing/README.md). Remove what is specific to your site (names, addresses, settings with
secrets) and keep the rest.

## Make it easy to start

- **A README that answers three questions** in its first screen: what it does, how to install it, how to see it
  working.
- **A screenshot or a short example** of the result.
- **Defaults that work**, so the first install shows something.
- **Help pages inside the extension**, which the administration's help system shows next to the kernel's
  ([Documenting your extension](../part-3-extension-developer/documenting-your-extension.md) and the tutorial
  [Help pages for your extension](../part-3-extension-developer/tutorials/by-hand-help-pages-for-your-extension.md)).

## Make it easy to help

- **Label issues** `good first issue` when they are small and well described. New contributors look for that label.
- **Say what help you want** in the README: translations, testing on PostgreSQL, a dark mode check.
- **Keep translations open.** Interface texts as translation strings (`i18n` with your extension's context) let
  anyone add a language with one file. expchangeclass gained Spanish, Hebrew, Latin and Klingon this way (commit
  c2a4f41, alongside German earlier).
- **Answer the first pull request of a new contributor quickly and kindly.** It decides whether there is a second.

## Make it findable

| Place | What it is for |
|---|---|
| [Packagist](https://packagist.org) | `composer require` finds it; search by keyword |
| GitHub topics | add topics such as `exponential` and `exponential-extension` to the repository |
| [projects.exponential.earth](https://projects.exponential.earth) | the community's project directory |
| [share.exponential.earth](https://share.exponential.earth) | the community's sharing and discussion site |
| [ezpedia.exponential.earth](https://ezpedia.exponential.earth) | the community's knowledge base, where articles can describe and link your extension |

Part 1 describes each site and what you find there
([projects.exponential.earth](../part-1-from-user-to-developer/projects-exponential-earth.md),
[ezpedia.exponential.earth](../part-1-from-user-to-developer/ezpedia-exponential-earth.md)).

## Share what you learn

A solved problem written down saves the next person a day:

- an article on ezpedia.exponential.earth about a technique;
- a correction to this book: every page is free documentation under the GNU FDL, and improvements are welcome
  ([Free documentation](../../free-documentation.md));
- an answer on share.exponential.earth to someone else's question.

## Be the kind of project people want to join

- Credit everyone, every time ([Attribution and copyright notices](attribution-and-copyright-notices.md)).
- Explain decisions in public, in issues and commit messages.
- Thank people.
- Never be the reason someone leaves.

## Related pages

- Previous: [The maintainer path](the-maintainer-path.md)
- Next: [Case study: porting a package from another project with credit](case-study-ported-from-another-project.md)
- [Contribute extensions back](../../contribute-extensions-back.md)
- [Who is the Exponential Foundation](../../who-is-the-exponential-foundation.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
