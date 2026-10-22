---
title: Erste Schritte der Inhaltsmodellierung für Anwender
audience: adopter, project-manager, editor, developer
category: adoption
order: 34
summary: Die ersten Schritte beim Entwurf eines Inhaltsmodells für Exponential während einer Einführung - Bestandsaufnahme, Inhaltstypen und ihre Felder, Verknüpfungen, der Inhaltsbaum, Sprachen und Websites und die Fehler, das alte System zu kopieren - mit einem Arbeitsblatt.
keywords: Inhaltsmodell, Inhaltsklassen, Inhaltstypen, Felder, Attribute, Verknüpfungen, Inhaltsbaum, Sprachen, Bestandsaufnahme, Workshop, Arbeitsblatt, content model, content classes, content types, fields, attributes, relations, content tree, languages, inventory, workshop, worksheet
type: help
---
# Erste Schritte der Inhaltsmodellierung für Anwender

Die "most notable feature is its fully customizable and extendable content model", die bemerkenswerteste Eigenschaft
von Exponential ist also sein vollständig anpassbares und erweiterbares Inhaltsmodell (`README.md`). Anwender
profitieren am meisten, wenn sie dieses Modell für ihre Inhalte entwerfen, statt das alte System zu kopieren.

## Inhalt

1. [Bestandsaufnahme](#bestandsaufnahme)
2. [Von der Bestandsaufnahme zu Inhaltstypen](#von-der-bestandsaufnahme-zu-inhaltstypen)
3. [Verknüpfungen, Baum und Sprachen](#verknüpfungen-baum-und-sprachen)
4. [Fehler, die Sie vermeiden](#fehler-die-sie-vermeiden)
5. [Ein Arbeitsblatt](#ein-arbeitsblatt)

## Bestandsaufnahme

Listen Sie auf, was Sie heute veröffentlichen: Seiten, Artikel, Produkte, Veranstaltungen, Personen, Dokumente. Zählen
Sie für jede Art die Objekte und notieren Sie, wer sie schreibt, wie oft und in welchen Sprachen.

## Von der Bestandsaufnahme zu Inhaltstypen

In Exponential sind Inhaltstypen "user defined content classes", selbst definierte Inhaltsklassen mit Feldern
(`README.md`). Für jede Art von Objekt:

- geben Sie ihr einen Inhaltstyp mit einem klaren Namen;
- listen Sie seine Felder auf: Titel, Zusammenfassung, Haupttext, Bild, Datum, Preis, Verknüpfung ...;
- kennzeichnen Sie, welche Felder Pflicht sind und welche übersetzt werden;
- entscheiden Sie, welche vorhandenen Inhaltstypen eines Seitenpakets Sie wiederverwenden können.

Workshops, um das mit den Beteiligten zu erarbeiten: [Workshops zur Inhaltsmodellierung](../../fuehrung/workshops-zur-inhaltsmodellierung.md).

## Verknüpfungen, Baum und Sprachen

| Entscheidung | Fragen |
|---|---|
| Verknüpfungen | Welche Objekte verweisen auf andere (ein Artikel auf seinen Autor, ein Produkt auf seine Kategorie)? |
| Der Inhaltsbaum | Wo liegt jede Art von Objekt? Welche Sektionen brauchen andere Rechte? |
| Sprachen | Welche Inhalte werden übersetzt, und welche gibt es nur in einer Sprache? ([Sprachen](../../sprachen.md)) |
| Websites | Werden Inhalte zwischen Websites geteilt? ([Mehrere Websites und Sprachen planen](../../fuehrung/multisite-und-mehrsprachigkeit-planen.md)) |

## Fehler, die Sie vermeiden

- Ein allgemeiner Typ "Seite" mit einem großen Feld für formatierten Text für alles: Sie verlieren Struktur, die
  Listen, Suche und Apps nutzen könnten.
- Felder, die nach den technischen Eigenheiten des alten Systems benannt sind.
- Für das heutige Design modellieren statt für die Inhalte.
- Die Redakteure übergehen: Sie werden diese Felder jeden Tag ausfüllen.

## Ein Arbeitsblatt

```text
Inhaltstyp:          <Name>
Zweck:               <wofür er da ist>
Felder:              <Name - Typ - Pflicht? - übersetzt?>
Verknüpfungen:       <zu welchen Typen>
Ort im Baum:         <wo>
Wer ihn anlegt:      <Rolle>
Menge:               <Objekte heute, pro Monat>
```

## Verwandte Seiten

- [Die Einführung planen](README.md)
- [Workshops zur Inhaltsmodellierung](../../fuehrung/workshops-zur-inhaltsmodellierung.md)
- [Ein Machbarkeitsnachweis](../bewerten/machbarkeitsnachweis.md)
- [Checkliste zur Inhaltsmigration](../umziehen/checkliste-inhaltsmigration.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
