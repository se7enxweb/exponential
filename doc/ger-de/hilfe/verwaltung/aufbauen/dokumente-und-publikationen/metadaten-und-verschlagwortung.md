---
title: Metadaten und Verschlagwortung von Dokumenten
audience: government, website-creator, editor
category: public-sector
order: 50
summary: Welche Metadaten öffentliche Dokumente brauchen und wie man sie mit echten Datentypen von Exponential speichert - Titel, Datum, Aktenzeichen, Typ, Sprache, Verantwortliche, Themen mit eztags, Metadaten für Suchmaschinen mit xrowmetadata - und wie Redakteure sie einheitlich halten.
keywords: Metadaten von Dokumenten, Veröffentlichungsdatum, Aktenzeichen, Dokumenttyp, Sprache, Verantwortliche, Themen, Schlagworte, Einheitlichkeit, kontrolliertes Vokabular, document metadata, publication date, reference number, document type, language, owner, topics, eztags, ezselection, ezdate, xrowmetadata, consistency, controlled vocabulary
type: help
---
# Metadaten und Verschlagwortung von Dokumenten

Diese Seite richtet sich an Site-Ersteller und Redakteure, die Dokumente beschreiben. Metadaten machen Dokumente
auffindbar, auflistbar und verwaltbar: das Veröffentlichungsdatum für Listen, der Typ für Filter, Themen für
Querverweise, eine verantwortliche Stelle für die Pflege. Die genannten Datentypen sind die aus
`kernel/classes/datatypes/` und den Erweiterungen eztags und xrowmetadata.

## Inhalt

1. [Die zu pflegenden Metadaten](#die-zu-pflegenden-metadaten)
2. [Themen mit eztags](#themen-mit-eztags)
3. [Metadaten für Suchmaschinen](#metadaten-für-suchmaschinen)
4. [Einheitlich halten](#einheitlich-halten)

## Die zu pflegenden Metadaten

| Metadaten | Datentyp | Wo |
|---|---|---|
| Titel | Textzeile | der Objektname |
| Veröffentlichungsdatum (amtlich) | Datum (`ezdate`) | getrennt vom Zeitpunkt der Veröffentlichung auf der Site, der abweichen kann |
| Aktenzeichen oder Dokumentnummer | Textzeile | zum Zitieren |
| Dokumenttyp | Auswahlliste (`ezselection`) | Bericht, Plan, Leitfaden, Formular, Beschluss, Daten |
| Sprache | die Übersetzung des Objekts, oder eine Auswahlliste für Dateien in nur einer Sprache | |
| Verantwortliche Stelle | die Sektion; oder eine Textzeile mit dem Namen der Stelle | Pflege |
| Themen | Tags (`eztags`) | Querverweise, Themenseiten |
| Datum der Überprüfung oder des Ablaufs | Datum | für lebende Dokumente |
| Zusammenfassung | XML Block | lesbar auf der Seite; durchsuchbar |

Diese Attribute gehören zur Klasse Publication (Einstiegsseite), nicht zu jedem File-Objekt; die Dateien werden mit ihr
verknüpft ([Aufbau einer Dokumentenbibliothek](aufbau-einer-dokumentenbibliothek.md)).

## Themen mit eztags

| Wahl | Praxis |
|---|---|
| Ein Schlagwortbaum für die ganze Site | Dokumente, Nachrichten und Leistungen teilen sich Themen |
| Geschlossenes Vokabular für Dokumente | **Limit by tags subtree** am Attribut; Redakteure wählen, das Webteam fügt Schlagworte hinzu |
| Synonyme | für die Wörter, die Menschen verwenden ([Lebenslagen und Themen](../leistungskatalog/lebenslagen-und-themen.md)) |
| Schlagwortseiten | `/tags/view/<tag>` listet alles mit dem Schlagwort ([Verschlagworten mit eztags](../../../../../eng-us/help/creators/website-creators/tagging-with-eztags.md)) |

## Metadaten für Suchmaschinen

Ein Attribut Metadaten (`xrowmetadata`) in der Klasse Publication gibt der Seite einen Titel und eine Beschreibung für
Suchergebnisse und Linkvorschauen
([Metadaten und Teilen in sozialen Netzwerken](../nachrichten-und-warnmeldungen/metadaten-und-teilen-in-sozialen-netzwerken.md)). PDF-Dateien tragen
ihre eigenen Metadaten (Titel, Autor, Sprache) in den Dateieigenschaften: Setzen Sie sie vor dem Export im
Quellprogramm, denn Suchmaschinen zeigen bei einem PDF-Treffer den eigenen Titel der Datei.

## Einheitlich halten

| Maßnahme | Wie |
|---|---|
| Pflichtfelder | kreuzen Sie in der Klasse für Datum, Typ und Zusammenfassung **Erforderlich** an |
| Auswahllisten statt Freitext | Typen und Sprachen als Auswahlliste |
| Eine jährliche Durchsicht der Schlagworte | führen Sie Dubletten im Dashboard der Schlagworte zusammen |
| Ein Verzeichnis | die Tabelle der Unterelemente mit Spalten für Metadaten, als CSV exportiert ([Inhaltsmodell und Bearbeitung, 5](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#5-die-tabelle-der-unterelemente)) |
| Offene Datenkataloge | sind Dokumente Datensätze, beschreiben Sie sie in Ihrem Katalog mit [DCAT 3](https://www.w3.org/TR/vocab-dcat-3/); Exponential erzeugt DCAT nicht selbst |

## Verwandte Seiten

- [Dokumente und Publikationen](README.md)
- [Versionen von Dokumenten](versionen-von-dokumenten.md)
- [eztags](../../../../../eng-us/features/6.0/extensions/eztags.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
