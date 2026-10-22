---
title: Die mitgelieferten Feldtypen
audience: dxp-developer
category: dxp-development
order: 18
summary: Jeder Feldtyp, den ein DXP-Projekt ab Werk hat, mit seinem Bezeichner, gelesen aus dem installierten Core und den Feldtyp-Paketen - Text, Zahlen, Datumsangaben, Medien, Relationen, Benutzer, Rich Text, Matrix und Inhaltsabfrage - und wofür Sie jeden verwenden.
keywords: Feldtypen, ibexa_string, ibexa_text, ibexa_richtext, ibexa_matrix, ibexa_content_query, ibexa_image, ibexa_image_asset, Relation, Auswahl, Bezeichner, fieldtype, field types, relation, selection, identifiers
type: help
---
# Die mitgelieferten Feldtypen

Diese Seite richtet sich an Entwickler, die Inhaltstypen entwerfen. Sie listet die Feldtypen eines DXP-Projekts auf. Die
Bezeichner wurden auf einer Installation von Platform v5 aus den `Type`-Klassen der installierten Pakete gelesen
(`se7enxweb/exponential-platform-dxp-core`, `se7enxweb/fieldtype-richtext`, `ibexa/fieldtype-matrix`,
`ibexa/fieldtype-query`).

## Inhalt

1. [Text und Zahlen](#text-und-zahlen)
2. [Datumsangaben und Auswahlen](#datumsangaben-und-auswahlen)
3. [Medien und Dateien](#medien-und-dateien)
4. [Relationen und Personen](#relationen-und-personen)
5. [Strukturierte Felder](#strukturierte-felder)
6. [In v5 geänderte Bezeichner](#in-v5-geänderte-bezeichner)

## Text und Zahlen

| Feldtyp | Bezeichner | Verwenden Sie ihn für |
|---|---|---|
| Textzeile | `ibexa_string` | Titel, kurze Beschriftungen |
| Textblock | `ibexa_text` | reinen Text über mehrere Zeilen |
| Rich Text | `ibexa_richtext` | formatierten Haupttext mit Einbettungen (Paket `se7enxweb/fieldtype-richtext`) |
| Ganzzahl | `ibexa_integer` | Anzahlen, Ränge |
| Gleitkommazahl | `ibexa_float` | Preise ohne Währungslogik, Messwerte |
| Kontrollkästchen | `ibexa_boolean` | Ja-oder-nein-Kennzeichen |
| E-Mail-Adresse | `ibexa_email` | Kontaktadressen |
| URL | `ibexa_url` | externe Links mit einem Text |
| ISBN | `ibexa_isbn` | Buchnummern, geprüft |
| Schlüsselwörter | `ibexa_keyword` | freie Tags |

## Datumsangaben und Auswahlen

| Feldtyp | Bezeichner | Verwenden Sie ihn für |
|---|---|---|
| Datum | `ibexa_date` | einen Tag |
| Datum und Uhrzeit | `ibexa_datetime` | einen Zeitpunkt |
| Uhrzeit | `ibexa_time` | eine Tageszeit |
| Auswahl | `ibexa_selection` | eine oder mehrere Wahlen aus einer festen Liste |
| Land | `ibexa_country` | Länder |
| Ort auf der Karte | `ibexa_gmap_location` | Breite, Länge und eine Adresse |

`exponential:timestamps:to-utc` wandelt gespeicherte Datums- und Zeitwerte in UTC um
([Konsolenbefehle](konsolenbefehle-und-umbenennungen.md)).

## Medien und Dateien

| Feldtyp | Bezeichner | Verwenden Sie ihn für |
|---|---|---|
| Bild | `ibexa_image` | ein mit dem Inhaltselement gespeichertes Bild, in Variationen ausgeliefert ([Bildvariationen](bildvariationen.md)) |
| Bild-Asset | `ibexa_image_asset` | eine Relation auf ein Bild-Inhaltselement in der Medienbibliothek, über Inhalte hinweg wiederverwendbar |
| Datei | `ibexa_binaryfile` | Downloads |
| Medien | `ibexa_media` | Audio- und Videodateien |

## Relationen und Personen

| Feldtyp | Bezeichner | Verwenden Sie ihn für |
|---|---|---|
| Inhaltsrelation (einzeln) | `ibexa_object_relation` | ein verknüpftes Inhaltselement |
| Inhaltsrelationen (mehrere) | `ibexa_object_relation_list` | mehrere verknüpfte Elemente |
| Autoren | `ibexa_author` | Namen und E-Mail-Adressen von Autoren |
| Benutzerkonto | `ibexa_user` | Anmeldename, E-Mail und Passwort eines Benutzer-Inhaltselements |

## Strukturierte Felder

| Feldtyp | Bezeichner | Paket | Verwenden Sie ihn für |
|---|---|---|---|
| Matrix | `ibexa_matrix` | `ibexa/fieldtype-matrix` | eine Tabelle aus Zeilen mit benannten Spalten |
| Inhaltsabfrage | `ibexa_content_query` | `ibexa/fieldtype-query` | ein Feld, dessen Wert das Ergebnis einer Abfrage ist, zum Beispiel "die neuesten fünf Artikel unter dieser Platzierung" |

## In v5 geänderte Bezeichner

Auf Platform v5 beginnen die Bezeichner mit `ibexa_` (zum Beispiel `ibexa_string`). Ältere Linien verwendeten Namen
`ez...`. Importieren Sie Inhaltstypen oder Templates aus einem älteren Projekt, ordnen Sie die Bezeichner zu; der Core
hat dafür ein Register von Feldtyp-Aliasen (`FieldTypeAliasRegistry` im installierten Core), aber schreiben Sie neue
Definitionen mit den Namen `ibexa_`. Den Bezeichner jedes Feldtyps in Ihrem Projekt prüfen Sie mit:

```bash
grep -rh "return 'ibexa_" vendor/se7enxweb/exponential-platform-dxp-core/src/lib/FieldType/*/Type.php | sort -u
```

## Verwandte Seiten

- [Inhalte, Inhaltstypen und Platzierungen](inhaltstypen-und-platzierungen.md)
- [Twig-Funktionen für den Alltag](twig-funktionen-fuer-den-alltag.md)
- [Umstieg von Ibexa OSS oder eZ Platform](umstieg-von-ibexa-oss-oder-ez-platform.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
