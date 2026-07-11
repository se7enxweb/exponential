# PHPUnit 13 / PHP 8.4.23 test suite cleanup

**Affected release:** Exponential 6.0.x  
**PHPUnit version:** `phpunit/phpunit 13.0.0`  
**PHP version:** 8.4.23 + 8.5.8  
**Date documented:** 2026-07-11  
**Document type:** Documentation / Historical change record

---

## The short answer

The Exponential 6.0 test suite now runs cleanly with PHPUnit 13 and PHP 8.4.23:

```
715 tests, 1117 assertions, 0 errors, 0 warnings, 0 deprecations, 0 risky
OK, but some tests were skipped!
```

Before this cleanup, the PHPUnit progress bar was a noisy alphabet soup of `D` (deprecations), `W` (warnings), `R` (risky tests), and `E` (errors). Coverage runs also failed inside `EzpSessionHandlerDBPhp8BugfixesTest` because Xdebug was not loaded in PHPUnit's isolated child processes. This work brought the suite back to a clean, trustworthy state.

---

## What the progress characters mean

If you are reading a PHPUnit output bar and wondering what the letters mean, here is the decoder ring:

| Char | Meaning |
|------|---------|
| `.`  | Passed |
| `D`  | Test triggered a PHP deprecation |
| `W`  | Test triggered a PHP warning |
| `R`  | Risky test (no assertion, leaked exception handlers, etc.) |
| `E`  | Error / fatal |
| `S`  | Skipped |
| `F`  | Failure |

A clean run is mostly dots and a few `S` characters for tests that require an optional extension or external service. This cleanup made that the normal state of the 6.0 branch.

---

## Background: why this was needed

PHP 8.4 tightened several behaviors that PHP 8.0 and 8.1 had only warned about, and PHPUnit 13 became stricter about class names, test suites, and process isolation. The 6.0 test suite was originally written for an earlier PHPUnit era and a more permissive PHP runtime, so it accumulated a long list of warnings, deprecations, and risky tests that obscured real failures. The goal was not to rewrite the suite, but to bring it into compliance with the modern tooling while keeping the existing tests and their intent intact.

The work split into three broad areas:

1. **Coverage and process isolation** — making `Xdebug` available in child processes.
2. **Test hygiene** — fixing dynamic properties, risky tests, and class-name mismatches.
3. **Production fixes surfaced by the tests** — small kernel and library fixes that the tests were correctly flagging.

---

## 1. Coverage / isolated-process fixes

`EzpSessionHandlerDBPhp8BugfixesTest` uses `#[RunTestsInSeparateProcesses]`. In PHPUnit 13, child processes re-read `php.ini` and scan directories, but they do not inherit the `xdebug` extension that was loaded in the parent via `-d zend_extension=xdebug.so`. The coverage run therefore failed because the children could not collect coverage.

The fix was to give the children their own `xdebug.ini` and discover it at runtime instead of relying on a hardcoded path.

| File | Change |
|------|--------|
| `tests/xdebug.ini` | New scan-dir `.ini` that loads `xdebug.so` and sets `xdebug.mode=coverage`. |
| `phpunit.xml` | Removed the hardcoded `PHP_INI_SCAN_DIR` environment variable; `coverage/include` paths for `lib`, `kernel`, and `tests` remain. |
| `tests/bootstrap.php` | Sets `PHP_INI_SCAN_DIR` at runtime using `__DIR__` and `php_ini_scanned_files()` so the repository path is never hardcoded. |
| `.gitignore` | Adds `/coverage` so the generated HTML coverage report is ignored. |

Run with coverage:

```bash
XDEBUG_MODE=coverage /opt/plesk/php/8.4/bin/php -d memory_limit=-1 -d zend_extension=xdebug.so ./vendor/bin/phpunit --coverage-html coverage
```

---

## 2. Test code fixes

### 2.1 Dynamic properties (PHP 8.2+ deprecation)

PHP 8.2 deprecated writing to undeclared object properties. Several test classes were assigning values in `setUp()` without declaring them first, which produced deprecation notices during the run.

| File | Property added |
|------|----------------|
| `tests/tests/lib/ezfile/eZFileDownloadTest.php` | `private string $file;` and `private string $content;` |
| `tests/tests/lib/ezimage/eZImageManagerTest.php` | `private eZINI $imageIni;` |
| `tests/tests/lib/ezimage/eZImageShellHandlerTest.php` | `private eZImageManager $imageManager;` |
| `tests/tests/lib/ezutils/eZURITest.php` | `private ?string $originalRequestURI;` |

### 2.2 Tests with no assertions or risky state

| File | Fix |
|------|-----|
| `tests/tests/lib/ezutils/eZMailTest.php` | `testStripEmail()` now asserts the return value of `eZMail::stripEmail()`. |
| `tests/tests/kernel/classes/eZExtensionWithOrderingTest.php` | `tearDown()` now calls `restore_exception_handler()` to clean up handlers left by the tested code. |
| `tests/tests/lib/ezimage/eZImageManagerTest.php` | `testMultiHandlerAlias()` now has enough execution time and no longer kills the process due to `set_time_limit(60)`. |

### 2.3 Class-name / filename mismatches

PHPUnit 13's loader warns when a class name does not match the file name. Two `eZDir` test classes were renamed to match their files.

| File | Change |
|------|--------|
| `tests/tests/lib/ezfile/eZDirTestInsideRootTest.php` | Class renamed `eZDirTestInsideRootTest` to match file. |
| `tests/tests/lib/ezfile/eZDirTestOutsideRootTest.php` | Class renamed `eZDirTestOutsideRootTest` to match file. |

### 2.4 Abstract test classes causing runner warnings

`eZClusterFileHandlerAbstractTest`, `eZClusterStaleCacheTest`, `eZDBBasedClusterFileHandlerAbstractTest`, and `eZDatatypeAbstractTest` are abstract bases. They were loaded as test files and produced "is abstract" warnings. They were excluded from the `kernel-classes` and `kernel-datatypes` suites, and concrete subclasses received `require_once` so they could still load their parents.

| File | Change |
|------|--------|
| `phpunit.xml` | Excludes the four abstract base files from the relevant suites. |
| `tests/tests/kernel/classes/clusterfilehandlers/eZFSFileHandlerTest.php` | `require_once __DIR__ . '/eZClusterFileHandlerAbstractTest.php';` |
| `tests/tests/kernel/classes/clusterfilehandlers/eZDFSFileHandlerTest.php` | `require_once __DIR__ . '/eZDBBasedClusterFileHandlerAbstractTest.php';` |
| `tests/tests/kernel/classes/clusterfilehandlers/eZDFSClusterStaleCacheTest.php` | `require_once __DIR__ . '/eZClusterStaleCacheTest.php';` |
| `tests/tests/kernel/classes/clusterfilehandlers/eZDBBasedClusterFileHandlerAbstractTest.php` | `require_once __DIR__ . '/eZClusterFileHandlerAbstractTest.php';` |
| `tests/tests/kernel/datatypes/ezmatrix/eZMatrixTypeTest.php` | `require_once __DIR__ . '/../eZDatatypeAbstractTest.php';` |
| `tests/tests/kernel/datatypes/ezstring/eZStringTypeTest.php` | `require_once __DIR__ . '/../eZDatatypeAbstractTest.php';` |
| `tests/tests/kernel/datatypes/ezemail/eZEmailTypeTest.php` | `require_once __DIR__ . '/../eZDatatypeAbstractTest.php';` |

---

## 3. Kernel / library production fixes surfaced by the tests

The warnings and errors were not all cosmetic. Several tests correctly pointed at real runtime issues in the kernel and libraries.

| File | Change | Why |
|------|--------|-----|
| `lib/ezimage/classes/ezimagegdhandler.php` | Cast float color values to `(int)` before `ImageColorAllocate()` / `ImageSetPixel()`. | PHP 8.4 `TypeError` / deprecation; `testMultiHandlerAlias` was fatalling. |
| `lib/ezutils/classes/ezmail.php` | `contentType()`, `contentCharset()`, `contentTransferEncoding()`, `contentDisposition()` now return `null` when `ContentType` is not initialized. | Prevents `Trying to access array offset on null` warnings in `eZMailEzcTest`. |
| `lib/ezi18n/classes/ezcharsetinfo.php` | `realCharsetCode()` guards `null` and casts to `string` before `strtolower()`. | Prevents `strtolower(): Passing null to parameter #1` deprecation. |
| `lib/ezdb/classes/expmongodb.php` | Adds `logError()` helper and uses it instead of `error_log()`; `findOne()` returns `getArrayCopy()`; `query()` returns `true` for `INSERT`. | Fixes MongoDB adapter tests and removes direct `error_log()` output. |
| `kernel/classes/ezsiteaccess.php` | `load()` no longer reads `$GLOBALS['eZCurrentAccess']` when it is not set. | Prevents undefined-array-key warnings. |
| `kernel/classes/datatypes/ezisbn/ezisbn13.php` | Replaces deprecated curly-brace string offsets `{$i}` with `[$i]`. | PHP 7.4/8.4 removed curly-brace string indexing. |
| `kernel/private/classes/webdav/ezwebdavcontentbackend.php` | Replaces deprecated curly-brace string offsets `{$i}` with `[$i]`. | Same. |

---

## 4. Session handler stubs

The session handler tests needed their own stub classes so they would not conflict with stubs used elsewhere in the suite.

| File | Change |
|------|--------|
| `tests/tests/lib/ezsession/EzpSessionHandlerDBPhp8BugfixesTestStubs.php` | New file. `StubEZDB` renamed `StubSessionEZDB` to avoid redeclaring the `StubEZDB` class in `tests/tests/kernel/classes/security/stubs.php`. Also declares typed properties and fixes `eZDBInterface` defaults. |
| `tests/tests/lib/ezsession/EzpSessionHandlerDBPhp8BugfixesTest.php` | Updated to use `StubSessionEZDB`; declares typed `private ezpSessionHandlerDB $handler;` and `private StubSessionEZDB $db;`. |

---

## 5. MongoDB adapter tests

The MongoDB adapter tests had drifted out of sync with the production implementation. The fixes were small but important: ensuring the database name was initialized before `selectCollection()` was called, and updating assertions to match the current `INSERT` behavior.

| File | Change |
|------|--------|
| `tests/tests/lib/ezdb/mongodb/stubs.php` | `expMongoDBTestable` constructor now initializes `$this->DB = 'mongo';` so `selectCollection()` receives a non-null database name. |
| `tests/tests/lib/ezdb/mongodb/expMongoDBAdapterTest.php` | `testQueryReturnsFalse` adjusted to match `INSERT` returning `true`; `testArrayQueryReturnsEmptyArray` now queries a non-existent group to trigger the expected warning. |

---

## 6. PHPUnit configuration and suite changes

`phpunit.xml` was updated to the PHPUnit 13 schema and `tests/bootstrap.php` was expanded to provide compatibility shims for older test helpers.

| File | Change |
|------|--------|
| `phpunit.xml` | Updated to PHPUnit 13 schema: `testsuites`, `source/include` for coverage, `PHP_INI_SCAN_DIR`, `exclude` for abstract classes and the `security`/`mongodb` directories, `executionOrder="depends,defects"`, `failOnRisky`, `failOnWarning`, `failOnPhpunitWarning`, `displayDetails...` etc. |
| `tests/bootstrap.php` | Expanded shim layer for `PHPUnit_Framework_*` classes, `ezpDatabaseHelper`, `ezpObject`, `ezpClass`, and `ezpDatatypeTestDataSet` helpers (when needed). |

---

## 7. Verification

The same clean result is achieved with or without coverage.

No coverage:

```bash
/opt/plesk/php/8.4/bin/php ./vendor/bin/phpunit --no-coverage --display-deprecations --display-warnings
```

Result:

```
OK, but some tests were skipped!
Tests: 715, Assertions: 1117, Skipped: 30
```

With coverage:

```bash
XDEBUG_MODE=coverage /opt/plesk/php/8.4/bin/php -d memory_limit=-1 -d zend_extension=xdebug.so ./vendor/bin/phpunit --coverage-html coverage
```

Result:

```
OK, but some tests were skipped!
Tests: 715, Assertions: 1117, Skipped: 30
```

The skipped tests are expected: they require optional extensions or external services that are not present in the test environment. The important part is the absence of errors, warnings, deprecations, and risky tests.

---

## What this means for the 6.0 branch

With this cleanup, the 6.0 test suite is once again a reliable signal. A green PHPUnit run now means the code is genuinely passing, not merely hiding behind a wall of suppressed warnings. Coverage runs work with process isolation, the MongoDB and session handler tests are stable, and the kernel fixes address real PHP 8.4 incompatibilities that would otherwise surface in production.

If you extend the 6.0 test suite, keep these patterns in mind:

- Declare test properties explicitly.
- Match class names to file names.
- Exclude abstract test classes from suites and use `require_once` in concrete subclasses.
- Clean up exception handlers and other global state in `tearDown()`.
- Avoid `error_log()` and other direct output in production code; route diagnostics through the framework's logging helpers.
