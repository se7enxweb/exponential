---
title: "Lesson 2.1: Indexing at publish"
audience: trainer, admin
category: training
order: 21
summary: Learners follow what happens to the search index when content is published, edited, hidden, moved or trashed, and prove it with searches on their training installation. 25 minutes.
keywords: search course, indexing, publish, search index, new version, hide, move, trash, drafts, eZSearchEngine
type: help
---
# Lesson 2.1: Indexing at publish

With the shipped settings, Exponential updates the search index the moment content is published. This lesson shows
learners what that means in practice: a draft is never found, a new version replaces the old words, and some
operations need no reindexing at all.

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Module | [Module 2: Indexing](README.md) |
| Duration | 25 minutes (10 presenting, 15 exercise) |
| Level | Intermediate |
| Audience | Site administrators and support staff |
| Prerequisites | [Module 1](../module-01-how-search-works/README.md); an administrator account on the training installation |

## Learning objectives

After this lesson, learners can:

- explain that only published versions are indexed, never drafts;
- show that publishing a new version replaces the indexed words of the object;
- say which operations the built-in engine needs no index update for (move, hide, reveal, a new location);
- predict what a search finds after an item is moved to the trash.

## Trainer's notes

### How to present it

Source: [10.8 Search indexing and image handling](../../../../install/10-after-installing.md#108-search-indexing-and-image-handling)
and [Publishing, versions and the trash](../../../publishing-and-versions.md).

1. **Publish indexes** (3 minutes). The publish operation hands the object to the search engine, which removes its old
   words and adds the words of the new published version. A draft is not part of the index; only the published
   version is.
2. **What the built-in engine does not need** (4 minutes). Show the table of chapter 10.8. For `eZSearchEngine` a
   move, a new location, a swap, hiding and revealing queue nothing, because its index holds no paths: the engine
   checks locations and visibility when it searches. Solr keeps paths, so for ezfind those operations do update the
   index (Module 4).
3. **Trash and delete** (3 minutes). Moving to the trash or deleting removes the object from search; restoring it
   from the trash makes it findable again.

### Common questions

**I published and the item is not found. Why?** With the shipped settings it is found at once. If not, the site may
use delayed indexing (lesson 2.2), the searching user may not be allowed to read it, or the attribute that holds the
word may not be searchable (lesson 2.4).

**Are hidden items found?** The engine filters hidden items when it searches, according to the siteaccess's visibility
rules; on the public site they are not found.

**Does a draft by another editor show up in my search?** No. Drafts are listed under **My drafts**, not in search.

### Pitfalls

- **Testing with a word from the old version.** Learners search for a word they just removed and are surprised it is
  gone. That is the point of the exercise: let them discover it.
- **Delayed indexing switched on in the lab.** If a previous class left `DelayedIndexing=enabled`, nothing is found
  at once. Check the setting before the class (`exp:ini get site.ini/SearchSettings/DelayedIndexing`).

## Hands-on exercise

Use your own training installation, signed in to the administration as an administrator.

1. Below a folder of your own, press **Create new**, choose **Article**, and give it the title `Indexing test pear`.
   Fill in the required fields with text that contains the word `orchard`.
2. Press **Store draft and exit** instead of publishing. Search for `orchard` with the **Search...** field.
3. Open **My drafts** on the dashboard, continue the draft and press **Send for publishing**. Search for `orchard`
   again.
4. Press **Edit** on the article, replace the word `orchard` with `greenhouse` everywhere and publish.
5. Search for `orchard`, then for `greenhouse`.
6. Move the article to another folder (**Move**). Search for `greenhouse`: is it still found, and with which path?
7. Press **Remove** on the article and confirm that it goes to the trash (do not delete it permanently). Search for
   `greenhouse`.
8. Open the **Trash**, restore the article to its original location and search once more.

## Expected result

- Step 2 finds nothing: the draft is not indexed.
- Step 3 finds the article at once.
- Step 5: `orchard` finds nothing, `greenhouse` finds the article (the new version replaced the words).
- Step 6 finds it with its new path; nothing had to be reindexed.
- Step 7 finds nothing; step 8 finds it again.
- The trainer checks by asking one learner to show steps 5 and 7 on the projector.

## Quiz

1. True or false: a stored draft is found by the administration search.
2. You publish version 3 of an article. Which words does the index hold for it afterwards?
   A. those of versions 1 to 3 B. those of version 3 only C. those of version 1 only D. none until the cronjob runs
3. With the built-in engine, does moving an item require an index update? Why?
4. What happens to search results when an item is moved to the trash?
5. Name two reasons why a just-published item may not be found.

<details>
<summary>Answers</summary>

1. **False.** Only published versions are indexed.
2. **B.** Publishing replaces the object's indexed words with those of the new published version (with the shipped
   settings, without delayed indexing).
3. No. Its index holds no paths; locations and visibility are checked when searching
   ([10.8](../../../../install/10-after-installing.md#108-search-indexing-and-image-handling)).
4. It is no longer found; after a restore it is found again.
5. Any two of: delayed indexing is on and the cronjob has not run; the user may not read it; the word is in an
   attribute that is not searchable; it is hidden.

</details>

## Further reading

- [10.8 Search indexing and image handling](../../../../install/10-after-installing.md#108-search-indexing-and-image-handling):
  what each operation queues.
- [Publishing, versions and the trash](../../../publishing-and-versions.md): versions and the trash for editors.
- [Searching](../../../searching.md): what editors are told about new items in search.

## Related pages

- [Module 2: Indexing](README.md)
- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
