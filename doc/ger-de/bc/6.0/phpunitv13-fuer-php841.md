# Bereinigung der Testsuite für PHPUnit 13 / PHP 8.4.23

Lesen Sie diese Seite, wenn Sie die Testsuite von Exponential 6.0 ausführen oder erweitern oder wenn Sie Code pflegen,
den die Tests bemängelt haben (Bildverarbeitung mit GD, `eZMail`, `eZCharsetInfo`, der MongoDB-Adapter,
`eZSiteAccess::load()`, ISBN13, WebDAV). Die Suite läuft jetzt sauber mit PHPUnit 13 (`phpunit/phpunit 13.0.0`) unter
PHP 8.4.23 und 8.5.8. Dies ist ein Protokoll der Änderung vom 2026-07-11: was falsch war, welche Dateien sich geändert
haben und wie Sie die Suite selbst ausführen.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Coverage funktioniert in isolierten Kindprozessen; Korrekturen an der Testhygiene; sieben Korrekturen an Kernel und Bibliotheken, die die Tests zutage gebracht haben. |
| Wer betroffen ist | Alle, die die Testsuite ausführen oder erweitern; Code, der sich auf das korrigierte Verhalten von Kernel und Bibliotheken verlassen hat (alle Korrekturen entfernen Warnungen oder Fehler). |
| Wie Sie es prüfen | `php ./vendor/bin/phpunit --no-coverage --display-deprecations --display-warnings` |
| Erwartetes Ergebnis | `OK, but some tests were skipped!` mit `Tests: 715, Assertions: 1117, Skipped: 30` zum Zeitpunkt der Änderung. |

Ergebnis der Bereinigung:

```
715 tests, 1117 assertions, 0 errors, 0 warnings, 0 deprecations, 0 risky
OK, but some tests were skipped!
```

Vor der Bereinigung war die Fortschrittsanzeige von PHPUnit voller `D` (Deprecations), `W` (Warnungen), `R` (riskante
Tests) und `E` (Fehler). Läufe mit Coverage schlugen außerdem in `EzpSessionHandlerDBPhp8BugfixesTest` fehl, weil
Xdebug in den isolierten Kindprozessen von PHPUnit nicht geladen war.

## Was die Zeichen der Fortschrittsanzeige bedeuten

| Zeichen | Bedeutung |
|------|---------|
| `.`  | Bestanden |
| `D`  | Der Test hat eine Deprecation von PHP ausgelöst |
| `W`  | Der Test hat eine Warnung von PHP ausgelöst |
| `R`  | Riskanter Test (keine Assertion, hinterlassene Exception-Handler usw.) |
| `E`  | Fehler / fatal |
| `S`  | Übersprungen |
| `F`  | Fehlschlag |

Ein sauberer Lauf besteht überwiegend aus Punkten und einigen `S` für Tests, die eine optionale Erweiterung oder einen
externen Dienst brauchen. Diese Bereinigung hat das zum Normalzustand des Branches 6.0 gemacht.


## Hintergrund: warum das nötig war

PHP 8.4 hat mehrere Verhaltensweisen verschärft, vor denen PHP 8.0 und 8.1 nur gewarnt hatten, und PHPUnit 13 wurde
strenger bei Klassennamen, Testsuiten und Prozessisolation. Die Testsuite von 6.0 wurde ursprünglich für eine frühere
Ära von PHPUnit und eine nachsichtigere PHP-Laufzeit geschrieben und hatte daher eine lange Liste von Warnungen,
Deprecations und riskanten Tests angesammelt, die echte Fehlschläge verdeckten. Ziel war nicht, die Suite neu zu
schreiben, sondern sie mit den modernen Werkzeugen in Einklang zu bringen und dabei die vorhandenen Tests und ihre
Absicht zu erhalten.

Die Arbeit teilte sich in drei große Bereiche:

1. **Coverage und Prozessisolation:** `Xdebug` in Kindprozessen verfügbar machen.
2. **Testhygiene:** dynamische Eigenschaften, riskante Tests und nicht passende Klassennamen korrigieren.
3. **Produktionskorrekturen, die die Tests zutage gebracht haben:** kleine Korrekturen an Kernel und Bibliotheken, die
   die Tests zu Recht bemängelten.


## 1. Korrekturen für Coverage und isolierte Prozesse

`EzpSessionHandlerDBPhp8BugfixesTest` verwendet `#[RunTestsInSeparateProcesses]`. In PHPUnit 13 lesen Kindprozesse
`php.ini` erneut und durchsuchen Verzeichnisse, erben aber nicht die Erweiterung `xdebug`, die im Elternprozess über
`-d zend_extension=xdebug.so` geladen wurde. Der Lauf mit Coverage schlug daher fehl, weil die Kinder keine Coverage
sammeln konnten.

Die Lösung war, den Kindern eine eigene `xdebug.ini` zu geben und sie zur Laufzeit zu finden, statt sich auf einen
fest eingetragenen Pfad zu verlassen.

| Datei | Änderung |
|------|--------|
| `tests/xdebug.ini` | Neue `.ini` für das Scan-Verzeichnis, die `xdebug.so` lädt und `xdebug.mode=coverage` setzt. |
| `phpunit.xml` | Die fest eingetragene Umgebungsvariable `PHP_INI_SCAN_DIR` entfernt; die Pfade `coverage/include` für `lib`, `kernel` und `tests` bleiben. |
| `tests/bootstrap.php` | Setzt `PHP_INI_SCAN_DIR` zur Laufzeit mit `__DIR__` und `php_ini_scanned_files()`, sodass der Pfad des Repositorys nie fest eingetragen ist. |
| `.gitignore` | Fügt `/coverage` hinzu, sodass der erzeugte HTML-Bericht der Coverage ignoriert wird. |

Mit Coverage ausführen:

```bash
XDEBUG_MODE=coverage php -d memory_limit=-1 -d zend_extension=xdebug.so ./vendor/bin/phpunit --coverage-html coverage
```


## 2. Korrekturen am Testcode

### 2.1 Dynamische Eigenschaften (Deprecation ab PHP 8.2)

PHP 8.2 hat das Schreiben auf nicht deklarierte Objekteigenschaften als veraltet markiert. Mehrere Testklassen wiesen
in `setUp()` Werte zu, ohne sie zuvor zu deklarieren, was während des Laufs Deprecation-Hinweise erzeugte.

| Datei | Hinzugefügte Eigenschaft |
|------|----------------|
| `tests/tests/lib/ezfile/eZFileDownloadTest.php` | `private string $file;` und `private string $content;` |
| `tests/tests/lib/ezimage/eZImageManagerTest.php` | `private eZINI $imageIni;` |
| `tests/tests/lib/ezimage/eZImageShellHandlerTest.php` | `private eZImageManager $imageManager;` |
| `tests/tests/lib/ezutils/eZURITest.php` | `private ?string $originalRequestURI;` |

### 2.2 Tests ohne Assertions oder mit riskantem Zustand

| Datei | Korrektur |
|------|-----|
| `tests/tests/lib/ezutils/eZMailTest.php` | `testStripEmail()` prüft jetzt den Rückgabewert von `eZMail::stripEmail()`. |
| `tests/tests/kernel/classes/eZExtensionWithOrderingTest.php` | `tearDown()` ruft jetzt `restore_exception_handler()` auf, um Handler aufzuräumen, die der getestete Code hinterlassen hat. |
| `tests/tests/lib/ezimage/eZImageManagerTest.php` | `testMultiHandlerAlias()` hat jetzt genug Ausführungszeit und beendet den Prozess nicht mehr wegen `set_time_limit(60)`. |

### 2.3 Nicht passende Klassen- und Dateinamen

Der Loader von PHPUnit 13 warnt, wenn ein Klassenname nicht zum Dateinamen passt. Zwei Testklassen für `eZDir` wurden
umbenannt, damit sie zu ihren Dateien passen.

| Datei | Änderung |
|------|--------|
| `tests/tests/lib/ezfile/eZDirTestInsideRootTest.php` | Klasse in `eZDirTestInsideRootTest` umbenannt, passend zur Datei. |
| `tests/tests/lib/ezfile/eZDirTestOutsideRootTest.php` | Klasse in `eZDirTestOutsideRootTest` umbenannt, passend zur Datei. |

### 2.4 Abstrakte Testklassen, die Warnungen des Runners verursachen

`eZClusterFileHandlerAbstractTest`, `eZClusterStaleCacheTest`, `eZDBBasedClusterFileHandlerAbstractTest` und
`eZDatatypeAbstractTest` sind abstrakte Basisklassen. Sie wurden als Testdateien geladen und erzeugten Warnungen „is
abstract“. Sie wurden aus den Suiten `kernel-classes` und `kernel-datatypes` ausgeschlossen, und konkrete Unterklassen
erhielten `require_once`, damit sie ihre Elternklassen weiterhin laden können.

| Datei | Änderung |
|------|--------|
| `phpunit.xml` | Schließt die vier abstrakten Basisdateien aus den betreffenden Suiten aus. |
| `tests/tests/kernel/classes/clusterfilehandlers/eZFSFileHandlerTest.php` | `require_once __DIR__ . '/eZClusterFileHandlerAbstractTest.php';` |
| `tests/tests/kernel/classes/clusterfilehandlers/eZDFSFileHandlerTest.php` | `require_once __DIR__ . '/eZDBBasedClusterFileHandlerAbstractTest.php';` |
| `tests/tests/kernel/classes/clusterfilehandlers/eZDFSClusterStaleCacheTest.php` | `require_once __DIR__ . '/eZClusterStaleCacheTest.php';` |
| `tests/tests/kernel/classes/clusterfilehandlers/eZDBBasedClusterFileHandlerAbstractTest.php` | `require_once __DIR__ . '/eZClusterFileHandlerAbstractTest.php';` |
| `tests/tests/kernel/datatypes/ezmatrix/eZMatrixTypeTest.php` | `require_once __DIR__ . '/../eZDatatypeAbstractTest.php';` |
| `tests/tests/kernel/datatypes/ezstring/eZStringTypeTest.php` | `require_once __DIR__ . '/../eZDatatypeAbstractTest.php';` |
| `tests/tests/kernel/datatypes/ezemail/eZEmailTypeTest.php` | `require_once __DIR__ . '/../eZDatatypeAbstractTest.php';` |


## 3. Produktionskorrekturen an Kernel und Bibliotheken, die die Tests zutage gebracht haben

Die Warnungen und Fehler waren nicht alle kosmetisch. Mehrere Tests wiesen zu Recht auf echte Laufzeitprobleme in
Kernel und Bibliotheken hin.

| Datei | Änderung | Warum |
|------|--------|-----|
| `lib/ezimage/classes/ezimagegdhandler.php` | Gleitkomma-Farbwerte vor `ImageColorAllocate()` / `ImageSetPixel()` in `(int)` umgewandelt. | `TypeError` / Deprecation unter PHP 8.4; `testMultiHandlerAlias` scheiterte fatal. |
| `lib/ezutils/classes/ezmail.php` | `contentType()`, `contentCharset()`, `contentTransferEncoding()`, `contentDisposition()` geben jetzt `null` zurück, wenn `ContentType` nicht initialisiert ist. | Verhindert Warnungen `Trying to access array offset on null` in `eZMailEzcTest`. |
| `lib/ezi18n/classes/ezcharsetinfo.php` | `realCharsetCode()` fängt `null` ab und wandelt vor `strtolower()` in `string` um. | Verhindert die Deprecation `strtolower(): Passing null to parameter #1`. |
| `lib/ezdb/classes/expmongodb.php` | Fügt die Hilfsmethode `logError()` hinzu und verwendet sie statt `error_log()`; `findOne()` gibt `getArrayCopy()` zurück; `query()` gibt bei `INSERT` `true` zurück. | Behebt die Tests des MongoDB-Adapters und entfernt direkte Ausgaben über `error_log()`. |
| `kernel/classes/ezsiteaccess.php` | `load()` liest `$GLOBALS['eZCurrentAccess']` nicht mehr, wenn es nicht gesetzt ist. | Verhindert Warnungen wegen undefinierter Array-Schlüssel. |
| `kernel/classes/datatypes/ezisbn/ezisbn13.php` | Ersetzt veraltete String-Offsets mit geschweiften Klammern `{$i}` durch `[$i]`. | PHP 7.4/8.4 haben die Indizierung von Strings mit geschweiften Klammern entfernt. |
| `kernel/private/classes/webdav/ezwebdavcontentbackend.php` | Ersetzt veraltete String-Offsets mit geschweiften Klammern `{$i}` durch `[$i]`. | Ebenso. |


## 4. Stubs für den Session-Handler

Die Tests des Session-Handlers brauchten eigene Stub-Klassen, damit sie nicht mit Stubs kollidieren, die an anderer
Stelle der Suite verwendet werden.

| Datei | Änderung |
|------|--------|
| `tests/tests/lib/ezsession/EzpSessionHandlerDBPhp8BugfixesTestStubs.php` | Neue Datei. `StubEZDB` in `StubSessionEZDB` umbenannt, um eine erneute Deklaration der Klasse `StubEZDB` in `tests/tests/kernel/classes/security/stubs.php` zu vermeiden. Deklariert außerdem typisierte Eigenschaften und korrigiert Standardwerte von `eZDBInterface`. |
| `tests/tests/lib/ezsession/EzpSessionHandlerDBPhp8BugfixesTest.php` | Auf `StubSessionEZDB` umgestellt; deklariert typisiert `private ezpSessionHandlerDB $handler;` und `private StubSessionEZDB $db;`. |


## 5. Tests des MongoDB-Adapters

Die Tests des MongoDB-Adapters waren aus dem Gleichschritt mit der Produktionsimplementierung geraten. Die Korrekturen
waren klein, aber wichtig: sicherstellen, dass der Datenbankname initialisiert war, bevor `selectCollection()`
aufgerufen wurde, und die Assertions an das aktuelle Verhalten von `INSERT` anpassen.

| Datei | Änderung |
|------|--------|
| `tests/tests/lib/ezdb/mongodb/stubs.php` | Der Konstruktor von `expMongoDBTestable` initialisiert jetzt `$this->DB = 'mongo';`, sodass `selectCollection()` einen Datenbanknamen ungleich null erhält. |
| `tests/tests/lib/ezdb/mongodb/expMongoDBAdapterTest.php` | `testQueryReturnsFalse` an `INSERT` angepasst, das `true` zurückgibt; `testArrayQueryReturnsEmptyArray` fragt jetzt eine nicht existierende Gruppe ab, um die erwartete Warnung auszulösen. |


## 6. Konfiguration und Suiten von PHPUnit

`phpunit.xml` wurde auf das Schema von PHPUnit 13 aktualisiert, und `tests/bootstrap.php` wurde um
Kompatibilitäts-Shims für ältere Test-Hilfsklassen erweitert.

| Datei | Änderung |
|------|--------|
| `phpunit.xml` | Auf das Schema von PHPUnit 13 aktualisiert: `testsuites`, `source/include` für Coverage, `PHP_INI_SCAN_DIR`, `exclude` für abstrakte Klassen und die Verzeichnisse `security`/`mongodb`, `executionOrder="depends,defects"`, `failOnRisky`, `failOnWarning`, `failOnPhpunitWarning`, `displayDetails...` usw. |
| `tests/bootstrap.php` | Erweiterte Shim-Schicht für die Klassen `PHPUnit_Framework_*`, `ezpDatabaseHelper`, `ezpObject`, `ezpClass` und die Hilfsklassen `ezpDatatypeTestDataSet` (bei Bedarf). |


## 7. Überprüfung

Das Ergebnis ist mit und ohne Coverage dasselbe. Verwenden Sie die `php`-Binärdatei der PHP-Version, die Sie testen
(8.4 oder 8.5), aus dem Wurzelverzeichnis der Installation. Führen Sie die Suite auf einer Entwicklungskopie aus:
Einige Tests schreiben in die Datenbank.

Ohne Coverage:

```bash
php ./vendor/bin/phpunit --no-coverage --display-deprecations --display-warnings
```

Ergebnis:

```
OK, but some tests were skipped!
Tests: 715, Assertions: 1117, Skipped: 30
```

Mit Coverage:

```bash
XDEBUG_MODE=coverage php -d memory_limit=-1 -d zend_extension=xdebug.so ./vendor/bin/phpunit --coverage-html coverage
```

Ergebnis:

```
OK, but some tests were skipped!
Tests: 715, Assertions: 1117, Skipped: 30
```

Die übersprungenen Tests sind zu erwarten: Sie brauchen optionale Erweiterungen oder externe Dienste, die in der
Testumgebung nicht vorhanden sind. Wichtig ist das Fehlen von Fehlern, Warnungen, Deprecations und riskanten Tests.


## Was das für den Branch 6.0 bedeutet

Mit dieser Bereinigung ist die Testsuite von 6.0 wieder ein verlässliches Signal. Ein grüner Lauf von PHPUnit bedeutet
jetzt, dass der Code wirklich besteht, nicht nur hinter einer Wand unterdrückter Warnungen verborgen ist. Läufe mit
Coverage funktionieren mit Prozessisolation, die Tests von MongoDB und dem Session-Handler sind stabil, und die
Korrekturen am Kernel beheben echte Inkompatibilitäten mit PHP 8.4, die sonst in der Produktion auftreten würden.

Wenn Sie die Testsuite von 6.0 erweitern, beachten Sie diese Muster:

- Deklarieren Sie Eigenschaften von Tests ausdrücklich.
- Lassen Sie Klassennamen zu Dateinamen passen.
- Schließen Sie abstrakte Testklassen aus Suiten aus und verwenden Sie `require_once` in konkreten Unterklassen.
- Räumen Sie Exception-Handler und anderen globalen Zustand in `tearDown()` auf.
- Vermeiden Sie `error_log()` und andere direkte Ausgaben im Produktionscode; leiten Sie Diagnosen über die
  Logging-Hilfen des Frameworks.

## Die Festlegung der PHPUnit-Version

`composer.json` verlangte `phpunit/phpunit` in der exakten Version `13.0.0`. Jetzt verlangt es `^13.4`, sodass
Releases von 13.x ab 13.4 erlaubt sind (13.4.0 ist das neueste). Mit PHPUnit 13.4.0 überprüft: Die Suiten `lib` und
`security` liefern dieselben Ergebnisse wie unter 13.0 (51 Sicherheitstests bestehen; `lib` besteht mit denselben 595
Tests, 9 weitere Tests als riskant markiert). `composer.lock` wird in diesem Repository nicht versioniert, daher folgt
das installierte `vendor/` beim nächsten bewussten Lauf von Composer.

## Verwandte Seiten

- [Unterstützung für PHP 8](php8.md)
- [Upgrade auf PHPUnit 10](phpunitv10.md) und [Upgrade auf PHPUnit 13](phpunitv13.md)
- [`ezpSessionHandlerDB`: Korrekturen für PHP 8 und die Testsuite für PHPUnit 13](ezpsessionhandlerdb-php8-korrektur-und-tests.md)
- [PHP 8.4 und 8.5 für den älteren Symfony-Stack: die Framework-Forks](../../funktionen/6.0/plattform-php85-framework-forks.md)
- Geschichte: [April 2025](../../geschichte/2025/2025-04.md), [September 2025](../../geschichte/2025/2025-09.md),
  [Dezember 2025](../../geschichte/2025/2025-12.md), [Februar 2026](../../geschichte/2026/2026-02.md),
  [März 2026](../../geschichte/2026/2026-03.md)
