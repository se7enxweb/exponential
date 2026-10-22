---
title: Tests ohne Datenbank und Testsuiten
audience: software-engineer, developer
category: software-engineering
order: 510
summary: Wie die PHPUnit-Konfiguration von Exponential organisiert ist - der Bootstrap, die Suiten in phpunit.xml, die ausgeschlossenen Gruppen, die PHPUnit-Version je PHP-Version - und wie Sie Tests schreiben und ausführen, die keine Datenbank brauchen, plus die Befehle für Datenbank- und Live-Tests, wenn Sie doch eine brauchen.
keywords: PHPUnit, phpunit.xml, Testsuiten, ohne Datenbank, Bootstrap, Gruppen, Gruppe database, runtests.php, Live-Tests, Umgebungsvariablen, ezpTestCase, Test-Toolkit, Softwareingenieur, test suites, DB-less, groups, database group, live tests, environment variables, test toolkit
type: help
---
# Tests ohne Datenbank und Testsuiten

Diese Seite richtet sich an Ingenieure, die Tests für den Kernel oder eine Erweiterung schreiben oder ausführen. Die
meisten Tests von Exponential laufen ohne Datenbank; das erlaubt der CI, sie bei jedem Push auf fünf PHP-Versionen
auszuführen. Die Referenz ist [Tests und Continuous Integration, PHPUnit](../../standards/tests-und-ci.md#phpunit);
für Autoren von Erweiterungen [PHPUnit-Tests ohne Datenbank](../ersteller/anwendungsentwickler/phpunit-tests-ohne-datenbank.md).

## Inhalt

1. [Die Konfiguration](#die-konfiguration)
2. [Die Suiten](#die-suiten)
3. [Tests ausführen](#tests-ausführen)
4. [Einen Test ohne Datenbank schreiben](#einen-test-ohne-datenbank-schreiben)
5. [Wenn Sie eine Datenbank oder einen Live-Server brauchen](#wenn-sie-eine-datenbank-oder-einen-live-server-brauchen)

## Die Konfiguration

`phpunit.xml`:

- `bootstrap="tests/bootstrap.php"` lädt den Autoloader von Composer, schaltet die Klassenzuordnung der Tests ein und
  lädt die Toolkit-Klassen in `tests/toolkit/` (`ezpTestCase`, `ezpDatabaseTestCase` ...). Es startet den Kernel nicht;
  Tests, die ihn brauchen, tun das selbst.
- Die Gruppen `database`, `mail-live`, `mongodb-live` und `network-live` sind standardmäßig ausgeschlossen.
- Ein Testlauf schreibt seine eigenen Audit-Einträge in ein Verzeichnis unter `var/tmp`.

PHPUnit selbst hängt von der PHP-Version ab: Die CI verwendet 10.5 auf PHP 8.1, 11.5 auf 8.2 und 8.3 und 13 auf 8.4 und
8.5. Schreiben Sie Tests, die mit allen dreien funktionieren ([PHPUnit 13](../../bc/6.0/phpunitv13.md),
[PHPUnit 10](../../bc/6.0/phpunitv10.md)).

## Die Suiten

| Suite | Deckt ab |
|---|---|
| `security` | `tests/tests/kernel/classes/security` |
| `kernel-classes` | Kernel-Klassen, die Template-Operatoren von `kernel/common`, das Fehlermodul |
| `kernel-content`, `kernel-datatypes`, `kernel-private`, `kernel-search` | Inhalte, Datentypen, REST und Konsolenbefehle, Suche |
| `kernel-contentfinder`, `kernel-contentcheck`, `kernel-logviewer`, `kernel-requestlog` | neuere Kernel-Subsysteme |
| `lib` | die Bibliotheken |
| `mongodb`, `mongodb-live` | der MongoDB-Adapter; die Live-Suite braucht Server |
| `cjw_newsletter`, `explayouts_admin`, `ezjscore`, `ezmultiupload`, `expservices`, `ezoe`, `maps`, `admin-extensions` | Erweiterungen |
| `quality` | die eigenen Codestil-Sniffs des Projekts, die Git-Hooks, die Workflow-Regeln |
| `design` | die Regeln für das Administrationsdesign, die Regeln der Content Security Policy, Übersetzungen |

## Tests ausführen

```bash
php vendor/bin/phpunit                                    # every suite, without a database
php vendor/bin/phpunit --testsuite security               # one suite
php vendor/bin/phpunit tests/tests/kernel/classes/security/eZSecurityHardeningTest.php   # one file
php vendor/bin/phpunit --list-tests                       # what would run
```

Ein Test, der allein besteht und im ganzen Lauf fehlschlägt, teilt Zustand mit einem anderen Test; führen Sie die beiden
Dateien zusammen aus, um das Paar zu finden. Ein Test, der auf Ihrer Installation besteht und in der CI fehlschlägt, liest
meist etwas aus Ihrer Installation; führen Sie ihn in einem frischen Klon aus, in dem nur `vendor/` installiert ist.

## Einen Test ohne Datenbank schreiben

- Testen Sie Klassen und Funktionen direkt; übergeben Sie Werte, lesen Sie sie nicht aus einer Anfrage.
- Lesen Sie weder `settings/override` noch Caches in `var/`; brauchen Sie eine Einstellung, setzen Sie sie im Test und
  stellen Sie sie wieder her.
- Räumen Sie Statics, im Prozess gesetzte INI-Werte und Dateien, die Sie angelegt haben, auf.
- Benennen Sie die Datei `*Test.php` und legen Sie sie unter das Verzeichnis einer Suite, sonst wird sie nie ausgeführt.
- Rendern Sie für einen Template-Operator oder eine View mit einem Ersatzobjekt statt mit einer Datenbankzeile.
- Lässt sich die Änderung einer Datei zuordnen, stellen Sie sicher, dass `tests/test-map.json` Ihren Test finden kann
  (nach der Klasse benannt oder ein Eintrag von Hand), damit die schnellen Gates ihn ausführen.

## Wenn Sie eine Datenbank oder einen Live-Server brauchen

```bash
php -d date.timezone=America/Los_Angeles tests/runtests.php --dsn mysql://user@127.0.0.1/testdb --db-per-test
```

führt die Suiten gegen eine Wegwerf-Datenbank aus, wie es der Datenbankjob der CI tut. Kennzeichnen Sie solche Tests mit
`@group database`. Tests gegen Live-Server lesen deren Ort aus Umgebungsvariablen (`EXP_TEST_MYSQL_*`,
`EXP_TEST_MONGO_URI`, `EXP_TEST_PUBLIC_HOST` ...) und werden mit dem Namen der Variablen übersprungen, wenn sie nicht
gesetzt ist. Übergeben Sie Passwörter über die Umgebung, nicht auf der Kommandozeile
([Qualitätsprüfungen, Tests, die einen Live-Server brauchen](../../funktionen/6.0/qualitaetspruefungen.md#tests-die-einen-laufenden-server-brauchen)).

## Verwandte Seiten

- [Teststrategie](teststrategie.md)
- [CI-Gates](ci-gates.md)
- [Tests und CI für eine aktualisierte Erweiterung](../erweiterungsentwicklung/teil-4-aktualisieren/tests-und-ci-fuer-eine-aktualisierte-erweiterung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
