---
title: Inhalte exportieren
audience: government, admin, project-manager, developer
category: public-sector
order: 120
summary: Für öffentliche Stellen, die Inhalte an ein Schriftgutsystem, ein Archiv oder eine neue Plattform übergeben - die Exportwege, die Exponential bietet (Pakete aus Inhaltsobjekten, PDF-Export, die REST-API, RSS, CSV-Dateien von Formularen und Konten, Audit-Exporte), mit dem, was jeder enthält und was er auslässt.
keywords: Export, Inhaltsexport, Pakete, ezpkg, Export von Inhaltsobjekten, PDF-Export, REST-API, RSS-Feeds, CSV, Audit-Export, Migration, Übergabe von Schriftgut, Abgabe an ein Archiv, Ausstiegsplan, öffentlicher Sektor, export, content export, packages, content object export, records transfer, archive transfer, exit plan, public sector
type: help
---
# Inhalte exportieren

Diese Seite richtet sich an Zuständige für Schriftgut, Projektleitungen und Entwickler. Schriftgut muss die Website
irgendwann verlassen: beim Veröffentlichen in ein Schriftgutsystem, am Ende der Aufbewahrung in ein Archiv oder am Ende
eines Vertrags auf eine neue Plattform. Exponential hat mehrere Exportwege; keiner ist für sich ein Übergabeformat für
Schriftgut, also wählen Sie nach Zweck.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Die Wege](#die-wege)
2. [Einen Weg wählen](#einen-weg-wählen)
3. [Ausstiegsplanung](#ausstiegsplanung)
4. [Verwandte Seiten](#verwandte-seiten)

## Die Wege

| Weg | Enthält | Lässt aus | Seite |
|---|---|---|---|
| Pakete, Export von Inhaltsobjekten | Objekte mit ihren Feldern und Dateien, Klassen, in einer `.ezpkg` (klassisches XML oder JSON) | Versionsgeschichte, Audit, andere nicht ausgewählte Inhalte | „Pakete für den Export“ |
| PDF-Export | ein PDF eines Knotens oder eines Baums, seine Attribute; einmal oder bei Bedarf erzeugt | Struktur zur Wiederverwendung; Tags | „Der PDF-Export“ |
| REST-API | Knoten, Objekte, Felder, Unterknoten als JSON, mit Rechten | Versionsgeschichte | „Export über die REST-API“ |
| RSS-Feeds | die neuesten Einträge einer Liste | alles Ältere | [RSS-Feeds](../../../anleitungen/rss-feeds.md) |
| CSV der gesammelten Informationen | die Einsendungen eines Formulars | andere Formulare | [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md) |
| CSV aus Benutzerkonten prüfen | die Liste der Konten | Inhalte der Konten | [Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md) |
| Audit-Export | Einträge als CSV, JSON-Zeilen, JSON oder ein signiertes Bündel | | „Audit-Einträge als Nachweis“ |
| Datenbank-Dump | alles | die Dateien (getrennt exportiert: `var/storage`) | [Datensicherungen](../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung) |

## Einen Weg wählen

| Zweck | Weg |
|---|---|
| Eine menschenlesbare Kopie einer Bekanntmachung beim Veröffentlichen | PDF (einmal erzeugter Export dieses Knotens) oder Drucken als PDF aus dem Browser; dazu die Metadaten in Ihrem Schriftgutsystem |
| Strukturierte Inhalte für ein anderes System | REST-API oder Pakete |
| Eine vollständige Kopie für ein Archiv oder den Ausstieg | Datenbank-Dump plus Dateien und eine Dokumentation der Klassen |
| Nachweis, wer was geändert hat | Audit-Export als signiertes Bündel |

Halten Sie für jeden Export in ein Schriftgutsystem fest, welche Version exportiert wurde (Versionsnummer und Datum
von der Versionsseite).

## Ausstiegsplanung

Eine öffentliche Stelle sollte jeden Dienstleister verlassen können. Mit Exponential: Die Software ist frei unter der
GPL, die Daten liegen in Ihrer Datenbank in einem dokumentierten Schema, die Dateien in `var/storage`, und die REST-API
und Pakete exportieren strukturierte Inhalte. Halten Sie in Ihrem Ausstiegsplan fest: das Verfahren für den
Datenbank-Dump, die Liste der Klassen und ihrer Felder (Setup > Klassen) und einen jährlichen Testexport.

## Verwandte Seiten

- [Pakete](../../../anleitungen/pakete.md)
- [PDF-Exporte](../../../anleitungen/pdf-exporte.md)
- [REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
