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
| Vollständig: Oberfläche und Inhalte | Die Administration spricht die Sprache vollständig; Inhalte, Datumsangaben, Zahlen und Währung folgen der Locale | [Sprachen in Exponential, Unterstützungsstufen](../../sprachen.md#2-unterstützungsstufen) |
| Teilweise Oberfläche, vollständige Inhalte | Eine ältere Übersetzung deckt einen Teil der heutigen Texte ab; der Rest erscheint auf Englisch | ebenda |
| Nur Inhalte | Keine Übersetzung der Administration; Inhalte können in der Sprache geschrieben und für sie formatiert werden | ebenda |

Fragen Sie immer „für die Bildschirme der Redaktion oder für Inhalte?“, bevor Sie „Wird meine Sprache unterstützt?“
beantworten.

## Die Punkte

| Sagen Sie | Quelle |
|---|---|
| „355 Locale-Dateien können als Inhaltssprachen genutzt werden.“ | [Sprachen in Exponential](../../sprachen.md), [Sprachen und Barrierefreiheit](../wer-ist-die-exponential-foundation.md#sprachen-und-barrierefreiheit) |
| „43 davon werden von rechts nach links geschrieben.“ | [Sprachen und Barrierefreiheit](../wer-ist-die-exponential-foundation.md#sprachen-und-barrierefreiheit) |
| „Es gibt Übersetzungen der Administration (die aktuellen Gesamtzahlen stehen am Anfang der Seite [Sprachen in Exponential](../../sprachen.md)).“ | ebenda |
| „Jede Übersetzung eines Inhalts wird einzeln veröffentlicht, und Kennzeichen zeigen, was es gibt und was fehlt.“ | [Sprachen](../sprachen.md) |
| „Jede redaktionelle Person wählt die Sprache ihrer eigenen Administration.“ | [Sprachen](../sprachen.md) |
| „Ein Land ohne eigene Übersetzung fällt auf eine verwandte zurück, zum Beispiel jedes deutschsprachige Land auf Deutsch.“ | [Sprachen in Exponential, Unterstützungsstufen](../../sprachen.md#2-unterstützungsstufen) |
| „In einer Sprache mit Schreibrichtung von rechts nach links dreht sich die Administration um, und Inhalte bekommen `dir=\"rtl\"`.“ | [Von rechts nach links](../../funktionen/6.0/rechts-nach-links.md) |
| „Für die Sprachen wird zur Laufzeit keine PHP-Erweiterung gebraucht.“ | [Sprachen in Exponential, Abschnitt 10](../../sprachen.md#10-keine-php-extension-zur-laufzeit) |

## Zeigen

Auf einer Installation, die Sie kontrollieren:

1. Öffnen Sie einen Inhalt; zeigen Sie auf die ausgefüllten und gestrichelten Sprachkennzeichen.
2. Klicken Sie auf ein gestricheltes Kennzeichen, **Übersetzung basiert auf** der vorhandenen Sprache, veröffentlichen.
3. Wählen Sie in der rechten Spalte unter **Oberfläche** Hebräisch oder Arabisch, **Anwenden**: Die Administration dreht
   sich um.
4. Stellen Sie zurück.

Fassung mit Zeitangaben: [Demo-Skripte, Teil 3: Sprachen und von rechts nach links](../geschaeft/demo-drehbuecher.md#teil-3-sprachen-und-schreibrichtung-von-rechts-nach-links-3-minuten).

## Sagen Sie nicht

- „Exponential gibt es in 355 Sprachen.“ Sagen Sie: „355 Locale-Dateien als Inhaltssprachen; Übersetzungen der Administration (die aktuellen Gesamtzahlen stehen am Anfang der Seite [Sprachen in Exponential](../../sprachen.md)).“
- „Alle Übersetzungen sind perfekt.“ Mehrere vollständige Übersetzungen wurden maschinell unterstützt erstellt; ein
  Gegenlesen ist „der schnellste Gewinn“ ([Sprachen in Exponential, Abschnitt 8](../../sprachen.md#8-eine-übersetzung-der-administrationsoberfläche-hinzufügen-oder-vervollständigen)).
- „Sie können so viele Inhaltssprachen nutzen, wie Sie möchten.“ Es gibt eine dokumentierte Grenze von 62 Inhaltssprachen
  pro Installation auf 64-Bit-PHP ([Abschnitt 11](../../sprachen.md#11-die-grenze-von-62-inhaltssprachen)).

## Aus einer Sprachgemeinschaft Beitragende machen

Sprachgemeinschaften sind natürliche Fürsprecher. Wenn Sie einer begegnen:

1. Zeigen Sie ihr den Stand ihrer Sprache ([Abschnitt 4](../../sprachen.md#4-alle-übersetzungen-der-administrationsoberfläche)).
2. Wenn sie vollständig, aber maschinell unterstützt ist, laden Sie Gegenleser ein
   ([Eine maschinell unterstützte Übersetzung gegenlesen](maschinell-unterstuetzte-uebersetzung-gegenlesen.md)).
3. Wenn sie teilweise vorhanden ist oder fehlt, laden Sie Übersetzer ein ([Bei Übersetzungen helfen](bei-uebersetzungen-helfen.md)).
4. Schlagen Sie einen Übersetzungs-Sprint vor ([Einen Beitrags-Sprint vorschlagen](einen-beitrags-sprint-vorschlagen.md)).
5. Übersetzen Sie auch eine Hilfeseite: „Eine übersetzte Seite hilft jedem Leser dieser Sprache“
   ([Freie Dokumentation](../freie-dokumentation.md#anderen-helfen)).

## Verwandte Seiten

- [Bei Übersetzungen helfen](bei-uebersetzungen-helfen.md)
- [Sprachen in Exponential](../../sprachen.md)
- [Mehrere Websites und Sprachen planen](../fuehrung/multisite-und-mehrsprachigkeit-planen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
