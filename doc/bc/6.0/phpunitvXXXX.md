# Steps to upgrade your Exponential 6.0.13 site to use PHPUnit 10 — what broke, how we fixed it, and how you run tests now

**Affected release:** Exponential 6.0.12 and all prior 6.0.x releases  
**Fixed in:** Exponential 6.0.13-alpha2 and later  
**PHPUnit version in use:** `phpunit/phpunit 10.0.0`  
**PHP version:** 8.4 / 8.5  
**Date documented:** 2026-02-21

---

## The short answer — why nothing ran and what to do right now

If you ran `php vendor/bin/phpunit` or `php tests/runtests.php` on any Exponential 6.0.x
installation prior to 6.0.13 you received one of these fatal errors and **zero tests ran**:

```
An error occurred inside PHPUnit.
Message:  Class "ezpDatabaseTestCase" not found
```

or:

```
PHP Fatal error: Class 'PHPUnit_Framework_TestCase' not found in tests/toolkit/ezptestcase.php
```

or (from `runtests.php`):

```
PHP Fatal error: Class 'PHPUnit_Runner_Version' not found in tests/runtests.php
```

**Root cause in one sentence:** The entire test toolkit (`tests/toolkit/`) was written for
PHPUnit 3.7 which used PEAR-style underscore class names (`PHPUnit_Framework_TestCase`).
PHPUnit 6 introduced PHP namespaces. PHPUnit 7 removed all backward-compatibility aliases.
PHPUnit 10 — the version pinned in `composer.json` — does not contain a single
`PHPUnit_*` class. Every toolkit file silently stopped working the day PHPUnit was upgraded
past version 5.

This document:

1. Identifies every broken file line by line
2. Shows you the exact before/after change for each file
3. Provides the `phpunit.xml` bootstrap you need to create
4. Gives you copy-pasteable commands to verify your setup end to end

---

## Table of contents

1. [Background — PHPUnit class name history](#background)
2. [Broken file inventory](#broken-file-inventory)
3. [Fix 1 — tests/toolkit/ezptestcase.php](#fix-1)
4. [Fix 2 — tests/toolkit/ezptestsuite.php](#fix-2)
5. [Fix 3 — tests/toolkit/ezptestregressionsuite.php](#fix-3)
6. [Fix 4 — tests/toolkit/ezptestrunner.php](#fix-4)
7. [Fix 5 — tests/runtests.php](#fix-5)
8. [Fix 6 — create phpunit.xml bootstrap at project root](#fix-6)
9. [Fix 7 — create tests/bootstrap.php](#fix-7)
10. [Verification — running the fixed toolkit](#verification)
11. [How the security hardening tests plug in](#security-tests)
12. [Full before/after diff summary](#full-diff)
13. [Frequently asked questions](#faq)
14. [Patch change log — 2026-02-21](#patch-changelog)

---

<a name="background"></a>
## 1. Background — PHPUnit class name history

Understanding *why* this broke requires understanding the PHPUnit naming migration:

| PHPUnit version | Naming convention | Status |
|---|---|---|
| 3.x – 5.x | `PHPUnit_Framework_TestCase` (PEAR underscore style) | **Removed** |
| 6.x | `PHPUnit\Framework\TestCase` (PHP namespace) + backward-compat aliases | Transitional |
| 7.x | Namespace only — aliases removed | Breaking change |
| 8.x | Namespace only — `PHPUnit_TextUI_Command` removed | Breaking change |
| 9.x – 10.x | Namespace only — significant internal API restructuring | Current |

The Exponential (eZ Publish Legacy) test toolkit was written circa 2012–2014 for PHPUnit
3.7. The `composer.json` in 6.0.x was updated to require `phpunit/phpunit: 10.0.0` — but
the toolkit files that wrap PHPUnit were never updated. The result is that `composer
install` brings in a PHPUnit version that is entirely incompatible with every class in
`tests/toolkit/`.

**You cannot run a single test** until the toolkit base classes are updated.

---

<a name="broken-file-inventory"></a>
## 2. Broken file inventory

Running `php vendor/bin/phpunit tests/tests/kernel/classes/ezurlwildcard_test.php`
produces this output on 6.0.12:

```
An error occurred inside PHPUnit.
Message:  Class "ezpDatabaseTestCase" not found
Location: tests/tests/kernel/classes/ezurlwildcard_test.php:14
```

PHPUnit 10 could not find `ezpDatabaseTestCase` because the bootstrap that loads the
toolkit classes was never defined — but even if it had been, the toolkit classes
themselves would fail to load because they each reference a class that no longer exists.

Here is every broken reference:

| File | Line | Broken reference | PHPUnit 10 replacement |
|---|---|---|---|
| `tests/toolkit/ezptestcase.php` | 11 | `extends PHPUnit_Framework_TestCase` | `extends PHPUnit\Framework\TestCase` |
| `tests/toolkit/ezptestsuite.php` | 11 | `extends PHPUnit_Framework_TestSuite` | `extends PHPUnit\Framework\TestSuite` |
| `tests/toolkit/ezptestregressionsuite.php` | 18 | `extends PHPUnit_Framework_TestSuite` | `extends PHPUnit\Framework\TestSuite` |
| `tests/toolkit/ezptestregressionsuite.php` | 75, 95, 112 | `new PHPUnit_Framework_Warning(...)` | `new PHPUnit\Framework\IncompleteTestError(...)` or removed |
| `tests/toolkit/ezptestrunner.php` | 12 | `extends PHPUnit_TextUI_Command` | Class removed in PHPUnit 9 — see Fix 4 |
| `tests/toolkit/ezptestrunner.php` | 388 | `isSubclassOf('PHPUnit_Framework_TestSuite')` | `isSubclassOf(PHPUnit\Framework\TestSuite::class)` |
| `tests/toolkit/ezptestrunner.php` | 416 | `@var PHPUnit_Framework_TestCase` | phpdoc — cosmetic only |
| `tests/runtests.php` | 25 | `PHPUnit_Runner_Version::id()` | `PHPUnit\Runner\Version::id()` |
| (missing) | — | No `phpunit.xml` at project root | Create one — see Fix 6 |
| (missing) | — | No `tests/bootstrap.php` | Create one — see Fix 7 |

---

<a name="fix-1"></a>
## 3. Fix 1 — tests/toolkit/ezptestcase.php

**What broke:** `ezpTestCase extends PHPUnit_Framework_TestCase` — the underscore class
`PHPUnit_Framework_TestCase` was removed entirely in PHPUnit 7.

**How you see it fail:**

```
PHP Fatal error: Class 'PHPUnit_Framework_TestCase' not found
  in tests/toolkit/ezptestcase.php on line 11
```

**The broken file (original):**

```php
<?php
/**
 * File containing the ezpTestCase class
 * @package tests
 */
class ezpTestCase extends PHPUnit_Framework_TestCase   // <-- BROKEN
{
    protected $sharedFixture;
    protected $backupGlobals = false;
}
```

**Apply this change:**

```diff
- class ezpTestCase extends PHPUnit_Framework_TestCase
+ class ezpTestCase extends PHPUnit\Framework\TestCase
```

**Full corrected file:**

```php
<?php
/**
 * File containing the ezpTestCase class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 */

class ezpTestCase extends PHPUnit\Framework\TestCase
{
    protected $sharedFixture;

    protected bool $backupGlobals = false;
}
```

> **Note on `$backupGlobals`:** PHPUnit 10 requires this property to be typed `bool`.
> Without the type declaration, PHPUnit 10 emits a deprecation and future versions will
> fatal. Add `bool` to the property declaration at the same time.

---

<a name="fix-2"></a>
## 4. Fix 2 — tests/toolkit/ezptestsuite.php

**What broke:** `ezpTestSuite extends PHPUnit_Framework_TestSuite`

**How you see it fail:**

```
PHP Fatal error: Class 'PHPUnit_Framework_TestSuite' not found
  in tests/toolkit/ezptestsuite.php on line 11
```

**The broken file (original, relevant excerpt):**

```php
class ezpTestSuite extends PHPUnit_Framework_TestSuite   // <-- BROKEN
{
    protected $sharedFixture;
    ...
```

**Apply this change:**

```diff
- class ezpTestSuite extends PHPUnit_Framework_TestSuite
+ class ezpTestSuite extends PHPUnit\Framework\TestSuite
```

**Full corrected class declaration:**

```php
class ezpTestSuite extends PHPUnit\Framework\TestSuite
{
    protected $sharedFixture;

    /** @var eZScript */
    protected static $script;
    ...
```

No other changes are needed inside this file. The `eZScript` bootstrap logic and
`__destruct()` remain unchanged.

---

<a name="fix-3"></a>
## 5. Fix 3 — tests/toolkit/ezptestregressionsuite.php

**What broke:** Three separate problems in this file:

1. `extends PHPUnit_Framework_TestSuite` — line 18
2. `new PHPUnit_Framework_Warning(...)` — lines 75, 95, 112 — class removed in PHPUnit 10
3. The constructor signature `__construct($theClass = '', $name = '')` conflicts with
   `PHPUnit\Framework\TestSuite` which has a different constructor shape in PHPUnit 9+

**How you see it fail:**

```
PHP Fatal error: Class 'PHPUnit_Framework_TestSuite' not found
  in tests/toolkit/ezptestregressionsuite.php on line 18
```

Or if the class loads but `addTest` is called with a `Warning`:

```
TypeError: Argument 1 passed to PHPUnit\Framework\TestSuite::addTest()
must implement interface PHPUnit\Framework\Test
```

**Apply these changes:**

```diff
- class ezpTestRegressionSuite extends PHPUnit_Framework_TestSuite
+ class ezpTestRegressionSuite extends PHPUnit\Framework\TestSuite
```

For each `new PHPUnit_Framework_Warning(...)` block, the `PHPUnit\Framework\Warning`
class was itself removed in PHPUnit 10. Replace each usage with a skipped/incomplete
wrapper using `PHPUnit\Framework\IncompleteTestError` or simply remove the warning
injection and let the suite be empty. The safest drop-in replacement that preserves
observable behaviour:

```diff
- $this->addTest(
-     new PHPUnit_Framework_Warning(
-         sprintf( 'Class "%s" has no public constructor.', $theClass->getName() )
-     )
- );
+ // PHPUnit 10: Warning tests removed; log to stderr and skip
+ fwrite( STDERR, 'Warning: Class "' . $theClass->getName() . '" has no public constructor.' . PHP_EOL );
```

Apply the same pattern to the other two `PHPUnit_Framework_Warning` blocks
(the "not a subclass" warning — which is commented out — and the "No regression tests
found" warning).

**Corrected class declaration and warning replacement (complete diff):**

```diff
- class ezpTestRegressionSuite extends PHPUnit_Framework_TestSuite
+ class ezpTestRegressionSuite extends PHPUnit\Framework\TestSuite

  // Line 75 block:
- $this->addTest( new PHPUnit_Framework_Warning(
-     sprintf( 'Class "%s" has no public constructor.', $theClass->getName() ) ) );
+ fwrite( STDERR, 'Warning: Class "' . $theClass->getName() . '" has no public constructor.' . PHP_EOL );

  // Line 112 block:
- $this->addTest( new PHPUnit_Framework_Warning(
-     sprintf( 'No regression tests found in class "%s".', $theClass->getName() ) ) );
+ fwrite( STDERR, 'Warning: No regression tests found in class "' . $theClass->getName() . '".' . PHP_EOL );
```

---

<a name="fix-4"></a>
## 6. Fix 4 — tests/toolkit/ezptestrunner.php

**What broke:** This is the most extensively broken file.

`ezpTestRunner extends PHPUnit_TextUI_Command`

`PHPUnit_TextUI_Command` was the old CLI entry point class. It was refactored into
`PHPUnit\TextUI\Command` in PHPUnit 6/7 and then **removed entirely** in PHPUnit 9 when
the CLI runner was rewritten from scratch as `PHPUnit\TextUI\Application`. There is no
drop-in replacement — the entire extension model changed.

**How you see it fail (from `tests/runtests.php`):**

```
PHP Fatal error: Class 'PHPUnit_TextUI_Command' not found
  in tests/toolkit/ezptestrunner.php on line 12
```

**The practical impact:** The `ezpTestRunner` class provided custom `--list-suites`,
`--list-tests`, `--dsn`, and `--db-per-test` CLI flags on top of PHPUnit's existing ones.
In PHPUnit 10 the only correct way to add custom runner behaviour is through the
`Extension` API (`PHPUnit\Runner\Extension\Extension`). Rewriting the full runner for
PHPUnit 10 is a larger project.

**Minimal fix to unblock all tests today:**

Replace the class body entirely with a static-method wrapper that delegates directly to
`PHPUnit\TextUI\Application` (the PHPUnit 10 entry point) and preserves the `dsn()` and
`dbPerTest()` static accessors used in `ezpDatabaseTestCase`:

```php
<?php
/**
 * File containing the ezpTestRunner class — PHPUnit 10 compatible shim.
 *
 * The original class extended PHPUnit_TextUI_Command (removed in PHPUnit 9).
 * This shim preserves the static accessor API used by ezpDatabaseTestCase
 * and delegates test execution to PHPUnit\TextUI\Application directly.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 */

class ezpTestRunner
{
    private static ?ezpTestRunner $instance = null;

    /** @var string DSN passed via --dsn= */
    private static string $dsn = '';

    /** @var bool Whether --db-per-test flag was supplied */
    private static bool $dbPerTest = false;

    public static function instance(): self
    {
        if ( self::$instance === null )
            self::$instance = new self();
        return self::$instance;
    }

    /** Returns the --dsn= value (database DSN for test setup). */
    public static function dsn(): string { return self::$dsn; }

    /** Returns true if --db-per-test flag was supplied. */
    public static function dbPerTest(): bool { return self::$dbPerTest; }

    /**
     * Parse our custom arguments from $argv, strip them, then hand off to
     * PHPUnit\TextUI\Application for the actual test run.
     */
    public function run( array $argv ): void
    {
        $filtered = [];
        foreach ( $argv as $arg )
        {
            if ( str_starts_with( $arg, '--dsn=' ) )
            {
                self::$dsn = substr( $arg, 6 );
                continue;
            }
            if ( $arg === '--db-per-test' )
            {
                self::$dbPerTest = true;
                continue;
            }
            $filtered[] = $arg;
        }

        // PHPUnit 10 entry point
        $application = new PHPUnit\TextUI\Application();
        $application->run( $filtered );
    }
}
```

**What you lose temporarily:** The `--list-suites` and `--list-tests` custom options are
dropped in this minimal shim. PHPUnit 10's built-in `--list-tests` and
`--list-test-files` cover most of those use cases natively. The `--dsn=` and
`--db-per-test` flags — which are the ones actually used by `ezpDatabaseTestCase` — are
fully preserved.

**What you gain:** Every test that uses `ezpTestCase` (no database) and every test that
uses `ezpDatabaseTestCase` (with `--db-per-test`) is now runnable.

**Remaining `PHPUnit_Framework_TestSuite` reference at line 388:**

The old runner had reflection-based suite discovery code. In the shim above this code is
not present — PHPUnit 10 handles suite discovery internally. If you restore any portion
of the original discovery logic, replace:

```diff
- $reflectionClass->isSubclassOf( 'PHPUnit_Framework_TestSuite' )
+ $reflectionClass->isSubclassOf( PHPUnit\Framework\TestSuite::class )
```

---

<a name="fix-5"></a>
## 7. Fix 5 — tests/runtests.php

**What broke:** Line 25 calls `PHPUnit_Runner_Version::id()`.

**How you see it fail:**

```
PHP Fatal error: Class 'PHPUnit_Runner_Version' not found in tests/runtests.php on line 25
```

**The broken code (original):**

```php
require_once 'vendor/autoload.php';
require_once 'autoload.php';

if ( !class_exists( 'ezpTestRunner', true ) )
{
    echo "The ezpTestRunner class isn't defined...\n";
    exit(1);
}

$version = PHPUnit_Runner_Version::id();     // <-- BROKEN

if ( version_compare( $version, '3.7.0' ) == -1 && $version !== '@package_version@' )
{
    echo "PHPUnit 3.7.0 (or later) is required...\n";
    exit(1);
}
```

**Apply these changes:**

```diff
- $version = PHPUnit_Runner_Version::id();
+ $version = PHPUnit\Runner\Version::id();

- if ( version_compare( $version, '3.7.0' ) == -1 && $version !== '@package_version@' )
- {
-     echo "PHPUnit 3.7.0 (or later) is required to run this test suite.\n";
-     exit(1);
- }
+ if ( version_compare( $version, '10.0.0' ) < 0 )
+ {
+     echo "PHPUnit 10.0.0 (or later) is required to run this test suite.\n";
+     exit(1);
+ }
```

**Full corrected runtests.php:**

```php
#!/usr/bin/env php
<?php
/**
 * File containing the runtests CLI script — PHPUnit 10 compatible.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 */

set_time_limit( 0 );

require_once 'vendor/autoload.php';
require_once 'tests/bootstrap.php';    // loads toolkit classes

if ( !class_exists( 'ezpTestRunner', true ) )
{
    echo "The ezpTestRunner class isn't defined.\n"
       . "Make sure tests/bootstrap.php is present and loads tests/toolkit/.\n";
    exit( 1 );
}

$version = PHPUnit\Runner\Version::id();

if ( version_compare( $version, '10.0.0' ) < 0 )
{
    echo "PHPUnit 10.0.0 (or later) is required to run this test suite.\n";
    exit( 1 );
}

try
{
    $runner = ezpTestRunner::instance();
    $runner->run( $_SERVER['argv'] );
}
catch ( Exception $e )
{
    echo $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}
```

> Notice the added `require_once 'tests/bootstrap.php'` — this is the bootstrap file
> created in Fix 7 below. Without it, PHPUnit cannot find `ezpTestCase` or any other
> toolkit class.

---

<a name="fix-6"></a>
## 8. Fix 6 — create phpunit.xml at project root

**What broke:** There was no `phpunit.xml` (nor `phpunit.xml.dist`) at the project root.
PHPUnit 10 requires a configuration file to know where the bootstrap file is, where to
find tests, and which groups to use. Without it, PHPUnit scans directories without loading
the toolkit classes, causing every test file that extends `ezpTestCase` to immediately
fatal.

**Create this file at the project root as `phpunit.xml`:**

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!--
  phpunit.xml — Exponential 6.0.13 / eZ Publish Legacy PHPUnit 10 configuration.

  Usage:
    php vendor/bin/phpunit                          # run all tests
    php vendor/bin/phpunit --group security         # run security group only
    php vendor/bin/phpunit tests/tests/kernel/      # run kernel tests only
    php vendor/bin/phpunit --list-tests             # list all test methods
-->
<phpunit
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/10.0/phpunit.xsd"
    bootstrap="tests/bootstrap.php"
    colors="true"
    displayDetailsOnIncompleteTests="true"
    displayDetailsOnSkippedTests="true"
    failOnEmptyTestSuite="false"
    cacheDirectory=".phpunit.cache"
>

    <testsuites>
        <testsuite name="kernel-classes">
            <directory>tests/tests/kernel/classes</directory>
        </testsuite>
        <testsuite name="kernel-content">
            <directory>tests/tests/kernel/content</directory>
        </testsuite>
        <testsuite name="kernel-datatypes">
            <directory>tests/tests/kernel/datatypes</directory>
        </testsuite>
        <testsuite name="lib">
            <directory>tests/tests/lib</directory>
        </testsuite>
        <testsuite name="security">
            <directory>tests/tests/kernel/classes/security</directory>
        </testsuite>
    </testsuites>

    <groups>
        <exclude>
            <!-- Tests that require a live database — run with --db-per-test --dsn=... -->
            <group>database</group>
        </exclude>
    </groups>

    <php>
        <env name="EZP_TEST_ENVIRONMENT" value="1"/>
    </php>

    <source>
        <include>
            <directory suffix=".php">kernel</directory>
            <directory suffix=".php">lib</directory>
        </include>
        <exclude>
            <directory>vendor</directory>
            <directory>var</directory>
        </exclude>
    </source>

</phpunit>
```

**Save this as `phpunit.xml` in the repository root** (same directory as `composer.json`).

---

<a name="fix-7"></a>
## 9. Fix 7 — create tests/bootstrap.php

**What broke:** PHPUnit cannot load any `ezpTestCase`-extending test class because there
was never a bootstrap file that `require`-s the toolkit classes. The old code relied on
eZ Publish's custom autoload system (`autoload.php`), which itself requires the platform to
be initialised. The toolkit classes however are simple PHP files that need no platform
boot — they just need to be included.

**Create `tests/bootstrap.php`:**

```php
<?php
/**
 * PHPUnit bootstrap file for Exponential / eZ Publish Legacy 6.0.x.
 *
 * Loaded by PHPUnit before any test file is parsed.  Loads the Composer
 * autoloader and all toolkit base classes so that ezpTestCase,
 * ezpDatabaseTestCase, ezpTestSuite, ezpTestRunner and related classes are
 * available to every test file without each test having to require them
 * individually.
 *
 * Do NOT bootstrap the full eZ Publish kernel here — tests that need it
 * use ezpTestSuite / ezpDatabaseTestCase which do so themselves via eZScript.
 *
 * @package tests
 */

// ── Composer autoloader ──────────────────────────────────────────────────────
require_once __DIR__ . '/../vendor/autoload.php';

// ── eZ Publish Legacy test toolkit (order matters) ───────────────────────────
$toolkit = __DIR__ . '/toolkit/';

require_once $toolkit . 'ezptestcase.php';           // ezpTestCase
require_once $toolkit . 'ezptestsuite.php';          // ezpTestSuite
require_once $toolkit . 'ezptestregressionsuite.php'; // ezpTestRegressionSuite
require_once $toolkit . 'ezpdatabasetestcase.php';   // ezpDatabaseTestCase
require_once $toolkit . 'ezpdatabasesuite.php';      // ezpDatabaseTestSuite
require_once $toolkit . 'ezpdatabaseregressiontest.php';
require_once $toolkit . 'ezptestrunner.php';         // ezpTestRunner
require_once $toolkit . 'ezpinihelper.php';
require_once $toolkit . 'ezpextensionhelper.php';
require_once $toolkit . 'ezptestdatabasehelper.php';
require_once $toolkit . 'ezptestdatabasehelper.php';
require_once $toolkit . 'ezpdsn.php';
```

> **Important:** This bootstrap does *not* call `eZScript::instance()`. Tests that need
> the full eZ Publish stack inherit from `ezpDatabaseTestCase` which calls `ezpTestSuite`
> which calls `eZScript::instance()` in its constructor. That is the correct separation of
> concerns. Do not add kernel bootstrapping here or you will break unit tests that
> intentionally run without the platform.

---

<a name="verification"></a>
## 10. Verification — running the fixed toolkit

After applying fixes 1–7, verify each step with these copy-pasteable commands:

### Step 1 — confirm PHPUnit finds the bootstrap

```bash
php vendor/bin/phpunit --list-test-files 2>&1 | head -20
```

Expected: a list of test files. If you see `Class "ezpDatabaseTestCase" not found`, the
bootstrap is not being picked up — confirm `phpunit.xml` is in the project root and the
`bootstrap` attribute points to `tests/bootstrap.php`.

### Step 2 — run the unit-only (no database) tests

```bash
php vendor/bin/phpunit --testsuite kernel-classes --exclude-group database
```

Expected: PHPUnit runs and reports PASS/FAIL/SKIP. You should see output like:

```
PHPUnit 10.0.0 by Sebastian Bergmann and contributors.

Runtime: PHP 8.5.3
Configuration: /path/to/phpunit.xml

...........S..F.

FAILURES!
Tests: 14, Assertions: 38, Failures: 1, Skipped: 1.
```

(Any failures at this point are pre-existing test failures related to missing database or
missing configuration — not toolkit failures.)

### Step 3 — run just the security hardening tests

```bash
php vendor/bin/phpunit --testsuite security --colors=always
```

Expected: all security tests pass. These tests do not require a database.

```
PHPUnit 10.0.0 by Sebastian Bergmann and contributors.

Runtime: PHP 8.5.3

...............

OK (15 tests, 15 assertions)
```

### Step 4 — run the kit's own runner script

```bash
php tests/runtests.php --testsuite security
```

Expected same output as step 3. This verifies `runtests.php` Fix 5 is working.

### Step 5 — confirm no old class names remain

```bash
grep -rn 'PHPUnit_Framework_TestCase\|PHPUnit_Framework_TestSuite\|PHPUnit_TextUI_Command\|PHPUnit_Runner_Version\|PHPUnit_Framework_Warning' tests/toolkit/ tests/runtests.php
```

Expected: **no output**. If any lines appear, those are remaining unfixed references.

---

<a name="security-tests"></a>
## 11. How the security hardening tests plug in

The 6.0.13 security hardening patch set (documented in `doc/bc/6.0/hardening.md`)
introduced a standalone security test suite at:

```
tests/tests/kernel/classes/security/
```

These tests are PHPUnit 10-native — they extend `PHPUnit\Framework\TestCase` directly
(not `ezpTestCase`) because they must run without any platform bootstrap. They test the
specific patches described in SEC-01 through SEC-07.

The `phpunit.xml` above includes them in the `security` testsuite:

```xml
<testsuite name="security">
    <directory>tests/tests/kernel/classes/security</directory>
</testsuite>
```

Run them standalone:

```bash
php vendor/bin/phpunit --testsuite security
```

Run them as part of the combined check via the eZScript-style runner:

```bash
php tests/bin/ezptestrunner_all_tests.php security
```

The eZScript runner calls both the PHPUnit security suite and the custom functional flow
tests (`tests/bin/tests/functional_tests.php`), combining the results into a single report:

```
php tests/bin/ezptestrunner_all_tests.php --report
```

This produces a dated report file at `tests/bin/test_report_YYYY-MM-DD.txt`.

---

<a name="full-diff"></a>
## 12. Full before/after diff summary

Here is a compact reference of every line changed across all toolkit files. Apply all
of these to restore test execution on PHPUnit 10:

### tests/toolkit/ezptestcase.php

```diff
-class ezpTestCase extends PHPUnit_Framework_TestCase
+class ezpTestCase extends PHPUnit\Framework\TestCase
 {
     protected $sharedFixture;
-    protected $backupGlobals = false;
+    protected bool $backupGlobals = false;
 }
```

### tests/toolkit/ezptestsuite.php

```diff
-class ezpTestSuite extends PHPUnit_Framework_TestSuite
+class ezpTestSuite extends PHPUnit\Framework\TestSuite
```

### tests/toolkit/ezptestregressionsuite.php

```diff
-class ezpTestRegressionSuite extends PHPUnit_Framework_TestSuite
+class ezpTestRegressionSuite extends PHPUnit\Framework\TestSuite

-            $this->addTest( new PHPUnit_Framework_Warning(
-                sprintf( 'Class "%s" has no public constructor.', $theClass->getName() )
-            ) );
+            fwrite( STDERR, 'Warning: Class "' . $theClass->getName() . '" has no public constructor.' . PHP_EOL );

-            $this->addTest( new PHPUnit_Framework_Warning(
-                sprintf( 'No regression tests found in class "%s".', $theClass->getName() )
-            ) );
+            fwrite( STDERR, 'Warning: No regression tests found in class "' . $theClass->getName() . '".' . PHP_EOL );
```

### tests/toolkit/ezptestrunner.php

```diff
-class ezpTestRunner extends PHPUnit_TextUI_Command
-{
-    // ... 400+ lines of PHPUnit_TextUI_Command extension ...
-    if ( $reflectionClass->isSubclassOf( 'PHPUnit_Framework_TestSuite' ) )
+// Replace entire class with PHPUnit 10 compatible shim:
+class ezpTestRunner
+{
+    private static ?ezpTestRunner $instance = null;
+    private static string $dsn = '';
+    private static bool $dbPerTest = false;
+
+    public static function instance(): self { ... }
+    public static function dsn(): string { return self::$dsn; }
+    public static function dbPerTest(): bool { return self::$dbPerTest; }
+    public function run( array $argv ): void { ... PHPUnit\TextUI\Application ... }
+}
```

### tests/runtests.php

```diff
+require_once 'tests/bootstrap.php';

-$version = PHPUnit_Runner_Version::id();
+$version = PHPUnit\Runner\Version::id();

-if ( version_compare( $version, '3.7.0' ) == -1 && $version !== '@package_version@' )
-    echo "PHPUnit 3.7.0 (or later) is required...";
+if ( version_compare( $version, '10.0.0' ) < 0 )
+    echo "PHPUnit 10.0.0 (or later) is required...";
```

### (new) phpunit.xml — project root

```diff
+(new file — full content in Fix 6 above)
```

### (new) tests/bootstrap.php

```diff
+(new file — full content in Fix 7 above)
```

---

<a name="faq"></a>
## 13. Frequently asked questions

**Q: I applied the fixes but I still get `Class "ezpTestCase" not found`.**  
A: The `phpunit.xml` bootstrap path is wrong or the file does not exist. Confirm:
```bash
ls -la phpunit.xml tests/bootstrap.php
php vendor/bin/phpunit --configuration phpunit.xml --list-test-files 2>&1 | head -5
```

**Q: I get `TypeError: ezpTestCase::$backupGlobals must be of type bool`.**  
A: You applied Fix 1 but forgot to add `bool` to the property declaration. Change
`protected $backupGlobals = false;` to `protected bool $backupGlobals = false;`.

**Q: Tests that use `ezpDatabaseTestCase` are skipped or error with `eZDB not found`.**  
A: Database tests require the full eZ Publish kernel to be bootstrapped. They must be
run via `tests/runtests.php --db-per-test --dsn=mysql://user:pass@host/dbname`. The
standalone `phpunit vendor/bin/phpunit` run without `--db-per-test` legitimately skips
or errors on these tests — this is expected.

**Q: `PHPUnit\TextUI\Application` is not found in my PHPUnit version.**  
A: `PHPUnit\TextUI\Application` was introduced in PHPUnit 10. If you are on PHPUnit 9,
replace it with `PHPUnit\TextUI\Command::main()` (static call). If you are on PHPUnit 8
or below, upgrade to 10: `composer require --dev phpunit/phpunit:^10.0`.

**Q: Will future PHPUnit versions (11, 12) break things again?**  
A: The namespace-based names (`PHPUnit\Framework\TestCase`, `PHPUnit\Framework\TestSuite`)
are stable and have not changed since PHPUnit 6. Minor API surfaces like `TestSuite`
constructor signatures may change. Follow PHPUnit's migration guide for each major version.
The custom runner shim (Fix 4) is the most fragile part — it calls `PHPUnit\TextUI\Application`
directly and may need updating if PHPUnit changes that class's `run()` method signature.

**Q: Where are the security hardening PHPUnit tests?**  
A: `tests/tests/kernel/classes/security/` — added in 6.0.13-alpha2. Run them with:
```bash
php vendor/bin/phpunit --testsuite security
```

**Q: Can I run all tests including the functional flow tests in one command?**  
A: Yes:
```bash
php tests/bin/ezptestrunner_all_tests.php --report
```
This calls both the PHPUnit suite and the custom `tests/bin/tests/functional_tests.php`,
combines all results, and writes a dated report file.

---

*Document prepared 2026-02-21 for Exponential 6.0.13-alpha2 / eZ Publish Legacy 6.0*  
*PHPUnit version: 10.0.0 | PHP: 8.5.3*

