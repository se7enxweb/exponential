---
title: Contributing documentation and help pages
audience: community, supporter, editor, advocate, trainer
category: community
order: 26
summary: How community members improve the Exponential help and documentation - fixing a page with Edit on GitHub, writing a new help page with the right front matter, the style rules, checking links with exp:help check, the GNU FDL, and other places to write.
keywords: documentation, help pages, Edit on GitHub, front matter, Markdown, exp:help check, GNU FDL, style, American English, eZpedia, Share, writing
type: help
---
# Contributing documentation and help pages

The help you are reading is free documentation, written in Markdown in the repository under `doc/eng-us/`. Anyone
can improve it, and no programming is needed.

## Contents

1. [Fix a page](#fix-a-page)
2. [Write a new page](#write-a-new-page)
3. [Style rules](#style-rules)
4. [Checking your page](#checking-your-page)
5. [The license](#the-license)
6. [Other places to write](#other-places-to-write)

## Fix a page

Every help page has an "Edit on GitHub" link. Open it, change the text in the browser, and propose the change as a
pull request ([Contributing documentation](../../supporters/contributing-documentation.md)). Typos, outdated steps
and unclear sentences are all welcome.

## Write a new page

A help page is a Markdown file under `doc/eng-us/help/` that starts with front matter:

```text
---
title: <Title>
audience: editor, admin
category: editing
order: 50
summary: <One or two sentences: who it is for and what it answers.>
keywords: <comma separated>
type: help
---
# <Title>
```

The audience and category values the help understands are configured in `settings/exphelp.ini`
([Contributing documentation](../../supporters/contributing-documentation.md)). Help pages end with a "Related pages"
list. The help shows the files that git tracks, so a new page appears once it is committed.

## Style rules

From [Contributing documentation](../../supporters/contributing-documentation.md):

- American English; "Exponential" for the product; the old name only for history.
- Every fact with its source: a file, a page, or a command with its expected output.
- Steps that work on a current installation; say which version you tested.
- No secrets, no private host names, no personal data in examples.
- Tables and checklists where they help; short sentences.

## Checking your page

On a clone, `./console exp:help check` checks "the links and images of the pages" (`bin/php/exphelp.php`); fix every
error it lists before you open the pull request. `./console exp:help show <page id>` shows the metadata and headings
of one page. The help system is described in [The help system](../../../features/6.0/help-system.md).

## The license

The documentation is under the GNU Free Documentation License 1.3 or later; your contribution is published under the
same license, so others may copy, translate and improve it ([Free documentation](../../free-documentation.md)).

## Other places to write

| Place | What | Page |
|---|---|---|
| [eZpedia](https://ezpedia.exponential.earth/) | A community encyclopedia under the GFDL, for tricks and how-tos | [Contributing to eZpedia](../../advocates/contributing-to-ezpedia.md) |
| [Share](https://share.exponential.earth/) | Blogs, articles, tutorials | [Publishing on Share and Projects](../../advocates/publishing-on-share-and-projects.md) |

## Related pages

- [Contributing as a community member](README.md)
- [Improving the help](../../advocates/improving-the-help.md)
- [Keeping the help up to date](../../maintaining-help-topics.md)
- [Keeping help accurate](../helping-others/keeping-help-accurate.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
