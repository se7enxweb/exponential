# Paging the role and policy screens

Read this page if your installation has roles with many policies (a large multi-site installation often does), if you
override the role or policy templates, or if your code reads `policyList()` and expects module order. The role and
policy screens now page and sort in the database, the way the [Locations tab](locations-tab-paging-and-sorting.md)
does, and the role editor can reorder policies.

A role may carry any number of policies, hundreds of thousands on a large installation. The screens that showed them
loaded every policy at once, and on a big role the page ran out of memory before the first row was written.

## In short

| | |
|---|---|
| What changed | `role/edit` and `role/view` show one page of policies (`site.ini [RoleSettings] PoliciesPerPage`, default `25`) with `(policy_offset)`, sortable ID, Module, Function and Limitations headings and, in the editor, up and down buttons. `role/list` has an ID column and sortable headings. The user policies window shows a bounded preview per role (`PolicyPreviewPerRole`, default `10`). New `eZRole::policyCount()`, `policyPage()`, `movePolicy()`; fetches `role/policy_count` and `role/policies`. |
| Who is affected | Code that relies on `policyList()` returning policies by module and function: it now returns them in id order. Overrides of `role/list.tpl`, `role/edit.tpl`, `role/view.tpl`, `policies.tpl` and `tabs/user/policies.tpl`. |
| How to check | `grep -n "function policyCount\|function policyPage\|function movePolicy\|function sortColumnsForPolicyList\|function sortColumnsForList" kernel/classes/ezrole.php` |
| How to fix | Sort in your own code if you need module order. Merge your template overrides with the shipped ones. No database update is needed: `ezpolicy` is unchanged. |

Permissions do not change: `eZRole::accessArray()` does not read the order (see
[The permission system does not read the order](#the-permission-system-does-not-read-the-order)).

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `site.ini` | `[RoleSettings]` | `PoliciesPerPage` | `25` | global or siteaccess |
| `site.ini` | `[RoleSettings]` | `PolicyPreviewPerRole` | `10` | global or siteaccess |
| `site.ini` | `[RoleSettings]` | `RolesPerPageList[]` | `10`, `25`, `50` (the role list selector; the stored preference is the position in this list) | global or siteaccess |

## What was wrong

### `role/edit` and `role/view`

Both did this:

```php
$policies = $role->attribute( 'policies' );
```

`policyList()` fetches **every** policy the role has, with no limit, and keeps
them on the object. That is right for the permission system — it has to see all
of them to answer a question — and wrong for a screen, which shows twenty five.
On a role carrying policies in the millions the page exhausts memory before the
first row is written.

The heading then called `$policies|count`, which is free only because the whole
list was already in hand — the cost that had to go.

### `role/list`

Already paged its roles, in the database — `fetchByOffset()` with a real
`LIMIT`, and `roleCount()` a real `COUNT(*)`. It also has a 10/25/50 per-page
selector, kept as a user preference. What it did not have was any way to order
the list, or the role id anywhere on screen.

It also ran, on every view:

```php
$tempRoles = eZRole::fetchList( $temporaryVersions = true );
$tpl->setVariable( 'temp_roles', $tempRoles );
```

That is one row per role anybody has open in the editor, unbounded, each loaded
with its policies — and **no template has ever used `$temp_roles`**. Removed.

### The policies window on a user or user group

`policies.tpl` was the worst of them:

```
{let assigned_policies=fetch( user, member_of ... )
     assigned_policies=fetch( user, user_role, hash( user_id, ... ) )}
```

`fetch( user, user_role )` builds the user's **entire access array** in PHP by
merging `accessArray()` from every role they hold — and it was asked for only
to put a number in the heading. Then, for every assigned role, the template
looped over **every** policy of that role, and each row asked the database for
its limitations and resolved their value names: unbounded × unbounded, with an
N+1 inside.


## What it does now

### `eZRole::policyCount()` and `eZRole::policyPage()`

```php
$role->policyCount();                 // SELECT COUNT(*), no rows loaded
$role->policyPage( $offset, $limit ); // one page, in the query
```

`policyPage()` deliberately does **not** store its result in `$this->Policies`.
That property is the whole list as far as every other caller is concerned, and
a page left there would be silently wrong for all of them.

`policyList()` gained `id` as a final sort key. Without it two policies of the
same module and function are ordered by whatever the database returns, which is
free to differ between queries — and a paged view of that drops and repeats
rows as you page through.

### Fetch functions

```
{fetch( 'role', 'policy_count', hash( 'role_id', 17 ) )}
{fetch( 'role', 'policies', hash( 'role_id', 17, 'offset', 0, 'limit', 25 ) )}
```

### The role list: an ID column, and sortable headings

The id is what the rest of the interface addresses a role by — `/role/view/17`,
`/role/edit/17` — and it was nowhere on the page that lists them. It is now the
second column, and both it and Name sort:

```
/role/list/(sort)/id/(dir)/desc
```

Sorting is done by the database, for the same reason as everywhere else here:
the list is shown a page at a time, so reordering the rows on screen would sort
ten of however many there are. The column is checked against
`eZRole::sortColumnsForList()`, so the value can come straight off the address;
anything else sorts by name, which is what this has always done. `id` is the
final sort key, so two roles of the same name keep a stable order and paging
cannot drop or repeat one.

The headings are `parts/sortheader.tpl` — the same component the RSS list and
the locations tab use.

#### A trap in eZPersistentObject

`fetchObjectList()` decides the direction with:

```php
if ( $sort_type == "desc" )
```

That is case sensitive. `array( 'name' => 'DESC' )` is therefore **ascending**,
silently, with no error and no warning — which is exactly what the first
version of this did, marking the heading as descending while the rows came back
ascending. The sort direction is now lower-cased before it is handed over.

Worth knowing before writing any other sorted `fetchObjectList()` call.

### The screens

`role/edit` and `role/view` fetch one page and a count, and carry the offset as
**`(policy_offset)`** — not `(offset)`, so a second list added to either page
later does not move with it:

```
/role/view/17/(policy_offset)/25
```

The page size is `site.ini`:

```ini
[RoleSettings]
PoliciesPerPage=25
PolicyPreviewPerRole=10
```

#### One trap worth naming

Editing a role works on a **temporary version**, which is a row of its own with
an id of its own. The first version of the pager built its links from
`$role.id` and so pointed at `/role/edit/20` while the page was `/role/edit/17`.
That is not cosmetic: `/role/edit/<the draft>` edits the draft directly, and
Apply would then write back to the wrong row. The page uri is now passed in
from PHP, built from the id in the address.

### The policies window

Each role shows its first `PolicyPreviewPerRole` policies and then a line
saying how many more there are, linking to the role's own page — which is
paged. The heading's count comes from `policy_count` per role, not from
building the access array.

That makes the window bounded by *roles × preview size* rather than by
*roles × policies*.


## Measured

Role 17, 401 policies:

| | before | after |
|---|---|---|
| `role/edit/17` | all 401 rows drawn | 25 rows, **0.5 s** |
| `role/view/17` | all 401 rows drawn | 25 rows, **0.5 s** |
| heading | counted the list in hand | `Policies (401)` from the database |
| pager | none | `/role/view/17/(policy_offset)/25` |


## Files

| File | |
|---|---|
| `kernel/classes/ezrole.php` | `policyCount()`, `policyPage()`, stable sort |
| `kernel/role/ezrolefunctioncollection.php` | `fetchRolePolicies()`, `fetchRolePolicyCount()` |
| `kernel/role/function_definition.php` | `policies`, `policy_count` |
| `kernel/role/edit.php`, `kernel/role/view.php` | entry points; the code is in `kernel/private/classes/views/role/edit.php` and `view.php` (one page, a count, `policy_page_uri`) |
| `kernel/role/list.php` (code in `kernel/private/classes/views/role/list.php`) | sorting, and the unused unbounded temp-role fetch removed |
| `design/admin/templates/role/list.tpl` | the ID column and sortable headings |
| `design/admin/templates/role/edit.tpl`, `role/view.tpl` | pager, count from the database |
| `design/admin/templates/policies.tpl` | bounded preview per role |
| `settings/site.ini` | `PoliciesPerPage`, `PolicyPreviewPerRole` |


## Tests

Private functional scripts exercised these screens against a role with 401 policies and against 26 roles; they are
not shipped. The behaviours they checked, which you can check by hand on a copy of the installation:

- `role/edit/<id>` and `role/view/<id>` render one page, the pager exists and uses `(policy_offset)`, the address keeps
  the role id rather than the draft's, the heading counts all policies and the last page loads.
- `role/list` renders one page of ten with a pager on `(offset)`, the per-page selector is intact, both headings sort,
  each column sorts both ways, the sorted one is marked, the pager carries the sort, and a sort value that is not a
  column is ignored.

To make many policies for a hand test, create a throwaway role (never add thousands of policies to the Administrator
role: that edits the permissions of the account everything runs as) and remove it afterwards.

Read-only check that the API is in place: `grep -n "function policyCount\|function policyPage\|function movePolicy\|function sortColumnsForPolicyList\|function sortColumnsForList" kernel/classes/ezrole.php`.


## The policy order, an ID column and sortable headings in `role/edit`

The policy list of the role editor shows each policy's **ID**, sorts by the
**ID**, **Module**, **Function** and **Limitations** headings, and has **up and
down buttons** that change the order of the policies.

### Where the order is kept: the ids, with no new column

A role's policies are in the order of their ids. `ezpolicy` did not change:
there is no order column, and no upgrade script is needed.

That works because of how the editor already works. `role/edit` edits a
temporary version of the role, whose policies are fresh copies made in the
order `policyList()` returns; Save moves those rows onto the role, and Cancel
deletes them. So the ids are the order, and the editor rewrites them anyway.

`eZRole::movePolicy( $policyID, 'up'|'down' )` swaps the **contents** of a policy
and its neighbour: module, function, limitations
(`ezpolicy_limitation.policy_id`), and any temporary copy the policy editor has
of either (`original_id`). Both ids stay where they are. The move goes into the
temporary version, so Save keeps it and Cancel drops it, like every other change
in the editor. A policy id that is not this role's is refused.

**What callers see change:** `policyList()` and `policyPage()` now return the
policies in id order, not by module and function. If they did not, the next
temporary copy would put the list back in alphabetical order and lose the order
set in the editor. `role/view` shows the same order.

The IDs shown in the editor belong to the temporary version: they are the ids
the policies will have after Save. That has always been true. Every save of a
role has given its policies new ids.

### The permission system does not read the order

`eZRole::accessArray()` merges the policies into
`module => function => p_<id> => limitations`. Each policy grants access by
itself, and a check succeeds when any of them matches, so the order and the ids
change nothing about who may do what. Checked on a sandbox copy: the role's
access array, with the policy ids taken out, has the same fingerprint before and
after a reorder and Save.

### Sorting

The sorting is done by the database, as on `role/list`, because the list is
shown a page at a time. It is carried as `(policy_sort)` / `(policy_dir)`, next
to `(policy_offset)`:

```
/role/edit/1/(policy_sort)/module/(policy_dir)/desc
```

The form's address carries the offset and the sort, so a button pressed on
page 3 of a sorted list comes back to page 3, still sorted. The pager keeps the
sort too. `eZRole::sortColumnsForPolicyList()` is the whitelist: `id`,
`module`, `function`, `limitation`. `limitation` sorts by the policy's first
limitation identifier, then by how many limitations it has. Policies without a
limitation come first. SQL engines do this with one `LEFT JOIN … GROUP BY`
query. MongoDB, which has no join here, sorts in php.

The up and down buttons are offered only in the role's own order, which is ID
ascending, the default. Under any other sort they are greyed out, and their
title says to sort by ID. There is no up button on the first policy and no down
button on the last one. On a paged list they work across the page boundary.

`role/view` has the same **ID** column and the same four sortable headings,
with the same `(policy_sort)` / `(policy_dir)` parameters. Its pager keeps the
sort. It has no order buttons, because it only reads. Its other sections, such as
the users and groups the role is assigned to, are unchanged.

Pressing Enter in the name field used to press the form's first submit button.
With the new buttons, that would have moved a policy. A hidden `ChangeRoleName`
button now comes first in the form, so Enter only keeps the name.


## Two copies of the policy window, and only one of them renders

`design/admin/templates/policies.tpl` and `roles.tpl` are included only by
`design/admin/override/templates/windows_user.tpl`, and **no shipped `override.ini`
registers that file**. Unless your own override registers it, they are not used.

The ones that actually render are `design/admin/templates/tabs/user/policies.tpl`
and `roles.tpl` — additional tabs, declared in `admininterface.ini` and pulled
in through `window_controls.tpl`. They carried the same unbounded shape, and
that is the copy the fix has to be in. Both are now bounded; the dead pair is
left consistent with it rather than left behind.

The engine settles which is which. `Number of unique templates used` in the
debug report, on a **cold** cache, lists every template that rendered — 64 of
them for a user group node view, `locations.tpl` and `tabs/user/policies.tpl`
among them. On a warm cache the same page reports three, because only what was
rendered fresh is listed: a cached page is not evidence of what a page uses.


## Related pages

- [Paging, sorting and page sizes](../../features/6.0/admin-list-paging.md) and [Where the page sizes live](pagination-settings.md)
- [Role and policy order](../../features/6.0/role-policy-order.md) and [Role and policy template operators](../../features/6.0/role-and-policy-template-operators.md)
- [The locations tab: paging and sorting](locations-tab-paging-and-sorting.md)
- [September 2026, first half: 15 September](../../history/2026/2026-09a.md#15-september-paging-everywhere)
- [January 2026](../../history/2026/2026-01.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
