---
title: "Lesson 2.3: Managing layouts: preview, copy, export and delete"
audience: trainer, editor, website-creator
category: training
order: 23
summary: Learners keep a growing list of layouts in order, preview a layout, base a new layout on an existing one, understand export and import, and delete a layout safely after checking its mappings. 45 minutes.
keywords: exponential layouts, layouts list, preview, copy layout, export, import, transfer, delete layout, mappings, sort, branching
type: help
---
# Lesson 2.3: Managing layouts: preview, copy, export and delete

A site soon has dozens of layouts. This lesson teaches the housekeeping: finding and previewing, basing a new layout on
an existing one, moving layouts between installations, and deleting only what nothing uses.

## At a glance

| | |
|---|---|
| Course | [Exponential Layouts](../README.md) |
| Module | [Module 2: Layouts and layout types](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Beginner |
| Audience | Editors and site builders |
| Prerequisites | [Lesson 2.2](creating-a-layout.md) |

## Learning objectives

After this lesson, learners can:

- sort the **Layouts** list and preview a layout with **Preview**;
- explain when to copy (branch) a layout instead of changing it;
- describe export and import as the way to move layouts between installations;
- check a layout's mappings before deleting it with **Delete**.

## Trainer's notes

### How to present it

Sources: [Exponential Layouts: Layout-level actions](../../../../bc/6.0/LAYOUTS.md#layout-level-actions),
[Import, export and share links](../../../../bc/6.0/LAYOUTS.md#import-export-and-share-links) and
[explayouts_ui](../../../../features/6.0/extensions/explayouts_ui.md).

1. **The list** (3 minutes). **Sort by** **Name**, **Description**, **Last modified**, **Layout type** or **Mappings**;
   **Order** ascending or descending. Each card shows the identifier, type, date and mappings, with **Open**,
   **Preview**, **Delete**.
2. **Preview** (2 minutes). Renders the published layout against a representative content item.
3. **Branching** (4 minutes). The layout actions include **Copy**, which duplicates a layout with its blocks. Rule of
   thumb from the reference: if a layout serves several sections and you want to change one, copy it, give the copy a
   clear identifier, and map the copy to that section with a higher priority. Branching avoids changing twenty pages
   while aiming at three. Show where your installation offers the copy action.
4. **Export and import** (3 minutes). **Export** produces a document describing a layout with its zones, blocks,
   parameters and collections; **Import** creates a layout from it. It is the supported way to move layouts from staging
   to production, and a backup before a large change. Identifiers travel with the export, so importing where the
   identifier exists needs care. The policies are **transfer_export** and **transfer_import**.
5. **Deleting** (3 minutes). **Delete** removes the layout entirely. Check first that no mapping points at it (the
   card's mapping count) and, for a shared layout, that no zone links to it.

### Common questions

**Is a deleted layout in the trash?** No, layouts are not content items. Export a layout first if you might need it
again.

**Can I preview a draft?** The preview screen takes the layout and its status in its address; the list's **Preview**
shows the published layout. The editor shows your draft as you work.

**Why do imported layouts not show?** An import brings layouts and, where included, rules; check the mappings and
publish state after an import.

### Pitfalls

- **Deleting a mapped layout.** Pages fall back to another rule or to the templates. Check the mapping count.
- **Copy without a new rule.** A copy is used nowhere until a rule maps it.
- **Import over an existing identifier.** Agree with the site builder before importing.

## Hands-on exercise

Use the training installation and your layouts training account.

1. Open **Layouts** > **Layouts**, set **Sort by** to **Mappings** and **Order** to **Descending**. Write down the
   layout with the most mappings.
2. Press **Preview** on it.
3. Find your own layout from lesson 2.2. Read its mapping count (0).
4. Find where your installation offers to copy a layout (in the editor or a list, as the trainer shows), and copy your
   layout; name the copy `Training - <your first name> - copy` with identifier `training_<your first name>_copy`.
5. Open **Export** in the left menu and read what it offers (do not export unless the trainer says so).
6. Open **Import** and read what it asks for. Do not import.
7. Back in **Layouts**, press **Delete** on your copy. If a confirmation appears, read it and confirm.
8. Check that your original layout is still listed.

## Expected result

- The learner found the most-mapped layout and previewed it.
- A copy of the learner's layout was created and deleted; the original remains.
- The learner can describe the export and import screens.
- The trainer checks the **Layouts** list on the projector: one layout per learner, no copies left.

## Quiz

1. How do you find the layouts that are used by the most mappings?
2. When should you copy a layout instead of changing it?
3. What does an export contain?
   A. Only the name B. The layout with its zones, blocks, parameters and collections C. The content items D. The
   templates
4. What do you check before deleting a layout?
5. True or false: a deleted layout can be restored from the trash.

<details>
<summary>Answers</summary>

1. Sort the **Layouts** list by **Mappings**, descending.
2. When it serves several pages or sections and you want to change only some
   ([Exponential Layouts](../../../../bc/6.0/LAYOUTS.md#walkthrough-a-customising-the-default-design)).
3. **B.**
4. That no mapping points at it and, for a shared layout, that no zone links to it.
5. **False.** Layouts are not content items; export first if in doubt.

</details>

## Further reading

- [Exponential Layouts: Import, export and share links](../../../../bc/6.0/LAYOUTS.md#import-export-and-share-links)
- [explayouts_ui: Preview a layout](../../../../features/6.0/extensions/explayouts_ui.md#preview-a-layout)

## Related pages

- [Module 2: Layouts and layout types](README.md)
- [Exponential Layouts: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
