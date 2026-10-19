---
title: "Lesson 3.3: Site search for visitors"
audience: trainer, admin, developer, website-creator
category: training
order: 33
summary: Learners test the public site's search as a visitor, control it through the address parameters SearchText, SubTreeArray and SearchPageLimit, explain how PathPrefix limits it to one site, and name the template that shows results. 30 minutes.
keywords: search course, site search, content/search, SearchText, SubTreeArray, SearchPageLimit, PathPrefix, search.tpl, robots.txt, visitors, search box
type: help
---
# Lesson 3.3: Site search for visitors

Visitors judge a site by its search. They use the search box of the public siteaccess, which sends their words to the
view `content/search`. This lesson shows administrators what a visitor really gets, how the request can be shaped by
the address, and where a developer changes the look of the results.

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Module | [Module 3: Searching in practice](README.md) |
| Duration | 30 minutes (10 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Site administrators, website creators and developers |
| Prerequisites | [Lesson 3.1](the-admin-search-and-advanced-search.md); the training installation's public site in a browser |

## Learning objectives

After this lesson, learners can:

- search the public site as an anonymous visitor and compare the results with the administration's;
- call `content/search` directly with `SearchText`, and narrow it with `SubTreeArray` and `SearchPageLimit`;
- explain that a siteaccess with `[SiteAccessSettings] PathPrefix` searches only its own tree when no subtree is
  given;
- name the template that shows the results (`content/search.tpl`) and why search pages are not crawled.

## Trainer's notes

### How to present it

Source: [Site search](../../../creators/website-creators/site-search.md), [A useful robots.txt out of the box](../../../../features/6.0/robots-txt.md)
and the view `kernel/content/search.php`.

1. **The request** (3 minutes). The search box is a form that sends `SearchText` to `content/search`, for example
   `/content/search?SearchText=salad`. Visitors find only published, visible items they may read.
2. **Parameters** (3 minutes). `SubTreeArray` limits the search to a node and everything below it (one or more node
   ids). `SearchPageLimit` chooses how many results a page shows (the view maps `1` to 5, `2` to 10, `3` to 20, `4`
   to 30, `5` to 50); whatever is asked, `MaximumSearchLimit` (30 as shipped) is the ceiling. Without a text, nothing is
   searched unless `AllowEmptySearch` is enabled.
3. **One site in a multisite installation** (2 minutes). When the siteaccess has a `PathPrefix` and no subtree is
   asked for, the search is limited to the siteaccess's own tree. Visitors of one site do not get results from
   another.
4. **The results page** (2 minutes). The design's `content/search.tpl` shows the results: the heading
   `Search for "%1" returned %2 matches` in the base design, the line view of each hit, paging, and **Search tips**
   when nothing is found. `robots.txt` disallows `/content/search/` and `/content/advancedsearch/`, because search
   results add no value to a search engine's index.

### Common questions

**Can visitors use the advanced search?** `content/advancedsearch` exists for every siteaccess whose users may read
content; whether the design links to it is a design decision.

**Why does the visitor see a different number of results than I do?** The anonymous role reads less than an
administrator: only some sections, no hidden items.

**Can we show results differently for one class?** Yes, through the line view templates of the design; that is
template work for the Theme design course.

### Pitfalls

- **Testing while signed in.** A signed-in administrator on the public siteaccess still has administrator rights.
  Use a private browser window to test as a visitor.
- **A design without a search box.** Some training designs have no box. Use the address directly.

## Hands-on exercise

Use your own training installation. Open the public site in a private browser window, so that you are an anonymous
visitor. Replace `<site>` with the address of your public siteaccess.

1. Use the site's search box, if it has one, for a word from the sample content. Note the address in the browser.
2. Open `<site>/content/search?SearchText=<word>` directly with the same word. Compare.
3. In the administration, find the node id of a folder that contains some of the matches (the **Node** button on its
   page).
4. Open `<site>/content/search?SearchText=<word>&SubTreeArray=<node id>`. Count the results.
5. Add `&SearchPageLimit=1` to the address. How many results does one page show now? Use the paging links.
6. Search for a word that is certainly not on the site, for example `zzqqxx`. Read the page.
7. Open `<site>/robots.txt` and find the lines for `content/search`.
8. Repeat step 2 in the administration's search, signed in as administrator, and compare the number of matches.

## Expected result

- Steps 1 and 2 give the same results; the address in step 1 shows `content/search` and `SearchText` (unless the
  design rewrote the address).
- Step 4 shows only matches below that folder; step 5 shows five per page with paging links.
- Step 6 shows the no-results message and the search tips of the design.
- Step 7 shows `Disallow: /content/search/` and `Disallow: /content/advancedsearch/`.
- Step 8 usually finds as many or more matches than the visitor.
- The trainer checks the addresses of steps 4 and 5 on one learner's screen.

![The search page of the public site](../../../images/trainers/se-site.webp)

## Quiz

1. Which parameter carries the visitor's words to `content/search`?
2. Which parameter limits the search to a part of the tree?
   A. `SearchSectionID` only B. `SubTreeArray` C. `PathPrefix` D. `SearchPageLimit`
3. A visitor asks for 50 results per page. How many do they get with the shipped settings, and why?
4. In a multisite installation, how does Exponential keep one site's search from listing another site's pages?
5. Why does the shipped `robots.txt` disallow the search pages?

<details>
<summary>Answers</summary>

1. `SearchText`.
2. **B.**
3. At most 30: `MaximumSearchLimit` caps the number per request.
4. When the siteaccess has a `PathPrefix` and no subtree is given, the search is limited to that siteaccess's tree.
5. Search results add no value to a search engine's index ([A useful robots.txt out of the box](../../../../features/6.0/robots-txt.md)).

</details>

## Further reading

- [Site search](../../../creators/website-creators/site-search.md): the search box, the results page and the settings.
- [A useful robots.txt out of the box](../../../../features/6.0/robots-txt.md): what search engines may crawl.
- [Searching](../../../searching.md#searching-the-site): what visitors see, in the editors' help.

## Related pages

- [Module 3: Searching in practice](README.md)
- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
