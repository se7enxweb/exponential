# The users and groups of a role, a page at a time

This page is for administrators whose roles are given to many users, and for developers who override `role/view.tpl`.

A role can be assigned to thousands of users and user groups, for example one role for every member of an
association. `role/view` used to load every one of those assignments together with its content object before it drew
the page, which took long enough to make the page unusable. It now shows them a page at a time, sorted by name, with a
name filter, and the role list says how many each role has.

## What you see

On `role/view/<role id>`, below the policies:

- The heading counts every assignment of the role, a limited one (Subtree or Section) counted on its own.
- The list shows one page, sorted by the name of the user or group without regard to upper and lower case, then by
  the order the assignments were made. A pager below the list moves through the pages.
- A **Name contains** field (shown when there is more than one page, or a filter is set) keeps only the users and
  groups whose name contains the text, without regard to case. `%` and `_` match only themselves. **Show all**
  removes the filter. Pressing Enter in the field filters; it does not open the role for editing.
- An assignment whose user or group no longer exists (its content object was removed without the assignment) is
  listed first as *User or user group no longer exists (object 1234)*, and a note above the list counts them, so they
  can be selected and removed. Before, such a row was drawn as an empty link.
- A limitation shows the node or section it points to, with a link; a limitation whose node or section is gone says
  *not found* instead of an empty name.

The role list (`role/list`) has an **Assigned** column with the number of assignments of each role; it links to the
role.

The policies of the role keep their own pager and sort order. Paging or sorting one list keeps the other where it
is, and **Remove selected**, **Assign** and the filter come back to the same place.

## The setting

```ini
# settings/override/site.ini.append.php
[RoleSettings]
# How many assignments role/view shows at once (default 50). 0 lists all of them on one page.
AssignmentsPerPage=100
```

Clear the INI cache after the change (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`); on Exponential
Velocity run `exp:velocity deploy`.

The other page sizes of the role pages are in [Where the page sizes live](../../bc/6.0/pagination-settings.md).

## The address

| Parameter | Meaning |
|---|---|
| `(assignment_offset)/<n>` | The first assignment of the page, counted from 0. Past the end shows the last page; anything that is not a whole number shows the first. |
| `(assignment_filter)/<text>` | The name filter, URL-encoded. `/`, `(` and `)` in a filter become spaces (the address is split at `/`, and an unordered parameter starts with `(`); a filter is cut to 100 characters. |
| `(policy_offset)`, `(policy_sort)`, `(policy_dir)` | The policy list, as before. |

Example: `/role/view/5/(assignment_offset)/50/(assignment_filter)/editor`.

## Sorting on each database

The assignments of a page come from one query that joins the content objects, so the database sorts them, and the
pages of a role never drop or repeat an assignment (ties are broken by the assignment id). Assignments whose object is
gone come first, then those whose object has an empty name, then the others by name.

| Database | Upper and lower case of a-z | Accents, umlauts, punctuation |
|---|---|---|
| MySQL / MariaDB | together | the column's collation (`utf8mb4_general_ci`: "Müller" next to "Muller", `_` after the letters) |
| PostgreSQL | together | the database's collation |
| SQLite | together | by their bytes (`LOWER()` of SQLite lowers a-z only) |
| Oracle | together | the session's sort order; an empty name is stored as null and still sorts first |
| MongoDB | together | sorted in php by the bytes of the lowered name (`mb_strtolower`) |

The name sorted and filtered on is the one stored with the object (`ezcontentobject.name`, the name in its main
language). The list shows the name in the language of the admin, which is the same for nearly every user and group.

## Cost

A page costs the same few queries whatever its size and however many assignments the role has: the page of
assignments, their objects, their main nodes, the nodes of their Subtree limitations, and the counts. On alpha, a role
with 114 assignments took 106 queries to draw `role/view` before and takes 10 now, for a page of 50.

Nothing is kept between requests, so the pages behave the same on PHP-FPM and on Exponential Velocity's persistent
workers.

## For developers

| Method | What it returns |
|---|---|
| `eZRole::assignmentCount( $filter = '' )` | The number of assignments, or of those whose name contains `$filter`. |
| `eZRole::assignmentOrphanCount()` | The number of assignments whose content object is gone. |
| `eZRole::assignmentPage( $offset, $limit, $filter = '' )` | One page, in the form of `fetchUserByRole()` (`user_object`, `user_role_id`, `limit_ident`, `limit_value`) plus `user_id`, `user_name`, `main_node_id`, `limit_node` and `limit_section`. `user_object` is `null` for a gone object. |
| `eZRole::assignmentRows( $offset, $limit, $filter = '' )` | The same page as plain rows, without objects. |
| `eZRole::assignmentCounts( $roleIDs )` | Role id => number of assignments, in one query. |

`fetchUserByRole()` is unchanged and still returns every assignment with its object, for the permission checks and
whatever else needs all of them.

The templates get `$user_array` (one page), `$assignment_total` (every assignment), `$assignment_count` (those the
filter keeps; all without a filter), `$assignment_orphan_count`, `$assignment_limit`, `$assignment_offset`,
`$assignment_filter`, and the address parts `$policy_uri_suffix`, `$assignment_uri_suffix` and
`$assignment_filter_uri_suffix`. `role/list.tpl` gets `$assignment_counts`.

A design that overrides `role/view.tpl` shows only the first page until it adds a pager; see the
[behaviour changes](../../bc/6.0/behaviour-changes-2026-10.md). `design/admin/templates/role/view.tpl` is the
example to copy.

## Tests

- `tests/tests/kernel/classes/eZRoleAssignmentSortTest.php`: sorting and filtering in php, the filter in the address
  and in SQL, the address parts of the page. No database.
- `tests/tests/kernel/classes/eZRoleAssignmentPageLiveTest.php`: counts, pages, order, a gone object, the filter, the
  counts of the role list and the number of queries, on an installation (skipped without one).
