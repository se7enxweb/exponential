# Roles: policy IDs, sorting and order buttons

This page is for administrators who maintain roles with many policies. Since 30 September 2026 the role editor and the
role view show policy IDs and have sortable headings, and the editor has buttons that change the order of the
policies.

## Reorder policies in the role editor

1. Open the **Users** tab, **Roles and policies**, then **Edit** on a role.
2. Each policy shows its **ID**. Keep the default sort (ID ascending, the role's own order).
3. Use the **up** and **down** buttons next to a policy. They also work across a page boundary.
4. Click **Save** to keep the new order, or **Cancel** to drop it. Like every change in the editor, a move is made in
   the temporary version of the role.

What a move does: it swaps the contents of a policy and its neighbour (module, function, limitations, and any temporary
copy the policy editor holds of either) and leaves both ids where they are.

Pressing Enter in the name field presses a hidden button that only keeps the name, not the first order button.

## Sort the list

Click the headings **ID**, **Module**, **Function** or **Limitations**.

- Sorting is done in the database, because the list is paged.
- Limitations sort by the first limitation identifier, then by the number of limitations (a join on SQL engines, PHP
  on MongoDB).
- The sort is carried in the view parameters `(policy_sort)` and `(policy_dir)`, next to `(policy_offset)`. The form
  address and the pager keep all three, so a button pressed on page three of a sorted list returns to page three,
  still sorted.

The role view shows each policy's ID and sorts with the same headings. It only reads, so it has no order buttons. The
users and groups the role is assigned to are unchanged.

## Does the order change who may do what?

No. Each policy grants access by itself; the permission system does not depend on the order. The role's access array,
with the policy ids taken out, is identical before and after a reorder and Save.

The order is kept in the policy ids, so the database schema is unchanged. Opening a role for editing copies its
policies in list order, Save keeps those copies and Cancel deletes them, so the ids *are* the order. For this to hold,
`eZRole::policyList()` and `policyPage()` now return policies in id order instead of by module and function.

## Related pages

- [Role and policy paging](../../bc/6.0/role-policy-paging.md)
- [Role and policy template operators](role-and-policy-template-operators.md)
- [Object states](../../guides/object-states.md): the `StateGroup_<identifier>` and `NewState` limitations, with a worked review workflow
- [Admin list paging](admin-list-paging.md)
- [Behaviour changes, 16 to 30 September 2026](../../bc/6.0/behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
- History: [January 2026](../../history/2026/2026-01.md), [16 to 30 September 2026](../../history/2026/2026-09b.md)
