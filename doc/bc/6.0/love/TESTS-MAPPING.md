# LoveStack's tests in Exponential's suite

Besides testing each feature, Exponential compared LoveStack's own `tests/` tree with its own.
Mugo Web changed test files in 18 commits since the common ancestor (`3429aa91bb`, 9 March 2016)
up to `e61e9fdf0c` (23 February 2026), 173 files in all; 152 of them changed only their file mode.
Every test case among the rest that covers behaviour Exponential has is now in Exponential's
PHPUnit suite, rebuilt for PHPUnit as Exponential runs it, with thanks to Mugo Web. Each of these
test files names the LoveStack commit it comes from.

Paths are relative to `tests/tests/` in Exponential and to `tests/tests/` in LoveStack. Tests
marked *live* need an installation and roll back what they write; the others need no database.

| LoveStack commit | LoveStack test | In Exponential | Status |
|---|---|---|---|
| [`f2284944b2`](https://github.com/mugoweb/ezpublish-legacy/commit/f2284944b27f0ba38e2f41876804ae9400fd25ce) | `lib/ezutils/ezuri_test.php`: `testBuildFullUrl`, `testBuildRelativeUrl` | `lib/ezutils/eZURIBuildTest.php` (L25): both cases with the same inputs and results, plus host mode, escaping and parity with `ezurl` | covered |
| [`95d86f5990`](https://github.com/mugoweb/ezpublish-legacy/commit/95d86f5990ba83be39e4871bd88c2ecf120e8099) | `kernel/classes/eznamepatternresolver_regression.php` | `kernel/classes/eZNamePatternResolverLimitTest.php`: the EZP-21410 length limit cases (ASCII, white space, comma and dot, UTF-8 with and without the ideographic comma) through `eZNamePatternResolver::resolve()` and the constructor | brought over; Exponential keeps the constructor rather than LoveStack's `instance()` (L18) |
| [`e280ff5d64`](https://github.com/mugoweb/ezpublish-legacy/commit/e280ff5d64288f1c441ff83d472d20b38bffa1f0) | `kernel/classes/ezcontentobjecttreenode_test2.php`: `testSortingByTrashed` | `kernel/classes/eZContentObjectTreeNodeSortingTest.php`: ascending (LoveStack's case), descending, short form, combined keys | brought over |
| [`60a40cdc05`](https://github.com/mugoweb/ezpublish-legacy/commit/60a40cdc053107bfdb08b343f80cb4659501b56e) | `kernel/datatypes/ezuser/ezusertype_regression.php`: `testUpdatePasswordUpdatesSerializedData` | `kernel/datatypes/ezuser/eZUserTypePasswordDraftLiveTest.php` (live): the stored hash is the new password's, the attribute's data holds the new hash, an id without an account is refused | brought over |
| [`30c1260b84`](https://github.com/mugoweb/ezpublish-legacy/commit/30c1260b840d747740b9cd93bc76a8ed1db5a7ae), [`09e49ce1ff`](https://github.com/mugoweb/ezpublish-legacy/commit/09e49ce1ffb27835b73d13dd0f7cafb6452c56d1) | `kernel/private/rest/request_parser_data/*`: recorded REST requests | `kernel/private/rest/ezpRestHttpRequestParserRecordedRequestsTest.php`: every recorded request against its expected result, the Accept order by quality | brought over (the data was already in Exponential) |
| [`d894b9e885`](https://github.com/mugoweb/ezpublish-legacy/commit/d894b9e8852467f9cd7ee01113378b535beba23b) | `kernel/common/ezautolinkoperator_test.php`: http, https, mail, ftp | `kernel/common/eZAutoLinkOperatorLinksTest.php`: the four cases with LoveStack's results, shortened text, an unlisted scheme | brought over (the fix itself had reached Exponential through upstream) |
| [`5286068d5b`](https://github.com/mugoweb/ezpublish-legacy/commit/5286068d5b6620f8aa3572db2063a9b5da02af65) | `ezsimplifiedxmlinputparser_regression.php`: `testParseAttributeWithAmpersand` | `kernel/classes/xmltext/eZSimplifiedXMLInputParserTest.php`: a bare `&` in a link address (`<link>` and `<a>`) | brought over |
| [`7517c0e925`](https://github.com/mugoweb/ezpublish-legacy/commit/7517c0e925178e5f932ac653439a39203ec03cb0) | `lib/ezi18n/ezchartransform_test.php`: trailing characters of URL aliases | `lib/ezi18n/eZCharTransformTrailingCleanupTest.php` (L33): LoveStack's five inputs, with the option off and on | covered |
| [`55029bd473`](https://github.com/mugoweb/ezpublish-legacy/commit/55029bd473e47e73b86f748f0d2d264a5e14843a), [`4a27ffecb4`](https://github.com/mugoweb/ezpublish-legacy/commit/4a27ffecb4a5fb8f7b17094c0253fe28a89832fa) | `kernel/classes/ezsitedata_test.php`: `testCreate` | `kernel/classes/eZSiteDataCreateTest.php` (`create()` without a database), `kernel/classes/eZSiteDataCreateNewLiveTest.php` (live: `createNew()` stores and replaces by name) | brought over |
| `cb8a3a1c58`, `30c1260b84`, `54c9fd9e3f`, `dfa6f4c9ac`, `09e49ce1ff` (part) | workflow event tests, the database test suite setup, skips of retired test servers, PHP 5.5 export normalisation | the same changes are already in Exponential's files | already in Exponential |
| `0a52081473` | siteaccess tests renamed from `plain`/`admin` to `test` | — | not taken: follows the removal of the example siteaccesses, which Exponential does not do |
| `db5e171aef` | a debug label in the approve workflow test | — | not taken: debug label wording |
| `eaf72c6ef3` | `tests/runtests.php` loads `vendor/autoload.php` | — | not needed: Exponential's tests load it through `tests/bootstrap.php` |
| `9f5b99fd2a` | 152 files: the executable flag removed | — | file modes only, not a test case |

## Tests added for the features

Besides the per-feature tests listed in [TESTING.md](TESTING.md), these tests were added while
checking the features as a whole:

- `kernel/classes/expShippedFeatureDefaultsTest.php`: the defaults an upgraded site keeps;
- `kernel/content/eZAdminFullViewCopyButtonTest.php` (L15),
  `kernel/content/eZEditLocationsVisibilityTest.php` (L20),
  `kernel/datatypes/eZSelectionMatrixViewTemplateTest.php` (L21): the templates;
- `extension/ezjscore/ezjscServerFunctionsAjaxUploaderImageTest.php` (L05) and
  `ezjscServerFunctionsAjaxUploaderAltTextTest.php` (L16): the AJAX uploader;
- `kernel/content/eZLoginTemplatesRegisterLinkTest.php` (L02), `extension/ezoe/expOECustomTagAssetsTest.php`
  (L22, L23), `kernel/classes/packages/eZStylePackageCreatorTest.php` (L06),
  `kernel/classes/eZContentCacheEventSendersTest.php` (L36);
- `kernel/common/eZTemplateRawURLEncodeOperatorTest.php` (L27), which found that the URL encoding
  operators handed null to PHP for an unset value on PHP 8.1 and later; fixed.

Not given a PHPUnit test on purpose: the Symfony session handler's `destroy()` (it returns early
on the command line; its behaviour is covered by the logout test) and L24 (a JavaScript dialog,
checked in the browser).
