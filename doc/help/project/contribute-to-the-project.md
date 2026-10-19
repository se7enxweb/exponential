---
title: "Contribute to the project: every way to help build Exponential"
audience: user, editor, admin, developer, designer, partner, supporter
category: project
order: 60
summary: How to take part in building Exponential, following the "Support Exponential!" FAQ and the community pages of exponential.earth and the kernel's CONTRIBUTING.md - code through pull requests, the license your contributions carry, testing and bug reports, translations and the Exponential Translation Project, documentation, answers in the community, extensions, Composer packages and site packages - with the first step for each.
keywords: contribute, contributing, contributor, pull request, fork, branch, GitHub, CONTRIBUTING.md, code of conduct, commit message, tests, manifest, bug report, testing, translation, Exponential Translation Project, documentation, GFDL, eZpedia, forums, extensions, composer.json, Packagist, site packages, license, GPL
featured: true
type: help
---
# Contribute to the project: every way to help build Exponential

"No matter your experience level", the website's [Support Exponential!](https://exponential.earth/faq/support) FAQ
says, you can contribute to Exponential. The community page adds that the project now actively welcomes pull requests
from the community. This page lists the ways in, from the ones that need no code at all to changes in the kernel, with
the first step for each.

## Choose your way in

| You can | Start here | Needs |
|---|---|---|
| Answer someone's question | [Share Exponential forums](https://share.exponential.earth/forums), [Telegram](https://t.me/exponentialcms) | Experience with Exponential |
| Report a bug well | [When something goes wrong](../when-something-goes-wrong.md#filing-a-bug-in-exponential) | A free GitHub account |
| Test a release or a pull request | [Testing a change before it is released](#test-changes-before-they-are-released) | A test installation |
| Translate | [Translations](#translations) | Your native language |
| Write or fix documentation | [Documentation](#documentation) | Clear writing |
| Fix a bug or add a feature | [Code](#code-pull-requests-to-the-kernel-and-extensions) | PHP, Git |
| Share an extension or a design | [Contribute extensions back](../contribute-extensions-back.md) | An extension that others could use |
| Add a `composer.json` to an extension | [Composer packages](#composer-packages-and-site-packages) | Composer basics |
| Fund the work | [Give and donate](give-and-donate.md) | |

## Code: pull requests to the kernel and extensions

The website describes the path: get a GitHub account, fork the repository, make your fix on a branch, and send a pull
request to 7x's repository, where it is reviewed and, when merged, released to everyone. In detail:

1. **Read the rules.** `CONTRIBUTING.md` in the root of the installation, and the code of conduct it links to.
2. **Work on a clone.** Fork [se7enxweb/exponential](https://github.com/se7enxweb/exponential) (or the extension's
   repository), clone your fork and create a branch for one change. [Develop with Git](../develop-with-git.md) shows
   how to turn an installation into clones you can work in.
3. **Make the change, small and focused.** One pull request per change. Follow the code style of the files around
   you; the website also points to the [Exponential code standards](https://share.exponential.earth/learn/exponential-code-standards).
4. **Write the commit message the project's way.** The subject starts with what the change does: `Added:`,
   `Updated:`, `Fixed:`, `Removed:` or `Renamed:`, for example `Fixed: The redirect keeps the query string`.
5. **Keep the checks green.** Run `make hooks` once in your clone; the hooks then check the commit message, the PHP
   syntax, the code style and the file manifest (`share/filelist.md5`, fixed with `make manifest-fix`) at every
   commit. Run the tests and update the documentation when your change needs it.
6. **Open the pull request** and describe what you changed, why, and how you tested it. Mention the issue it fixes
   (`Fixes #123`).

The project's continuous integration runs on every pull request ([CI](../../bc/6.0/ci.md)). The project merges every
pull request and keeps the contributor's commits as written, fixing on top whatever needs fixing
([Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#contributors-and-pull-requests)).

### The license of your contribution

Contribute under the same terms as everyone else: the GNU GPL version 2 or later. The website's FAQ asks for this so
that contributions stay compatible with the rest of the code and the project can move to later versions of the GPL as
a whole; `CONTRIBUTING.md` says the same. You keep the copyright of your code. If you cannot license a contribution
that way, do not submit it ([Licensing questions](licensing-faq.md#may-i-contribute-code-under-gpl-version-3-only-or-gpl-version-2-only)).

## Test changes before they are released

Every change reaches `main` on GitHub long before it is in a release. Running `main` on a test installation and
reporting what breaks is one of the most useful contributions there is.
[Update with Git](../update-with-git.md) shows how to follow `main` and how to go back to a release. Report what you
find in the [issue tracker](https://github.com/se7enxweb/exponential/issues), with the commit you tested.

## Translations

The website's [Exponential Translation Project](https://exponential.earth/about/etp) (ETP) restarted the translation
work for the Exponential family: first to take stock of the existing translations and how complete each is, then to
add new languages, with the aim that every language in common use has a ready translation.

In this repository the translations are the files `share/translations/<locale>/translation.ts`; the state of each
language, and which are complete, is on [Languages](../../languages.md). How to translate and test:
[Contributing translations](../supporters/contributing-translations.md).

## Documentation

The documentation you are reading is the folder `doc/` of the kernel, under the GNU Free Documentation License; a
correction is a pull request like any other ([Free documentation](../free-documentation.md)). The community wiki,
[eZpedia](https://ezpedia.exponential.earth/), is open to anyone who signs in. The roadmap also asks for help with the
documentation websites ([The roadmap](roadmap.md#the-community-websites)). More:
[Contributing documentation](../supporters/contributing-documentation.md).

## Composer packages and site packages

The official roadmap asks for volunteers for two jobs ([The roadmap](roadmap.md#two-calls-for-volunteers)):

- **Composer packages**: many older extension repositories still lack a `composer.json` and a Packagist entry. Adding
  them is a small, well-defined pull request per extension
  ([Contribute extensions back](../contribute-extensions-back.md#4-write-composerjson)).
- **Site packages**: the project wants community site packages in the setup wizard's list
  ([Contribute your site package](../site-packages/contribute-your-site-package.md)).

## Help in the community

Answer questions in the forums and chats, write an article on Share Exponential, report a broken link on a project
website in the [project tracker](https://github.com/se7enxweb/exponential-community/issues), or tell others about
Exponential. These cost no money and keep the community alive. More ideas:
[Helping in the community](../supporters/helping-in-the-community.md) and the
[advocates' section](../advocates/advocate-toolkit.md).

## Related pages

- [Contribute extensions back](../contribute-extensions-back.md)
- [Develop with Git](../develop-with-git.md)
- [Ways to support Exponential](../supporters/ways-to-support-exponential.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
