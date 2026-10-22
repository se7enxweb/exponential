---
title: Wie verknüpfe ich andere Elemente mit dem, das ich gerade bearbeite?
audience: editor
category: manual
order: 455
summary: Der Kasten Verwandte Objekte unter dem Bearbeitungsformular fügt vorhandene Elemente hinzu (Vorhandenes hinzufügen) oder lädt eine neue Datei hoch (Neu hochladen) und verknüpft sie, zeigt den Code, mit dem man jedes davon in Rich Text einbettet oder verlinkt, und Ausgewählte entfernen nimmt Verknüpfungen weg, ohne die Elemente zu löschen.
keywords: verwandte Objekte, verknüpfte Objekte, Verknüpfungen, Relationen, Vorhandenes hinzufügen, neu hochladen, ausgewählte entfernen, Einbettungscode, XML Code, allgemeine Relation, eingebettet, verlinkt, Attribut, rückwärts verknüpft, related objects, relations, add existing, upload new, remove selected, embed code, XML code, common relation, embedded, linked, attribute, reverse related
type: help
---
# Wie verknüpfe ich andere Elemente mit dem, das ich gerade bearbeite?

Eine Relation hält fest, dass ein Element ein anderes verwendet. Exponential kennt vier Arten, die im Tab
**Verknüpfungen** eines Elements angezeigt werden:

| Art der Relation | Wie sie entsteht |
|---|---|
| **Allgemein** | von Hand im Kasten **Verwandte Objekte** des Bearbeitungsformulars hinzugefügt |
| **Eingebettet** | ein Element, das in ein Rich-Text-Feld eingebettet ist |
| **Verlinkt** | ein Link auf ein Element in einem Rich-Text-Feld |
| **Attribut** | ein Relationsfeld der Klasse, etwa *Image* eines Artikels |

Diese Seite behandelt den Kasten **Verwandte Objekte**. Templates können allgemeine Relationen anzeigen, zum Beispiel
als Liste verwandter Seiten; ob sie das tun, hängt von der Website ab.

## Wer das tun kann

Redakteure, die das Element bearbeiten und die verknüpften Elemente lesen dürfen. **Neu hochladen** erfordert
außerdem das Recht, das neue Element dort anzulegen, wohin Uploads gehen.

## Schritte

1. Bearbeiten Sie das Element und scrollen Sie unter den Feldern zu **Verwandte Objekte**.
   Der Kasten listet **Related images**, **Related files** und **Related content** auf, jeweils mit ihrer Anzahl.
2. Um ein vorhandenes Element zu verknüpfen, klicken Sie auf **Vorhandenes hinzufügen**, suchen Sie es im Browser,
   wählen Sie es aus und klicken Sie auf **Auswählen**.
3. Um eine Datei von Ihrem Computer zu verknüpfen, klicken Sie auf **Neu hochladen**, wählen Sie die Datei und
   bestätigen Sie.
   Aus der Datei wird ein neues Element erstellt und sofort verknüpft.
4. Um ein verknüpftes Element in einem Rich-Text-Feld zu platzieren, kopieren Sie den Code aus seiner Spalte **XML
   Code** und fügen Sie ihn in den Text ein, oder verwenden Sie die Schaltflächen des Editors zum Einbetten und
   Verlinken.
5. Um Verknüpfungen wegzunehmen, haken Sie sie an und klicken Sie auf **Ausgewählte entfernen**. Die Elemente selbst
   werden nicht gelöscht.
6. Klicken Sie auf **Zur Veröffentlichung schicken**.

## Was schiefgehen kann

- **Ein eingebettetes oder verlinktes Element lässt sich hier nicht entfernen.** Diese Relationen folgen dem Text.
  Entfernen Sie die Einbettung oder den Link aus dem Rich-Text-Feld, und die Relation verschwindet mit.
- **Die Relation erscheint nirgends auf der Website.** Die Templates der Website zeigen allgemeine Relationen nicht
  an. Verwenden Sie stattdessen ein Relationsfeld oder eine Einbettung, oder fragen Sie die Person, die die Templates
  baut.
- **Sie möchten sehen, wer dieses Element verwendet.** Das Bearbeitungsformular zeigt das nicht. Öffnen Sie das
  Element und sehen Sie sich im Tab **Verknüpfungen** die **Rückwärts verknüpfte Objekte** an.
- **Die hochgeladene Datei ist in einem unerwarteten Ordner gelandet.** Uploads gehen an die Stelle, die Ihre Website
  für jede Dateiart festlegt, meist ein Ordner der Medienbibliothek.

## Verwandte Seiten

- [Inhalte erstellen und bearbeiten](../inhalte-bearbeiten.md#verknüpfungen-und-bilder)
- [Wie verwende ich ein Bild oder eine Datei aus der Medienbibliothek wieder?](wie-verwende-ich-ein-bild-oder-eine-datei-wieder.md)
- [Wie bette ich ein anderes Objekt in einen Text ein?](wie-bette-ich-ein-anderes-objekt-ein.md)
- [Relationen und Einbettungen](../../../eng-us/help/cms-developers/relations-and-embeds.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
