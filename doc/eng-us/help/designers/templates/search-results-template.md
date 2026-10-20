---
title: Search results template
audience: designer, developer
category: templates
order: 700
summary: Styling the site search: the content/search.tpl template and the variables it uses, the search fetch, results drawn with a search view, the navigator with the search text in its links, an accessible search form, no results, and the advanced search.
keywords: search template, content/search.tpl, search results, searchresult.tpl, SearchText, fetch content search, search form, no results, advanced search, search page design
type: help
---
# Search results template

This page is for designers who style the site's search page. The content module's search view renders
`content/search.tpl`; your design copies it and changes the markup.

## Contents

1. [What the shipped template does](#what-the-shipped-template-does)
2. [Your version](#your-version)
3. [The form](#the-form)
4. [No results](#no-results)
5. [Other search pages](#other-search-pages)

## What the shipped template does

`design/standard/templates/content/search.tpl`:

1. fetches the results itself when `$use_template_search` is set:
   `fetch( 'content', 'search', hash( 'text', $search_text, 'section_id', ..., 'subtree_array', ..., 'offset', $view_parameters.offset, 'limit', $page_limit ) )`,
   and reads `SearchResult`, `SearchCount` and `StopWordArray` from the result;
2. prints the form, the number of matches, the results through `content/searchresult.tpl`, and the navigator with the
   search text kept in the page links (`page_uri_suffix`).

The variables it uses: `$search_text`, `$search_result`, `$search_count`, `$stop_word_array`, `$view_parameters`.

## Your version

Copy it to `design/mytheme/templates/content/search.tpl`, keep the variables and the fetch, and change the markup.
Draw each hit with a view mode, so classes get their own look:

```tpl
<h1>{'Search'|i18n( 'design/mytheme/search' )}</h1>
{if $search_text}
    <p role="status">{'%count results for "%query"'|i18n( 'design/mytheme/search',, hash( '%count', $search_count, '%query', $search_text|wash ) )}</p>
{/if}
<ol class="search-results">
{foreach $search_result as $hit}
    <li>{node_view_gui content_node=$hit view='line'}</li>
{/foreach}
</ol>
```

Wash `$search_text` everywhere it is printed: it comes from the visitor.

## The form

```tpl
<form role="search" action={'/content/search'|ezurl} method="get">
    <label for="search-text">{'Search for'|i18n( 'design/mytheme/search' )}</label>
    <input id="search-text" type="search" name="SearchText" value="{$search_text|wash}">
    <button type="submit" name="SearchButton">{'Search'|i18n( 'design/mytheme/search' )}</button>
</form>
```

The field name `SearchText` is what the search view reads.

## No results

Say so in words, repeat the query, and offer help: check the spelling, fewer words, a link to the front page. The
shipped template also lists stop words that were ignored.

## Other search pages

`content/advancedsearch.tpl` for the advanced form (the media design styles it with `exp-advanced-search.css`). With a
search engine extension such as ezfind installed, its own templates may replace these; check the extension's design
folder.

## Related pages

- [Pagination](pagination.md)
- [Accessible forms](../theme-design/accessible-forms.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
