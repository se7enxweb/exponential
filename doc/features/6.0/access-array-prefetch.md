# Faster access arrays for users with many role assignments

Read this page if your installation has many users with many role assignments, for example members of dozens or
hundreds of teamrooms whose member roles are assigned per teamroom subtree, and the first request of a user after a
role change or a cache clear is slow. It describes how the access array of a user is built now, what it gained, and
the setting that turns it off.

## In short

| | |
|---|---|
| What changed | The access array of a user is built from the rows of all their roles, loaded in three queries, and the limitations of a policy are turned into their part of the array once, not once per assignment. |
| Measured | A user with 600 subtree assignments (120 teamrooms × 5 member roles of 20 policies): 3,550 ms and about 48,600 queries before, 244 ms and 4 queries now (14.6×). |
| Result | The same access array: the same modules, functions, policies and limitations with the same values. |
| Setting | `site.ini [RoleSettings] AccessArrayPrefetch=enabled` (default); `disabled` builds it role by role as before. |
| Who must act | Nobody. |

## When the access array is built

`eZUser::hasAccessTo()` checks every policy against the access array of the user: all policies of all roles assigned
to the user and to the user's groups, each assignment with its limitation (Subtree, Section). The array is cached per
user (`[RoleSettings] EnableCaching`). It is built again for each user after a role or an assignment changes, after
the user cache is cleared, and when a user signs in for the first time. On an installation with 30,000 users, a change
of a member role means 30,000 rebuilds, one at the first request of each user.

## Why it was slow

A role assigned for 120 subtrees comes back from the database as 120 role objects, one per assignment, each with all
its policies. Building the array asked the database

- once for the roles of the user,
- once per assignment for the policies of its role,
- once per policy for its limitations,
- once per limitation for its values, and once more for the limitation the assignment adds,

and turned every row into an object. For the user above that was about 48,600 queries. On a database server reached
over the network every query is a round trip of some tenths of a millisecond, so the queries alone took 10 to 25
seconds there; locally on SQLite the whole build took 3.5 seconds.

On top of that, the parts were merged one by one with `array_merge_recursive()`, which copies the growing array for
every role: 74 ms of the 3.5 seconds for this user.

## What it does now

1. **Three queries.** `eZRole::accessArrayByUserID()` loads the policies of all the user's roles, their limitations
   and the values of those limitations in three queries (`eZRole::prefetchAccessRows()`). `eZRole::policyList()`,
   `eZPolicy::limitationList()` and `eZPolicyLimitation::valueList()` take their rows from there while the array is
   built, and ask the database as before at any other time.
2. **Once per policy.** The 120 assignments of a role differ only in the limitation the assignment adds
   (`User_Subtree`, `User_Section`), which `limitationList()` adds as the last limitation. So the part of the array
   the policy's own limitations make is computed once with `eZPolicyLimitation::limitArray()` and reused for every
   assignment; the assignment's limitation is added to it the same way (`eZPolicy::prefetchedAccessArray()`). A
   policy that has a limitation of the assignment's identifier itself goes the way of `limitationList()`, which
   narrows such a limitation.
3. **One merge.** The parts of a role and the roles of a user are merged in one `array_merge_recursive()` call, which
   gives the same array as merging them one by one. This also speeds up the build with `AccessArrayPrefetch=disabled`.

The rows are dropped when the array is built, also when building it fails. On MongoDB the array is built role by role.

## Measurements

Built 3 times each on a local installation (SQLite, PHP 8.4), for a user to whom 5 member roles of 20 to 22 policies
(2 to 4 limitations each, with 2 to 4 values) are assigned for 120 subtrees each, 600 assignments:

| Way | Time per build | Queries |
|---|---|---|
| Before: role by role, merged one by one | 3,550 ms | about 48,600 |
| Role by role, merged once (`AccessArrayPrefetch=disabled`) | 3,140 ms | about 48,600 |
| Rows ahead, limitations once per policy, merged once (`enabled`) | 244 ms | 4 |

Where the time of the build goes for this user, with the rows loaded ahead but the limitations still computed per
assignment: 2 ms for the 600 role objects, 3 ms for the three queries, 651 ms for building the parts of the 600
assignments (the step the reuse per policy removes), 74 ms for merging one by one (1.4 ms in one call).

For users with few roles the gain is smaller: the anonymous user of a demo installation (one role, 12 policies) needs
4 queries instead of 25 and is built 2.6 times as fast.

An alternative was tried and not taken: one SQL query joining roles, policies, limitations and values, with the
access array assembled from its rows by code of its own (as an older project patch did). It took 862 ms for the user
above, transferred about 67,800 rows, and gave a different access array as soon as a policy carried a limitation of
the assignment's identifier: a second implementation of the permission rules drifts from the first.

## The same access array

The tests compare both ways for every user and user group of an installation and for the cases above. The modules,
functions, policies and limitations come in the same order, with the same values. Only the order of the values
within one limitation may differ: built role by role it is the order the database returns them in (an index can sort
them by value), with the rows loaded ahead it is the order of their ids. Nothing depends on it: the values are
compared with `in_array()` and written into SQL `IN ()` lists.

## Settings

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `settings/site.ini` | `RoleSettings` | `AccessArrayPrefetch` | `enabled` | `disabled` builds the access array role by role, asking the database for each role, policy and limitation. |

## Tests

- `eZPolicyPrefetchedAccessArrayTest` (no database): the part of a policy computed once and reused gives the same
  array as `limitationList()` from the same rows, unlimited, with limitations, assigned for a subtree or a section, for
  a policy with a limitation of the assignment's identifier, and merging in one call gives what merging one by one
  gave.
- `eZRoleAccessArrayPrefetchLiveTest` (on an installation): both ways for every user and user group, with a role of its
  own assigned for 40 subtrees and for a section; the loaded rows are dropped afterwards.

## Related pages

- [Content policy limitations of extensions](content-limitation-handlers.md)
- [Velocity response cache](velocity-response-cache.md)
