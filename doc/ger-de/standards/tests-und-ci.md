---
title: Tests und Continuous Integration
audience: developer, admin
category: standards
order: 90
summary: Wie Exponential getestet wird - PHPUnit-Suites mit und ohne Datenbank, die Test-Map, die Workflows von GitHub Actions, das Dateimanifest, Git-Hooks und die Prüfungen vor einem Push - mit Zahlen und bekannten Lücken.
keywords: Tests, Testsuite, Continuous Integration, Qualität, Leistung, Benchmark, Dateimanifest, Git-Hooks, Prüfungen, testing, PHPUnit, test suite, CI, GitHub Actions, workflow, quality, phpunit.yml, quality.yml, performance, CI health, test map, file manifest, filelist.md5, checkmanifest, git hooks, pre-commit, commit-msg, pre-push, gates, MySQL, PostgreSQL, SQLite
type: reference
---
# Tests und Continuous Integration

Diese Seite richtet sich an Entwickler, die Exponential ändern und wissen möchten, welche Tests und Prüfungen laufen,
und an Administratoren, die beurteilen möchten, wie ein Release überprüft wurde. Sie beantwortet: Welches
Testframework und welche Suites es gibt, was bei jedem Push und Pull Request läuft, was nur lokal läuft und was nicht
abgedeckt ist. Die Einzelheiten der Arbeit stehen unter [Qualitätsprüfungen](../funktionen/6.0/qualitaetspruefungen.md) und
[Continuous Integration](../bc/6.0/ci.md).

## Zusammenfassung

| Standard / Praxis | Version | Status | Nachweis (kurz) |
|---|---|---|---|
| Unit- und Integrationstests mit PHPUnit | PHPUnit 10.5, 11.5, 13 (je nach PHP-Version) | **Erfüllt** | `phpunit.xml` (23 Suites), 874 `*Test.php`-Dateien unter `tests/tests/` |
| Continuous Integration | GitHub Actions | **Erfüllt** | `.github/workflows/quality.yml`, `phpunit.yml`, `performance.yml`, `ci-health.yml` |
| Tests gegen echte Datenbanken | MySQL 8.0, PostgreSQL 15 | **Teilweise erfüllt** | `phpunit.yml`, Job `phpunit-db`; kein Job für SQLite-, MariaDB-, Oracle- oder MongoDB-Server |
| Dateimanifest | `share/filelist.md5` | **Erfüllt** | `bin/php/checkmanifest.php`, blockierend bei einem Push auf `main` |
| Git-Hooks | Git `core.hooksPath` | **Erfüllt** (Opt-in) | `.githooks/pre-commit`, `commit-msg`, `pre-push`; `make hooks` |
| Prüfung der Commit-Nachrichten | Projektrichtlinie | **Erfüllt** | `.githooks/check-commit-range`, `quality.yml`, Schritt „Commit messages“ |
| Testauswahl für eine Änderung | `tests/test-map.json` | **Erfüllt** | [Qualitätsprüfungen](../funktionen/6.0/qualitaetspruefungen.md), „Nur die Tests einer Änderung ausführen“ |
| Bericht zur Codeabdeckung | - | **Nicht erfüllt** | die Abdeckung wird erfasst (`coverage: xdebug`), aber weder veröffentlicht noch als Prüfkriterium genutzt |
| Prüfung auf Leistungsrückgänge | Projekt-Benchmark | **Teilweise erfüllt** | `performance.yml`, nur Warnung |

## PHPUnit

**Was es ist.** PHPUnit (Sebastian Bergmann) ist das Standard-Framework für Unit-Tests in PHP.

**Version.** `require-dev` in `composer.json` erlaubt `phpunit/phpunit` `^9.6 || ^10.5 || ^11.5 || ^12.0 || ^13.4`,
sodass Composer die neueste Version wählt, die die PHP-Version zulässt. Die CI legt sie je Durchlauf fest: PHPUnit 10.5
unter PHP 8.1, 11.5 unter PHP 8.2 und 8.3, den Bereich aus `composer.json` (13) unter PHP 8.4 und 8.5. `phpunit.xml`
verweist auf das Schema von PHPUnit 13.0.

**Konfiguration** (`phpunit.xml`):

- `bootstrap="tests/bootstrap.php"`: lädt den Autoloader von Composer, aktiviert die Klassenzuordnung der Tests
  (`ezpAutoloader::enableTests()`, `var/autoload/ezp_tests.php`), definiert `PHPUNIT_COMPOSER_INSTALL` für isolierte
  Kindprozesse und lädt die Toolkit-Klassen in `tests/toolkit/` (`ezpTestCase`, `ezpDatabaseTestCase` ...). Den Kernel
  startet es nicht; Tests, die ihn brauchen, tun das selbst.
- `memory_limit` `1G`, Umgebung `EZP_TEST_ENVIRONMENT=1`, `<source>` = `lib` und `kernel`.
- Standardmäßig ausgeschlossene Gruppen: `database`, `mail-live`, `mongodb-live`, `network-live`.

**Suites** (23, aus `phpunit.xml`):

| Suite | Was sie abdeckt | Benötigt |
|---|---|---|
| `security` | `tests/tests/kernel/classes/security` | nichts |
| `kernel-classes` | `tests/tests/kernel/classes` (ohne security), `kernel/common` (Template-Operatoren), `kernel/error` | nichts |
| `kernel-content`, `kernel-datatypes`, `kernel-private` | Inhalte, Datentypen, REST, Konsolenbefehle, Hilfsklassen | einige Tests eine Datenbank |
| `kernel-search` | die eingebaute Suchmaschine | Datenbanktests werden zurückgerollt |
| `kernel-contentfinder`, `kernel-contentcheck` | Content-Finder, Inhaltsprüfungen | Datenbanktests nur in einer Wegwerf-Sandbox (`EXP_CONTENTFINDER_SANDBOX`, `EXP_CONTENTCHECK_SANDBOX`) |
| `kernel-logviewer`, `kernel-requestlog` | Log-Viewer, Anfrage-Logs und Klassifizierer | nichts |
| `lib` | `tests/tests/lib` (ohne MongoDB) | nichts |
| `mongodb` | Units des MongoDB-Adapters | nichts |
| `mongodb-live` | Integration mit MongoDB und MySQL | laufende Server |
| `cjw_newsletter`, `explayouts_admin`, `expservices`, `ezoe` | Verhalten von Extensions auf einer laufenden Installation | eine installierte Site; ohne sie übersprungen (`ezpLiveInstallation::requireOrSkip()`) |
| `ezjscore`, `ezmultiupload`, `maps` | Code von Extensions mit Platzhaltern | nichts |
| `admin-extensions` | Admin-Templates von `git_manager`, `ezupdate`, `sevenx_dse`, das Bearbeitungsskript von ezflow | nur Dateien |
| `quality` | der phpcs-Sniff des Projekts, die Git-Hooks, die Workflow-Regeln (`tests/tests/bin`) | Entwicklungswerkzeuge |
| `design` | die Regeln der Admin-Designs, CSP, Übersetzungen (`tests/tests/design`) | nur Dateien, für einige Node.js |

**Zahlen** (für diese Seite mit `find` ermittelt):

| Verzeichnis | `*Test.php`-Dateien |
|---|---|
| `tests/tests/kernel` | 508 (davon `kernel/classes` 388) |
| `tests/tests/extension` | 148 |
| `tests/tests/lib` | 123 |
| `tests/tests/design` | 91 |
| `tests/tests/bin` | 4 |
| **Gesamt** | **874** (insgesamt 1.135 PHP-Dateien, mit Hilfsdateien und Fixtures) |

43 Testdateien tragen `@group database`. [Qualitätsprüfungen](../funktionen/6.0/qualitaetspruefungen.md) nennt mehr als
10.000 Testmethoden im gesamten Durchlauf; diese Zahl wurde hier nicht nachgezählt.

### Tests mit und ohne Datenbank

- `vendor/bin/phpunit` führt jede Suite ohne Datenbank aus; die Gruppe `database` ist ausgeschlossen.
- `tests/runtests.php --dsn <dsn> --db-per-test` führt jede Suite aus `phpunit.xml` mit ihren Ausschlüssen gegen eine
  Wegwerf-Datenbank aus, wie es der CI-Job `phpunit-db` tut. Es endet mit dem Exit-Code von PHPUnit (seit 15.10.2026;
  vorher endete es immer mit 0).
- Suites für laufende Installationen überspringen jede Klasse mit einer Begründung, wenn keine installierte Site
  vorhanden ist (die CI hat keine).

## Workflows von GitHub Actions

**Was es ist.** GitHub Actions führt die Workflows in `.github/workflows/` auf von GitHub gehosteten Ubuntu-Runnern aus.

| Workflow | Auslöser | Jobs (PHP) | Blockiert |
|---|---|---|---|
| `quality.yml` | Push und Pull Request auf `main`/`master`, manuell | `checks` (8.3, Node.js 22): PHP-Syntax geänderter Dateien, JavaScript-Syntax geänderter Dateien, Dateimanifest, Commit-Nachrichten, Codestil (nur Warnung); `phpstan` (8.3); `sniffs` (8.4): `phpunit --testsuite quality`; `php80` (8.0): `make lint80-all`, `composer install --no-dev`, Start-Smoke-Test `tests/bin/bootsmoke.php` | ja, außer Codestil; das Manifest ist bei einem Pull Request eine Warnung |
| `phpunit.yml` | Push und Pull Request auf `main`/`master`, manuell; kein Pfadfilter | `phpunit`-Matrix 8.1, 8.2, 8.3, 8.4, 8.5 (Node.js 22, ImageMagick, xdebug); `phpunit-db` auf MySQL 8.0 und PostgreSQL 15 mit PHP 8.3, `date.timezone=America/Los_Angeles` | ja |
| `performance.yml` | Push und Pull Request, die PHP, Templates, `settings/`, die deutsche Übersetzung oder `tests/benchmark/` ändern | `micro` (8.5): `bin/php/benchmark.php micro` gegen `tests/benchmark/baseline.json`, eine Nachmessung | nein (`continue-on-error`) |
| `ci-health.yml` | montags 06:17 UTC, manuell | `report`: `.github/scripts/ci_health_report.py` (Erfolgsquoten, fehlschlagende Schritte und Tests, instabile Durchläufe) | nein, nur Bericht |

Gemeinsame Eigenschaften, alle aus den Workflow-Dateien gelesen:

- `permissions: contents: read` (`actions: read` für CI health); kein `pull_request_target`.
  `tests/tests/bin/CiWorkflowPullRequestTest.php` sorgt dafür, dass das so bleibt.
- `COMPOSER_ROOT_VERSION: dev-main`, damit Pakete, die `se7enxweb/exponential` voraussetzen, bei einem Pull Request
  aufgelöst werden.
- Eine Concurrency-Gruppe je Pull Request (ein neuer Push bricht den alten Durchlauf ab); jeder Push auf `main` behält
  seinen eigenen Durchlauf.
- Jeder Job hat ein Zeitlimit (20 oder 30 Minuten); Composer-Schritte werden dreimal wiederholt.
- Die PHPUnit-Jobs lösen die Abhängigkeiten bei jedem Durchlauf neu auf (`composer.ci-phpunit.json`, nicht
  committet), sodass ein neues Release einer Abhängigkeit ein Ergebnis ändern kann, ohne dass sich das Repository
  ändert.
- `phpunit` prüft, dass die Suites `security`, `kernel-classes`, `lib`, `expservices` und `ezoe` konfiguriert sind.

## Das Dateimanifest

**Was es ist.** `share/filelist.md5` listet `md5  path` für jede Datei der Distribution (9.551 Zeilen). Die
Aktualisierungsprüfung der Administrationsoberfläche vergleicht die installierten Dateien damit; eine veraltete Zeile
erscheint dort als geänderte Datei. Siehe [Prüfung der Dateikonsistenz](../funktionen/6.0/dateikonsistenzpruefung.md).

- `bin/php/checkmanifest.php --all` prüft jeden Eintrag, `--staged` die vorgemerkten Dateien, `--fix` schreibt
  veraltete Prüfsummen neu und ergänzt fehlende Dateien. Es braucht weder Kernel noch Datenbank und verwendet dieselbe
  Klasse wie die Aktualisierungsprüfung (`kernel/private/classes/expfileconsistencyreport.php`).
- `make manifest-check` und `make manifest-fix` kapseln es.
- Pre-Commit-Hook: `--staged`. CI: blockierend bei einem Push auf `main`, bei einem Pull Request eine Warnung mit dem
  genauen Patch.

**Status: Erfüllt.**

## Git-Hooks

`make hooks` setzt `git config core.hooksPath .githooks`. Die Hooks sind je Klon ein Opt-in.

| Hook | Was er ausführt |
|---|---|
| `pre-commit` | `php -l` für jede vorgemerkte PHP-Datei (auch mit PHP 8.0, falls vorhanden), `node --check` für vorgemerkte Skripte (falls Node.js installiert ist), `checkmanifest.php --staged`, `checkcodestyle.php --staged` |
| `commit-msg` | der Betreff muss mit `Added: `, `Updated: `, `Fixed: `, `Removed: ` oder `Renamed: ` beginnen (`Merge`, `Revert`, `fixup!`, `squash!`, `amend!` von Git sind erlaubt) |
| `pre-push` | PHPStan, wenn die Entwicklungswerkzeuge installiert sind |

`.githooks/check-commit-range [--github] <base> <head>` wendet die Regel für Commit-Nachrichten auf einen Bereich an,
wie es die CI tut. Ein Commit eines Projektautors (eine Adresse, die in `.githooks/commit-msg-authors` aufgeführt ist)
scheitert an einem fehlerhaften Betreff; der Commit eines Beitragenden erhält nur eine Warnung, weil Pull Requests mit
ihren Commits so zusammengeführt werden, wie sie geschrieben sind. `EXP_COMMIT_MSG_STRICT_ALL=1` macht die Prüfung für
jeden Autor streng. Getestet durch `tests/tests/bin/githooks/CheckCommitRangeTest.php`.

## Die Prüfungen vor einem Push

Die Push-Prüfung des Projekts führt die CI-Prüfungen für eine committete Revision in einem sauberen Worktree aus
([Continuous Integration](../bc/6.0/ci.md), „Die lokalen Prüfungen und die CI“):

| Prüfung | Kontrolle |
|---|---|
| lint | `php -l` für geänderte PHP-Dateien (PHP 8.3, wie in der CI) |
| lint80 | dasselbe mit PHP 8.0 |
| js | `node --check` für geänderte Skripte (Node.js 22) |
| commits | `.githooks/check-commit-range` |
| packagist | jede geänderte Versionsbedingung für `se7enxweb/*` in `composer.json` passt zu einem Release, das Packagist bereits listet |
| manifest | `bin/php/checkmanifest.php --all` |
| phpcs | `bin/php/checkcodestyle.php` (nur Warnung, wie in der CI) |
| phpstan | PHPStan gegen die Baseline |
| phpunit | die Tests der Änderung (`--fast`) oder der gesamte Durchlauf ohne Datenbank; `--ci` ergänzt den Lint mit PHP 8.0 für jede Datei und die Installation unter PHP 8.0 mit dem Start-Smoke-Test |

Das Prüfskript selbst ist ein Werkzeug der Maintainer und nicht Teil des Repositorys; die Befehle, die es ausführt,
sind die aus der Tabelle, und sie liegen alle im Repository. Für Beitragende listet
[Qualitätsprüfungen](../funktionen/6.0/qualitaetspruefungen.md), „Vor dem Push“, dieselben Prüfungen als einfache Befehle auf.

## Nur die Tests einer Änderung ausführen: `tests/test-map.json`

`tests/test-map.json` ordnet Quelldateien den Testdateien zu, die sie abdecken. Sie hat drei Abschnitte: `map` (von
Hand erstellte Einträge, ein Pfad oder ein Muster, in dem `*` auch `/` abdeckt), `generated` (Quellpfade, die
Testdateien wörtlich nennen) und `ignore` (Dateien, die absichtlich kein PHPUnit-Test abdeckt: `doc/*`, `*.md`,
`share/filelist.md5`, Übersetzungen, `autoload/*`, `.github/*` ...). Zusammen mit Tests, die nach den geänderten
Klassen benannt sind, wählt sie die Testdateien einer Änderung aus; jede läuft in einem eigenen PHPUnit-Prozess. Die
Befehle stehen unter [Qualitätsprüfungen](../funktionen/6.0/qualitaetspruefungen.md), „Nur die Tests einer Änderung ausführen“.

## Bekannte Lücken

1. **Sechs Testdateien gehören zu keiner Suite** und werden daher weder von `phpunit` noch von der CI ausgeführt:
   `tests/tests/extension/ezupdate/ezUpdateComposerLocationTest.php`, `ezUpdateComposerVersionCacheTest.php`,
   `ezUpdateInstallabilityTest.php`, `ezUpdateJobOutcomeTest.php`, `tests/tests/extension/bccie/ezBccieTest.php` und
   `tests/tests/extension/syndication/ezSyndicationTest.php` (gegen die Suites in `phpunit.xml` geprüft).
2. Die Suites für laufende Installationen (`expservices`, `ezoe`, `cjw_newsletter`, `explayouts_admin`,
   `mongodb-live`) werden in der CI übersprungen.
3. Die Datenbank-Jobs decken nur MySQL 8.0 und PostgreSQL 15 ab; SQLite 3 (die Standarddatenbank), MariaDB, Oracle und
   MongoDB-Server haben keinen CI-Job.
4. PHPUnit läuft nicht unter PHP 8.0.
5. Die Abdeckung wird in den Durchläufen ohne Datenbank erfasst, aber weder veröffentlicht noch erzwungen.
6. Codestil und Leistung sind nur Warnungen.
7. Git-Hooks sind ein Opt-in; ein Klon ohne `make hooks` verlässt sich auf die CI.
8. Branch-Schutz: `main` hat keine Branch-Schutzregel und kein Ruleset (GitHub-API, 18.10.2026), daher blockiert eine
   rote Prüfung einen Merge oder Push technisch nicht; es handelt sich um eine Richtlinie.

## Verwandte Seiten

- [Qualitätsprüfungen](../funktionen/6.0/qualitaetspruefungen.md)
- [Continuous Integration](../bc/6.0/ci.md)
- [Codierstandards](programmierstandards.md)
- [Versionsverwaltung und Releases](versionsverwaltung-und-releases.md)
- [Standards für PHP, PHP-FIG und Composer](php-und-composer.md)
