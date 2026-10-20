---
title: Improving the help
audience: advocate
category: advocacy
order: 58
summary: How advocates improve the help pages they are reading - Edit on GitHub, a small pull request, the commit message format, the GNU FDL, front matter, link checks - with the kinds of fixes that help most and what to avoid.
keywords: improve the help, Edit on GitHub, pull request, documentation fix, typo, front matter, commit message, GFDL, contributing documentation, doc folder, exp:help check
type: help
---
# Improving the help

This page is for advocates who find something to improve in the help: a typo, an outdated step, a missing sentence, a
whole missing page. The help pages are the Markdown files in the repository's `doc/` folder, so improving them is a
pull request like any other ([Develop with Git](../develop-with-git.md#contribute-a-change-with-a-pull-request)).

## Contents

1. [The quick way: Edit on GitHub](#the-quick-way-edit-on-github)
2. [The thorough way: a clone](#the-thorough-way-a-clone)
3. [What helps most](#what-helps-most)
4. [Writing rules for help pages](#writing-rules-for-help-pages)
5. [What to avoid](#what-to-avoid)

## The quick way: Edit on GitHub

1. On the help page, press **Edit on GitHub**. GitHub opens the file.
2. Press the pencil to edit. GitHub offers to fork the repository for you.
3. Make your change.
4. Write a commit message that starts with what it does, for example `Fixed: The layouts help names the Discard button`
   ([CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md)).
5. Propose the change and open the pull request. Say in one sentence what was wrong and how you checked.

Every pull request is merged; while it is open, its author squashes and rebases it onto `main` as the reviews ask
([How the project grows](../who-is-the-exponential-foundation.md#how-the-project-grows)).

## The thorough way: a clone

For larger changes, work in a clone:

```bash
git clone https://github.com/<you>/exponential.git
cd exponential
git switch -c help-fix-layouts
# edit doc/eng-us/help/...
git commit -am "Fixed: The layouts help names the Discard button"
git push -u origin help-fix-layouts
```

In an installation, `./console exp:help check` reports broken links and images in `doc/`
([The help system, On the command line](../../features/6.0/help-system.md#on-the-command-line)). For kernel pull
requests, CONTRIBUTING.md also describes the commit hook and the file manifest (`make hooks`, `make manifest-fix`).

## What helps most

| Fix | Example |
|---|---|
| A step that no longer matches the screen | A button was renamed |
| A missing step | "First enable the extension" |
| A link to the deeper page | From a help page to the feature page |
| A clearer sentence | Shorter, active, the word on screen |
| A missing page for a common question | You answered it three times in the forums |
| A translation of a page | "A translated page helps every reader of that language" ([Free documentation](../free-documentation.md#help-others)) |

## Writing rules for help pages

- **Front matter**: new pages start with `title`, `audience`, `category`, `order`, `summary`, `keywords` and `type`,
  as the existing pages do ([The help system, Front matter](../../features/6.0/help-system.md#front-matter)).
- **Words on screen**: "Use the product name the reader sees on screen", and the button names exactly as shown
  (help style guide, quoted in [Brand and naming rules](../business/brand-and-naming-rules.md#language-and-spelling)).
- **American English**: license, color, behavior.
- **Sources**: link the page or file that proves a fact.
- **Safe Markdown**: raw HTML is escaped in the help; use plain Markdown, tables and fenced code
  ([What it shows, and what never](../../features/6.0/help-system.md#what-it-shows-and-what-never)).
- **License**: your change is accepted under the GNU FDL 1.3 or later; say so if any text comes from a source with a
  different license ([Contributing to the documentation](../../README-LICENSE.md#contributing-to-the-documentation)).

## What to avoid

- Changing many pages in one pull request for unrelated reasons: keep it focused.
- Copying text from websites or books with other licenses.
- Adding personal data, real customer names, keys or server addresses to examples.
- Renaming code identifiers (`eZINI`, `ezcontentobject`) in documentation of code; only the product name changes
  ([Brand and naming rules](../business/brand-and-naming-rules.md#code-identifiers-stay-as-they-are)).
- Marketing language in help pages: the help explains; it does not sell.

## Related pages

- [Advocating for free documentation](advocating-for-free-documentation.md)
- [Mentoring a first contribution](mentoring-a-first-contribution.md)
- [Free documentation](../free-documentation.md#help-others)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
