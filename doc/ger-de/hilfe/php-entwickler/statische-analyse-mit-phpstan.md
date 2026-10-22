---
title: Statische Analyse mit PHPStan
audience: php-developer, developer
category: php-development
order: 100
summary: Wie Exponential PHPStan verwendet - Level 1 über kernel/ und lib/ mit einer Baseline der vorhandenen Befunde, was ausgeschlossen ist und warum, die Ausführung lokal und in der CI, einen Befund lesen, ihn beheben statt in die Baseline aufzunehmen, und die eigene Erweiterung analysieren.
keywords: PHPStan, statische Analyse, Baseline, Befund, Analyse von Erweiterungen, static analysis, phpstan.neon.dist, phpstan-baseline.neon, level 1, baseline, make phpstan, make phpstan-baseline, pre-push hook, treatPhpDocTypesAsCertain, excludePaths, .devtools, extension analysis
type: help
---
# Statische Analyse mit PHPStan

Diese Seite richtet sich an PHP-Entwickler, die zu Exponential committen oder dasselbe Sicherheitsnetz für eine
Erweiterung möchten. PHPStan liest den Code, ohne ihn auszuführen, und meldet Aufrufe nicht existierender Methoden,
falsche Argumentanzahlen, undefinierte Variablen und ähnliche Fehler. In Exponential läuft es auf Level 1 mit einer
Baseline, sodass nur Befunde fehlschlagen, die Ihre Änderung hinzufügt.

## Inhalt

1. [Die Konfiguration](#die-konfiguration)
2. [Ausführen](#ausführen)
3. [Die Baseline](#die-baseline)
4. [Einen Befund lesen und beheben](#einen-befund-lesen-und-beheben)
5. [Was ausgeschlossen ist und warum](#was-ausgeschlossen-ist-und-warum)
6. [Ihre Erweiterung analysieren](#ihre-erweiterung-analysieren)

## Die Konfiguration

`phpstan.neon.dist`:

| Parameter | Wert | Bedeutung |
|---|---|---|
| `includes` | `phpstan-baseline.neon` | die bekannten Befunde, ignoriert |
| `level` | `1` | undefinierte Variablen, unbekannte Klassen und Methoden, falsche Argumentanzahlen und die Prüfungen von Level 0 |
| `phpVersion` | `80100` | der Code wird als PHP 8.1 analysiert |
| `paths` | `kernel`, `lib` | was analysiert wird |
| `excludePaths` | `vendor/*`, `var/*`, `*/tests/*`, `kernel/private/classes/commands/*`, `.../dfs_filter_iterator.php` | siehe unten |
| `bootstrapFiles` | `autoload.php` | die Klassen-Maps, damit PHPStan die Klassen des Kernels findet |
| `tmpDir` | `.devtools/phpstan` | der Ergebnis-Cache |
| `treatPhpDocTypesAsCertain` | `false` | Docblock-Typen leiten die Analyse, aber ein defensives `is_array()` auf einen als Array dokumentierten Wert ist kein Fehler |

Da der Code keine Typdeklarationen hat, stammt das Typwissen von PHPStan aus Docblocks; gute Zeilen `@param` und
`@return` verbessern die Analyse ([Warum der Code keine Typdeklarationen hat](warum-keine-typdeklarationen.md)).

## Ausführen

```bash
make devtools          # once: PHPStan into .devtools/ (composer.dev.json), never into vendor/
make phpstan           # .devtools/vendor/bin/phpstan analyse -c phpstan.neon.dist --memory-limit=4G
```

Der pre-push-Hook führt denselben Befehl aus, sodass ein Push mit einem neuen Befund abgelehnt wird, sofern Sie nicht
`--no-verify` verwenden ([Git-Hooks und make-Ziele](git-hooks-und-make-ziele.md)). In der CI läuft der Job **PHPStan**
des Quality-Workflows unter PHP 8.3 und blockiert.

## Die Baseline

`phpstan-baseline.neon` listet jeden Befund auf, den der Code hatte, als die Prüfung eingeführt wurde (damals 3033),
jeweils mit Meldung, Bezeichner, Anzahl und Pfad. Ein Befund in der Baseline wird ignoriert; ein neuer schlägt fehl.
Zwei Regeln:

- **Beheben Sie neue Befunde; nehmen Sie sie nicht in die Baseline auf.** `make phpstan-baseline` akzeptiert alle
  aktuellen Befunde und ist für die seltene Änderung gedacht, die das Level anhebt oder die Baseline neu ordnet, in
  einer eigenen Änderung.
- **Verkleinern Sie die Baseline, wenn Sie ohnehin in der Datei sind.** Wenn Sie einen Befund aus der Baseline beheben,
  passt PHPStan diesen Eintrag nicht mehr an; `reportUnmatchedIgnoredErrors: false` verhindert, dass das fehlschlägt,
  und eine spätere Neuerzeugung der Baseline entfernt den Eintrag.

## Einen Befund lesen und beheben

Ein Befund nennt die Datei, die Zeile, die Meldung und einen Bezeichner:

```text
 ------ ------------------------------------------------------------
  Line   extension/acmereport/classes/acmereportservice.php
 ------ ------------------------------------------------------------
  42     Call to an undefined method acmeReportItem::fetchByOwner().
         method.notFound
 ------ ------------------------------------------------------------
```

| Häufige Meldung | Übliche Ursache | Lösung |
|---|---|---|
| `Variable $x might not be defined` | nur in einem Zweig zugewiesen | initialisieren Sie sie vor dem Zweig |
| `Call to an undefined method` | ein Tippfehler oder eine Methode einer anderen Klasse | verwenden Sie die richtige Methode; prüfen Sie `kernel/classes/` |
| `... invoked with 3 parameters, 2 required` | die Signatur hat sich geändert | passen Sie den Aufruf an |
| `Access to an undefined property` | eine dynamische Eigenschaft | deklarieren Sie sie in der Klasse |
| `Instantiated class X not found` | ein fehlender Eintrag in der Klassen-Map oder ein Tippfehler | generieren Sie die Autoloads neu; korrigieren Sie den Namen |

Viele Befunde der Form `Variable $Params might not be defined` in der Baseline stammen aus View-Dateien, die `$Params`
von `eZProcess::runFile()` erhalten. Die klassenbasierten Views (`Exponential\Runnable\ModuleView`) nehmen sie
stattdessen aus `$scope` und haben das Problem nicht ([Modul-Views als Klassen](modul-views-als-klassen.md)).

## Was ausgeschlossen ist und warum

| Pfad | Grund (aus `phpstan.neon.dist`) |
|---|---|
| `vendor/*`, `var/*` | Code von Drittanbietern und generierter Code |
| `*/tests/*` | Tests werden geprüft, indem man sie ausführt |
| `kernel/private/classes/commands/*` | Befehlsklassen, die auch globale Hilfsfunktionen deklarieren; zwei davon in einem Analyseprozess zu laden deklariert sie erneut |
| `kernel/private/classes/clusterfilehandlers/dfsbackends/dfs_filter_iterator.php` | seinen Iterator-Methoden fehlen die vorläufigen Rückgabetypen von PHP 8.1 oder `#[\ReturnTypeWillChange]`, was PHPStan nicht in die Baseline aufnehmen kann |

## Ihre Erweiterung analysieren

PHPStan ist nicht auf den Kernel beschränkt. Mit installierten Entwicklungswerkzeugen richten Sie es mit einer kleinen
eigenen Konfiguration, die den Autoloader des Kernels bootstrapt, auf Ihre Erweiterung:

```neon
# extension/acmereport/phpstan.neon
parameters:
    level: 1
    paths:
        - .
    excludePaths:
        - tests/*
    bootstrapFiles:
        - ../../autoload.php
```

```bash
.devtools/vendor/bin/phpstan analyse -c extension/acmereport/phpstan.neon --memory-limit=2G
```

Generieren Sie zuerst die Autoloads neu (`php bin/php/ezpgenerateautoloads.php -e`), damit Ihre eigenen Klassen bekannt
sind. Beginnen Sie wie der Kernel auf Level 1 und heben Sie das Level an, wenn die Befunde verschwunden sind.

## Verwandte Seiten

- [Qualitätsprüfungen](../../funktionen/6.0/qualitaetspruefungen.md#phpstan)
- [Der Coding-Standard (phpcs.xml.dist)](der-coding-standard.md)
- [CI und das Nachstellen einer roten Prüfung](ci-und-eine-rote-pruefung-nachstellen.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
