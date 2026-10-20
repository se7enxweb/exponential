---
title: Contributing documentation
audience: supporter
category: supporting
order: 40
summary: How to improve the Exponential documentation and this help - fixing a page with Edit on GitHub, writing a new help page with front matter, style rules, sources, the GFDL, and other places to write (eZpedia, share.exponential.earth).
keywords: documentation, docs, help, write, edit on GitHub, front matter, Markdown, GFDL, eZpedia, tutorial, how-to, style
type: help
---
# Contributing documentation

This page is for anyone who wants to make the documentation better. You need a GitHub account; no programming.

## Fix a page in two minutes

Every help page has an "Edit on GitHub" link. Open it, change the text in the browser, and propose the change as a
pull request. Typos, outdated steps, unclear sentences: all welcome.

## Write a new help page

The help shows the git-tracked files in `doc/`. A help page is a Markdown file under `doc/help/` that starts with
front matter:

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

<First paragraph: who the page is for and what it answers.>
```

The audience and category values the help understands are configured in `settings/exphelp.ini`.

## Style that makes pages trustworthy

- American English; "Exponential" for the product; the old name eZ Publish only for history.
- Every fact with its source: a file, a page, or a command with its expected output.
- Steps that work on a current installation; say which version you tested.
- No secrets, no private host names, no personal data in examples.
- Tables and checklists where they help; short sentences.

## License

The documentation is free documentation under the GNU Free Documentation License 1.3 or later
([The license of this documentation](../../README-LICENSE.md)). Your contribution is published under the same
license, so others may copy, translate and improve it ([Free documentation](../free-documentation.md)).

## Other places to write

| Place | What | License |
|---|---|---|
| eZpedia <https://ezpedia.exponential.earth/> | "The free eZ Publish encyclopedia that anyone can edit", since 2006 | GFDL |
| share.exponential.earth <https://share.exponential.earth/> | Forums, blogs, tutorials | As stated on the site |
| Your own blog | Tutorials and stories | Your choice; a free license helps others reuse it |

## What the documentation needs most

- How-tos for tasks people ask about in the forums.
- Screens of the current administration in tutorials that still show old ones.
- Translations of help pages ([Contributing translations](contributing-translations.md)).

## Related pages

- [Contributing translations](contributing-translations.md)
- [Free documentation](../free-documentation.md)
- [Develop with Git](../develop-with-git.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
