# Class groups: finding classes and removing a group safely

This guide teaches the class pages of the administration interface (Setup > Classes, `/class/grouplist`, a group's classes, a class and its form):
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

## 4. The classes of a group

Open a group (its name or **Open**) for `/class/classlist/<group id>`:

- the group's ID and last change, with **Back to class groups**, **Edit** (rename) and **Remove** (the group's
  removal confirmation);
- figures: classes, their published objects, containers; a search over names and identifiers;
- **New class** creates a class in this group, in the language chosen beside it, and opens it for editing;
- one row per class: name and icon, identifier, ID, container, published objects, the other groups it is in
  ("only here" when none), the last change, and **View**, **Edit** and **Copy**. Copy makes a copy in the same groups
  (`content.ini [CopySettings] ClassRedirect` says where you land: the copy's edit form, the list, its page or the
  group list); it is a form button, so a copy is never made by following a link;
- **Remove selected** opens the confirmation: per class how many objects go with it and their sub items, or why it
  cannot be removed. A class that is also in another group only leaves this one.

## 5. A class page and the class form

The class page (`/class/view/<id>`) leads with **Edit** in the language chosen, then figures (objects, attributes,
groups, translations), the settings, and one card per attribute with its type, flags, category, description and
the settings of its type. The chips under it show or hide the class groups (add the class to a group, or take it
out of the ticked ones), the override templates and the translations (view, edit, set the main one, remove).

The class form keeps **OK**, **Apply** and **Cancel** and **Add attribute** in a bar at the top and again at the end.
**The class** holds name, identifier, description, object and URL alias name patterns, default sorting, container
and default availability, each with what it does. Each attribute is a card: order arrows and position, name,
identifier, description, category, the flags (required, searchable, information collector, disable translation;
those its type cannot have are greyed out) and the settings of its type. Nothing reaches the objects before **OK**;
**Cancel** throws the draft away and goes back to the page the form was opened from. A class in a language it does
not have yet first asks which language to add and which to start from.

## 6. Recently modified classes

Below the groups, the ten classes changed last, with ID, identifier, modifier, time, objects and **Edit**.

## References

- Module and views: `kernel/class/module.php`, `kernel/private/classes/views/class/grouplist.php`
  (`Grouplist::links()`, `classFacts()`, `overview()`, `summary()`), `removegroup.php`.
- Templates: `design/admin4/templates/class/grouplist.tpl`, `classlist.tpl`, `view.tpl` with `windows.tpl`, `window_controls.tpl`, `groups.tpl`, `translations.tpl`, `templates.tpl`, `edit.tpl`, `select_language.tpl`, `edit_denied.tpl`, `groupedit.tpl`, `removeclass.tpl`, `removegroup.tpl`, `removetranslation.tpl`, `exp_style.tpl`, `exp_list_script.tpl` (the same files
  in `design/admin/templates/class/`).
- Paging: `settings/admininterface.ini [PaginationSettings] ItemsPerPage[class/grouplist]`.
- Tests: `tests/tests/kernel/classes/expAdminListsRedesignTest.php` (no database).
