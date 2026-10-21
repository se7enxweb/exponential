---
title: Dateien und Downloads
audience: website-creator, editor
category: website-creation
order: 350
summary: Dokumente zum Download anbieten - die Klasse file und ezbinaryfile, die Ansicht content/download und ihre Rechteprüfung, Download-Links in Templates, durchsuchbare PDFs, geschützte Downloads und große Dateien.
keywords: Dateien, Downloads, PDF, Dokumente, Binärdatei, geschützter Download, Download für Mitglieder, Dateigröße, Website-Ersteller, files, downloads, ezbinaryfile, content/download, documents, binary file, protected download, members download, file size, enhancedezbinaryfile, website creator
type: help
---
# Dateien und Downloads

Diese Seite richtet sich an Website-Ersteller, die Dokumente, Broschüren oder Software zum Download anbieten. Dateien
sind Inhaltsobjekte mit einem **Datei**-Feld (`ezbinaryfile`); Downloads laufen über die Ansicht `content/download`, die
die Rechte prüft, bevor sie die Datei sendet.

## Inhalt

1. [Wie ein Download funktioniert](#wie-ein-download-funktioniert)
2. [Download-Links in Templates](#download-links-in-templates)
3. [Geschützte Downloads](#geschützte-downloads)
4. [In Dateien suchen](#in-dateien-suchen)
5. [Große Dateien](#große-dateien)
6. [Checkliste](#checkliste)

## Wie ein Download funktioniert

- Ein **File**-Objekt (Klasse `file`) enthält das Dokument in seinem Attribut `file` (Datentyp `ezbinaryfile`).
  Hochgeladene Dateien, die keine Bilder sind, werden zu File-Objekten (`DefaultClass=file` in `settings/upload.ini`).
- Die Download-Adresse ist `content/download/<object ID>/<attribute ID>` (`kernel/content/module.php`, Ansicht
  `download`, Funktion `read`). Die Ansicht prüft, ob der Besucher das Objekt lesen darf, und sendet dann die Datei.
- Wegen dieser Prüfung kann eine Datei in einer Sektion nur für Mitglieder auch nur von Mitgliedern heruntergeladen
  werden.

## Download-Links in Templates

Das View-Template des Datentyps rendert einen Link mit Name und Größe:

```text
{attribute_view_gui attribute=$node.data_map.file}
```

Ihr eigenes Markup:

```text
{def $file = $node.data_map.file}
{if $file.has_content}
  <a href={concat( 'content/download/', $file.contentobject_id, '/', $file.id, '/file/', $file.content.original_filename )|ezurl}>
    {$node.name|wash} ({$file.content.mime_type|wash}, {$file.content.filesize|si( byte )})
  </a>
{/if}
{undef $file}
```

Nennen Sie den Besuchern Format und Größe, bevor sie klicken; für viele Websites ist das auch eine Anforderung der
Barrierefreiheit.

## Geschützte Downloads

| Ziel | Wie |
|---|---|
| Dateien nur für Mitglieder | legen Sie sie in eine Sektion, die nur Mitglieder lesen dürfen ([Sektionen und Objektzustände für Website-Ersteller](sektionen-und-objektzustaende.md)) |
| Dateien für eine Kundengruppe | eine Teilbaum-Einschränkung auf `content/read` für diese Gruppe |
| Ein zeitlich begrenztes Dokument | verbergen Sie es oder verschieben Sie es nach Ablauf der Zeit in eine Sektion, die Besucher nicht lesen können |

Verlinken Sie Dateien nicht direkt aus `var/.../storage/`: Das umgeht die Rechteprüfung. Die mitgelieferten
Rewrite-Regeln liefern gespeicherte Bilder aus Geschwindigkeitsgründen direkt aus; für Dokumente verlassen Sie sich auf
`content/download`.

## In Dateien suchen

`settings/binaryfile.ini` hat Handler, die Text aus PDF- und Word-Dateien für den Suchindex extrahieren
(`[PDFHandlerSettings]`, `[WordHandlerSettings]`), mit externen Programmen, die Sie installieren. Mit Solr (`ezfind`)
lassen sich Dateiinhalte ebenfalls indizieren ([Suche mit ezfind und Solr](suche-mit-ezfind-und-solr.md)).

## Große Dateien

- `upload_max_filesize` und `post_max_size` von PHP begrenzen, was die Redaktion hochladen kann; setzen Sie sie in der
  `php.ini` des Web-PHP.
- Ein Dateifeld kann in der Klasse eine maximale Größe haben.
- Sehr große Dateien (Videos) liegen besser bei einem dafür gebauten Dienst und werden eingebettet.
- Die Erweiterung `enhancedezbinaryfile` fügt dem Datei-Datentyp Optionen hinzu
  ([enhancedezbinaryfile](../../../../eng-us/features/6.0/extensions/enhancedezbinaryfile.md)).

## Checkliste

- [ ] Download-Links zeigen Format und Größe.
- [ ] Geschützte Dateien liegen in einer geschützten Sektion, und der Download ist abgemeldet getestet.
- [ ] Die Upload-Grenzen in `php.ini` passen zur größten Datei, die die Redaktion braucht.
- [ ] Dateinamen sind lesbar (sie erscheinen in der Download-Adresse).

## Verwandte Seiten

- [Die Medienbibliothek organisieren](die-medienbibliothek-organisieren.md)
- [Medien und Uploads (für die Redaktion)](../../medien-und-uploads.md)
- [PDF-Exporte](../../../anleitungen/pdf-exporte.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
