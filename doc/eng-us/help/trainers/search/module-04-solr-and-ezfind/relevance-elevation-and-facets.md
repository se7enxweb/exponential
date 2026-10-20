---
title: "Lesson 4.4: Relevance, elevation and facets"
audience: trainer, admin, developer
category: training
order: 44
summary: Learners tune what comes first in Solr results with index-time boosts, query boosts and elevation of chosen objects, exclude classes from the index, and see what highlighting, spell checking and facets offer. 35 minutes.
keywords: search course, ezfind, relevance, boost, IndexBoost, QueryBoost, elevation, Elevate, Synchronise, IndexExclude, highlighting, spell check, facets, SortBy
type: help
---
# Lesson 4.4: Relevance, elevation and facets

The main reason to run Solr is better results: the best match first, the page an organization wants people to find
on top, and filters that let visitors narrow a long list. This lesson shows the three levers ezfind gives
administrators, boosting, elevation and exclusion, and introduces the features developers build on: highlighting,
spell checking and facets.

> **Note**
> Every change in this lesson is made on your own training installation. Boost changes need a reindex; plan that on a
> live site.

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Module | [Module 4: Solr and ezfind](README.md) |
| Duration | 35 minutes (15 presenting, 20 exercise) |
| Level | Advanced |
| Audience | Site administrators, developers, content leads |
| Prerequisites | [Lesson 4.3](indexing-with-solr.md); a Solr index built; the ezfind table created (lesson 4.1) |

## Learning objectives

After this lesson, learners can:

- set an index-time boost for a class or an attribute in `ezfind.ini [IndexBoost]` and reindex;
- elevate an object for a search query in the administration and synchronise the configuration with Solr;
- exclude a class from the index with `[IndexExclude] ClassIdentifierList[]`;
- say what `[HighLighting]`, `[SpellCheck]` and facets offer, and where a developer reads more.

## Trainer's notes

### How to present it

Source: the extension's `settings/ezfind.ini`, its admin4 template `ezfind/elevate.tpl`,
[Search with ezfind and Solr](../../../creators/website-creators/search-with-ezfind-and-solr.md#what-changes-for-templates)
and [The order of search results in the editor's dialogs](../../../../guides/content-model-and-editing.md#the-order-of-search-results-in-the-editors-dialogs).

1. **Boosting at index time** (4 minutes). `[IndexBoost]`: `Class[<class identifier>]` boosts whole objects of a
   class, `Attribute[<attribute>]` or `Attribute[<class>/<attribute>]` boosts a field, `Datatype[<datatype>]` boosts
   every field of a datatype, `ReverseRelatedScale` adds boost for objects many others relate to. The settings note
   that matching boosts are added, while class and attribute boosts multiply in Solr. Changes apply when objects are
   indexed again.
2. **Boosting at query time** (2 minutes). `[QueryBoost] RawBoostQueries[]` takes raw Solr boost queries, for example
   the commented `meta_class_identifier_ms:folder^10`. Expert setting: test it on the training site first.
3. **Elevation** (4 minutes). With ezfind active, the administration gets a top menu tab named by the extension's
   `menu.ini` (*eZ Find*) with the left menu entry **Elevation** (`ezfind/elevate`, policy `ezfind/elevate`).
   "Elevating an object" for a search query means that the object is returned among the very first results when
   that query is searched. Show **Elevate object** (browse for the object), **Search query**, **Language**, **Fuzzy
   match**, **Elevate**, the list **Existing configurations** with **Filter**, and **Synchronise**, which synchronises
   the configuration with Solr.
4. **Exclusion and features** (5 minutes). `[IndexExclude] ClassIdentifierList[]` keeps whole classes out of the
   index. `[HighLighting] Enabled=true` marks the search words in results (`SimplePre`, `SimplePost`,
   `FragmentSize`). `[SpellCheck] SpellCheck=enabled` offers spelling suggestions. Facets let visitors filter by
   class, tag or date; they are template work with ezfind's fetch functions, and the extension's own documentation
   names them for the installed version. The ordering of the editor's search dialogs is set with `ezfind.ini
   [SearchSettings] SortBy[]`.

### Common questions

**Boost or elevation?** A boost changes the weight of many objects at once and needs a reindex. Elevation pins one
object for one query and works after **Elevate** (and **Synchronise** when needed).

**Can editors elevate?** Whoever has the policy `ezfind/elevate`. Give it to the content lead, not to every editor.

**Why is my boost not visible?** Index-time boosts apply only to objects indexed after the change. Reindex.

### Pitfalls

- **Overboosting.** A boost of 10 on a class pushes every object of it above everything else. Start with small
  values such as the commented examples (`2.0`, `3.0`) and compare.
- **Elevating in the wrong language.** An elevation is stored with a language; check **Language** before pressing
  **Elevate**.
- **No table.** Without `ezfind_elevate_configuration`, elevation cannot be stored. Run the schema file of lesson 4.1.

## Hands-on exercise

Use your training installation from lesson 4.3, with Solr running. Add `--allow-root-user` only if you work as root.

1. Search the administration for a word that finds a folder and several articles. Note the order of the first five
   results.
2. Boost articles at index time and reindex:

   ```bash
   php bin/php/console exp:ini set "ezfind.ini/IndexBoost/Class[article]" 2.0 global
   php bin/php/ezcache.php --clear-tag=ini
   php extension/ezfind/bin/php/updatesearchindexsolr.php -s <admin siteaccess>
   ```

3. Search again and compare the order with step 1.
4. Open the extension's tab in the top menu and choose **Elevation** in the left menu.
5. Press **Elevate object**, browse to the folder from step 1 and select it. Enter the search word in **Search query**,
   choose the language, and press **Elevate**.
6. Search for the word again. Where is the folder now?
7. In **Existing configurations**, use **Filter** with the word to find your elevation. Read **Details** for it.
8. Undo your changes: in the elevation's details, use the remove action in **Actions** and press **Remove** on the
   confirmation page; then remove the boost and reindex:

   ```bash
   php bin/php/console exp:ini rem "ezfind.ini/IndexBoost/Class[article]" global
   php bin/php/ezcache.php --clear-tag=ini
   php extension/ezfind/bin/php/updatesearchindexsolr.php -s <admin siteaccess>
   ```

## Expected result

- Step 3 shows articles higher than in step 1.
- Step 5 shows *Successful creation of the following Elevate configuration*.
- Step 6 lists the folder among the very first results.
- Step 7 lists the elevation under *Objects elevated by* the query.
- Step 8 shows *The Elevate configuration was removed.*; after the reindex the order is as in step 1 again.
- The trainer checks steps 3 and 6 on the projector with one learner's installation.

## Quiz

1. You set `Attribute[title]=1.5`. When does it change the results?
   A. at once B. after the affected objects are indexed again C. after clearing the template cache D. never
2. What does elevating an object for a query do?
3. Which setting keeps a whole class out of the Solr index?
4. Which policy is needed to manage elevations?
5. Name two features of ezfind that change how results are shown, and the settings block of each.

<details>
<summary>Answers</summary>

1. **B.** Index-time boosts apply when objects are indexed (the extension's `ezfind.ini`).
2. It returns the object among the very first results whenever that query is searched.
3. `ezfind.ini [IndexExclude] ClassIdentifierList[]`.
4. `ezfind/elevate`.
5. Any two of: highlighting (`[HighLighting]`), spell checking (`[SpellCheck]`), autocomplete
   (`[AutoCompleteSettings]`), facets (template work with the extension's fetch functions).

</details>

## Further reading

- [Search with ezfind and Solr](../../../creators/website-creators/search-with-ezfind-and-solr.md): what Solr adds and
  what changes for templates.
- [The content model and editing content](../../../../guides/content-model-and-editing.md#the-order-of-search-results-in-the-editors-dialogs):
  ordering the editor's search dialogs with `SortBy[]`.
- [Roles, policies and unactivated users](../../../../guides/roles-and-policies.md): giving the elevation policy to a
  role.

## Related pages

- [Module 4: Solr and ezfind](README.md)
- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
