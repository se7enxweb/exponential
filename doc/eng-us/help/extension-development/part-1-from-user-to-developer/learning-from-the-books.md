---
title: Learning from the free books
audience: user, developer, designer, app-creator
category: extension-development
order: 140
summary: Three books about the code base, kept on GitHub by se7enxweb, two under the GNU FDL: what each covers, how to read them against Exponential 6, and how to reuse them.
keywords: books, learning, Learning eZ Publish, Building an eZ Publish Site, Basics guide, GFDL, free documentation, PDF, source, history
type: help
---
# Learning from the free books

Part 1, chapter 14. Several books were written about the code base Exponential continues, when it was still called
eZ Publish. Three of them are kept in repositories of the se7enxweb organization. They teach the concepts that have
not changed (content classes and objects, the node tree, templates, overrides, siteaccesses, extensions) in the long,
patient form only a book allows.

## The three books

| Repository | What it is | License (as stated in the repository) |
|---|---|---|
| [Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish) | "Learning eZ Publish", described as the first book published about the system. A PDF and its sources | GNU Free Documentation License 1.3 (`LICENSE.md`, `COPYRIGHT.md`, `AUTHORS.md` in the repository) |
| [Book-Building-An-eZPublish-Site](https://github.com/se7enxweb/Book-Building-An-eZPublish-Site) | "Building a eZ Publish Site", building a complete site step by step. Sources | GNU Free Documentation License 1.3 (`LICENSE.md`, `COPYRIGHT.md`, `AUTHORS.md`) |
| [Book-eZPublish-Basics-GuideBook](https://github.com/se7enxweb/Book-eZPublish-Basics-GuideBook) | "eZ Publish Basics (Guide)", a mini book. A PDF | The repository description says it was released under the GNU FDL, but the repository holds no license file. Link to it; do not copy from it. |

The Share Exponential portal also links a books page from its **Learn** menu
([Share Exponential](share-exponential-earth.md)).

## Reading them against Exponential 6

The books describe older releases. Read them for concepts and keep this table at hand:

| In the books | In Exponential 6 | In this book |
|---|---|---|
| Installing from a tarball and a web wizard | `composer`, Git clones, `exp:install`, the kickstarter or the setup wizard | [Setting up a development installation](setting-up-a-development-installation.md) |
| Editing `settings/override/site.ini.append.php` by hand | Still possible; `./console exp:ini` does it with a backup and a diff | [Changing settings with exp:ini](../part-2-builder/the-exp-ini-command.md) |
| The `design/` folders, the override system | Unchanged in principle; more designs (admin4, admin4l) | [Part 2](../part-2-builder/README.md) |
| Extensions with `include_once()` of kernel files | Autoloading | [Autoloads and class naming](../part-3-extension-developer/autoloads-and-class-naming.md) |
| PHP 4 and PHP 5 examples | PHP 8.0 to 8.5 | [Part 4](../part-4-upgrading/README.md) |
| JavaScript in templates | Script files only, under a Content Security Policy | [Content Security Policy for extension authors](../part-3-extension-developer/content-security-policy-for-extensions.md) |

The chapters of the Learning book, rewritten for Exponential 6, are in [Learning Exponential](../../book-learning/README.md), and the tutorial *Building a site* is in [Building a site step by step](../../book-building-a-site/README.md).

## Reusing their text

The two books under the GNU FDL 1.3 may be quoted, translated and adapted, under the same license, with credit. The
same license covers this help, so a passage of those books can become part of a help page if you:

1. keep the GNU FDL 1.3 notice (this help's notice already is one),
2. name the book and link its repository,
3. say what you changed (for example "updated for Exponential 6"),
4. keep the authors' copyright lines; `AUTHORS.md` and `COPYRIGHT.md` in each repository name them.

The details of what the GNU FDL asks are on [gnu.org](https://www.gnu.org/licenses/fdl-1.3.html) and in this help's
[Free documentation](../../free-documentation.md) page. When in doubt, link instead; see
[Linking versus copying](linking-versus-copying.md).

## Bringing a book up to date

A book under a free documentation license can be improved by anyone. If you find a chapter whose example no longer
works, a pull request to the book's repository with the Exponential 6 version is a welcome contribution, and a fine
first pull request for a new contributor (see
[Contributing back with pull requests](../part-5-extend-fork-contribute/contributing-back-with-pull-requests.md)).

## Related pages

- Previous: [eZpedia (ezpedia.exponential.earth)](ezpedia-exponential-earth.md)
- Next: [Reading licenses before you reuse anything](reading-licenses-before-reusing.md)
- [Who is 7x?](../../who-is-7x.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
