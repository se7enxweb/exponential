---
title: Statische Analyse und Codestil
audience: software-engineer, developer
category: software-engineering
order: 520
summary: Wie Exponential Code prüft, ohne ihn auszuführen - PHPStan auf Stufe 1 gegen eine Baseline geerbter Befunde, PHP_CodeSniffer mit dem eigenen Stil und den eigenen Sniffs des Projekts (keine Typdeklarationen von PHP 7+, Kompatibilität mit PHP 8.0), Prüfungen, die nur an dem scheitern, was eine Änderung hinzufügt, die make-Ziele und Hooks und wie Sie die Baseline verkleinern.
keywords: statische Analyse, PHPStan, Baseline, phpstan-baseline.neon, Stufe, PHP_CodeSniffer, phpcs, phpcs.xml.dist, Codestil, Sniffs, PHPCompatibility, Typdeklarationen, make devtools, make phpstan, Softwareingenieur, static analysis, baseline, level, code style, sniffs, type declarations
type: help
---
# Statische Analyse und Codestil

Diese Seite richtet sich an Ingenieure, die die statischen Prüfungen ausführen, prüfen oder verbessern. Der Ansatz passt
zu einer alten, großen Codebasis: Die Prüfungen wurden eingeführt, ohne vorhandenen Code umzuformatieren, und sie scheitern
nur an dem, was eine Änderung hinzufügt. Die Referenz ist [Qualitätsprüfungen](../../funktionen/6.0/qualitaetspruefungen.md).

## Inhalt

1. [Die Werkzeuge einrichten](#die-werkzeuge-einrichten)
2. [PHPStan und seine Baseline](#phpstan-und-seine-baseline)
3. [Der Codestil](#der-codestil)
4. [Warum keine Typdeklarationen](#warum-keine-typdeklarationen)
5. [Nur was eine Änderung hinzufügt](#nur-was-eine-änderung-hinzufügt)
6. [Die Baseline verbessern](#die-baseline-verbessern)

## Die Werkzeuge einrichten

```bash
make devtools   # PHP_CodeSniffer and PHPStan into .devtools/ (composer.dev.json), never into vendor/
make hooks      # git config core.hooksPath .githooks
make check      # lint, manifest-check, phpcs and phpstan, as CI does
```

Die Entwicklungswerkzeuge liegen in `.devtools/`, aufgelöst aus `composer.dev.json` für PHP 8.0.30, sodass sie das
`vendor/` der Installation nie ändern.

## PHPStan und seine Baseline

| Einstellung | Wert (`phpstan.neon.dist`) |
|---|---|
| Stufe | 1 |
| PHP-Version | 8.1 (`phpVersion: 80100`) |
| Pfade | `kernel`, `lib` |
| Ausgeschlossen | `vendor/*`, `var/*`, Tests, die Befehlsklassen in `kernel/private/classes/commands/` (sie deklarieren globale Hilfsfunktionen), `dfs_filter_iterator.php` (braucht zuerst vorläufige Rückgabetypen) |
| Baseline | `phpstan-baseline.neon`: die Befunde, die der Code hatte, als die Prüfung eingeführt wurde |

Nur Befunde, die in der Baseline fehlen, lassen die Prüfung scheitern. `make phpstan` führt sie aus; der Pre-Push-Hook
führt sie aus, wenn die Werkzeuge installiert sind; die CI führt sie auf PHP 8.3 aus und blockiert.

## Der Codestil

`phpcs.xml.dist` beschreibt den Stil von Exponential 6.0.15; jede Regel wurde vor ihrer Aufnahme am vorhandenen Code
gemessen:

- `array( ... )` statt `[ ... ]`;
- ein Leerzeichen innerhalb der Klammern von Kontrollstrukturen, Aufrufen und Deklarationen: `if ( $a )`, `foo( $a, $b )`;
- die öffnende geschweifte Klammer einer Funktion in eigener Zeile;
- `!$a` ohne Leerzeichen; Schlüsselwörter und Konstanten kleingeschrieben;
- Unix-Zeilenenden, Leerzeichen statt Tabulatoren, keine Leerzeichen am Zeilenende;
- keine aus PHP entfernten Funktionen.

PHPCompatibility prüft gegen `testVersion` `8.0-`: keine Syntax und keine Funktionen neuer als PHP 8.0, sofern nicht durch
`function_exists()` abgesichert oder in `lib/phpcompat.php` bereitgestellt ([Unterstützung von PHP 8.0](../../bc/6.0/php-8.0-unterstuetzung.md)).
Die eigenen Sniffs des Projekts liegen in `bin/phpcs/Exponential/` und haben Tests
(`php vendor/bin/phpunit --testsuite quality`).

In der CI ist der Codestil vorerst eine Warnung; der Pre-Commit-Hook lehnt einen Commit ab, der Verstöße hinzufügt.

## Warum keine Typdeklarationen

Der Sniff `bin/phpcs/Exponential/Sniffs/TypeDeclarations/` meldet `declare( strict_types=1 )`, skalare, Pseudo- und
Union-Parametertypen, Rückgabetypen, Eigenschaftstypen und Konstantentypen. Die Parameterhinweise, die schon PHP 5 hatte,
bleiben erlaubt: `array`, `callable`, `self` und Klassennamen, auch nullbar (`?array`, `?eZINI`). Dateien unter `tests/`
dürfen das `: void` deklarieren, das PHPUnit verlangt.

Der Grund ist Kompatibilität: Der Kernel muss auf PHP 8.0 laufen, und Typen an vorhandenen Signaturen würden jede
Erweiterung brechen, die sie überschreibt oder erweitert. Ingenieure, die streng typisiertes PHP gewohnt sind, sollten
Typen stattdessen in Docblocks dokumentieren, die PHPStan liest.

## Nur was eine Änderung hinzufügt

| Prüfung | Wie sie vergleicht |
|---|---|
| Codestil | die Zahl der Verstöße jeder geänderten Datei gegen ihre vorherige Fassung |
| PHPStan | die Befunde gegen `phpstan-baseline.neon` |
| Syntax | jede geänderte Datei, mit PHP 8.3 und PHP 8.0 |

Von einer Änderung, die eine alte Datei berührt, wird nicht verlangt, die ganze Datei zu korrigieren; sie darf sie nur
nicht verschlechtern.

```bash
make lint                 # php -l on the PHP files changed since origin/main
make lint80               # the same with PHP 8.0
make phpcs BASE=origin/main
make phpstan
```

## Die Baseline verbessern

Erhöhen Sie die PHPStan-Stufe oder verkleinern Sie die Baseline in kleinen, getrennten Änderungen, und erzeugen Sie sie
mit `make phpstan-baseline` neu. Erzeugen Sie die Baseline nie neu, um einen neuen Befund durchzulassen: Das versteckt den
Befund, als wäre er immer da gewesen. Dasselbe gilt für den Codestil: Korrigieren Sie den Code, statt einen Sniff
abzuschwächen.

## Verwandte Seiten

- [Codierstandards](../../standards/programmierstandards.md)
- [Standards für PHP, PHP-FIG und Composer](../../standards/php-und-composer.md)
- [Mit technischen Schulden umgehen](technische-schulden.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
