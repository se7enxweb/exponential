---
title: Documentation sites: start here
audience: customer, editor
category: customer-success
order: 40
summary: For product documentation, knowledge bases and manuals on Exponential: structure, review and release states, search, translations, PDF and e-book exports, and the routines that keep documentation correct.
keywords: documentation site, knowledge base, manual, product documentation, help center, documentation page, review, release, search, PDF, EPUB, translations
type: help
---
# Documentation sites: start here

Documentation is read when someone needs an answer now. A documentation site is judged by two things: whether the
answer can be found in seconds, and whether it is still true. Everything else (the design, the number of pages)
comes after. Exponential is a good fit for documentation because its content is structured: a page has fields, a
place in a tree, versions, translations and relations, so a manual can grow for years without becoming a heap of
loose pages.

This page is for the documentation lead, the technical writers and the product owner who answers for the docs.

## What Exponential gives a documentation site

| Need | What carries it | Where it is explained |
|---|---|---|
| Pages in a clear hierarchy | The content tree and container classes; the demonstration installation has a *Documentation page* class | [Structuring the content tree](../../creators/website-creators/structuring-the-content-tree.md) |
| Review before release | Drafts, approval workflows, object states | [Approvals and review](../day-to-day/approvals-and-review.md), [Object states](../../../guides/object-states.md) |
| Knowing what a page said before | Version control | [Publishing and versions](../../publishing-and-versions.md) |
| Answers found fast | The built in search engine, or Solr through eZ Find where installed | [Site search](../../creators/website-creators/site-search.md), [Search with eZ Find and Solr](../../creators/website-creators/search-with-ezfind-and-solr.md) |
| The same page in several languages | Translations of every item | [Languages](../../languages.md), [Translation routine](../day-to-day/translation-routine.md) |
| Printable manuals | The PDF export (**Setup > PDF export**) | [PDF export](../../../features/6.0/pdf-export.md) |
| Links that do not break | URL aliases and redirects, link checks | [URLs, redirects and moved pages](../day-to-day/urls-redirects-and-moved-pages.md) |

## A worked model: the help you are reading

The administration help of Exponential is itself a documentation site, and it shows the patterns. Since 6.0.15, help
topics, manuals, figures and the glossary can be maintained **as content of the site**: eight classes in the class
group "Help" (manual, chapter, concept, task, reference, troubleshooting, figure, glossary entry), an object state
group `help_status` with the states Draft, Review and Released, roles "Help editor" and "Help release", and an
export of a manual as EPUB or PDF (`doc/eng-us/features/6.0/help-content.md`). A demonstration
installation has these classes.

You do not have to use these classes for your own product's documentation, but the model is worth copying:

- **topic types** (a concept explains, a task gives steps, a reference lists facts, troubleshooting starts from a
  symptom), so writers know what a page is for;
- **a release state separate from publishing**, so a page can be visible to reviewers before it is released;
- **two roles**: people who write and people who release;
- **a glossary**, so a term means the same on every page.

Ask your partner whether your site's classes follow such a model, and what it would take if not
([Change requests](../partners-and-hosts/change-requests.md)).

## Keeping documentation true

- **With every product release**: list the pages the release changes, update them as drafts, have them reviewed,
  and release them on the release day ([Scheduled publishing](../day-to-day/scheduled-publishing.md)).
- **Every month**: look at what readers search for and do not find
  ([Site search and what visitors look for](../day-to-day/site-search-and-what-visitors-look-for.md)): each failed
  search is a missing page or a missing word.
- **Every quarter**: a [content audit](../day-to-day/content-audits.md): pages nobody reads, pages for products
  that no longer exist, and broken links.

> **Tip**
> Never delete a documentation page that people may have bookmarked. Move it, keep its old address working with a
> redirect, or replace its text with a pointer to the page that took over.

## Your next step

Follow the [documentation site reading path](documentation-site-reading-path.md). Documentation in several languages
should add the [multilingual reading path](multilingual-site-reading-path.md).

## Related pages

- [Documentation site reading path](documentation-site-reading-path.md)
- [Help maintained as content](../../../features/6.0/help-content.md)
- [Relations and reuse](../../creators/website-creators/relations-and-reuse.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
