---
title: Inhaltsklassen für Builder
audience: developer, designer, app-creator
category: extension-development
order: 170
summary: Das Inhaltsmodell entwerfen - Klassen, Attribute, Datentypen, Bezeichner, Muster für Objektnamen, Container - und wie das Modell mit Templates, Overrides und Fetches zusammenhängt, mit Regeln, die es wartbar halten.
keywords: Inhaltsklasse, Inhaltsmodell, Attribut, Datentyp, Bezeichner, Objektnamensmuster, Container, Klassengruppe, data_map, AvailableDataTypes, Inhaltsmodellierung, content class, content model, attribute, datatype, identifier, object name pattern, class group, content modeling
type: help
---
# Inhaltsklassen für Builder

Teil 2, Kapitel 17. Eine **Inhaltsklasse** ist eine Art von Inhalt: Artikel, Veranstaltung, Produkt. Ihre **Attribute**
sind die Felder, jedes mit einem **Datentyp** (eine Textzeile, Rich Text, Bild, Relation ...). Alles, was ein Builder
mit Templates tut, beruht auf diesem Modell; ein wenig Sorgfalt hier erspart später viel Template-Code. Wie Sie eine
Klasse Schritt für Schritt anlegen, steht in [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../../anleitungen/inhaltsmodell-und-bearbeitung.md).

## Die Begriffe

| Begriff | Bedeutung |
|---|---|
| Inhaltsklasse | ein Typ: Name, Bezeichner, Attribute |
| Attribut | ein Feld einer Klasse: Name, Bezeichner, Datentyp, Pflichtfeld oder nicht, übersetzbar oder nicht |
| Datentyp | welche Art von Wert ein Attribut enthält (`ezstring`, `eztext`, `ezxmltext`, `ezimage`, `ezobjectrelationlist` ...) |
| Inhaltsobjekt | ein einzelner Inhalt einer Klasse, mit Versionen und Übersetzungen |
| Knoten (Platzierung) | ein Ort eines Objekts im Inhaltsbaum; ein Objekt kann mehrere haben |

Die Datentypen, die beim Hinzufügen eines Attributs angeboten werden, stehen in `content.ini [DataTypeSettings]
AvailableDataTypes[]`; Erweiterungen fügen ihre eigenen hinzu (eztags zum Beispiel ein Tag-Feld).

## Bezeichner sind der Vertrag

Der Klassenbezeichner (`article`) und die Attributbezeichner (`title`, `intro`, `body`, `image`) sind das, was
Templates, Overrides und Fetches verwenden:

```text
{$node.data_map.title.content|wash}
{attribute_view_gui attribute=$node.data_map.body}
```

```ini
Match[class_identifier]=article
```

Wählen Sie sie deshalb einmal und mit Sorgfalt:

- Kleinbuchstaben, Ziffern und Unterstriche; englische Wörter, die sagen, was das Feld ist,
- benennen Sie nie einen Bezeichner um, den Templates verwenden (ändern Sie stattdessen den lesbaren Namen; der
  Bezeichner kann bleiben),
- derselbe Bezeichner für dieselbe Bedeutung in jeder Klasse (`title`, `image`, `intro`), damit ein Template-Teil das
  Bild eines Artikels, einer Nachricht und einer Veranstaltung gleichermaßen zeichnen kann.

## Das Muster für den Objektnamen

Jede Klasse hat ein **Objektnamensmuster** wie `<title>` oder `<first_name> <last_name>`. Es bildet den Namen, der im
Baum, in Listen und in URLs erscheint. Verwenden Sie das Attribut, an dem man den Inhalt erkennt; URL-Aliase werden aus
dem Namen gebildet.

## Container

Eine als **Container** markierte Klasse kann Unterelemente haben. Ordner, Startseiten und Bereiche einer Website sind
Container; Artikel meist nicht. Die Listen eines Builders (`fetch( 'content', 'list' )`) ergeben nur unterhalb von
Containern einen Sinn.

## Gute Modelle sind langweilig

| Besser | Als |
|---|---|
| eine Klasse je echter Inhaltsart | eine Klasse mit einer „Typ“-Auswahl, die alles verändert |
| ein Relationsattribut auf gemeinsame Inhalte (ein Autor, ein Ort) | dieselben Daten in viele Objekte kopieren |
| Rich Text für lange Texte | viele kleine Textfelder für Absätze |
| ein vorhandener Datentyp | ein neuer Datentyp, solange Sie nicht sicher sind (siehe [Datentypen](../teil-3-erweiterungsentwickler/datentypen.md)) |

Modellieren Sie die Inhalte so, wie Redakteure über sie denken; die Templates folgen.

## Klassen in einer Erweiterung

Eine in der Administration angelegte Klasse liegt in der Datenbank. Um sie mit einer Erweiterung auszuliefern (damit
eine andere Installation sie anlegen kann), gibt es zwei Wege:

- exportieren Sie sie als **Paket** (`Setup > Pakete`, Klassenexport) und legen Sie das Paket in die Erweiterung; siehe
  [Pakete](../../../anleitungen/pakete.md),
- schreiben Sie sie als **Skript**, das die Klasse anlegt: Der RAD-Assistent **Content extension**
  (`/setup/contentextension`) schreibt eines für Sie ([RAD-Werkzeuge](../../../../eng-us/features/6.0/rad-tools.md)).

## Verwandte Seiten

- Zurück: [Caching und Templates](caching-und-templates.md)
- Weiter: [Sektionen, Rollen und Zugriff für Builder](sektionen-rollen-und-zugriff-fuer-builder.md)
- [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../../anleitungen/inhaltsmodell-und-bearbeitung.md)
- [Klassengruppen](../../../anleitungen/klassengruppen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
