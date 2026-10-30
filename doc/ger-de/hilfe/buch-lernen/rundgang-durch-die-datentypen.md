---
title: "Rundgang durch die Datentypen"
audience: admin, builder, developer
category: books
order: 260
summary: Die Datentypen des Buchs Learning, einer nach dem anderen, mit dem, was jeder speichert, worauf Sie achten müssen und was sich geändert hat: Datumswerte, das veraltete enum, Bilder, die Typen keyword und matrix, Beziehungen.
keywords: Datentyp, Datentypen, Rundgang, Buch Learning, datatype, ezstring, eztext, ezxmltext, ezauthor, ezbinaryfile, ezboolean, ezdate, ezemail, ezenum, ezfloat, ezinteger, ezidentifier, ezimage, ezinisetting, ezisbn, ezkeyword, ezmatrix, ezmedia, ezobjectrelation, ezobjectrelationlist, ezoption, ezselection, Learning book
type: help
---
# Rundgang durch die Datentypen

Das Buch geht die Datentypen einzeln durch und sagt bei jedem, was er speichert und ob er durchsucht oder zum Sammeln
von Informationen von Besuchern verwendet werden kann ([Objekte anlegen und bearbeiten](objekte-anlegen-und-bearbeiten.md)).
Dieselben Datentypen gibt es noch, und weitere dazu. Diese Seite behält die Beschreibungen des Buchs bei und ergänzt
die heutigen Hinweise. Welchen Sie für welche Aufgabe wählen, ist Thema von [Datentypen wählen](../ersteller/website-ersteller/datentypen-waehlen.md);
die Sicht von der Code-Seite ist [Datentypen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/datentypen.md). Die
Liste auf Ihrer eigenen Website ist die, die der Klasseneditor zeigt, denn `content.ini [DataTypeSettings] AvailableDataTypes[]`
legt sie fest.

## Text und Zahlen

| Datentyp | Was er speichert | Hinweise |
|---|---|---|
| `ezstring` | eine Textzeile, mit Höchstlänge und Standardwert | |
| `eztext` | mehrere Zeilen reinen Text | |
| `ezxmltext` | strukturierten Text ([Rich-Text-Tags](rich-text-tags.md)) | Der Online-Editor schreibt ihn für den Redakteur. |
| `ezinteger`, `ezfloat` | ganze Zahlen und Dezimalzahlen, mit Minimum, Maximum und Standardwert, bei der Eingabe geprüft | Ganze Zahlen sind durchsuchbar; Dezimalzahlen laut Buch nicht. |
| `ezemail` | eine E-Mail-Adresse | Nur das Format wird geprüft, nie, ob die Adresse oder die Domain existiert. |
| `ezisbn` | eine ISBN, auf ihre Form geprüft | |
| `ezidentifier` | eine erzeugte fortlaufende Zeichenkette wie `doc-007-en`: Präfix, Suffix und Anzahl der Stellen | Gut für Seriennummern von Artikeln, Fotos oder Produkten. |
| `ezboolean` | ein Kontrollkästchen | Wird oft verwendet, um einen Teil des Templates ein- oder auszuschalten, etwa eine Kommentar-Schaltfläche. |
| `ezauthor` | einen oder mehrere Autoren (Name und E-Mail) | Hat nichts mit den Benutzern der Website zu tun. |

## Datum und Uhrzeit

`ezdate`, `ezdatetime` und `eztime` prüfen ihre Eingabe. Das Buch warnte, dass Datumswerte als UNIX-Zeitstempel
gespeichert werden und daher unter Linux auf die Jahre 1901 bis 2038 und unter Windows auf 1970 bis 2038 begrenzt sind.
Diese Grenze kam von 32-Bit-Zeitstempeln; bei einem 64-Bit-PHP, das jede unterstützte Exponential-Installation
verwendet, gilt sie nicht. Die andere Warnung bleibt bestehen: Die Prüfung ist nicht narrensicher (zum Beispiel wird der
Tag des Monats nicht gegen den Monat geprüft); testen Sie also ungewöhnliche Datumswerte, bevor Sie sich in einem
Filter oder einer Sortierung darauf verlassen.

## Dateien und Medien

| Datentyp | Was er speichert | Hinweise |
|---|---|---|
| `ezbinaryfile` | eine beliebige Datei, mit einer Upload-Grenze in MB (0 für keine) | Die Indexierung von Nicht-Text-Dateien für die Suche verwendet die Programme aus `settings/binaryfile.ini`. |
| `ezimage` | ein Bild; die Größen, die Sie definieren (Aliase wie medium, small, large), werden beim Hochladen erzeugt | Die Aliase stehen in `settings/image.ini`. Ein Alternativtext ist Pflicht, wo die Klasse ihn verlangt ([Inhaltsmodell](../../anleitungen/inhaltsmodell-und-bearbeitung.md)). |
| `ezmedia` | einen Medienclip, mit Größengrenze | Die Formate Flash, RealPlayer und Quicktime aus dem Buch sind Geschichte; verwenden Sie Dateien und einen Player im Template. |

## Auswahlen

| Datentyp | Was er speichert | Hinweise |
|---|---|---|
| `ezselection` | einen oder mehrere Werte aus einer Liste, die die Klasse definiert | Der Ersatz für `ezenum` laut Buch. |
| `ezenum` | eine Liste von Werten, einfach oder mehrfach | Seit 3.3 wegen seiner Komplexität und seines Aufwands veraltet. Er ist für alte Klassen noch installiert; verwenden Sie ihn nicht für neue. |
| `ezoption`, `ezmultioption`, `ezmultioption2`, `ezrangeoption` | Optionen mit Preisen für einen Shop | Siehe [E-Commerce](../e-commerce/lesepfad-fuer-e-commerce.md). |
| `ezkeyword` | durch Kommas getrennte Schlüsselwörter | Objekte mit einem gemeinsamen Schlüsselwort lassen sich zusammen auflisten („siehe auch“); siehe [Häufige Aufgaben mit Templates](haeufige-aufgaben-mit-templates.md). |
| `ezcountry` | ein Land (oder mehrere) | |

## Struktur

| Datentyp | Was er speichert | Hinweise |
|---|---|---|
| `ezmatrix` | eine Tabelle: eine feste Zahl von Spalten, Zeilen, die der Autor hinzufügen kann | Das Buch zählt ihn zu den vielseitigsten Typen, für eine wechselnde Zahl von Einträgen in einem Objekt. |
| `ezobjectrelation` | ein verwandtes Objekt | Kann die Klasse des gewählten Objekts nicht einschränken. |
| `ezobjectrelationlist` | viele verwandte Objekte, die optional angelegt werden können, mit einem Startknoten zum Durchsuchen | `content.ini [ObjectRelationDataTypeSettings] ClassAttributeStartNode[]` setzt den Startknoten. Nützlich, um zusammengesetzte Objekte und Kategorien aufzubauen. |
| `ezinisetting` | eine INI-Einstellung, gewählt nach Siteaccess, Datei, Abschnitt und Parameter, aus einem Objekt heraus geändert | Mit Vorsicht verwenden: Jeder, der das Objekt bearbeiten darf, ändert die Einstellung. |
| `ezuser` | das Konto (Anmeldename, E-Mail, Passwort) eines Benutzerobjekts | |
| `ezurl` | einen Link mit seinem Text | In einer Tabelle von Links gespeichert, damit Linkprüfungen ihn finden. |

Erweiterungen fügen weitere Datentypen hinzu; Beispiele sind Tags (`eztags`), ein Kartenort (`ezgmaplocation`) und
Sternebewertungen (`ezstarrating`). Unter [Datentypen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/datentypen.md)
erfahren Sie, wie einer gebaut wird.

Weiter: [Rich-Text-Tags](rich-text-tags.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 2, „Content Management with eZ publish“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: umgestellt und gekürzt; Einstellungen, Beschriftungen, Datentypen, Tags und URLs gegen Exponential 6 geprüft und korrigiert; Links auf die aktuellen Anleitungen ergänzt.
