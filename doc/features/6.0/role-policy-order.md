# Roles: policy IDs, sorting and order buttons

A role can hold dozens of policies. On 30 September 2026 the role editor and the
role view gained policy IDs, sortable headings and, in the editor, buttons that
change the order of the policies.

## In the role editor

Open the **Users** tab, **Roles and policies**, then **Edit** on a role.

- Each policy shows its **ID**.
- Click the headings **ID**, **Module**, **Function** or **Limitations** to sort
  the policy list. Sorting is done in the database because the list is paged.
  Limitations sort by the first limitation identifier, then by the number of
  limitations (a join on SQL engines, PHP on MongoDB).
- In the role's own order (ID ascending, the default) each policy has **up** and
  **down** buttons. They also work across a page boundary. A move swaps the
  contents of a policy and its neighbour (module, function, limitations and any
  temporary copy the policy editor holds of either) and leaves both ids where they
  are.
- Like every change in the editor, a move is made in the temporary version of the
  role: **Save** keeps it, **Cancel** drops it.
- Pressing Enter in the name field now presses a hidden button that only keeps
  the name, not the first order button.

Sort state is carried in view parameters `(policy_sort)` and `(policy_dir)` next
to `(policy_offset)`. The form address and the pager keep all three, so a
button pressed on page three of a sorted list returns to page three, still
sorted.

## In the role view

The role view shows each policy's ID and sorts with the same headings. It only
reads, so it has no order buttons. The default is ID ascending, the role's own
order. The users and groups the role is assigned to are unchanged.

## Does the order change who may do what?

No. Each policy grants access by itself; the permission system does not depend on
the order. The role's access array, with the policy ids taken out, is identical
before and after a reorder and Save. The order is kept in the policy ids, so the
database schema is unchanged: opening a role for editing copies its policies in
list order, Save keeps those copies and Cancel deletes them, so the ids *are* the
order. For this to hold, `eZRole::policyList()` and `policyPage()` now return
policies in id order instead of by module and function.

## Related pages

- [Role and policy paging](../../bc/6.0/role-policy-paging.md)
- [Role and policy template operators](role-and-policy-template-operators.md)
- [Admin list paging](admin-list-paging.md)
- [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md)
- [Behaviour changes, 16 to 30 September 2026](../../bc/6.0/behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)

## Related pages

- [January 2026](../../history/2026/2026-01.md)
