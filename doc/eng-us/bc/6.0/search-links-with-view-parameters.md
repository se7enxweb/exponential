# Search links of the administration use view parameters

Read this page if you keep links, bookmarks or scripts that open a search in the administration with a query string,
such as `content/search?SearchText=news` or a `content/advancedsearch?...` address. Since 2026-11-02 the
administration's search page reads its search from view parameters only. A front-end siteaccess whose design brings
its own `content/search.tpl` is not affected.

## In short

| | |
|---|---|
| What changed | The administration's search page takes its search as view parameters (`content/search/(text)/news`), never from the query string. |
| Who is affected | Links, bookmarks, scripts and templates that open the administration's search with `?SearchText=...` or an advanced search query string. |
| How to check | Open `content/search?SearchText=news` in the administration: the page shows an empty search. |
| How to fix | Write the search as view parameters: `content/search/(text)/news`. |

## What changed

- **`content/search?SearchText=...` opens an empty search.** The query string is not read, so the page opens with an
  empty search field and no results, instead of the results for the words.
- **An old `content/advancedsearch?...` address leads to the search page, also empty.** Its query string
  (`SearchText`, `PhraseSearchText`, `SearchContentClassID`, `SearchSectionID`, `SearchDate` ...) is not read either.
  A posted advanced search form still works: the search page takes it in and answers with the address of the same
  search.
- **The search lives in the address.** The filter bar and the header search field are posted, and the view answers
  with a redirect (303) to an address such as
  `content/search/(text)/easter/(class)/2,16/(section)/1/(sort)/name_asc`. The view parameters are `text`, `mode`,
  `class`, `attribute`, `section`, `modified`, `modified_from`, `modified_to`, `published`, `published_from`,
  `published_to`, `owner`, `language`, `subtree`, `sort` and `limit`; a list is given once, its values joined by
  commas.
- **The search of the browse window (`Mode=browse`) is unchanged.**

## How to check

1. In the administration, open `content/search?SearchText=news`. The page shows an empty search field.
2. Open `content/search/(text)/news`. The page shows the results for "news".

## How to fix

Rewrite each link in the new form:

| Old | New |
|---|---|
| `content/search?SearchText=news` | `content/search/(text)/news` |
| `content/search?SearchText=news&SectionID=1` | `content/search/(text)/news/(section)/1` |
| `content/advancedsearch?SearchText=news&SearchContentClassID=2` | `content/search/(text)/news/(class)/2` |

Bookmarks of a search made before the change keep their old address. Open the search again and bookmark it once
more; the new bookmark stores the address with view parameters. PHP code builds the address with
`expContentSearchFilters::viewSuffix()`, which also escapes a slash or other special character in the words.

## See also

- [Changelog 6.0.15, the search page with a filter bar](../../../changelogs/6.0/6.0.15.md#2026-11-02-the-search-page-with-a-filter-bar-the-advanced-search-in-it)
- [Bookmarks of system pages](bookmarks-of-system-pages.md)
