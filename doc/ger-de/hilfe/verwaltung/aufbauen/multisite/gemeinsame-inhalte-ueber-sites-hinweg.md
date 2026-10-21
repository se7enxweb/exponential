---
title: Gemeinsame Inhalte über Sites hinweg
audience: government, editor, website-creator
category: public-sector
order: 60
summary: Wie ein Inhalt (eine Kontaktseite, eine Richtlinie, eine Nachricht) auf mehreren Abteilungs-Sites einer Exponential-Installation erscheint, ohne kopiert zu werden - mehrere Orte, Objektbeziehungen und Einbettungen, gemeinsame Ordner, und worauf bei Sprachen und Berechtigungen zu achten ist.
keywords: gemeinsame Inhalte, Wiederverwendung, mehrere Orte, Orte hinzufügen, Objektbeziehung, Einbettung, gemeinsamer Ordner, immer verfügbar, Multisite, keine Kopien, shared content, reuse, multiple locations, Add locations, object relation, embed, shared folder, always available, multisite, no copies
type: help
---
# Gemeinsame Inhalte über Sites hinweg

Diese Seite richtet sich an Redakteure und Site-Ersteller einer Multisite-Installation. Weil alle Sites einen
Inhaltsbaum teilen, kann ein Objekt auf mehreren Sites erscheinen, ohne kopiert zu werden; Korrekturen erreichen dann
jede Site zugleich ([Beziehungen und Wiederverwendung](../../../../../eng-us/help/creators/website-creators/relations-and-reuse.md)).

## Inhalt

1. [Drei Wege zu teilen](#drei-wege-zu-teilen)
2. [Mehrere Orte](#mehrere-orte)
3. [Beziehungen und Einbettungen](#beziehungen-und-einbettungen)
4. [Ein gemeinsamer Ordner](#ein-gemeinsamer-ordner)
5. [Worauf zu achten ist](#worauf-zu-achten-ist)

## Drei Wege zu teilen

| Weg | Verwenden Sie ihn, wenn |
|---|---|
| Mehrere Orte | das Element ins Menü zweier Sites gehört; dasselbe Objekt unter zwei Adressen |
| Objektbeziehung oder Einbettung | eine Seite einer Site ein Element zeigt oder verlinkt, das anderswo liegt |
| Ein gemeinsamer Ordner, den Templates lesen | viele Elemente (gemeinsame Nachrichten, ein zentrales A bis Z) auf jeder Site erscheinen |

## Mehrere Orte

Ein Objekt kann mehr als einen Ort (Knoten) im Baum haben; jeder Ort kann im Ordner einer anderen Site liegen. In Listen
von Elementen setzt **Add location...** ausgewählte Elemente zusätzlich an einen weiteren Ort (in der Liste der
hochgeladenen Dateien), und der Reiter **Orte** des Elements hat **Orte hinzufügen**; der Reiter zeigt alle seine Orte
und welcher der Hauptort ist. Das Bearbeiten des Objekts ändert es überall.

## Beziehungen und Einbettungen

Beziehungsfelder und Einbettungen im Rich Text zeigen auf das Objekt, nicht auf eine Adresse, und folgen ihm daher,
wenn es verschoben wird. Mit der Erweiterung `ezurlmultisite` zeigt ein Link auf Inhalte im Ordner einer anderen Site
auf den Host dieser Site (siehe „Links zwischen Abteilungs-Sites“).

## Ein gemeinsamer Ordner

Halten Sie zentrale Inhalte (zum Beispiel „Nachrichten aus der ganzen Verwaltung“) in einem Ordner außerhalb der
Abteilungsordner, und lassen Sie die Templates jeder Site daraus abrufen. Jede Site zeigt dann dieselben Elemente in
ihrem eigenen Design.

## Worauf zu achten ist

- **Berechtigungen**: Ein Element in einem gemeinsamen Ordner ist auf jeder Site sichtbar, deren Besucher seine Sektion
  lesen dürfen.
- **Sprachen**: Eine Site zeigt das Element nur in Sprachen, die ihre `SiteLanguageList[]` enthält, sofern es nicht
  immer verfügbar ist.
- **Entfernen**: Das Entfernen eines Ortes eines Elements mit mehreren Orten behält die anderen; das Entfernen des
  Objekts entfernt es überall.
- **Adressen**: Jeder Ort hat seine eigene Adresse; der Hauptort bestimmt die kanonische.

## Verwandte Seiten

- [Beziehungen und Wiederverwendung](../../../../../eng-us/help/creators/website-creators/relations-and-reuse.md)
- [Links zwischen Abteilungs-Sites](links-zwischen-abteilungs-sites.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
