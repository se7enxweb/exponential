---
title: Dateien und die Klasse File
audience: government, website-creator, editor
category: public-sector
order: 20
summary: Wie Dokumente in Exponential gespeichert werden - die Klasse File mit ihrem Attribut ezbinaryfile, Uploads über die Upload-Seite und das Bearbeitungsformular, Upload-Grenzen, Dateinamen, Download-Links mit Format und Größe und die Suche in Dateien.
keywords: Klasse File, Upload, Mehrfach-Upload, Upload-Grenzen, Dateinamen, Download-Link, Dateigröße, Format, Suche in Dateien, File class, ezbinaryfile, upload, multi-upload, upload limits, upload_max_filesize, file names, download link, file size, format, binaryfile.ini, search inside files
type: help
---
# Dateien und die Klasse File

Diese Seite richtet sich an Site-Ersteller und Redakteure, die Dokumente auf eine Site des öffentlichen Sektors
stellen. Ein Dokument ist ein Objekt **File**, das die Datei in einem Attribut `ezbinaryfile` enthält; Seiten verlinken
oder verknüpfen es. Quellen: [Dateien und Downloads](../../../ersteller/website-ersteller/dateien-und-downloads.md) und
[Medien und Uploads](../../../medien-und-uploads.md).

## Inhalt

1. [Die Klasse File](#die-klasse-file)
2. [Hochladen](#hochladen)
3. [Grenzen](#grenzen)
4. [Download-Links](#download-links)
5. [In Dateien suchen](#in-dateien-suchen)

## Die Klasse File

| Attribut | Bezeichner | Datentyp | Erforderlich |
|---|---|---|---|
| Name | `name` | Textzeile | ja |
| Beschreibung | `description` | XML Block | nein |
| Datei | `file` | Datei (`ezbinaryfile`) | ja |

Die Fassung im Paket `sevenx_classes` fügt Schlüsselwörter hinzu; bei einer neuen Installation behält die Klasse File
die oben gezeigte Definition der Basisdaten ([sevenx_classes](../../../seitenpakete/sevenx-multisite/sevenx-classes-das-paket-der-inhaltsklassen.md)).
File-Objekte sind „immer verfügbar“ und erscheinen daher in jedem Sprach-Siteaccess.

## Hochladen

| Weg | Wo |
|---|---|
| Die Upload-Seite: viele Dateien per Schaltfläche, Ziehen oder Einfügen, mit einem Titel je Datei und **Keep as drafts** | **Hochladen** an einem Ordner, **Dateien hochladen** im Dashboard ([Medien und Uploads](../../../medien-und-uploads.md#dateien-hochladen)) |
| Aus einem Bearbeitungsformular: **Neu hochladen** in einem Beziehungsfeld | legt ein File-Objekt an und verknüpft es |
| Ein File-Objekt von Hand anlegen | **Neu erstellen** > **File** |
| ezmultiupload | **Mehrere Dateien hochladen** ([ezmultiupload](../../../../funktionen/6.0/erweiterungen/ezmultiupload.md)) |

Dateien, die keine Bilder sind, werden zu File-Objekten (`settings/upload.ini`, `DefaultClass=file`). Geben Sie jedem
einen echten Titel; voreingestellt ist der Dateiname.

## Grenzen

| Grenze | Wo |
|---|---|
| Größe je Upload | PHP `upload_max_filesize` und `post_max_size`; die Upload-Seite zeigt das größte akzeptierte Maß (zum Beispiel „At most 64 MB per file“) |
| Eine Höchstgröße je Dateiattribut | die Einstellung des Klassenattributs |
| Erlaubte Typen | die Upload-Einstellungen; gefährliche Namen (zum Beispiel `shell.php.jpg`) werden beim Upload im Editor abgelehnt |

## Download-Links

Ein Download geht über `content/download/<object ID>/<attribute ID>`, das prüft, ob der Besucher das Objekt lesen
darf. Das View-Template des Dateiattributs erzeugt einen Link mit Name und Größe:

```text
{attribute_view_gui attribute=$node.data_map.file}
```

Sagen Sie Besuchern Format und Größe, bevor sie klicken („Jahresbericht 2025 (PDF, 2,4 MB)“). Für Ihr eigenes Markup
enthält der Inhalt des Attributs den ursprünglichen Dateinamen, die Größe und den MIME-Typ
([Dateien und Downloads](../../../ersteller/website-ersteller/dateien-und-downloads.md#download-links-in-templates)).

## In Dateien suchen

`settings/binaryfile.ini` hat Handler, die Text aus PDF- und Word-Dateien für den Suchindex extrahieren
(`[PDFHandlerSettings]`, `[WordHandlerSettings]`), mit externen Programmen, die Sie installieren; mit eZ Find und Solr
können Dateiinhalte ebenfalls indiziert werden. Ohne sie werden nur Name und Beschreibung durchsucht: Schreiben Sie eine
gute Beschreibung.

## Verwandte Seiten

- [Dokumente und Publikationen](README.md)
- [Aufbau einer Dokumentenbibliothek](aufbau-einer-dokumentenbibliothek.md)
- [Die Medienbibliothek ordnen](../../../ersteller/website-ersteller/die-medienbibliothek-organisieren.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
