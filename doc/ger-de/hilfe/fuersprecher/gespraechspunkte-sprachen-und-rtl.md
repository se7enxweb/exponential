---
title: "Gesprächspunkte: Sprachen und Schreibrichtung von rechts nach links"
audience: advocate
category: advocacy
order: 18
summary: Belegte Gesprächspunkte zu den Sprachen von Exponential (Locale-Dateien, Inhaltssprachen, Übersetzungen der Administration und ihre Stufen, Rückfallsprachen, Schriften von rechts nach links) und wie Fürsprecher aus einer Sprachgemeinschaft Beitragende machen.
keywords: Gesprächspunkte, Sprachen, mehrsprachig, Übersetzungen, Locales, Inhaltssprachen, Übersetzung der Administration, von rechts nach links, RTL, Hebräisch, Arabisch, Rückfallsprache, Unterstützungsstufen, Übersetzer, talking points, languages, multilingual, translations, locales, content languages, administration translation, right to left, Hebrew, Arabic, fallback, support levels, translators
type: help
---
# Gesprächspunkte: Sprachen und Schreibrichtung von rechts nach links

Diese Seite ist für Fürsprecher, die mit mehrsprachigen Organisationen und mit Sprachgemeinschaften sprechen. Sprachen
sind eine der stärksten Geschichten von Exponential, und eine, bei der es auf Genauigkeit ankommt: Eine „unterstützte“
Sprache kann sehr Verschiedenes bedeuten.

## Inhalt

1. [Drei Arten von Sprachunterstützung](#drei-arten-von-sprachunterstützung)
2. [Die Punkte](#die-punkte)
3. [Zeigen](#zeigen)
4. [Sagen Sie nicht](#sagen-sie-nicht)
5. [Aus einer Sprachgemeinschaft Beitragende machen](#aus-einer-sprachgemeinschaft-beitragende-machen)

## Drei Arten von Sprachunterstützung

| Stufe | Was funktioniert | Quelle |
|---|---|---|
| Vollständig: Oberfläche und Inhalte | Die Administration spricht die Sprache vollständig; Inhalte, Datumsangaben, Zahlen und Währung folgen der Locale | [Sprachen in Exponential, Unterstützungsstufen](../../../en/us/languages.md#2-support-levels) |
| Teilweise Oberfläche, vollständige Inhalte | Eine ältere Übersetzung deckt einen Teil der heutigen Texte ab; der Rest erscheint auf Englisch | ebenda |
| Nur Inhalte | Keine Übersetzung der Administration; Inhalte können in der Sprache geschrieben und für sie formatiert werden | ebenda |

Fragen Sie immer „für die Bildschirme der Redaktion oder für Inhalte?“, bevor Sie „Wird meine Sprache unterstützt?“
beantworten.

## Die Punkte

| Sagen Sie | Quelle |
|---|---|
| „268 Locale-Dateien können als Inhaltssprachen genutzt werden.“ | [Sprachen in Exponential](../../../en/us/languages.md), [Sprachen und Barrierefreiheit](../wer-ist-die-exponential-foundation.md#sprachen-und-barrierefreiheit) |
| „40 davon werden von rechts nach links geschrieben.“ | [Sprachen und Barrierefreiheit](../wer-ist-die-exponential-foundation.md#sprachen-und-barrierefreiheit) |
| „Es gibt 32 Übersetzungen der Administration, zehn davon vollständig.“ | ebenda |
| „Jede Übersetzung eines Inhalts wird einzeln veröffentlicht, und Kennzeichen zeigen, was es gibt und was fehlt.“ | [Sprachen](../sprachen.md) |
| „Jede redaktionelle Person wählt die Sprache ihrer eigenen Administration.“ | [Sprachen](../sprachen.md) |
| „Ein Land ohne eigene Übersetzung fällt auf eine verwandte zurück, zum Beispiel jedes deutschsprachige Land auf Deutsch.“ | [Sprachen in Exponential, Unterstützungsstufen](../../../en/us/languages.md#2-support-levels) |
| „In einer Sprache mit Schreibrichtung von rechts nach links dreht sich die Administration um, und Inhalte bekommen `dir=\"rtl\"`.“ | [Von rechts nach links](../../../en/us/features/6.0/right-to-left.md) |
| „Für die Sprachen wird zur Laufzeit keine PHP-Erweiterung gebraucht.“ | [Sprachen in Exponential, Abschnitt 10](../../../en/us/languages.md#10-no-php-extension-at-run-time) |

## Zeigen

Auf einer Installation, die Sie kontrollieren:

1. Öffnen Sie einen Inhalt; zeigen Sie auf die ausgefüllten und gestrichelten Sprachkennzeichen.
2. Klicken Sie auf ein gestricheltes Kennzeichen, **Übersetzung basiert auf** der vorhandenen Sprache, veröffentlichen.
3. Wählen Sie in der rechten Spalte unter **Oberfläche** Hebräisch oder Arabisch, **Anwenden**: Die Administration dreht
   sich um.
4. Stellen Sie zurück.

Fassung mit Zeitangaben: [Demo-Skripte, Teil 3: Sprachen und von rechts nach links](../geschaeft/demo-drehbuecher.md#teil-3-sprachen-und-schreibrichtung-von-rechts-nach-links-3-minuten).

## Sagen Sie nicht

- „Exponential gibt es in 268 Sprachen.“ Sagen Sie: „268 Locale-Dateien als Inhaltssprachen; 32 Übersetzungen der
  Administration, zehn vollständig.“
- „Alle Übersetzungen sind perfekt.“ Mehrere vollständige Übersetzungen wurden maschinell unterstützt erstellt; ein
  Gegenlesen ist „der schnellste Gewinn“ ([Sprachen in Exponential, Abschnitt 8](../../../en/us/languages.md#8-adding-or-completing-an-administration-translation)).
- „Sie können so viele Inhaltssprachen nutzen, wie Sie möchten.“ Es gibt eine dokumentierte Grenze von 62 Inhaltssprachen
  pro Installation auf 64-Bit-PHP ([Abschnitt 11](../../../en/us/languages.md#11-the-limit-of-62-content-languages)).

## Aus einer Sprachgemeinschaft Beitragende machen

Sprachgemeinschaften sind natürliche Fürsprecher. Wenn Sie einer begegnen:

1. Zeigen Sie ihr den Stand ihrer Sprache ([Abschnitt 4](../../../en/us/languages.md#4-every-administration-translation)).
2. Wenn sie vollständig, aber maschinell unterstützt ist, laden Sie Gegenleser ein
   ([Eine maschinell unterstützte Übersetzung gegenlesen](maschinell-unterstuetzte-uebersetzung-gegenlesen.md)).
3. Wenn sie teilweise vorhanden ist oder fehlt, laden Sie Übersetzer ein ([Bei Übersetzungen helfen](bei-uebersetzungen-helfen.md)).
4. Schlagen Sie einen Übersetzungs-Sprint vor ([Einen Beitrags-Sprint vorschlagen](einen-beitrags-sprint-vorschlagen.md)).
5. Übersetzen Sie auch eine Hilfeseite: „Eine übersetzte Seite hilft jedem Leser dieser Sprache“
   ([Freie Dokumentation](../freie-dokumentation.md#anderen-helfen)).

## Verwandte Seiten

- [Bei Übersetzungen helfen](bei-uebersetzungen-helfen.md)
- [Sprachen in Exponential](../../../en/us/languages.md)
- [Mehrere Websites und Sprachen planen](../fuehrung/multisite-und-mehrsprachigkeit-planen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
