---
title: Inhaltsklassen auf externe Daten abbilden
audience: integrator, developer
category: integration
order: 290
summary: Datensätze eines anderen Systems in Inhaltsobjekte verwandeln - die Klasse entwerfen, Datentypen wählen, die Textformate von fromString() der gängigen Datentypen, Bilder und Dateien aus einem DAM, Beziehungen, Sprachen, Platzierungen und eine Vorlage für eine Abbildungstabelle.
keywords: Abbildung, Mapping, Inhaltsklasse, Datentyp, fromString, toString, ezstring, eztext, ezxmltext, ezboolean, ezinteger, ezdatetime, ezimage, ezbinaryfile, ezobjectrelationlist, ezselection, ezprice, ezurl, ezkeyword, eztags, DAM, PIM, Sprachen, Platzierungen, Feldzuordnung, mapping, content class, datatype, languages, locations, field mapping
type: help
---
# Inhaltsklassen auf externe Daten abbilden

Diese Seite ist für Integrationsentwickler und Inhaltsarchitekten, die entscheiden, wie Datensätze eines PIM, eines
ERP, eines DAM oder eines Veranstaltungssystems zu Inhalten in Exponential werden. Die Abbildung ist der Vertrag der
Integration: Sobald Daten fließen, ist eine Änderung eine Migration. Legen Sie sie vor dem ersten Import schriftlich
fest.

## Inhalt

1. [Die Klasse für die Integration entwerfen](#die-klasse-für-die-integration-entwerfen)
2. [Textformate der gängigen Datentypen](#textformate-der-gängigen-datentypen)
3. [Bilder und Dateien aus einem DAM](#bilder-und-dateien-aus-einem-dam)
4. [Beziehungen zwischen importierten Datensätzen](#beziehungen-zwischen-importierten-datensätzen)
5. [Sprachen und Platzierungen](#sprachen-und-platzierungen)
6. [Eine Abbildungstabelle](#eine-abbildungstabelle)

## Die Klasse für die Integration entwerfen

| Entscheidung | Empfehlung |
|---|---|
| Eine Klasse pro Art von Datensatz | `product`, `product_category`, `event`; überladen Sie keinen allgemeinen `article` |
| Namen der Bezeichner | stabil und in Kleinbuchstaben (`sku`, `price`, `stock`); der Import verwendet Bezeichner, nie Attribut-IDs |
| Felder, die der Quelle gehören | in der Attributbeschreibung kennzeichnen („vom ERP-Import gesetzt; Änderungen werden überschrieben“) und eine Rolle erwägen, die sie nicht bearbeiten kann |
| Felder, die Redakteuren gehören | getrennt halten (ein redaktionelles `intro` neben der importierten `description`); der Import berührt sie nie |
| Versteckte technische Felder | ein Textattribut für die Prüfsumme oder letzte Änderung der Quelle, in Templates nicht angezeigt |
| Pflichtattribute | nur, was die Quelle immer liefert; ein Pflichtfeld, das die Quelle manchmal leer lässt, lässt das Anlegen scheitern |
| Durchsuchbar | markieren, wonach Besucher suchen; technische Felder aus dem Index lassen |

Liefern Sie die Klasse mit Ihrer Erweiterung als Paket aus, damit jede Umgebung dieselben Bezeichner hat
([Pakete](pakete-importieren-und-exportieren.md)). Das Inhaltsmodell einer ganzen Website ist ein Thema für Workshops
([Workshops zur Inhaltsmodellierung](../fuehrung/workshops-zur-inhaltsmodellierung.md)).

## Textformate der gängigen Datentypen

`eZContentFunctions::createAndPublishObject()` und `updateAndPublishObject()` nehmen jeden Wert als Zeichenkette und
übergeben ihn an `fromString()` des Datentyps; `toString()` gibt dasselbe Format zurück. Aus den Datentypen des Kernels
(`kernel/classes/datatypes/<type>/<type>type.php`):

| Datentyp | Format | Beispiel |
|---|---|---|
| `ezstring`, `eztext`, `ezemail`, `ezisbn` | der Text | `Trail shoe 42` |
| `ezinteger`, `ezfloat` | eine Zahl | `12`, `19.95` |
| `ezboolean` | `1` oder `0` (anderer Text wird auf 0 oder 1 abgebildet) | `1` |
| `ezdate`, `ezdatetime` | ein Unix-Zeitstempel | `1793404800` |
| `ezprice` | `Preis\|ID des USt-Typs\|USt enthalten (1 oder 0)` | `119.00\|1\|1` |
| `ezurl` | `Adresse\|Linktext` (unsichere Schemata werden abgewiesen) | `https://example.com/manual.pdf\|Manual` |
| `ezselection` | Namen der Optionen, getrennt durch `\|` | `Red\|Blue` |
| `ezkeyword` | Schlüsselwörter, durch Kommas getrennt | `trail,running` |
| `ezobjectrelationlist` | Objekt-IDs, getrennt durch `-` | `5678-5679` |
| `ezauthor` | Autoren, getrennt durch `&`, jeder `Name\|E-Mail\|ID` | `Ada Example\|ada@example.com\|0` |
| `ezimage` | `Pfad\|Alternativtext`; der Pfad muss eine Datei innerhalb der Installation, ihres var- oder temporären Verzeichnisses sein | `var/import/img/10442.jpg\|Trail shoe, side view` |
| `ezbinaryfile`, `ezmedia` | ein Dateipfad | `var/import/files/manual-10442.pdf` |
| `ezxmltext` | das gespeicherte XML (siehe unten) | `<?xml version="1.0" encoding="utf-8"?><section ...><paragraph>...</paragraph></section>` |

Lesen Sie im Zweifel `fromString()` des Datentyps: Die Methode ist kurz, und mehrere wurden in 6.0.15 gehärtet, sodass
sie ungültige Werte abweisen, statt sie zu speichern ([Die API von eZDataType](../erweiterungsentwicklung/teil-8-referenz/ezdatatype-api.md)).

**Rich Text.** `ezxmltext` speichert sein eigenes XML (`section`, `paragraph`, `header`, `link` ...), kein HTML.
Wandeln Sie das HTML der Quelle in Ihrem Import in dieses XML um, halten Sie die Menge der erlaubten Elemente klein,
und maskieren Sie Text. Die Erweiterung `nxc_powercontent`, die von der REST-Route zum Anlegen verwendet wird,
akzeptiert HTML für Attribute vom Typ `ezxmltext` ([nxc_powercontent](../../funktionen/6.0/erweiterungen/nxc_powercontent.md)).
Datentypen aus Erweiterungen wie `eztags` haben eigene Formate; lesen Sie deren `fromString()`.

## Bilder und Dateien aus einem DAM

1. Laden Sie die Datei in ein Verzeichnis unter `var/` der Installation herunter (zum Beispiel `var/import/`), nie in
   das Web-Wurzelverzeichnis einer anderen Website.
2. Geben Sie im Attributwert den Pfad relativ zur Installation an, oder übergeben Sie einmal `storage_dir` und pro
   Datensatz die Dateinamen (`createAndPublishObject()` setzt `storage_dir` für `ezimage`, `ezbinaryfile` und `ezmedia`
   davor).
3. Entfernen Sie nach dem Import die heruntergeladenen Kopien; der Datentyp hat seine eigene gespeichert.
4. Vergleichen Sie vor einem erneuten Herunterladen eine Prüfsumme der Quelldatei, damit ein unverändertes Bild nicht
   erneut importiert wird.

Fragen Sie bei großen Medienbibliotheken, ob die Website die Dateien überhaupt braucht oder nur einen Link zur
Auslieferungs-URL des DAM (ein Attribut `ezurl` oder Text), was das Kopieren vermeidet.

## Beziehungen zwischen importierten Datensätzen

Beziehungen speichern Objekt-IDs, die Sie erst kennen, wenn beide Objekte existieren:

1. Importieren Sie zuerst alle Datensätze der bezogenen Klasse (Kategorien vor Produkten).
2. Lösen Sie jeden Verweis der Quelle über seine Remote-ID auf: `eZContentObject::fetchByRemoteID( 'pim-category-shoes' )`,
   dann verwenden Sie dessen `id` im Wert von `ezobjectrelationlist`.
3. Wenn sich ein Verweis noch nicht auflösen lässt, importieren Sie ohne ihn und setzen ihn in einem zweiten Durchlauf.

## Sprachen und Platzierungen

- **Sprachen**: ein Objekt pro Datensatz, eine Übersetzung pro Sprache. Übergeben Sie `language` (zum Beispiel
  `ger-DE`) an `createAndPublishObject()` für die erste Sprache und an `updateAndPublishObject()` für jede weitere. Die
  Sprache muss in der Installation existieren ([Inhaltssprachen](../../anleitungen/inhaltssprachen.md)).
- **Platzierungen**: `parent_node_id` gibt die Hauptplatzierung an. Ein Datensatz, der an mehrere Stellen gehört,
  bekommt weitere Platzierungen (`expobject` und die Inhaltsjobs bieten das Hinzufügen von Platzierungen an; in PHP die
  Knotenoperationen). Entscheiden Sie, ob ein Kategoriewechsel an der Quelle das Objekt **verschiebt**
  (`eZContentObjectTreeNodeOperations::move()`) oder eine Platzierung hinzufügt.
- **Sektionen und Zustände**: `section_id` beim Anlegen; Objektzustände für einen Workflow-Status wie
  „zurückgezogen“.

## Eine Abbildungstabelle

Übernehmen Sie dies in die Schnittstellenspezifikation, eine Tabelle pro Klasse:

```text
Class: product (identifier)          Source: ERP article export, file articles.csv
remote_id scheme: erp-product-<ARTNR>
Parent: node <id> "Products" (main location); category as an extra location

Source field   | Attribute     | Datatype   | Transformation              | Owner  | Required
---------------+---------------+------------+-----------------------------+--------+---------
ARTNR          | sku           | ezstring   | as is                       | ERP    | yes
NAME_EN        | name          | ezstring   | trim                        | ERP    | yes
NAME_DE        | name (ger-DE) | ezstring   | translation                 | ERP    | no
PRICE          | price         | ezprice    | "<PRICE>|1|1"               | ERP    | yes
STOCK          | stock         | ezinteger  | as is                       | ERP    | no
IMAGE_URL      | image         | ezimage    | download to var/import/     | DAM    | no
-              | intro         | ezxmltext  | not imported                | editor | no
```

Die Spalten bedeuten: Quellfeld, Attribut, Datentyp, Umwandlung, Verantwortlicher, Pflicht.

## Verwandte Seiten

- [Idempotente Importe mit remote_id als Schlüssel](idempotente-importe-ueber-die-remote-id.md)
- [Ein Importskript schreiben](ein-importskript-schreiben.md)
- [Inhaltsklassen für Ersteller](../erweiterungsentwicklung/teil-2-builder/inhaltsklassen-fuer-builder.md)
- [Vorlage für eine Schnittstellenspezifikation](vorlage-fuer-eine-schnittstellenspezifikation.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
