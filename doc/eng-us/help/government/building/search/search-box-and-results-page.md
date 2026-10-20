---
title: Search box and results page
audience: government, website-creator
category: public-sector
order: 40
summary: How to build an accessible search box and results page for a public site with Exponential: the form that sends SearchText to content/search, limiting to a part of the site with SubTreeArray, the results template, empty results, and accessibility points.
keywords: search box, search form, results page, content/search, SearchText, SubTreeArray, search.tpl, accessible search, no results, role search
type: help
---
# Search box and results page

This page is for site builders. The search box is a form that sends `SearchText` to `content/search`; the results
page is the `content/search.tpl` template of your design ([Site search](../../../creators/website-creators/site-search.md)).

## Contents

1. [The search box](#the-search-box)
2. [Searching one part of the site](#searching-one-part-of-the-site)
3. [The results page](#the-results-page)
4. [When nothing is found](#when-nothing-is-found)
5. [Accessibility](#accessibility)

## The search box

```text
<form role="search" method="get" action={'content/search'|ezurl}>
  <label for="site-search">{'Search'|i18n( 'design/mysite' )}</label>
  <input type="search" id="site-search" name="SearchText">
  <button type="submit">{'Search'|i18n( 'design/mysite' )}</button>
</form>
```

Translate the labels with `|i18n` so every language siteaccess shows them in its language.

## Searching one part of the site

The `content/search` view reads a `SubTreeArray` parameter and limits the results to that subtree
(`kernel/private/classes/views/content/search.php`). A search box on the disclosure log or the data catalog can send
it as a hidden field:

```text
<input type="hidden" name="SubTreeArray" value="{$node.node_id}">
```

## The results page

Override `content/search.tpl` in your design ([Template overrides](../../../creators/website-creators/template-overrides.md)).
It receives the results and the search text. Show:

- the search text and the number of results;
- each result with its title (a link), a short text, its type (page, document, decision) and date;
- paging when there are more results than one page.

Use each class's line view for the results, so a document shows its format and size and a decision its reference.

## When nothing is found

Say so plainly, repeat the search text, and offer what to try: other words, the A to Z of services, the contact page.
Phrases that find nothing are what **Setup > Search statistics** helps you collect (see "Search statistics and
privacy").

## Accessibility

- A visible label, not only a placeholder.
- `role="search"` on the form (as above) so assistive technology finds it.
- The results heading receives focus or announces the number of results.
- Result links that make sense alone (the title).
- Check the page with keyboard only and at 200% zoom ([Accessibility for site builders](../../../creators/website-creators/accessibility-for-site-builders.md)).

## Related pages

- [Site search](../../../creators/website-creators/site-search.md)
- [Built-in search for public sites](built-in-search-for-public-sites.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
