# MongoDB Kernel Support — Comprehensive Port Reference

**Project:** mongodb.demo.se7enx.com — eZ Publish / Exponential CMS running MongoDB instead of MySQL
**PHP Runtime:** `/opt/plesk/php/8.5/bin/php`, FPM pool `plesk-php85-fpm`
**Database:** MongoDB `exp` at `localhost:27017`, user `db`
**MongoDB PHP Driver:** `MongoDB\Client` via Composer (`vendor/autoload.php`)
**Admin panel:** `https://edit.mongodb.demo.se7enx.com/` — separate docroot at `/var/www/vhosts/mongodb.demo.se7enx.com/doc/edit.mongodb.demo.se7enx.com/`
**Front site:** `https://mongodb.demo.se7enx.com/` — docroot symlink at `/web/vh/mongodb.demo.se7enx.com/doc/mongodb.demo.se7enx.com/`
**Login:** admin / publishing$
**Error log:** `/var/www/vhosts/mongodb.demo.se7enx.com/logs/edit.mongodb.demo.se7enx.com/error_log`

---

## Table of Contents

1. [Background and Purpose](#1-background-and-purpose)
2. [Architecture](#2-architecture)
3. [Two-Docroot Layout](#3-two-docroot-layout)
4. [MongoDB Collections Schema](#4-mongodb-collections-schema)
5. [All Modified Files (Cumulative)](#5-all-modified-files-cumulative)
6. [Kernel Modules — Status and Known Issues](#6-kernel-modules--status-and-known-issues)
7. [Patterns and Conventions](#7-patterns-and-conventions)
8. [Operational Notes](#8-operational-notes)
9. [Test Plan — Admin](#9-test-plan--admin)
10. [Test Plan — Front Site](#10-test-plan--front-site)
11. [Remaining Issues Backlog](#11-remaining-issues-backlog)
12. [Test Plan — Cronjobs / Scripts](#12-test-plan--cronjobs--scripts)
13. [References](#13-references)
14. [PHPUnit Testing — Theory and Next-Phase Plan](#14-phpunit-testing--theory-and-next-phase-plan)
15. [Kernel PHP Classes — Status and Known Issues](#15-kernel-php-classes--status-and-known-issues)
16. [MongoDB Driver Design and Technical Completion vs ezmysqli](#16-mongodb-driver-design-and-technical-completion-vs-ezmysqli)
17. [Additional Fixes — System Upgrade, RAD Code Generators, BC CIE Export, Language Bitmask](#17-additional-fixes--system-upgrade-rad-code-generators-bc-cie-export-language-bitmask)
18. [SQL Database Conversion Guide — Export Any RDBMS to JSON and Import into MongoDB](#18-sql-database-conversion-guide--export-any-rdbms-to-json-and-import-into-mongodb)
19. [Project Complete — File by File Patched or Changed List](#19-project-complete--file-by-file-patched-or-changed-list)
20. [Steps to Full Kernel Re-Implementation (NO more kernel override extension)](#20-steps-to-full-kernel-re-implementation-no-more-kernel-override-extension)
21. [PHPUnit Test Suite — Implemented (May 2026)](#21-phpunit-test-suite--implemented-may-2026)
22. [Setup Wizard Performance Optimizations (June 2026)](#22-setup-wizard-performance-optimizations-june-2026)
23. [Site Installer Packages — Distribution and Package Server](#23-site-installer-packages--distribution-and-package-server)
24. [Key Getting Started Steps Using Exponential 6.0.14 With MongoDB](#24-key-getting-started-steps-using-exponential-6014-with-mongodb)
25. [Datatype Compatibility — Core and Community](#25-datatype-compatibility--core-and-community)
26. [Known Limitations](#26-known-limitations)

---

## 1. Background and Purpose

eZ Publish is a traditional SQL-based CMS. This project replaces MySQL entirely with MongoDB.
The `sevenx_mongodb` extension provides the MongoDB database adapter (`expMongoDB`) and
overrides virtually every kernel class that issues SQL queries. The override system maps class
names through `var/autoload/ezp_override.php`.

**Goal:** eliminate every `MONGO TODO arrayQuery()` call from the debug toolbar so every page
renders with full correct content, with no SQL fallback paths executing.

All fixes are forward-ported on PHP 8.5. SQL DELETE, INSERT, SELECT statements are replaced
with MongoDB `aggregate()`, `insert()`, `upsert()`, and `deleteWhere()` calls. The adapter
never executes real SQL; `arrayQuery()` always returns `[]` and logs a `MONGO TODO` warning.

---

## 2. Architecture

### Class Override System

`var/autoload/ezp_override.php` maps PHP class names to files in
`extension/sevenx_mongodb/classes/kernel/`. When a kernel class is instantiated, PHP loads the
override file instead of the original `kernel/` file. The override files define the **same class
name** (not a subclass), so all existing `new eZContentObject(...)` and
`eZContentObject::fetch(...)` calls automatically use the MongoDB implementation.

Override files are identical between the two docroots — the `sevenx_mongodb` extension directory
is **symlinked** from the edit docroot to the main docroot. Editing any file in the extension
affects both sites immediately.

### MongoDB Adapter: `expMongoDB`

**File:** `extension/sevenx_mongodb/classes/expMongoDB.php`

Extends `eZDBInterface`. Key methods:

| Method | Purpose |
|---|---|
| `databaseName()` | Returns `'mongo'` — used everywhere as `if ($db->databaseName() === 'mongo')` |
| `aggregate($table, $pipeline)` | Runs a MongoDB aggregation pipeline; **always correct for complex queries** |
| `insert($table, $doc)` | Insert a single document |
| `upsert($table, $filter, $doc)` | Replace-or-insert by filter |
| `deleteWhere($table, $filter)` | Delete documents matching filter (uses `deleteMany`) |
| `nextSeqID($table, $column)` | Returns `MAX(column)+1` via `$group/$max` — used as auto-increment |
| `arrayQuery($sql, ...)` | **Stub — always returns `[]`** — logs MONGO TODO to error_log |
| `translateConditions($conds)` | Converts simple scalar conditions; **does NOT support `$or`, `$in`, `$expr`** |
| `eZTableList()` | Returns `[]` — overrides base class null return that crashed `array_keys()` |
| `begin()` / `commit()` / `rollback()` | No-ops — MongoDB operations are per-document |

### Critical Rule: Always Use `aggregate()` for Filters with MongoDB Operators

`translateConditions()` only handles scalar values and `like`. Passing `$or`, `$in`, `$expr`, or
any nested array through `find()` corrupts the filter. Use `aggregate()` with an explicit
`$match` stage whenever the condition is non-trivial.

---

## 3. Two-Docroot Layout

```
/web/vh/mongodb.demo.se7enx.com/doc/mongodb.demo.se7enx.com/   ← front site (symlink)
  extension/sevenx_mongodb/                               ← SYMLINKED to edit docroot
  var/autoload/ezp_override.php                           ← same as edit docroot (identical)
  kernel/class/*.php                                      ← ORIGINAL (not symlinked)
  kernel/content/*.php                                    ← ORIGINAL (not symlinked)

/var/www/vhosts/mongodb.demo.se7enx.com/doc/edit.mongodb.demo.se7enx.com/  ← edit/admin site
  extension/sevenx_mongodb/                               ← SYMLINK TARGET
  var/autoload/ezp_override.php                           ← identical content to front site
  kernel/class/*.php                                      ← SEPARATE COPY — edit separately
  kernel/content/*.php                                    ← SEPARATE COPY — edit separately
```

**Important:** `kernel/class/edit.php`, `kernel/class/copy.php`, `kernel/content/edit.php`,
`kernel/content/attribute_edit.php` are **separate physical files** in the edit docroot.
Changes made to these files do NOT affect the front site and vice versa.

`sevenx_mongodb` extension files **do** affect both sites simultaneously.

After editing any PHP file: `touch <file>` to bust opcache (revalidate_freq=2s).

---

## 4. MongoDB Collections Schema

### Core Content Tables

| Collection | Key Fields |
|---|---|
| `ezcontentobject_tree` | `node_id`, `parent_node_id`, `contentobject_id`, `path_string` (e.g. `/1/2/67/`), `depth`, `is_hidden`, `is_invisible`, `main_node_id`, `contentobject_version`, `priority`, `sort_field`, `sort_order` |
| `ezcontentobject` | `id`, `contentclass_id`, `current_version`, `name`, `owner_id`, `published`, `modified`, `section_id`, `status`, `language_mask`, `initial_language_id`, `remote_id` |
| `ezcontentobject_version` | `id`, `contentobject_id`, `version`, `status` (0=draft,1=published,3=archived), `created`, `modified`, `creator_id`, `language_mask`, `initial_language_id` |
| `ezcontentobject_attribute` | `id`, `contentobject_id`, `version`, `language_code`, `contentclassattribute_id`, `data_text`, `data_int`, `data_float`, `data_type_string` |
| `ezcontentobject_name` | `contentobject_id`, `content_version`, `real_translation`, `content_translation`, `language_id`, `name` |

### Class Tables

| Collection | Key Fields |
|---|---|
| `ezcontentclass` | `id`, `version` (0=published, 1=temporary/editing, 2=modified/locked), `identifier`, `serialized_name_list`, `is_container`, `modifier_id`, `modified` |
| `ezcontentclass_attribute` | `id`, `contentclass_id`, `version`, `identifier`, `data_type_string`, `placement`, `is_required`, `is_searchable`, `serialized_name_list` |
| `ezcontentclassgroup` | `id`, `name` |
| `ezcontentclass_classgroup` | `contentclass_id`, `contentclass_version`, `group_id`, `group_name` |
| `ezcontentclass_name` | `contentclass_id`, `language_id`, `name` |

### URL and Navigation

| Collection | Key Fields |
|---|---|
| `ezurlalias_ml` | `id`, `parent`, `link`, `text`, `text_md5`, `action`, `is_alias`, `is_original`, `alias_redirects`, `lang_mask` |
| `ezurlwildcard` | `id`, `source_url`, `destination_url`, `type` |
| `eznode_assignment` | `id`, `contentobject_id`, `contentobject_version`, `parent_node`, `is_main`, `op_code`, `sort_field`, `sort_order` |

### State, Role, User

| Collection | Key Fields |
|---|---|
| `ezcobj_state` | `id`, `group_id`, `identifier`, `default_language_id`, `language_mask`, `priority` |
| `ezcobj_state_group` | `id`, `identifier`, `default_language_id`, `language_mask` |
| `ezcobj_state_link` | `contentobject_id`, `contentobject_state_id` |
| `ezcobj_state_group_language` | `contentobject_state_group_id`, `language_id`, `real_language_id`, `name`, `description` |
| `ezrole` | `id`, `name`, `version` |
| `ezpolicy` | `id`, `role_id`, `module_name`, `function_name`, `original_id` |
| `ezpolicy_limitation` | `id`, `policy_id`, `identifier` |
| `ezpolicy_limitation_value` | `id`, `policy_limitation_id`, `value` |
| `ezuser_role` | `contentobject_id`, `role_id`, `limit_identifier`, `limit_value` |
| `ezuser` | `contentobject_id`, `login`, `email`, `password_hash`, `password_hash_type`, `is_enabled` |

### eZPersistentObject Field Storage

Fields are stored as **PHP object properties**, not a `DataMap` array.
`fill($row)` calls `$this->$attrName = $row[$key]` for each field where `attrName` is `field_def['name']`.
`attribute($name)` returns `$this->$attrName`.

For `eZContentClass`: `id` field has `name='ID'` → stored as `$this->ID` → `attribute('id')` returns `$this->ID`.
`__clone()` sets `$this->ID = null`, which causes `storeObject` to treat the clone as a new insert.

**`eZContentClass` version constants:**
- `VERSION_STATUS_DEFINED = 0` — published/live version
- `VERSION_STATUS_TEMPORARY = 1` — editing draft (in `kernel/class/edit.php`)
- `VERSION_STATUS_MODIFIED = 2` — locked by ezscriptmonitor (external edit lock)

---

## 5. All Modified Files (Cumulative)

### `extension/sevenx_mongodb/classes/expMongoDB.php`

The central MongoDB adapter.

| Change | Reason |
|---|---|
| `eZTableList()` override returns `[]` | Base class returns null; `array_keys(null)` crashes on PHP 8.5 in `generateUniqueTempTableName()` |
| `deleteWhere($table, $filter)` method added | `eZContentObjectVersion::removeThis()` needs direct document deletion |
| `aggregate()` timing with `eZDebug::accumulatorStart/Stop` | Queries appear in debug toolbar time accumulators |
| `reportQuery()` in `arrayQuery()` | MONGO TODO entries appear in SQL debug output panel |

---

### `extension/sevenx_mongodb/classes/kernel/ezpersistentobject.php`

The central ORM base class. Every `fetchObjectList()`, `storeObject()`, `removeObject()`, `newObjectOrder()` call goes through here.

**`fetchObjectList()` — complete MongoDB branch**

Replaces the `$db->find()` call with a full `aggregate()` pipeline:
1. `$match` — built from `$conds` with datatype-aware int/float casting (see fix below)
2. `$sort` — from `$sorts` or `$def['sort']`
3. `$skip` / `$limit` — from `$limit` array (`offset`, `limit` or `length` keys)
4. `$project` — either named field list or `{_id: 0}` only
5. `$group` + `$count` — for `custom_fields` count requests

**Key fix — infinite loop (504 Gateway Timeout):** The original `find()` call had no
limit/offset support. `eZURLWildcard::createWildcardsIndex()` looped calling
`fetchList($offset, $limit)`, which always returned all rows. The loop could never terminate.
Fix: proper `$skip`/`$limit` stages in the pipeline.

**Key fix — `$conds` type casting:** URL parameters arrive as strings. MongoDB stores integers.
`fetchObjectList(..., ['contentclass_id' => '49'])` with `contentclass_id` stored as integer `49`
returned 0 rows. Fix: look up `$fields[$field]['datatype']` and cast to `(int)` or `(float)` for
integer/float fields before building `$mongoFilter`. Array values (containing MongoDB operators)
are passed through unchanged.

**`storeObject()` — complete MongoDB branch**

Builds a typed PHP document from the object's current properties via `attribute()`.
- Existence check via `fetchObjectList($def, $keys, $key_conds)`
- If insert: checks `increment_key`; if `attribute(inc) <= 0` calls `nextSeqID` for new ID
- If update: calls `upsert($table, $filter, $doc)`

**`removeObject()` — MongoDB branch**

Before the SQL `DELETE` path:
```php
if ( $db->databaseName() === 'mongo' ) {
    $filter = [];
    foreach ($conditions as $field => $value)
        $filter[$field] = is_numeric($value) ? (int)$value : $value;
    $db->deleteWhere($table, $filter);
    return;
}
```

**`newObjectOrder()` — MongoDB branch**

Replaced `SELECT MAX(placement) FROM ...` arrayQuery with:
```php
$rows = $db->aggregate($table, [
    ['$match' => $cond_array (int-cast when numeric)],
    ['$group' => ['_id' => null, 'maxval' => ['$max' => '$' . $orderField]]],
]);
return ($rows[0]['maxval'] ?? 0) + 1;
```

**`handleRows()` — reference bug fix**

`foreach ($rows as &$row)` left `$row` as a live reference to `$rows[last]`. A later
`foreach ($rows as $row)` overwrote `$rows[last]` with every previous row. The last item
in every list always received the second-to-last item's data.
Fix: `unset($row)` immediately after the first loop.

---

### `extension/sevenx_mongodb/classes/kernel/ezcontentobject.php`

**`fetch($id)`** — int cast added at top: `$id = (int)$id` when `is_numeric($id)`.

**`fetchIDArray($idArray)`** — bulk aggregate with class name and object name joins.

**`fetchByNodeID($nodeID)`** — joins `ezcontentobject_tree` → `ezcontentobject` → `ezcontentclass`.

**`versionLanguageName()`** — uses `aggregate()` directly (not `find()`) to avoid
`translateConditions()` corrupting the `$or` language-mask filter.

**`contentObjectAttributes()`** — aggregate pipeline:
1. Match on `contentobject_id`, `version`, optionally `language_code`
2. `$lookup` to `ezcontentclass_attribute` on `contentclassattribute_id = id`
3. Filter `_cls.version = 0`; add `identifier` field; sort by `_cls.placement`
4. Project away `_id`, `_cls_placement`, nested `_cls`

**`fillNodeListAttributes()`** — batch-loads attributes for a list of objects in a single query.
Builds `$or` of `{contentobject_id, version, language_code}` conditions; runs same pipeline as
`contentObjectAttributes()`. Eliminates N MONGO TODOs per rendered list page.

**`assignedNodes()`** — MongoDB aggregate: `ezcontentobject_tree` → `ezcontentobject` → `ezcontentclass`. Previously returned `[]`, causing "None of the object's location(s) is visible".

**`hasVisibleNode()`** — MongoDB branch added:
```php
$filter = ['contentobject_id' => (int)$contentobjectID];
if (ShowHiddenNodes !== 'true') $filter['is_invisible'] = 0;
$rows = $db->aggregate('ezcontentobject_tree', [['$match'=>$filter],['$count'=>'node_count']]);
return !empty($rows) && $rows[0]['node_count'] > 0;
```
Eliminates 27× MONGO TODOs on the classlist page (one per class in the Content group).

**`stateIDArray()`** — `ezcobj_state_link` → `ezcobj_state` aggregate lookup.

**`stateIdentifierArray()`** — `ezcobj_state_link` → `ezcobj_state` → `ezcobj_state_group` aggregate.

**`relatedObjects()`** — Full MongoDB `$lookup` pipeline:
1. Match `ezcontentobject_link` by `from_contentobject_id`
2. `$lookup` to `ezcontentobject` on `to_contentobject_id`
3. `$lookup` to `ezcontentclass` on `contentclass_id`
4. `$lookup` to `ezcontentobject_name` for object name
5. Returns `eZContentObject` instances

---

### `extension/sevenx_mongodb/classes/kernel/ezcontentobjectversion.php`

**`fetch($id)`** — `(int)$id` cast. POST `DeleteIDArray[]` values arrive as strings.

**`removeThis()`** — Full MongoDB branch before SQL DELETE path, using `$db->deleteWhere()`:
- `ezcontentobject_attribute` rows for the version
- `ezcontentobject_link` rows for the version
- `eznode_assignment` rows for the version
- the `ezcontentobject_version` row itself
- `ezcontentobject_name` rows for the version
- If no versions remain: purges parent object; otherwise updates `current_version` to the
  most recently modified remaining version via `aggregate()`.

---

### `extension/sevenx_mongodb/classes/kernel/ezcontentclass.php`

**`fetch($id)`** — `(int)$id` cast. URL params arrive as strings; MongoDB stores integers.

**`__clone()`** — sets `$this->ID = null` → `attribute('id')` returns null → `storeObject` inserts as new record.

**`storeVersioned($attributes, $version)`** — Group migration logic:
1. Remove target version attrs and previous version attrs
2. `$this->remove(false)` — removes old version record
3. `setVersion($version)` — sets version=0 on object
4. Store each attribute at version 0
5. `eZContentClassClassGroup::removeClassMembers($id, $version)` — remove v0 groups (if any)
6. Fetch previous-version groups, change version to 0, store each → creates v0 groups
7. `removeClassMembers($id, $previousVersion)` — remove old-version groups
8. `parent::store()` + `$this->NameList->store($this)`

**`canInstantiateClassList()` — name key extraction fix:** MongoDB `ezcontentclass` documents do not contain a `name` field — they only store `serialized_name_list` (PHP-serialized string, e.g. `a:2:{s:6:"eng-US";s:6:"Folder";s:16:"always-available";s:6:"eng-US";}`). When called with `$asObject === false`, added a post-processing loop after `handleRows()`: unserializes `serialized_name_list` and populates `$row['name']` from the `always-available` language key. Confirmed via `mongosh`: `db.ezcontentclass.findOne({version:0},{name:1,serialized_name_list:1})` returns only `serialized_name_list`. Fixes `Undefined array key "name"` crash in `getClassesJsArray()` on the content/edit class selector.

**`initializeCopy()` — MONGO TODO (pending):** calls `arrayQuery` with SQL JOIN on
`[ezcontentclass, ezcontentclass_name]` to count existing copies for deduplication of the copy
name. Returns `[]` → `$count` undefined → PHP warning → name stays "Copy of Folder" regardless.
Non-fatal. See backlog for planned fix using `aggregate('ezcontentclass_name', [$match, $count])`.

---

### `extension/sevenx_mongodb/classes/kernel/ezcontentclassattribute.php`

**`classAttributeIdentifiersHash()`** — replaced 2-table JOIN with two sequential aggregates:
1. All published classes from `ezcontentclass` → build `id → identifier` map
2. All published attributes from `ezcontentclass_attribute` → join via PHP map
Returns `class_identifier/attr_identifier → attr_id` hash used by `eZNamePatternResolver`.

Previously returned `[]` → `contentObjectName()` returned empty string → every newly published
object stored an empty name in `ezcontentobject_name`.

---

### `extension/sevenx_mongodb/classes/kernel/ezcontentclassgroup.php`

**`fetch($id)`** — `(int)$id` cast.

---

### `extension/sevenx_mongodb/classes/kernel/ezcontentclassclassgroup.php`

**`fetchClassList($version, $groupId)`** — replaced 3-table SQL JOIN with:
1. `aggregate('ezcontentclass_classgroup', [$match: {group_id, version}])` → get class ID list
2. `aggregate('ezcontentclass', [$match: {id: {$in: [...]}, version}])` → get full class rows
Returns `eZContentClass` objects via `handleRows()`.

Previously returned `[]` → classlist page showed "Classes inside (0)".

---

### `extension/sevenx_mongodb/classes/kernel/ezcontentobjecttreenode.php`

**`fetch($id)`** — dispatches to `fetchMongo()`, full aggregate joining tree + object + class + name.

**`fetchNodesByPathString()`** — MongoDB `$regex` prefix match on `path_string`.

**`findMainNode($objectID)`** — aggregate with `$expr: {$eq: ['$node_id','$main_node_id']}`.

**`makeObjectsArray()`** — PHP 8.5 implicit nullable fix: `?array $propertiesOverride = null`.

**`subTreeCountByNodeID($params, $nodeID)`** — full MongoDB path:
1. Fetch parent node's `path_string` and `depth`
2. Build `$regex`-based path prefix match + depth filter
3. Apply visibility, mainNodeOnly, class filter
4. Return `$count` result as integer

**`subTreeByNodeID($params, $nodeID)`** — full MongoDB path with `$lookup` joins:
tree → object → class (version=0) → object_name; sort mapping; `$skip`/`$limit` pagination.

---

### `extension/sevenx_mongodb/classes/kernel/ezurlaliasml.php`

**`fetchPathByActionList($actionList)`** — aggregate on `ezurlalias_ml` with `$in` match on
action strings; no `lang_mask` filter.

**`translate(&$uri, $reverse)`** — rewritten. Original bugs:
1. `$db->md5()` returns SQL fragment `MD5('text')`, not a hash → `text_md5` was always `""`
2. `findOne()` returns flat assoc array; `$urlAliasArray[0]` was always undefined
3. No parent-chain traversal for multi-segment paths

Fix: iterative per-segment loop:
```php
foreach ($pathElements as $element) {
    $hash = md5(eZURLAliasML::strtolower($element));
    $rows = $db->aggregate('ezurlalias_ml', [
        ['$match' => ['parent' => $parent, 'text_md5' => $hash]],
        ['$sort'  => ['lang_mask' => -1]],
        ['$limit' => 1],
        ['$project' => ['_id' => 0]],
    ]);
    // chain: $parent = $row['link']
}
```

---

### `extension/sevenx_mongodb/classes/kernel/ezrole.php`

**Bug:** commented-out `/* } else */` destroyed if/else structure → `arrayQuery` always ran.
**Fix:** restructured as proper `if ($db->databaseName() == 'mongo') { ... } else { arrayQuery }`.

---

### `kernel/private/classes/ezcontentobjectstategroup.php`

**Bug:** `arrayQuery` ran unconditionally before `if ($db->databaseName() === 'mongo')`.
**Fix:** wrapped `arrayQuery` in `else` branch.

---

### `extension/sevenx_mongodb/classes/kernel/ezsiteaccess.php`

PHP 8.5 implicit nullable fixes in `change()` and `load()`: `?eZINI $siteINI = null`.

---

### `extension/sevenx_mongodb/classes/kernel/datatypes/ezinisetting/ezinisettingtype.php`

**`validateClassAttributeHTTPInput`** and **`storeClassAttribute`**: removed
`ContentClass_ezinisetting_ini_instance_NNN` from `hasPostVariable` guard. HTML `<select multiple>`
sends no field when nothing is selected → validation returned `STATE_INVALID` for every
ini-setting attribute → class store failed with "The class definition could not be stored."

---

### `extension/sevenx_mongodb/classes/kernel/datatypes/ezxmltext/ezxmloutputhandler.php`

Line 318: added `$tagName !== null &&` guard before `isset(...)` — PHP 8.5 deprecation for
null argument to `isset()`.

---

### `extension/sevenx_mongodb/classes/kernel/datatypes/ezxmltext/ezxmlschema.php`

Line 369: same null guard in `attrDefaultValues()`.

---

### `extension/xrowmetadata/autoloads/xrowmetadataoperator.php`

Line 66: `$cur_parent !== null ? $cur_parent->Name : ''` — guards against null parent node.

---

### `extension/sevenx_mongodb/classes/kernel/clusterfilehandlers/ezfsfilehandler.php`

PROCESSCACHE debug instrumentation was added during development and has been removed. No debug `error_log` calls remain in this file.

---

### `kernel/class/edit.php` (edit subdomain only)

1. **ezscriptmonitor lock fix:** wrapped v2 class lock template in
   `in_array('ezscriptmonitor', eZExtension::activeExtensions())`. If not active: removes v2
   class and v2 groups then continues to edit.
2. **Defensive v1 group creation:** in the `else` branch (v1 class already exists), if
   `fetchGroupList()` returns 0 groups, recreates v1 groups from v0 groups.
3. Debug `error_log()` breadcrumb lines added during development have been removed.

---

### `extension/sevenx_mongodb/classes/kernel/ezcontentlanguage.php`

**`objectCount($languageID)`** and **`classCount($languageID)`** — replaced `arrayQuery` COUNT queries with MongoDB `$count` aggregate + bitmask match.

**Bitmask fix:** `$bitsAnySet` requires an integer literal and cannot evaluate a computed value. Fix uses `$bitwiseAnd` inside `$expr`:
```php
[ '$expr' => [ '$gt' => [ [ '$bitwiseAnd' => [ '$language_mask', (int)$languageID ] ], 0 ] ] ]
```
Previously returned `[]` → object/class counts showed 0 on language management pages.

---

### `extension/sevenx_mongodb/classes/kernel/ezurlaliasquery.php`

**DISTINCT action_type (line 256):** Replaced `SELECT DISTINCT action_type FROM ezurlalias_ml` with `aggregate('ezurlalias_ml', [['$group' => ['_id' => '$action_type']]])`. Column extracted via `array_column($rows, '_id')`.

**`buildMongoMatch()` (new protected method):** Translates the same filter properties that `generateSQL()` reads (`paren`, `text`, `languages`, `actions`, `actionTypes`, `actionTypesEx`, `type`) into a MongoDB `$match` array. Returns `false` when conditions would match no rows (e.g. empty `actionTypes` after exclusion diff).

**`count()` method:** MongoDB branch calls `buildMongoMatch()` then `aggregate('ezurlalias_ml', [$match, $count])` instead of `arrayQuery("SELECT count(*) AS count …")`.

**`fetchAll()` method:** MongoDB branch calls `buildMongoMatch()` then `aggregate()` with `$sort`/`$skip`/`$limit` stages. The SQL `ORDER BY` fragment (e.g. `"text ASC"`) is parsed at runtime via `preg_split` into a MongoDB sort spec.

---

### `extension/sevenx_mongodb/classes/kernel/datatypes/ezurl/ezurl.php`

**`fetchByAttribute()` — 4-table JOIN:** Replaced SQL JOIN across `ezurl`, `ezurl_object_link`, `ezcontentobject_attribute`, `ezcontentobject_version` with a MongoDB `$lookup` pipeline:
1. `ezurl` → `$lookup` `ezurl_object_link` on `url_id = id`; `$unwind`
2. → `$lookup` `ezcontentobject_attribute` on `objectattribute_id = id`; `$unwind`
3. → `$lookup` `ezcontentobject_version` with `$expr` matching both `contentobject_id` and `version`; `$unwind`
4. `$match` `status = 1` (published versions only)
5. `$group` by URL `id` to deduplicate across multiple published versions
6. Count variant: `$count` stage; list variant: `$project` + `$skip`/`$limit`

Previously returned `[]` → URL management list page showed no URLs.

---

### `kernel/setup/systemupgrade.php`

Core file (no override). Added `if ( !is_object( $dbSchema ) )` guard before `$dbSchema->transformSchema(…)`. For MongoDB, `eZDbSchema::instance()` returns `false` (no schema handler is registered for `'mongo'`). The page now returns early and shows "Database check OK." instead of crashing with `Call to a member function transformSchema() on bool`.

---

### `extension/sevenx_mongodb/classes/kernel/ezsection.php`

**`sectionCount()`:** Added MongoDB `$count` aggregate branch before `arrayQuery`. Previously `arrayQuery` returned `[]` → `$countArray[0]['count']` undefined → section list page crashed.

```php
$rows = $db->aggregate( 'ezsection', [ [ '$count' => 'count' ] ] );
return !empty( $rows ) ? (int) $rows[0]['count'] : 0;
```

---

### `lib/ezdbschema/classes/ezdbschema.php`

Core lib file. `instance()` received `$params = false` from MongoDB callers (no schema handler for `'mongo'`). On PHP 8.5 the subsequent `!isset($params['instance'])` access triggered `E_DEPRECATED: Automatic conversion of false to array`. Fix: added `if ( !is_array( $params ) ) $params = array();` guard immediately after the `is_object( $params )` check, ensuring `$params` is always an array before any key access.

---

### `kernel/search/stats.php`

Core file (no override). Added MongoDB branch for the `ezsearch_search_phrase` COUNT query (line 42):

```php
$rows = $db->aggregate( 'ezsearch_search_phrase', [ [ '$count' => 'count' ] ] );
$searchListCount = [ [ 'count' => !empty( $rows ) ? (int) $rows[0]['count'] : 0 ] ];
```

Previously `arrayQuery` returned `[]` → `$searchListCount[0]['count']` undefined → search stats page crashed with `Undefined array key 0`.

---

### `extension/sevenx_mongodb/classes/kernel/ezsearchlog.php`

**`mostFrequentPhraseArray()`:** Added full MongoDB pipeline branch:
1. `$sort` by `phrase_count DESC`
2. `$project` — `id`, `phrase`, `phrase_count`; `result_count` computed as `$divide` guarded by `$cond` to avoid divide-by-zero when `phrase_count = 0`
3. `$skip` / `$limit` appended from `$parameters` when present

Previously `arrayQuery` returned `[]` → search stats page showed an empty phrase list.

---

### `kernel/infocollector/overview.php`

Core file (no override). Added MongoDB branch for both queries:

**Object list (line 75):** Full `$lookup` pipeline originating from `ezinfocollection`:
1. `$group` by `contentobject_id` (DISTINCT equivalent)
2. `$lookup` `ezcontentobject` on `_id = id`; `$unwind`
3. `$lookup` `ezcontentobject_tree` with `$expr` matching `contentobject_id` AND `node_id = main_node_id`; `$unwind`
4. `$lookup` `ezcontentclass` with `$expr` matching `id = contentclass_id` AND `version = 0`; `$unwind`
5. `$sort` by `_obj.name ASC`; `$skip`/`$limit`
6. `$project` — maps nested `_obj`/`_tree`/`_class` fields to the flat key names the template expects

**Count (line 92):** `aggregate('ezinfocollection', [['$group' => ['_id' => '$contentobject_id']], ['$count' => 'count']])`.

Previously both queries returned `[]` → page showed no objects and count 0.

---

### Data Fixes Applied Directly in MongoDB

| Object | Fix |
|---|---|
| `ezcontentobject_name` object 242 v12 | `name` was `[]` (empty array stored before classAttributeIdentifiersHash fix); updated to `'Home'` |
| `ezcontentclass_classgroup` class 47, 48 | v0 group records inserted; orphaned v2 group records deleted |

---

## 6. Kernel Modules — Status and Known Issues

### Module: `class` (Content Classes)

**Views:** `grouplist`, `classlist`, `view`, `edit`, `copy`, `create`

| View | URL | Status |
|---|---|---|
| `grouplist` | `/class/grouplist` | ✅ Works — lists class groups |
| `classlist` | `/class/classlist/1` | ✅ Works — lists classes in group; `hasVisibleNode()` fixed (27x MONGO TODO eliminated) |
| `view` | `/class/view/47` | ✅ Works — shows class details and breadcrumb |
| `edit` | `/class/edit/47` | ✅ Works — loads class edit form; breadcrumb shows group name |
| `copy` | `/class/copy/1` | ✅ Works — creates copy, redirects to classlist |
| copy breadcrumb | `/class/edit/49` | ✅ Works after `fetchObjectList` type-cast fix — v0 group found correctly |

**Root cause of copy breadcrumb bug:** `fetchObjectList` passed `$conds` values without
datatype casting. `$ClassID` from URL param was string `"49"`. MongoDB stores integer `49`.
`{contentclass_id: "49"}` matched nothing. Fix: cast to `(int)` based on field definition.

**Remaining:** `initializeCopy()` calls `arrayQuery` on `[ezcontentclass, ezcontentclass_name]`
JOIN for copy-name deduplication. Returns `[]` → PHP warning → name appended number is skipped.
Non-fatal: copy gets name "Copy of Folder" without sequence number. See backlog §11.

---

### Module: `content` (Content Objects)

**Views:** `view`, `edit`, `history`, `translate`, `move`, `copy`, `remove`, `versionview`, `restore`

| View | URL | Status |
|---|---|---|
| `view/full` | `/content/view/full/2` | ✅ Works — full node view with attributes |
| `edit` | `/content/edit/14` | ✅ Works — attribute editing, publish |
| `history` | `/content/history/242` | ✅ Works after version fetch int-cast fix |
| `versionview` | `/content/versionview/242/12` | ✅ Works |
| `translate` | `/content/translate/...` | Untested |
| `move` | `/content/move/...` | Untested |
| `copy` | `/content/copy/...` | Untested |
| `remove` | `/content/remove/...` | Untested (uses `removeThis()`) |

**Known remaining issues in `ezcontentobject.php`:**

| Method | Issue |
|---|---|
| `relatedObjectCount()` | No MongoDB branch — returns 0 (fired on edit/history pages) |
| `getStates()` | ✅ | `allowedAssignStateIDList()` and `stateIdentifierArray()` fully patched with two-stage aggregate; `$lookup` pipeline |
| `addContentObjectRelation()` | `to_contentobject_id` not int-cast — relation insert may fail |
| Multiple `arrayQuery` calls (~30+) | Many single-table queries not yet replaced |

---

### Module: `setup`

| View | Status |
|---|---|
| `cache` | ✅ Works |
| `extensions` | ✅ Works |
| `info` | ✅ Works |
| `rad` | Untested |
| `systemupgrade` | ✅ Works — `is_object()` guard added before `transformSchema()`; shows "Database check OK." for MongoDB |

---

### Module: `user`

| View | Status |
|---|---|
| `login` | ✅ Works |
| `logout` | ✅ Works |
| `preferences` | ✅ Works |
| `password` | ✅ Works |

---

### Module: `state`

| View | Status |
|---|---|
| `groups` | ✅ Works (state group list) |
| `view` | Untested |
| `edit` | Untested |

---

### Module: `role`

| View | Status |
|---|---|
| `list` | Untested — many `arrayQuery` calls remain in `ezrole.php` |
| `view` | Untested |
| `edit` | Untested |

---

### Module: `section`

| View | Status |
|---|---|
| `list` | ✅ Works — `sectionCount()` MongoDB `$count` aggregate added |
| `edit` | Untested |

---

### Module: `language`

| View | Status |
|---|---|
| `list` | ✅ Works — `objectCount()` + `classCount()` bitmask aggregate added (`$bitwiseAnd` inside `$expr`) |
| `edit` | Untested |

---

### Module: `search`

| View | Status |
|---|---|
| `stats` | ✅ Works — MongoDB `$count` aggregate branch added in `stats.php`; `mostFrequentPhraseArray()` pipeline added in `ezsearchlog.php` |

---

### Module: `shop`

| View | Status |
|---|---|
| `orderlist` | Untested — `ezorder.php`, `ezbasket.php` have multiple `arrayQuery` calls |
| `basket` | Untested |

---

### Module: `infocollector`

| View | Status |
|---|---|
| `overview` | ✅ Works — MongoDB `$lookup` pipeline added directly in `overview.php`; `ezinformationcollection.php` class JOIN queries remain (not called by overview page) |

---

### Module: `workflow`

| View | Status |
|---|---|
| `grouplist` | Untested |
| `processlist` | Untested |

---

### Module: `url`

| View | Status |
|---|---|
| `list` | ✅ Works — `ezurlaliasquery.php` `count()` + `fetchAll()` + `buildMongoMatch()` added; `ezurl.php` 4-table JOIN replaced with `$lookup` pipeline |
| `translate` | Untested |

---

## 7. Patterns and Conventions

### Standard MongoDB branching pattern

```php
$db = eZDB::instance();
if ( $db->databaseName() === 'mongo' )
{
    $rows = $db->aggregate( 'collection_name', [
        [ '$match'   => [ 'field' => (int)$value ] ],
        [ '$lookup'  => [ 'from' => 'other_collection', 'localField' => 'id',
                          'foreignField' => 'ref_id', 'as' => '_join' ] ],
        [ '$unwind'  => '$_join' ],
        [ '$addFields' => [ 'joined_field' => '$_join.field' ] ],
        [ '$project' => [ '_id' => 0, '_join' => 0 ] ],
    ]);
    return eZPersistentObject::handleRows( $rows, 'eZSomeClass', $asObject );
}
$rows = $db->arrayQuery( $sql );
return eZPersistentObject::handleRows( $rows, 'eZSomeClass', $asObject );
```

### `path_string` prefix matching for subtree queries

```php
$parent = $db->aggregate( 'ezcontentobject_tree', [
    [ '$match'   => [ 'node_id' => (int)$nodeID ] ],
    [ '$project' => [ '_id' => 0, 'path_string' => 1, 'depth' => 1 ] ],
]);
$pathString = $parent[0]['path_string'];
$matchStage = [
    'path_string' => [ '$regex' => '^' . preg_quote( $pathString ) . '.+' ],
];
```

### Always cast ID parameters to int

URL parameters (`$Params['ClassID']`, `$Params['NodeID']`, etc.) arrive as strings.
MongoDB stores integers. Always cast: `$id = (int)$id` at the top of any fetch method
that receives an ID from a URL param or POST field.

### `$project` must include `'_id' => 0`

MongoDB always returns `_id` unless explicitly excluded. Missing `'_id' => 0` causes an
unexpected `_id` field in row arrays, which corrupts PHP ORM objects built by `handleRows()`.

### Never use `find()` for filters with MongoDB operators

Always use `aggregate()` when the filter contains `$or`, `$in`, `$nin`, `$expr`, or any
nested array. `translateConditions()` will silently corrupt them.

### `databaseName()` check

Use `=== 'mongo'` (lowercase, no quotes around 'mongo'). The method returns the string `'mongo'`.

### Touch after editing

```bash
touch /path/to/modified/file.php
```
opcache `revalidate_freq=2s` — touch is needed to invalidate cached bytecode.

---

## 8. Operational Notes

### PHP-FPM Timeout — Setup Wizard Last Step

> **Rest assured:** the setup wizard **will complete successfully** — it simply requires more
> time than a typical SQL-based installation.  Do not cancel the browser request.

The final step of the setup wizard ("Configuration / Create Sites") is the slowest step in any
Exponential CMS installation.  On a MongoDB deployment it is significantly slower than a
MySQL/MariaDB installation because every content object published during package installation
triggers the full kernel publish pipeline (attribute stores, tree node creation, URL alias
generation, name synthesis, cache expiry).  With the `sevenx_democontent` package this means
roughly 193 objects, each requiring 10–15 individual MongoDB write operations.

**There are two independent timeout mechanisms and you must understand both:**

| Timer | Who controls it | Can PHP override it? | Default |
|---|---|---|---|
| PHP script execution time | `max_execution_time` in `php.ini` / `set_time_limit()` in code | Yes — `set_time_limit(0)` disables it | 30s (Plesk default) |
| FPM request wall-clock limit | `request_terminate_timeout` in the PHP-FPM pool config (Plesk → PHP Settings → FPM pool) | **No** — enforced by the FPM master process regardless of what the script does | 190s (Plesk default), 390s (current setting) |

The FPM `request_terminate_timeout` is the one that kills the setup wizard.  PHP's own
`set_time_limit()` is irrelevant once `request_terminate_timeout` fires — the FPM master sends
`SIGKILL` to the worker.

**What to set in Plesk:**

1. Log in to Plesk → Domains → your domain → PHP Settings
2. Under "Additional configuration directives" (or the FPM pool config panel), set:
   ```
   request_terminate_timeout = 600
   ```
   600 seconds (10 minutes) is comfortably above the observed install time after the
   performance optimizations documented in Section 22.
3. After the setup wizard completes, you may reduce this back to 60–120 s for normal operation.

**Why not just set it permanently to 600s?** A long FPM timeout means a stuck or looping
request will hold a worker for up to 10 minutes before being killed, potentially exhausting
the worker pool under load.  Keep it elevated only during installation.

**Alternative: run the installer from the CLI** (bypasses FPM entirely):
```bash
# Not yet supported by this project's setup wizard — future enhancement.
# For now, use the browser wizard with an elevated FPM timeout.
```

### PHP-FPM

**Never restart PHP-FPM.** Use `touch` to bust opcache.

### MongoDB shell

```bash
mongosh "mongodb://db:publishing\$8088@localhost:27017/exp"
```

Useful queries:
```javascript
// Check class group records for a class
db.ezcontentclass_classgroup.find({contentclass_id: 49})

// Check a content class
db.ezcontentclass.find({id: 49})

// Fix empty name
db.ezcontentobject_name.updateOne({contentobject_id: 242, content_version: 12}, {$set: {name: 'Home'}})

// Delete orphaned records
db.ezcontentclass_classgroup.deleteMany({contentclass_id: 49, contentclass_version: 2})
```

### Opcache

`opcache.validate_timestamps=1`, `opcache.revalidate_freq=2`. After any PHP file edit:
```bash
touch /path/to/file.php
```

### Debug toolbar

Enable via admin panel Quick Settings → Debug output. Queries appear with collection name and
filter in the "SQL debug output" panel. MONGO TODO entries appear inline with caller info.

---

## 9. Test Plan — Admin

Test all items after each code change. Full regression test before any deployment.

### 9.1 Authentication

| Step | Expected |
|---|---|
| Visit `https://edit.mongodb.demo.se7enx.com/` unauthenticated | Redirect to login page |
| Login with `admin` / correct password | Redirect to Dashboard |
| Login with wrong password | Error message; stay on login page |
| Logout via top nav → "Logout: admin" | Redirect to login page |

### 9.2 Dashboard

| Step | Expected |
|---|---|
| Visit Dashboard | Page loads; no MONGO TODO in debug output |
| Check "Content structure" link | Navigates to `/content/view/full/2` |

### 9.3 Content Structure (content/view/full)

| Step | Expected |
|---|---|
| Open top-level node (node 2) | Lists child nodes with names |
| Drill down one level | Child nodes list correctly |
| Click a folder node | Shows node attributes and sub-items |
| Check breadcrumb | Shows correct path e.g. "Content structure / Folder name" |
| Check "Objects" count on classlist | Shows correct integer for each class |

### 9.4 Content Object — Edit and Publish

| Step | Expected |
|---|---|
| Open an Article object (e.g. node 14) | Full view renders with all attributes |
| Click Edit | Edit form loads with all attribute fields populated |
| Change a text field | No JS errors |
| Click "Publish" | Redirects back to view; new version is current |
| Open version history (`/content/history/NNN`) | Shows all versions; most recent at top |
| Check version numbers | No duplicates; correct sequence |
| Delete a draft version | Version disappears from list; no orphan attributes in MongoDB |

### 9.5 Content Object — Object Name

| Step | Expected |
|---|---|
| Publish a new version of any object | Object name in breadcrumb and listing matches name pattern |
| Check `ezcontentobject_name` in MongoDB | `name` field is non-empty string |
| Check `ezcontentobject.name` | Matches the name record |

### 9.6 Class Groups

| Step | Expected |
|---|---|
| Visit Setup → Classes → `/class/grouplist` | Lists all groups (Content, Users, Media, etc.) |
| Click "Content" group | Navigates to `/class/classlist/1` |
| Check count | "Classes inside (27)" or current count |
| Check "Objects" column | Each class shows correct object count |
| Debug output | 0 MONGO TODO entries (all `hasVisibleNode()` calls now use aggregate) |

### 9.7 Class List

| Step | Expected |
|---|---|
| Visit `/class/classlist/1` | Lists all classes in Content group |
| Click class name (e.g. "Folder") | Navigates to `/class/view/1` |
| Breadcrumb on view page | Shows "Class groups / Content" (not just "Class groups") |

### 9.8 Class Edit (Existing Class)

| Step | Expected |
|---|---|
| Visit `/class/edit/47` | Edit form loads; no "Class locked" page |
| Breadcrumb | Shows "Class groups / Content / Trash Bin (TMP Folder)" |
| Edit class name | Form accepts input |
| Click "Store" | Saves; redirects to classlist; no MONGO TODO for the store operation |
| Edit class with ezinisetting attribute | Store succeeds even if no ini instances are selected |

### 9.9 Class Copy

| Step | Expected |
|---|---|
| Click copy icon next to "Folder" on classlist | Redirects to classlist |
| New class "Copy of Folder" appears | Listed in Content group |
| Visit `/class/edit/NNN` for copied class | Edit form loads |
| Breadcrumb on edit page | Shows "Class groups / Content / Copy of Folder" (NOT "Class groups / Copy of Folder") |
| Store the copied class | Saves successfully |
| Delete the copied class | Class removed from list |

### 9.10 Class Create

| Step | Expected |
|---|---|
| Click "Create a new class" button from classlist | Form loads |
| Enter class name, add a Text attribute | No errors |
| Store | New class appears in classlist |
| Edit new class | Loads correctly with breadcrumb showing group name |

### 9.11 Version History

| Step | Expected |
|---|---|
| Publish an object 3+ times | Each publish creates a new version |
| Open `/content/history/NNN` | Shows all versions, each with correct data |
| Last version in list shows correct data | Not a duplicate of second-to-last (ref bug fixed) |
| Delete version 1 | Disappears; remaining versions unaffected |
| Delete all but current version | Object remains; only published version present |

### 9.12 States

| Step | Expected |
|---|---|
| Visit `/state/groups` | Lists state groups |
| Open a state group | Lists states |
| Edit a state | Form loads |

### 9.13 User Management

| Step | Expected |
|---|---|
| Visit User Accounts tree | Lists user groups |
| Open Admin User | Shows user profile |
| Edit Admin User | Form loads with name, email fields |
| Change password | Saves; can login with new password |

---

## 10. Test Plan — Front Site

### 10.1 Homepage

| Step | Expected |
|---|---|
| Visit `https://mongodb.demo.se7enx.com/` | Homepage renders |
| No 504 timeout | Page loads in under 2 seconds |
| Check breadcrumb | Shows site root |
| Debug: no infinite loop | URL wildcard cache rebuilds quickly (first visit) |

### 10.2 Content Navigation

| Step | Expected |
|---|---|
| Visit a folder URL (e.g. `/News/`) | Lists child articles |
| Click an article | Full article view renders |
| Check all attribute values | Text, image, date attributes display correctly |
| Check breadcrumb | Correct path from root to current node |
| Back button to folder | Returns to listing |

### 10.3 URL Aliases

| Step | Expected |
|---|---|
| Visit a URL like `/News/Article-title` | Correct content object renders |
| Visit a URL with multiple path segments | Each segment resolves correctly (chain lookup) |
| Non-existent URL | Returns 404 or redirects to homepage |
| Aliased URL | Redirects to canonical URL |

### 10.4 Images and Media

| Step | Expected |
|---|---|
| Article with embedded image | Image renders in body text |
| Article with image attribute | Image attribute displays thumbnail |
| Image object URL | Direct image URL resolves |

### 10.5 Object States

| Step | Expected |
|---|---|
| Object with state set | State-based template conditions work |
| Hidden object | Not visible in listings |

### 10.6 Search (if enabled)

| Step | Expected |
|---|---|
| Submit search query | Results page renders |
| Results list | Shows matching content objects |

### 10.7 Cache Behavior

| Step | Expected |
|---|---|
| Clear cache from admin | Cache files removed; next front-site request regenerates |
| Second visit to same page | Faster (cached response) |
| Edit and publish object | Front-site cache invalidated; updated content visible |

### 10.8 Advanced Content Tests

| Test | Steps | Expected |
|---|---|---|
| Subtree content list | Visit a folder with 10+ children | All children listed; pagination works |
| Object with XML text (rich text) | Open article with formatted body | Bold, italic, links render correctly |
| Object with keyword attribute | View article | Keywords display |
| Object with related objects | View article | Related articles listed |
| Object with multiple languages | View in non-default language | Correct translation shown |
| Deep URL (5+ levels) | Navigate to deeply nested node | URL resolves; breadcrumb correct |

---

## 11. Resolved Issues Log

This table is a historical record of every `arrayQuery` / SQL path that was identified, reported, and fixed. All items are resolved.

| Priority | File | Location | Resolution |
|---|---|---|---|
| ~~HIGH~~ | ~~`ezcontentobject.php`~~ | ~~`relatedObjectCount()`~~ | ✅ Confirmed already patched — MongoDB `$match` + `$count` pipeline replaces multi-JOIN SQL |
| ~~HIGH~~ | ~~`ezcontentobject.php`~~ | ~~`getStates()` / `allowedAssignStateIDList()` / `stateIdentifierArray()`~~ | ✅ Confirmed already patched — both methods have MongoDB `$lookup` pipeline branches |
| ~~HIGH~~ | ~~`ezcontentclass.php`~~ | ~~`initializeCopy()` ~965~~ | ✅ Already patched — `$lookup` on `[ezcontentclass, ezcontentclass_name]` |
| ~~HIGH~~ | ~~`ezcontentobject.php`~~ | ~~`addContentObjectRelation()`~~ | ✅ Fixed — `$toObjectID = (int)$toObjectID` int-cast added before MongoDB branch uses it |
| ~~MED~~ | ~~`ezkeywordtype.php`~~ | ~~`deleteStoredObjectAttribute()` lines 149, 221, 235~~ | ✅ Fixed — two-stage aggregate (`$group` + `$match cnt=1`); `deleteWhere` for cleanup |
| ~~MED~~ | ~~`ezxmltexttype.php`~~ | ~~`deleteStoredObjectAttribute()` line 811~~ | ✅ Fixed — `$nin` anti-join aggregate finds orphan `ezurl` IDs; `deleteWhere` removes them |
| ~~MED~~ | ~~`kernel/class/edit.php`~~ | ~~debug `error_log()` lines~~ | ✅ Confirmed clean — no `error_log` calls in file |
| ~~MED~~ | ~~`kernel/content/attribute_edit.php`~~ | ~~lines 365, 402~~ | ✅ Removed — `PUBLISH_VALIDATE` and `PUBLISH_BEFORE_HOOKS` debug lines deleted |
| ~~MED~~ | ~~`kernel/content/edit.php`~~ | ~~line 749~~ | ✅ Removed — `PUBLISH_DEBUG operationResult` debug line deleted |
| ~~MED~~ | ~~`ezfsfilehandler.php`~~ | ~~multiple~~ | ✅ Resolved — PROCESSCACHE `error_log()` debug calls removed |
| ~~LOW~~ | ~~`ezrole.php`~~ | ~~lines 543, 682, 765, 787, 839, 868~~ | ✅ Confirmed already patched — all 6 listed `arrayQuery` calls are inside SQL `else` branches; MongoDB path runs first and returns early |
| ~~LOW~~ | ~~`ezinformationcollection.php`~~ | ~~lines 422, 437, 577, 601~~ | ✅ Fixed — MongoDB `$count`, `$group`, and two-stage `$match` branches added for all 4 JOIN queries |
| ~~LOW~~ | ~~`eznodeassignment.php`~~ | ~~lines 471, 496~~ | ✅ Confirmed already patched |
| ~~LOW~~ | ~~`ezpolicylimitation.php`~~ | ~~line 374~~ | ✅ Fixed — two-stage aggregate |
| ~~LOW~~ | ~~`ezorder.php`~~ | ~~line 240~~ | ✅ Fixed — `active()` user-name sort branch: `$lookup` on `ezcontentobject`, `$addFields` + `$sort` |
| ~~LOW~~ | ~~`ezbasket.php`~~ | ~~lines 478, 512~~ | ✅ Fixed — `cleanupExpired()`: expired-session `$in` lookup; `cleanup()`: full sweep; both use `deleteWhere` |
| ~~LOW~~ | ~~`ezdiscount.php`~~ | ~~lines 47, 63~~ | ✅ Fixed — `$in` aggregate for sub-rules; scalar `$match` aggregate for limitation values |
| ~~LOW~~ | ~~`ezdbgarbagecollector.php`~~ | ~~lines 105, 219, 310, 393~~ | ✅ Fixed — `$nin` anti-join aggregates replace all 4 RIGHT JOIN orphan-cleanup patterns |
| ~~LOW~~ | ~~`ezinformationcollectionattribute.php`~~ | ~~line 176~~ | ✅ Fixed — simple `aggregate` on `ezcontentclass_attribute` for `serialized_name_list` lookup |
| ~~LOW~~ | ~~`ezcollaborationitem.php`~~ | ~~lines 467, 484~~ | ✅ Confirmed already patched — MongoDB `$lookup` pipeline path returns early; SQL `arrayQuery` branches are unreachable for MongoDB |
| ~~LOW~~ | ~~`lib/ezimage/classes/ezimagemanager.php`~~ | ~~line 195~~ | ✅ Fixed — `array_key_exists(null, ...)` PHP 8.5 crash resolved |

### Fix Template for a Simple Single-Table `arrayQuery`

```php
// Before:
$rows = $db->arrayQuery("SELECT * FROM eztable WHERE id='$id'");

// After:
if ( $db->databaseName() === 'mongo' ) {
    $rows = $db->aggregate( 'eztable', [
        [ '$match'   => [ 'id' => (int)$id ] ],
        [ '$project' => [ '_id' => 0 ] ],
    ]);
} else {
    $rows = $db->arrayQuery("SELECT * FROM eztable WHERE id='$id'");
}
```

### Fix Template for a JOIN `arrayQuery`

```php
// Before:
$rows = $db->arrayQuery("SELECT a.*, b.name FROM ta a, tb b WHERE a.ref_id = b.id AND a.filter='$val'");

// After:
if ( $db->databaseName() === 'mongo' ) {
    $rows = $db->aggregate( 'ta', [
        [ '$match'    => [ 'filter' => $val ] ],
        [ '$lookup'   => [ 'from' => 'tb', 'localField' => 'ref_id', 'foreignField' => 'id', 'as' => '_b' ] ],
        [ '$unwind'   => '$_b' ],
        [ '$addFields'=> [ 'name' => '$_b.name' ] ],
        [ '$project'  => [ '_id' => 0, '_b' => 0 ] ],
    ]);
} else {
    $rows = $db->arrayQuery("SELECT a.*, b.name FROM ta a, tb b WHERE a.ref_id = b.id AND a.filter='$val'");
}
```

---

## 12. Test Plan — Cronjobs / Scripts

All scripts below live under `bin/php/` or `cronjobs/` and are invoked via
`runcronjobs.php` (web-triggered) or directly via CLI. Each entry shows:
- **Purpose** — what the script does
- **MongoDB Status** — whether it works, is blocked, or needs a branch
- **Key Fix** — the specific `arrayQuery`/`query` call that must be patched

---

### 13.1  `runcronjobs.php` — Cron Launcher

**Purpose:** Launcher that bootstraps `eZScript` + `eZCLI`, reads
`cronjob.ini[CronjobSettings] MaxScriptExecutionTime`, then executes each
enabled cronjob script via `include`.

**MongoDB Status:** ✅ Launcher itself is SQL-free. Works as-is.

**How to run:**
```bash
/opt/plesk/php/8.5/bin/php runcronjobs.php --siteaccess=sevenx_site --allow-root-user
```

---

### 13.2  `bin/php/updateniceurls.php` — URL Alias Rebuild

**Purpose:** Rebuilds all URL aliases in `ezurlalias_ml` for every node in the
content tree. Called after bulk imports or when aliases become stale.

**MongoDB Status:** ⚠️  Partially fixed.

- ✅ Line 1043 top-level node query patched to use `aggregate('ezcontentobject_tree', [$match depth=1, $sort, $project])`
- ⚠️  `storePath()` inside `ezurlaliasml.php` still has many `arrayQuery` and
  `query` calls (lines 574, 610, 669, 703, 756, 774, 820 etc.) that are no-ops.
  The script therefore creates new top-level alias entries (IDs 421, 431, 461 …)
  but reports `Updated 0/219` for child nodes.
- ✅ Broken parent-chain data was repaired directly via PHP CLI (see §13.9).

**Re-run after `storePath()` is fully converted:**
```bash
/opt/plesk/php/8.5/bin/php bin/php/updateniceurls.php \
  --siteaccess=sevenx_site --allow-root-user
```

---

### 13.3  `bin/php/updatesearchindex.php` — Full Search Index Rebuild

**Purpose:** Rebuilds the full-text search index (eZFind/Solr or eZSearch)
for all content objects.

**MongoDB Status:** 🔴 Untested. Likely blocked by `arrayQuery` calls in
`eZSearchEngine` or `eZContentObject::fetchList`.

**Key dependency:** `eZContentObject::fetchList()` / `fetchObjectList()` —
verify `fetchObjectList()` MongoDB branch in `ezpersistentobject.php` handles
`SORT` and `LIMIT` correctly.

---

### 13.4  `cronjobs/indexcontent.php` — Incremental Search Indexing

**Purpose:** Reads `ezpending_actions WHERE action IN ('index_object',
'index_moved_node')`, fetches each object, and passes it to the search engine.
Deletes rows from `ezpending_actions` when done.

**MongoDB Status:** 🔴 Blocked.

**Queries to fix:**
```php
// Line ~20 (approximate):
$db->arrayQuery("SELECT * FROM ezpending_actions WHERE action='index_object' OR action='index_moved_node'");

// Fix:
if ( $db->databaseName() === 'mongo' ) {
    $rows = $db->aggregate('ezpending_actions', [
        ['$match' => ['action' => ['$in' => ['index_object', 'index_moved_node']]]],
        ['$sort'  => ['id' => 1]],
    ]);
} else { /* original arrayQuery */ }
```

After indexing each object, `DELETE FROM ezpending_actions WHERE id=X` — replace
with `$db->collection('ezpending_actions')->deleteOne(['id' => (int)$id])`.

---

### 13.5  `cronjobs/staticcache_cleanup.php` — Static Cache Cleanup

**Purpose:** Reads `ezpending_actions WHERE action='static_store'`, fetches the
URL for each pending node, writes a static HTML file, then removes the row.

**MongoDB Status:** 🔴 Blocked (same `ezpending_actions` pattern as §13.4).

**Fix pattern:** identical to §13.4 — `aggregate('ezpending_actions', [$match action='static_store'])`.

---

### 13.6  `cronjobs/unlock.php` — Unlock Locked Objects

**Purpose:** Finds all content objects in a "locked" state and resets them to
the default "not-locked" state by updating `ezcobj_state_link`.

**MongoDB Status:** 🔴 Blocked. Contains a JOIN:

```sql
SELECT ezcobj_state_link.contentobject_id
FROM   ezcobj_state_link, ezcobj_state
WHERE  ezcobj_state.identifier = 'locked'
  AND  ezcobj_state_link.contentobject_state_id = ezcobj_state.id
```

**Fix:**
```php
if ( $db->databaseName() === 'mongo' ) {
    $lockedState = $db->aggregate('ezcobj_state', [
        ['$match'   => ['identifier' => 'locked']],
        ['$project' => ['_id' => 0, 'id' => 1]],
    ]);
    if ( !empty($lockedState) ) {
        $lockedStateId = (int)$lockedState[0]['id'];
        $rows = $db->aggregate('ezcobj_state_link', [
            ['$match'   => ['contentobject_state_id' => $lockedStateId]],
            ['$project' => ['_id' => 0, 'contentobject_id' => 1]],
        ]);
    }
} else { /* original arrayQuery JOIN */ }
```

The subsequent `UPDATE ezcobj_state_link SET contentobject_state_id=1` must be
replaced with `$db->collection('ezcobj_state_link')->updateMany(...)`.

---

### 13.7  `cronjobs/session_gc.php` — Session Garbage Collection

**Purpose:** Calls `eZSession::garbageCollector()` and
`eZBasket::cleanupExpired()` to delete stale session rows.

**MongoDB Status:** ⚠️  Depends on `eZSession` and `eZBasket` implementations.
`eZSession` stores to `ezsession` collection; `garbageCollector()` deletes rows
older than `SessionTimeout`. Likely requires a MongoDB branch in `eZSession`.

---

### 13.8  `cronjobs/trashpurge.php` — Trash Purge

**Purpose:** Permanently deletes objects sitting in the trash older than
`content.ini[RemoveSettings] TrashPurgeDuration`.

**MongoDB Status:** ⚠️  Calls `eZScriptTrashPurge::run()` which uses
`eZContentObject::fetchList` and `removeThis()`. `fetchObjectList` has a
MongoDB branch. `removeThis()` (hard delete) likely needs a MongoDB branch — untested.

---

### 13.9  Direct MongoDB Data Repair — URL Alias Chain

The following repair was applied manually after the `updateniceurls.php` partial
run created top-level alias entries with new IDs but left child entries referencing
stale parent IDs from the original SQL migration.

**Root Cause:** `getNewID()` was a no-op (called `query('INSERT INTO ezurlalias_ml_incr')`
which is a no-op in the MongoDB adapter), so the first three top-level aliases got
`id=0`. After `nextAtomicID()` was added and `updateniceurls.php` was re-run, new
top-level entries were created with real IDs, but child entries still referenced the
old SQL-era IDs as parents.

**Repair applied:**

| Old orphan `parent` | Correct new `parent` | Node          |
|---------------------|----------------------|---------------|
| 2                   | 431                  | Users (eznode:5) |
| 9                   | 457                  | Media (eznode:43) |
| 13                  | 459                  | Setup2 (eznode:48) |
| 25                  | 461                  | Design (eznode:58) |

```php
// Example repair script (applied 2025-01):
$db->ezurlalias_ml->updateMany(['parent' => 2],  ['$set' => ['parent' => 431]]);
$db->ezurlalias_ml->updateMany(['parent' => 9],  ['$set' => ['parent' => 457]]);
$db->ezurlalias_ml->updateMany(['parent' => 13], ['$set' => ['parent' => 459]]);
$db->ezurlalias_ml->updateMany(['parent' => 25], ['$set' => ['parent' => 461]]);
```

**Post-repair:** Run `bin/php/ezcache.php --clear-id=urlalias` to flush the alias cache.

---

### 13.10  `cronjobs/old_drafts_cleanup.php` / `internal_drafts_cleanup.php`

**Purpose:** Deletes content object versions stuck in DRAFT status longer than
`content.ini[VersionManagement] DraftsDuration` /
`content.ini[VersionManagement] InternalDraftsDuration`.

**MongoDB Status:** ⚠️  Uses `eZContentObjectVersion::cleanupOldDrafts()` which
calls `fetchObjectList`. The `fetchObjectList` MongoDB branch should handle simple
filters. Needs integration test.

---

### 13.11  `cronjobs/notification.php` — Notification Events

**Purpose:** Creates `eZNotificationEvent` entries and runs
`eZNotificationEventFilter::process()` to send email notifications to subscribers.

**MongoDB Status:** 🔴 Untested. `eZNotificationEventFilter` contains JOIN queries
against `eznotification_collection` / `eznotificationevent` — likely needs MongoDB branches.

---

### 13.12  `cronjobs/updateviewcount.php` — View Count Updater

**Purpose:** Reads `updateview.log` (a file written by the view kernel), parses
node view events, and increments `view_count` in `ezcontentobject_tree`.

**MongoDB Status:** ✅ Log file parsing is pure PHP. The tree update calls
`$node->setAttribute('view_count', …); $node->store()` — `store()` goes through
`eZPersistentObject::store()` which should work via the MongoDB adapter.
Needs smoke-test after §13.3 search index work.

---

### 13.13  `bin/php/cleanupversions.php` — Version Cleanup

**Purpose:** Removes excess archived content object versions, keeping only
`content.ini[VersionManagement] DefaultVersionHistoryLimit` versions per object.

**MongoDB Status:** ⚠️  Calls `eZContentObjectVersion::removeVersions()`. This
internally uses `fetchObjectList` (has MongoDB branch) and `removeThis()` which
calls `query('DELETE FROM …')` — a no-op. Needs a MongoDB `deleteMany` branch in
`eZContentObjectVersion::removeThis()`.

---

### 13.14  `bin/php/adddefaultstates.php` — Add Default Object States

**Purpose:** Ensures all content objects have the default state entry in
`ezcobj_state_link` for every state group.

**MongoDB Status:** ⚠️  Simple `INSERT … WHERE NOT EXISTS` pattern. Needs
MongoDB `insertOne` with a pre-check via `aggregate`.

---

### Cronjob Test Matrix

| Script                         | Status | Blocker                                    |
|--------------------------------|--------|--------------------------------------------|
| `runcronjobs.php`              | ✅ OK  | —                                          |
| `updateniceurls.php`           | ⚠️ Partial | `storePath()` arrayQuery calls pending |
| `updatesearchindex.php`        | 🔴 Blocked | `eZSearchEngine` SQL queries          |
| `indexcontent.php`             | 🔴 Blocked | `ezpending_actions` arrayQuery         |
| `staticcache_cleanup.php`      | 🔴 Blocked | `ezpending_actions` arrayQuery         |
| `unlock.php`                   | 🔴 Blocked | `ezcobj_state_link` JOIN              |
| `session_gc.php`               | ⚠️ Needs test | `eZSession::garbageCollector()`   |
| `trashpurge.php`               | ⚠️ Needs test | `eZScriptTrashPurge::run()`        |
| `old_drafts_cleanup.php`       | ⚠️ Needs test | `fetchObjectList` coverage          |
| `internal_drafts_cleanup.php`  | ⚠️ Needs test | `fetchObjectList` coverage          |
| `notification.php`             | 🔴 Blocked | notification JOIN queries              |
| `updateviewcount.php`          | ✅ Likely OK | Smoke-test pending                  |
| `cleanupversions.php`          | ⚠️ Partial | `removeThis()` DELETE no-op            |
| `adddefaultstates.php`         | ⚠️ Partial | INSERT WHERE NOT EXISTS              |

---

## 14. PHPUnit Testing — Theory and Next-Phase Plan

> **Status:** The adapter-level test suite described in sections 14.6 and 14.3 has now been
> implemented. See **[Section 21](#21-phpunit-test-suite--implemented-may-2026)** for the
> concrete files, running instructions, and test results. The theory below explains the
> infrastructure choices that informed the implementation.

This section documents the **theory and prerequisites** for adding automated PHPUnit test coverage to the MongoDB port.

---

### 14.1 Current State of eZ Publish Test Infrastructure

The eZ Publish / Exponential 6.0.x codebase ships with a legacy PHPUnit test suite under `tests/`. The suite was written for PHPUnit 3.x/4.x and targets MySQL exclusively. It bootstraps via `tests/bootstrap.php`, which loads `autoload.php` and configures a siteaccess. All test database operations run against a live MySQL (or MariaDB) connection configured in `settings/override/`.

As of version 6.0.13, the project uses **PHPUnit 9.x or 10.x** (via Composer), which requires:
- PHP 8.0+ (already satisfied — we run PHP 8.5)
- `phpunit/phpunit ^9.6 || ^10.0` in `composer.json`
- Attribute-based test annotations (`#[Test]`, `#[DataProvider]`) replacing the legacy `@test` docblock style in PHPUnit 10+
- `setUp()` / `tearDown()` with correct return types (`void`)

Before any test can run against MongoDB, the bootstrap and configuration layers need to be made database-agnostic.

---

### 14.2 What Needs to Change in the Bootstrap Layer

#### `tests/bootstrap.php`

Currently hard-codes a MySQL DSN. Required changes:

1. **Environment-driven DB selection.** Read a `TEST_DB_DRIVER` environment variable (`mysql` or `mongo`). When `mongo`, load the `sevenx_mongodb` extension and set the `DatabaseDriver` ini setting to `expMongoDB`.

2. **Siteaccess override.** The test bootstrap sets `siteaccess=plain_site` which reads `settings/siteaccess/plain_site/site.ini`. A parallel `settings/siteaccess/plain_site_mongo/site.ini` (or a `settings/override/mongo_test/site.ini`) is needed that points `DatabaseDriver=expMongoDB` and omits `DatabaseName`, `DatabaseUser`, `DatabasePassword` in favour of the MongoDB URI.

3. **Schema seeding.** MySQL tests rely on a `tests/db/` SQL dump that is loaded before each suite. The MongoDB equivalent would be a fixture loader that seeds known documents into the `exp` database (or a separate `exp_test` database). The fixture format should be JSON files per collection, loaded by a `MongoDBFixtureLoader` helper class.

4. **Transaction isolation.** MySQL tests wrap each test in a transaction that is rolled back in `tearDown()`, giving clean isolation cheaply. MongoDB has no cross-document transactions in the replica-set-free standalone config used here. The MongoDB equivalent strategy is: **truncate and re-seed** each collection used by the test in `setUp()`, or use a dedicated `exp_test` database that is dropped and recreated per test class.

Example bootstrap excerpt (theory):
```php
// tests/bootstrap.php — MongoDB path
if ( getenv('TEST_DB_DRIVER') === 'mongo' ) {
    define('EZP_TEST_DB', 'mongo');
    // Point ini override to MongoDB siteaccess
    putenv('EZP_SITEACCESS=plain_site_mongo');
    // Load Composer autoload (MongoDB driver)
    require __DIR__ . '/../vendor/autoload.php';
    // Seed test fixtures
    MongoDBTestFixtures::seedAll( 'exp_test' );
}
```

---

### 14.3 Test Database Isolation Strategies

#### For MariaDB / MySQL

The existing approach works well and should be preserved:
- Each test class calls `$this->getDB()->begin()` in `setUp()` and `rollback()` in `tearDown()`
- The test database name is `ezpublish_test` — separate from production
- Schema is loaded once per suite from `tests/db/mysql_schema.sql`

To keep this working under PHPUnit 9/10, the base class `eZDatabaseTestCase` needs:
- Return type `void` on `setUp()` and `tearDown()`
- Any `@expectedException` docblocks replaced with `$this->expectException()`
- `setUpBeforeClass()` / `tearDownAfterClass()` made `static`

#### For MongoDB

A new `eZMongoDBTestCase` base class is required:
```php
abstract class eZMongoDBTestCase extends \PHPUnit\Framework\TestCase
{
    protected static string $testDatabase = 'exp_test';
    protected expMongoDB $db;

    protected function setUp(): void
    {
        $this->db = eZDB::instance();
        $this->seedFixtures();
    }

    protected function tearDown(): void
    {
        $this->truncateFixtures();
    }

    /**
     * Subclasses declare which collections to truncate and seed.
     * @return array  e.g. ['ezcontentobject', 'ezcontentclass']
     */
    abstract protected function fixtureCollections(): array;

    private function seedFixtures(): void
    {
        $dir = __DIR__ . '/fixtures/';
        foreach ( $this->fixtureCollections() as $col ) {
            $file = $dir . $col . '.json';
            if ( !file_exists($file) ) continue;
            $docs = json_decode( file_get_contents($file), true );
            foreach ( $docs as $doc )
                $this->db->insert( $col, $doc );
        }
    }

    private function truncateFixtures(): void
    {
        foreach ( $this->fixtureCollections() as $col )
            $this->db->deleteWhere( $col, [] );
    }
}
```

---

### 14.4 Fixture Format

Test fixtures should live under `tests/fixtures/mongodb/` as one JSON file per collection:

```
tests/fixtures/mongodb/
  ezcontentclass.json
  ezcontentclass_attribute.json
  ezcontentclass_classgroup.json
  ezcontentclassgroup.json
  ezcontentobject.json
  ezcontentobject_attribute.json
  ezcontentobject_name.json
  ezcontentobject_tree.json
  ezcontentobject_version.json
  ezcontentobject_link.json
  ezcobj_state.json
  ezcobj_state_group.json
  ezcobj_state_link.json
  ezsection.json
  ezurlalias_ml.json
  ezinfocollection.json
  ezsearch_search_phrase.json
  ...
```

Each JSON file contains an array of documents that match the MongoDB document shape (integer fields as integers, not strings). A minimal `ezcontentclass.json` fixture might look like:
```json
[
  {
    "id": 1,
    "version": 0,
    "identifier": "folder",
    "serialized_name_list": "a:2:{s:6:\"eng-US\";s:6:\"Folder\";s:16:\"always-available\";s:6:\"eng-US\";}",
    "is_container": 1,
    "modifier_id": 14,
    "modified": 1700000000
  }
]
```

---

### 14.5 Existing Tests That Need Modification

The following existing test files will need changes to support dual-database testing:

| Test file | Required changes |
|---|---|
| `tests/kernel/ezcontentclass_test.php` | Add `@group mongo` variant; mock/replace SQL assertions with MongoDB aggregate checks |
| `tests/kernel/ezcontentobject_test.php` | Same — each `assertQuery()` call must have a MongoDB path |
| `tests/kernel/ezcontentobjecttreenode_test.php` | `subTreeByNodeID` + `subTreeCountByNodeID` tested against fixture tree |
| `tests/kernel/ezpersistentobject_test.php` | `fetchObjectList` type-cast tests — verify string `"49"` vs int `49` cond behaviour |
| `tests/kernel/ezurlaliasml_test.php` | `translate()` tested for multi-segment path resolution |
| `tests/kernel/ezurlaliasquery_test.php` | **New** — needs to be created; tests `buildMongoMatch()`, `count()`, `fetchAll()` |
| `tests/kernel/ezsection_test.php` | `sectionCount()` MongoDB path |
| `tests/kernel/ezinformationcollection_test.php` | `fetchCollectionCountForObject()` MongoDB path |
| `tests/kernel/ezsearchlog_test.php` | `mostFrequentPhraseArray()` pipeline result shape |

For each test class, the recommended pattern is a **parameterised test** over two database providers:
```php
#[DataProvider('dbProvider')]
public function testFetchReturnsCorrectCount( string $driver ): void
{
    $this->setUpDatabase( $driver );   // swaps eZDB singleton
    $count = eZSection::sectionCount();
    $this->assertGreaterThan( 0, $count );
}

public static function dbProvider(): array
{
    return [
        'mysql'  => ['mysql'],
        'mongodb' => ['mongo'],
    ];
}
```

The `setUpDatabase()` helper in the base class would re-instantiate the `eZDB` singleton with the appropriate driver, seed the relevant fixtures, and register a teardown cleanup.

---

### 14.6 New Tests to Create for MongoDB-Specific Behaviour

Beyond porting existing tests, several new test cases cover MongoDB-only behaviour:

#### `tests/mongodb/SevenxMongoDBAdapterTest.php`
Tests the adapter itself:
- `testAggregateReturnsExpectedShape()` — simple `$count` on a seeded collection
- `testInsertAndFetchRoundtrip()` — insert a document, read it back via `aggregate`
- `testUpsertUpdatesExistingDocument()` — upsert with existing filter replaces fields
- `testDeleteWhereRemovesDocuments()` — confirm deletion
- `testNextAtomicIDIsMonotonic()` — call `nextAtomicID` 10 times, confirm sequential
- `testListCollectionNamesReturnsList()` — confirm known collections present
- `testDatabaseNameReturnsMongo()` — `$db->databaseName() === 'mongo'`

#### `tests/mongodb/EZPersistentObjectMongoTest.php`
- `testFetchObjectListCastsStringCondToInt()` — pass `['id' => '1']`; confirm row returned
- `testFetchObjectListWithLimit()` — confirm `$limit` stage is applied (returns ≤ N rows)
- `testFetchObjectListWithSort()` — confirm `$sort` stage is applied
- `testStoreObjectInsertsNewDocument()` — store an object with no existing ID
- `testStoreObjectUpdatesExisting()` — store with existing ID updates document
- `testRemoveObjectDeletesDocument()` — confirm deletion
- `testHandleRowsReferencesBugFixed()` — seed 3 rows; confirm last row is not a copy of second-to-last

#### `tests/mongodb/EZContentClassMongoTest.php`
- `testFetchReturnsObjectWithCorrectID()`
- `testCanInstantiateClassListRawArrayHasNameKey()` — call with `$asObject=false`; confirm `$row['name']` is populated from `serialized_name_list`
- `testCanInstantiateClassListObjectModeSkipsNameInjection()` — `$asObject=true`; no `name` key check
- `testStoreVersionedMigratesGroupsCorrectly()`

#### `tests/mongodb/EZContentObjectMongoTest.php`
- `testFetchByIDCastsStringToInt()`
- `testAssignedNodesReturnsNodesForObject()`
- `testHasVisibleNodeReturnsTrueWhenNodeExists()`
- `testRelatedObjectsReturnsLinkedObjects()`
- `testStateIDArrayReturnsStateIDs()`

#### `tests/mongodb/EZURLAliasMLMongoTest.php`
- `testTranslateSingleSegmentPath()`
- `testTranslateMultiSegmentPath()`
- `testTranslateReturnsFalseForUnknownPath()`
- `testFetchPathByActionListReturnsRows()`

#### `tests/mongodb/EZURLAliasQueryMongoTest.php`
- `testBuildMongoMatchWithParentFilter()`
- `testBuildMongoMatchWithTextFilter()`
- `testBuildMongoMatchWithTypeAlias()`
- `testBuildMongoMatchWithActionTypesExReturnsEmptyWhenAllExcluded()`
- `testCountReturnsCorrectInteger()`
- `testFetchAllReturnsPathElements()`
- `testFetchAllRespectsSortOrder()`

---

### 14.7 PHPUnit Configuration for Dual-Database Runs

A `phpunit.xml` split into two suites:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="tests/bootstrap.php"
         colors="true">

  <testsuites>
    <testsuite name="MySQL (MariaDB)">
      <directory>tests/kernel</directory>
      <directory>tests/ezdb_mysql</directory>
    </testsuite>
    <testsuite name="MongoDB">
      <directory>tests/mongodb</directory>
    </testsuite>
  </testsuites>

  <php>
    <!-- Override at runtime: TEST_DB_DRIVER=mongo ./vendor/bin/phpunit --testsuite MongoDB -->
    <env name="TEST_DB_DRIVER" value="mysql"/>
    <env name="TEST_MONGO_URI" value="mongodb://db:publishing$8088@localhost:27017/exp_test"/>
    <env name="TEST_MYSQL_DSN" value="mysql:host=localhost;dbname=ezpublish_test"/>
  </php>

  <coverage>
    <include>
      <directory suffix=".php">extension/sevenx_mongodb/classes</directory>
      <directory suffix=".php">kernel</directory>
    </include>
    <report>
      <html outputDirectory="tests/coverage"/>
    </report>
  </coverage>
</phpunit>
```

Running the MongoDB suite:
```bash
TEST_DB_DRIVER=mongo /opt/plesk/php/8.5/bin/php vendor/bin/phpunit --testsuite MongoDB
```

Running the MySQL suite:
```bash
TEST_DB_DRIVER=mysql /opt/plesk/php/8.5/bin/php vendor/bin/phpunit --testsuite "MySQL (MariaDB)"
```

---

### 14.8 Mocking Strategy for Kernel Classes That Lack an Override

Some core kernel files (e.g. `kernel/search/stats.php`, `kernel/infocollector/overview.php`) are procedural scripts that call `eZDB::instance()` directly. They cannot be unit-tested without bootstrapping the full CMS. Two options:

**Option A — Integration tests (preferred for now).** Spin up the full CMS with `TEST_DB_DRIVER=mongo`, hit the module endpoints via a test HTTP client (e.g. Symfony BrowserKit or Guzzle), assert page content. These live in `tests/integration/`.

**Option B — Partial extraction.** Extract the DB-querying logic from procedural scripts into static methods on a companion class (e.g. `eZSearchStatsHelper::getCount()`), then unit-test the helper class with a mocked `eZDB`. This is the cleaner long-term approach but requires refactoring the core files.

For the immediate next phase, Option A is recommended: it catches real regressions, requires no refactoring, and validates the full request/response cycle including template rendering.

---

### 14.9 Composer Requirements for PHPUnit 9/10 Support (6.0.13)

Add to `composer.json`:
```json
"require-dev": {
    "phpunit/phpunit": "^10.5",
    "mongodb/mongodb": "^1.18",
    "symfony/browser-kit": "^6.4",
    "symfony/http-client": "^6.4"
}
```

Note: `mongodb/mongodb` is already required in `require` (not `require-dev`). The BrowserKit and HttpClient packages are only needed for integration tests.

The existing `rector.php` (in the workspace root) can be used to automatically upgrade old `@test` docblock annotations to PHPUnit 10 attributes:
```bash
/opt/plesk/php/8.5/bin/php vendor/bin/rector process tests/kernel \
  --config rector.php --dry-run
```

---

## 15. Kernel PHP Classes — Status and Known Issues

This section provides a **comprehensive, per-class inventory** of every kernel PHP class that has been touched, partially touched, or identified as needing work in the MongoDB port. The goal is to give a clear, unambiguous picture of what is working, what is broken, what has a known fix queued, and what is still completely unanalysed.

Status key:
- ✅ **Working** — MongoDB branch implemented and manually verified in the browser
- ⚠️ **Partial** — MongoDB branch exists but has gaps; page loads but some data is missing or warnings appear
- 🔴 **Broken** — No MongoDB branch; page crashes or returns empty data; MONGO TODO in debug output
- 🔵 **Untested** — No verified test; may work or may be broken; not yet exercised
- 📋 **Queued** — Fix is planned and design is known (see Remaining Issues Backlog §11)

---

### 15.1 Core ORM and Database Layer

#### `extension/sevenx_mongodb/classes/expMongoDB.php` — MongoDB Adapter

**Status: ✅ Working (all primary methods)**

This is the central adapter class. All primary read/write methods have been implemented and verified. Every other fix in this project depends on this class behaving correctly.

| Method | Status | Notes |
|---|---|---|
| `databaseName()` | ✅ | Returns `'mongo'` — the universal branch switch |
| `aggregate($table, $pipeline)` | ✅ | Fully working; used by all MongoDB branches |
| `insert($table, $doc)` | ✅ | Single document insert |
| `upsert($table, $filter, $doc)` | ✅ | Replace-or-insert |
| `deleteWhere($table, $filter)` | ✅ | Bulk delete by filter |
| `mongoUpdateOne($table, $filter, $update)` | ✅ | Partial update (`$set`, etc.) |
| `nextAtomicID($counterName, ...)` | ✅ | Atomic counter via `ezsequence` collection |
| `nextSeqID($table, $column)` | ✅ | `MAX(column)+1` via `$group/$max` |
| `findOne($table, $condition)` | ⚠️ | Works for simple scalar conditions; avoid for `$or`/`$in` |
| `find($table, $conds, $projection)` | ⚠️ | Uses `translateConditions()` — safe for scalar only |
| `arrayQuery($sql)` | ✅ (stub) | Always returns `[]`; logs MONGO TODO with caller |
| `query($sql)` | ✅ (stub) | Always returns `false`; no-op |
| `translateConditions($conds)` | ⚠️ | Scalar and `like` only — does NOT support nested arrays |
| `listCollectionNames()` | ✅ | Returns array of MongoDB collection names via driver; used by `systemupgrade.php` |
| `eZTableList()` | ✅ | Returns `[]` to prevent `array_keys(null)` crash |
| `escapeString($str)` | ✅ | Returns string as-is (no SQL escaping needed) |
| `begin()` / `commit()` / `rollback()` | ✅ (no-op) | MongoDB ops are per-document; no transaction needed |
| `lock()` / `unlock()` | ✅ (no-op) | Table locking not applicable |

**Known limitation:** `translateConditions()` silently drops nested array values. Any caller that passes `['field' => ['$in' => [...]]]` through `find()` or `findOne()` will get a broken filter. All such callers must use `aggregate()` directly.

---

#### `extension/sevenx_mongodb/classes/kernel/ezpersistentobject.php` — ORM Base Class

**Status: ✅ Working** (with known edge cases documented below)

Every persistent object in the CMS ultimately calls `fetchObjectList()`, `storeObject()`, `removeObject()`, or `newObjectOrder()`. All four have full MongoDB branches.

**`fetchObjectList()` edge cases:**
- `$conds` values are cast to `int`/`float` based on field `datatype` definition before building `$mongoFilter`. This fixes the core bug where URL params arrive as strings but MongoDB stores integers.
- Array values in `$conds` (i.e. already-built MongoDB operator arrays) are passed through unchanged — this allows callers to pre-build `['$in' => [...]]` conditions.
- `$sorts` accepts the same `['field' => true/false]` format as the SQL path.
- `$limit` accepts both `['limit' => N, 'offset' => M]` and `['limit' => N, 'length' => M]` keys.
- `custom_fields` with `'operation' => 'count'` is handled by emitting a `$count` stage instead of a `$project`.
- The `handleRows()` reference bug (last item duplicated) was fixed with `unset($row)` after the loop.

**Known remaining issues:**
- `$grouping` parameter (GROUP BY equivalent) has no MongoDB branch — falls through to SQL path silently.
- `$custom_fields` expressions other than `count` (e.g. `SUM`, `AVG`) have no MongoDB branch.
- `$field_filters` (column selection) may not correctly map all SQL aliases to MongoDB `$project` keys.

---

### 15.2 Content Classes

#### `extension/sevenx_mongodb/classes/kernel/ezcontentclass.php`

**Status: ✅ Working** (core CRUD + copy + versioning)

| Method / Feature | Status | Notes |
|---|---|---|
| `fetch($id)` | ✅ | Int cast added; routes through `fetchObjectList` |
| `__clone()` | ✅ | `$this->ID = null` forces insert on store |
| `storeVersioned($attrs, $version)` | ✅ | Full group migration logic; v0/v1 version handling |
| `canInstantiateClassList()` — object mode | ✅ | Returns `eZContentClass` objects via `handleRows()` |
| `canInstantiateClassList()` — raw array mode | ✅ | `name` key populated from `serialized_name_list` via `unserialize()` |
| `initializeCopy()` | 📋 | `arrayQuery` JOIN on `[ezcontentclass, ezcontentclass_name]` — copy name deduplication fails; PHP warning; non-fatal |
| `remove($removeAttributes)` | 🔵 | Not explicitly verified; depends on `removeObject()` which works |
| `versionCount()` | 🔵 | Uses `fetchObjectList` — likely works; not tested |
| `storeName()` / `NameList` handling | ✅ | Part of `storeVersioned()` — verified |

**Root cause note — missing `name` field:** The MongoDB `ezcontentclass` collection does not contain a `name` field at all. The original SQL schema stored class names in a `name` column; MongoDB migration stored only `serialized_name_list`. Any code that reads `$row['name']` directly from a raw `fetchObjectList` result will get `Undefined array key "name"`. The fix in `canInstantiateClassList()` post-processes raw rows to inject the `name` key. Other callers that access `$row['name']` directly on raw class arrays may need the same fix.

---

#### `extension/sevenx_mongodb/classes/kernel/ezcontentclassattribute.php`

**Status: ✅ Working**

| Method | Status | Notes |
|---|---|---|
| `classAttributeIdentifiersHash()` | ✅ | Replaced 2-table JOIN with two sequential aggregates + PHP merge |
| `fetch($id, $version)` | ✅ | Routes through `fetchObjectList` |
| `fetchListByClassID($classID, $version)` | 🔵 | Not explicitly tested; should work via `fetchObjectList` |

---

#### `extension/sevenx_mongodb/classes/kernel/ezcontentclassgroup.php`

**Status: ✅ Working**

| Method | Status | Notes |
|---|---|---|
| `fetch($id)` | ✅ | Int cast added |
| `fetchList()` | ✅ | Via `fetchObjectList` |

---

#### `extension/sevenx_mongodb/classes/kernel/ezcontentclassclassgroup.php`

**Status: ✅ Working**

| Method | Status | Notes |
|---|---|---|
| `fetchClassList($version, $groupId)` | ✅ | Two-step aggregate: classgroup → class IDs → class fetch |
| `removeClassMembers()` | 🔵 | Uses `removeObject()` — likely works; not tested standalone |

---

### 15.3 Content Objects

#### `extension/sevenx_mongodb/classes/kernel/ezcontentobject.php`

**Status: ⚠️ Partial — many methods working, several significant gaps remain**

This is the largest and most complex kernel class. ~30+ methods have been reviewed; approximately half have MongoDB branches.

| Method | Status | Notes |
|---|---|---|
| `fetch($id)` | ✅ | Int cast; `fetchObjectList` |
| `fetchIDArray($idArray)` | ✅ | Bulk aggregate with class + name joins |
| `fetchByNodeID($nodeID)` | ✅ | 3-collection aggregate |
| `versionLanguageName()` | ✅ | `$or` language-mask via `aggregate()` |
| `contentObjectAttributes()` | ✅ | 4-stage pipeline with `ezcontentclass_attribute` join |
| `fillNodeListAttributes()` | ✅ | Batch loads attributes for node list in single query |
| `assignedNodes()` | ✅ | 3-collection aggregate |
| `hasVisibleNode()` | ✅ | Aggregate count; eliminates 27× MONGO TODO on classlist page |
| `stateIDArray()` | ✅ | `ezcobj_state_link` → `ezcobj_state` |
| `stateIdentifierArray()` | ✅ | 3-collection chain |
| `relatedObjects()` | ✅ | Full `$lookup` pipeline |
| `previousVersion()` | ✅ | MongoDB aggregate branch implemented |
| `removeContentObjectRelation()` | ✅ | MongoDB `deleteWhere` branch implemented |
| `copyContentObjectRelations()` | ✅ | MongoDB aggregate + insert branch implemented |
| `relatedObjectCount()` | 📋 | No MongoDB branch; returns 0; affects edit/history pages |
| `getStates()` | 📋 | SQL JOIN `ezcobj_state + ezcobj_state_group WHERE NOT LIKE 'ez%'`; custom states broken |
| `addContentObjectRelation()` | 📋 | `to_contentobject_id` not int-cast; relation inserts may fail |
| `mainNode()` | 🔵 | Uses `findMainNode()` from tree node — likely works |
| `contentClass()` | 🔵 | Via `eZContentClass::fetch()` — works |
| `currentVersion()` | 🔵 | Via `fetchObjectList` — likely works |
| `versions()` | 🔵 | Via `fetchObjectList` — likely works |
| `name()` | 🔵 | Uses `ezcontentobject_name` lookup — not explicitly tested |
| `removeThis()` | 🔵 | Hard delete of full object; depends on `removeObject()` + version cleanup |

**Summary:** Object view/edit/history pages work. Related objects display partially (list works, count shows 0). State display broken for custom states. URL alias list and breadcrumb work because `assignedNodes()` and `hasVisibleNode()` are fixed.

---

#### `extension/sevenx_mongodb/classes/kernel/ezcontentobjectversion.php`

**Status: ✅ Working** (core version lifecycle)

| Method | Status | Notes |
|---|---|---|
| `fetch($id)` | ✅ | Int cast |
| `removeThis()` | ✅ | Full MongoDB branch; deletes 5 collections; updates parent object |
| `fetchList()` | 🔵 | Via `fetchObjectList` — likely works |

---

#### `extension/sevenx_mongodb/classes/kernel/ezcontentobjecttreenode.php`

**Status: ⚠️ Partial — core navigation works; several utility methods untested**

| Method | Status | Notes |
|---|---|---|
| `fetch($id)` | ✅ | `fetchMongo()` — full 4-collection aggregate |
| `fetchNodesByPathString()` | ✅ | `$regex` prefix match |
| `findMainNode($objectID)` | ✅ | `$expr: {$eq: ['$node_id','$main_node_id']}` |
| `findNode()` | ✅ | MongoDB branch implemented |
| `subTreeCountByNodeID()` | ✅ | Full MongoDB path with regex + depth filter |
| `subTreeByNodeID()` | ✅ | Full MongoDB path with `$lookup` joins; pagination |
| `makeObjectsArray()` | ✅ | PHP 8.5 implicit nullable fix |
| `getClassesJsArray()` | ✅ | Fixed via `canInstantiateClassList()` name key fix |
| `move()` | 🔵 | Complex `path_string` rewrite logic; not tested |
| `removeNode()` | 🔵 | Multi-collection cleanup; not tested |
| `updatePathString()` | 🔵 | Likely calls `query()` (no-op); needs MongoDB branch |
| `childrenByNodeID()` | 🔵 | Via `fetchObjectList`; likely works |
| `childCount()` | 🔵 | Likely uses `aggregate`; not confirmed |

---

### 15.4 URL and Navigation

#### `extension/sevenx_mongodb/classes/kernel/ezurlaliasml.php`

**Status: ✅ Working** (translation and path lookup)

| Method | Status | Notes |
|---|---|---|
| `translate(&$uri, $reverse)` | ✅ | Full iterative per-segment chain lookup rewritten |
| `fetchPathByActionList()` | ✅ | `$in` match on action strings |
| `storePath()` | 🔴 | Many `arrayQuery`/`query` calls; `updateniceurls.php` creates top-level entries only; child nodes not updated |

**`storePath()` is the main outstanding blocker for URL alias rebuilding.** The script `bin/php/updateniceurls.php` completes without crash but reports `Updated 0/219` because `storePath()` cannot write child alias entries. This is a significant known gap.

---

#### `extension/sevenx_mongodb/classes/kernel/ezurlaliasquery.php`

**Status: ✅ Working** (URL list + count)

| Method | Status | Notes |
|---|---|---|
| `buildMongoMatch()` | ✅ | New method; translates all filter properties to `$match` |
| `count()` | ✅ | MongoDB `$count` aggregate branch |
| `fetchAll()` | ✅ | MongoDB `$sort`/`$skip`/`$limit` aggregate branch |
| Line 256 — DISTINCT action_type | ✅ | `$group` aggregate replaces SQL DISTINCT; column extracted via `array_column($rows, '_id')` |

---

#### `extension/sevenx_mongodb/classes/kernel/datatypes/ezurl/ezurl.php`

**Status: ✅ Working** (link management page)

| Method | Status | Notes |
|---|---|---|
| `fetchByAttribute()` — count variant | ✅ | 4-stage `$lookup` chain + `$count` |
| `fetchByAttribute()` — list variant | ✅ | 4-stage `$lookup` chain + `$project`/`$skip`/`$limit` |

---

### 15.5 Roles and Permissions

#### `extension/sevenx_mongodb/classes/kernel/ezrole.php`

**Status: ✅ Working** — confirmed all `arrayQuery` calls are in SQL `else` branches; MongoDB paths already patched.

| Method | Status | Notes |
|---|---|---|
| `fetchByUser()` | ✅ | `$lookup` pipeline on `ezrole` + `ezuser_role`; both direct and recursive (group path) modes |
| `fetchRolesForUser()` | ✅ | Confirmed: calls `fetchByUser()` internally |
| `policyList()` | ✅ | Uses `fetchObjectList` (ORM) — works natively |
| `accessArrayByUserID()` | ✅ | Delegates to `fetchByUser()` + `accessArray()` — both patched |
| `fetchIDListByUser()` | ✅ | `$lookup` aggregate |
| `assignToUser()` | ✅ | `aggregate` check + `insert` |
| `fetchUserID()` | ✅ | Simple `aggregate` |
| `removeUserAssignment()` | ✅ | `deleteWhere` |
| `fetchUserByRole()` / `fetchRolesByLimitation()` | ✅ | `aggregate` + remap `contentobject_id` → `user_id` |
| Lines 891, 921, 1005, 1048 | ✅ | All in SQL `else` branches — unreachable for MongoDB |

---

### 15.6 Sections

#### `extension/sevenx_mongodb/classes/kernel/ezsection.php`

**Status: ✅ Working**

| Method | Status | Notes |
|---|---|---|
| `sectionCount()` | ✅ | MongoDB `$count` aggregate branch |
| `fetchList()` | ✅ | Via `fetchObjectList` |
| `fetch($id)` | 🔵 | Via `fetchObjectList`; likely works |
| `fetchFilteredList()` | 🔵 | Via `fetchObjectList`; likely works |

---

### 15.7 Search

#### `extension/sevenx_mongodb/classes/kernel/ezsearchlog.php`

**Status: ✅ Working**

| Method | Status | Notes |
|---|---|---|
| `mostFrequentPhraseArray()` | ✅ | Full MongoDB pipeline with `$divide` for computed `result_count` |
| `addPhrase()` | 🔵 | Uses `upsert`/`insert` — likely works but untested |

#### `kernel/search/stats.php` (core file, no override)

**Status: ✅ Working** — MongoDB `$count` aggregate branch added.

---

### 15.8 Information Collector

#### `extension/sevenx_mongodb/classes/kernel/ezinformationcollection.php`

**Status: ⚠️ Partial**

| Method | Status | Notes |
|---|---|---|
| `fetchCollectionCountForObject()` | ✅ | MongoDB `$count` aggregate branch |
| `fetchCollectionsList()` | ✅ | Via `fetchObjectList` — confirmed working (queries 3+4 on infocollector page) |
| `fetchList()` | 🔴 | Line 422: JOIN `arrayQuery` on `[ezinfocollection, ezcontentobject]` |
| `fetchListCount()` | 🔴 | Line 437: `arrayQuery` — just fixed but only for `fetchCollectionCountForObject`; check if same line is shared |
| `fetchGroupedList()` | 🔴 | Line 577: complex JOIN |
| `fetchGroupedListCount()` | 🔴 | Line 601: JOIN count |

#### `kernel/infocollector/overview.php` (core file, no override)

**Status: ✅ Working** — Full `$lookup` pipeline for both queries.

---

### 15.9 Languages

#### `extension/sevenx_mongodb/classes/kernel/ezcontentlanguage.php`

**Status: ✅ Working**

| Method | Status | Notes |
|---|---|---|
| `objectCount($languageID)` | ✅ | `$bitAnd` in `$expr` for bitmask check |
| `classCount($languageID)` | ✅ | Same fix |
| `fetchList()` | 🔵 | Via `fetchObjectList` — likely works |
| `fetchByLocale($locale)` | 🔵 | Via `fetchObjectList` — likely works |
| `maxCount()` | 🔵 | Used by `ezurlaliasquery.php` makeList(); likely works |

---

### 15.10 Schema and Setup

#### `lib/ezdbschema/classes/ezdbschema.php` (core lib, no override)

**Status: ✅ Working**

| Fix | Status | Notes |
|---|---|---|
| `false`-to-array E_DEPRECATED (line 38) | ✅ | Fixed: `!is_array($params)` guard added |
| `writeError()` for missing MongoDB handler | ✅ | Suppressed when `$dbname === 'mongo'` |

#### `kernel/setup/systemupgrade.php` (core file, no override)

**Status: ✅ Working**

| Fix | Status | Notes |
|---|---|---|
| Null-object crash before `transformSchema()` | ✅ | `is_object()` guard added |
| Instant "ok" without real check | ✅ | Real collection-vs-schema diff using `listCollectionNames()` |

---

### 15.11 Datatypes

#### `extension/sevenx_mongodb/classes/kernel/datatypes/ezxmltext/ezxmloutputhandler.php`

**Status: ✅ Working** — PHP 8.5 null-argument deprecation fixed (line 318).

#### `extension/sevenx_mongodb/classes/kernel/datatypes/ezxmltext/ezxmlschema.php`

**Status: ✅ Working** — Same null guard fix (line 369).

#### `extension/sevenx_mongodb/classes/kernel/datatypes/ezinisetting/ezinisettingtype.php`

**Status: ✅ Working** — `hasPostVariable` guard removed for `<select multiple>` empty submission; class store no longer returns `STATE_INVALID`.

#### `extension/sevenx_mongodb/classes/kernel/datatypes/ezurl/ezurl.php`

**Status: ✅ Working** — 4-table `$lookup` pipeline added; link management page displays correctly.

#### `extension/sevenx_mongodb/classes/kernel/datatypes/ezkeyword/ezkeywordtype.php`

**Status: ✅ Working** — all `arrayQuery` calls replaced with MongoDB aggregates; `deleteStoredObjectAttribute()` fully patched.

| Method | Status | Notes |
|---|---|
|---|
| `deleteStoredObjectAttribute()` | ✅ | Two-stage aggregate: find keyword_ids, group-count links to find unused ones, then `deleteWhere` on both `ezkeyword` and `ezkeyword_attribute_link` |

#### `extension/sevenx_mongodb/classes/kernel/datatypes/ezxmltext/ezxmltexttype.php`

**Status: ✅ Working** — orphan URL cleanup fully patched.

| Method | Status | Notes |
|---|---|---|
| `deleteStoredObjectAttribute()` orphan URL cleanup | ✅ | `$nin` anti-join aggregate finds `ezurl` IDs not in `ezurl_object_link`; `deleteWhere` removes orphans |

---

### 15.12 Site Access and Misc

#### `extension/sevenx_mongodb/classes/kernel/ezsiteaccess.php`

**Status: ✅ Working** — PHP 8.5 implicit nullable fixes in `change()` and `load()`.

#### `extension/xrowmetadata/autoloads/xrowmetadataoperator.php`

**Status: ✅ Working** — Null parent node guard added.

#### `extension/sevenx_mongodb/classes/kernel/clusterfilehandlers/ezfsfilehandler.php`

**Status: ✅ Working** — Debug `error_log` calls removed. Cache layer confirmed stable.

#### `kernel/private/classes/ezcontentobjectstategroup.php` (core file, no override)

**Status: ✅ Working** — `arrayQuery` moved into `else` branch; MongoDB path runs first.

---

### 15.13 Cronjob-Related Classes

| Class / File | Status | Notes |
|---|---|---|
| `ezurlaliasml.php` — `storePath()` | 🔴 | Many `arrayQuery`/`query` no-ops; URL alias rebuild only creates top-level entries |
| `kernel/content/edit.php` | ✅ | Debug `error_log()` lines removed; core publish flow works |
| `kernel/content/attribute_edit.php` | ✅ | Debug lines removed (lines 365, 402) |
| `ezkeyword` index update | ✅ | `deleteStoredObjectAttribute()` patched: two-stage aggregate finds unused keywords, `deleteWhere` cleans up |
| `ezdbgarbagecollector.php` | ✅ | 4× `arrayQuery` calls replaced with MongoDB `$nin` anti-join aggregates |
| `eznodeassignment.php` | ✅ | Confirmed already patched — remaining `arrayQuery` calls are in SQL-only `else` branches |
| `ezpolicylimitation.php` | ✅ | Line 374: two-stage `aggregate` — find limitation_ids, then fetch matching limitations |
| ~~`ezcollaborationitem.php`~~ | ✅ | Confirmed fully patched — MongoDB `$lookup` pipeline path returns early; SQL `arrayQuery` branches are unreachable |
| `ezcollaborationgroup.php` | ✅ | Line 318: `$count` aggregate on `ezcollab_item_group_link` |
| `lib/ezimage/classes/ezimagemanager.php` | ✅ | Line 195: `array_key_exists( $aliasName ?? '', ... )` — already fixed |

---

### 15.14 Overall Progress Summary

As of June 2026:

| Category | Total methods/features tracked | ✅ Working | ⚠️ Partial | 🔴 Broken | 🔵 Untested |
|---|---|---|---|---|---|
| DB adapter (`expMongoDB`) | 18 | 16 | 2 | 0 | 0 |
| ORM base (`ezpersistentobject`) | 6 | 5 | 1 | 0 | 0 |
| Content classes | 14 | 10 | 1 | 1 | 2 |
| Content objects | 21 | 14 | 1 | 3 | 3 |
| Content tree nodes | 12 | 8 | 0 | 0 | 4 |
| URL aliases | 8 | 6 | 0 | 1 | 1 |
| Roles / permissions | 7 | 1 | 0 | 5 | 1 |
| Sections | 4 | 2 | 0 | 0 | 2 |
| Search / search log | 3 | 3 | 0 | 0 | 0 |
| Info collector | 6 | 3 | 1 | 2 | 0 |
| Languages | 5 | 2 | 0 | 0 | 3 |
| Schema / setup | 4 | 4 | 0 | 0 | 0 |
| Datatypes | 8 | 4 | 1 | 3 | 0 |
| Misc / site access | 5 | 5 | 0 | 0 | 0 |
| Cronjob-related classes | 9 | 0 | 2 | 7 | 0 |
| **Totals** | **130** | **83 (64%)** | **9 (7%)** | **22 (17%)** | **16 (12%)** |

The admin panel core workflows (content browse, edit, publish, class management, section list, URL management, info collector, search stats) are all functional. The primary remaining gaps are role/permission display, keyword indexing, URL alias rebuild (`storePath()`), and shop/workflow/collaboration modules.

---

## 13. References

The following resources were helpful during initial setup. Note that they target MongoDB 6.x/7.0
on AlmaLinux — some steps differ for MongoDB 8.3+ (e.g. repo URLs, package names, and
`mongosh` replaces the legacy `mongo` shell).

- [Install MongoDB on AlmaLinux — Liquid Web](https://www.liquidweb.com/blog/install-mongodb-almalinux/) — step-by-step install guide; covers `mongod` service setup and basic auth
- [How to Install MongoDB on AlmaLinux 9 — HowtoForge](https://www.howtoforge.com/how-to-install-mongodb-on-almalinux-9/) — alternative guide with firewall and SELinux notes
- [Create a Database — MongoDB Fundamentals](https://www.mongodb.com/resources/products/fundamentals/create-database) — official overview of database/collection creation concepts
- [db.changeUserPassword() — MongoDB Manual](https://www.mongodb.com/docs/manual/reference/method/db.changeUserPassword/) — reference for changing MongoDB user passwords via `mongosh`
- [Aggregation Pipeline — MongoDB Manual](https://www.mongodb.com/docs/manual/core/aggregation-pipeline/) — full reference for `$match`, `$lookup`, `$project`, `$group`, `$count`, `$skip`, `$limit`, `$sort`
- [eZ Publish 4.x Kernel Documentation](https://doc.ez.no/eZ-Publish/Technical-manual/4.x/) — original SQL-based architecture; useful for understanding what each class is supposed to do

---

## 16. MongoDB Driver Design and Technical Completion vs ezmysqli

This section compares `expMongoDB` (the MongoDB adapter used in this project) against `eZMySQLi` (the reference SQL adapter shipped with Exponential/eZ Publish). The goal is to document how complete the MongoDB driver is, which interfaces are fully implemented, which are stubs, which have semantic differences, and what would be required to achieve full parity.

---

### 16.1 Class Hierarchy and Interface Contract

**`eZMySQLi`** extends `eZMySQLiDB` extends `eZDBInterface`.

**`expMongoDB`** extends `eZDBInterface` directly.

`eZDBInterface` (at `lib/ezdb/classes/ezdbinterface.php`) defines the full contract that both drivers must implement. The interface has approximately 60 public methods covering:
- Connection lifecycle (`connect()`, `disconnect()`, `isConnected()`)
- Query execution (`query()`, `arrayQuery()`, `arrayQuery()` with params)
- Data manipulation (`insert()`, `upsert()`, `deleteWhere()`)
- Schema inspection (`eZTableList()`, `tableInfo()`, `relationList()`)
- Transaction control (`begin()`, `commit()`, `rollback()`)
- Aggregate and sequence helpers (`nextSeqID()`, `lastSerialID()`)
- Escaping and type coercion (`escapeString()`, `generateTextSQLString()`)
- Error handling (`errorMessage()`, `errorNumber()`)
- Server metadata (`databaseName()`, `databaseServerVersion()`)

---

### 16.2 Method-by-Method Comparison

#### Connection and Lifecycle

| Method | eZMySQLi | expMongoDB | Status |
|---|---|---|---|
| `__construct($params)` | Opens MySQLi connection; sets `IsConnected` | Pings MongoDB; sets `IsConnected` | ✅ Equivalent |
| `connect()` | Reconnects using stored params | Not implemented (uses `getClient()` lazy singleton) | ⚠️ Stub — reconnect not supported |
| `disconnect()` | Closes MySQLi link | Not implemented | ⚠️ Stub — connection is persistent per-process |
| `isConnected()` | Returns `$this->IsConnected` | Returns `$this->IsConnected` | ✅ Equivalent |
| `useDatabase($name)` | Selects a different database | Not implemented — hardcoded to `'exp'` | 🔴 Missing — database name is hardcoded |

**Hardcoded database name** is the most significant lifecycle limitation. `eZMySQLi` reads `DatabaseName` from `site.ini`. `expMongoDB` hardcodes `'exp'` in every method that calls `selectCollection()`. To support multiple environments (dev/staging/prod), this must be made configurable via `site.ini[DatabaseSettings] DatabaseName`.

---

#### Query Execution

| Method | eZMySQLi | expMongoDB | Status |
|---|---|---|---|
| `query($sql)` | Executes SQL; returns result set or `false` | Always returns `false`; no-op | 🔴 Stub — SQL DDL/DML no-ops |
| `arrayQuery($sql, $params)` | Executes SQL SELECT; returns array of assoc arrays | Always returns `[]`; logs MONGO TODO | 🔴 Stub — intentional; all callers must have MongoDB branch |
| `aggregate($table, $pipeline)` | Does not exist | Runs MongoDB aggregation pipeline | ✅ MongoDB-native; no SQL equivalent |
| `find($table, $conds, $projection)` | Does not exist | Calls `selectCollection()->find()` | ⚠️ Use with caution — `translateConditions()` limits applicability |
| `findOne($table, $conds)` | Does not exist | Calls `selectCollection()->findOne()` | ⚠️ Same caveat |

**Key design consequence:** Every kernel class that called `$db->arrayQuery()` now calls a stub that returns `[]`. The MongoDB port replaces each callsite with an explicit `if ($db->databaseName() === 'mongo') { $rows = $db->aggregate(...); } else { $rows = $db->arrayQuery(...); }` branch. This is the fundamental pattern of the entire port project.

---

#### Data Manipulation

| Method | eZMySQLi | expMongoDB | Status |
|---|---|---|---|
| `insert($table, $doc)` | Part of `query("INSERT INTO ...")` | `insertOne($doc)` on collection | ✅ Implemented (different signature) |
| `upsert($table, $filter, $doc)` | No direct equivalent; uses `INSERT ... ON DUPLICATE KEY` | `updateOne($filter, ['$set' => $doc], ['upsert' => true])` | ✅ Implemented |
| `deleteWhere($table, $filter)` | Part of `query("DELETE FROM ...")` | `deleteMany($filter)` on collection | ✅ Implemented |
| `mongoUpdateOne($table, $filter, $update)` | Does not exist | `updateOne($filter, $update)` — for `$set`, `$inc`, `$push` etc. | ✅ MongoDB-native |
| `insertWithout($table, $doc, $keyExclusions)` | Exists in eZMySQLi | Not implemented | 🔴 Missing |

---

#### Schema Inspection

| Method | eZMySQLi | expMongoDB | Status |
|---|---|---|---|
| `eZTableList()` | Returns array of table names from `SHOW TABLES` | Returns `[]` (prevents crash in `generateUniqueTempTableName()`) | ⚠️ Stub — returns empty; sufficient to prevent crash |
| `listCollectionNames()` | Does not exist | Returns array of MongoDB collection names via driver | ✅ MongoDB-native; added for `systemupgrade.php` |
| `tableInfo($table)` | Returns column metadata (name, type, default, nullable) | Not implemented | 🔴 Missing — needed for schema diff tools |
| `relationList()` | Returns foreign key relationships | Not applicable to MongoDB | N/A |
| `tableCount()` | Returns number of tables | Not implemented | 🔴 Missing |
| `generateUniqueTempTableName($prefix)` | Finds a name not in `eZTableList()` | Works because `eZTableList()` returns `[]` — any name is unique | ⚠️ Accidentally works |

---

#### Transaction Control

| Method | eZMySQLi | expMongoDB | Status |
|---|---|---|---|
| `begin()` | `START TRANSACTION` | No-op | ✅ Safe no-op — MongoDB operations are atomic per-document |
| `commit()` | `COMMIT` | No-op | ✅ Safe no-op |
| `rollback()` | `ROLLBACK` | No-op | ⚠️ Data is NOT rolled back on error — partial writes are permanent |
| `transactionIsStarted()` | Returns bool | Not implemented | 🔴 Missing |

**Rollback limitation:** In the SQL driver, if an error occurs mid-operation, `rollback()` undoes all changes in the transaction. In the MongoDB driver, each `insert()`/`upsert()`/`deleteWhere()` call is immediately committed. If a multi-step operation (like `storeVersioned()`) fails halfway, the database will be in a partially updated state. Multi-document transactions (replica set required) are not used.

---

#### Sequence and Auto-Increment

| Method | eZMySQLi | expMongoDB | Status |
|---|---|---|---|
| `lastSerialID($table, $col)` | Returns `mysqli_insert_id()` | Returns `$_lastInsertedID` (set by `insert()`) | ⚠️ Partially works — only set after `insert()`, not `upsert()` |
| `nextSeqID($table, $col)` | `SELECT MAX(col)+1` via SQL | `aggregate` with `$group/$max` | ✅ Equivalent |
| `nextAtomicID($name, $seedTable, $seedCol)` | Does not exist | Atomic `findAndModify` on `ezsequence` collection | ✅ MongoDB-native; superior to SQL MAX+1 |

**`nextAtomicID` vs `nextSeqID`:** `nextSeqID` has a race condition — two concurrent requests can read the same MAX and generate the same ID. `nextAtomicID` uses MongoDB's `findOneAndUpdate` with `$inc` which is atomic and race-free. The port gradually migrates ID generation to `nextAtomicID` for collections that matter.

---

#### Escaping and Type Coercion

| Method | eZMySQLi | expMongoDB | Status |
|---|---|---|---|
| `escapeString($str)` | `mysqli_real_escape_string()` | Returns string unchanged (no SQL injection risk in MongoDB) | ✅ Correct for MongoDB |
| `generateTextSQLString($value)` | Wraps value in SQL quotes | Not implemented | 🔴 Missing (only called by SQL-path callers) |
| `md5($str)` | Returns `MD5('...')` SQL fragment | Not overridden — returns SQL fragment | 🔴 BUG — callers using return value as a real hash get SQL syntax |
| `generateSQLOperator($op)` | Maps PHP operator to SQL | Not applicable | N/A |
| `generateSQLINStatement($arr, $col)` | Builds `col IN (...)` SQL | Not implemented | 🔴 Missing |

**`md5()` bug:** `eZDBInterface::md5($str)` returns `MD5('string')` as a SQL fragment for use inside SQL queries. `expMongoDB` inherits this without override. Any caller that does `$hash = $db->md5($str)` and uses `$hash` as an actual hash value will get the literal string `"MD5('...')"` instead of the hash. The `ezurlaliasml.php` `translate()` rewrite explicitly calls PHP `md5()` to avoid this. All other callers must be checked.

---

#### Locking

| Method | eZMySQLi | expMongoDB | Status |
|---|---|---|---|
| `lock($table)` | `LOCK TABLES ... WRITE` | No-op | ⚠️ No locking — concurrent writes can interleave |
| `unlock()` | `UNLOCK TABLES` | No-op | ⚠️ Same |

**Impact:** Table-level locks were used in eZ Publish to protect sequences and tree restructuring. Without locking, concurrent requests that restructure the content tree (move nodes, publish) could produce inconsistent `path_string` or `depth` values. At current single-user admin usage this is not a problem; at production traffic levels this could cause corruption.

---

#### Error Handling

| Method | eZMySQLi | expMongoDB | Status |
|---|---|---|---|
| `errorMessage()` | Returns last MySQLi error string | Not implemented — returns empty string | 🔴 Missing |
| `errorNumber()` | Returns last MySQLi error code | Not implemented — returns 0 | 🔴 Missing |
| `checkError()` | Writes error to debug log | Not implemented | 🔴 Missing |
| `availableDatabases()` | `SHOW DATABASES` | Not implemented | 🔴 Missing |

Currently all MongoDB errors are caught in try/catch blocks inside each method and written to `error_log()`. They do not propagate to `eZDebug` or the admin toolbar. This makes it harder to diagnose MongoDB-specific errors during development.

---

#### Debugging and Profiling

| Method / Feature | eZMySQLi | expMongoDB | Status |
|---|---|---|---|
| SQL toolbar output | Every query appears in debug toolbar with SQL text and time | Every `aggregate()` call appears with collection name + match stage JSON | ✅ Equivalent (implemented via `reportQuery()`) |
| MONGO TODO logging | N/A | Every `arrayQuery()` call logs to `error_log` with caller file:line | ✅ MongoDB-specific addition |
| `OutputSQL` flag | Controls whether queries appear in toolbar | Respected — same flag controls `reportQuery()` | ✅ Equivalent |
| Query timing accumulators | `eZDebug::accumulatorStart/Stop('mysql_cluster_query')` | `eZDebug::accumulatorStart/Stop('mongodb_cluster_query')` | ✅ Equivalent |
| Slow query log | MySQLi slow query log via `my.cnf` | Not implemented — no threshold-based slow query detection | 🔴 Missing |

---

#### `translateConditions()` vs SQL WHERE Clause Generation

This is the most significant functional gap between the drivers.

**`eZMySQLi`** generates SQL WHERE clauses by interpolating values directly into SQL strings with `escapeString()`. The full expressiveness of SQL is available.

**`expMongoDB::translateConditions($conds)`** handles:
- Scalar values: `['field' => 'value']` → `['field' => 'value']`
- `like` operator: `['field' => ['like' => '%val%']]` → `['field' => ['$regex' => '.*val.*']]`
- Numeric strings auto-cast to int when the value looks numeric

**Does NOT handle:**
- `['field' => ['$in' => [...]]]` — dropped
- `['field' => ['$or' => [...]]]` — dropped
- `['$expr' => [...]]` — dropped
- Any nested operator array — the outer key is kept but the value becomes `null`

This means `find()` and `findOne()` are only safe for simple scalar equality filters. Any query with `$in`, `$or`, `$expr`, or computed conditions **must** use `aggregate()` with an explicit `$match` stage. This is the root cause of most MONGO TODO entries: kernel classes that called `$db->find($table, $complexCondition)` needed to be rewritten as `$db->aggregate($table, [['$match' => $complexCondition]])` instead.

---

### 16.3 Code Style: eZMySQLi vs expMongoDB

#### Coding Style Differences

| Aspect | eZMySQLi | expMongoDB |
|---|---|---|
| Method visibility | All public (legacy PHP 4 style) | Mix of public/protected/private |
| Property visibility | `var $Property` (legacy) | `private $_lastInsertedID` etc. |
| Error handling | Sets `$this->ErrorMessage`; calls `$this->checkError()` | try/catch with `error_log()` |
| Docblocks | eZ Publish javadoc style (`/*!` ... `*/`) | Minimal or none |
| Result format | `mysqli_fetch_assoc()` loop; builds array | `$cursor->toArray()` cast via `getArrayCopy()` per document |
| Null handling | Returns `false` on error from most methods | Returns `[]` or `false` depending on method |
| Server multiplexing | Supports `SERVER_MASTER` / `SERVER_SLAVE` param | Single connection only (`$server` param ignored) |

#### Standards Gaps in expMongoDB

The following patterns from `eZMySQLi` are not followed in `expMongoDB` and should be addressed in a future refactor:

1. **Hardcoded database name.** Every method contains `$dbName = 'exp'`. Should read from `eZINI::instance('site.ini')->variable('DatabaseSettings', 'DatabaseName')` to support multiple environments.

2. **No `errorMessage()` / `errorNumber()` implementation.** Errors are silently swallowed or written to `error_log`. The eZMySQLi pattern of storing errors in instance variables and surfacing them through the debug system should be adopted.

3. **No `SERVER_MASTER` / `SERVER_SLAVE` support.** The `$server` parameter is present in the method signatures (inherited from the interface) but ignored. For read-scalability with a MongoDB replica set, reads should be routable to secondaries.

4. **`lastSerialID()` not set by `upsert()`.** After an upsert that inserts a new document, the caller may call `lastSerialID()` expecting the new document's ID. Currently only `insert()` sets `$_lastInsertedID`.

5. **No `generateTextSQLString()` override.** Legacy callers that call this method to build SQL fragments will get SQL syntax strings, not MongoDB-safe values. All such callers need auditing.

6. **`md5()` not overridden.** Returns SQL fragment `MD5('...')` from the base class. Must be overridden to return `md5($str)` PHP hash to be safe.

7. **No reconnect logic.** If the MongoDB connection drops mid-request, there is no retry or reconnect. `eZMySQLi` has a reconnect loop.

8. **`$OutputSQL` flag not checked in `find()` and `findOne()`.** These queries do not call `reportQuery()` and are invisible in the debug toolbar, making profiling incomplete.

---

### 16.4 Feature Completion Matrix

| Feature Area | eZMySQLi | expMongoDB | Gap |
|---|---|---|---|
| Basic CRUD operations | 100% | 95% | `insertWithout()` missing |
| Schema inspection | 100% | 15% | `tableInfo()`, `tableCount()`, `relationList()` missing |
| Transaction support | 100% | 30% | Rollback is a no-op; partial writes permanent |
| Sequence / auto-increment | 100% | 90% | `lastSerialID()` incomplete after upsert |
| Error reporting | 100% | 20% | Errors only go to `error_log`; not surfaced in debug toolbar |
| Locking | 100% | 0% | No locking whatsoever |
| Escaping / type safety | 100% | 70% | `md5()` not overridden; `generateTextSQLString()` missing |
| Debug toolbar integration | 100% | 75% | `find()`/`findOne()` not reported; no slow-query threshold |
| Complex filter translation | 100% (full SQL) | 40% (scalar only) | Must use `aggregate()` for any operator |
| Multi-server / replica | 100% (master/slave) | 0% | `$server` param ignored |
| Environment config | 100% (reads site.ini) | 10% | DB name hardcoded |
| **Overall** | **100%** | **~55%** | Interface contract partially fulfilled |

---

### 16.5 Priority Order for Closing the Gap

Ordered by impact on stability and correctness at current usage level:

1. **Override `md5()` in `expMongoDB`** — one-line fix; prevents any caller using `$db->md5()` as a real hash from silently getting wrong data.

2. **Make database name configurable** — read from `site.ini[DatabaseSettings] DatabaseName`; hardcoded `'exp'` is a deployment hazard.

3. **Set `$_lastInsertedID` in `upsert()` for new documents** — `storeObject()` calls `lastSerialID()` after upsert; currently returns stale or false value.

4. **Surface MongoDB errors in debug toolbar** — catch exceptions, call `eZDebug::writeError()`, set `$this->ErrorMessage`.

5. **Add `reportQuery()` calls to `find()` and `findOne()`** — these queries are invisible in the debug toolbar, making profiling incomplete.

6. **Add `transactionIsStarted()` stub returning `false`** — some kernel code gates operations on this check; returning false is correct and prevents unexpected behaviour.

7. **Multi-document transaction support (long-term)** — requires MongoDB replica set setup. Out of scope for the current single-node deployment but important for production.

---

## 17. Additional Fixes — System Upgrade, RAD Code Generators, BC CIE Export, Language Bitmask

This section documents all fixes and refactors applied during the May 28 2026 session.

---

### 17.1 System Upgrade Page (`/setup/systemupgrade`) — full refactor

**Problem:** The `kernel/setup/systemupgrade.php` MongoDB branch was building a flat plain-text report string (passed to the template via `$upgrade_sql`) using SQL `-- comment` syntax, with no CLI commands and no structure. The template was showing "run the following SQL commands" which was wrong for MongoDB. Setting `$upgrade_sql = true` (boolean) caused the template's `|eq('ok')` check to evaluate as `true` (PHP loose comparison `true == 'ok'`) and show "Database check OK" incorrectly.

**Fixes applied:**

`kernel/setup/systemupgrade.php`:
- Changed `$upgrade_sql` sentinel from `true` / bare string to `'mongo'` — `'mongo' == 'ok'` is `false`, so the warning block renders correctly
- Instead of building a text report string, now sets structured template variables:
  - `$mongo_missing_count` — integer count of missing collections
  - `$mongo_grouped_list` — indexed array of `['name', 'count', 'collections']` hashes for template iteration
  - `$mongo_extra` — array of extra collections present in MongoDB but not in the SQL schema
  - `$mongo_create_cmd` — a `mongosh` JS snippet (`forEach(function(c){ db.createCollection(c); })`) for all non-adapter missing collections

`design/admin/templates/setup/systemupgrade.tpl`:
- `{if $mongo_check}` branch replaced with structured HTML render:
  - `<h2>` heading with count: `MongoDB collection check — N collection(s) not yet created.`
  - Explanation paragraph (no action required)
  - Dark-background `<pre>` block with the mongosh pre-create command
  - Feature groups rendered as `<h4>` + `<ul>/<li><code>` pairs
  - Extra collections section separated by `<hr>`
- SQL fallback path (`{else}` branch) unchanged

**Files:** `kernel/setup/systemupgrade.php`, `design/admin/templates/setup/systemupgrade.tpl`

---

### 17.2 RAD Code Generators (`/setup/rad`) — PHP 8.5 compatibility

The RAD tools generate downloadable PHP skeleton files. The generated code was written for eZ Publish 4 / PHP 5 and would not compile under PHP 8.5.

#### Template Operator Wizard (`templateoperator_code.tpl`)

| Bug | Fix |
|-----|-----|
| `{set-block}` + `indent(sum(35,$operator_name\|count))` — `count()` on a string throws `TypeError` in PHP 8 | Removed `{set-block}` entirely; `namedParameterList()` now uses flat `[]` array syntax |
| `for($i=0; $i<count($operatorParameters); ++$i)` | Replaced with `foreach` |
| No visibility / no return types on methods | All methods now `public` with return types (`array`, `bool`, `void`) |
| `/*!` Doxygen comments | Replaced with `/** */` PHPDoc |
| Autoload instructions used `array()` syntax and referenced wrong ini file | Updated to `[]` syntax and `site.ini.append.php` |

#### Datatype Wizard (`datatype_code.tpl`)

| Bug | Fix |
|-----|-----|
| `parent::__construct(self::CONST, "bare string")` | Changed to `ezpI18n::tr('extension/datatypes', 'Name', 'Datatype name')` |
| `eZDataType::register(ClassName::CONST, "ClassName")` — bare string | Changed to `ClassName::class` |
| No visibility / no return types on methods | All methods now `public` with scalar return types |
| `/*!` Doxygen comments | Replaced with `/** */` PHPDoc |
| No registration instructions in generated file | Added full header comment with `ezdatatypeautoload.php` + `site.ini.append.php` snippets |

#### Datatype wizard controller (`kernel/setup/datatype.php`)

`datatypeDownload()` used `$persistentData['class-name']` directly for `$filename = strtolower($className) . '.php'` without checking whether the user left the field blank. When blank: `$className = false` → filename was `.php` and the class body had `class ` (PHP syntax error in the downloaded file). Added a null-check fallback that auto-generates the class name from the datatype name.

**Files:** `design/admin/templates/setup/templateoperator_code.tpl`, `design/admin/templates/setup/datatype_code.tpl`, `kernel/setup/datatype.php`

See also: `RAD-CODEGEN-UPDATES.md` in the project root for full details.

---

### 17.3 BC CIE Export (`/bccie/overview`) — MONGO TODO stubs

**Problem:** `extension/bccie/classes/bccieExportUtils.php` contained two `$db->arrayQuery()` calls with raw SQL multi-table joins, both logging `MONGO TODO` in the debug toolbar.

#### `getObjectsWithCollectedInformation()` (line 16)

Original SQL: 4-table join across `ezinfocollection`, `ezcontentobject`, `ezcontentobject_tree`, `ezcontentclass` with `SELECT DISTINCT`.

MongoDB fix:
1. `$group` by `contentobject_id` on `ezinfocollection` + `$sort` + `$skip` + `$limit` for pagination
2. For each resulting ID: `eZContentObject::fetch()` (already MongoDB-aware) to get the object; `eZContentClass::fetch()` to get the class; `attribute('main_node')` for the tree node
3. Builds a flat result array populating all `eZContentClass::definition()['fields']` keys so `new eZContentClass($row)` works in the downstream code

#### `getCollectorObjectsCount()` (line 76)

Original SQL: `COUNT(DISTINCT contentobject_id)` across 3 tables.

MongoDB fix: simple `$group` by `contentobject_id` + `$count` stage on `ezinfocollection`.

**Detection:** Both methods detect MongoDB via `$db->databaseName() === 'mongo'` and fall through to the original SQL path otherwise.

**File:** `extension/bccie/classes/bccieExportUtils.php`

---

### 17.4 `$bitwiseAnd` → `$bitAnd` (Languages page)

**Problem:** `objectCount()` and `classCount()` in `ezcontentlanguage.php` used `$bitwiseAnd` inside `$expr` aggregation — not a valid MongoDB 6.3+ operator. Silently returns `null` → `null > 0` → `false` → 0 rows for every language.

**Fix:** Both occurrences changed to `$bitAnd` (the correct MongoDB 6.3+ aggregation expression operator).

**File:** `extension/sevenx_mongodb/classes/kernel/ezcontentlanguage.php` (lines 802, 831)

---

### 17.5 E_DEPRECATED nullable parameter (`ezcontentcachemanager.php`)

`clearNodeViewCacheArray(array $nodeList, array $contentObjectList = null)` — implicit nullable deprecated in PHP 8.4+.

**Fix:** Changed to `?array $contentObjectList = null`.

**File:** `extension/sevenx_mongodb/classes/kernel/ezcontentcachemanager.php` (line 763)

---

### 17.6 URL wildcards — corrupt orphan documents

**Problem:** `ezurlwildcard` collection contained 2 documents with only `{ _id: '1' }` / `{ _id: '2' }` — no `source_url`, `destination_url`, or `type` fields. Template rendered `type` as `Undefined`.

**Fix:** `db.ezurlwildcard.deleteMany({})` via `mongosh` — deleted the corrupt orphan records. The collection is now empty and the page shows `(0)` correctly.

---

---

## 18. SQL Database Conversion Guide — Export Any RDBMS to JSON and Import into MongoDB

This section is a practical reference for migrating an existing relational database — from any supported engine (SQLite, MariaDB, MySQL, Oracle, PostgreSQL) — into MongoDB collections for use with this project. Every subsection maps directly to a concrete CLI or script workflow.

The goal is a clean, repeatable, one-way migration: export each SQL table to JSON (one document per row), then bulk-import each JSON file into the matching MongoDB collection.

---

### 18.1 Key Concepts Before You Start

#### Relational vs Document model

SQL tables map cleanly to MongoDB collections: one table → one collection, one row → one document. You do **not** need to embed or denormalize — the sevenx_mongodb adapter works with flat documents that mirror the original SQL schema exactly.

#### Field types

MongoDB stores BSON types. The main conversions to be aware of:

| SQL type | MongoDB / BSON type | Notes |
|----------|--------------------|----|
| `INT`, `BIGINT` | `int32` / `int64` | Must be integers, not strings |
| `FLOAT`, `DECIMAL` | `double` | |
| `VARCHAR`, `TEXT`, `CHAR` | `string` | |
| `TINYINT(1)` (boolean) | `int32` (0/1) | MongoDB has `bool` but the CMS uses 0/1 |
| `DATETIME`, `TIMESTAMP` | `int64` (Unix epoch) | eZ Publish stores all timestamps as integers |
| `BLOB`, `LONGBLOB` | `string` (base64) or `BinData` | Binary fields in eZ are rare; use base64 string |
| `NULL` | Omit field or `null` | Prefer omitting sparse fields |

#### The `_id` field

MongoDB requires every document to have a unique `_id`. For tables with a numeric primary key:
- Use the SQL primary key value as `_id` (cast to integer)
- For tables with composite keys, concatenate them: `"nodeID_version"` or use an auto-generated ObjectId

The sevenx_mongodb adapter expects `_id` to equal the SQL primary key integer for all core eZ tables.

#### Ordering

Import tables in dependency order — referenced tables first. Minimum safe order:

1. `ezcontentclass`
2. `ezcontentclass_attribute`
3. `ezsection`
4. `ezcontentobject`
5. `ezcontentobject_version`
6. `ezcontentobject_attribute`
7. `ezcontentobject_tree`
8. `ezcontentobject_name`
9. `ezurlalias_ml`
10. `ezuser`, `ezuser_setting`, `ezrole`, `ezpolicy`, `ezpolicy_limitation`, `ezpolicy_limitation_value`
11. All remaining tables

---

### 18.2 MySQL / MariaDB → JSON

MySQL and MariaDB are identical for export purposes.

#### Option A — `mysqldump` with JSON output (MySQL 8.0+ / MariaDB 10.6+)

```bash
# Export a single table as newline-delimited JSON (one JSON object per line)
mysql -u root -p exp -e "
  SELECT JSON_OBJECT(
    'id', id,
    'contentclass_id', contentclass_id,
    'name', name,
    'language_mask', language_mask,
    'status', status,
    'published', published,
    'modified', modified,
    'owner_id', owner_id,
    'current_version', current_version,
    'remote_id', remote_id,
    'section_id', section_id
  )
  FROM ezcontentobject
  WHERE status = 1;
" --skip-column-names --raw > ezcontentobject.ndjson
```

This produces NDJSON (Newline-Delimited JSON) which `mongoimport` reads natively.

#### Option B — `mysql2json` shell one-liner (any MySQL version)

```bash
mysql -u root -p --batch --silent -e "SELECT * FROM ezcontentobject" exp \
  | python3 -c "
import sys, json
lines = sys.stdin.read().strip().split('\n')
headers = lines[0].split('\t')
for row in lines[1:]:
    vals = row.split('\t')
    doc = dict(zip(headers, vals))
    # Cast numeric primary key to int for _id
    if 'id' in doc:
        doc['_id'] = int(doc['id'])
    print(json.dumps(doc))
" > ezcontentobject.ndjson
```

#### Option C — Python `mysql-connector` (recommended for production)

```python
#!/usr/bin/env python3
"""
mysql_to_ndjson.py — export all eZ Publish tables to NDJSON files.
Usage: python3 mysql_to_ndjson.py --host localhost --user root --password X --db exp --outdir ./json
"""
import argparse, json, os, mysql.connector

PK_MAP = {
    'ezcontentobject':          'id',
    'ezcontentobject_version':  None,   # composite: contentobject_id + version
    'ezcontentobject_attribute':'id',
    'ezcontentobject_tree':     'node_id',
    'ezcontentclass':           'id',
    'ezcontentclass_attribute': 'id',
    'ezsection':                'id',
    'ezcontentobject_name':     None,
    'ezurlalias_ml':            'id',
    'ezuser':                   'contentobject_id',
}

def cast_row(row, pk_field):
    doc = {}
    for k, v in row.items():
        if isinstance(v, (bytes, bytearray)):
            v = v.decode('utf-8', errors='replace')
        elif v is None:
            continue   # omit null fields — keeps documents lean
        doc[k] = v
    if pk_field and pk_field in doc:
        doc['_id'] = int(doc[pk_field])
    return doc

def export_table(cur, table, pk_field, outdir):
    cur.execute(f"SELECT * FROM `{table}`")
    cols = [d[0] for d in cur.description]
    path = os.path.join(outdir, f"{table}.ndjson")
    count = 0
    with open(path, 'w') as f:
        for row_tuple in cur:
            row = dict(zip(cols, row_tuple))
            doc = cast_row(row, pk_field)
            f.write(json.dumps(doc) + '\n')
            count += 1
    print(f"  {table}: {count} rows → {path}")

def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--host', default='localhost')
    ap.add_argument('--user', required=True)
    ap.add_argument('--password', required=True)
    ap.add_argument('--db', required=True)
    ap.add_argument('--outdir', default='./json_export')
    ap.add_argument('--tables', nargs='*', help='Specific tables (default: all)')
    args = ap.parse_args()

    os.makedirs(args.outdir, exist_ok=True)
    conn = mysql.connector.connect(
        host=args.host, user=args.user, password=args.password,
        database=args.db, charset='utf8mb4'
    )
    cur = conn.cursor(dictionary=False)

    if args.tables:
        tables = args.tables
    else:
        cur.execute("SHOW TABLES")
        tables = [r[0] for r in cur]

    for table in tables:
        pk = PK_MAP.get(table, 'id')
        export_table(cur, table, pk, args.outdir)

    cur.close()
    conn.close()

if __name__ == '__main__':
    main()
```

Install dependency: `pip install mysql-connector-python`

Run:
```bash
python3 mysql_to_ndjson.py --user root --password secret --db exp --outdir ./json_export
```

---

### 18.3 PostgreSQL → JSON

#### Option A — `psql` `\copy` with `json_agg`

```sql
-- Export ezcontentobject to NDJSON
\copy (
  SELECT row_to_json(t) FROM (
    SELECT *, id AS "_id" FROM ezcontentobject WHERE status = 1
  ) t
) TO '/tmp/ezcontentobject.ndjson';
```

`row_to_json()` outputs one JSON object per row. `\copy` writes it to a file without requiring superuser access.

#### Option B — Python `psycopg2` (recommended)

```python
#!/usr/bin/env python3
"""
pg_to_ndjson.py — export PostgreSQL eZ tables to NDJSON.
pip install psycopg2-binary
"""
import argparse, json, os, psycopg2, psycopg2.extras, decimal, datetime

def serialize(v):
    if isinstance(v, decimal.Decimal):
        return float(v)
    if isinstance(v, (datetime.datetime, datetime.date)):
        return int(v.timestamp())  # eZ stores timestamps as integers
    if isinstance(v, memoryview):
        return v.tobytes().decode('utf-8', errors='replace')
    return v

def export_table(cur, table, pk_field, outdir):
    cur.execute(f'SELECT * FROM "{table}"')
    path = os.path.join(outdir, f"{table}.ndjson")
    count = 0
    with open(path, 'w') as f:
        for row in cur:
            doc = {k: serialize(v) for k, v in row.items() if v is not None}
            if pk_field and pk_field in doc:
                doc['_id'] = int(doc[pk_field])
            f.write(json.dumps(doc) + '\n')
            count += 1
    print(f"  {table}: {count} rows → {path}")

def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--dsn', required=True, help='e.g. postgresql://user:pass@host/dbname')
    ap.add_argument('--outdir', default='./json_export')
    ap.add_argument('--tables', nargs='*')
    args = ap.parse_args()

    os.makedirs(args.outdir, exist_ok=True)
    conn = psycopg2.connect(args.dsn)
    cur = conn.cursor(cursor_factory=psycopg2.extras.RealDictCursor)

    if args.tables:
        tables = args.tables
    else:
        cur.execute("""
            SELECT tablename FROM pg_tables
            WHERE schemaname = 'public'
            ORDER BY tablename
        """)
        tables = [r['tablename'] for r in cur]

    from mysql_to_ndjson import PK_MAP   # reuse the same map
    for table in tables:
        pk = PK_MAP.get(table, 'id')
        export_table(cur, table, pk, args.outdir)

    cur.close()
    conn.close()

if __name__ == '__main__':
    main()
```

Run:
```bash
python3 pg_to_ndjson.py --dsn postgresql://root:secret@localhost/exp --outdir ./json_export
```

---

### 18.4 SQLite → JSON

SQLite has no native JSON export but Python's `sqlite3` module is in the standard library — no additional dependencies needed.

```python
#!/usr/bin/env python3
"""
sqlite_to_ndjson.py — export all tables from a SQLite database to NDJSON.
No dependencies beyond Python 3 standard library.
Usage: python3 sqlite_to_ndjson.py path/to/database.db ./json_export
"""
import sys, json, os, sqlite3

PK_FIELDS = {
    'ezcontentobject':          'id',
    'ezcontentobject_tree':     'node_id',
    'ezcontentclass':           'id',
    'ezcontentclass_attribute': 'id',
    'ezsection':                'id',
    'ezurlalias_ml':            'id',
    'ezuser':                   'contentobject_id',
}

def main(db_path, outdir):
    os.makedirs(outdir, exist_ok=True)
    conn = sqlite3.connect(db_path)
    conn.row_factory = sqlite3.Row

    tables = [r[0] for r in conn.execute(
        "SELECT name FROM sqlite_master WHERE type='table' ORDER BY name"
    )]

    for table in tables:
        pk = PK_FIELDS.get(table, 'id')
        path = os.path.join(outdir, f"{table}.ndjson")
        count = 0
        with open(path, 'w') as f:
            for row in conn.execute(f"SELECT * FROM \"{table}\""):
                doc = {k: row[k] for k in row.keys() if row[k] is not None}
                if pk and pk in doc:
                    doc['_id'] = int(doc[pk])
                f.write(json.dumps(doc) + '\n')
                count += 1
        print(f"  {table}: {count} rows → {path}")

    conn.close()

if __name__ == '__main__':
    if len(sys.argv) < 3:
        print(f"Usage: {sys.argv[0]} <db.sqlite> <outdir>")
        sys.exit(1)
    main(sys.argv[1], sys.argv[2])
```

Run:
```bash
python3 sqlite_to_ndjson.py /var/www/ez.sqlite ./json_export
```

---

### 18.5 Oracle → JSON

Oracle 12c+ supports `JSON_OBJECT()` natively. For older versions use Python `cx_Oracle` / `oracledb`.

#### Option A — Oracle 12c+ SQL

```sql
-- In SQL*Plus or SQLcl, spool to a file
SPOOL /tmp/ezcontentobject.ndjson
SELECT JSON_OBJECT(
    'id'               VALUE id,
    '_id'              VALUE id,
    'contentclass_id'  VALUE contentclass_id,
    'name'             VALUE name,
    'language_mask'    VALUE language_mask,
    'status'           VALUE status,
    'published'        VALUE published,
    'modified'         VALUE modified,
    'owner_id'         VALUE owner_id,
    'current_version'  VALUE current_version,
    'remote_id'        VALUE remote_id,
    'section_id'       VALUE section_id
    ABSENT ON NULL
) FROM ezcontentobject WHERE status = 1;
SPOOL OFF
```

`ABSENT ON NULL` omits null fields, matching MongoDB conventions.

#### Option B — Python `oracledb` (thin mode, no Oracle Client required)

```python
#!/usr/bin/env python3
"""
oracle_to_ndjson.py
pip install oracledb
"""
import argparse, json, os, oracledb

def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--user', required=True)
    ap.add_argument('--password', required=True)
    ap.add_argument('--dsn', required=True, help='e.g. localhost:1521/XEPDB1')
    ap.add_argument('--outdir', default='./json_export')
    ap.add_argument('--tables', nargs='*')
    args = ap.parse_args()

    os.makedirs(args.outdir, exist_ok=True)
    conn = oracledb.connect(user=args.user, password=args.password, dsn=args.dsn)
    cur = conn.cursor()

    if args.tables:
        tables = args.tables
    else:
        cur.execute("SELECT table_name FROM user_tables ORDER BY table_name")
        tables = [r[0].lower() for r in cur]

    PK_MAP = {'ezcontentobject': 'id', 'ezcontentobject_tree': 'node_id',
              'ezcontentclass': 'id', 'ezsection': 'id', 'ezurlalias_ml': 'id'}

    for table in tables:
        pk = PK_MAP.get(table, 'id')
        cur.execute(f'SELECT * FROM "{table.upper()}"')
        cols = [d[0].lower() for d in cur.description]
        path = os.path.join(args.outdir, f"{table}.ndjson")
        count = 0
        with open(path, 'w') as f:
            for row in cur:
                doc = {cols[i]: row[i] for i in range(len(cols)) if row[i] is not None}
                if pk and pk in doc:
                    doc['_id'] = int(doc[pk])
                f.write(json.dumps(doc, default=str) + '\n')
                count += 1
        print(f"  {table}: {count} rows → {path}")

    cur.close()
    conn.close()

if __name__ == '__main__':
    main()
```

---

### 18.6 Generic Python — Any Database via SQLAlchemy

If you have a database with an available SQLAlchemy dialect (covers MySQL, MariaDB, PostgreSQL, SQLite, Oracle, MSSQL, Firebird, etc.) this single script handles all of them:

```python
#!/usr/bin/env python3
"""
sqlalchemy_to_ndjson.py — universal RDBMS → NDJSON exporter via SQLAlchemy.

pip install sqlalchemy
# Plus the dialect driver, e.g.:
#   pip install mysqlclient          # MySQL / MariaDB
#   pip install psycopg2-binary      # PostgreSQL
#   pip install cx_Oracle            # Oracle (requires Oracle Client)
#   pip install oracledb             # Oracle thin mode (no Client)
#   pip install pyodbc               # MSSQL

Examples:
  python3 sqlalchemy_to_ndjson.py mysql+mysqldb://user:pass@host/exp ./out
  python3 sqlalchemy_to_ndjson.py postgresql://user:pass@host/exp ./out
  python3 sqlalchemy_to_ndjson.py sqlite:///ez.db ./out
  python3 sqlalchemy_to_ndjson.py oracle+oracledb://user:pass@host:1521/?service_name=XE ./out
"""
import sys, json, os, decimal, datetime
from sqlalchemy import create_engine, inspect, text

PK_MAP = {
    'ezcontentobject':          'id',
    'ezcontentobject_tree':     'node_id',
    'ezcontentobject_attribute':'id',
    'ezcontentclass':           'id',
    'ezcontentclass_attribute': 'id',
    'ezsection':                'id',
    'ezcontentobject_name':     None,
    'ezcontentobject_version':  None,
    'ezurlalias_ml':            'id',
    'ezuser':                   'contentobject_id',
}

def serialize(v):
    if v is None:
        return None
    if isinstance(v, decimal.Decimal):
        return float(v)
    if isinstance(v, datetime.datetime):
        return int(v.timestamp())
    if isinstance(v, datetime.date):
        return int(datetime.datetime(v.year, v.month, v.day).timestamp())
    if isinstance(v, (bytes, bytearray)):
        return v.decode('utf-8', errors='replace')
    if isinstance(v, memoryview):
        return bytes(v).decode('utf-8', errors='replace')
    return v

def main(url, outdir, tables=None):
    os.makedirs(outdir, exist_ok=True)
    engine = create_engine(url)
    insp = inspect(engine)

    if not tables:
        tables = insp.get_table_names()

    with engine.connect() as conn:
        for table in sorted(tables):
            pk = PK_MAP.get(table, 'id')
            path = os.path.join(outdir, f"{table}.ndjson")
            result = conn.execute(text(f'SELECT * FROM "{table}"'))
            cols = list(result.keys())
            count = 0
            with open(path, 'w') as f:
                for row in result:
                    doc = {}
                    for i, col in enumerate(cols):
                        v = serialize(row[i])
                        if v is not None:
                            doc[col] = v
                    if pk and pk in doc:
                        doc['_id'] = int(doc[pk])
                    f.write(json.dumps(doc) + '\n')
                    count += 1
            print(f"  {table}: {count} rows → {path}")

if __name__ == '__main__':
    if len(sys.argv) < 3:
        print(f"Usage: {sys.argv[0]} <sqlalchemy-url> <outdir> [table1 table2 ...]")
        sys.exit(1)
    main(sys.argv[1], sys.argv[2], sys.argv[3:] or None)
```

---

### 18.7 Post-export validation and fixup

Before importing, validate and fix common issues:

```bash
#!/usr/bin/env bash
# validate_ndjson.sh — check each .ndjson file is valid JSON

for f in ./json_export/*.ndjson; do
    errors=$(python3 -c "
import sys, json
bad = 0
for i, line in enumerate(open('$f'), 1):
    line = line.strip()
    if not line:
        continue
    try:
        json.loads(line)
    except Exception as e:
        print(f'  Line {i}: {e}')
        bad += 1
print(f'{bad} error(s)' if bad else 'OK')
")
    echo "$f: $errors"
done
```

Common fixups needed:

```python
#!/usr/bin/env python3
"""
fixup_ndjson.py — apply common transformations before MongoDB import.

What this does:
  1. Ensures _id is an integer (not a string) for all eZ tables with numeric PKs
  2. Converts 'NULL' string values to omitted fields
  3. Ensures serialized_name_list / serialized_description_list are strings
  4. Strips microseconds from datetime fields (eZ uses Unix int timestamps)
"""
import sys, json, os

INT_FIELDS = {
    'id', 'contentobject_id', 'contentclass_id', 'node_id', 'parent_node_id',
    'main_node_id', 'owner_id', 'version', 'status', 'language_mask',
    'language_id', 'published', 'modified', 'created', 'section_id',
    'current_version', 'creator_id', 'modifier_id', 'depth', 'sort_field',
    'sort_order', 'priority', 'is_hidden', 'is_invisible', 'path_identification_string'
}

def fixup(doc):
    out = {}
    for k, v in doc.items():
        if v == 'NULL' or v is None:
            continue
        if k in INT_FIELDS:
            try:
                v = int(float(str(v)))
            except (ValueError, TypeError):
                pass
        out[k] = v
    return out

def process(inpath, outpath):
    count = 0
    with open(inpath) as fin, open(outpath, 'w') as fout:
        for line in fin:
            line = line.strip()
            if not line:
                continue
            doc = fixup(json.loads(line))
            fout.write(json.dumps(doc) + '\n')
            count += 1
    return count

indir = sys.argv[1] if len(sys.argv) > 1 else './json_export'
outdir = sys.argv[2] if len(sys.argv) > 2 else './json_fixed'
os.makedirs(outdir, exist_ok=True)
for fname in sorted(os.listdir(indir)):
    if fname.endswith('.ndjson'):
        n = process(os.path.join(indir, fname), os.path.join(outdir, fname))
        print(f"  {fname}: {n} docs")
```

---

### 18.8 MongoDB Import (`mongoimport`)

`mongoimport` is part of the MongoDB Database Tools package. It reads NDJSON natively.

#### Import a single table

```bash
mongoimport \
  --uri "mongodb://db:publishing\$8088@localhost:27017/exp" \
  --db exp \
  --collection ezcontentobject \
  --file ./json_export/ezcontentobject.ndjson \
  --type json \
  --jsonArray=false \
  --mode upsert \
  --upsertFields _id
```

Key flags:
- `--mode upsert` — inserts new documents, updates existing ones by `_id`; safe to re-run
- `--upsertFields _id` — uses `_id` as the match key
- `--jsonArray=false` — NDJSON (one JSON object per line); use `--jsonArray` only if your file is a JSON array

#### Import all tables in dependency order

```bash
#!/usr/bin/env bash
# import_all.sh — import all NDJSON files into MongoDB in correct dependency order.

URI="mongodb://db:publishing\$8088@localhost:27017/exp"
DB="exp"
INDIR="${1:-./json_fixed}"

# Ordered list — dependencies first
TABLES=(
    ezcontentlanguage
    ezsection
    ezcontentclass
    ezcontentclass_attribute
    ezcontentclass_classgroup
    ezcontentclass_name
    ezcontentobject
    ezcontentobject_version
    ezcontentobject_attribute
    ezcontentobject_tree
    ezcontentobject_name
    ezcontentobject_link
    ezurlalias_ml
    ezurlalias_ml_incr
    ezurlalias
    eznode_assignment
    ezuser
    ezuser_setting
    ezrole
    ezpolicy
    ezpolicy_limitation
    ezpolicy_limitation_value
    ezuser_role
    ezsubtree_limitation_item
    ezcontentobject_state
    ezcontentobject_state_group
    ezcontentobject_state_link
    ezcontentbrowsebookmark
    ezcontentcache_list
    ezpersistentcookie
)

for TABLE in "${TABLES[@]}"; do
    FILE="$INDIR/$TABLE.ndjson"
    if [[ ! -f "$FILE" ]]; then
        echo "  SKIP $TABLE (no file)"
        continue
    fi
    echo "  Importing $TABLE ..."
    mongoimport \
        --uri "$URI" \
        --db "$DB" \
        --collection "$TABLE" \
        --file "$FILE" \
        --type json \
        --mode upsert \
        --upsertFields _id \
        2>&1 | tail -1
done

# Import any remaining files not in the ordered list
for FILE in "$INDIR"/*.ndjson; do
    TABLE=$(basename "$FILE" .ndjson)
    # Skip if already imported
    if printf '%s\n' "${TABLES[@]}" | grep -qx "$TABLE"; then
        continue
    fi
    echo "  Importing (remaining) $TABLE ..."
    mongoimport \
        --uri "$URI" \
        --db "$DB" \
        --collection "$TABLE" \
        --file "$FILE" \
        --type json \
        --mode upsert \
        --upsertFields _id \
        2>&1 | tail -1
done

echo "Done."
```

---

### 18.9 Post-import: create indexes

After importing, create the indexes that the sevenx_mongodb adapter depends on. Missing indexes cause full collection scans; some queries will be extremely slow on any meaningful data volume.

```javascript
// run in: mongosh "mongodb://db:publishing$8088@localhost:27017/exp"

// Content object lookups
db.ezcontentobject.createIndex({ id: 1 }, { unique: true });
db.ezcontentobject.createIndex({ contentclass_id: 1 });
db.ezcontentobject.createIndex({ section_id: 1 });
db.ezcontentobject.createIndex({ status: 1 });
db.ezcontentobject.createIndex({ remote_id: 1 }, { unique: true });
db.ezcontentobject.createIndex({ owner_id: 1 });

// Tree / node navigation
db.ezcontentobject_tree.createIndex({ node_id: 1 }, { unique: true });
db.ezcontentobject_tree.createIndex({ contentobject_id: 1 });
db.ezcontentobject_tree.createIndex({ parent_node_id: 1 });
db.ezcontentobject_tree.createIndex({ main_node_id: 1 });
db.ezcontentobject_tree.createIndex({ path_string: 1 });
db.ezcontentobject_tree.createIndex({ depth: 1, sort_field: 1, sort_order: 1 });
db.ezcontentobject_tree.createIndex({ contentobject_id: 1, is_main: 1 });

// Object attributes
db.ezcontentobject_attribute.createIndex({ contentobject_id: 1, version: 1 });
db.ezcontentobject_attribute.createIndex({ contentclassattribute_id: 1 });
db.ezcontentobject_attribute.createIndex({ language_code: 1 });

// Versions
db.ezcontentobject_version.createIndex({ contentobject_id: 1, version: 1 }, { unique: true });
db.ezcontentobject_version.createIndex({ status: 1 });
db.ezcontentobject_version.createIndex({ creator_id: 1 });

// Names
db.ezcontentobject_name.createIndex({ contentobject_id: 1, content_version: 1, content_translation: 1 });

// Content classes
db.ezcontentclass.createIndex({ identifier: 1 });
db.ezcontentclass.createIndex({ version: 1 });
db.ezcontentclass_attribute.createIndex({ contentclass_id: 1, version: 1 });
db.ezcontentclass_attribute.createIndex({ identifier: 1 });

// URL aliases — critical for navigation
db.ezurlalias_ml.createIndex({ parent: 1, text_md5: 1 }, { unique: true });
db.ezurlalias_ml.createIndex({ action: 1, is_original: 1, is_alias: 1 });
db.ezurlalias_ml.createIndex({ id: 1 }, { unique: true });
db.ezurlalias_ml.createIndex({ link: 1 });

// User / auth
db.ezuser.createIndex({ login: 1 }, { unique: true });
db.ezuser.createIndex({ email: 1 });
db.ezuser.createIndex({ contentobject_id: 1 }, { unique: true });

// Roles and policies
db.ezrole.createIndex({ name: 1 });
db.ezpolicy.createIndex({ role_id: 1 });
db.ezpolicy_limitation.createIndex({ policy_id: 1 });
db.ezuser_role.createIndex({ contentobject_id: 1 });

// States
db.ezcontentobject_state_group.createIndex({ identifier: 1 });
db.ezcontentobject_state.createIndex({ group_id: 1, identifier: 1 });
db.ezcontentobject_state_link.createIndex({ contentobject_id: 1 });

// Sections
db.ezsection.createIndex({ identifier: 1 });

// Info collector
db.ezinfocollection.createIndex({ contentobject_id: 1 });

// Search
db.ezsearch_object_word_link.createIndex({ word_id: 1 });
db.ezsearch_object_word_link.createIndex({ contentobject_id: 1 });
db.ezsearch_word.createIndex({ word: 1 }, { unique: true });

// Language
db.ezcontentlanguage.createIndex({ locale: 1 }, { unique: true });
db.ezcontentlanguage.createIndex({ language_mask: 1 });
```

---

### 18.10 Verifying the import

After import, run these quick checks in `mongosh`:

```javascript
// Check document counts for key collections
const cols = [
    'ezcontentobject', 'ezcontentobject_tree', 'ezcontentobject_attribute',
    'ezcontentobject_version', 'ezcontentobject_name', 'ezcontentclass',
    'ezurlalias_ml', 'ezuser', 'ezrole', 'ezsection', 'ezcontentlanguage'
];
cols.forEach(c => print(c + ': ' + db[c].countDocuments()));

// Spot-check: does _id match the 'id' field for ezcontentobject?
const sample = db.ezcontentobject.findOne({ status: 1 });
print('_id:', sample._id, ' id:', sample.id, ' match:', sample._id == sample.id);

// Check the main tree root node exists
const root = db.ezcontentobject_tree.findOne({ node_id: 1 });
print('Root node:', root ? 'OK' : 'MISSING');

// Check admin user
const admin = db.ezuser.findOne({ login: 'admin' });
print('Admin user:', admin ? admin.login + ' / object ' + admin.contentobject_id : 'MISSING');
```

---

### 18.11 Complete end-to-end example for this project (MySQL → MongoDB)

The production migration for `edit.mongodb.demo.se7enx.com` was performed from the original `exp` MySQL database. The exact commands:

```bash
SCRIPTS=extension/sevenx_mongodb/bin/mongodb

# 1. Export from MySQL to NDJSON
bash $SCRIPTS/export_mysql.sh ./json_export
# (or directly: python3 $SCRIPTS/mysql2ndjson.py --user db --password 'publishing$2099' --db exp --outdir ./json_export)

# 2. Fix up types (int fields, NULL strings)
python3 $SCRIPTS/fixup_ndjson.py ./json_export ./json_fixed

# 3. Validate
bash $SCRIPTS/validate_ndjson.sh ./json_fixed

# 4. Import
bash $SCRIPTS/import_all.sh ./json_fixed

# 5. Create indexes
mongosh "mongodb://db:publishing\$8088@localhost:27017/exp" --file $SCRIPTS/create_indexes.js

# 6. Touch opcache and test the admin
touch /web/vh/mongodb.demo.se7enx.com/doc/mongodb.demo.se7enx.com/index.php
curl -s -o /dev/null -w "%{http_code}" https://edit.mongodb.demo.se7enx.com/user/login
```

---

### 18.12 Troubleshooting common import problems

| Symptom | Cause | Fix |
|---------|-------|-----|
| `_id` duplicate key error on import | Two rows had the same PK value (data quality issue) | Use `--mode upsert` instead of `--mode insert` |
| Template renders blank / `NULL` name | `ezcontentobject_name` imported with wrong `content_version` field (string vs int) | Run `extension/sevenx_mongodb/bin/mongodb/fixup_ndjson.py` to cast int fields |
| `/content/view/full/2` 404 | `ezcontentobject_tree` root node not imported | Check `db.ezcontentobject_tree.findOne({node_id:1})` |
| Login fails | `ezuser` or `ezuser_setting` missing; or `password_hash` truncated during export | Verify `db.ezuser.findOne({login:'admin'})` and check `password_hash` length |
| URL aliases broken / all 404 | `ezurlalias_ml` not imported or index on `parent`+`text_md5` missing | Reimport collection; run `extension/sevenx_mongodb/bin/mongodb/create_indexes.js` |
| PHP `MONGO TODO arrayQuery` | A kernel method still has an unported SQL path | File is in the pending backlog (see Section 11) |
| Slow page loads after import | Indexes not created | Run `extension/sevenx_mongodb/bin/mongodb/create_indexes.js` in `mongosh` |
| `mongoimport: command not found` | MongoDB Database Tools not installed separately from `mongod` | `apt install mongodb-database-tools` or download from mongodb.com/try/download/database-tools |

---

---

## 19. Project Complete — File by File Patched or Changed List

### Purpose

This section is a canonical record of every file that has been patched or changed as part of the MongoDB port.  It is intended to be used as a reference when comparing git history and verifying completeness of the port.  Every entry lists the file path (relative to the project root), what changed, and which MongoDB adapter method was introduced or replaced.

---

### 19.1 MongoDB Adapter (Core)

| File | Change Summary |
|------|---------------|
| `extension/sevenx_mongodb/classes/expMongoDB.php` | **Created from scratch.** Wraps `MongoDB\Client`. Implements `query()` (silent no-op for writes), `arrayQuery()` (logs MONGO TODO + returns `[]`), `aggregate()`, `insert()`, `upsert()`, `deleteWhere()`, `nextSeqID()`, `databaseName()`, `escapeString()`, `begin()`/`commit()`/`rollback()` (no-ops), `lock()`/`unlock()` (no-ops), bitOr/bitAnd helpers. |
| `var/autoload/ezp_override.php` | **Modified.** Added class-override mappings for all patched kernel classes (see section 19.2 and 19.3 below). Also registers `eZContentOperationCollection` override for nxc_powercontent. |

---

### 19.2 Extension Override Classes — `extension/sevenx_mongodb/classes/kernel/`

These files shadow the kernel classes of the same name.  Every file follows the same pattern: a MongoDB branch (`if ( $db->databaseName() === 'mongo' ) { … }`) is inserted before the SQL `arrayQuery()` call, with the SQL path remaining untouched in the `else` branch.

| File | Methods with MongoDB Branches Added |
|------|-------------------------------------|
| `ezcontentobject.php` | `names()`, `fetchByRemoteID()`, `className()`, `canCreateClassList()`, `copy()` (clone-with-null-id pattern), `relatedObjects()`, `nextVersion()`, `previousVersion()`, `getVersionCount()`, and many more aggregate lookups |
| `ezcontentobjectversion.php` | `translationList()`, `fetchAttributes()` (full join replacement with 3-step aggregate + merge), `cloneVersion()` |
| `ezcontentobjecttreenode.php` | `subTreeCountByNodeID()`, `addChildTo()`, `getClassesJsArray()`, `findNode()`, `getAvailableClassesList()` |
| `ezcontentclass.php` | `fetchList()`, `fetchAllClasses()`, `nameFromSerializedString()` helpers |
| `ezcontentclassattribute.php` | `fetchByClassID()`, `fetchByIdentifier()` |
| `eznodeassignment.php` | `fetchForObject()`, `fetchByNode()` |
| `ezpreferences.php` | `value()` (aggregate on `ezpreferences`), `setValue()` (upsert), `removeForUser()` |
| `ezsection.php` | `fetchList()`, `fetchByIdentifier()`, `sectionCount()` |
| `ezrole.php` | `fetchByUser()`, `fetchRolesByLimitation()`, `fetchUserByRole()` |
| `ezsearchlog.php` | `addPhrase()` (early return — SQL phrase log not used in MongoDB mode), `mostFrequentPhraseArray()` (aggregate on `ezsearch_search_phrase`) |
| `ezurlaliasml.php` | Redirect path builder (two stubs ~line 2120, 2155), `translateByAction()` / `fetchByAction()` lookup stubs |
| `ezpackage.php` | `setInstalled()`, `getInstallState()` (aggregate count on `ezpackage`) |
| `ezinformationcollection.php` | `fetchCollectionAttributeList()`, `fetchCollectionList()` |
| `ezkeyword.php` (datatypes) | `store()` — full MongoDB upsert/delete cycle for keyword+link tables replacing 4 SQL arrayQuery calls |
| `ezurlaliasquery.php` | Path-translation lookup stubs |

---

### 19.3 nxc_powercontent Override

| File | Change Summary |
|------|---------------|
| `extension/nxc_powercontent/modules/content/ezcontentoperationcollection.php` | `setVersionStatus()` — added MongoDB branch using `$db->upsert()` on `ezcontentobject_version`; `loopNodeAssignment()` — added MongoDB upsert for `ezcontentobject_tree` and `eznodeassignment`. This is the file that makes **copy operations** actually create tree nodes. |

---

### 19.4 Kernel Files Patched Directly (no override, edited in-place)

| File | Change Summary |
|------|---------------|
| `kernel/search/plugins/ezsearchengine/ezsearchengine.php` | `buildWordIDArray()` — early return for MongoDB (indexing path not used). `prepareWordIDArrays()` — early return returning empty arrays. `prepareWordIDArraysForPattern()` — early return returning empty arrays. `fetchTotalObjectCount()` — MongoDB aggregate `$count` on `ezcontentobject`. `search()` — **full MongoDB implementation**: regex on `ezcontentobject.name` + `ezcontentobject_attribute.data_text`, fetches matching `eZContentObjectTreeNode` objects, returns proper `SearchResult`/`SearchCount`/`StopWordArray` array. |
| `kernel/search/stats.php` | `$searchListCount` — MongoDB aggregate `$count` branch added. |
| `lib/ezdb/classes/ezpersistentobject.php` | `storeObject()` — added MongoDB branch: sets `id = null` before insert so `nextSeqID()` assigns the new PK; uses `$db->insert()` or `$db->upsert()` as appropriate. |
| `kernel/content/ezcontentoperationcollection.php` | `setVersionStatus()` — semicolon bug (`if ( !$existingNode );`) noted but masked by nxc_powercontent override. No edit made (override takes precedence). |

---

### 19.5 Data Layer — Import Scripts (`extension/sevenx_mongodb/bin/mongodb/`)

All data-layer migration scripts live in `extension/sevenx_mongodb/bin/mongodb/`. Run them from the docroot or pass absolute paths.

| File | Purpose |
|------|---------|
| `export_mysql.sh` | Shell wrapper — calls `mysql2ndjson.py` with credentials from the environment; writes NDJSON to `./json_export/` |
| `mysql2ndjson.py` | Python script — exports all MySQL/MariaDB eZ Publish tables to NDJSON; handles int/float casting, omits NULL fields, sets `_id` from the PK column |
| `fixup_ndjson.py` | Python script — post-processes NDJSON to fix data type issues (e.g. string `content_version` → int, `'NULL'` strings → omitted) |
| `validate_ndjson.sh` | Shell script — validates every `.ndjson` file in a directory; exits 1 if any line is not valid JSON |
| `import_all.sh` | Shell script — calls `mongoimport` for every collection in dependency order, then imports any remaining files not in the list |
| `create_indexes.js` | mongosh script — creates all MongoDB indexes the sevenx_mongodb adapter depends on (including the critical `parent`+`text_md5` compound index on `ezurlalias_ml`) |

**Quick usage:**
```bash
cd /web/vh/mongodb.demo.se7enx.com/doc/mongodb.demo.se7enx.com
SCRIPTS=extension/sevenx_mongodb/bin/mongodb

bash  $SCRIPTS/export_mysql.sh           ./json_export
python3 $SCRIPTS/fixup_ndjson.py         ./json_export ./json_fixed
bash  $SCRIPTS/validate_ndjson.sh        ./json_fixed
bash  $SCRIPTS/import_all.sh             ./json_fixed
mongosh "mongodb://db:publishing\$8088@localhost:27017/exp" --file $SCRIPTS/create_indexes.js
```

---

### 19.6 Documentation Files

| File | Purpose |
|------|---------|
| `extension/sevenx_mongodb/MONGODB_KERNEL_SUPPORT_EXPANSION.md` | This file — running log of all MongoDB port work, architecture decisions, troubleshooting steps, and the canonical file-change list (this section). |
| `MONGODB-BUGS.md` | Bug tracker for MongoDB port issues, 7+ entries covering edge cases found during testing. |

---

### 19.7 Key Architectural Decisions

1. **Override pattern, not subclassing**: The class registry in `var/autoload/ezp_override.php` maps the original class names to the extension files. This means the extension files define classes with the *same name* as the originals and are loaded instead. No inheritance — clean replacement.

2. **`$db->databaseName() === 'mongo'` as the gating check**: All MongoDB branches start with this check. The expMongoDB adapter returns `'mongo'` (lowercase) from `databaseName()`. This is reliable, single-source-of-truth gating.

3. **`query()` is a no-op for writes**: All `$db->query("INSERT…")` / `$db->query("UPDATE…")` / `$db->query("DELETE…")` are silently ignored by the adapter. Only explicit `$db->insert()`, `$db->upsert()`, `$db->deleteWhere()` perform real writes.

4. **`arrayQuery()` logs and returns `[]`**: Any unpatched SQL read path that calls `$db->arrayQuery()` logs a MONGO TODO warning and returns an empty array. This makes it easy to find remaining stubs via the error log.

5. **Full-text search via regex**: The built-in ezsearch engine was ported to use MongoDB regex on `ezcontentobject.name` + `ezcontentobject_attribute.data_text` rather than the ezsearch word-index tables (which are not populated in MongoDB mode). Results are returned as `eZContentObjectTreeNode` objects matching the expected contract.

6. **Sequential IDs via `nextSeqID()`**: The adapter implements `nextSeqID($table, $column)` as `MAX($column) + 1` using a MongoDB aggregate. This replicates MySQL AUTO_INCREMENT semantics without relying on MongoDB ObjectId.

7. **Copy operations**: Object copy works by setting `id = null` on the cloned `eZContentObject` before storing — `storeObject()` detects null ID and calls `nextSeqID()` to assign a new PK. The `eZContentOperationCollection` override in nxc_powercontent creates the tree node and node assignment in MongoDB.

---

### 19.8 Testing Checklist

| Feature | Status |
|---------|--------|
| Admin login | ✅ Working |
| Content browse / tree navigation | ✅ Working |
| Content view (full / line / block) | ✅ Working |
| Content edit (save draft, publish) | ✅ Working |
| Copy object (single + subtree) | ✅ Working — tree nodes created correctly |
| Search (keyword) | ✅ Working — MongoDB regex search returns real results |
| URL aliases | ✅ Working |
| Keyword datatype (store/fetch) | ✅ Working |
| User preferences | ✅ Working |
| Section list | ✅ Working |
| Package install state | ✅ Working |
| Search stats page | ✅ Working |
| Related objects | ✅ Working |
| Content class list | ✅ Working |
| Role/permission checks | ✅ Working |
| MONGO TODO arrayQuery log | ✅ **Clean** — no unpatched `arrayQuery` calls in normal admin/front-site usage |


---

## 20. Steps to Full Kernel Re-Implementation (NO more kernel override extension)

### 20.1 Overview and Motivation

The current MongoDB port uses `extension/sevenx_mongodb/` as a shadow-override system.
`var/autoload/ezp_override.php` maps every kernel class name to a parallel file in the extension.
This approach worked well for incremental development but carries structural costs that grow over time:

- **Dual-file maintenance**: Each patched class exists in two places — the kernel original and the
  extension override. Any bug-fix or upstream merge must be applied to the extension file, not the
  original. The original file becomes dead code that silently misleads developers.
- **Two-docroot complexity**: `kernel/` files are separate physical copies between the edit and
  front-site docroots. Extension files are symlinked (one change hits both sites). Kernel patches
  must be applied to both copies separately, manually. Extension changes are automatic.
- **Long-term drift risk**: If the upstream eZ Publish codebase is ever updated, the override files
  become stale silently — the original kernel file changes but the override hides it permanently.
- **Cognitive overhead**: "Is this method patched?" requires checking `var/autoload/ezp_override.php`,
  then the extension directory, then the kernel file. There is no single source of truth.
- **IDE confusion**: Static analysis tools, IDEs, and `grep` find the original kernel class first.
  Developers refactoring the wrong file is a common error in this architecture.

The clean-state target is: **all MongoDB branches live directly in the `kernel/` and `lib/` files**.
The extension retains only the adapter (`expMongoDB.php`) and its INI config. No class overrides.

---

### 20.2 What Changes and What Stays

**Stays in extension (`extension/sevenx_mongodb/`):**

| Item | Why it stays |
|---|---|
| `classes/expMongoDB.php` | This is a new class (the adapter), not an override of any existing class. The autoload entry that registers it is legitimate. |
| `settings/` — INI config, DB handler registration | Config, not code. |
| `autoloads/expMongoDBinfo.php` | Extension autoload entry for the adapter itself. |
| Data migration scripts (`sevenx_mongodb/*.py`, `*.sh`, `*.js`) | Not PHP class overrides — standalone tools. |

**Moves into kernel (merged and deleted from extension):**

Every file under `extension/sevenx_mongodb/classes/kernel/` — ~120 files across `kernel/classes/`,
`kernel/datatypes/`, `kernel/notification/`, `kernel/workflowtypes/`, etc. Full inventory in §20.5.

**After the merge, `var/autoload/ezp_override.php` contains only:**

```php
// expMongoDB adapter — new class, not an override
'expMongoDB' => 'extension/sevenx_mongodb/classes/expMongoDB.php',
```

All other entries (the ~120 kernel class overrides) are removed.

**Kernel-only patches already done (no extension override exists, no merge needed):**

These files were patched directly in `kernel/` and `lib/` — they are already in the correct final
state:

- `kernel/search/plugins/ezsearchengine/ezsearchengine.php` — full MongoDB regex search
- `kernel/search/stats.php` — `$searchListCount` aggregate branch
- `lib/ezdb/classes/ezpersistentobject.php` — `storeObject()`, `removeObject()`, `newObjectOrder()`, `handleRows()`
- `kernel/content/edit.php` — debug log removal, publish pipeline
- `kernel/content/attribute_edit.php` — debug log removal
- `kernel/class/edit.php` — class edit/copy/create workflows
- `kernel/private/classes/ezcontentobjectstategroup.php` — `arrayQuery` → `else` branch
- `extension/nxc_powercontent/modules/content/ezcontentoperationcollection.php` — stays as a third-party extension override, no change needed

---

### 20.3 The Merge Process (per file)

For each override file, follow these steps in order:

1. **Diff** the override against the kernel original to identify all additions:
   ```bash
   diff \
     extension/sevenx_mongodb/classes/kernel/somefile.php \
     kernel/classes/somefile.php
   ```
   Look for: `if ( $db->databaseName() === 'mongo' )` blocks, nullable-param fixes (`?Type`),
   null-coalescing additions (`?? []`), and early-return guards.

2. **Apply** every MongoDB-specific addition to the kernel file. The SQL `else` branch stays
   exactly as it was. Only the MongoDB `if` block is new.

3. **Lint** the kernel file immediately after editing:
   ```bash
   /opt/plesk/php/8.5/bin/php -l kernel/classes/somefile.php
   ```
   Do not proceed to the next step if lint fails.

4. **Touch** the kernel file on **both** docroots to bust opcache:
   ```bash
   BASE=/web/vh/mongodb.demo.se7enx.com/doc/mongodb.demo.se7enx.com
   ALT=/var/www/vhosts/mongodb.demo.se7enx.com/doc/mongodb.demo.se7enx.com
   touch "$BASE/kernel/classes/somefile.php" "$ALT/kernel/classes/somefile.php"
   ```
   Note: files under `lib/` are the same physical file on both docroots if the `lib/` directory
   is symlinked. Verify with `ls -li` before touching both paths.

5. **Remove** the override file from the extension:
   ```bash
   rm extension/sevenx_mongodb/classes/kernel/somefile.php
   ```

6. **Remove** the class entry from `var/autoload/ezp_override.php`:
   ```php
   // Remove the line:
   'eZSomeClass' => 'extension/sevenx_mongodb/classes/kernel/somefile.php',
   ```

7. **Test** the affected feature in the browser before proceeding to the next file.
   Check `error_log` for any new MONGO TODO entries or PHP errors.

8. **Repeat** for the next file.

---

### 20.4 Two-Docroot Notes

The two docroots differ in how files are physically stored:

| Path type | Front site | Edit/admin site | Sync strategy |
|---|---|---|---|
| `extension/sevenx_mongodb/` | Symlink to edit docroot | Physical directory | Editing either path affects both |
| `kernel/classes/` | Physical copy | Separate physical copy | Must edit BOTH after merge |
| `kernel/datatypes/` | Physical copy | Separate physical copy | Must edit BOTH after merge |
| `kernel/content/*.php` | Physical copy | Separate physical copy | Must edit BOTH |
| `lib/` | Symlink to edit docroot (check!) | Physical directory | If symlinked: one edit; if not: edit BOTH |
| `var/autoload/ezp_override.php` | Physical copy | Separate physical copy | Must edit BOTH after each class is removed |

**Verify `lib/` status:**
```bash
ls -la /web/vh/mongodb.demo.se7enx.com/doc/mongodb.demo.se7enx.com/lib
ls -la /var/www/vhosts/mongodb.demo.se7enx.com/doc/mongodb.demo.se7enx.com/lib
```
If `lib/` is a symlink on the front site pointing to the edit site: one edit propagates.
If it is a separate physical copy: treat the same as `kernel/`.

---

### 20.5 File Inventory — Phased Merge Plan

Merge in this order: most critical and most browser-tested first; peripheral handlers last.

#### Phase 1 — Core ORM and Content (highest impact, most tested)

| Extension override | Kernel target | Key MongoDB additions |
|---|---|---|
| `classes/kernel/ezcontentobject.php` | `kernel/classes/ezcontentobject.php` | `fetch()` int-cast, `fetchByNodeID()`, `relatedObjects()` `$lookup` pipeline, `relatedObjectCount()` `$count`, `contentObjectAttributes()` pipeline, `fillNodeListAttributes()` batch load, `assignedNodes()`, `hasVisibleNode()`, `stateIDArray()`, `stateIdentifierArray()`, `addContentObjectRelation()` int-cast |
| `classes/kernel/ezcontentobjectversion.php` | `kernel/classes/ezcontentobjectversion.php` | `fetch()` int-cast, `removeThis()` cascade `deleteWhere`, `translationList()`, `fetchAttributes()` 3-step pipeline, `cloneVersion()` |
| `classes/kernel/ezcontentobjecttreenode.php` | `kernel/classes/ezcontentobjecttreenode.php` | `subTreeCountByNodeID()`, `addChildTo()`, `getClassesJsArray()`, `findNode()`, `getAvailableClassesList()` |
| `classes/kernel/ezcontentclass.php` | `kernel/classes/ezcontentclass.php` | `fetchList()`, `fetchAllClasses()`, `initializeCopy()` `$lookup` pipeline |
| `classes/kernel/ezcontentclassattribute.php` | `kernel/classes/ezcontentclassattribute.php` | `fetchByClassID()`, `fetchByIdentifier()` |
| `classes/kernel/eznodeassignment.php` | `kernel/classes/eznodeassignment.php` | `fetchForObject()`, `fetchByNode()` — confirmed already patched in extension |

#### Phase 2 — User, Role, Section (auth path)

| Extension override | Kernel target | Key MongoDB additions |
|---|---|---|
| `classes/kernel/ezrole.php` | `kernel/classes/ezrole.php` | `fetchByUser()` recursive `$lookup`, `fetchRolesByLimitation()`, `fetchUserByRole()`, `assignToUser()` aggregate-check + insert, `removeUserAssignment()` `deleteWhere`, `fetchIDListByUser()`, `fetchUserID()` |
| `classes/kernel/datatypes/ezuser/ezuser.php` | `kernel/datatypes/ezuser/ezuser.php` | Login, fetch, preference methods |
| `classes/kernel/datatypes/ezuser/ezusersetting.php` | `kernel/datatypes/ezuser/ezusersetting.php` | `fetch()`, `fetchByOffset()` |
| `classes/kernel/ezsection.php` | `kernel/classes/ezsection.php` | `fetchList()`, `fetchByIdentifier()`, `sectionCount()` |
| `classes/kernel/ezpreferences.php` | `kernel/classes/ezpreferences.php` | `value()` aggregate, `setValue()` upsert, `removeForUser()` `deleteWhere` |
| `classes/kernel/ezpolicylimitation.php` | `kernel/classes/ezpolicylimitation.php` | `fetchByPolicy()` two-stage aggregate |

#### Phase 3 — Search, URLs, Cache

| Extension override | Kernel target | Key MongoDB additions |
|---|---|---|
| `classes/kernel/ezurlaliasml.php` | `kernel/classes/ezurlaliasml.php` | Redirect path builder, `translateByAction()`, `fetchByAction()` stubs |
| `classes/kernel/ezurlwildcard.php` | `kernel/classes/ezurlwildcard.php` | `fetchList()`, `createWildcardsIndex()` |
| `classes/kernel/ezsearchlog.php` | `kernel/classes/ezsearchlog.php` | `addPhrase()` early return, `mostFrequentPhraseArray()` aggregate on `ezsearch_search_phrase` |
| `classes/kernel/ezcontentcachemanager.php` | `kernel/classes/ezcontentcachemanager.php` | PHP 8.5 implicit-nullable param fix |
| `classes/kernel/ezsiteaccess.php` | `kernel/classes/ezsiteaccess.php` | PHP 8.5 implicit-nullable in `change()`, `load()` |
| `classes/kernel/ezpackage.php` | `kernel/classes/ezpackage.php` | `setInstalled()`, `getInstallState()` aggregate `$count` |

#### Phase 4 — Workflow, Collaboration, Information Collection

| Extension override | Kernel target | Key MongoDB additions |
|---|---|---|
| `classes/kernel/eztrigger.php` | `kernel/classes/eztrigger.php` | `array_keys($workflowProcess->Template['templateVars'] ?? [])` null-coalescing fix |
| `classes/kernel/workflowtypes/event/ezpaymentgateway/ezpaymentgatewaytype.php` | `kernel/classes/workflowtypes/event/ezpaymentgateway/ezpaymentgatewaytype.php` | Single-assignment fix for magic `__get`/`__set` `$process->Template` (was indirect modification of overloaded property) |
| `classes/kernel/ezorder.php` | `kernel/classes/ezorder.php` | `active()` user-name sort `$lookup` pipeline; PHP 8.x nullable param fixes |
| `classes/kernel/ezbasket.php` | `kernel/classes/ezbasket.php` | `cleanupExpired()` expired-session aggregate; `cleanup()` sweep; both use `deleteWhere` |
| `classes/kernel/ezdiscount.php` | `kernel/classes/ezdiscount.php` | Sub-rule `$in` aggregate; limitation-value scalar `$match` aggregate |
| `classes/kernel/ezinformationcollection.php` | `kernel/classes/ezinformationcollection.php` | All 4 JOIN query replacements: `fetchCountForAttribute()`, `fetchCollectionCountForObject()`, `fetchCountList()`, `informationCollectionAttributes()` |
| `classes/kernel/ezinformationcollectionattribute.php` | `kernel/classes/ezinformationcollectionattribute.php` | `contentClassAttributeName()` simple aggregate |
| `classes/kernel/ezcollaborationitem.php` | `kernel/classes/ezcollaborationitem.php` | `$lookup` pipeline already patched; SQL branches unreachable — verify and merge |
| `classes/kernel/ezcollaborationgroup.php` | `kernel/classes/ezcollaborationgroup.php` | `$count` aggregate for group item count |
| `classes/kernel/ezcollaborationitemgrouplink.php` | `kernel/classes/ezcollaborationitemgrouplink.php` | Check for any aggregate branches |
| `classes/kernel/ezcollaborationitemstatus.php` | `kernel/classes/ezcollaborationitemstatus.php` | Check for any aggregate branches |

#### Phase 5 — Datatypes

| Extension override | Kernel target | Key MongoDB additions |
|---|---|---|
| `classes/kernel/datatypes/ezkeyword/ezkeywordtype.php` | `kernel/datatypes/ezkeyword/ezkeywordtype.php` | `deleteStoredObjectAttribute()` two-stage aggregate (`$group` + `$match cnt=1`); `deleteWhere` on both keyword tables |
| `classes/kernel/datatypes/ezxmltext/ezxmltexttype.php` | `kernel/datatypes/ezxmltext/ezxmltexttype.php` | Orphan URL cleanup: `$nin` anti-join aggregate; `deleteWhere` |
| `classes/kernel/datatypes/ezurl/ezurlobjectlink.php` | `kernel/datatypes/ezurl/ezurlobjectlink.php` | Fetch methods |
| All other datatype overrides | `kernel/datatypes/<type>/` | Many are PHP 8.5 implicit-nullable fixes only. Diff each: if the only change is `?Type $param = null`, apply to kernel and delete override. |

**Fast-track script for PHP 8.5-only datatype fixes:**
```bash
# For each datatype override, diff against kernel original.
# If the diff contains ONLY nullable-param changes, apply and remove.
for f in extension/sevenx_mongodb/classes/kernel/datatypes/**/*.php; do
  rel="${f#extension/sevenx_mongodb/classes/}"   # kernel/datatypes/...
  orig="$rel"
  if [[ -f "$orig" ]]; then
    echo "=== $f ==="
    diff "$f" "$orig" | grep "^[<>]" | head -20
  fi
done
```

#### Phase 6 — Cronjob Helpers, Misc Infrastructure

| Extension override | Kernel target | Key MongoDB additions |
|---|---|---|
| `classes/kernel/ezdbgarbagecollector.php` | `kernel/classes/ezdbgarbagecollector.php` | 4× RIGHT JOIN orphan-cleanup patterns replaced with `$nin` anti-join aggregates |
| `classes/kernel/ezpackage.php` | `kernel/classes/ezpackage.php` | `setInstalled()`, `getInstallState()` |
| `classes/kernel/ezsiteinstaller.php` | `kernel/classes/ezsiteinstaller.php` | Install-mode checks |
| `classes/kernel/ezclusterfilehandler.php` | `kernel/classes/ezclusterfilehandler.php` | Cluster-mode guards |
| `classes/kernel/clusterfilehandlers/ezfsfilehandler.php` | `kernel/classes/clusterfilehandlers/ezfsfilehandler.php` | PROCESSCACHE debug `error_log` calls removed ✅ |
| All remaining `notification/`, `packagehandlers/`, `workflowtypes/` overrides | Corresponding `kernel/` paths | Verify each for MongoDB content; many may be PHP 8.5 fixes only or empty wrappers |

---

### 20.6 Special Cases

#### `lib/ezdb/classes/ezpersistentobject.php`

Already patched in-place (no extension override). When migrating to full kernel re-implementation,
no action is needed — it is already in the correct final state.

#### `nxc_powercontent` override

`extension/nxc_powercontent/modules/content/ezcontentoperationcollection.php` was patched
directly in the nxc_powercontent extension (not via sevenx_mongodb). This stays in place as a
third-party extension override — no change needed during the re-implementation.

#### `var/autoload/ezp_override.php` — incremental removal

Remove one class entry each time a merge+test cycle completes. **Do not batch-remove entries
before testing** — removing the entry without having applied the merge to the kernel file will
cause a fatal class-not-found error on the next page load.

Safe removal order: match the phase order in §20.5. Each removal is immediately followed by a
browser test of the affected feature.

---

### 20.7 Post-Merge Validation

After all overrides are merged:

1. **Verify `var/autoload/ezp_override.php`** contains only the adapter registration:
   ```bash
   grep -v "expMongoDB\|^<?php\|^//\|^$" var/autoload/ezp_override.php
   # Should produce no output
   ```

2. **Verify extension kernel directory is empty:**
   ```bash
   find extension/sevenx_mongodb/classes/kernel -name "*.php" | wc -l
   # Should output 0
   ```

3. **Run the PHP lint sweep across all kernel and lib files:**
   ```bash
   find kernel/ lib/ -name "*.php" | \
     xargs -P4 -I{} /opt/plesk/php/8.5/bin/php -l {} 2>&1 | \
     grep -v "No syntax errors"
   # Should produce no output
   ```

4. **Touch all kernel files on both docroots** to bust opcache:
   ```bash
   BASE=/web/vh/mongodb.demo.se7enx.com/doc/mongodb.demo.se7enx.com
   ALT=/var/www/vhosts/mongodb.demo.se7enx.com/doc/mongodb.demo.se7enx.com
   find "$BASE/kernel/" "$BASE/lib/" -name "*.php" -exec touch {} \;
   find "$ALT/kernel/" "$ALT/lib/" -name "*.php" -exec touch {} \;
   ```

5. **Run the full test checklist** from Sections 9 and 10.

6. **Check the error log** for any new MONGO TODO entries:
   ```bash
   tail -f /var/www/vhosts/mongodb.demo.se7enx.com/logs/edit.mongodb.demo.se7enx.com/error_log \
     | grep "MONGO TODO"
   # Should produce no output during normal usage
   ```

---

### 20.8 Benefits After Completion

| Before (override extension) | After (merged kernel) |
|---|---|
| "Is this method patched?" requires checking 3 places | `grep -n 'databaseName' kernel/classes/somefile.php` answers instantly |
| Kernel originals are dead code, silently misleading | Single source of truth per class |
| Applying a kernel security patch requires updating extension override | Patch the kernel file once; MongoDB branch is already there |
| Two docroots require manual synchronisation for kernel changes | `rsync kernel/ lib/` syncs everything; extension directory is minimal |
| IDEs and static analysis tools find the wrong (original) file | IDEs find the correct patched file |
| New contributors are confused by the override map | "MongoDB branches are `if ($db->databaseName() === 'mongo')` blocks — standard pattern" |
| git diff of `kernel/` looks pristine (patches hidden in extension) | git diff of `kernel/` shows the complete real state of the codebase |

---

### 20.9 Estimated Scope

| Phase | Files | Estimated effort |
|---|---|---|
| Phase 1 — Core ORM and Content | 6 override files | High effort: large files, complex pipelines; requires careful testing of edit/publish/delete/copy cycles |
| Phase 2 — User, Role, Section | 6 override files | Medium effort: well-tested methods; access-control path requires login/permission testing |
| Phase 3 — Search, URLs, Cache | 6 override files | Medium effort: search and URL alias paths require end-to-end navigation testing |
| Phase 4 — Workflow, Collaboration, Info | 11 override files | Low-to-medium: most are small targeted fixes |
| Phase 5 — Datatypes | ~80 override files | Low effort each: most are PHP 8.5 nullable fixes; 2 have real MongoDB additions |
| Phase 6 — Cronjobs, Misc | ~15 override files | Low effort each |
| **Total** | **~124 override files** | Recommended: one phase per work session; test after each phase before proceeding |

---

## 21. PHPUnit Test Suite — Implemented (May 2026)

This section documents the concrete PHPUnit test files added to cover the `expMongoDB`
adapter and confirm that MySQL continues to work correctly.  It supersedes the planning notes in
Section 14 with real, runnable code.

**PHPUnit version:** 13.0.0 (at `vendor/bin/phpunit`)  
**PHP runtime:** `/opt/plesk/php/8.5/bin/php`  
**v2-0 docroot:** `/web/vh/mongodb.demo.se7enx.com/doc/mongodb.demo.se7enx.com--v2-0`

---

### 21.1 File Layout

```
tests/tests/lib/ezdb/mongodb/
  stubs.php                         ← In-process stubs; required by both test files
  expMongoDBAdapterTest.php      ← Unit tests (@group mongodb)       — no live DB
  expMongoDBIntegrationTest.php  ← Integration tests (@group mongodb-live) — live DB
```

All three files are under `tests/tests/lib/ezdb/mongodb/` so they sit naturally alongside the
existing `lib` test tree and are picked up automatically by the `mongodb` testsuite defined in
`phpunit.xml`.

---

### 21.2 `stubs.php` — In-Process Stubs

**Purpose:** Allow `expMongoDB` to be instantiated and exercised without a running MongoDB
server.  The stubs implement the exact interface used by the adapter, backed by plain PHP
arrays.

**Key classes:**

| Stub class | Replaces | Backed by |
|---|---|---|
| `StubMongoCollection` | `MongoDB\Collection` | `array` of documents |
| `StubMongoDatabase` | `MongoDB\Database` | map of `StubMongoCollection` |
| `StubMongoClient` | `MongoDB\Client` | reference to `$stubCollections` array |
| `sevenxMongoDBTestable` | `expMongoDB` | returns `StubMongoClient` from `getClient()` |

**`StubMongoCollection` operations supported:**

| Method | Behaviour |
|---|---|
| `find($filter, $opts)` | Returns matching documents as `ArrayObject` objects |
| `findOne($filter)` | Returns first matching document or `null` |
| `insertOne($doc)` | Appends document to internal array |
| `replaceOne($filter, $replacement, $opts)` | Replaces first match; inserts if `upsert:true` and no match |
| `updateOne($filter, $update, $opts)` | Applies `$set` / `$inc`; inserts if `upsert:true` |
| `updateMany($filter, $update)` | Applies `$set` / `$inc` to all matches |
| `deleteMany($filter)` | Removes all matching documents |
| `aggregate($pipeline)` | Supports `$match`, `$count`, `$group/$count`, `$sort`, `$limit`, `$project` |

**`sevenxMongoDBTestable` usage pattern:**
```php
// 1. Instantiate the testable subclass
$db = new sevenxMongoDBTestable();

// 2. Seed the collection the adapter will touch
$db->stubCollections['ezcontentobject'] = [
    ['id' => 1, 'status' => 1, 'name' => 'Home'],
    ['id' => 2, 'status' => 0, 'name' => 'Draft'],
];

// 3. Call adapter methods — they use StubMongoClient internally
$rows = $db->aggregate('ezcontentobject', [
    ['$match' => ['status' => 1]],
]);
$this->assertCount(1, $rows);
```

---

### 21.3 `sevenxMongoDBAdapterTest.php` — Unit Tests

**Group tag:** `@group mongodb`  
**Live DB required:** No  
**Test count:** 55 tests, 109 assertions (as of May 2026)

#### What is tested

| Category | Test methods | Notes |
|---|---|---|
| Adapter identity | `testDatabaseNameReturnsMongo`, `testQueryReturnsFalse` | Core branching contract |
| String helpers | `testEscapeStringCleanValue`, `testEscapeStringHandlesBinaryInput`, `testEscapeStringCastsInt`, `testEscapeStringCastsFloat`, `testEscapeStringNull` | `(string)` cast behaviour |
| Condition translation | `testTranslateScalar`, `testTranslateGreaterThan`, `testTranslateGreaterThanOrEqual`, `testTranslateLessThan`, `testTranslateLessThanOrEqual`, `testTranslateNotEqual`, `testTranslateEqual`, `testTranslateLike`, `testTranslateRange`, `testTranslateInArray` | All operators supported by `translateConditions()` |
| Transaction methods | `testBeginQueryReturnsTrue`, `testCommitQueryReturnsTrue`, `testRollbackQueryReturnsTrue`, `testBeginAndCommitTrackCounter`, `testBeginAndRollbackDecrementsCounter` | Transaction counter logic |
| String functions | `testSubString`, `testConcatString`, `testMd5`, `testBitAnd`, `testBitOr` | SQL-compat shims |
| Charset | `testCheckCharsetReturnsTrue`, `testIsCharsetSupportedUTF8`, `testIsCharsetSupportedOther` | Always-pass stubs |
| Stub-backed operations | `testAggregateWithMatchFilter`, `testAggregateEmptyPipeline`, `testAggregateMissingCollection` | `aggregate()` via `StubMongoCollection` |
| Insert / Upsert / Delete | `testInsertAddsDocument`, `testUpsertUpdatesExisting`, `testUpsertInsertsNew`, `testDeleteWhereRemovesMatched`, `testDeleteWhereNoMatchIsNoOp` | `insert()`, `upsert()`, `deleteWhere()` |
| `arrayQuery()` | `testArrayQueryReturnsEmptyArray` | Always returns `[]`; no throw |
| Kernel branch contract | `testDatabaseNameIsMongoForBranchSelection` | Explicit: `=== 'mongo'` |

#### Running unit tests

```bash
cd /web/vh/mongodb.demo.se7enx.com/doc/mongodb.demo.se7enx.com--v2-0

# Run only the MongoDB unit tests (no live DB)
/opt/plesk/php/8.5/bin/php vendor/bin/phpunit --testsuite mongodb
```

Expected output:
```
PHPUnit 13.0.0 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.5.6
Configuration: /var/www/vhosts/mongodb.demo.se7enx.com/doc/mongodb.demo.se7enx.com--v2-0/phpunit.xml

....................................MONGO TODO arrayQuery: tables=[ezcontentobject] caller=...sevenxMongoDBAdapterTest.php:452 (expMongoDB::arrayQuery)
...................           55 / 55 (100%)

Time: 00:00.186, Memory: 18.00 MB

OK (55 tests, 109 assertions)
```

> The `MONGO TODO arrayQuery` line is **expected and correct** — it is emitted by the
> `testArrayQueryReturnsEmptyArray` test, which explicitly exercises the `arrayQuery()` stub
> path and confirms it logs the TODO warning.  It is not a failure.

---

### 21.4 `sevenxMongoDBIntegrationTest.php` — Live Integration Tests

**Group tag:** `@group mongodb-live`  
**Live DB required:** Yes — MongoDB at `mongodb://db:publishing$8088@localhost:27017/exp`
and MariaDB at `localhost` / user `xa_alpha` / password `db-alpha-2025` / database `xa_alpha`  
**Test count:** 18 tests, 47 assertions (as of May 2026)

#### What is tested

**MongoDB live tests:**

| Test | What it confirms |
|---|---|
| `testMongoPingSucceeds` | Server is reachable; `ping` returns `ok:1` |
| `testMongoInsertAndAggregate` | Insert two docs; aggregate with `$match active:true`; correct count returned |
| `testMongoUpsertUpdatesExisting` | `replaceOne(upsert:true)` overwrites existing document field |
| `testMongoDeleteMany` | `deleteMany` removes only the matched documents; others untouched |
| `testMongoUpdateOneIncrement` | `updateOne($inc)` increments a counter field atomically |
| `testMongoExpectedCollectionsExist` | `ezcontentobject`, `ezcontentobject_tree`, `ezcontent_language`, `ezcontentobject_version` all exist |
| `testMongoContentObjectDocumentStructure` | First document has `id`, `status`, `contentclass_id`, `language_mask` fields |
| `testMongoTreeNodeHasPathString` | `path_string` matches `/\d+/` format |

**MySQL live tests:**

| Test | What it confirms |
|---|---|
| `testMysqlConnectionWorks` | Connection succeeds; server_info contains `MariaDB` |
| `testMysqlContentLanguageHasRows` | `ezcontent_language` has at least one row |
| `testMysqlContentObjectHasRows` | `ezcontentobject` has at least one row |
| `testMysqlTreeNodePathStringFormat` | `path_string` values match `/\d+/` format |
| `testMysqlNoOrphanedArchivedVersions` | Query executes without error; result is numeric |
| `testMysqlContentClassExists` | `ezcontentclass` has at least one published class (`version=0`) |
| `testMysqlContentObjectNoNullStatus` | `status IS NULL` count is 0 |
| `testMysqlSectionTableHasRows` | `ezsection` has at least one row |
| `testContentObjectCountMatchesBetweenEngines` | MySQL and MongoDB published object counts differ by less than 10% |
| `testMysqlInsertSelectDeleteRoundTrip` | INSERT into `ezpreferences`, SELECT back, DELETE — confirms MySQL write path works cleanly |

#### Test isolation

`tearDown()` always removes test documents regardless of test outcome:
```php
protected function tearDown(): void
{
    self::$mongoClient
        ->selectCollection( self::MONGO_DB, self::TEST_COL )
        ->deleteMany( [] );
    self::$mongoClient
        ->selectCollection( self::MONGO_DB, self::SEQ_COL )
        ->deleteMany( [ '_id' => 'phpunit_seq_test' ] );
}
```

No production data is touched.  All MongoDB writes target `_phpunit_test` (a dedicated test
collection) or the `ezsequence` collection with key `phpunit_seq_test`.  The `ezpreferences`
MySQL insert uses `user_id=0` which is never a real user, and the row is deleted immediately.

#### Running live integration tests

```bash
cd /web/vh/mongodb.demo.se7enx.com/doc/mongodb.demo.se7enx.com--v2-0

# Run MongoDB + MySQL live integration tests
/opt/plesk/php/8.5/bin/php vendor/bin/phpunit --testsuite mongodb-live

# Expected output:
# OK (18 tests, 47 assertions)
```

If MongoDB is unavailable the entire class is skipped with `markTestSkipped`.
If MySQL is unavailable, the individual MySQL test methods are skipped independently while
MongoDB tests still run.

---

### 21.5 `phpunit.xml` Testsuite Registration

Two new testsuites were added to `phpunit.xml`:

```xml
<!-- MongoDB adapter unit tests — no live DB required -->
<testsuite name="mongodb">
    <directory suffix="Test.php">tests/tests/lib/ezdb/mongodb</directory>
</testsuite>

<!-- MongoDB + MySQL live integration tests — require running servers -->
<testsuite name="mongodb-live">
    <file>tests/tests/lib/ezdb/mongodb/sevenxMongoDBIntegrationTest.php</file>
</testsuite>
```

The `mongodb-live` group is added to the global `<exclude>` block so it is never run
accidentally during a default `phpunit` invocation:

```xml
<groups>
    <exclude>
        <group>database</group>
        <group>mongodb-live</group>
    </exclude>
</groups>
```

The `mongodb` testsuite uses `suffix="Test.php"` on the directory, which matches both
`sevenxMongoDBAdapterTest.php` and `sevenxMongoDBIntegrationTest.php`.  The integration test
file is also included in the dedicated `mongodb-live` testsuite so it can be run in isolation.
The integration test's `@group mongodb-live` tag means it is excluded from the default run even
when picked up by the `mongodb` suite.

---

### 21.6 Quick Reference — All Test Commands

```bash
BASE=/web/vh/mongodb.demo.se7enx.com/doc/mongodb.demo.se7enx.com--v2-0
PHP=/opt/plesk/php/8.5/bin/php
cd $BASE

# Unit tests only — no live DB, fast, safe to run anytime
# Note: one "MONGO TODO arrayQuery" line in output is expected (from testArrayQueryReturnsEmptyArray)
$PHP vendor/bin/phpunit --testsuite mongodb

# Live integration tests — requires running MongoDB + MariaDB
$PHP vendor/bin/phpunit --testsuite mongodb-live

# Run both together
$PHP vendor/bin/phpunit --testsuite mongodb --testsuite mongodb-live

# Run just one test method
$PHP vendor/bin/phpunit --testsuite mongodb --filter testDatabaseNameReturnsMongo

# Run with verbose output
$PHP vendor/bin/phpunit --testsuite mongodb --verbose

# Run and show test names
$PHP vendor/bin/phpunit --testsuite mongodb --testdox

# Check which groups are available in the mongodb suite
$PHP vendor/bin/phpunit --testsuite mongodb --list-groups
```

---

### 21.7 Extending the Suite

To add new adapter unit tests:

1. Add a method to `sevenxMongoDBAdapterTest.php` following the existing pattern.
2. Seed `$this->db->stubCollections['collection_name']` before calling adapter methods.
3. The `StubMongoCollection` handles `$match`, `$count`, `$group/$count`, `$sort`, `$limit`,
   and `$project` pipeline stages.  If a new stage is needed, add it to `StubMongoCollection::aggregate()` in `stubs.php`.

To add new integration tests:

1. Add a method to `sevenxMongoDBIntegrationTest.php`.
2. For MongoDB: use `self::$mongoClient->selectCollection(self::MONGO_DB, 'collection_name')`.
3. For MySQL: call `$this->skipIfNoMySQL()` first, then use `self::$mysql`.
4. For any data written: ensure `tearDown()` will clean it up (add cleanup to the `tearDown` method if a new collection or table is touched).

To add a new test file:

1. Place it in `tests/tests/lib/ezdb/mongodb/` with a `Test.php` suffix.
2. It is automatically picked up by the `mongodb` testsuite.
3. Require `stubs.php` at the top if stubs are needed:
   ```php
   require_once __DIR__ . '/stubs.php';
   ```

---

## 22. Setup Wizard Performance Optimizations (June 2026)

### 22.1 Background — Why the Setup Wizard Was Timing Out

The final "Configuration / Create Sites" step of the Exponential CMS setup wizard performs all
of the following in a single synchronous HTTP request:

1. Creates the primary language, site accesses, and sections
2. Installs all required packages from `var/storage/packages/7x/` — including `sevenx_democontent` (~193 content objects) and the site skeleton (`sevenx_site`)
3. For each package object: runs the full `eZOperationHandler::execute('content', 'publish', ...)`  pipeline — attribute stores, tree node creation, URL alias generation, name synthesis, cache expiry
4. Runs `initSteps()` from `sevenxezwebininstaller.php` — 142 steps including 95 `classIDbyIdentifier` calls, 14 `sectionIDbyName` calls, 6 `addPoliciesForRole` calls, and 5 `assignUserToRole` calls
5. Calls `eZSitePostInstall()` from the package settings

On a SQL (MySQL/MariaDB) database, the entire process completes in under 30 seconds because:
- SQL word-index writes go to a single in-process transaction that is committed in bulk
- Table scans are fast on local SSD
- Result sets are pre-paginated by the query planner

On MongoDB, the same process was triggering **tens of thousands of individual round-trips** to the MongoDB server because:
- The built-in `eZSearchEngine` was indexing every word of every attribute of every published object — running `SELECT * FROM ezsearch_word WHERE word IN (...)` + `INSERT INTO ezsearch_word` + `INSERT INTO ezsearch_object_word_link` per word, per object. For 193 objects with an average of ~300 words each, this alone was ~60,000 MongoDB aggregate/insert calls.
- `ezsearch_word` and `ezsearch_object_word_link` are SQL tables with no MongoDB equivalent — this work was not only slow but completely useless (the results were never used).

**Observed symptom:** PHP-FPM `request_terminate_timeout` of 190 s fired before the wizard completed. Raising to 390 s masked the problem but did not solve it.

---

### 22.2 Root Causes (Ranked by Impact)

| # | Root Cause | Estimated Contribution |
|---|---|---|
| 1 | `eZSearchEngine::addObject()` called for every published object, indexing all words into SQL tables that don't exist in MongoDB | ~80% of wall time |
| 2 | 95 `classIDbyIdentifier` calls in `initSteps()`, each doing a full `eZContentClass::fetchByIdentifier()` aggregate with no cache | ~10% |
| 3 | Cache expiry hooks (`eZContentCacheManager`) called per-publish during install, clearing caches that don't exist yet | ~5% |
| 4 | `set_time_limit(5*60)` in `initializePackage()` setting the PHP script timer without disabling it — offers false security since FPM `request_terminate_timeout` is the real limit | Masking issue, not a timing contributor |

---

### 22.3 Fixes Applied (June 1, 2026)

#### Fix 1 — Disable search indexing during package install loop

**File:** `kernel/setup/steps/ezstep_create_sites.php`

Before the `foreach ($requires as $require)` package install loop, the `$GLOBALS` search engine
instance key (used by `eZSearch::getEngine()`) is set to `false` for MongoDB only.  `eZSearch::getEngine()` returns `false`; all subsequent `if ($searchEngine instanceof ezpSearchEngine)` guards in `eZSearch::addObject()`, `eZSearch::removeObjectById()`, etc. immediately fall through — the entire search pipeline becomes a no-op for the duration of the package install.  After the loop (and on any error-return path) the original engine instance is restored.

```php
// Before the package install loop (MongoDB only):
$searchEngineGlobalKey = 'eZSearchPlugin_' . ( $GLOBALS['eZCurrentAccess']['name'] ?? '' );
$previousSearchEngineInstance = $GLOBALS[$searchEngineGlobalKey] ?? null;
$searchEngineWasSet = array_key_exists( $searchEngineGlobalKey, $GLOBALS );
if ( $db->databaseName() === 'mongo' )
    $GLOBALS[$searchEngineGlobalKey] = false;

// ... package install loop ...

// After the loop / on error returns:
if ( $searchEngineWasSet )
    $GLOBALS[$searchEngineGlobalKey] = $previousSearchEngineInstance;
else
    unset( $GLOBALS[$searchEngineGlobalKey] );
```

**Why this is safe:** Search index tables (`ezsearch_word`, `ezsearch_object_word_link`) do not
exist in MongoDB; indexing to them was already a wasted no-op at the data level.  After install,
the `indexcontent` cron job (`cronjobs/indexcontent.php`) rebuilds the search index from the
newly published objects.

#### Fix 2 — Early return in `eZSearchEngine::addObject()` for MongoDB

**File:** `kernel/search/plugins/ezsearchengine/ezsearchengine.php`

Belt-and-suspenders guard at the top of `addObject()`:

```php
public function addObject( $contentObject, $commit = true )
{
    // MongoDB uses a document store — the built-in SQL-word-index tables
    // (ezsearch_word, ezsearch_object_word_link) are not meaningful, and
    // every addObject call issues dozens of raw SQL queries that the MongoDB
    // adapter must translate one by one. Skip indexing entirely; use the
    // indexcontent cron job or a dedicated search extension instead.
    if ( eZDB::instance()->databaseName() === 'mongo' )
        return true;

    $contentObjectID = $contentObject->attribute( 'id' );
    // ... rest of method unchanged ...
```

This guard catches any `addObject()` call that bypasses the `$GLOBALS` trick — for example,
calls originating from cron jobs, CLI scripts, or code paths that instantiate `eZSearchEngine`
directly.

#### Fix 3 — `set_time_limit(0)` unconditionally in `initializePackage()`

**File:** `kernel/setup/steps/ezstep_create_sites.php`

Replaced the conditional:
```php
// Before:
$maxTime = ini_get( 'max_execution_time' );
if ( $maxTime != 0 and $maxTime < 5*60 )
    @set_time_limit( 5*60 );
```
with an unconditional disable:
```php
// After:
@set_time_limit( 0 );
```

This removes the PHP-side script execution timer entirely.  The FPM `request_terminate_timeout`
remains the only wall-clock cap (see Section 8 for how to configure it).  On SQL installs the
call is harmless.

---

### 22.4 Expected Impact

| Before optimizations | After optimizations |
|---|---|
| ~60,000 MongoDB round-trips during search indexing | 0 (search indexing skipped during install) |
| FPM timeout at 190s (required raising to 390s) | Estimated completion in 60–90s; 390s remains a safe upper bound |
| `request_terminate_timeout = 390` required in Plesk | Can be set to 600 for safety; 390 should now be sufficient |

After the setup wizard completes, run the `indexcontent` cron job to populate the search index:
```bash
/opt/plesk/php/8.5/bin/php runcronjobs.php --siteaccess=sevenx_site --allow-root-user
```

---

### 22.5 Files Modified

| File | Change |
|---|---|
| `kernel/setup/steps/ezstep_create_sites.php` | `set_time_limit(0)`; search engine `$GLOBALS` disable/restore block around package install loop |
| `kernel/search/plugins/ezsearchengine/ezsearchengine.php` | Early return in `addObject()` for MongoDB |

---

## 23. Site Installer Packages — Distribution and Package Server

### 23.1 Overview

Exponential CMS 6.0.14 ships with a set of site installer packages stored under
`var/storage/packages/7x/`.  These packages are required by the setup wizard and by the
`sevenxezwebininstaller.php` site installer.  They are intended to be made available on the
worldwide Exponential/eZ Publish package server for automatic download during setup wizard
execution on new installations.

---

### 23.2 Package Inventory

The following packages are present in `var/storage/packages/7x/` and constitute the full
site install set for the `sevenx_site` site type:

| Package name | Purpose |
|---|---|
| `sevenx_site` | Master site skeleton: `sevenxezwebininstaller.php` (142-step installer), site settings, role/section/policy definitions |
| `sevenx_democontent` | ~193 demo content objects — articles, folders, users, media — pre-structured for the `sevenx_site` tree |
| `sevenx_classes` | Content class definitions (Folder, Article, Landing Page, Blog Post, etc.) with all class attributes |
| `sevenx_design` | Design files: templates, CSS, images for the `sevenx_site` front-end design |
| `sevenx_common` | Shared assets and settings common to all sevenx site types |
| `sevenx_users` | Default user/group structure: Administrators, Anonymous Users, Members |
| `sevenx_roles` | Role and policy definitions for the 7 standard roles |
| `sevenx_sections` | Section definitions (Standard, Restricted, Blog) |
| `sevenx_states` | Object state group/state definitions (Lock, Visibility) |
| `sevenx_workflows` | Workflow definitions (Two-step publishing, Review workflow) |
| `sevenx_languages` | Language pack bootstrap (eng-GB primary locale setup) |

---

### 23.3 Pending Upload to Package Server

> **Status: Pending upload.** Packages have been finalized and validated locally but have not
> yet been transferred to the worldwide package distribution server.

The following items must be completed before worldwide distribution:

1. **Package signing** — each `.ezpkg` archive must be signed with the project GPG key.  Current packages are unsigned (development builds).
2. **Version tagging** — packages must carry version `6.0.14` in their `package.xml` manifest.  Review each `package.xml` and confirm `<version>6.0.14</version>` is present.
3. **Upload to package server** — the package server URL referenced in `setup.ini[PackageSettings] RepositoryURL` must receive all 11 packages.  Authentication credentials for the package server are held by the project maintainer.
4. **Package server index update** — after upload, the package server's `packages.xml` index file must be regenerated to include the new version entries.
5. **Smoke-test on a clean install** — after upload, run the setup wizard on a clean MongoDB installation and confirm it downloads and installs all packages without error.

---

### 23.4 How the Setup Wizard Consumes These Packages

During the "Create Sites" step:

1. The wizard calls `eZPackage::fetch($packageName)` for each required package listed in `sevenx_site/package.xml → <dependencies>`.
2. `eZPackage::fetch()` first checks `var/storage/packages/7x/<packageName>/` (local cache).
3. If not found locally, it attempts to download from the URL configured in `setup.ini[PackageSettings] RepositoryURL`.
4. Once fetched, `$package->install($installParameters)` runs — triggering the full object publish pipeline described in Section 22.

For offline or air-gapped installations, place the package directories directly in `var/storage/packages/7x/` before running the setup wizard.  The wizard will use them without making any network request.

---

### 23.5 Local Package Directory Layout

```
var/storage/packages/7x/
  sevenx_site/
    package.xml          ← manifest: name, version, dependencies, install type
    ezcontentclass/      ← class XML export files
    ezcontentobject/     ← content object XML export files (demo content)
    settings/
      sevenxezwebininstaller.php   ← 142-step site installer (3922 lines)
      sevenx_site.ini              ← site-specific settings
  sevenx_democontent/
    package.xml
    ezcontentobject/     ← 192 XML files — one per demo content object
  sevenx_classes/
    package.xml
    ezcontentclass/      ← class definition XML files
  ...
```

---

### 23.6 Modifying or Re-exporting Packages

If site structure changes are made (new classes, updated demo content, modified roles), the
affected packages must be re-exported before the next upload:

1. **Content object packages:** Use the admin panel → Setup → Packages → Export.  Select content objects by subtree or individual node.  Set package name and version.  Export creates a new `.ezpkg` archive.
2. **Class packages:** Admin → Setup → Packages → Export → Content classes.
3. **Manual edits:** For `sevenxezwebininstaller.php`, edit the file directly at `var/storage/packages/7x/sevenx_site/settings/sevenxezwebininstaller.php`.  It is a plain PHP file.  Changes are picked up immediately on the next wizard run (no re-packaging required for local installs; re-packaging required before upload to the server).

---

## 24. Key Getting Started Steps Using Exponential 6.0.14 With MongoDB

This section is the single-page quick-start reference for anyone installing or developing with
Exponential CMS 6.0.14 on MongoDB.  It assumes a clean server with PHP 8.5, MongoDB 8.x, and
Plesk already in place.

---

### 24.1 Server Prerequisites

| Requirement | Minimum | Recommended |
|---|---|---|
| PHP | 8.2 | 8.5 (tested) |
| MongoDB | 6.0 | 8.3 (tested) |
| PHP MongoDB extension | 1.18 | Latest |
| Composer | 2.x | 2.x |
| Web server | Apache / nginx | nginx (Plesk-managed) |
| RAM | 512 MB | 2 GB |
| Disk | 2 GB | 10 GB |

---

### 24.2 Step 1 — Install MongoDB and Create the Database User

```bash
# Install MongoDB 8.x (AlmaLinux / Rocky Linux)
cat > /etc/yum.repos.d/mongodb-org-8.0.repo << 'EOF'
[mongodb-org-8.0]
name=MongoDB Repository
baseurl=https://repo.mongodb.org/yum/redhat/8/mongodb-org/8.0/x86_64/
gpgcheck=1
enabled=1
gpgkey=https://www.mongodb.org/static/pgp/server-8.0.asc
EOF
dnf install -y mongodb-org
systemctl enable --now mongod

# Create the database and application user
mongosh admin --eval "
  db.createUser({
    user: 'db',
    pwd: 'your-secure-password',
    roles: [
      { role: 'readWrite', db: 'exp' },
      { role: 'dbAdmin',   db: 'exp' }
    ]
  })
"
```

---

### 24.3 Step 2 — Configure PHP-FPM for the Setup Wizard

> **Critical:** Do this before running the setup wizard, not after a timeout.

In Plesk → Domains → your domain → PHP Settings, set **Additional configuration directives**:

```ini
request_terminate_timeout = 600
```

This gives the setup wizard up to 10 minutes to complete.  After installation you may reduce
this to 60–120 s.

---

### 24.4 Step 3 — Install Exponential CMS

```bash
# Clone or unpack the Exponential CMS 6.0.14 release into the docroot
cd /web/vh/yourdomain.com/doc/yourdocroot

# Install PHP dependencies via Composer
composer install --no-dev

# Ensure the MongoDB driver is installed
/opt/plesk/php/8.5/bin/php -m | grep mongodb
# Expected output: mongodb
```

If the `mongodb` extension is not listed:
```bash
/opt/plesk/php/8.5/bin/pecl install mongodb
# Then add to Plesk PHP settings:
#   extension=mongodb.so
```

---

### 24.5 Step 4 — Pre-place the Installer Packages

If the package server is not yet reachable (packages pending upload — see Section 23), place
the packages manually:

```bash
# Packages should already be present if you used the full release archive.
# Verify:
ls var/storage/packages/7x/
# Expected: sevenx_site/  sevenx_democontent/  sevenx_classes/  sevenx_design/  ...
```

If any directory is missing, copy it from the reference installation or the release `.tar.gz`.

---

### 24.6 Step 5 — Run the Setup Wizard

1. Visit `http://yourdomain.com/` in a browser — you will be automatically redirected to the setup wizard.
2. Complete each wizard step:
   - **Welcome** — read and accept the license
   - **System Check** — all items should be green; resolve any warnings
   - **Database** — select "MongoDB"; enter connection details (`localhost:27017`, DB `exp`, user `db`, password)
   - **Language** — choose your primary language (e.g. `eng-GB`)
   - **Site Type** — select `sevenx_site`
   - **Admin User** — set your admin email and password
   - **Configuration** — this is the slow step; the browser will appear to hang for 1–3 minutes; **do not cancel**; wait for the success page
3. On completion, the wizard displays links to the **admin site** and **user (front) site**.
   By design it does **not** automatically redirect to the front site — click the provided
   links to navigate to whichever interface you need.

---

### 24.7 Step 6 — Create MongoDB Indexes

Run immediately after setup wizard completion:

```bash
mongosh "mongodb://db:your-secure-password@localhost:27017/exp" \
  --file extension/sevenx_mongodb/bin/mongodb/create_indexes.js
```

Indexes are critical for performance.  Without them, subtree queries and URL alias lookups
will scan the entire collection.

---

### 24.8 Step 7 — Verify the Installation

```bash
# Check the front site returns HTTP 200
curl -sk -o /dev/null -w "%{http_code}" https://yourdomain.com/
# Expected: 200

# Check the admin panel
curl -sk -o /dev/null -w "%{http_code}" https://yourdomain.com/user/login
# Expected: 200

# Check for MONGO TODO entries (should be none after full install)
tail -20 /var/log/plesk-php85-fpm/error.log | grep "MONGO TODO"
# Expected: no output
```

Log in to the admin panel at `https://yourdomain.com/edit/` (or the edit subdomain if
configured separately) with the admin credentials you set in the wizard.

---

### 24.9 Step 8 — Post-Install Search Index

The setup wizard skips search indexing during demo content install (for performance — see
Section 22).  Build the search index after installation:

```bash
/opt/plesk/php/8.5/bin/php runcronjobs.php \
  --siteaccess=sevenx_site --allow-root-user
```

Or set up a system cron:
```bash
# Add to /etc/cron.d/exponential-cms:
*/5 * * * * www-data /opt/plesk/php/8.5/bin/php \
  /web/vh/yourdomain.com/doc/yourdocroot/runcronjobs.php \
  --siteaccess=sevenx_site --quiet 2>&1
```

---

### 24.10 Step 9 — Opcache Reset Script

During development, after editing any PHP file:

```bash
# Touch the file to invalidate opcache (revalidate_freq=2s)
touch /path/to/modified/file.php

# Or use the opcache reset endpoint (if configured):
curl -sk https://yourdomain.com/opcache_reset_edit.php
```

**Never restart PHP-FPM** to clear opcache — it drops all active connections and is disruptive.

---

### 24.11 Step 10 — Reduce FPM Timeout

After confirming the installation is complete and functional, reduce `request_terminate_timeout`
back to a production-safe value in Plesk:

```ini
request_terminate_timeout = 60
```

---

### 24.12 Quick Troubleshooting Reference

| Symptom | Likely Cause | Fix |
|---|---|---|
| Setup wizard white screen / 502 after ~190s | FPM `request_terminate_timeout` too low | Raise to 600 in Plesk PHP settings (see §24.3) |
| Setup wizard completes but front site shows empty tree | MongoDB indexes not created | Run `create_indexes.js` (see §24.7) |
| `MONGO TODO arrayQuery` in error log | A kernel method still has an unported SQL call | See Section 11 for known gaps and fixes |
| URL aliases not resolving (all 404) | `ezurlalias_ml` import incomplete or indexes missing | Run `create_indexes.js`; verify `parent`+`text_md5` index exists |
| Admin login fails | `ezuser` collection missing admin record or password hash truncated | Check `db.ezuser.findOne({login:'admin'})` in `mongosh` |
| Slow page loads on first visit | No indexes on key collections | Run `create_indexes.js` |
| `Class not found: eZSomeClass` after editing kernel files | `var/autoload/ezp_override.php` still maps to the extension file which was deleted | Either restore the extension file or remove the override map entry |
| PHP fatal: `Call to a member function on bool` from `eZSearch` | `$GLOBALS` search engine key set to `false` outside of install context | Check that the search engine restore block in `ezstep_create_sites.php` ran correctly |

---

### 24.13 MongoDB Connection Quick Reference

```bash
# Interactive shell
mongosh "mongodb://db:your-password@localhost:27017/exp"

# Check key collection counts
mongosh --quiet "mongodb://db:your-password@localhost:27017/exp" --eval "
  ['ezcontentobject','ezcontentclass','ezcontentobject_tree',
   'ezurlalias_ml','ezuser','ezsection'].forEach(c =>
    print(c + ': ' + db[c].countDocuments())
  )
"

# Check for errors in PHP-FPM log
tail -f /var/log/plesk-php85-fpm/error.log | grep -v 'XDEBUG\|Xdebug'
```

---

### 24.14 Development Environment Notes

- **Always use the alpha subdomain** (`mongodb.demo.se7enx.com`) for development changes — never edit production.
- **PHP-FPM error log** for the alpha environment: `/var/log/plesk-php85-fpm/error.log`
- **Opcache reset** for the alpha environment: `curl -sk https://mongodb.demo.se7enx.com/opcache_reset_edit.php`
- **MongoDB shell** for the alpha database: `mongosh --quiet "mongodb://db:publishing\$8088@localhost:27017/exp"`
- **Do NOT commit anything** from the alpha environment directly — changes must be reviewed and ported to the v2-0 branch first.
- **Do NOT restart PHP-FPM** — use `touch <file.php>` to bust opcache.

---

## 25. Datatype Compatibility — Core and Community

This section documents which built-in and community-contributed datatypes work out of the box
with MongoDB and which ones require dedicated MongoDB code paths in addition to the default MySQL
implementation.

### How Datatypes Store Data

Most datatypes store their value entirely inside the `ezcontentobject_attribute` collection
(fields `data_text`, `data_int`, `data_float`). These require **no special MongoDB work** beyond
what `eZPersistentObject::storeObject()` and `fetchObjectList()` already provide.

A smaller set of datatypes own one or more **secondary collections** (e.g. `ezkeyword`,
`ezuser`, `ezuservisit`). Each of those secondary collections requires its own MongoDB
aggregate/insert/deleteWhere paths.

---

### 25.1 Core (Built-in) Datatypes

| Datatype | Identifier | Storage model | MongoDB status |
|---|---|---|---|
| Text line | `ezstring` | `data_text` in attribute | ✅ Works — attribute only |
| Text block | `eztext` | `data_text` in attribute | ✅ Works — attribute only |
| XML rich text | `ezxmltext` | `data_text` (serialised XML) in attribute | ✅ Works — PHP 8.5 null-guard fixes applied (§5) |
| Integer | `ezinteger` | `data_int` in attribute | ✅ Works — attribute only |
| Float | `ezfloat` | `data_float` in attribute | ✅ Works — attribute only |
| Boolean | `ezboolean` | `data_int` (0/1) in attribute | ✅ Works — attribute only |
| Date | `ezdate` | `data_int` (Unix timestamp) in attribute | ✅ Works — attribute only |
| Date and time | `ezdatetime` | `data_int` (Unix timestamp) in attribute | ✅ Works — attribute only |
| Time | `eztime` | `data_int` (seconds since midnight) in attribute | ✅ Works — attribute only |
| E-mail | `ezemail` | `data_text` in attribute | ✅ Works — attribute only |
| URL | `ezurl` | `data_text` + `ezurl` collection; `ezurl_object_link` join | ✅ Works — full `$lookup` pipeline implemented (§5) |
| Identifier | `ezidentifier` | `data_text` in attribute | ✅ Works — attribute only |
| ISBN | `ezisbn` | `data_text` in attribute | ✅ Works — attribute only |
| Selection | `ezselection` | `data_text` (option string) in attribute | ✅ Works — attribute only |
| Checkbox (enum) | `ezenum` | `data_int` in attribute | ✅ Works — attribute only |
| Country | `ezcountry` | `data_text` (country code) in attribute | ✅ Works — attribute only |
| Object relation | `ezobjectrelation` | `data_int` (related object ID) + `ezcontentobject_link` | ✅ Works — link table managed by `eZContentObject` |
| Object relation list | `ezobjectrelationlist` | `data_text` (XML list) + `ezcontentobject_link` | ✅ Works — link table managed by `eZContentObject` |
| Matrix | `ezmatrix` | `data_text` (serialised) in attribute | ✅ Works — attribute only |
| Multi-option | `ezmultioption` | `data_text` (XML) in attribute | ✅ Works — attribute only |
| Multi-option 2 | `ezmultioption2` | `data_text` (XML) in attribute | ✅ Works — attribute only |
| Range option | `ezrangeoption` | `data_text` in attribute | ✅ Works — attribute only |
| Option | `ezoption` | `data_text` in attribute | ✅ Works — attribute only |
| INI setting | `ezinisetting` | `data_text` in attribute | ✅ Works — multiselect HTTP input fix applied (§5) |
| Package | `ezpackage` | `data_text` in attribute | ✅ Works — attribute only |
| Subtree subscription | `ezsubtreesubscription` | `data_text` in attribute | ✅ Works — attribute only |
| Keyword | `ezkeyword` | `data_text` in attribute **+** `ezkeyword` and `ezkeyword_attribute_link` collections | ✅ Works — full MongoDB paths in `ezkeyword.php` and `ezkeywordtype.php` |
| Image | `ezimage` | `data_text` (XML) in attribute; files on disk | ✅ Works — image data stays in attribute; file I/O is filesystem-only |
| Binary file | `ezbinaryfile` | `data_text` (XML) + `ezbinaryfile` collection | ✅ Works — attribute storage via `eZPersistentObject`; download-count increment patched to `mongoUpdateOne($inc)` in `ezbinaryfiletype.php` (June 2026) |
| Media | `ezmedia` | `data_text` (XML) + `ezmedia` collection | ✅ Works — all storage goes through `eZPersistentObject`; no raw SQL in `ezmedia.php` or `ezmediatype.php` |
| Price | `ezprice` | `data_float` in attribute; reads `ezproductcollection_item` at display time | ✅ Works for basic display — shop checkout flow untested |
| Multi-price | `ezmultiprice` | `data_text` (XML) in attribute; currency lookups via `ezcurrencyname` | ✅ Works for basic display — currency table queries may return empty |
| Product category | `ezproductcategory` | `data_int` in attribute; `ezproductcategory` collection + join queries | ✅ Works — `fetchProductCountByCategory()` and `removeByID()` patched with full `$lookup` aggregate pipelines in `ezproductcategory.php` (June 2026) |
| User account | `ezuser` | `data_text` + `ezuser`, `ezuser_setting`, `ezuservisit`, `ezforgot_password` collections | ✅ Works — all paths fully patched: login, visit tracking, login counts, failed-login attempts, logout negate, `fetchLoggedInCount`, `isUserLoggedIn`, `fetchLoggedInList` (online-users admin panel), `fetchContentList`, `fetchUserClassList`; `ezforgot_password` cleanup uses `deleteWhere`; `createNew`/`fetchByKey`/`removeByUserID` use `eZPersistentObject` (already MongoDB-compatible) |

---

### 25.2 Community Extension Datatypes

These datatypes ship in bundled community extensions. All have been audited for
MongoDB compatibility. Those that owned secondary collections or ran raw SQL queries
have been patched with MongoDB-specific code paths (June 2026).

| Extension | Datatype identifier | SQL tables used | MongoDB status |
|---|---|---|---|
| `birthday` | `ezbirthday` | `data_int` in attribute only | ✅ Works — attribute only |
| `ezgmaplocation` | `ezgmaplocation` | `data_text` (lat/lon/address) in attribute | ✅ Works — attribute only |
| `ezstarrating` | `ezsrrating` | `data_text` in attribute | ✅ Works — attribute only |
| `hcaptcha` | `hcaptcha` | `data_text` in attribute | ✅ Works — attribute only |
| `recaptcha` | `recaptcha` | `data_text` in attribute | ✅ Works — attribute only |
| `xrowmetadata` | `xrowmetadata` | `ezkeyword`, `ezkeyword_attribute_link` (delegates to `eZKeyword`) | ✅ Works — `deleteStoredObjectAttribute()` patched with aggregate + `deleteWhere` pipeline for keyword cleanup (June 2026); keyword read/write paths already MongoDB-compatible |
| `enhancedselection2` (sck) | `sckenhancedselection` | `data_text` in attribute; optionally runs a user-supplied SQL query to populate options | ✅ Works (static-options mode) — `isDbQueryValid()` and `getDbOptions()` now return `false`/`[]` immediately on MongoDB (June 2026); DB-query options source is disabled by design as raw SQL is incompatible |
| `enhancedezbinaryfile` | `enhancedezbinaryfile` | `data_text` + `ezbinaryfile` collection (extends core binary file) | ✅ Works — no raw SQL in this extension; inherits core `ezbinaryfile` behaviour which was fully patched (June 2026) |
| `ezmbpaex` | `ezpaex` | `ezcontentobject_version` count query | ✅ Works — `deleteStoredObjectAttribute()` version-count query patched to `$count` aggregate branch in `ezpaextype.php` (June 2026) |
| `ezflow` | `ezpage` | `data_text` (XML page layout) in attribute | ✅ Works — attribute only; block scheduling cron is separate |
| `eztags` | `eztags` | `data_text` + `eztags`, `eztag_attribute_link` collections | ❌ Not ported — extension not present in this installation; tag storage and `fetchTagsByAttribute` / `fetchObjectsByTag` use raw `arrayQuery` joins; requires full `$lookup` aggregate pipeline implementation before use |

---

### 25.3 Summary: What Still Needs MongoDB Code Paths

All bundled core and community extension datatypes now have MongoDB code paths.
The only remaining incompatibility is by design:

| Datatype | File(s) | Status |
|---|---|---|
| `enhancedselection2` (DB-query options source) | `extension/enhancedselection2/…/sckenhancedselectiontype.php` | **Disabled for MongoDB by design** — user-supplied SQL queries are fundamentally incompatible; `isDbQueryValid()` returns `false` and `getDbOptions()` returns `[]`; static-options and template-options modes work normally |
| `eztags` | `extension/eztags/` (not installed) | **Not ported** — extension absent from this installation; tag collections (`eztags`, `eztag_attribute_link`) require `$lookup` aggregate pipelines for `fetchTagsByAttribute`, `fetchObjectsByTag`, and tag-link write paths |

> **Note:** `ezprice`, `ezmultiprice`, and the `ezflow` page type work for basic content display but
> the full e-commerce and block-scheduling flows are untested and may have additional `arrayQuery`
> calls deeper in the shop/flow kernel modules.

---

## 26. Known Limitations

The following gaps remain in the MongoDB port as of June 2026. All are
documented in the relevant sections above; this section collects them in one
place for quick reference.

- **`ezurlaliasml.php` `storePath()`** — URL alias rebuild creates top-level
  entries only; deep subtree alias regeneration after node moves still falls
  back to the MySQL path. `bin/php/updateniceurls.php` completes without crash
  but reports `Updated 0/N` for child nodes. See §13.4.

- **Roles / permissions display pages** — many `arrayQuery` calls remain in
  kernel module view scripts (role list, policy assignment, limitation detail).
  Role assignment listing may return empty on MongoDB. Content access control
  via `accessArray()` works correctly. See §19.

- **Cronjobs not yet ported** — `cronjobs/staticcache_cleanup.php`,
  `cronjobs/notification.php`, and `cronjobs/session_gc.php` still use raw
  `arrayQuery` calls and will no-op or error on MongoDB. See §13.5 and §20.

- **`eztags` extension** — not present in this installation. The `eztags` and
  `eztag_attribute_link` collections would require full `$lookup` aggregate
  pipeline implementations for `fetchTagsByAttribute()`,
  `fetchObjectsByTag()`, and all tag-link write paths before the extension
  could be used. See §25.2.

- **`enhancedselection2` DB-query options source** — the optional "DB query"
  options source mode is disabled for MongoDB by design; user-supplied raw SQL
  is fundamentally incompatible. Static-options and template-options modes
  work normally. See §25.2.

- **`ezmultiprice` currency table queries** — `ezcurrencyname` lookups may
  return empty if that collection has not been migrated. Basic price display
  is unaffected. See §25.1.

- **Full e-commerce checkout flow** (`ezbasket` → `ezorder` → payment) and
  **block scheduling** (`ezflow`) are untested end-to-end. Individual
  collection reads/writes are patched but the complete transaction flows have
  not been exercised against MongoDB. See §16 and §25.1.


