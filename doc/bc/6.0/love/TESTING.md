# Testing

Every LoveStack feature in Exponential is covered by PHPUnit tests in `tests/tests/`, written
with the feature. This page lists them per feature and says how to run them.

## How to run them

Each test file runs on its own:

```sh
php vendor/bin/phpunit tests/tests/lib/ezutils/eZURIBuildTest.php
```

Most of them need no database. The few that do are marked *live* below: they belong to the PHPUnit
group `database`, run on the installation the tests run on inside a transaction that is rolled
back, and are skipped when there is no installation:

```sh
php vendor/bin/phpunit --group database tests/tests/kernel/content/eZAuditLocationTest.php
```

To run only the tests of the files a change touched (instead of the whole suite), see
[run only the tests of a change](../../../features/6.0/quality-checks.md#run-only-the-tests-of-a-change):
`tests/test-map.json` maps source files, templates and scripts to the tests that cover them.

One test checks every default an upgraded site keeps, so a later change cannot switch a feature
on by accident: `tests/tests/kernel/classes/expShippedFeatureDefaultsTest.php` (L01, L02, L05,
L07, L08, L10, L13, L14, L18, L19, L22, L23, L27, L29, L30, L32, L33).

## Per feature

Paths are relative to `tests/tests/`.

### Security and sessions

| ID | Test | What it checks |
|---|---|---|
| L01 | `extension/ezjscore/ezjscServerRouterPolicyTest.php` | `ezjscnode` refused without *ezjscore / call* when `RequirePolicy=enabled`, allowed with it, unchanged when `disabled` or not set; 403 only when every call is refused for that reason |
| L02 | `kernel/classes/expPolicyOmitListTest.php`, `kernel/content/eZLoginTemplatesRegisterLinkTest.php` | `PolicyOmitListExclude[]` takes an entry out after every file is merged; every login template asks `can_open` before it shows the register link |
| L03 | `kernel/classes/eZUserLogoutTest.php` | after logout the session and its id are gone, through every handler, and the cookie is expired with the same path, domain, `Secure`, `HttpOnly`, `SameSite`; a session left running is not written back |
| L04 | `kernel/classes/expUserLoginIntegrityTest.php` | duplicates found (including case), the index refused while there are duplicates and added after |
| L05 | `kernel/classes/expImageUploadValidatorTest.php`, `extension/ezjscore/ezjscServerFunctionsAjaxUploaderImageTest.php` | JPEG, PNG, GIF, WebP, AVIF accepted; a PHP file named `.jpg` refused; SVG refused unless listed; the AJAX uploader refuses before it stores |
| L06 | `kernel/classes/packages/eZStylePackageCreatorTest.php` | package thumbnails and style images checked by content |
| SameSite | `lib/ezsession/eZSessionSameSiteAliasTest.php` | `SameSite` read when `CookieSameSite` is unset; `CookieSameSite` wins |

### Diagnostics and logging

| ID | Test | What it checks |
|---|---|---|
| L07 | `lib/ezutils/eZDebugDeprecatedTest.php` | a deprecation goes to `deprecated.log`, is counted, is not kept in memory |
| L08 | `lib/ezfile/eZLogMaxSizeTest.php` | rotation at the INI size; the constant and `setMaxLogSize()` still win |
| L09 | `lib/eztemplate/eZTemplateOperatorDefaultWarningTest.php` | the warning names operator and parameter, once |
| L10 | `kernel/common/eZTemplatesStatisticsReporterEditUrlTest.php` | the edit link follows `UsedTemplateEditUrl`; empty gives the usual link |
| L11 | `lib/ezdb/eZDBExceptionCodeTest.php` | `getCode()` is the driver's error number |
| L12 | `kernel/classes/eZContentCacheManagerThresholdWarningTest.php` | the warning text with node count and threshold |

### Content, datatypes and the administration

| ID | Test | What it checks |
|---|---|---|
| L13 | `kernel/content/eZAuditLocationTest.php` (live) | adding, removing and changing the main location each write their event with the object and node ids |
| L14 | `kernel/content/eZContentFunctionCollectionSiteDataTest.php`, `kernel/classes/eZSiteDataCreateTest.php`, `kernel/classes/eZSiteDataCreateNewLiveTest.php` (live) | listed names readable, others not; `create()` and `createNew()` |
| L15 | `kernel/content/eZAdminFullViewCopyButtonTest.php` | the Copy button between Move and Remove, enabled only with create access at the parent |
| L16 | `kernel/classes/datatypes/eZImageTypeAltTextTest.php`, `extension/ezjscore/ezjscServerFunctionsAjaxUploaderAltTextTest.php` | required alt text refused when empty, accepted when given; off by default; the AJAX uploader's field |
| L17 | `kernel/classes/datatypes/eZStringTypeTrimTest.php` | the stored value trimmed only with the option |
| L18 | `kernel/classes/eZNamePatternResolverClassTest.php`, `kernel/classes/eZNamePatternResolverLimitTest.php` | a listed class is used; an unlisted one ignored and logged; normal patterns and the length limit unchanged |
| L19 | `kernel/content/eZContentFunctionCollectionClassFilterTypeTest.php` | empty setting: today's results; `include`: filtered |
| L20 | `kernel/content/eZEditLocationsVisibilityTest.php` | an existing location starts at "Unchanged" |
| L21 | `kernel/datatypes/eZSelectionMatrixViewTemplateTest.php` | no stray `<br />` |

### Rich text editor

| ID | Test | What it checks |
|---|---|---|
| L22 | `extension/ezoe/expOECustomTagListTest.php` | hidden tags not offered, still rendered and editable where they exist |
| L23 | `extension/ezoe/expOECustomTagAssetsTest.php` | the stylesheet for the editor and its dialogs, valid plugin scripts, the styled classes, the example image in the dialog |
| L24 | — | a JavaScript dialog, checked in the browser on TinyMCE 3 and Tiptap |

### Developer APIs and settings

| ID | Test | What it checks |
|---|---|---|
| L25 | `lib/ezutils/eZURIBuildTest.php` | relative and full URLs, siteaccess in URL and host modes, the same result as `ezurl` |
| L26 | `lib/ezutils/eZMailTransportSendTest.php` | the given transport is used for one message only |
| L27 | `kernel/common/eZTemplateRawURLEncodeOperatorTest.php` | `rawurlencode` / `rawurldecode`, and an unset value with and without compilation |
| L28 | `lib/ezutils/eZINIIncludeTest.php` | relative glob, nesting, loops, a missing file, an override that includes, the INI cache recording the included files and seeing their changes |
| L29 | `kernel/error/ErrorHeaderListTest.php` | headers from `HeaderList[]`, invalid ones left out, sent again from the view cache |
| L30 | `extension/ezjscore/ezjscServerFunctionsJsSearchSortByTest.php` | the search order from `SortBy[]` |
| L31 | `kernel/common/eZTemplateDesignResourceSetKeyValueTest.php` | `setKeyValue()` and the `layout` key |

### URLs and search

| ID | Test | What it checks |
|---|---|---|
| L32 | `lib/ezutils/eZSysRequestURIDecodeOnceTest.php` | off: today's decoding; on: `%2B` stays `+` |
| L33 | `lib/ezi18n/eZCharTransformTrailingCleanupTest.php` | off: aliases unchanged; on: LoveStack's cases |
| L34 | `kernel/search/eZSearchEngineIntegerClampTest.php` | values beyond the column range indexed without an error, per driver |
| L35 | `kernel/datatypes/eZObjectRelationMetaDataTest.php` | related objects' relation fields left out, a cycle of relations included |
| L36 | `kernel/classes/runnable/HideCronjobSearchIndexTest.php`, `kernel/classes/eZContentCacheEventSendersTest.php` | the hide cronjob updates the index; every sender of `content/cache` passes object ids |

### PHP 8 review, migration and LoveStack's own tests

- PHP 8 review: one regression test per site, listed in [PHP8-REVIEW.md](PHP8-REVIEW.md).
- Migration: `kernel/classes/expLoveStackCheckTest.php` and `kernel/classes/expLoveStackMigrateTest.php`
  on LoveStack-shaped and Exponential-shaped fixture databases; nothing is written by `check`, and
  `migrate` changes only its two rows and is idempotent.
- LoveStack's own test cases for behaviour Exponential has: [TESTS-MAPPING.md](TESTS-MAPPING.md).

The URL options L32 and L33 were also compared over the URL aliases of a whole site with the
options off and on: switching L32 on changed none of the request paths, and switching L33 on changed only
the IRI aliases that contain a double quote.
