---
title: "Lesson 5.3: When results are missing"
audience: trainer, admin
category: training
order: 53
summary: Learners work through a fixed checklist for "search does not find it" reports, from permissions and visibility to delayed indexing, searchable attributes, settings, the index and Solr, and solve four prepared cases on their training installation. 35 minutes.
keywords: search course, troubleshooting, missing results, not found, permissions, hidden, draft, trash, DelayedIndexing, searchable, stop words, reindex, exp:solr, checklist
type: help
---
# Lesson 5.3: When results are missing

"Search is broken" is one of the most common support reports, and it is rarely the engine. This lesson gives
learners a checklist in a fixed order, cheapest checks first, and practice on prepared cases, so they find the cause
in minutes instead of rebuilding indexes by reflex.

> **Note**
> The trainer prepares the four cases on each training installation before the lesson (see "Preparing this module"
> in the module overview). Never practise on a live site.

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Module | [Module 5: Tuning and troubleshooting](README.md) |
| Duration | 35 minutes (10 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Site administrators and support staff |
| Prerequisites | Modules [2](../module-02-indexing/README.md) and [3](../module-03-searching-in-practice/README.md) |

## Learning objectives

After this lesson, learners can:

- ask the four questions that frame every report: who searched, where, for which words, and which item;
- work through the checklist in order and stop at the first cause;
- tell a permission or visibility cause from an index cause;
- choose the smallest fix: republish, run the cronjob, change a setting, or rebuild the index.

## Trainer's notes

### How to present it

Source: [Searching](../../../searching.md#tips), [When something goes wrong](../../../when-something-goes-wrong.md),
[10.8 Search indexing and image handling](../../../../install/10-after-installing.md#108-search-indexing-and-image-handling)
and the earlier lessons of this course.

1. **Frame the report** (2 minutes). Who searched (which user or visitor), where (which siteaccess), which words
   exactly, which item they expected (get its node id; the content finder confirms it exists).
2. **The checklist** (6 minutes). Write it on the board and keep it there:
   1. Does the item exist and is it published? A draft or an item in the trash is not found (content finder).
   2. May the searcher read it? Repeat the search as that user or as a visitor; check the section and the role.
   3. Is it hidden, or outside the siteaccess's tree (`PathPrefix`) or languages?
   4. Is the word in a searchable attribute of its class (*Is searchable* on the class page)?
   5. Is the word a stop word, cut by `MaximumSearchTextWords`, or a part of a word without wildcards?
   6. Is delayed indexing on, and has the global cronjob part run since publishing?
   7. Is the index out of step (after an import, a database move or a class change)? Rebuild.
   8. With ezfind: does `exp:solr status` answer, and does `SearchServerURI` point to the right core?
3. **Smallest fix** (2 minutes). Each step has its own fix; a full rebuild is only step 7. Note the cause in the
   ticket so patterns become visible.

### Common questions

**Why not rebuild the index first, just in case?** It takes time, can make search incomplete while it runs with
`--clean`, and hides the real cause, which comes back.

**Where do I see errors of the search engine?** In the error log, **Setup > Log files**; an error page shows a
reference that [Finding an error by its reference](../../../../guides/finding-an-error-by-its-reference.md) explains.

**The editor says "it worked yesterday".** Ask what changed: a class edit, a role change, an import, a move to
another section.

### Pitfalls

- **Testing as administrator.** Steps 2 and 3 are invisible to an administrator. Always repeat as the reporting user
  or a visitor.
- **Skipping step 1.** Many reports are about drafts. Check first.

## Hands-on exercise

Use your own training installation, on which the trainer has prepared four cases, each an article with a distinct
word in its title. The trainer gives you the four words and the siteaccess to search on; you do not know which cause
belongs to which word.

1. For the first word, write the four framing answers: who searches, where, which words, which item.
2. Find the item with **Setup > Content finder** (by name in the content tree, then its node id) and note whether it
   is published, a draft or in the trash.
3. Search for the word as a visitor on the public site, then in the administration.
4. Work down the checklist until you find the cause. Write the step number.
5. Apply the smallest fix and search again.
6. Repeat steps 1 to 5 for the other three words.
7. Compare your four causes with your neighbor's.

## Expected result

- For each word the learner names one checklist step as the cause and shows the item found after the fix.
- Typical prepared causes and their fixes: an article left as a draft (publish it); an article in a section the
  anonymous role may not read (move it to a readable section); the word only in a field made not searchable (make it
  searchable again and rebuild); delayed indexing on and the cronjob not run (run the global part).
- The trainer checks the four step numbers with each learner.

## Quiz

1. Name the four questions that frame a "not found" report.
2. Which check comes first in the checklist, and why?
3. An administrator finds the item, a visitor does not. Which checklist steps are likely?
   A. 1 and 7 B. 2 and 3 C. 4 and 5 D. 8 only
4. When is a full rebuild of the index the right fix?
5. With ezfind, which command shows whether the search server answers?

<details>
<summary>Answers</summary>

1. Who searched, where (which siteaccess), which words, which item.
2. Whether the item exists and is published: drafts and trashed items are never found, and it is the cheapest check.
3. **B.** Permissions, visibility, the siteaccess's tree or languages.
4. When the index is out of step with the content: after an import, a database move or a change of searchable
   attributes ([10.8](../../../../install/10-after-installing.md#108-search-indexing-and-image-handling)).
5. `php bin/php/console exp:solr status` (or `ping`).

</details>

## Further reading

- [Searching](../../../searching.md#tips): what editors are told when they cannot find an item.
- [When something goes wrong](../../../when-something-goes-wrong.md): first steps for any problem.
- [Finding an error by its reference](../../../../guides/finding-an-error-by-its-reference.md): from an error page to
  the log entry.

## Related pages

- [Module 5: Tuning and troubleshooting](README.md)
- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
