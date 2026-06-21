# eZINI Preserves Comments on Save

## TL;DR

`eZINI` now uses a round-trip save path for direct-access INI writes to preserve existing comments and structure instead of rewriting the whole file from normalized arrays.

Legacy full rewrite is still available as fallback when round-trip cannot be applied safely.

## Problem

Historically, `eZINI` parse/save behavior dropped comments and formatting because:

- parser ignored full-line comments (`# ...`)
- parser stripped inline comment tails (`## ...`)
- writer always serialized from `BlockValues`

In real admin flows (for example extension activation), this caused `settings/override/site.ini.append.php` to lose much of its original layout and comments.

## What Was Implemented

Round-trip support was added in [lib/ezutils/classes/ezini.php](lib/ezutils/classes/ezini.php):

- capture original file lines and parse line indexes for sections/settings
- patch only the targeted keys/sections during save
- preserve untouched lines, comments, and spacing

## Follow-up Fixes After Real-World Validation

Two additional bugs were fixed:

1. Cache-loaded direct-access instances:
- if values were restored from INI cache, parse-time snapshot data could be missing
- save now lazily loads source from disk before patching

2. Section and line-replacement correctness:
- comment-only / empty sections are now tracked during parse and preserved
- variable replacement now removes exact indexed lines (not contiguous spans), preventing unrelated line loss

## Behavior

Round-trip save is attempted when:

1. direct-access mode is used
2. source lines can be resolved for the target file
3. patching is safe for current operation

When active, save can:

- retain existing comments and blank lines
- update changed values in place
- append new keys in the correct section
- remove deleted keys/sections on full save (`$onlyModified = false`)

Fallback behavior:

- if round-trip prerequisites are not met, legacy serializer is used

## Scope and BC

- Scope: direct-access saves
- API compatibility: unchanged (constructor/signatures/callers do not change)
- BC: backward compatible at API level, improved output preservation behavior

## Tests

Test file: [tests/tests/lib/ezutils/ezini_test.php](tests/tests/lib/ezutils/ezini_test.php)

Added/expanded round-trip regressions:

1. `testSavePreservesCommentsInDirectAccessMode`
2. `testSaveAppendsNewSettingWithoutDroppingComments`
3. `testSaveRemovesDeletedSettingAndKeepsSectionComments`
4. `testSavePreservesCommentsWhenLoadedFromCache`
5. `testSaveRetainsRealSiteIniStructureWhenUpdatingExtensions`
6. `testSaveRetainsCurrentSiteIniAppendOutsideExtensionSettings`

Also included:

- temporary fixture cleanup helper
- compatibility alias class (`ezini_test extends eZINITest`) for filename-based runners

## Commands and Current Results

Syntax checks:

```bash
php -l lib/ezutils/classes/ezini.php
php -l tests/tests/lib/ezutils/ezini_test.php
```

Result:

- no syntax errors

Focused regression subset:

```bash
./vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/tests/lib/ezutils/ezini_test.php --filter 'testSave(RetainsCurrentSiteIniAppendOutsideExtensionSettings|RetainsRealSiteIniStructureWhenUpdatingExtensions|PreservesCommentsWhenLoadedFromCache)$'
```

Result:

- OK (3 tests, 17 assertions)

Expanded round-trip subset:

```bash
./vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/tests/lib/ezutils/ezini_test.php --filter 'testSave(PreservesCommentsInDirectAccessMode|AppendsNewSettingWithoutDroppingComments|RemovesDeletedSettingAndKeepsSectionComments|PreservesCommentsWhenLoadedFromCache|RetainsRealSiteIniStructureWhenUpdatingExtensions)$'
```

Result:

- OK (5 tests, 29 assertions)

Full eZINI test file:

```bash
./vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/tests/lib/ezutils/ezini_test.php
```

Result:

- OK (11 tests, 50 assertions)

## Rollout Notes

No config/schema migration is required.

Recommended rollout:

1. Deploy code update.
2. Run the full `ezini_test.php` file.
3. Verify admin write flows in staging for INI files with comments (extensions, toolbar/menu settings, etc.).
