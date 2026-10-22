---
title: Why does search not find an item I know exists?
audience: editor, admin
category: manual
order: 496
summary: Search finds only published versions of items you may read, whole words, and only fields marked as searchable; drafts, the trash, unsearchable fields, a search scope that is too narrow, and indexing that runs later are the usual reasons an item is missing from the results.
keywords: search, not found, no results, searchable, search index, published only, drafts, wildcard, current location, delayed indexing, reindex, permissions
type: help
---
# Why does search not find an item I know exists?

The search does not read the content tree directly. It looks things up in an index that is filled when an item is
published. What is not in the index, or not visible to you, is not found. Go through the checks below in order.

## Who can do this

Every editor can do the checks. The last ones need an administrator.

## Steps

1. **Check the words.** The built-in search matches whole words, upper and lower case alike: *garden* does not find
   *gardening*. Try another form of the word, or fewer words. (Sites with Solr search are more forgiving.)
2. **Check the scope.** If the search scope is set to **Current location** or to one section, items elsewhere are
   left out. Open the funnel button of the search field and choose to search everywhere.
3. **Check that the item is published.** Drafts, pending versions and archived versions are not searched. Open the
   item, or look in **My drafts**: an item that was never published cannot be found.
4. **Check the trash.** Removed items are not found. Open **Trash** at the bottom of the left column.
5. **Check the field.** Only fields marked as **searchable** in the class are indexed; numbers, prices and dates
   usually are not. Search for a word from the title instead, or ask an administrator to look at the class
   (**Setup** > **Classes**, the field's **Searchable** box).
6. **Wait a little.** Some sites index new content in the background, a few minutes after publishing.
7. **Ask an administrator** if the item is still missing. Your role may not let you read it, or the index needs to be
   rebuilt.

## What can go wrong

- **An asterisk does nothing.** Searching with `*` at the end of a word works only on sites that switched wildcard
  search on, and then only after a few letters.
- **A changed class field is still not found.** Ticking **Searchable** affects items published afterwards. Existing
  items are found once they are published again, or after an administrator rebuilds the index.
- **Visitors find less than you.** The public site does not show hidden items, and visitors may read less than
  editors.

## Related pages

- [Searching](../searching.md)
- [How do I find content in the administration?](how-do-i-find-content.md)
- [Searchable fields and indexing](../government/building/search/searchable-fields-and-indexing.md)
- [Search with ezfind and Solr](../creators/website-creators/search-with-ezfind-and-solr.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
