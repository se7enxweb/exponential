---
title: In Dokumenten suchen
audience: government, admin, website-creator
category: public-sector
order: 50
summary: Wie Sie den Text von PDF- und Word-Dokumenten auf einer mit Exponential gebauten öffentlichen Site auffindbar machen - die Handler zur Textextraktion in binaryfile.ini und die externen Werkzeuge, die sie brauchen, Solr als Alternative, gescannte Dokumente und Prüfungen.
keywords: Dokumentensuche, PDF-Suche, Volltext, Textextraktion, Texterkennung, gescanntes PDF, document search, PDF search, full text, binaryfile.ini, PDFHandlerSettings, WordHandlerSettings, TextExtractionTool, text extraction, OCR, scanned PDF, ezfind
type: help
---
# In Dokumenten suchen

Diese Seite richtet sich an Administratoren und Site-Ersteller, deren öffentliche Site viele Dokumente veröffentlicht:
Berichte, Protokolle, Beschlüsse. Ohne Textextraktion sind nur der Name des Dokuments und die Felder seines Objekts
durchsuchbar ([Dateien und Downloads](../../../ersteller/website-ersteller/dateien-und-downloads.md#in-dateien-suchen)).

## Inhalt

1. [Die Handler zur Textextraktion](#die-handler-zur-textextraktion)
2. [Einrichtung](#einrichtung)
3. [Mit Solr](#mit-solr)
4. [Gescannte Dokumente](#gescannte-dokumente)
5. [Prüfen](#prüfen)

## Die Handler zur Textextraktion

`settings/binaryfile.ini` hat Handler, die mit externen Programmen, die Sie auf dem Server installieren, Text aus PDF-
und Word-Dateien für den Suchindex extrahieren:

| Block | Schlüssel | Mitgelieferter Wert |
|---|---|---|
| `[PDFHandlerSettings]` | `TextExtractionTool` | `pstotext` |
| `[WordHandlerSettings]` | `TextExtractionTool` | `wvWare -x /usr/local/wv/wvText.xml` |

Die mitgelieferten Werte nennen alte Werkzeuge; setzen Sie das Werkzeug, das Sie installieren, mit vollem Pfad in
`settings/override/binaryfile.ini.append.php`, und testen Sie es zuerst auf der Kommandozeile.

## Einrichtung

1. Installieren Sie auf dem Webserver ein Kommandozeilenwerkzeug zur Textextraktion für PDF (und Word, wenn Sie
   Word-Dateien veröffentlichen).
2. Überschreiben Sie `TextExtractionTool` mit dem Befehl.
3. Markieren Sie das Dateiattribut der Klasse File als **Durchsuchbar**.
4. Bauen Sie den Index neu auf: `php bin/php/updatesearchindex.php --clean`.

## Mit Solr

Mit der Erweiterung `ezfind` und einem Solr-Server können auch Dateiinhalte indiziert werden
([Suche mit ezfind und Solr](../../../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md)); prüfen Sie die
Dokumentation der Erweiterung in `extension/ezfind/` auf die Einstellung in Ihrer Version.

## Gescannte Dokumente

Ein Scan ohne Textebene hat keinen Text zum Extrahieren und ist auch nicht barrierefrei. Veröffentlichen Sie Dokumente
mit echtem Text oder führen Sie vor dem Hochladen eine Texterkennung (OCR) durch; eine Zusammenfassung in der
Beschreibung des Objekts macht selbst einen Scan über sein Thema auffindbar.

## Prüfen

Suchen Sie nach einem Wort, das nur in einem Dokument vorkommt. Wird es nicht gefunden: Prüfen Sie, dass das Werkzeug
als Benutzer des Webservers läuft, dass das Dateiattribut durchsuchbar ist und dass der Index neu aufgebaut wurde.

## Verwandte Seiten

- [Dateien und Downloads](../../../ersteller/website-ersteller/dateien-und-downloads.md)
- [Durchsuchbare Felder und Indizierung](durchsuchbare-felder-und-indizierung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
