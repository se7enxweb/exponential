---
title: "Lesson 2.2: One item in several places: locations"
audience: trainer, editor
category: training
order: 22
summary: Learners give an item a second location, read the Locations tab, change the main location, hide one location and remove a location without removing the item. 45 minutes.
keywords: editors 201, locations, add locations, add a location, main location, set main, node, object, hide location, remove location, multiple placement
type: help
---
# Lesson 2.2: One item in several places: locations

An item can appear in more than one place of the tree without being copied: the same article in *News* and in
*Projects*, one text, two places. Each place is a **location** (in the item's numbers, a node). This lesson teaches
adding, reading and removing locations.

## At a glance

| | |
|---|---|
| Course | [Editors 201](../README.md) |
| Module | [Module 2: Relations, locations and embedding](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Editors who organize content |
| Prerequisites | [Lesson 2.1](relations-and-embedding.md) |

## Learning objectives

After this lesson, learners can:

- explain the difference between a copy and a second location;
- add a location with **Add locations** on the **Locations** tab or **Add a location** in a **Sub items** list;
- read the **Locations** tab (**Location**, **Sub items**, **Main**) and change the main location with **Set main**;
- hide one location and remove a location with **Remove selected** without removing the item.

## Trainer's notes

### How to present it

Sources: [Many items at once](../../../editing-content.md#many-items-at-once),
[Remove and restore](../../../publishing-and-versions.md#remove-and-restore) and
[Copying, locations and how selections are shown](../../../../guides/content-model-and-editing.md#10-copying-locations-and-how-selections-are-shown).

1. **Object and node** (3 minutes). Recall Editors 101: an item's *Object* number belongs to the item, its *Node*
   number to one place. A second location gives the same object a second node. Editing the item changes it in every
   place; a copy would be a separate item.
2. **Adding** (3 minutes). On the **Locations** tab, **Add locations** opens a browser to choose one or more new
   parents. In a **Sub items** list, tick items and choose **More actions** > **Add a location**.
3. **The tab** (4 minutes). **Locations (n)** lists each place with its path (**Location**), its number of **Sub
   items** and which one is **Main**. Buttons: **Hide**, **Remove selected**, **Add locations**, **Set main**. The
   main location is the one used for the item's main address and, usually, its section.
4. **Removing a location** (3 minutes). Removing one location keeps the item in its other places; it goes to the
   trash only when its last location is removed.
5. **Visibility per location** (2 minutes). A location can be hidden on its own. In the edit form's **Locations**
   window, **Visibility after publishing** starts at **Unchanged** for existing locations, so publishing does not
   reveal a hidden location.

### Common questions

**When should I use a second location instead of a link?** When the item should be listed in both places, for
example in two menus or two lists. A link only points; a location makes the item a sub item of the second place.

**Which address does the item get?** Each location has its own address; the main location gives the item's main
address.

**Are sub items of the item also in the second place?** Yes: the item's own sub items appear below each of its
locations.

### Pitfalls

- **Removing the wrong location.** **Remove selected** on the **Locations** tab removes only the ticked places. Read
  the paths.
- **Expecting a copy.** Learners edit the item "in the second place" and are surprised the first changed too. Show it
  on purpose.
- **Permissions.** Adding a location needs permission to create in the new parent.

## Hands-on exercise

Use the training installation and your course folder.

1. In your course folder, create a subfolder `Projects` and publish it.
2. Open your article *Our team* (lesson 2.1) and its **Locations** tab. Write down its one location.
3. Press **Add locations**, choose `Projects` in the browser and confirm.
4. Read the **Locations** tab again: two locations, one marked **Main**. Write down both node numbers (open each
   location's path to see its node).
5. Open `Projects` and find *Our team* in its **Sub items**.
6. Edit *Our team* from `Projects`, change its title to `Our team, two places` and publish. Check its title in the
   course folder.
7. On the **Locations** tab, select the location in `Projects` and press **Set main**.
8. Tick the location in your course folder and press **Hide**. Check on the site that the article is still reachable
   through `Projects`.
9. Unhide it (item menu > **Advanced** > **Hide / unhide** on that location, or as the trainer shows).
10. Tick the location in `Projects` and press **Remove selected**; confirm. The article is still in your course
    folder and not in the trash.

## Expected result

- *Our team, two places* was visible in both folders with one text, then lost the `Projects` location in step 10.
- After step 7 the main location was in `Projects`; after step 10 the remaining location is the main one.
- The trash does not contain the article.
- The trainer checks by opening one learner's **Locations** tab on the projector.

## Quiz

1. What is the difference between a copy and a second location?
2. Name the four buttons of the **Locations** tab.
3. You remove one of an item's two locations. Where does the item go?
   A. To the trash B. Nowhere, it stays in its other location C. It is deleted D. It becomes hidden
4. What is the main location used for?
5. You edit an item through its second location. What changes in the first?

<details>
<summary>Answers</summary>

1. A copy is a separate item; a location is another place of the same item.
2. **Hide**, **Remove selected**, **Add locations**, **Set main**.
3. **B.** It goes to the trash only when its last location is removed
   ([Publishing, versions and the trash](../../../publishing-and-versions.md#remove-and-restore)).
4. The item's main address and, usually, its section.
5. The same change: it is one item.

</details>

## Further reading

- [Creating and editing content](../../../editing-content.md#many-items-at-once): **Add a location** in lists.
- [The content model and editing content](../../../../guides/content-model-and-editing.md#10-copying-locations-and-how-selections-are-shown):
  copying and the Locations window.
- [Sections](../../../../guides/sections.md#82-moving-and-locations): what locations do to sections.

## Related pages

- [Module 2: Relations, locations and embedding](README.md)
- [Editors 201: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
