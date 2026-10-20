---
title: How do I find content in the administration?
audience: editor, admin
category: manual
order: 325
summary: Type in the Search box of the top bar for a word search, use Advanced search to filter by class, section, date and place, or type an id, path or address into the Content finder in the left column.
keywords: search, find content, advanced search, content finder, filter, class, section, node id, path, content/search, content/advancedsearch
type: help
---
# How do I find content in the administration?

Three tools find content: the search box for words, **Advanced search** for filters, and the **Content finder** when
you know an id, path or address.

## Who can do this

Every user with `content/read`. You find only what your role may read.

## Steps

1. **By words:** type into **Search...** in the top bar and press Enter.
   The results list the matching objects, best first.
2. **With filters:** open **Advanced search** (in the search results, or **Dashboard** > **Advanced search**). Choose a
   class, and for it the attribute to search; a section; when it was published; and below which place. Click
   **Search**.
3. **By id, path or address:** type into **Content finder** in the left column, for example `275`, `/news/summer`
   or the address from the site, and click **Find**.
4. Click a result to open it.

![The Advanced search form with the class, attribute, section and date filters](images/advanced-search.webp)

## What can go wrong

- **A new object is not found.** The search index is updated by a cronjob, a few minutes after publishing. Use the
  Content finder or the tree in the meantime.
- **Too many results.** Put words in quotation marks for an exact phrase, or use **Advanced search** with a class.
- **Nothing is found but you know it exists.** It may be in the trash, hidden from your role, or in another language
  of the site.

## Related pages

- [Searching](../searching.md)
- [The content finder](../../features/6.0/content-finder.md)
- [User manual](README.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
