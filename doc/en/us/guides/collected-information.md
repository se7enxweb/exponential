# Collected information: forms, polls and what visitors sent

This guide explains information collection in Exponential and the **Setup > Collected information** pages
(`/infocollector/overview`): which content collects, the overview with its figures, search, sorting and paging,
reading one form's collections, exporting them as CSV, removing them, and what to do when something does not behave
as expected.

It is written for administrators and editors who run forms. Every page, field and rule below was checked against
the code of this repository on 6 October 2026; the files are named in [References](#references).

[Guides](README.md) · Related: [The content model and editing content](content-model-and-editing.md) ·
[Security and audit](security-and-audit.md) · [List paging](../features/6.0/admin-list-paging.md)

## In short

- An object **collects** when its class has attributes marked **Information collector** (a feedback form, a poll,
  a lead form). Each time a visitor sends the form one **collection** is stored, with one value per collector
  attribute.
- **Setup > Collected information** lists the objects that have collected, with figures, a search by name, sorting
  and paging. Each card links to the object's collections, to the object and to a **CSV export**.
- When nothing (or little) has been collected, the page lists the published objects that **can** collect, so you know
  where to look.
- Removing deletes every collection of the ticked objects (or the ticked collections of one object) after a
  confirmation. The objects and their forms stay. Export first if the data is still needed.
- Everything needs the policy `infocollector/read`.

## 1. The overview

| Part | What it shows |
|---|---|
| Figures | Objects with collections, collections in all, collections in the last 30 days and the last 7 days, the date of the latest collection, and (red, only when there are any) objects that were removed or are in the trash whose collections are not listed |
| Find an object | Part of the object's name; **Update list** applies it, **Clear search** removes it. `%` and `_` match themselves. The search is kept in your session. |
| Sort by | Name (A to Z), collections, last collection, first collection (newest or largest first), type; selecting the current sort reverses it. The sort is in the address (`/(sortby)/collections/(order)/desc`). |
| The cards | Name (a link to the collections), class icon and name, the number of collections and how many came in the last 30 days, first and last collection, object ID; buttons **Collections**, **Export CSV**, **Object** |
| Per page and pages | The sizes of `admininterface.ini [PaginationSettings] ItemsPerPageList_infocollector_overview[]` (10, 25 and 50 when it is not set) and the pager |
| Objects that can collect but have nothing yet | Up to ten published objects of collector classes without collections, with their class and ID, shown on the first page when the list is shorter than a page |

On MongoDB the list is sorted by name and has no figures or search; everything else is the same.

## 2. Reading and exporting collections

**Collections** opens `infocollector/collectionlist/<object id>`: one row per sending, each opening
`infocollector/view/<collection id>` with every value.

**Export CSV** downloads `infocollector/export/<object id>` as `<name>-collected.csv` (UTF-8 with a byte order mark,
so spreadsheets read accents): the collection ID, when it was sent and changed (UTC, `2026-10-06 14:05:00 UTC`), the
ID of the user who sent it (the anonymous user for visitors), then one column per information collector attribute in
the order of the class. A value is its text, or its number when it has no text. Values a spreadsheet would read as a
formula (`=`, `+`, `-`, `@` at the start) begin with an apostrophe. At most 50 000 collections, newest first. Each
export is recorded in the audit as `data.export.csv` with the number of rows, never the values.

The export contains what visitors typed, often names and e-mail addresses. Keep the file where personal data belongs
and delete it when it is no longer needed.

## 3. Removing collections

On the overview, tick objects and choose **Remove selected**. The confirmation says how many collections go, lists
the objects with their counts, and says that the objects and their forms stay. **Remove** deletes; **Cancel**
changes nothing. The message after it says how many collections of how many objects were removed.

On one object's collection list, tick collections and choose **Remove selected**: the same confirmation, for those
collections only.

Removals are recorded in the audit as `data.infocollection.remove`. The button and field names are those of earlier
versions (`RemoveObjectCollectionButton`, `ObjectIDArray[]`, `ConfirmRemoveButton`, `CancelButton`), and every form
carries the form token.

## 4. Problems

| Symptom | Cause and fix |
|---|---|
| A form was sent but nothing appears | The class attribute is not marked **Information collector**, or the form template does not post to `content/action` with `ActionCollectInformation`. The object is then not among "Objects that can collect". |
| An object is missing from the list | It has no main location (removed, or in the trash). The red figure counts such objects; restore the object from the trash to see its collections again. |
| The CSV has an empty column | The attribute was made a collector after those collections were sent, or its values have no text and the number 0. |
| A column is missing from the CSV | The attribute is no longer a collector in the class; only current collector attributes are exported. |

## References

- Views: `kernel/private/classes/views/infocollector/overview.php` (`Overview`), `.../export.php` (`Export`),
  `.../collectionlist.php`, `.../view.php`; module definition `kernel/infocollector/module.php` (policy
  `infocollector/read`).
- Templates (design/admin and design/admin4): `infocollector/overview.tpl`, `infocollector/confirmremoval.tpl`,
  `infocollector/exp_style.tpl`.
- CSV cells: `kernel/classes/subitems/expsubitemscsvexport.php` (`cell()`, `writeLine()`).
- Settings: `admininterface.ini [PaginationSettings] ItemsPerPageList_infocollector_overview[]`.
- [Changelog 6.0.15](../changelogs/6.0/6.0.15.md)
