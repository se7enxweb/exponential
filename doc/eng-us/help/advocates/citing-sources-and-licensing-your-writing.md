---
title: Citing sources and licensing your writing
audience: advocate
category: advocacy
order: 55
summary: How advocates cite the documentation and code correctly, quote under the GNU FDL and the GPL, credit other people's work, use material from eZpedia and Share, and choose a license for their own posts, talks and videos. Not legal advice.
keywords: citation, sources, quoting, license, GFDL, GNU Free Documentation License, GPL, copyright, attribution, credit, eZpedia, Share, Creative Commons, own writing
type: help
---
# Citing sources and licensing your writing

This page is for advocates who write, present or record. It answers: how do I cite the documentation, what may I copy
from it, how do I credit others, and how do I license what I make?

> **Warning**
> This page is not legal advice. It points to the license texts and the project's statements. For a legal question,
> read the license and ask an adviser.

## Contents

1. [How to cite](#how-to-cite)
2. [Quoting versus copying](#quoting-versus-copying)
3. [Copying from the help (GNU FDL)](#copying-from-the-help-gnu-fdl)
4. [Code examples (GPL)](#code-examples-gpl)
5. [Other community sources](#other-community-sources)
6. [Crediting people and projects](#crediting-people-and-projects)
7. [Licensing your own work](#licensing-your-own-work)

## How to cite

A good citation lets the reader find the exact place:

| Source | Cite as |
|---|---|
| A help page | Its title and a link to the page in the repository, e.g. `doc/eng-us/help/security-hardening.md` on GitHub |
| A section | The page and the heading: "Security hardening, Checklist" |
| A repository file | The file name and its link, e.g. SECURITY.md |
| A commit | The short hash, which anyone can open with `git show <hash>` |
| A public website | The site and the page, with the date you read it |

Link to the file on GitHub (for example `https://github.com/se7enxweb/exponential/blob/main/doc/eng-us/help/...`) rather than
to an administration URL of one installation, which readers cannot open.

## Quoting versus copying

- **Quote** short passages in quotation marks, with the source. This is how the help itself cites its sources.
- **Copy** whole pages or large parts only under the license's conditions (below).
- **Paraphrase** in your own words and still link the source.

## Copying from the help (GNU FDL)

The documentation is under the GNU Free Documentation License 1.3 or later
([The license of this documentation](../../README-LICENSE.md)). You may copy, translate, adapt and share it under the
license's conditions. [Free documentation](../free-documentation.md#what-you-can-do) explains, in plain words:

- [Copy it and share it](../free-documentation.md#copy-it-and-share-it-with-other-users)
- [Translate it](../free-documentation.md#translate-it)
- [Adapt and improve it, and share your version](../free-documentation.md#adapt-and-improve-it-and-share-your-version)
- [Combine it with other works](../free-documentation.md#combine-it-with-other-works)
- [Cite it](../free-documentation.md#cite-it)

Some older pages carry their own terms ([Older pages with their own terms](../free-documentation.md#older-pages-with-their-own-terms));
check the page you copy.

## Code examples (GPL)

Code from Exponential is under the GNU GPL version 2 or later
([LICENSE](https://github.com/se7enxweb/exponential/blob/main/LICENSE)). Short snippets in a tutorial that show how to
call something are common; copying larger parts of the code into your own software brings the GPL's conditions with it.
When in doubt, link to the file instead of copying it.

## Other community sources

| Source | License | What to do |
|---|---|---|
| [eZpedia](https://ezpedia.exponential.earth/) | GNU FDL (the site says so) | Quote with a link; copy only under the GFDL |
| [Share](https://share.exponential.earth/) posts | Each author's own terms | Quote briefly with a link; ask the author before copying |
| [Projects](https://projects.exponential.earth/) | Each project's own license | Read it in the project |
| Training books mirror, "Learning eZ Publish" | Per the repository descriptions; "Learning eZ Publish" under the GFDL | [Community places](../who-is-the-exponential-foundation.md#community-places) |
| Other websites and blogs | Usually all rights reserved | Link, do not copy |

When you contribute text to the help that comes from another source with a different license, say so in your pull
request ([Contributing to the documentation](../../README-LICENSE.md#contributing-to-the-documentation)).

## Crediting people and projects

- Credit work as the project does: "brings the Netgen Layouts concept to the Exponential kernel", "with full credit
  to Mugo Web" ([How the project grows](../who-is-the-exponential-foundation.md#how-the-project-grows)).
- Name contributors only as public sources name them, and only with what is public.
- Keep copyright notices exactly as written: "7x & Exponential Foundation"
  ([Brand and naming rules](../business/brand-and-naming-rules.md#the-nonprofit-the-exponential-foundation)).

## Licensing your own work

Your posts, slides and videos are yours to license. Options:

| Choice | Effect |
|---|---|
| GNU FDL 1.3 or later | Same license as the help; easy to contribute back into it |
| A Creative Commons license | Common for posts and slides; check compatibility before contributing to the help |
| All rights reserved | Others may only link and quote |

Whatever you choose, **state it** at the end of the work. If you want your tutorial to become a help page one day,
the GNU FDL makes that simplest.

## Related pages

- [Advocating for free documentation](advocating-for-free-documentation.md)
- [Writing a blog post](writing-a-blog-post.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
