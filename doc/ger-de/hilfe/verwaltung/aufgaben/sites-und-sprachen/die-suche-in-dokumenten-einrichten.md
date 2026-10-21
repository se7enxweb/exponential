---
title: Die Suche in Dokumenten einrichten
audience: government, admin
category: public-sector
order: 320
summary: Aufgabe für Administratoren - den Text in PDF- (und Word-)Dokumenten durchsuchbar machen, mit den Handlern zur Textextraktion in binaryfile.ini und einem installierten Extraktionswerkzeug, dann den Index neu aufbauen und testen.
keywords: Dokumentensuche, PDF, Textextraktion, durchsuchbare Datei, Volltext, document search, binaryfile.ini, TextExtractionTool, PDFHandlerSettings, updatesearchindex, searchable file, full text
type: help
---
# Die Suche in Dokumenten einrichten

Diese Aufgabe richtet sich an Administratoren. Hintergrund:
[In Dokumenten suchen](../../aufbauen/suche/in-dokumenten-suchen.md).

## Bevor Sie beginnen

Ein Kommandozeilenwerkzeug zur Textextraktion aus PDF ist auf dem Webserver installiert; testen Sie es an einer
Beispieldatei als Benutzer des Webservers.

## Schritte

1. Überschreiben Sie den Handler mit Ihrem Werkzeug:

   ```ini
   # settings/override/binaryfile.ini.append.php
   <?php /*
   [PDFHandlerSettings]
   TextExtractionTool=<full path and options of your tool>
   */ ?>
   ```

   Die mitgelieferten Werte (`pstotext`, `wvWare ...`) nennen alte Werkzeuge.
2. **Setup > Klassen**, die Klasse File: Stellen Sie sicher, dass das Dateiattribut **Durchsuchbar** ist.
3. Leeren Sie den INI-Cache und bauen Sie den Index neu auf: `php bin/php/updatesearchindex.php --clean`.

## Prüfen

Suchen Sie auf der öffentlichen Site nach einem Wort, das nur in einem PDF vorkommt: Das Dokument wird gefunden.

## Probleme

| Problem | Lösung |
|---|---|
| Nichts gefunden | das Werkzeug schlägt als Benutzer des Webservers fehl, oder der Index wurde nicht neu aufgebaut |
| Gescannte PDFs werden nicht gefunden | sie haben keine Textebene; führen Sie vor dem Hochladen eine Texterkennung (OCR) durch oder beschreiben Sie sie im Objekt |

## Verwandte Seiten

- [Dateien und Downloads](../../../../../eng-us/help/creators/website-creators/files-and-downloads.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
