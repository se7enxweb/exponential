---
title: "Lesson 2.4: Choosing searchable attributes"
audience: trainer, admin, developer
category: training
order: 24
summary: Learners read which attributes of a class are searchable, decide which ones should be, change one on their training installation and rebuild the index so the change takes effect. 30 minutes.
keywords: search course, searchable, Is searchable, class attribute, class edit, datatype, index size, updatesearchindex, content model
type: help
---
# Lesson 2.4: Choosing searchable attributes

The search engine only indexes attributes marked **Searchable** in their class. That one checkbox decides whether a
word in a field can ever be found. Too few searchable fields and visitors miss content; too many and results fill
with matches in internal codes. This lesson teaches learners to read and choose the flag.

> **Note**
> Changing a class affects every object of that class. Do it only on your own training installation.

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Module | [Module 2: Indexing](README.md) |
| Duration | 30 minutes (10 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Site administrators and developers who maintain classes |
| Prerequisites | [Lesson 2.3](rebuilding-the-search-index.md); an administrator account with access to **Setup > Classes** |

## Learning objectives

After this lesson, learners can:

- find the flags **Is searchable** and **Is not searchable** on a class's page in **Setup > Classes**;
- explain that some datatypes cannot be searchable and the checkbox is then disabled;
- choose which attributes of a class should be searchable, with reasons;
- make an attribute searchable and rebuild the index so that existing objects are found by it.

## Trainer's notes

### How to present it

Source: [Site search](../../../creators/website-creators/site-search.md#settings-that-matter),
[The content model and editing content](../../../../guides/content-model-and-editing.md#2-create-a-class) and the class
edit form (admin4).

1. **Where the flag is** (3 minutes). Open **Setup > Classes**, a class group, and the class *Article*. Under
   **Attributes** every field shows its flags, for example *Is required, Is searchable, Does not collect
   information*. In the edit form the checkbox is called **Searchable**; its tooltip says it specifies "whether the
   contents of the attribute should be indexed by the search engine". When the datatype does not support indexing,
   the checkbox is disabled.
2. **What to choose** (4 minutes). The website creator's guide gives the rule: mark useful attributes searchable
   (titles, intros, bodies), not internal codes. Discuss examples: keywords yes, a product's internal number perhaps
   (staff search for it), a free text "notes for editors" field no.
3. **Changing it** (3 minutes). The flag applies to new indexing. Objects already indexed keep their old entries until
   they are republished or the index is rebuilt (lesson 2.3).

### Common questions

**Does making a field not searchable hide its content?** No. It only stops the words being found by search. The
content is still shown wherever templates show it.

**Why is the checkbox greyed out for an image field?** Its datatype does not support search indexing; the tooltip
names the datatype.

**Does it change the editor's form?** No. Editors see no difference.

### Pitfalls

- **Editing the class without a rebuild.** Learners change the flag and search at once: nothing changes for existing
  objects. Let them see it, then rebuild.
- **Leaving a class in edit.** An unfinished class edit stays as a temporary version. Make sure learners press
  **OK** or **Cancel** at the end, as the form offers.
- **Changing classes on a shared training server.** Every learner shares the class. Do this exercise on one
  installation per learner only.

## Hands-on exercise

Use your own training installation, signed in as an administrator, and a terminal in its root.

1. Open **Setup > Classes**, the group that holds *Article*, and click *Article*.
2. Under **Attributes**, write down which fields show *Is searchable* and which *Is not searchable*.
3. Create and publish an **Article** whose **Short title** (or another text field that is searchable) contains the word
   `kumquat`, and whose title does not. Search for `kumquat`.
4. Back on the class page, press **Edit**. Find that field and clear its **Searchable** checkbox. Save the class with
   **OK**.
5. Search for `kumquat` again.
6. Rebuild the index:

   ```bash
   php bin/php/updatesearchindex.php -s <siteaccess> --clean
   ```

7. Search for `kumquat` a third time.
8. Edit the class again, tick **Searchable** for the field, press **OK**, and rebuild the index once more so the class is as
   shipped.

## Expected result

- Step 3 finds the article.
- Step 5 still finds it: the old index entries are still there.
- Step 7 finds nothing: after the rebuild the field is no longer indexed.
- After step 8 the class shows *Is searchable* again for the field, and `kumquat` is found again.
- The trainer checks the class page of two learners at the end.

![The classes of a group; each class's attributes say whether they are searchable](../../../images/trainers/se-class.webp)

## Quiz

1. Where do you see whether an attribute is searchable without opening the edit form?
2. You make an attribute searchable. When are existing objects found by its content?
   A. at once B. after a rebuild of the index or a new publish C. after clearing the template cache D. never
3. Why is the **Searchable** checkbox disabled for some attributes?
4. Name two attributes of an article that should be searchable and one kind of field that usually should not.
5. True or false: clearing **Searchable** hides the field's content from visitors.

<details>
<summary>Answers</summary>

1. On the class's page in **Setup > Classes**, under **Attributes** (*Is searchable* or *Is not searchable*).
2. **B.** The flag applies when an object is indexed again.
3. The datatype does not support search indexing.
4. For example the title and the body (or intro); internal codes or editor notes usually should not
   ([Site search](../../../creators/website-creators/site-search.md#settings-that-matter)).
5. **False.** It only stops the words from being found by search.

</details>

## Further reading

- [Site search](../../../creators/website-creators/site-search.md): which attributes to mark searchable.
- [The content model and editing content](../../../../guides/content-model-and-editing.md): classes and their attributes.
- [Class groups](../../../../guides/class-groups.md): finding a class in **Setup > Classes**.

## Related pages

- [Module 2: Indexing](README.md)
- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
