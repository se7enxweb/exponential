# Spezifikation: Continuous Integration und die Testsuite (Juli bis August 2026)

Diese Seite beschreibt die Testsuite des Root-Repositorys und den GitHub-Actions-Workflow, der sie bei jeder Änderung auf PHP 8.1
bis 8.5 ausführt: was läuft, wie Sie dasselbe auf Ihrem Rechner ausführen und was jeder Teil absichert. Lesen Sie sie, wenn
Sie den Kernel ändern oder Extensions ausliefern und einen Nachweis wollen, dass eine Änderung die Plattform funktionsfähig hält. Beides wurde
zwischen dem 11. Juli und dem 18. August 2026 hinzugefügt.

## Kurz gesagt

- Alles ausführen: `php vendor/bin/phpunit`. Eine Suite ausführen: `php vendor/bin/phpunit --testsuite security`.
- Der Workflow ist `.github/workflows/phpunit.yml`, mit einem Job pro PHP-Version und einem Datenbank-Job.
- Datenbanktests legen Tabellen an und löschen sie: Richten Sie sie nur auf eine Wegwerf-Datenbank.

## Was hinzugefügt wurde

| Datum | Commit | Was |
|---|---|---|
| 11. Juli | `8798108324` | PHPUnit 13 und Aufräumarbeiten für PHP 8.4: neue `phpunit.xml`, `tests/bootstrap.php`, `tests/xdebug.ini`, 132 geänderte Dateien unter `tests/` und daneben, 72 davon neu (Cluster-Datei-Handler, Inhaltsklassen, Rollen, RSS-Export, Siteaccess ...; zählen mit `git show --name-status 8798108324`). |
| 18. August | `ea4b1dbbb7`, `ea350a1cd1`, `fd67c78a2f` | Der Workflow `.github/workflows/phpunit.yml`: PHPUnit auf PHP 8.1 bis 8.5; Pfadfilter; ein manueller Trigger. |
| 18. August | `1ba705708d` und folgende | Ein zweiter Job führt die Datenbanktests auf MySQL und PostgreSQL aus. |
| 18. August | `d07edf3297`, `4de4ebbbe5`, `3cad135744` | MongoDB-Unterstützung im Workflow. |
| 18. August | `8ec58ea189`, `a1effad49a`, `fcd79af6b8` | Eine Kompatibilitätsschicht für PHPUnit, damit das alte Test-Toolkit (geschrieben für PHPUnit 3.7) auf aktuellem PHPUnit läuft. |
| 18. August | `d362e2523d`, `f964202536`, `03b6de43d8` | Veraltete Aufrufe aus PHPUnit 10 und PHP 8.5 entfernt (`setAccessible()`, `$backupGlobals`, `imagedestroy()`). |

Viele der Commits vom 18. August beheben einzelne Fehlschläge des Workflows, die
beim Ausführen gefunden wurden; es sind gemergte Pull Requests mit dem Titel „Initial plan“
(ein leerer erster Commit), gefolgt von der Korrektur.

## Der Workflow

Datei `.github/workflows/phpunit.yml`. Auslöser: ein Push oder Pull Request auf
`main` oder `master`, der eine `.php`-Datei, `composer.json`, `composer.lock`,
`phpunit.xml` oder den Workflow selbst ändert, und ein manueller Lauf (`workflow_dispatch`).
Das Token hat nur Lesezugriff (`permissions: contents: read`).

### Job `phpunit`

| Punkt | Wert |
|---|---|
| Matrix | PHP `8.1`, `8.2`, `8.3`, `8.4`, `8.5` (nicht fail-fast: eine fehlschlagende Version hält die anderen nicht an) |
| PHPUnit | `^10.5` auf PHP 8.1, `^11.5` auf 8.2 und 8.3, die von `composer.json` verlangte Version auf 8.4 und 8.5 |
| PHP-Erweiterungen | dom, libxml, mbstring, pcre, json, iconv, reflection, session, spl, simplexml, gd, mongodb, mysqli, pdo_mysql, pdo_sqlite, sqlite3, zip |
| Codeabdeckung | Xdebug |
| Systempakete | ImageMagick (Bildtests), die Bibliotheken des MongoDB-Treibers |
| Autoloads | `php bin/php/ezpgenerateautoloads.php -s -e` |
| Befehl | `vendor/bin/phpunit --colors=always` |

Der Workflow kopiert `composer.json` in eine temporäre Overlay-Datei
(`composer.ci-phpunit.json`), passt `phpunit/phpunit` an die PHP-Version an,
verlangt `mongodb/mongodb`, löst die Abhängigkeiten mit
`composer update` neu auf und löscht das Overlay am Ende. Eine einzige
eingecheckte Lock-Datei kann er nicht verwenden: Die Dev-Abhängigkeiten unterscheiden sich je PHP-Version, und eine eingefrorene Lock-Datei
brach bei jedem späteren Neu-Pinnen von `composer.json`.

PHP 8.0, die älteste unterstützte Version, ist nicht in dieser Matrix: PHPUnit 10 und neuer
brauchen 8.1, und die Tests sind für diese Versionen geschrieben. PHP 8.0 wird stattdessen vom Job `php80`
in `.github/workflows/quality.yml` abgedeckt: `php -l` mit 8.0 auf jeder PHP-Datei, ein
`composer install` für den Produktivbetrieb auf 8.0 und der Start-Rauchtest `tests/bin/bootsmoke.php`
(jede Klasse des Kernels und der Extensions geladen, die Kompatibilitätsfunktionen, ein Template mit
`wash`). Siehe [PHP-8.0-Unterstützung](../../../en/us/bc/6.0/php-8.0-support.md) und
[Qualitätsprüfungen](../../../en/us/features/6.0/quality-checks.md).

### Job `phpunit-db`

Läuft auf PHP 8.3 mit MySQL 8.0 und mit PostgreSQL 15 (Service-Container),
legt die Datenbank `testdb` an (PostgreSQL erhält zusätzlich die Erweiterung `pgcrypto`),
setzt die Zeitzone für stabile Ergebnisse auf `America/Los_Angeles` und führt aus:

```bash
php -d date.timezone=America/Los_Angeles tests/runtests.php --dsn "mysql://root@127.0.0.1/testdb" --db-per-test tests extension/ezoe
```

`--db-per-test` gibt jeder Testklasse ein frisches Schema, wie es das alte Toolkit tut.

## Dasselbe auf Ihrem Rechner ausführen

Sie brauchen einen Checkout mit seinem Verzeichnis `vendor/` (eine Kopie der Exponential-Distribution,
keinen nackten Git-Klon; fehlt `vendor/`, führen Sie einmal selbst `composer install` aus).

```bash
# all suites
php vendor/bin/phpunit

# one suite
php vendor/bin/phpunit --testsuite security
php vendor/bin/phpunit --testsuite kernel-classes

# list tests and files
php vendor/bin/phpunit --list-tests
php vendor/bin/phpunit --list-test-files

# the suites and how many tests each holds (6.0.15 tree, PHPUnit 13.0.0)
php vendor/bin/phpunit --list-suites

# the database tests of the legacy toolkit (use a throw-away database)
php tests/runtests.php --dsn=mysql://YOUR_USER:YOUR_PASSWORD@127.0.0.1/testdb --db-per-test tests
```

Die Testsuites in `phpunit.xml`. Die Anzahl der Tests wurde am 5. Oktober 2026 mit `--list-suites` geprüft (2. Oktober:
kernel-classes 1364, kernel-datatypes 38, lib 610); sie wächst mit jeder Release.

| Suite | Verzeichnis | Tests | Braucht |
|---|---|---|---|
| `security` | `tests/tests/kernel/classes/security` | 51 | nichts |
| `kernel-classes` | `tests/tests/kernel/classes` (ohne `security`) und `tests/tests/kernel/common` | 2110 | nichts; die Tests einer laufenden Installation werden ohne eine solche übersprungen |
| `kernel-content` | `tests/tests/kernel/content` | 4 | nichts |
| `kernel-datatypes` | `tests/tests/kernel/datatypes` | 141 | nichts |
| `lib` | `tests/tests/lib` (ohne `ezdb/mongodb`) | 2043 | nichts |
| `mongodb` | `expMongoDBAdapterTest.php` | 37 | nichts (kein laufendes MongoDB) |
| `mongodb-live` | `expMongoDBIntegrationTest.php` | 18 | laufende MongoDB- und MySQL-Server; ihre Gruppe ist vom Standardlauf ausgeschlossen |
| `cjw_newsletter` | `tests/tests/extension/cjw_newsletter` (nach August hinzugefügt) | 244 | eine laufende Datenbank der Installation (Wegwerf-Daten, E-Mails werden in Dateien geschrieben, nie versendet) |

Die Gruppen `database`, `mail-live`, `mongodb-live` und `network-live` sind vom Standardlauf ausgeschlossen. Richten Sie einen Testlauf nicht auf eine Datenbank, die Inhalte enthält, die Sie behalten wollen: Die
Datenbanktests legen Tabellen an und löschen sie.

### Die Tests ausführen: die zwei Arten von Tests

- **Unit-Tests** brauchen weder Datenbank noch Siteaccess. Sie rufen eine Klasse direkt auf, mit Platzhalterobjekten, wo sie ein
  Objekt braucht (ein Template, ein Attribut eines Inhaltsobjekts, eine Anfrage), und mit den Einstellungen, von denen sie abhängen, im Test gesetzt
  und in `tearDown()` zurückgesetzt. Sie laufen in jedem CI-Job. Beispiele: `eZTemplateEngineRenderTest` rendert etwa 150
  Template-Quellen einmal verarbeitet und einmal kompiliert (kompilierte Dateien landen in einem privaten Verzeichnis unter `var/tmp` und
  werden danach entfernt), und beide Ausgaben müssen übereinstimmen; `eZLocaleFormattingTest` prüft jede Locale in
  `share/locale`; die Datentyp-Tests schreiben das gespeicherte XML und die Textform, die Import und
  Export verwenden, und lesen sie zurück; die REST-Tests bauen `ezpRestRequest`-Objekte.
- **Tests einer laufenden Installation** beginnen mit `ezpLiveInstallation::requireOrSkip()` und werden mit Angabe des Grundes übersprungen, wenn
  es keine Datenbank mit einer installierten Site gibt, wie in CI. Sie legen ihre eigenen Daten an und entfernen sie wieder.

Regeln für neue Tests: Tabellen von Fällen als Data Provider; kein Netzwerk, keine Wartezeiten, keine echten E-Mails (der
Datei-Transport); temporäre Dateien unter `var/tmp`, in `tearDown()` entfernt; PHP-8.0-Syntax (Typdeklarationen nur bei
`setUp()`/`tearDown()`, was PHPUnit verlangt). Ein für einen Fehler geschriebener Test schlägt vor der Korrektur fehl und wird mit ihr
oder direkt danach committet; ein fehlschlagender Test wird nie abgeschwächt, damit er besteht.

Auf einem gemeinsam genutzten Server, auf dem mehrere Personen Suites gegen eine SQLite-Datenbank ausführen, führen Sie die Suites, die in sie schreiben,
nacheinander aus: SQLite erlaubt nur einen Schreiber.

## Codeabdeckung mit Xdebug

`tests/xdebug.ini` lädt Xdebug im Coverage-Modus für isolierte Testprozesse.
`tests/bootstrap.php` ermittelt zur Laufzeit das INI-Scan-Verzeichnis von PHP auf dem System und
hängt `tests/` an, sodass auch die Kindprozesse die Datei laden (11. Juli; am 18. August wurde das doppelte
Laden per `zend_extension` entfernt, das PHP 8.4 mit Xdebug 3.5 zum Absturz brachte, `302134b6e8`).

```bash
php -d xdebug.mode=coverage vendor/bin/phpunit --coverage-text
```

Wenn Xdebug installiert, aber für die CLI nicht aktiviert ist (wie bei den PHP-Builds von Plesk), laden Sie es nur für einen Lauf:
`-d zend_extension=...` erreicht die isolierten Testprozesse nicht, die die INI-Scan-Verzeichnisse lesen; setzen Sie deshalb ein
Verzeichnis mit einer INI-Datei, die es lädt, vor `tests/`:

```bash
mkdir -p var/tmp/xdebug-scan
printf 'zend_extension=/opt/plesk/php/8.5/lib64/php/modules/xdebug.so\nxdebug.mode=coverage\n' > var/tmp/xdebug-scan/90-xdebug.ini
PHP_INI_SCAN_DIR="/opt/plesk/php/8.5/etc/php.d:$PWD/var/tmp/xdebug-scan:$PWD/tests" XDEBUG_MODE=coverage \
    /opt/plesk/php/8.5/bin/php -d memory_limit=4G vendor/bin/phpunit --coverage-text --only-summary-for-coverage-text
```

Die `<source>` von `phpunit.xml` ist `lib/` und `kernel/`, etwa 205 000 Zeilen. Gemessen am 5. Oktober 2026 mit
vorhandener laufender Datenbank: Alle Suites zusammen deckten 23,4 % der Zeilen (47 580) und 18,7 % der Methoden ab, bevor
die Unit-Tests dieses Tages hinzukamen; die Suite `lib` allein stieg am selben Tag von 2,4 % der Zeilen (629 Tests) auf 7,6 %
(2 028 Tests). Die Suites, die in eine laufende Datenbank schreiben, sind mit eingeschalteter Codeabdeckung langsam; messen Sie deshalb auf einem
gemeinsam genutzten Server die Suites, die keine Datenbank brauchen, direkt und die mit laufender Datenbank ein Verzeichnis nach dem anderen.

## Für Autoren von Extensions

- Legen Sie Tests unter `tests/` in Ihrer Extension ab und fügen Sie einer Kopie von `phpunit.xml` eine `testsuite` hinzu.
- Deklarieren Sie denselben Testklassennamen nicht zweimal (zwei Korrekturen im August benannten
  Duplikate in den MongoDB-Tests und die Klasse `eZINITest` um).
- Abstrakte Basisklassen sind von den Suites ausgeschlossen; konkrete Unterklassen
  binden ihre Basisklasse mit `require_once` ein.
- Das Test-Toolkit bietet weiterhin `ezpTestCase`, `ezpDatabaseTestCase` und
  `ezpTestSuite`; sie starten den Kernel über `eZScript`, starten Sie den Kernel
  deshalb nicht selbst in `bootstrap.php`.

## Siehe auch

- Werkzeugkette: [PHPUnit 10](../../../en/us/bc/6.0/phpunitv10.md), [PHPUnit 13](../../../en/us/bc/6.0/phpunitv13.md), [PHPUnit 13 für PHP 8.4.1](../../../en/us/bc/6.0/phpunitv13forPHP841.md), [PHP-8-Unterstützung](../../../en/us/bc/6.0/php8.md)
- Spezifikationen: [Sicherheits-Patches vom August 2026](sicherheitshaertung-2026-08.md) (die Suite `security`), [Datenbanktreiber und Installer, September 2026](datenbanktreiber-2026-09.md)
- [Verhaltensänderungen vom Juli und August 2026](../../../en/us/bc/6.0/behaviour-changes-2026-07-08.md), [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md), Monatsseiten [Juli 2026](../../../en/us/history/2026/2026-07.md) und [August 2026](../../../en/us/history/2026/2026-08.md)
