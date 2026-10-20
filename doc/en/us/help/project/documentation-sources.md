---
title: "Documentation sources: where to read about Exponential"
audience: user, editor, admin, developer, designer
category: project
order: 90
summary: Every documentation source the Documentation page of exponential.earth lists, with what each covers, how current it is and how to use it next to this help - the documentation website, the free books, the articles on Share Exponential, eZpedia, the older core and SDK manuals, Martin Bauer's core concepts, the Mugo documentation, the Zeta Components documentation, training material and videos.
keywords: documentation, docs, manual, technical manual, installation documentation, exponential.doc.exponential.earth, doc.exponential.earth, books, Learning eZ Publish, PDF, Share Exponential, articles, eZpedia, doc.core.exponential.earth, doc.sdk.exponential.earth, Martin Bauer, Mugo, Zeta Components, training, YouTube, ExponentialCMSInsights, featured articles, RSS
type: help
---
# Documentation sources: where to read about Exponential

The [Documentation](https://exponential.earth/doc/) page of the project website lists the places where Exponential
is documented. Some are current, some describe releases from many years ago and are kept because their concepts
still hold. This page lists them all, read in October 2026, says what each is good for, and how to use them next to
this help.

## Start with this help

This help is the documentation of the release you are running: it is the folder `doc/` of the installation, shown in
the administration, and it changes with every change to the software. Where an older source and this help disagree
about the software, trust this help and the [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md). It is free
documentation ([Free documentation](../free-documentation.md)).

## Current sources

| Source | What it covers | Address |
|---|---|---|
| **The documentation website** | The Exponential 6 installation documentation and the technical manual (concepts, templates, configuration), together with documentation of the Exponential Platform 5 line. `doc.exponential.earth` leads to the same site. | <https://exponential.doc.exponential.earth/> |
| **Installation, 6.x** | The website's newest section, the installation of Exponential 6 | [Installation](https://exponential.doc.exponential.earth/Exponential/Technical-manual/6.x/Installation.html) |
| **Share Exponential articles** | Articles on key subjects collected over many years | <https://share.exponential.earth/learn> |
| **eZpedia** | The community wiki that anyone can edit after signing in | <https://ezpedia.exponential.earth/> |
| **ExponentialCMSInsights** | Videos about Exponential on YouTube | <https://www.youtube.com/@ExponentialCMSInsights> |
| **Training** | The website's collection of training material and developer knowledge sharing | <https://exponential.earth/doc/training> |
| **Featured articles** | Articles and blog posts about Exponential from around the web, with an RSS feed | <https://exponential.earth/doc/exponential-featured> |

## The free books

The website offers free books as PDF downloads on its [books page](https://exponential.earth/doc/books), starting
with "Learning eZ Publish", written when the software still had its old name. Three books are kept in se7enxweb
repositories, two of them under the GNU Free Documentation License; the chapter
[Learning from the free books](../extension-development/part-1-from-user-to-developer/learning-from-the-books.md)
lists them and has a table that maps what the books describe to how Exponential 6 does it today.

## Older manuals, still useful for concepts

These describe much older releases. Their concepts (content classes and objects, the node tree, templates, overrides,
siteaccesses, extensions) still apply; their installation steps, PHP versions and version numbers do not.

| Source | What it is | Address |
|---|---|---|
| **Exponential Core Documentation** | A refined copy of the usage documentation of the 3.0 to 3.4 era | <https://doc.core.exponential.earth/> |
| **Exponential SDK Documentation** | A short introduction to the concepts and APIs, with usage examples, from the 3.0 to 3.2 era | <https://doc.sdk.exponential.earth/> |
| **Core concepts by Martin Bauer** | An overview of the system's core concepts by an early author | [martinbauer.com](http://www.martinbauer.com/eZ-publish-Overview.html) |
| **Share Exponential: articles on the old name** | Articles written when the software was still called eZ Publish | <https://share.exponential.earth/learn/ez-publish> |
| **Mugo documentation** | Documentation of the software under its old name, kept by Mugo Web, a long-time contributor | <https://ezpublishdoc.mugo.ca/> |
| **The older FAQ** | The FAQ of the archived documentation, which the website calls "a bit older in age" | <https://doc.ezpublishlegacy.se7enx.com/FAQ.html> |

The website notes that Exponential is compatible with the documentation and software examples written for eZ Publish.
In practice this holds for templates, the content model and the extension API; check PHP code against the PHP
versions you run, and installation steps against the [installation book](../../install/README.md).

## Libraries Exponential uses

Exponential uses the Zeta Components, a set of PHP libraries for tasks such as mail, archives, the database and
configuration. Their documentation, with many examples, is at
[zetacomponents.org](https://zetacomponents.org/documentation/overview.html).

## How to use them together

1. Search this help first: it matches your release.
2. For a concept explained at length, read the books or the older manuals.
3. For a practical recipe, search eZpedia and the Share articles, then compare with this help.
4. If something is still unclear, ask ([Getting support](getting-support.md)), and when you find the answer, add it
   to eZpedia or to this help so the next person finds it
   ([Contribute to the project](contribute-to-the-project.md#documentation)).

## Related pages

- [The Exponential project: start here](README.md)
- [The help for editors and users](../README.md)
- [Learning from the free books](../extension-development/part-1-from-user-to-developer/learning-from-the-books.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
