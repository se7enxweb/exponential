---
title: "Lesson 3.2: The content finder"
audience: trainer, admin
category: training
order: 32
summary: Learners find content by object id, node id, remote id, URL alias, page address or file path with Setup > Content finder, read a result's versions, locations and trash entries, and know who may use it. 30 minutes.
keywords: search course, content finder, contentfinder/search, contentfinder/delete, node id, object id, remote id, URL alias, file path, support
type: help
---
# Lesson 3.2: The content finder

Support work often starts with a number: a node id from an address, an object id from a log, the path of an image
file. The content finder turns any of these into the object, with its versions, locations and trash entries. It is
not a word search, and that is exactly why it belongs in a search course: learners must know when to use which.

> **Note**
> The finder also offers **Move to trash**, **Delete permanently**, **Purge from the trash**, **Restore** and **Remove
> version**. This lesson only looks; nothing is removed.

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Module | [Module 3: Searching in practice](README.md) |
| Duration | 30 minutes (10 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Site administrators and support staff |
| Prerequisites | [Lesson 3.1](the-admin-search-and-advanced-search.md); an administrator account |

## Learning objectives

After this lesson, learners can:

- open **Setup > Content finder** and the small finder box below the content tree;
- find an object by node id, object id, URL alias, page address and image file path, with **Search for:**
  *Recognise automatically* and with a fixed type;
- read a result: **Found by**, the object's details, its versions, its locations and any trash entry;
- name the two policies `contentfinder/search` and `contentfinder/delete` and who should get them.

## Trainer's notes

### How to present it

Source: [The content finder](../../../../features/6.0/content-finder.md).

1. **What it takes** (4 minutes). One field, **Id, path or URL**, and **Search for:** with *Recognise automatically*
   (default), *Object id*, *Node id*, *Attribute id*, *Version id*, *Remote id*, *URL alias*, *File path*. A plain
   number is tried as object, node, attribute and version id, and every hit is shown. Prefixes such as `node:43` or
   `object:41` fix the type. A full page address, a URL alias such as `News/Summer-party`, a 32-digit remote id and a
   path below `var/` all work.
2. **What it shows** (3 minutes). **Found by** says what matched. Then the object (class, name, owner, section,
   languages, states, remote id, dates), every version with a link to the version view, every location (node id,
   path, URL alias, main, hidden), and when it is in the trash, where it was, when and by whom it was trashed.
3. **Who may use it** (3 minutes). `contentfinder/search` to open and search; `contentfinder/delete` for the actions.
   In a new installation only the Administrator role has them. The finder never shows more than the user may see
   elsewhere; when nothing is visible it answers **Nothing found, or you have no access to what was found.** Every
   search and action is recorded in the audit.

### Common questions

**Why does a word find nothing here?** The finder does not search text. A word is tried as a remote id and a URL alias
(and as part of an alias); use the search for words.

**Can a support role use it without being able to delete?** Yes: give `contentfinder/search` only.

**Does it work on the public site?** No. It answers on administration siteaccesses only (`contentfinder.ini
[ContentFinderSettings] SiteAccessList[]`).

### Pitfalls

- **Clicking an action "to see what happens".** Every action goes through a confirmation page, but tell learners not to
  press any action button in this lesson.
- **A node id and an object id are different numbers.** A plain number may hit both, as two results. Show the
  **Found by** line on each.

## Hands-on exercise

Use your own training installation, signed in as an administrator. Do not press any action button.

1. Open a sample article in **Content structure**. Note the numbers on its buttons **Node** and **Object**.
2. Open **Setup > Content finder**. Type the node number into **Id, path or URL** and press **Find**. Read **Found
   by**.
3. Type the object number and press **Find**. Compare the results with step 2.
4. Choose **Search for:** *Node id* and search for the object number. What changes?
5. Copy the article's address from the public site (or its URL alias path) and search for it with *Recognise
   automatically*.
6. Find an image object in **Media**, open its image in the browser, copy the image address and search for it in the
   finder.
7. Remove nothing. Instead, open the trash, pick an item that is already there, and search for its node number in the
   finder. Read the trash entry.
8. Write down which role in your organization should have `contentfinder/search`, and which `contentfinder/delete`.

## Expected result

- Steps 2 and 3 find the same object, with **Found by** naming the node and the object respectively; a plain number
  can also hit an attribute or version with the same number.
- Step 4 finds the node with that number, which is usually a different object or nothing.
- Steps 5 and 6 find the article and the image object.
- Step 7 shows where the item was, when and by whom it was trashed.
- The trainer checks step 8 in a short round: search for support roles, delete only for roles that also remove
  content.

## Quiz

1. What does the content finder take as input? Name four kinds.
2. True or false: the content finder searches the words of an article's body.
3. Which policy lets a role use the finder without its actions?
   A. `content/read` B. `contentfinder/search` C. `contentfinder/delete` D. `setup/system_info`
4. What does the finder answer when the user may not see what was found?
5. Where is every finder search recorded?

<details>
<summary>Answers</summary>

1. Any four of: object id, node id, attribute id, version id, remote id, URL alias, page address, file path below
   `var/` ([The content finder](../../../../features/6.0/content-finder.md)).
2. **False.** It looks up ids, aliases and paths, not text.
3. **B.**
4. **Nothing found, or you have no access to what was found.**, the same as for an id that does not exist.
5. In the audit trail, as `content.finder.search`.

</details>

## Further reading

- [The content finder](../../../../features/6.0/content-finder.md): every input, every result field and every action.
- [Roles, policies and unactivated users](../../../../guides/roles-and-policies.md): giving a support role the
  finder.
- [Audit trail](../../../../features/6.0/audit-trail.md): reading the finder's audit records.

## Related pages

- [Module 3: Searching in practice](README.md)
- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
