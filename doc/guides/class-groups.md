# Class groups: finding classes and removing a group safely

This guide teaches the class group page of the administration interface (Setup > Classes, `/class/grouplist`):
what a class group is, what every part of the page says, and how to remove a group without losing content you
wanted to keep.

It is for administrators who maintain the content model. Every figure below was checked against the code
(`kernel/private/classes/views/class/grouplist.php` and `removegroup.php`) on the demonstration server
(alpha.se7enx.com) on 6 October 2026.

[Guides](README.md) · Related: [The content model and editing content](content-model-and-editing.md),
[Admin list paging](../features/6.0/admin-list-paging.md)

## In short

- A **class** defines a kind of content. Classes are kept in **groups** (Content, Users, Media, Setup ...) so they
  are easy to find. A class can be in more than one group.
- The page shows an overview (groups, classes, published objects, classes in more than one group, classes in no
  group), a search over group names, class names and identifiers, and one card per group.
- Each card lists the group's classes with their number of published objects, when the group or one of its classes
  last changed, and **what removing the group would remove**.
- Removing a group removes **every class that is in no other group, with all its objects**. The card says how many
  before you tick it, and **Remove selected** asks first on the class/removegroup page.

## 1. The overview

| Figure | What it counts |
|---|---|
| Groups | Class groups |
| Classes | Defined classes |
| Published objects | Published objects of every class |
| In more than one group | Classes linked to two or more groups |
| In no group | Classes no group lists (shown only when there are any): they are reachable only by their ID or identifier |

## 2. A group card

The head shows the group's icon and name (a link to its class list), its ID, the number of classes and objects,
and **Open** and **Edit**. Below:

- the classes as chips, by name, each with its number of published objects and its identifier as a tooltip; a
  group with more than twelve says "and N more", linking to the group;
- **Last change**: the newest of the group's own change and its classes' changes, naming the class;
- **Group modified**: when and by whom the group itself was saved;
- **Removing it**: "Removes only the group" when every class is also in another group, or, in red when objects
  would go, "Removes N classes and their M objects", followed by how many classes stay in their other groups.

**New class group** creates a group. Classes are created inside a group: open it and use New class there.

## 3. Removing a group

1. Read the card's **Removing it** line.
2. A class you want to keep but that is only in this group: open it, edit it and add it to another group first.
3. Tick the group and press **Remove selected**. The confirmation page lists each class that will be removed with
   its number of objects. **OK** removes them; **Cancel** goes back.

Nothing on the list page removes anything by itself.

## 4. Recently modified classes

Below the groups, the ten classes changed last, with ID, identifier, modifier, time, objects and **Edit**.

## References

- Module and views: `kernel/class/module.php`, `kernel/private/classes/views/class/grouplist.php`
  (`Grouplist::links()`, `classFacts()`, `overview()`, `summary()`), `removegroup.php`.
- Templates: `design/admin4/templates/class/grouplist.tpl`, `exp_style.tpl`, `exp_list_script.tpl` (the same files
  in `design/admin/templates/class/`).
- Paging: `settings/admininterface.ini [PaginationSettings] ItemsPerPage[class/grouplist]`.
- Tests: `tests/tests/kernel/classes/expAdminListsRedesignTest.php` (no database).
