# New API Features — Exponential v6.0.13

**Branch:** `exp_cjw_network_cms_improvements_and_features_2026_02`  
**Port date:** 2026-02-28  
**Base tag:** v6.0.13 (`6d2aac449f`)  
**Source patches:** cjw-network ezpublish7x repository, commits after `c079b52d59` (Feb 1 2025)  
**PHP target:** 8.5.3  
**Test suite:** `tests/tests/kernel/classes/exp_cms_features_2026_02_test.php` — 25 tests, all pass

---

## Summary

This document describes the public API changes introduced by the backport of the
cjw-network patch set into the Exponential CMS codebase. All patch identifiers have
been renamed from the upstream `JAC_PATCH` / `cjw_network` namespace to
the Exponential `exp_feature` / `exp` / `exp_ece` namespace. German comments in the
ported code have been translated to English.

---

## Porting Methodology

1. A full git log of the source repository was studied: 74 commits from the base
   commit `c079b52d59` to HEAD on the `cjw_patches` branch.
2. All non-vendor, non-documentation changed files were extracted (42 files total).
3. An automated diff+apply script compared each file against the exponential target
   and applied clean hunks. Conflicts were logged and resolved manually.
4. String replacements were applied across all affected files:
   - `cjw_network` → `exp`
   - `cjw` → `exp`
   - `JAC_PATCH` → `exp_feature`
   - `JAC_PATCH_G_NNN_EZ_YYYY.MM` → `exp_feature_gNNN_ezYYYY.MM`
   - `JAC_SECURITY_PATCH_S_N_EZ_YYYY.MM` → `exp_feature_security_sN_ezYYYY.MM`
   - `JAC_BACKPORT_PATCH_B_N_EZ_YYYY.MM` → `exp_feature_backport_bN_ezYYYY.MM`
5. PHP lint was run on all 42 modified files — all pass with PHP 8.5.3.
6. Unit tests were written for every verifiable API surface and all 25 pass.

See also:

- `doc/bc/6.0/patches-global-exp-network-original.txt` — full patch identifier list

---

## New Constants

| Constant | File | Purpose |
|---|---|---|
| `EXP_USE_EXTRA_FOLDER_VAR_CACHE` | global scope | When `true`, `eZSys::cacheDirectory()` uses `var_cache/` instead of `var/` prefix |
| `EXP_USE_EXTRA_FOLDER_VAR_LOG` | global scope | When `true`, `eZDebug::setLogFiles()` uses `var_log/` instead of `var/` prefix |
| `EXP_ENABLE_ASSIGNED_NODES_CACHE` | global scope | When `true`, enables request-level cache for `eZContentObject::assignedNodes()` |
| `EXP_ASSIGNED_NODES_CACHE_IGNORE` | `$GLOBALS` | When set, bypass the assigned-nodes cache for the current request |
| `EXP_SITE_STRUCTURE_EZ_INI_OVERRIDE_DIR_LIST` | `eZExtension` | Comma-separated list of per-siteaccess INI override directories to load at boot |

---

## New INI Settings

### `site.ini` — `[FileSettings]`

| Key | Default | Purpose |
|---|---|---|
| `UseGlobalLogDir` | `enabled` | Set to `disabled` to route log files to the per-siteaccess `VarDir/LogDir/` path instead of the global `var/log/` |

### `site.ini` — `[DatabaseSettings]`

| Key | Default | Purpose |
|---|---|---|
| `DatabasePrefix` | `` (empty) | Prefix added to the configured database name before connecting |
| `DatabasePostfix` | `` (empty) | Postfix appended to the configured database name before connecting |

---

## Modified Public APIs

### `eZDebug` (`lib/ezutils/classes/ezdebug.php`)

#### New method: `setLogFiles( string $logDir = 'var/log/' ): void`

Sets the log file path for all five severity levels (`LEVEL_NOTICE`, `LEVEL_WARNING`,
`LEVEL_ERROR`, `LEVEL_DEBUG`, `LEVEL_STRICT`) to the specified directory.

When the constant `EXP_USE_EXTRA_FOLDER_VAR_LOG` is defined and `true`, the path
prefix `var/` is automatically replaced with `var_log/` to route logs into a
dedicated filesystem volume.

```php
// Use the global log directory (default behaviour)
eZDebug::instance()->setLogFiles();

// Route logs for the current siteaccess into var/mysite/log/
eZDebug::instance()->setLogFiles( 'var/mysite/log/' );
```

Patch: `###exp_feature_g03_ez2014.11###` / `###exp_feature_g44_ez2014.11###`

---

#### New static method: `updateLogFileDirForCurrentSiteaccess(): void`

Reads `FileSettings.UseGlobalLogDir` from `site.ini`. If the value is `"disabled"`, 
it constructs the per-siteaccess log directory from `VarDir` + `LogDir` and calls 
`setLogFiles()` accordingly. Otherwise calls `setLogFiles()` with no arguments 
(global default).

Called automatically from `eZDebug::updateSettings()` after the INI system is ready.

```php
// Called internally — no external call needed in normal usage
eZDebug::updateLogFileDirForCurrentSiteaccess();
```

Patch: `###exp_feature_g03_ez2014.11###`

---

### `eZSys` (`lib/ezutils/classes/ezsys.php`)

#### `cacheDirectory()` — separate cache volume support

When `EXP_USE_EXTRA_FOLDER_VAR_CACHE` is `true`, the resolved cache directory path
replaces the `var/` prefix with `var_cache/`, allowing the cache to reside on a
dedicated volume or partition.

```php
define( 'EXP_USE_EXTRA_FOLDER_VAR_CACHE', true );
// eZSys::cacheDirectory() will now return e.g. 'var_cache/mysite/cache/'
```

Patch: `###exp_feature_g44_ez2014.11###`

---

#### `getValidwwwDir()` — nginx bare-path `PHP_SELF` fix

The `getValidwwwDir()` method now accepts `$_SERVER['PHP_SELF']` values without a
leading slash (as returned by some nginx configurations). Previously only `/index.php`
was recognised; now bare `index.php` also matches.

No API change — this is a transparent bug fix for nginx deployments.

Patch: `###exp_feature_g52_ez2014.11###`

---

### `eZContentObject` (`kernel/classes/ezcontentobject.php`)

#### `checkAccess( string $functionName, ..., mixed $userID = false ): int` — optional userID

The `$userID` parameter has been added as an optional last argument to `checkAccess()`.
When `false` (default), the current logged-in user is resolved via `eZUser::currentUser()`.
When an explicit integer user ID is provided, `eZUser::fetch( $userID )` is used instead,
allowing permission checks to be performed on behalf of any user.

```php
// Check if user 42 can read this object
$canRead = $object->checkAccess( 'read', false, false, false, 42 );
```

Patch: `###exp_feature_g1003_ez2014.11###`

---

#### `assignedNodes( bool $asObject = true ): array` — request-level cache

When `EXP_ENABLE_ASSIGNED_NODES_CACHE` is `true`, the list of assigned tree nodes is
cached in `$GLOBALS` for the duration of the request. Subsequent calls with the same
`$asObject` value return the cached value without a database query.

To bypass the cache for a specific call (e.g. after a structural change), set the
global flag before calling:

```php
$GLOBALS['EXP_ASSIGNED_NODES_CACHE_IGNORE'] = true;
$nodes = $object->assignedNodes();
unset( $GLOBALS['EXP_ASSIGNED_NODES_CACHE_IGNORE'] );
```

Patch: `###exp_feature_g1004_ez2014.11###`

---

#### `copyVersion()` — `op_code` guard for rejected articles

When copying a content version's node assignments, assignments that have `remote_id == 0`
**and** whose `op_code` is `OP_CODE_CREATE` are no longer skipped. This fixed a bug
where articles rejected after initial publication (which receive `OP_CODE_CREATE`
marked cloned assignments) had their location assignments dropped on copy.

Assignments with `remote_id == 0` that are **not** `OP_CODE_CREATE` are still skipped
as before to avoid duplicating stale records.

No API change — transparent bug fix.

Patch: `###exp_feature_g1005_ez2014.11###`

---

#### `checkAccess( 'can_edit' )` — unpublished content check relaxed

The condition that prevented editing objects with `current_version == 1` and
`status != STATUS_PUBLISHED` has been relaxed. The `current_version == 1` constraint
was removed; now only `status` is evaluated. This allows editors to open and modify
objects that were created but never published (e.g. drafts, archived items stuck at
version 1).

No API change — transparent behaviour change.

Patch: `###exp_feature_g1014_ez2014.11###`

---

#### `checkAccess( 'edit' )` — draft parent node subtree lookup

When checking `edit` access under a subtree policy, the method now resolves the parent
node via the version's `tempMainNode()` when no assigned nodes exist yet (e.g. for new
draft objects). This prevents false "access denied" responses while a draft is being
created under a location the editor has permission to write to.

No API change — transparent bug fix.

Patch: `###exp_feature_g1016_ez2014.11###`

---

#### `purge()` — notification event cleanup

When `purge()` permanently deletes a content object version, it now also removes
orphaned rows from the following notification tables:

- `eznotificationevent`
- `eznotificationcollection`
- `eznotificationcollection_item`

This prevents notification queue bloat in long-running installations where many
versions are purged over time.

No API change — transparent cleanup behaviour appended to existing `purge()` logic.

Patch: `###exp_feature_g1018_ez2014.11###`

---

### `eZContentObjectTreeNode` (`kernel/classes/ezcontentobjecttreenode.php`)

#### `checkAccess()` — optional `$userID` parameter

Mirrors the change made to `eZContentObject::checkAccess()`. An optional `$userID`
argument is accepted; when `false`, the current user is used.

Patch: `###exp_feature_g1003_ez2014.11###`

---

#### SQL optimisation in subtree queries

The `G_1004` SQL optimisation reduces the number of JOIN columns fetched in assigned
nodes queries, improving performance on large content trees.

Patch: `###exp_feature_g1004_ez2014.11###`

---

### `eZSiteAccess` (`kernel/classes/ezsiteaccess.php`)

#### Host/URI matching — begins-with check

The host matching logic for `HostMatchMapList` now supports a begins-with comparison:
a configured host entry matches the actual request hostname if the actual hostname
**starts with** the configured value. This enables wildcard-like subdomain matching
without changing INI syntax.

#### Browser-language fallback — begins-with check

Browser `Accept-Language` header matching also uses begins-with comparison, so a
configured language code `de` correctly matches headers like `de-DE,de;q=0.9`.

Patch: `###exp_feature_g01_ez2014.11###`

---

### `eZExtension` (`lib/ezutils/classes/ezextension.php`)

#### Per-siteaccess INI override directory list

The constant `EXP_SITE_STRUCTURE_EZ_INI_OVERRIDE_DIR_LIST` can hold a comma-separated
list of INI override directories to inject at bootstrap time. This enables multi-site
installations to load per-site INI overrides from a centralised structure directory
rather than duplicating settings across extension `settings/override/` folders.

Patch: `###exp_feature_g04_ez2014.11###`

---

### `eZINI` (`lib/ezutils/classes/ezini.php`)

#### INI cache directory global (`eZINI_CONFIG_CACHE_DIR`)

The computed INI cache directory path is stored in `$GLOBALS['eZINI_CONFIG_CACHE_DIR']`
on first resolution and reused for all subsequent `eZINI` instantiations. This
eliminates repeated filesystem calls to resolve the cache path on sites with many
INI files loaded per request.

Patch: `###exp_feature_g48_ez2014.11###`

---

### `eZDB` (`lib/ezdb/classes/ezdb.php`)

#### `DatabasePrefix` / `DatabasePostfix` INI settings

Before connecting, `eZDB` reads `DatabasePrefix` and `DatabasePostfix` from
`site.ini [DatabaseSettings]` and applies them to the configured database name:

```ini
[DatabaseSettings]
DatabasePrefix=exp_
DatabasePostfix=_devel
```

This allows a single code base to connect to environment-specific databases
(`exp_mysite_devel`) from a shared `site.ini` by varying only the prefix/postfix,
rather than overriding the full database name per environment.

Patch: `###exp_feature_g09_ez2014.11###`

---

### `eZContentObject` / `eZContentObjectTreeNode` — SNAC role-matrix check (`exp_feature_g1002_ez2014.11`)

A new `SNAC_RoleMatrixCheck` case has been added to the `checkAccess()` permission switch in both
`eZContentObject` and `eZContentObjectTreeNode`. The case is guarded with `class_exists('ExpEceSnacRoleBitmask')`
so it is a no-op unless the `ExpEceSnacRoleBitmask` extension is loaded.

When the extension is active and the `SNAC_RoleMatrixCheck` limitation is enabled in a policy, the check
delegates to `ExpEceSnacRoleBitmask::checkAccessByObjectIdAndVersionAndUserId()` (in-memory path) and
`ExpEceSnacRoleBitmask::createPermissionCheckingSQL()` (SQL fetch path in eZContentObjectTreeNode).

Patch: `###exp_feature_g1002_ez2014.11###`

---

### Zeta Components — Archive and Mail (`exp_feature_g57_ez2014.11`, `exp_feature_g65_ez2014.11`)

Exponential's `composer.json` already required `zetacomponents/archive ~1.5.1` and
`zetacomponents/mail ~1.10.1`, both of which supersede the versions targeted by these upstream
patches (Archive PHP 5.6+ compat fix; Mail iconv/UTF-8 empty-subject fix for 1.8.4+).
`composer install` was run on this branch to produce a current `vendor/` directory with these
versions resolved.

Patch coverage: `###exp_feature_g57_ez2014.11###` / `###exp_feature_g65_ez2014.11###`

---

### `eZPersistentObject` — Oracle long-name resolution (`exp_feature_g58_ez2014.11`)

The Oracle DB short-name → long-name resolution patch in `kernel/classes/ezpersistentobject.php`
was already present in the shared upstream base commit (`c079b52d59`) for both the source
(ezpublish7x) and target (exponential) repositories. The files are byte-for-byte identical at
this location; no further porting was required.

Patch coverage: `###exp_feature_g58_ez2014.11###`

---

## Running the Tests

The test suite requires PHP 8.5+ with no extensions (`php -n`). No full eZ Publish
bootstrap is needed — stub classes cover all dependencies.

```bash
# With vendor/ installed (after composer install)
php vendor/bin/phpunit --no-coverage \
    --configuration tests/tests/kernel/classes/phpunit-exp-cms-features-in-6013.xml

# With the project's own test runner
php tests/bin/ezptestrunner.php --group=exp_cms_features_2026_02
```

---

## Backward Compatibility

All changes are backward compatible:

- New methods are additive and carry default parameters identical to the pre-patch behaviour.
- New INI keys have safe defaults that reproduce the previous behaviour when absent.
- New global constants default to `false`/disabled in the absence of explicit definition.
- Bug fixes alter no documented API contract — they correct silent incorrect behaviour only.

No database schema changes are required.

---

*Generated: 2026-02-28 — Exponential CMS v6.0.13 — branch exp_cjw_network_cms_improvements_and_features_2026_02*
