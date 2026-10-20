---
title: "Lektion 12.1: Die Test-Suites"
audience: trainer, developer
category: training
order: 121
summary: Die Teilnehmenden finden die PHPUnit-Suites des Kernels in phpunit.xml, führen eine Suite ohne Datenbank und eine einzelne Testdatei aus, lesen das Ergebnis und erklären, welche Suites eine Datenbank oder eine laufende Installation brauchen. 40 Minuten.
keywords: entwickler, tests, PHPUnit, phpunit.xml, testsuite, ohne datenbank, DB-less, gruppe database, tests/tests, tests/bootstrap.php, security-suite, design-suite, runtests.php, test-map.json, CI, developers course, database group, security suite, design suite
type: help
---
# Lektion 12.1: Die Test-Suites

Der Kernel hat Hunderte von Testdateien, und sie sind die besten Beispiele dafür, wie seine Klassen verwendet werden
sollen. Wer Code ändert, muss außerdem wissen, welche Tests vor dem Teilen der Änderung auszuführen sind. Diese
Lektion zeigt, wo die Tests liegen, wie sie gruppiert sind und wie man sie ohne Datenbank ausführt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 12: Tests und Qualität](README.md) |
| Dauer | 40 Minuten (15 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 1.2](../modul-01-die-entwicklungsinstallation/eine-entwicklungsinstallation-mit-git.md): eine Git-Installation mit Entwicklungsabhängigkeiten |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Suites in `phpunit.xml` und die Tests in `tests/tests/` finden;
- eine Suite mit `vendor/bin/phpunit --testsuite <name>` und eine Testdatei über ihren Pfad ausführen;
- erklären, dass der Standardlauf die Gruppe `database` ausschließt und keine Datenbank braucht;
- die Suites nennen, die eine laufende Installation oder Server brauchen;
- die Zusammenfassungszeile von PHPUnit lesen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Tests und Continuous Integration](../../../../../eng-us/standards/testing-and-ci.md).

1. **Wo** (3 Minuten). `phpunit.xml` im Wurzelverzeichnis definiert die Suites; `tests/tests/` enthält die Tests,
   gegliedert wie der Code (`kernel/`, `lib/`, `extension/`, `design/`, `bin/`). `tests/bootstrap.php` lädt die
   Autoloader und das Test-Toolkit; es startet den Kernel nicht.
2. **Suites** (4 Minuten). Beispiele: `security`, `kernel-classes`, `lib` (nichts nötig); `design` (nur Dateien);
   `kernel-search` (Datenbanktests werden zurückgerollt); `cjw_newsletter`, `ezoe` (eine laufende Installation, ohne
   sie übersprungen); `mongodb-live` (laufende Server).
3. **Ohne Datenbank** (3 Minuten). `vendor/bin/phpunit` führt jede Suite aus, wobei die Gruppe `database`
   ausgeschlossen ist. `tests/runtests.php --dsn <dsn> --db-per-test` läuft gegen eine Wegwerf-Datenbank, wie es der
   Datenbank-Job von CI tut.
4. **CI** (3 Minuten). GitHub Actions führt die Suites unter PHP 8.1 bis 8.5 für jeden Push und jeden Pull Request
   auf `main` aus; ein roter Lauf blockiert.
5. **Nur die Tests einer Änderung** (2 Minuten). `tests/test-map.json` ordnet Quelldateien den Tests zu, die sie
   abdecken; der vollständige Lauf bleibt die Prüfung vor einem Release.

### Häufige Fragen

**Wie lange dauert der gesamte Lauf?** Auf einem ausgelasteten Rechner viele Minuten; darum werden zuerst die Tests
einer Änderung ausgeführt. Führen Sie vor einem großen Pull Request die vollständige Suite aus.

**Verändern Tests meine Installation?** Tests ohne Datenbank nicht; Datenbanktests brauchen eine Wegwerf-Datenbank.
Richten Sie `runtests.php` niemals auf eine Datenbank, die Ihnen wichtig ist.

**Wo sind die Tests von Erweiterungen?** Einige Erweiterungen haben Suites in `phpunit.xml`; eine eigene Erweiterung
führt ihre Tests in ihrem eigenen Repository (Lektion 12.2).

### Stolperfallen

- **Mit voller Geschwindigkeit auf einem gemeinsam genutzten Server ausführen.** Verwenden Sie `nice -n 15` und
  immer nur eine Suite auf einmal.
- **Fehlende Entwicklungsabhängigkeiten.** `vendor/bin/phpunit` existiert erst nach einem `composer install` mit
  Entwicklungspaketen.
- **Nur die Punkte lesen.** Lesen Sie die Zusammenfassungszeile und jedes `F`, `E`, `W` oder `S` darüber.

## Praktische Übung

Auf der Entwicklungsinstallation, aus ihrem Wurzelverzeichnis.

1. Listen Sie die Suites auf: `grep -o '<testsuite name="[^"]*"' phpunit.xml`.
2. Zählen Sie die Testdateien: `find tests/tests -name "*Test.php" | wc -l`.
3. Führen Sie die Security-Suite aus: `nice -n 15 vendor/bin/phpunit --testsuite security --no-coverage`.
4. Führen Sie eine Testdatei über ihren Pfad aus, zum Beispiel einen Test der Template-Engine: Suchen Sie einen mit
   `ls tests/tests/lib/eztemplate/ | head` und führen Sie ihn mit `vendor/bin/phpunit <path>` aus.
5. Finden Sie über die Zuordnung, welche Tests die Klasse `eZINI` abdecken: `grep -n "ezini" tests/test-map.json | head`.
6. Lesen Sie einen Test von `eZINI` und notieren Sie einen Methodenaufruf, den Sie noch nicht kannten.
7. Lesen Sie die Ausschlüsse des Standardlaufs: `grep -n -A16 "<groups>" phpunit.xml`.

## Erwartetes Ergebnis

- Schritt 1 listet mehr als zwanzig Suites.
- Schritt 3 endet mit `OK (<n> tests, <m> assertions)`.
- Schritt 4 endet mit `OK` oder erklärt einen übersprungenen Test mit `S`.
- Schritt 7 zeigt die ausgeschlossenen Gruppen, darunter `database`.
- Der Trainer fragt alle Teilnehmenden, welche Suite sie nach der Änderung eines Datentyps ausführen würden.

## Quiz

1. Welche Datei definiert die Test-Suites?
2. Welche Gruppe schließt der Standardlauf aus?
   A. `security` B. `database` C. `design` D. `lib`
3. Wie führen Sie nur die Suite `security` aus?
4. Was tun Suites für laufende Installationen, wenn keine installierte Website vorhanden ist?
5. Wofür ist `tests/test-map.json` da?

<details>
<summary>Antworten</summary>

1. `phpunit.xml` im Wurzelverzeichnis der Installation.
2. **B.** `database` (zusammen mit `mail-live`, `mongodb-live` und `network-live`).
3. `vendor/bin/phpunit --testsuite security`.
4. Sie überspringen jede Klasse mit einer Begründung.
5. Sie ordnet Quelldateien den Tests zu, die sie abdecken, damit zuerst die Tests einer Änderung ausgeführt werden
   können.

</details>

## Weiterführende Literatur

- [Tests und Continuous Integration](../../../../../eng-us/standards/testing-and-ci.md).
- [Qualitätsprüfungen](../../../../../eng-us/features/6.0/quality-checks.md): Hooks, Manifest, Codestil, PHPStan, die Tests
  einer Änderung.
- [Continuous Integration](../../../../../eng-us/bc/6.0/ci.md).

## Verwandte Seiten

- [Modul 12: Tests und Qualität](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
