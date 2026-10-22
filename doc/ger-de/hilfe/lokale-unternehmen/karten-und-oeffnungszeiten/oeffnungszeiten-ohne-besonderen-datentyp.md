---
title: Öffnungszeiten ohne besonderen Datentyp
audience: local-business, businesses, customer, website-creator
category: local-business
order: 32
summary: Exponential hat keinen Datentyp für Öffnungszeiten; drei Wege, Öffnungszeiten mit dem Mitgelieferten abzubilden - ein Textabschnitt in einem gemeinsamen Objekt, eine kleine Klasse für Öffnungszeiten mit Zeit- und Matrixfeldern oder eine Tabelle im Rich Text - mit den Vor- und Nachteilen, den passenden Feldern und wie Sie die Zeiten in der Fußzeile und auf der Kontaktseite zeigen.
keywords: Öffnungszeiten, Geschäftszeiten, kein Datentyp, eztime, ezmatrix, eztext, Inhaltsklasse, Fußzeile, Kontaktseite, Modell, lokales Unternehmen, Restaurant, Geschäft, opening hours, business hours, no datatype, content class, footer, contact page, local business
type: help
---
# Öffnungszeiten ohne besonderen Datentyp

Diese Seite ist für Inhaber eines lokalen Unternehmens und die Person, die die Website baut. Besucher suchen nach
Öffnungszeiten öfter als nach fast allem anderen. Exponential hat **keinen Datentyp für Öffnungszeiten**: Die
Datentypen des Kernels in `kernel/classes/datatypes/` umfassen Text, Datum, Uhrzeit, Datum und Uhrzeit, Auswahl und
eine Matrix (Tabelle), aber nichts, das Wochentage und Öffnungszeiten kennt, und keine Erweiterung in der
Standard-`composer.json` fügt einen hinzu (geprüft am 20. Oktober 2026). Öffnungszeiten werden mit den vorhandenen
Datentypen abgebildet.

## Inhalt

1. [Drei Wege](#drei-wege)
2. [Möglichkeit 1: ein Textabschnitt in einem gemeinsamen Objekt](#möglichkeit-1-ein-textabschnitt-in-einem-gemeinsamen-objekt)
3. [Möglichkeit 2: eine kleine Klasse für Öffnungszeiten](#möglichkeit-2-eine-kleine-klasse-für-öffnungszeiten)
4. [Möglichkeit 3: eine Tabelle im Rich Text](#möglichkeit-3-eine-tabelle-im-rich-text)
5. [Die Öffnungszeiten überall zeigen](#die-öffnungszeiten-überall-zeigen)
6. [Was keine der Möglichkeiten kann](#was-keine-der-möglichkeiten-kann)
7. [Checkliste](#checkliste)

## Drei Wege

| | Möglichkeit 1: Textabschnitt | Möglichkeit 2: eigene Klasse | Möglichkeit 3: Tabelle im Rich Text |
|---|---|---|---|
| Datentypen | `eztext` | Felder `ezmatrix` oder `eztime`, `eztext` | `ezxmltext` |
| Eingerichtet von | Ersteller, eine Stunde | Ersteller, ein paar Stunden | niemand; Redakteure erledigen es |
| Leicht zu bearbeiten | ja | ja, feste Felder | ja, aber Tabellen im Rich Text sind fummelig |
| Überall gleiches Layout | wie eingetippt | ja, das Template entscheidet | wie eingetippt |
| Für strukturierte Daten nutzbar | nein | ja | nein |
| Passt zu | den meisten kleinen Unternehmen | Restaurants, Praxen, mehreren Standorten | einem schnellen Start |

Die Feldtypen werden in [Datentypen wählen](../../ersteller/website-ersteller/datentypen-waehlen.md) erklärt.

## Möglichkeit 1: ein Textabschnitt in einem gemeinsamen Objekt

Fügen Sie dem Objekt, das schon Adresse und Telefon enthält, ein Feld vom Typ **Textabschnitt** (`eztext`) namens
„Öffnungszeiten“ hinzu (siehe [Ihre Seiten planen](../einstieg/ihre-seiten-planen.md#schreiben-sie-jede-tatsache-nur-einmal)).
Der Redakteur tippt zum Beispiel:

```text
Monday to Friday   8:00 - 18:00
Saturday           9:00 - 14:00
Sunday             closed
```

Auf einer deutschen Website also etwa „Montag bis Freitag 8:00 - 18:00, Samstag 9:00 - 14:00, Sonntag geschlossen“,
eine Zeile pro Eintrag.

Fügen Sie ein zweites Textfeld „Hinweis“ für Ausnahmen hinzu („Am 1. Mai geschlossen“). Das Template gibt beide aus
und behält die Zeilenumbrüche bei.

## Möglichkeit 2: eine kleine Klasse für Öffnungszeiten

Der Ersteller legt eine Klasse an, zum Beispiel `opening_hours`, mit:

| Feld | Bezeichner | Datentyp | Hinweis |
|---|---|---|---|
| Titel | `title` | Textzeile | „Öffnungszeiten“ oder der Name des Standorts |
| Reguläre Öffnungszeiten | `regular_hours` | Matrix (`ezmatrix`) | Spalten: Tag, Öffnet, Schließt, Hinweis; eine Zeile pro Tag |
| Hinweis | `notice` | Textabschnitt | Feiertage, Ausnahmen |
| Hinweis gültig bis | `notice_until` | Datum | das Template blendet den Hinweis nach diesem Datum aus |

Die Matrix gibt jedem Tag dasselbe Format und lässt das Template sie als Tabelle darstellen oder strukturierte Daten
ausgeben ([Strukturierte Daten: was es gibt](../gefunden-werden/strukturierte-daten-was-es-gibt.md#einen-localbusiness-block-ergänzen)).
Statt einer Matrix sind vierzehn Felder vom Typ **Zeit** (`eztime`) möglich (Öffnen und Schließen für jeden Tag), aber
mühsam zu bearbeiten; die meisten Ersteller bevorzugen die Matrix. Für eine Mittagspause fügen Sie für denselben Tag
eine zweite Zeile hinzu.

Ein fertiger Text für den Redakteur steht in der [Inhaltsvorlage für Öffnungszeiten](../vorlagen/inhaltsvorlage-fuer-oeffnungszeiten.md).

## Möglichkeit 3: eine Tabelle im Rich Text

Redakteure können mit dem Online-Editor in jedes Rich-Text-Feld eine Tabelle einfügen. Das braucht keine Einrichtung
und ist deshalb ein guter Start. Die Schwächen: Die Tabelle sieht auf jeder Seite, auf der sie eingetippt wird, anders
aus, und es ist leicht, eine Kopie zu vergessen, wenn sich die Zeiten ändern. Wechseln Sie zu Möglichkeit 1 oder 2,
sobald Sie können.

## Die Öffnungszeiten überall zeigen

Welche Möglichkeit Sie auch wählen, die Öffnungszeiten sollten **einmal** eingetippt und angezeigt werden:

- in der Fußzeile jeder Seite oder als kurze Zeile „Heute: 8:00 - 18:00“ in der Kopfzeile;
- auf der Kontakt- und Standortseite;
- auf der Startseite, wenn das die Frage ist, die Besucher am häufigsten stellen.

Der Ersteller holt das eine Objekt im Pagelayout oder im Template der Fußzeile
([Gemeinsame Kopf- und Fußzeile](../../ersteller/website-ersteller/gemeinsamer-kopf-und-fussbereich.md)). Die Fußzeile wird für
die Geschwindigkeit zwischengespeichert, daher kann es nach dem Bearbeiten der Zeiten einen Moment dauern, bis die
Änderung erscheint; der Ersteller sorgt dafür, dass das Veröffentlichen des Objekts mit den Öffnungszeiten den Cache
leert, der sie enthält ([View-Cache und Cache-Blöcke](../../ersteller/website-ersteller/view-cache-und-cache-bloecke.md)).

## Was keine der Möglichkeiten kann

- **„Jetzt geöffnet“ / „Jetzt geschlossen“** braucht ein kleines Template oder Skript, das die aktuelle Uhrzeit mit den
  Öffnungszeiten vergleicht; das ist nicht eingebaut.
- **Feiertagskalender** für Ihre Region sind nicht eingebaut. Feiertagszeiten werden von Hand bearbeitet
  ([Feiertags- und Saisonöffnungszeiten](feiertags-und-saisonoeffnungszeiten.md)).
- **Ihre Einträge auf Kartenplattformen** werden nicht von der Website aus aktualisiert
  ([Unternehmenseinträge außerhalb von Exponential](../gefunden-werden/unternehmenseintraege-ausserhalb-von-exponential.md)).

## Checkliste

- [ ] Eine Möglichkeit gewählt und im Briefing festgehalten.
- [ ] Öffnungszeiten nur an einer Stelle eingetippt.
- [ ] In der Fußzeile oder Kopfzeile und auf der Kontaktseite angezeigt.
- [ ] Ein Hinweisfeld für Ausnahmen existiert.
- [ ] Das Bearbeiten der Öffnungszeiten aktualisiert jede Seite, die sie zeigt.
- [ ] Dieselben Öffnungszeiten stehen auf den Karten- und Suchplattformen.

## Verwandte Seiten

- [Feiertags- und Saisonöffnungszeiten](feiertags-und-saisonoeffnungszeiten.md)
- [Inhaltsklassen für Websites](../../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md)
- [Inhaltsvorlage für Öffnungszeiten](../vorlagen/inhaltsvorlage-fuer-oeffnungszeiten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
