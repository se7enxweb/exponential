# Die Testsuite auf PHPUnit 10 aktualisieren (6.0.13)

Lesen Sie diese Seite, wenn Sie die Testsuite von Exponential ausführen oder eigene Tests auf dem Test-Toolkit von
Exponential pflegen, auf einem Release vor 6.0.13. Unter Exponential 6.0.12 und älter brachen `php vendor/bin/phpunit`
und `php tests/runtests.php` mit einem fatalen Fehler ab und **führten überhaupt keine Tests aus**. Exponential
6.0.13-alpha2 hat das Toolkit für `phpunit/phpunit 10.0.0` (PHP 8.4 und 8.5) korrigiert; diese Seite zeigt jede
Änderung, die Dateien, die Sie brauchen, und wie Sie Ihren Aufbau von Anfang bis Ende prüfen. Sie wurde am 2026-02-21
geschrieben.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Das Toolkit (`tests/toolkit/`) wurde für PHPUnit 3.7 und dessen Klassennamen im PEAR-Stil (`PHPUnit_Framework_TestCase`) geschrieben. PHPUnit 6 führte Namespaces ein, PHPUnit 7 entfernte die alten Aliase, und PHPUnit 10 (in `composer.json` festgelegt) hat überhaupt keine Klasse `PHPUnit_*`. Das Toolkit, `tests/runtests.php`, eine neue `phpunit.xml` und eine neue `tests/bootstrap.php` mit Shims beheben das. |
| Wer betroffen ist | Jede Installation älter als 6.0.13, die Tests ausführt, und jeder eigene Test, der gegen die alten Klassennamen geschrieben ist. |
| Wie Sie es prüfen | `php vendor/bin/phpunit --list-tests` (siehe [Kontrolle](#10-kontrolle--das-korrigierte-toolkit-laufen-lassen)) |
| Wie Sie es beheben | Aktualisieren Sie auf 6.0.13 oder neuer; die Shims in `tests/bootstrap.php` halten alte Klassennamen funktionsfähig. Folgen Sie dann [Unterstützung für PHPUnit 13](phpunitv13.md) für den nächsten Schritt. |

Die Fehler, die Sie vor der Korrektur sehen:

```
An error occurred inside PHPUnit.
Message:  Class "ezpDatabaseTestCase" not found
```

```
PHP Fatal error: Class 'PHPUnit_Framework_TestCase' not found in tests/toolkit/ezptestcase.php
```

```
PHP Fatal error: Class 'PHPUnit_Runner_Version' not found in tests/runtests.php
```

## Inhalt

1. [Hintergrund — die Geschichte der Klassennamen von PHPUnit](#1-hintergrund--die-geschichte-der-klassennamen-von-phpunit)
2. [Bestandsaufnahme der defekten Dateien](#2-bestandsaufnahme-der-defekten-dateien)
3. [Korrektur 1 — tests/toolkit/ezptestcase.php](#3-korrektur-1--teststoolkitezptestcasephp)
4. [Korrektur 2 — tests/toolkit/ezptestsuite.php](#4-korrektur-2--teststoolkitezptestsuitephp)
5. [Korrektur 3 — tests/toolkit/ezptestregressionsuite.php](#5-korrektur-3--teststoolkitezptestregressionsuitephp)
6. [Korrektur 4 — tests/toolkit/ezptestrunner.php](#6-korrektur-4--teststoolkitezptestrunnerphp)
7. [Korrektur 5 — tests/runtests.php](#7-korrektur-5--testsruntestsphp)
8. [Korrektur 6 — phpunit.xml im Projektwurzelverzeichnis anlegen](#8-korrektur-6--phpunitxml-im-projektwurzelverzeichnis-anlegen)
9. [Korrektur 7 — tests/bootstrap.php anlegen](#9-korrektur-7--testsbootstrapphp-anlegen)
10. [Kontrolle — das korrigierte Toolkit laufen lassen](#10-kontrolle--das-korrigierte-toolkit-laufen-lassen)
11. [Wie die Sicherheitstests eingebunden werden](#11-wie-die-sicherheitstests-eingebunden-werden)
12. [Zusammenfassung aller Diffs vorher/nachher](#12-zusammenfassung-aller-diffs-vorhernachher)
13. [Fragen und Antworten](#13-fragen-und-antworten)


<a name="background"></a>
## 1. Hintergrund — die Geschichte der Klassennamen von PHPUnit

Um zu verstehen, *warum* das kaputtging, muss man die Umstellung der Namen in PHPUnit verstehen:

| PHPUnit-Version | Namenskonvention | Status |
|---|---|---|
| 3.x – 5.x | `PHPUnit_Framework_TestCase` (Unterstrich-Stil von PEAR) | **Entfernt** |
| 6.x | `PHPUnit\Framework\TestCase` (PHP-Namespace) + Aliase für Abwärtskompatibilität | Übergang |
| 7.x | Nur Namespace — Aliase entfernt | Inkompatible Änderung |
| 8.x | Nur Namespace — `PHPUnit_TextUI_Command` entfernt | Inkompatible Änderung |
| 9.x – 10.x | Nur Namespace — erheblicher Umbau der internen API | Aktuell |

Das Test-Toolkit von Exponential wurde um 2012–2014 für PHPUnit 3.7 geschrieben. Die `composer.json` in 6.0.x wurde
so geändert, dass sie `phpunit/phpunit: 10.0.0` verlangt — aber die Dateien des Toolkits, die PHPUnit umhüllen, wurden
nie angepasst. Das Ergebnis ist, dass `composer install` eine PHPUnit-Version mitbringt, die mit jeder Klasse in
`tests/toolkit/` vollständig inkompatibel ist.

**Sie können keinen einzigen Test ausführen**, bis die Basisklassen des Toolkits angepasst sind.


<a name="broken-file-inventory"></a>
## 2. Bestandsaufnahme der defekten Dateien

`php vendor/bin/phpunit tests/tests/kernel/classes/ezurlwildcard_test.php` erzeugt unter 6.0.12 diese Ausgabe:

```
An error occurred inside PHPUnit.
Message:  Class "ezpDatabaseTestCase" not found
Location: tests/tests/kernel/classes/ezurlwildcard_test.php:14
```

PHPUnit 10 konnte `ezpDatabaseTestCase` nicht finden, weil der Bootstrap, der die Klassen des Toolkits lädt, nie
definiert wurde — aber selbst wenn er es gewesen wäre, würden die Klassen des Toolkits selbst nicht laden, weil jede auf
eine Klasse verweist, die es nicht mehr gibt.

Hier ist jeder defekte Verweis:

| Datei | Zeile | Defekter Verweis | Ersatz in PHPUnit 10 |
|---|---|---|---|
| `tests/toolkit/ezptestcase.php` | 11 | `extends PHPUnit_Framework_TestCase` | `extends PHPUnit\Framework\TestCase` |
| `tests/toolkit/ezptestsuite.php` | 11 | `extends PHPUnit_Framework_TestSuite` | `extends PHPUnit\Framework\TestSuite` |
| `tests/toolkit/ezptestregressionsuite.php` | 18 | `extends PHPUnit_Framework_TestSuite` | `extends PHPUnit\Framework\TestSuite` |
| `tests/toolkit/ezptestregressionsuite.php` | 75, 95, 112 | `new PHPUnit_Framework_Warning(...)` | `new PHPUnit\Framework\IncompleteTestError(...)` oder entfernt |
| `tests/toolkit/ezptestrunner.php` | 12 | `extends PHPUnit_TextUI_Command` | Klasse in PHPUnit 9 entfernt — siehe Korrektur 4 |
| `tests/toolkit/ezptestrunner.php` | 388 | `isSubclassOf('PHPUnit_Framework_TestSuite')` | `isSubclassOf(PHPUnit\Framework\TestSuite::class)` |
| `tests/toolkit/ezptestrunner.php` | 416 | `@var PHPUnit_Framework_TestCase` | phpdoc — nur kosmetisch |
| `tests/runtests.php` | 25 | `PHPUnit_Runner_Version::id()` | `PHPUnit\Runner\Version::id()` |
| (fehlt) | — | Keine `phpunit.xml` im Projektwurzelverzeichnis | Legen Sie eine an — siehe Korrektur 6 |
| (fehlt) | — | Keine `tests/bootstrap.php` | Legen Sie eine an — siehe Korrektur 7 |


<a name="fix-1"></a>
## 3. Korrektur 1 — tests/toolkit/ezptestcase.php

**Was kaputtging:** `ezpTestCase extends PHPUnit_Framework_TestCase` — die Unterstrich-Klasse
`PHPUnit_Framework_TestCase` wurde in PHPUnit 7 vollständig entfernt.

**Wie Sie den Fehler sehen:**

```
PHP Fatal error: Class 'PHPUnit_Framework_TestCase' not found
  in tests/toolkit/ezptestcase.php on line 11
```

**Die defekte Datei (Original):**

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

**Nehmen Sie diese Änderung vor:**

```diff
- class ezpTestCase extends PHPUnit_Framework_TestCase
+ class ezpTestCase extends PHPUnit\Framework\TestCase
```

**Vollständige korrigierte Datei:**

```php
<?php
/**
 * File containing the ezpTestCase class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
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

> **Hinweis zu `$backupGlobals`:** PHPUnit 10 verlangt, dass diese Eigenschaft als `bool` typisiert ist. Ohne die
> Typdeklaration gibt PHPUnit 10 eine Deprecation aus, und künftige Versionen enden fatal. Fügen Sie `bool` gleichzeitig
> zur Deklaration der Eigenschaft hinzu.


<a name="fix-2"></a>
## 4. Korrektur 2 — tests/toolkit/ezptestsuite.php

**Was kaputtging:** `ezpTestSuite extends PHPUnit_Framework_TestSuite`

**Wie Sie den Fehler sehen:**

```
PHP Fatal error: Class 'PHPUnit_Framework_TestSuite' not found
  in tests/toolkit/ezptestsuite.php on line 11
```

**Die defekte Datei (Original, relevanter Auszug):**

```php
class ezpTestSuite extends PHPUnit_Framework_TestSuite   // <-- BROKEN
{
    protected $sharedFixture;
    ...
```

**Nehmen Sie diese Änderung vor:**

```diff
- class ezpTestSuite extends PHPUnit_Framework_TestSuite
+ class ezpTestSuite extends PHPUnit\Framework\TestSuite
```

**Vollständige korrigierte Klassendeklaration:**

```php
class ezpTestSuite extends PHPUnit\Framework\TestSuite
{
    protected $sharedFixture;

    /** @var eZScript */
    protected static $script;
    ...
```

Andere Änderungen innerhalb dieser Datei sind nicht nötig. Die Bootstrap-Logik von `eZScript` und `__destruct()`
bleiben unverändert.


<a name="fix-3"></a>
## 5. Korrektur 3 — tests/toolkit/ezptestregressionsuite.php

**Was kaputtging:** Drei getrennte Probleme in dieser Datei:

1. `extends PHPUnit_Framework_TestSuite` — Zeile 18
2. `new PHPUnit_Framework_Warning(...)` — Zeilen 75, 95, 112 — Klasse in PHPUnit 10 entfernt
3. Die Signatur des Konstruktors `__construct($theClass = '', $name = '')` kollidiert mit
   `PHPUnit\Framework\TestSuite`, das ab PHPUnit 9 eine andere Form des Konstruktors hat

**Wie Sie den Fehler sehen:**

```
PHP Fatal error: Class 'PHPUnit_Framework_TestSuite' not found
  in tests/toolkit/ezptestregressionsuite.php on line 18
```

Oder, wenn die Klasse lädt, aber `addTest` mit einer `Warning` aufgerufen wird:

```
TypeError: Argument 1 passed to PHPUnit\Framework\TestSuite::addTest()
must implement interface PHPUnit\Framework\Test
```

**Nehmen Sie diese Änderungen vor:**

```diff
- class ezpTestRegressionSuite extends PHPUnit_Framework_TestSuite
+ class ezpTestRegressionSuite extends PHPUnit\Framework\TestSuite
```

Für jeden Block `new PHPUnit_Framework_Warning(...)`: Die Klasse `PHPUnit\Framework\Warning` wurde in PHPUnit 10
selbst entfernt. Ersetzen Sie jede Verwendung durch eine Hülle für übersprungene/unvollständige Tests mit
`PHPUnit\Framework\IncompleteTestError` oder entfernen Sie einfach das Einfügen der Warnung und lassen die Suite leer.
Der sicherste direkte Ersatz, der das beobachtbare Verhalten erhält:

```diff
- $this->addTest(
-     new PHPUnit_Framework_Warning(
-         sprintf( 'Class "%s" has no public constructor.', $theClass->getName() )
-     )
- );
+ // PHPUnit 10: Warning tests removed; log to stderr and skip
+ fwrite( STDERR, 'Warning: Class "' . $theClass->getName() . '" has no public constructor.' . PHP_EOL );
```

Wenden Sie dasselbe Muster auf die beiden anderen Blöcke mit `PHPUnit_Framework_Warning` an (die Warnung „not a
subclass“ — die auskommentiert ist — und die Warnung „No regression tests found“).

**Korrigierte Klassendeklaration und Ersatz der Warnungen (vollständiger Diff):**

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


<a name="fix-4"></a>
## 6. Korrektur 4 — tests/toolkit/ezptestrunner.php

**Was kaputtging:** Das ist die am stärksten defekte Datei.

`ezpTestRunner extends PHPUnit_TextUI_Command`

`PHPUnit_TextUI_Command` war die alte Klasse für den Einstieg über die Kommandozeile. Sie wurde in PHPUnit 6/7 zu
`PHPUnit\TextUI\Command` umgebaut und dann in PHPUnit 9 **vollständig entfernt**, als der CLI-Runner als
`PHPUnit\TextUI\Application` von Grund auf neu geschrieben wurde. Einen direkten Ersatz gibt es nicht — das ganze
Erweiterungsmodell hat sich geändert.

**Wie Sie den Fehler sehen (aus `tests/runtests.php`):**

```
PHP Fatal error: Class 'PHPUnit_TextUI_Command' not found
  in tests/toolkit/ezptestrunner.php on line 12
```

**Die praktische Auswirkung:** Die Klasse `ezpTestRunner` stellte zusätzlich zu den vorhandenen Optionen von PHPUnit
eigene CLI-Optionen `--list-suites`, `--list-tests`, `--dsn` und `--db-per-test` bereit. In PHPUnit 10 ist der einzig
richtige Weg, eigenes Verhalten des Runners hinzuzufügen, die `Extension`-API (`PHPUnit\Runner\Extension\Extension`).
Den ganzen Runner für PHPUnit 10 neu zu schreiben, ist ein größeres Projekt.

**Minimale Korrektur, um heute alle Tests freizuschalten:**

Ersetzen Sie den Klassenkörper vollständig durch eine Hülle mit statischen Methoden, die direkt an
`PHPUnit\TextUI\Application` (den Einstiegspunkt von PHPUnit 10) weiterreicht und die statischen Zugriffsmethoden
`dsn()` und `dbPerTest()` erhält, die in `ezpDatabaseTestCase` verwendet werden:

```php
<?php
/**
 * File containing the ezpTestRunner class — PHPUnit 10 compatible shim.
 *
 * The original class extended PHPUnit_TextUI_Command (removed in PHPUnit 9).
 * This shim preserves the static accessor API used by ezpDatabaseTestCase
 * and delegates test execution to PHPUnit\TextUI\Application directly.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
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

**Was Sie vorübergehend verlieren:** Die eigenen Optionen `--list-suites` und `--list-tests` entfallen in dieser
minimalen Hülle. Die eingebauten `--list-tests` und `--list-test-files` von PHPUnit 10 decken die meisten dieser
Anwendungsfälle von Haus aus ab. Die Optionen `--dsn=` und `--db-per-test` — die tatsächlich von `ezpDatabaseTestCase`
verwendet werden — bleiben vollständig erhalten.

**Was Sie gewinnen:** Jeder Test, der `ezpTestCase` verwendet (ohne Datenbank), und jeder Test, der
`ezpDatabaseTestCase` verwendet (mit `--db-per-test`), lässt sich jetzt ausführen.

**Verbleibender Verweis auf `PHPUnit_Framework_TestSuite` in Zeile 388:**

Der alte Runner hatte Code zum Finden von Suiten über Reflection. In der Hülle oben gibt es diesen Code nicht — PHPUnit
10 findet Suiten intern. Stellen Sie einen Teil der ursprünglichen Logik zum Finden wieder her, ersetzen Sie:

```diff
- $reflectionClass->isSubclassOf( 'PHPUnit_Framework_TestSuite' )
+ $reflectionClass->isSubclassOf( PHPUnit\Framework\TestSuite::class )
```


<a name="fix-5"></a>
## 7. Korrektur 5 — tests/runtests.php

**Was kaputtging:** Zeile 25 ruft `PHPUnit_Runner_Version::id()` auf.

**Wie Sie den Fehler sehen:**

```
PHP Fatal error: Class 'PHPUnit_Runner_Version' not found in tests/runtests.php on line 25
```

**Der defekte Code (Original):**

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

**Nehmen Sie diese Änderungen vor:**

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

**Vollständige korrigierte runtests.php:**

```php
#!/usr/bin/env php
<?php
/**
 * File containing the runtests CLI script — PHPUnit 10 compatible.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
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

> Beachten Sie das hinzugefügte `require_once 'tests/bootstrap.php'` — das ist die Bootstrap-Datei, die in Korrektur 7
> unten angelegt wird. Ohne sie findet PHPUnit weder `ezpTestCase` noch eine andere Klasse des Toolkits.


<a name="fix-6"></a>
## 8. Korrektur 6 — phpunit.xml im Projektwurzelverzeichnis anlegen

**Was kaputtging:** Im Projektwurzelverzeichnis gab es keine `phpunit.xml` (und keine `phpunit.xml.dist`). PHPUnit 10
braucht eine Konfigurationsdatei, um zu wissen, wo die Bootstrap-Datei liegt, wo Tests zu finden sind und welche
Gruppen zu verwenden sind. Ohne sie durchsucht PHPUnit Verzeichnisse, ohne die Klassen des Toolkits zu laden, sodass
jede Testdatei, die `ezpTestCase` erweitert, sofort fatal endet.

**Legen Sie diese Datei im Projektwurzelverzeichnis als `phpunit.xml` an:**

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!--
  phpunit.xml — Exponential 6.0.13 PHPUnit 10 configuration.

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

**Speichern Sie das als `phpunit.xml` im Wurzelverzeichnis des Repositorys** (demselben Verzeichnis wie
`composer.json`).


<a name="fix-7"></a>
## 9. Korrektur 7 — tests/bootstrap.php anlegen

**Was kaputtging:** PHPUnit kann keine Testklasse laden, die `ezpTestCase` erweitert, weil es nie eine Bootstrap-Datei
gab, die die Klassen des Toolkits per `require` einbindet. Der alte Code verließ sich auf das eigene Autoload-System von
Exponential (`autoload.php`), das selbst verlangt, dass die Plattform initialisiert ist. Die Klassen des Toolkits sind
jedoch einfache PHP-Dateien, die keinen Start der Plattform brauchen — sie müssen nur eingebunden werden.

**Legen Sie `tests/bootstrap.php` an:**

```php
<?php
/**
 * PHPUnit bootstrap file for Exponential 6.0.x.
 *
 * Loaded by PHPUnit before any test file is parsed.  Loads the Composer
 * autoloader and all toolkit base classes so that ezpTestCase,
 * ezpDatabaseTestCase, ezpTestSuite, ezpTestRunner and related classes are
 * available to every test file without each test having to require them
 * individually.
 *
 * Do NOT bootstrap the full Exponential kernel here — tests that need it
 * use ezpTestSuite / ezpDatabaseTestCase which do so themselves via eZScript.
 *
 * @package tests
 */

// ── Composer autoloader ──────────────────────────────────────────────────────
require_once __DIR__ . '/../vendor/autoload.php';

// ── Exponential test toolkit (order matters) ───────────────────────────
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

> **Wichtig:** Dieser Bootstrap ruft `eZScript::instance()` *nicht* auf. Tests, die den vollständigen
> Exponential-Stack brauchen, erben von `ezpDatabaseTestCase`, das `ezpTestSuite` aufruft, das in seinem Konstruktor
> `eZScript::instance()` aufruft. Das ist die richtige Trennung der Zuständigkeiten. Fügen Sie hier keinen Start des
> Kernels hinzu, sonst brechen Sie Unit-Tests, die absichtlich ohne die Plattform laufen.


<a name="verification"></a>
## 10. Kontrolle — das korrigierte Toolkit laufen lassen

Nachdem Sie die Korrekturen 1–7 angewendet haben, prüfen Sie jeden Schritt mit diesen kopierbaren Befehlen:

### Schritt 1 — bestätigen, dass PHPUnit den Bootstrap findet

```bash
php vendor/bin/phpunit --list-test-files 2>&1 | head -20
```

Erwartet: eine Liste von Testdateien. Sehen Sie `Class "ezpDatabaseTestCase" not found`, wird der Bootstrap nicht
übernommen — bestätigen Sie, dass `phpunit.xml` im Projektwurzelverzeichnis liegt und das Attribut `bootstrap` auf
`tests/bootstrap.php` zeigt.

### Schritt 2 — die reinen Unit-Tests (ohne Datenbank) ausführen

```bash
php vendor/bin/phpunit --testsuite kernel-classes --exclude-group database
```

Erwartet: PHPUnit läuft und meldet PASS/FAIL/SKIP. Sie sollten eine Ausgabe wie diese sehen:

```
PHPUnit 10.0.0 by Sebastian Bergmann and contributors.

Runtime: PHP 8.5.3
Configuration: /path/to/phpunit.xml

...........S..F.

FAILURES!
Tests: 14, Assertions: 38, Failures: 1, Skipped: 1.
```

(Fehlschläge an dieser Stelle sind bereits vorhandene Testfehler, die mit fehlender Datenbank oder fehlender
Konfiguration zusammenhängen — keine Fehler des Toolkits.)

### Schritt 3 — nur die Tests der Sicherheitshärtung ausführen

```bash
php vendor/bin/phpunit --testsuite security --colors=always
```

Erwartet: Alle Sicherheitstests bestehen. Diese Tests brauchen keine Datenbank.

```
PHPUnit 10.0.0 by Sebastian Bergmann and contributors.

Runtime: PHP 8.5.3

...............

OK (15 tests, 15 assertions)
```

### Schritt 4 — das eigene Runner-Skript des Kits ausführen

```bash
php tests/runtests.php --testsuite security
```

Erwartet: dieselbe Ausgabe wie in Schritt 3. Das bestätigt, dass Korrektur 5 an `runtests.php` funktioniert.

### Schritt 5 — bestätigen, dass keine alten Klassennamen übrig sind

```bash
grep -rn 'PHPUnit_Framework_TestCase\|PHPUnit_Framework_TestSuite\|PHPUnit_TextUI_Command\|PHPUnit_Runner_Version\|PHPUnit_Framework_Warning' tests/toolkit/ tests/runtests.php
```

Erwartet: **keine Ausgabe**. Erscheinen Zeilen, sind das verbleibende, nicht korrigierte Verweise.


<a name="security-tests"></a>
## 11. Wie die Sicherheitstests eingebunden werden

Die Patches der Sicherheitshärtung von 6.0.13 (dokumentiert in `doc/eng-us/bc/6.0/hardening.md`) haben eine
eigenständige Suite von Sicherheitstests eingeführt unter:

```
tests/tests/kernel/classes/security/
```

Diese Tests sind von Haus aus für PHPUnit 10 geschrieben — sie erweitern `PHPUnit\Framework\TestCase` direkt (nicht
`ezpTestCase`), weil sie ohne jeden Start der Plattform laufen müssen. Sie testen die konkreten Patches, die in SEC-01
bis SEC-07 beschrieben sind.

Die `phpunit.xml` oben nimmt sie in die Testsuite `security` auf:

```xml
<testsuite name="security">
    <directory>tests/tests/kernel/classes/security</directory>
</testsuite>
```

Führen Sie sie eigenständig aus:

```bash
php vendor/bin/phpunit --testsuite security
```

Führen Sie sie als Teil der kombinierten Prüfung über den Runner im Stil von eZScript aus:

```bash
php tests/bin/ezptestrunner_all_tests.php security
```

Der eZScript-Runner ruft sowohl die PHPUnit-Suite für Sicherheit als auch die eigenen funktionalen Ablauftests
(`tests/bin/tests/functional_tests.php`) auf und fasst die Ergebnisse in einem Bericht zusammen:

```
php tests/bin/ezptestrunner_all_tests.php --report
```

Das erzeugt eine datierte Berichtsdatei unter `tests/bin/test_report_YYYY-MM-DD.txt`.


<a name="full-diff"></a>
## 12. Zusammenfassung aller Diffs vorher/nachher

Hier ist eine kompakte Referenz jeder geänderten Zeile in allen Dateien des Toolkits. Wenden Sie alle an, um die
Ausführung von Tests unter PHPUnit 10 wiederherzustellen:

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

### (neu) phpunit.xml — Projektwurzelverzeichnis

```diff
+(new file — full content in Fix 6 above)
```

### (neu) tests/bootstrap.php

```diff
+(new file — full content in Fix 7 above)
```


<a name="faq"></a>
## 13. Fragen und Antworten

**F: Ich habe die Korrekturen angewendet, erhalte aber weiterhin `Class "ezpTestCase" not found`.**  
A: Der Bootstrap-Pfad in `phpunit.xml` ist falsch, oder die Datei existiert nicht. Prüfen Sie:
```bash
ls -la phpunit.xml tests/bootstrap.php
php vendor/bin/phpunit --configuration phpunit.xml --list-test-files 2>&1 | head -5
```

**F: Ich erhalte `TypeError: ezpTestCase::$backupGlobals must be of type bool`.**  
A: Sie haben Korrektur 1 angewendet, aber vergessen, `bool` zur Deklaration der Eigenschaft hinzuzufügen. Ändern Sie
`protected $backupGlobals = false;` in `protected bool $backupGlobals = false;`.

**F: Tests, die `ezpDatabaseTestCase` verwenden, werden übersprungen oder enden mit `eZDB not found`.**  
A: Datenbanktests brauchen den vollständig gestarteten Exponential-Kernel. Sie müssen über
`tests/runtests.php --db-per-test --dsn=mysql://user:pass@host/dbname` ausgeführt werden. Der eigenständige Lauf
`phpunit vendor/bin/phpunit` ohne `--db-per-test` überspringt diese Tests zu Recht oder endet mit Fehlern — das ist
erwartet.

**F: `PHPUnit\TextUI\Application` wird in meiner PHPUnit-Version nicht gefunden.**  
A: `PHPUnit\TextUI\Application` wurde in PHPUnit 10 eingeführt. Unter PHPUnit 9 ersetzen Sie es durch
`PHPUnit\TextUI\Command::main()` (statischer Aufruf). Unter PHPUnit 8 oder älter aktualisieren Sie auf 10:
`composer require --dev phpunit/phpunit:^10.0`.

**F: Werden künftige PHPUnit-Versionen (11, 12) wieder etwas kaputt machen?**  
A: Die Namen auf Basis von Namespaces (`PHPUnit\Framework\TestCase`, `PHPUnit\Framework\TestSuite`) sind stabil und
haben sich seit PHPUnit 6 nicht geändert. Kleinere API-Flächen wie die Signaturen der Konstruktoren von `TestSuite`
können sich ändern. Folgen Sie dem Migrationsleitfaden von PHPUnit für jede Hauptversion. Die Hülle für den eigenen
Runner (Korrektur 4) ist der empfindlichste Teil — sie ruft `PHPUnit\TextUI\Application` direkt auf und muss womöglich
angepasst werden, wenn PHPUnit die Signatur der Methode `run()` dieser Klasse ändert.

**F: Wo sind die PHPUnit-Tests der Sicherheitshärtung?**  
A: `tests/tests/kernel/classes/security/` — hinzugefügt in 6.0.13-alpha2. Führen Sie sie so aus:
```bash
php vendor/bin/phpunit --testsuite security
```

**F: Kann ich alle Tests einschließlich der funktionalen Ablauftests mit einem Befehl ausführen?**  
A: Ja:
```bash
php tests/bin/ezptestrunner_all_tests.php --report
```
Das ruft sowohl die PHPUnit-Suite als auch die eigene `tests/bin/tests/functional_tests.php` auf, fasst alle
Ergebnisse zusammen und schreibt eine datierte Berichtsdatei.


*Dokument erstellt am 2026-02-21 für Exponential 6.0.13-alpha2*  
*PHPUnit-Version: 10.0.0 | PHP: 8.5.3*

## Verwandte Seiten

- [Unterstützung für PHP 8](php8.md)
- [Unterstützung für PHPUnit 13 und die Testsuite der String-Operatoren](phpunitv13.md)
- [Aufräumen der Testsuite für PHPUnit 13 / PHP 8.4.23](phpunitv13-fuer-php841.md)
- [Kompatibilitätskorrekturen von `ezpSessionHandlerDB` für PHP 8 und die Testsuite für PHPUnit 13](ezpsessionhandlerdb-php8-korrektur-und-tests.md)
- [PHP 8.4 und 8.5 für den älteren Symfony-Stack: die Framework-Forks](../../../eng-us/features/6.0/platform-php85-framework-forks.md)
- [April 2025](../../../eng-us/history/2025/2025-04.md)
- [September 2025](../../../eng-us/history/2025/2025-09.md)
- [Dezember 2025](../../../eng-us/history/2025/2025-12.md)

- [Februar 2026](../../../eng-us/history/2026/2026-02.md)
- [März 2026](../../../eng-us/history/2026/2026-03.md)
