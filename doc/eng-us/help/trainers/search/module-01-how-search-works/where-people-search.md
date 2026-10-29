---
title: "Lesson 1.1: Where people search in Exponential"
audience: trainer, admin
category: training
order: 11
summary: Learners name every place an Exponential installation offers a search, who uses each one, and which rules decide what a search may return. 25 minutes.
keywords: search course, search, quick search, advanced search, content finder, site search, help search, permissions, overview
type: help
---
# Lesson 1.1: Where people search in Exponential

Before anyone tunes a search engine, they need a map of who searches where. An Exponential installation has several
searches that look alike but answer different questions. This lesson draws that map, so that later lessons can say
"this setting changes that search" without confusion.

> **Note**
> Use a training installation for the exercise, never a live site. Searching changes nothing, but it is a good habit
> from the first lesson on.

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Module | [Module 1: How search works](README.md) |
| Duration | 25 minutes (10 presenting, 15 exercise) |
| Level | Intermediate |
| Audience | Site administrators, support staff, developers who will look after search |
| Prerequisites | An administrator account on a training installation; Editors 101 or equivalent experience |

## Learning objectives

After this lesson, learners can:

- list the five places where people search: the administration's quick search, the advanced search, the content
  finder, the site search for visitors and the help's own search;
- say for each one whether it uses the search index or looks up the database directly;
- explain why two users can get different results for the same words;
- name the two search engines an installation can use: the built-in engine and Solr through the ezfind extension.

## Trainer's notes

### How to present it

Source: [Searching](../../../searching.md), [Site search](../../../creators/website-creators/site-search.md) and
[The content finder](../../../../features/6.0/content-finder.md).

1. **The map** (4 minutes). Draw five boxes on the board:
   - **Quick search**: the **Search...** field in the header of the administration (admin4). Searches all content
     the user may read, through the search engine.
   - **Advanced search**: the same engine with more fields (exact phrase, class, class attribute, section, published
     date). Reached through **Search scope** or the **Advanced search** link on the results page.
   - **Content finder**: **Setup > Content finder**. Not a full-text search: it looks up an id, a remote id, a URL
     alias or a file path directly in the database.
   - **Site search**: the search box of the public site, the view `content/search` of the public siteaccess.
   - **Help search**: the search field of the Help center. It searches the help pages, not content.
2. **One engine behind most of them** (3 minutes). The quick search, the advanced search and the site search all ask
   the configured search engine (`site.ini [SearchSettings] SearchEngine`). The shipped value is `eZSearchEngine`,
   the built-in engine that keeps its index in the database. With the ezfind extension active, the value is
   `ezsolr` and a Solr server answers instead. Module 4 covers that.
3. **Permissions** (3 minutes). The engine only returns what the searching user may read. An editor with a role
   limited to one subtree finds less than an administrator; an anonymous visitor finds only published, visible items
   in sections anonymous users may read. This is the most frequent cause of "search is broken" reports.

### Common questions

**Is the content finder a better search?** It is a different tool. It finds exactly one thing by its number, path or
address, and shows its versions, locations and trash entries. It does not search words in text.

**Does the help search use the same index as content?** No. It searches the help pages only.

**Why can a visitor not search on the admin siteaccess?** Visitors use the public siteaccess. The admin search needs
an account with access to the administration.

### Pitfalls

- **Testing as administrator only.** An administrator sees everything. Always repeat a search as a less privileged
  user before deciding that content is missing from the index.
- **Mixing up the content finder and the search.** Learners type a word into **Content finder** and get no match.
  Show that it expects an id, a path or an address.

## Hands-on exercise

Use your own training installation (installed with `php bin/php/console exp:install --apply`, see
[Installing from the command line](../../../../install/07-console-install.md)), signed in to the administration as
an administrator.

1. In the header, click the **Search...** field and type a word that appears in one of the sample articles, then
   press Enter.
2. Note the heading of the results page, for example `Search for <your word> returned 3 matches`, and the columns
   **Name** and **Type**.
3. On the results page, click **Advanced search**. Note the fields **Search for all of the following words:**,
   **Search for an exact phrase:**, **Class:**, **Class attribute:**, **In:** and **Published:**.
4. Open **Setup** in the top menu and click **Content finder**. Type the node number of the front page (shown as
   *Node* on its page; on a standard installation it is `2`) into **Id, path or URL** and press **Find**.
5. Type the same word as in step 1 into **Content finder** and press **Find**. Compare: what does the finder answer?
6. Open the public site in a second browser window, signed out. Use its search field, if the design has one, with the
   same word. Count the results.
7. Open the Help center (**Help menu**) and type `search` in its search field.
8. Write one line per search in your notes: where it is, who uses it, what it searches.

## Expected result

- Step 2 lists matches with **Name** and **Type**.
- Step 4 finds the front page and shows its versions and locations; step 5 finds nothing or only an alias, because the
  finder does not search text.
- Step 6 shows the same or fewer results than step 1: the visitor may read less than the administrator.
- Step 7 lists help pages, not content.
- The trainer checks the notes of two learners against the map on the board.

![The Search field in the header of the administration](../../../images/trainers/se-where.webp)

## Quiz

1. Which setting names the search engine an installation uses?
   A. `site.ini [SiteSettings] SearchEngine` B. `site.ini [SearchSettings] SearchEngine` C. `solr.ini [SolrSettings] Url`
   D. `search.ini [Engine] Name`
2. An editor and an administrator search for the same word and get different numbers of results. Name the most likely
   reason.
3. True or false: the content finder searches words in the body of articles.
4. Which search does a visitor of the public site use?
5. What is the shipped value of the search engine setting?

<details>
<summary>Answers</summary>

1. **B.** `site.ini [SearchSettings] SearchEngine` ([Search backends](../../../hosting/basics/search-backends.md)).
2. Their roles differ: the engine only returns content the user may read.
3. **False.** It looks up ids, remote ids, URL aliases and file paths
   ([The content finder](../../../../features/6.0/content-finder.md)).
4. The site search: the view `content/search` of the public siteaccess, through the design's search box.
5. `eZSearchEngine`, the built-in engine.

</details>

## Further reading

- [Searching](../../../searching.md): the editor's view of the quick search, the scope and the advanced search.
- [Site search](../../../creators/website-creators/site-search.md): the visitor's search, for website creators.
- [Search backends](../../../hosting/basics/search-backends.md): the two engines from a host's point of view.

## Related pages

- [Module 1: How search works](README.md)
- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
