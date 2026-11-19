# Queries over id sets of any size: expDBInList and InListChunkSize

Read this page if you write kernel or extension code that asks the database for rows by a list of ids that grows with
the installation (the users of a role, the subscribers of a subtree, the objects of a removal), if you run Exponential
on Oracle, or if a page of a large installation failed with ORA-00913, ORA-01795, "MySQL server has gone away" or
"string or blob too big". It describes the helper `expDBInList`, the setting `site.ini [DatabaseSettings]
InListChunkSize`, the kernel code that uses them, and the places that were checked and left as they are.

## In short

| | |
|---|---|
| What changed | Kernel code that asks for a set of ids of unknown size asks in IN lists of at most 1,000 ids, one statement per list, and checks every result before reading rows from it. |
| Why | One statement with tens of thousands of ids fails: Oracle takes at most 1,000 values per IN list, MySQL and MariaDB refuse a statement past `max_allowed_packet`, SQLite one past `SQLITE_MAX_SQL_LENGTH` (1 MB). The failed result was then read as rows: `(array)false` is one row of its own. |
| Where it started | The role page of a role with tens of thousands of assignments on Oracle (ORA-00913), fixed in pull request #365 by Ekkehard Dörre. |
| For code | `expDBInList::rows()`, `column()`, `count()`, `query()`, `chunks()`, `condition()` (`lib/ezdb/classes/expdbinlist.php`). |
| Setting | `site.ini [DatabaseSettings] InListChunkSize=1000`, per engine `InListChunkSizes[<engine>]`; Oracle never more than 1,000. |
| Who must act | Nobody. Extension code with its own IN lists can switch to the helper. |

## Where it comes from

Ekkehard Dörre (CJW) found the fault on an Oracle installation and fixed it in
[pull request #365](https://github.com/se7enxweb/exponential/pull/365) of `se7enxweb/exponential`: `role/view` of a
role assigned to tens of thousands of users put every assigned id into one IN list. The Oracle driver splits a long
list into lists of 1,000 joined by OR, and Oracle still refused the statement (ORA-00913 in `arrayQuery()`). The
query returned false, `(array)false` counted as one row, every assigned id was taken for a user group, and the
locations of all of them were asked for in a second statement of the same size. His fix asks in lists of 1,000,
checks every result with `is_array()`, and reads the groups' locations only below the group limit. His test,
`expRolePageAffectedTest`, runs the page's counting against a database that records the statements, without a
database server; the tests of this work use the same pattern.

This page makes the lesson system-wide: the chunked query became a kernel helper, its size a setting, and the kernel
code with the same shape uses it. Thank you, Ekke.

## The limits

| Engine | What fails | Limit |
|---|---|---|
| Oracle | ORA-01795 (more than 1,000 values in one list), ORA-00913 (a statement of many OR-joined lists) | 1,000 values per list; `InListChunkSize` is capped at 1,000 |
| MySQL, MariaDB | "MySQL server has gone away", "Got a packet bigger than max_allowed_packet" | `max_allowed_packet` (4 MB to 64 MB by default) |
| SQLite | "string or blob too big" | `SQLITE_MAX_SQL_LENGTH`, 1,000,000 bytes by default |
| PostgreSQL | no hard limit for literals; planning time and memory grow with the list | |
| MongoDB (SQL fallback) | the statement is parsed and translated; long lists cost memory | |

A thousand ids are about 10 KB of SQL, far inside every limit, and a statement per thousand ids costs one round trip
each: 50 statements for 50,000 assigned users.

## The helper

`expDBInList` (in `lib/ezdb`, autoloaded with the kernel) runs `"<select> <column> IN ( ... ) <tail>"` once per list
and returns **false when any statement fails**, never a part of the rows or a count made from a failed result:

```php
$db = eZDB::instance();

// rows: the statement up to WHERE (or AND), the ids, the column, then 'int' or 'string', then what follows the list
$rows = expDBInList::rows( $db, 'SELECT contentobject_id FROM ezuser WHERE', $objectIDs, 'contentobject_id' );
if ( $rows === false )
    return $unknown;                       // a failed list: say so, do not count from it

// one field of every row
$ids = expDBInList::column( $db, 'SELECT node_id FROM ezcontentobject_tree WHERE', $objectIDs, 'contentobject_id',
                            'node_id', 'int', 'AND node_id = main_node_id' );

// COUNT(*) per list, summed
$n = expDBInList::count( $db, 'FROM ezcontentobject WHERE section_id <> 3 AND', $objectIDs, 'id' );

// UPDATE or DELETE per list; stops at the first that fails
expDBInList::query( $db, 'DELETE FROM ezuser_role WHERE', $objectIDs, 'contentobject_id' );

// strings are escaped and quoted by the helper
$rows = expDBInList::rows( $db, 'SELECT id, remote_id FROM ezcontentobject WHERE', $remoteIDs, 'remote_id', 'string' );

// code that builds its own statements: the lists and the condition of one list
foreach ( expDBInList::chunks( $nodeIDs, 'int', $db ) as $chunk )
    $sql = 'SELECT * FROM t WHERE ' . expDBInList::condition( $db, $chunk, 'node_id' );
```

Rules that follow from one statement per list:

- The values are made unique and cast (`(int)` or `(string)`) before they are split, so a row found by its value comes
  back once. No values: no statement, `array()` from `rows()` and `column()`, `0` from `count()`, `true` from `query()`.
- `ORDER BY`, `LIMIT`, `GROUP BY` and aggregates in the tail apply to each list on its own. Sort the rows afterwards;
  use `count()` (a sum of `COUNT(*)`) rather than `COUNT( DISTINCT ... )`. A query that pages or sorts over the whole
  set cannot be split this way (see [Left as they are](#left-as-they-are)).
- A write through `query()` is one statement per list and not atomic across them: run it in the caller's
  transaction when that matters.
- The condition is written by the driver's `generateSQLINStatement()`, so each engine writes it its own way.

`expDBInList::chunkSize( $db )` is the list size for a connection; `setChunkSize( $n )` fixes it for tests and scripts
and `setChunkSize( null )` goes back to the settings.

## The setting

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DatabaseSettings]
# at most this many values in one IN list (default 1000)
InListChunkSize=1000
# per engine, by the name the driver gives: mysql, postgresql, sqlite, oracle, mongo, pdo_<driver>
InListChunkSizes[]
InListChunkSizes[postgresql]=5000
*/ ?>
```

Oracle (`oracle`, `pdo_oci`) never takes more than 1,000, whatever is set. A value that is not a positive number counts
as the default. The setting is read once per process; [the generated settings page](../../examples/ini-settings/site/DatabaseSettings.md)
quotes it.

## The kernel code that uses it

| Code | What it asks for | Before |
|---|---|---|
| `expRolePage::affected()` (role/view) | the users among the assigned objects, the groups' locations | its own copy of the chunked query from #365 |
| `eZSubtreeNotificationRule::fetchUserList()` | the nodes, roles and rules of the subscribers of a subtree | one list per statement; a failed statement ended in a TypeError in `array_filter()` |
| `eZContentObject::fetchIDArray()` | objects by id, any number | one list; an empty list was a failing statement |
| `eZContentObjectTreeNode::findMainNodeArray()`, `fetchAliasesFromNodeList()`, `getParentNodeIdListByContentObjectID()` | main nodes, URL paths, parent nodes of any list | one list; the last looped over false |
| `ezpSessionHandlerDB::deleteByUserIDs()` | the sessions of the users of a bulk removal | one list; a failed read looped over false |
| `eZSearchEngine::updateObjectsSection()` | the search index rows of the objects that changed section | one UPDATE for every object a migration passed |
| Subtree copy: `eZContentObjectTreeNodeOperations::copySubtree()`, `content/copysubtree`, `bin/php/ezsubtreecopy.php` | the relations of every copied object | one list for the whole subtree (the command line has no size limit) |
| `TrashList::classOptions()` on MongoDB | the classes of every object in the trash | one list; a failed trash read became object 0 |
| `expUserApprovalSection::changes()` | the objects below a staff group that change section | one COUNT over every object of the subtree |
| `expStaticPublishQueue::nodes()` | the static pages of the nodes of a publish | one list of every node the cache clear names |

Behaviour, templates and return values are unchanged otherwise: a fetch that returned false on a failed statement
still does, one that looped over the failed result now returns an empty list.

## Left as they are

The kernel, `lib/` and the bundled kernel extensions were searched for IN lists built with `generateSQLINStatement()`
or `implode()` (about 100 and 120 lines). These were checked and left, with the reason:

| Kind | Places | Why it stays |
|---|---|---|
| Already in lists | content jobs (`expContentJob*`: batches of `[ContentJobSettings] BatchSize`, 50), `eZContentObjectTreeNode::assignSectionToSubTree()` (100 per statement), `eZContentObject::attributeListConditions()` (500 ids per list), help content repository, package comparison, audit indexer, review hold, notification event cleanup (100), content checks (200), basket and product collection garbage collection (one batch of `$limit`) | already bounded |
| One page or a fixed limit | `expRolePage` group locations (at most `AFFECTED_GROUP_LIMIT`, 200), `eZRole::assignmentPageFromRows()`, notification rule list, reverse relation check (`LIST_LIMIT`; the rest are subqueries), trash ancestors, unactivated users, user hygiene names, moderation list, session overview, subitems columns, ezjscore child priorities, content finder | the list is one page |
| The user's own memberships | `eZRole::fetchByUser()` and `fetchIDListByUser()`, `eZUser::limitList()` and `generateGroupIdList()`, discount rules, `expUserManager::roleAssignments()`, `expUserGroupManager`, debug bar user panel, the notification's path nodes | the groups and locations of one user, or the depth of one node |
| The schema | class, class group, section, state, language, status and identifier lists (content class filters, search filters, VAT rules, keyword class filters, state group removal, content limitations) | as many values as classes, sections or states exist |
| A filter in a sorted, paged or counted query | trash filter (`ContentObjectIDList`, trashed-by ids), tree fetch conditions, moderation filters | splitting would change the paging and the order; the size comes from a form |
| One object or one item | keywords and URLs of one attribute, collaboration participants of one item, bookmarks of one user, `expUserManager::administratorIDs()` (the accounts below the administrator groups) | small by nature |
| Demo data | `expCollaborationSampleData` | the generated sample only |

The `(array)$db->arrayQuery( ... )` pattern, which turns a failed query into one bogus row, occurs about 90 times
outside the places above, mostly in reports (content checks, package comparison, migration simulation) where one
failed statement already shows as an error. Those are left; new code checks the result with `is_array()` or uses
`expDBInList`, which never hands out a failed result.

## Tests

- `tests/tests/lib/ezdb/expDBInListTest.php`: lists of 1,000, 1,001 and 2,500 values, a failure in a middle list,
  no values, unique and quoted values, the tail, counts (also in Oracle's upper case), the settings per engine and
  the Oracle cap, writes.
- `tests/tests/kernel/classes/expDBInListCallersTest.php`: the object and node fetches, the search index's section
  update, the session removal, the subtree notification recipients and the trash classes on MongoDB, against a
  database that records the statements: lists of at most 1,000 ids, the conditions after each list, a failed list.
  The static publishing queue runs in `expStaticPublishSqliteTest` on SQLite; the subtree copy and the approval
  section count go through the same helper calls.
- `tests/tests/kernel/classes/expRolePageAffectedTest.php` (from #365): the role page's counts, chunk boundaries and
  failures.

All run without a database (`vendor/bin/phpunit tests/tests/lib/ezdb/expDBInListTest.php`).

## Related pages

- [Permission checks for users with many role assignments](permission-checks-for-many-assignments.md)
- [The users and groups of a role, a page at a time](role-assignment-paging.md)
- [SQLite database support](sqlite-database.md)
- [MongoDB as the database](mongodb-database-support.md)
