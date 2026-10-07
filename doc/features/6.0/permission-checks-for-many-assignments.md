# Permission checks for users with many role assignments

Read this page if your installation has users with many role assignments, for example members of dozens or hundreds
of teamrooms whose member roles are assigned per teamroom subtree, and their pages are slow or their content lists stay
empty. It describes what the permission checks of such a user cost before, what they do now, the measurements and the
setting that turns the shorter permission condition off.

## In short

| | |
|---|---|
| What changed | `eZContentObject::checkAccess()` and `canCreateClassList()` load the locations of an object once per call instead of once per policy. The content fetches merge the read policies that differ only in the subtree their role is assigned for and leave out those that cannot give access to a fetched node. |
| Measured | For a member of 120 teamrooms (1,203 read policies): checking read access to an article took 120 queries and 27.8 ms, now 1 query and 1.0 ms; the create menu of a folder 601 queries and about 100 ms, now 2 queries and 2 ms; listing the children of a folder 24.4 ms, now 1.2 ms. |
| Fixed | On SQLite every content fetch of a user with 1,000 read policies or more failed ("Expression tree is too large") and returned nothing. |
| Result | The same answers: `checkAccess()` and `canCreateClassList()` compare the same locations, the fetches return the same nodes. |
| Setting | `site.ini [RoleSettings] PermissionSQLOptimization=enabled` (default); `disabled` ORs every read policy into the fetch condition as before. |
| Who must act | Nobody. |

## Why it was slow

A role assigned for a subtree brings each of its policies once per assignment, with a `User_Subtree` limitation for
the subtree. A member of 120 teamrooms whose 5 member roles have 2 read policies each has 1,200 read policies, plus
those of the roles of all members. Three places worked through them one by one:

- **`checkAccess()`** compared each policy with the locations of the object and asked the database for these locations
  again for every policy with a `Subtree`, `User_Subtree` or `ParentDepth` limitation. It stops at the first policy
  that allows, so an article in the 120th teamroom cost 120 queries for one check. A full view checks read, edit,
  remove, move and create for the object and for each child it lists. For an object that was never published it loaded
  the parent nodes, and for a `Node` limitation the limitation's node, for every policy as well.
- **`canCreateClassList()`**, the classes the create menu of a container offers, asked `classListFromPolicy()` about
  every create policy, and that loaded the locations again for each: 601 queries for every container view.
- **The content fetches** (`fetch( 'content', 'list' )`, `tree`, counts, search, the content structure menu) ORed
  every read policy into the SQL condition: 1,203 groups of `class_id IN (...) AND path_string LIKE '/1/2/x/%'`, 146 KB
  of SQL for each fetch, and the database evaluated all of them for every candidate row. SQLite stops at an expression
  depth of 1,000, so from 1,000 policies on each fetch failed and the lists were empty.

On a database server reached over the network every query is a round trip of some tenths of a millisecond: the 120
queries of one access check cost 25 to 60 ms there, the 601 queries of a create menu 120 to 300 ms.

## What it does now

1. **Locations once per call.** `checkAccess()` keeps the locations of the object, the paths of the parent nodes of an
   unpublished object (`accessCheckParentPaths()`) and the main node of each `Node` limitation for all the policies it
   compares within the call. `canCreateClassList()` hands the locations from one `classListFromPolicy()` call to the
   next (its new third parameter, by reference). Nothing is kept beyond the call, so a location that changes later in
   the request is seen.
2. **Merged policies.** `eZContentObjectTreeNode::mergeLimitationList()` makes one policy of the policies whose
   limitations are equal except for `User_Subtree`, with all their subtrees: the policies of a list are ORed and so are
   the values of a limitation, so `(A AND subtree 1) OR (A AND subtree 2)` is `A AND (subtree 1 OR subtree 2)`. Equal
   subtrees are listed once. The 1,203 read policies of the member above become 5, the condition 13.7 KB. The last
   lists merged are remembered: the list of the current user arrives with every fetch, and comparing it with the same
   array costs nothing. `createPermissionCheckingSQL()` merges every list, so search, trash, the content structure menu
   and the fetches of extensions profit too.
3. **Only what can give access.** A fetch of a subtree or of children returns nodes under known paths.
   `pruneLimitationList()` keeps the `Subtree` and `User_Subtree` values that lie on the way to these paths or below
   them, and leaves out a policy none of whose values remains. With a class filter that includes classes, a policy for
   other classes only stays out; with one that excludes classes, a policy for these classes only. In a teamroom the
   condition shrinks to the policies of this teamroom: 432 bytes. The paths come from the condition the fetch builds
   anyway; a fetch of children (depth 1) asks for the path of its parent node in one query of its own, and only when
   the list has 20 subtree values or more (`PERMISSION_PATH_LOOKUP_MIN_VALUES`).

What the pruning never does:

- It never makes the condition empty, which would mean no condition at all: when no policy remains, the condition is
  `0 = 1` and the fetch finds nothing, as the full condition would.
- It keeps a policy whose `Subtree` lies outside but which has a `Node` limitation: `Node` and `Subtree` are
  alternatives, the node may lie in the fetched subtree.
- It keeps every value that is not a path of node ids, since SQL compares it with `LIKE` and a `%` or `_` in it could
  match more than its text.
- `subTreeMultiPaths()` builds the joins of the condition once for several parts, so it only merges and does not prune.

## Measurements

On a copy of a local installation (SQLite, PHP 8.4) with 120 teamrooms of 261 nodes each (a teamroom folder with 10
folders of 25 articles), 31,610 nodes in all. The test user has 5 member roles, each with 2 read, 2 edit, a create
and a remove policy, assigned for all 120 teamrooms; read gives 1,203 policies. The times are medians of 7 runs.

Access checks for one object:

| Check | Before | Now |
|---|---|---|
| `checkAccess( 'read' )`, article in the 1st teamroom | 1 query, 0.25 ms | 1 query, 0.30 ms |
| `checkAccess( 'read' )`, article in the 61st teamroom | 61 queries, 13.2 ms | 1 query, 0.65 ms |
| `checkAccess( 'read' )`, article in the 120th teamroom | 120 queries, 27.8 ms | 1 query, 1.0 ms |
| `canCreateClassList()`, a teamroom folder | 601 queries, 100 to 150 ms | 2 queries, about 2 ms |

Fetches with the 1,203 read policies: before, every one of them failed on SQLite. To compare with the condition of
before, the same fetches ran with the first 800 policies (3 of the member roles for all teamrooms, the fourth for
40 of them):

| Fetch | 800 policies, before | 800 policies, now | 1,203 policies, now |
|---|---|---|---|
| Children of a folder in a teamroom, 25 | 24.4 ms | 1.2 ms | 1.1 ms |
| Teamrooms under the content root | 30.9 ms | 6.4 ms | 7.5 ms |
| Articles of a teamroom (class filter) | 93.3 ms | 65.6 ms | 65.4 ms |
| A teamroom, the 20 newest | 98.1 ms | 98.6 ms | 72.8 ms |
| The whole tree, the 20 newest | 1,429 ms | 870 ms | 855 ms |

The fetches of a whole teamroom are dominated by the query itself (SQLite does not use an index for `LIKE`), not by
the permission condition. The whole tree with all teamrooms still evaluates 240 subtree conditions per row; nothing
can be left out there.

What the steps cost, for the 1,203 policies: merging 2.5 ms the first time and 0.0005 ms for each fetch after it,
pruning for a teamroom 0.16 ms, writing the condition 3.0 ms for all policies, 0.27 ms merged and 0.01 ms pruned, the
path lookup of a fetch of children 0.03 ms.

These are local numbers without a network. With MySQL or PostgreSQL on a server of its own the queries that are saved
weigh more, each a round trip; how the planner of each database treats a long OR condition was not measured here.

## Limits

The subtrees of one merged policy are ORed one after the other. A user with more than about 1,000 subtree assignments
of the same role still reaches the expression limit of SQLite in a fetch over the whole tree; within one subtree the
pruning keeps only the subtrees of that subtree.

## The original project patch

An older project patch had the same aims. It was not taken over as it was:

- It left out policies before the condition was built and returned an empty list when none remained. An empty list
  means no condition, so a user without access to a teamroom saw all of it.
- It left out a policy whose `Subtree` lay outside the fetched node even when the policy had a `Node` limitation,
  and so hid nodes the user may read.
- It remembered the shortened list per request by the first three levels of the path only: another user, another
  policy list or a `Limitation` parameter of a fetch in the same request got the list of the first.
- It kept the locations of every object for the rest of a `content/view` request in a global variable, which had to be
  switched off again where nodes are removed, and served old locations after a move in the same request.
- It only worked with a `define()` in `config.php` and for lists of 10 policies or more.

## Settings

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `settings/site.ini` | `RoleSettings` | `PermissionSQLOptimization` | `enabled` | `disabled` ORs every read policy into the condition of a fetch, as before. The locations in `checkAccess()` and `canCreateClassList()` are loaded once either way. |

## For extension authors

- `eZContentObjectTreeNode::createPermissionCheckingSQL()` takes an optional fourth parameter, the fetch scope of
  `permissionFetchScope()`. Without it the list is merged, not pruned.
- `createPathConditionAndNotEqParentSQLStrings()` returns the paths it loaded through an optional sixth parameter.
- `classListFromPolicy()` takes the locations of the object as an optional third parameter, by reference; called with
  two parameters it loads them as before.
- `mergeLimitationList()` and `pruneLimitationList()` are public and work without the database.

## Tests

- `eZContentPermissionSQLOptimizationTest` (no database): merging (only policies that differ in `User_Subtree`, equal
  subtrees once, the remembered list), 600 assignments giving the condition of three policies, the setting, pruning
  by path, by a `Node` next to a `Subtree`, by values that are no path and by class filter, and the condition no node
  meets when nothing remains.
- `eZContentPermissionSQLOptimizationLiveTest` (on an installation): a user whose role is assigned for 40 subtrees.
  `checkAccess()` and `canCreateClassList()` need at most 3 and 5 queries (41 and more before), and subtree, children,
  whole tree, class filter and multi-node fetches and their counts return the same nodes with the setting enabled
  and disabled.

## Related pages

- [Content policy limitations of extensions](content-limitation-handlers.md)
