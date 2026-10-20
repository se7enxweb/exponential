# PHP 8 review of LoveStack's fixes

LoveStack carries about forty fixes for PHP 7.2 to 8.3, made by Mugo Web between 2017 and 2026.
Exponential, which runs on PHP 8.1 to 8.5, had fixed the same areas in its own way (for example
`#[AllowDynamicProperties]` on `eZPersistentObject` instead of declared properties, null-safe
string handling, `count()` guards). To be sure nothing was missed, each site a LoveStack fix
names was reproduced in Exponential as a PHPUnit test, on PHP 8.5 and 8.1, and the code was
changed only where the test failed first.

For **developers**; for everyone else the short version is: the fixes that were still needed are
in 6.0.15, on for every site, with nothing to do on an upgrade.

Test paths are relative to `tests/tests/`.

## Fixed in Exponential

| LoveStack commit | Site | Test | What changed |
|---|---|---|---|
| [`e828c54709`](https://github.com/mugoweb/ezpublish-legacy/commit/e828c54709a8ed4916f43042d6d1b01c1b8c89b8) | `get_class` / `is_class` template operators on a value that is not an object | `lib/eztemplate/eZTemplatePHP8RegressionTest.php` | without template compilation they now give `''` and `false`, as the compiled code did; before, a string, number or null ended the request with a TypeError |
| [`4065ed16b3`](https://github.com/mugoweb/ezpublish-legacy/commit/4065ed16b39e7d7c25725599b478f3c526af6e43) | `wash(email)` of null | `lib/eztemplate/eZTemplatePHP8RegressionTest.php` | no "Passing null" deprecation (other `wash` modes already cast) |
| [`4065ed16b3`](https://github.com/mugoweb/ezpublish-legacy/commit/4065ed16b39e7d7c25725599b478f3c526af6e43) | the classic HTML debug report with a null label or text | `lib/ezutils/eZDebugReportNullLabelTest.php` | both cast, as the debug bar already did |
| [`4065ed16b3`](https://github.com/mugoweb/ezpublish-legacy/commit/4065ed16b39e7d7c25725599b478f3c526af6e43) | the debug bar with a null or numeric message text (with xdebug loaded) | `lib/ezutils/expDebugBarReportMessageTextTest.php` | no deprecation from `strncmp()` |
| [`4065ed16b3`](https://github.com/mugoweb/ezpublish-legacy/commit/4065ed16b39e7d7c25725599b478f3c526af6e43) | `ezurl`, `ezroot`, `eZURI::transformURI()` of a number; `new eZURI( null )` | `lib/ezutils/eZURITransformURIEscapeNullTest.php` | no "Trying to access array offset" warning for a node id; `new eZURI( null )` (the command line before a request address is known) is an empty address; `eZURI::build()` unchanged |
| [`75cbb1daa2`](https://github.com/mugoweb/ezpublish-legacy/commit/75cbb1daa2804b129f1a3b395dd44d79ebcf667b) | `eZHTTPTool::createPostVarsFromImageButtons()` with `$_POST` null | `lib/ezutils/eZHTTPToolPostNullTest.php` | copes with it |
| [`1bd250d4c6`](https://github.com/mugoweb/ezpublish-legacy/commit/1bd250d4c6cdfeda4d99a44d56bc1cd801ebf935) | `update/common/scripts/5.4/cleanupfieldvaluerelations.php` with empty relation data | `kernel/classes/eZUpdateScriptCleanupFieldValueRelationsTest.php` | skips NULL or empty `data_text` instead of stopping with a ValueError |
| [`1bd250d4c6`](https://github.com/mugoweb/ezpublish-legacy/commit/1bd250d4c6cdfeda4d99a44d56bc1cd801ebf935) | `eZPostgreSQLDB::escapeString( null )` | `lib/ezdb/eZPostgreSQLDBEscapeStringNullTest.php` | gives `''`, as the MySQLi, SQLite3 and MongoDB drivers do |
| [`5a17c08bfc`](https://github.com/mugoweb/ezpublish-legacy/commit/5a17c08bfc761df3fc0d9ce6d117f3cbc9cfc129) | e-mail addresses with "+" in the shop's order lists | `kernel/classes/eZShopCustomerOrderViewEmailTest.php` | the order list, archive and dashboard links `rawurlencode` the address (standard, admin and admin4 designs), and the view no longer decodes it a third time; works with L32 off and on |

## Already handled in Exponential (kept as tests)

| LoveStack commit | Site | Test |
|---|---|---|
| `1bd250d4c6` | `navigator/google.tpl` string / int division | `lib/eztemplate/eZTemplatePHP8RegressionTest.php` |
| `1bd250d4c6` | `ezdatetime` fields posted as strings; `ezauthor` without an e-mail address | `kernel/datatypes/eZDatatypePHP8RegressionTest.php` |
| `1bd250d4c6` | `eZPersistentObject` escaping a value that is not a string (MySQLi, SQLite3, MongoDB) | — (null-safe `escapeString()`) |
| `b11e85ca5a`, `fd839afd28` | `ezmatrix` with `data_text` NULL or empty | `kernel/datatypes/eZDatatypePHP8RegressionTest.php` |
| `a5f16bd9f7` | `ezmatrix` validation of a value that is not an array | `kernel/datatypes/eZDatatypePHP8RegressionTest.php` |
| `a5f16bd9f7`, `4917e57c92`, `f627bcf278` | `"${}"` interpolation in the template loops | `lib/eztemplate/eZTemplatePHP8RegressionTest.php` (also a token scan of the template engine sources) |
| `be78a54e49` | `eZModuleOperationInfo` counting the memento list | — (`is_array()` guard) |
| `82a7684d6f` | `eZTemplateOptimizer` on a value that is not countable | `lib/eztemplate/eZTemplatePHP8RegressionTest.php` |
| `bd4fa8600d`, `be26d66906` | `eZTemplateNodeTool::isPHPVariableElement()` of null; the compiler's placement of a bool node | `lib/eztemplate/eZTemplatePHP8RegressionTest.php` |
| `bd4fa8600d`, `0513dbae32`, `cb8a3a1c58` | removed functions (`each()`, `create_function()`, `mysql_escape_string()`), the preg `/e` modifier | `kernel/classes/eZPHP8RemovedFunctionCallsTest.php` (a token scan of `bin/php`, `kernel`, `lib`, `cronjobs`, `update`) |
| `e8dc1dba4d` | `eZObjectForwarder` counting custom match conditions | — (null guard) |
| `cb8a3a1c58` | `ezsendmailtransport.php` counting the sender; the spell checker's raw input, `/e` modifier and constructors | `extension/ezoe/ezoeSpellcheckRpcRawInputTest.php` |
| `3b6a11496f`, `72009a7f46`, `f8fcc09a57`, `10e40c35dd`, `d7f1480c76`, `6bf0faa5f2` | dynamic properties (PHP 8.2) | `kernel/classes/eZDynamicPropertiesPHP82Test.php` (every property LoveStack declared, set on 30 classes, on PHP 8.1, 8.2 and 8.5) |

## Not taken, and why

| LoveStack commit | Item | Why |
|---|---|---|
| `1bd250d4c6` | accepting an author without an e-mail address | a change of behaviour, not a PHP 8 fix; Exponential keeps refusing it, as every earlier release did, for now |
| `1bd250d4c6` | extra constructor arguments in `ezworkflowtype` and `ezdatatype`, `ezexec.php` wording | not PHP 8 failures |
| `f627bcf278` | a later form of the template loop interpolation | Exponential's form is the one PHP reads as intended |
| `72009a7f46` | a class name change in the same commit | not needed in Exponential |
| `3fa3796297` | old-style constructors in the XML handlers | Exponential has `__construct()` only |
| `630ccdbada` | the `ezenum` datatype in `legacy_2018` | the extension is not brought over |
| `6c5b892829`, `e546bf30d4`, `18caa668a4`, `30c1260b84`, `eaf72c6ef3` | composer constraints, Travis | Exponential has its own `composer.json` (PHP 8.1 to 8.5) and CI |

Also reported for designs outside the kernel: the ezwebin and ezdemo designs still link to
`shop/customerorderview` without `|rawurlencode`; the view change makes "+" work for them as well.
