---
title: "Modul 12: Tests und Qualität"
audience: trainer, developer
category: training
order: 120
summary: Das zwölfte Modul des Kurses Entwickler. Die Teilnehmenden führen die PHPUnit-Suites des Kernels ohne Datenbank aus, schreiben einen Test für ihren eigenen Template-Operator und führen die Prüfungen aus, die CI auf jede Änderung anwendet: Syntax, Dateimanifest, Commit-Nachrichten, Hooks, Codestil, PHPStan und die Tests einer Änderung. 2 Stunden 25 Minuten.
keywords: entwickler, modul 12, tests, PHPUnit, phpunit.xml, ohne datenbank, DB-less, einen test schreiben, qualitätsprüfungen, checkmanifest, git-hooks, commit-nachrichten, phpstan, phpcs, CI, developers course, module 12, writing a test, quality checks, git hooks, commit messages
type: help
---
# Modul 12: Tests und Qualität

Bevor die Teilnehmenden Code teilen, lernen sie, zu belegen, dass er funktioniert, und die Prüfungen zu bestehen, die
das Projekt auf jede Änderung anwendet. Dieses Modul führt die Test-Suites des Kernels aus, lässt die Teilnehmenden
einen eigenen Test schreiben und geht die Prüfungen und Hooks durch, die den Hauptzweig grün halten.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Dauer | 2 Stunden 25 Minuten (mit einer Pause von 10 Minuten) |
| Lektionen | 3 |
| Niveau | Fortgeschrittene |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 12.1: Die Test-Suites](die-test-suiten.md) | 40 Minuten | Die Suites finden, die Suite `security` und eine einzelne Testdatei ausführen |
| [Lektion 12.2: Einen Test schreiben](einen-test-schreiben.md) | 50 Minuten | `mysite_initials` testen, den Code kaputt machen, den Test fehlschlagen sehen |
| [Lektion 12.3: Prüfungen, bevor Sie eine Änderung teilen](pruefungen-bevor-sie-eine-aenderung-teilen.md) | 45 Minuten | Hooks, Manifest, Commit-Nachrichten, Syntax und die Tests einer Änderung |

## Dieses Modul vorbereiten

- Die Installationen brauchen die Entwicklungsabhängigkeiten (`vendor/bin/phpunit` existiert nach `composer install`).
- Lektion 12.3 braucht den Kernel als Git-Klon mit dem Remote `origin`; die Teilnehmenden committen nur auf ihrem
  eigenen Zweig und pushen in diesem Modul niemals.
- Führen Sie Tests auf einem gemeinsam genutzten Schulungsserver mit `nice -n 15` und immer nur eine Suite auf einmal
  aus.
- `make devtools` lädt PHP_CodeSniffer und PHPStan herunter; führen Sie es vor dem Unterricht aus, wenn die
  Verbindung langsam ist.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 12.1 | 40 |
| Lektion 12.2 | 50 |
| Pause | 10 |
| Lektion 12.3 | 45 |
| **Gesamt** | **145** |

## Verwandte Seiten

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
