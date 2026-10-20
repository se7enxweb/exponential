---
title: Übersetzungen beitragen
audience: supporter
category: supporting
order: 50
summary: Wie Muttersprachler Exponential übersetzen – wo die Übersetzungskataloge liegen, welche Sprachen vollständig sind, wie Sie übersetzen und testen, die Prüfung durch einen zweiten Muttersprachler, Sprachen mit Schreibrichtung von rechts nach links und die Übersetzung der Dokumentation.
keywords: Übersetzung, Übersetzungen, übersetzen, Sprache, Gebietsschema, Katalog, translation.ts, i18n, Lokalisierung, von rechts nach links, RTL, Prüfung, translation, translations, translate, language, locale, catalogue, localization, right to left, review
type: help
---
# Übersetzungen beitragen

Diese Seite ist für Muttersprachler, die Exponential in ihrer Sprache haben möchten. Übersetzungen erreichen alle
Anwender dieser Sprache, oft Menschen, die nie ein englisches Handbuch lesen würden.

## Wo die Übersetzungen liegen

- Die Meldungen der Administration stehen in `share/translations/<locale>/translation.ts`, eine Datei pro Sprache
  (zum Beispiel `ger-DE`, `fre-FR`, `esl-ES`, `ukr-UA`, `heb-IL`).
- `doc/en/us/languages.md` führt die Gebietsschemas, die Übersetzungen der Administration und deren Vollständigkeit
  auf; die Hilfeseite [Sprachen](../sprachen.md) erklärt Sprachen für Redakteure und Administratoren.
- Erweiterungen bringen ihre eigenen Übersetzungen in ihren eigenen Verzeichnissen `translations/` mit.

## Wie Sie übersetzen

1. Wählen Sie eine Sprache und prüfen Sie ihre Vollständigkeit in `doc/en/us/languages.md`.
2. Erstellen Sie einen Fork des Repositorys und öffnen Sie die Datei `translation.ts` der Sprache.
3. Übersetzen Sie die Meldungen, die noch nicht übersetzt oder als unfertig markiert sind. Lassen Sie Platzhalter wie
   `%1` genau so, wie sie sind.
4. Testen Sie: Stellen Sie die Administration auf Ihre Sprache um und sehen Sie sich die übersetzten Bildschirme an.
5. Committen Sie mit einer Betreffzeile wie `Updated: German translation of the help dashboard` und eröffnen Sie einen
   Pull-Request ([Code beitragen](code-beitragen.md) beschreibt den Ablauf und die Regel zum Manifest).

## Qualität

- Verwenden Sie die Begriffe, die der Rest der Übersetzung bereits verwendet; Einheitlichkeit ist wichtiger als
  Eleganz.
- Bitten Sie einen zweiten Muttersprachler um eine Prüfung, bevor Sie einreichen.
- Übersetzen Sie nicht maschinell ohne Prüfung.
- Produktnamen bleiben, wie sie sind: Exponential, 7x, die Exponential Foundation.

## Sprachen mit Schreibrichtung von rechts nach links

Die Administration unterstützt `dir="rtl"` für Sprachen, die von rechts nach links geschrieben werden, etwa Arabisch
und Hebräisch ([Sprachen](../sprachen.md)). Wenn Sie eine solche Sprache übersetzen, prüfen Sie das Layout der
Bildschirme, nicht nur die Wörter.

## Die Dokumentation übersetzen

Hilfeseiten sind freie Dokumentation unter der GFDL 1.3 oder höher, Sie dürfen sie also übersetzen
([Freie Dokumentation](../freie-dokumentation.md)). Stimmen Sie mit den Maintainern in einem Issue ab, wie übersetzte
Seiten organisiert werden, bevor Sie viele Seiten übersetzen.

## Verwandte Seiten

- [Sprachen](../sprachen.md)
- [Dokumentation beitragen](dokumentation-beitragen.md)
- [Code beitragen](code-beitragen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
