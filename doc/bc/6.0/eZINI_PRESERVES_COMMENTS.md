# eZINI keeps comments when it saves

Read this page if you edit INI files by hand and also let the admin or a script change them (activating an
extension, toolbar or menu settings, `exp:ini`). Before, every save through `eZINI` rewrote the whole file and
dropped your comments and layout. Now a save changes only the lines it has to and keeps everything else. The API
did not change, and there is nothing to migrate.

## In short

| | |
|---|---|
| What changed | Direct-access saves in `eZINI` patch the file in place (round trip) instead of rewriting it. The old full rewrite stays as fallback. |
| Who is affected | Everyone whose override files (for example `settings/override/site.ini.append.php`) carry comments. Positive change only. |
| How to check | Add a comment to an override, activate an extension in Setup > Extensions, and look at the file: the comment is still there. |
| How to fix | Nothing to fix. No settings or schema migration. |

## What changed

Before, `eZINI` lost comments and formatting on save because:

- the parser ignored full-line comments (`# ...`);
- the parser stripped inline comment tails (`## ...`);
- the writer always serialised the file from `BlockValues`.

In real admin flows such as activating an extension, `settings/override/site.ini.append.php` lost much of its
layout and comments.

Now `lib/ezutils/classes/ezini.php` saves by round trip:

- it keeps the original file lines and the line numbers of sections and settings while parsing;
- on save it patches only the keys and sections that changed;
- untouched lines, comments and spacing stay as they were.

## When the round trip is used

All three must hold:

1. direct-access mode is used;
2. the source lines of the target file can be found;
3. patching is safe for the current operation.

Then a save:

- keeps existing comments and blank lines;
- updates changed values in place;
- appends new keys in the correct section;
- removes deleted keys and sections on a full save (`$onlyModified = false`).

If any condition fails, the old serialiser writes the file as before.

## Fixes found in real use

Two more bugs were fixed after testing on real files:

1. **Instances loaded from the INI cache.** When values came from the INI cache, the parse-time snapshot could be
   missing. A save now loads the source from disk first.
2. **Sections and line replacement.** Sections that hold only comments, or nothing, are tracked during parsing and
   kept. Replacing a variable removes exactly its indexed lines, not a run of neighbouring lines, so unrelated lines
   are no longer lost.

## Compatibility

- Scope: direct-access saves.
- API: unchanged; constructor, signatures and callers stay the same.
- Output: the same values; comments and layout are now kept.

## How to check

1. On a staging copy, add a comment line to `settings/override/site.ini.append.php`.
2. Activate or deactivate an extension in Setup > Extensions (or change a value with `exp:ini`).
3. Open the file again. Your comment is still there, and only the changed value moved.

Check the INI admin flows you use in the same way (extensions, toolbar and menu settings).

## Tests

The tests are in `tests/tests/lib/ezutils/eZINITest.php`. `tests/tests/lib/ezutils/ezini_test.php` is a
compatibility alias (`ezini_test extends eZINITest`) for runners that look for that file name.

Round-trip regression tests:

1. `testSavePreservesCommentsInDirectAccessMode`
2. `testSaveAppendsNewSettingWithoutDroppingComments`
3. `testSaveRemovesDeletedSettingAndKeepsSectionComments`
4. `testSavePreservesCommentsWhenLoadedFromCache`
5. `testSaveRetainsRealSiteIniStructureWhenUpdatingExtensions`
6. `testSaveRetainsCurrentSiteIniAppendOutsideExtensionSettings`

They clean up their temporary fixture files.

Run them:

```bash
php -l lib/ezutils/classes/ezini.php
./vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/tests/lib/ezutils/ezini_test.php
```

Results recorded when the change was made:

| Run | Result |
|---|---|
| `php -l` on both files | no syntax errors |
| `--filter 'testSave(RetainsCurrentSiteIniAppendOutsideExtensionSettings\|RetainsRealSiteIniStructureWhenUpdatingExtensions\|PreservesCommentsWhenLoadedFromCache)$'` | OK (3 tests, 17 assertions) |
| `--filter 'testSave(PreservesCommentsInDirectAccessMode\|AppendsNewSettingWithoutDroppingComments\|RemovesDeletedSettingAndKeepsSectionComments\|PreservesCommentsWhenLoadedFromCache\|RetainsRealSiteIniStructureWhenUpdatingExtensions)$'` | OK (5 tests, 29 assertions) |
| the whole file | OK (11 tests, 50 assertions) |

## Rollout

1. Deploy the code.
2. Run the whole eZINI test file.
3. On staging, check the admin write flows for INI files with comments (extensions, toolbar and menu settings).

## Related pages

- [Change settings from the command line: exp:ini](../../features/6.0/exp-ini-command.md)
- [Changing settings from the command line: `exp:ini`](console-exp-ini.md)
- [June 2026, second half (16 to 30 June)](../../history/2026/2026-06b.md)
