---
title: Produktimporte und Migrationen
audience: ecommerce, website-creator, businesses
category: e-commerce
order: 77
summary: Einen Produktkatalog in Exponential laden - das Skript ezcsvimport und sein Dateiformat, Preis- und Bildspalten, was es nicht tut (Aktualisierungen, Kopfzeilen), Preise in ezmultiprice umwandeln, Pakete, Code und entfernte Dienste für größere Migrationen und ein Migrationsplan von einem anderen Shopsystem.
keywords: Produktimport, CSV-Import, Migration, Katalogmigration, Preisimport, Bildimport, Pakete, Umzug von einem anderen Shop, product import, CSV import, ezcsvimport, ezcsvexport, migration, catalog migration, price import, image import, storage-dir, packages, expproduct, setPrice, convertprice2multiprice, move from another shop
type: help
---
# Produktimporte und Migrationen

Diese Seite richtet sich an Entwickler, Agenturen und Händler, die einen Katalog in einen Exponential-Shop laden,
entweder einmalig aus einem anderen Shopsystem oder regelmäßig aus einer Lieferantendatei. Sie beschreibt die
vorhandenen Werkzeuge, ihr genaues Verhalten und einen Plan für eine Migration.

## Inhalt

1. [Die Werkzeuge](#die-werkzeuge)
2. [Der CSV-Import im Detail](#der-csv-import-im-detail)
3. [Die Datei vorbereiten](#die-datei-vorbereiten)
4. [Was der CSV-Import nicht tut](#was-der-csv-import-nicht-tut)
5. [Größere Migrationen](#größere-migrationen)
6. [Ein Migrationsplan](#ein-migrationsplan)

## Die Werkzeuge

| Werkzeug | Tut | Passt für |
|---|---|---|
| `bin/php/ezcsvimport.php` | Legt aus einer CSV-Datei neue Objekte einer Klasse unterhalb eines Knotens an und veröffentlicht sie | Ein erstes Laden eines einfachen Katalogs |
| `bin/php/ezcsvexport.php` | Schreibt einen Teilbaum in eine CSV-Datei je Klasse | Vorlagen für den Import, Prüfungen, Preislisten |
| `bin/php/convertprice2multiprice.php` | Wandelt `ezprice`-Felder in `ezmultiprice` um | Wechsel zu mehreren Währungen ([Preisfelder im Vergleich](../katalog/preisfelder-im-vergleich.md#später-wechseln)) |
| Pakete | Klassen und Inhalte zwischen Installationen verschieben | Einen Katalog zwischen Ihren eigenen Installationen verschieben ([Pakete](../../../anleitungen/pakete.md)) |
| Code | Inhalte aus PHP anlegen und aktualisieren | Regelmäßige Feeds, Aktualisierungen, komplexe Quellen ([Inhalte im Code anlegen](../../ersteller/anwendungsentwickler/inhalte-im-code-anlegen.md)) |
| Produktdienste von `expservices` | Produkte und Preise über HTTP, einschließlich `expproduct::setPrice` | Aktualisierungen aus einem anderen System ([Headless Commerce mit expservices](headless-commerce-mit-expservices.md)) |

Importe und Exporte werden im Audit-Trail festgehalten (`data.*`, in den Kanal `commerce` geleitet).

## Der CSV-Import im Detail

```bash
php bin/php/ezcsvimport.php --class=product --creator=14 --storage-dir=import/images 2 products.csv
```

| Argument | Bedeutung |
|---|---|
| `--class` | Bezeichner der anzulegenden Klasse |
| `--creator` | Benutzer-ID, die als Ersteller festgehalten wird |
| `--storage-dir` | Verzeichnis, das Bild-, Datei- und Medienpfaden in der Datei vorangestellt wird |
| erstes Argument | Knoten-ID des übergeordneten Ordners |
| zweites Argument | Die CSV-Datei |

Aus dem Code (`kernel/private/classes/commands/ezcsvimport.php`): Jede Zeile wird zu einem neuen Objekt; die Felder
werden **in der Reihenfolge der Attribute der Klasse** gefüllt, Spalte für Spalte, über `fromString()` des jeweiligen
Datentyps; dann wird das Objekt veröffentlicht.

## Die Datei vorbereiten

- Trennzeichen `;`, Einschluss `"`, Escape `\`.
- Keine Kopfzeile: Die erste Zeile wird ebenfalls als Produkt importiert.
- Eine Spalte je Klassenattribut, in der Reihenfolge der Klasse; lassen Sie eine Spalte leer, um das Feld leer zu lassen.
- Preise in der Textform des Datentyps: `ezprice` als `19.90|<VAT type id>|1` (1 inklusive USt, 0 exklusive),
  `ezmultiprice` als `<VAT type id>|1|EUR|19.90|CUSTOM|USD|21.50|AUTO`
  ([Preisfelder im Vergleich](../katalog/preisfelder-im-vergleich.md#textformate-für-importe-und-exporte)).
- Bilder, Dateien und Medien als Pfade relativ zu `--storage-dir`.
- Exportieren Sie ein vorhandenes Produkt mit `ezcsvexport.php` und verwenden Sie seine Zeile als Muster.

## Was der CSV-Import nicht tut

| Nicht getan | Folge |
|---|---|
| Vorhandene Objekte aktualisieren | Wer eine Datei zweimal ausführt, legt jedes Produkt zweimal an |
| Nach Artikelnummer abgleichen | Aktualisierungen brauchen Code oder Dienste |
| Eine Kopfzeile überspringen | Entfernen Sie sie vor dem Import |
| Spalten nach Namen zuordnen | Eine Umsortierung der Klassenattribute bricht alte Dateien |
| Ordner anlegen | Importieren Sie jede Kategorie in ihren eigenen Ordner, ein Lauf je Ordner |

## Größere Migrationen

Aus einem anderen Shopsystem ist der Katalog nur ein Teil:

| Daten | Vorgehen |
|---|---|
| Produkte, Preise, Bilder | CSV-Import für einfache Kataloge; Code für Optionen, Beziehungen und Übersetzungen |
| Kategorien | Ordner zuerst anlegen, von Hand oder im Code |
| Kunden | Nur mit einer Rechtsgrundlage und einem Plan für Passwörter; oft besser, Kunden sich neu registrieren zu lassen |
| Frühere Bestellungen | Meist im alten System behalten oder für die Unterlagen exportiert; die Bestelltabellen sind nicht für Importe ausgelegt |
| URLs | Weiterleitungen von alten Produktadressen ([Weiterleitungen und verschobene Inhalte](../../ersteller/website-ersteller/weiterleitungen-und-verschobene-inhalte.md)) |

Der Migrationskurs für Trainer behandelt den Umzug von Sites im Allgemeinen ([Migrationskurs](../../trainer/migration/README.md)).

## Ein Migrationsplan

1. Modellieren Sie die Klassen ([Katalogmodellierung im Detail](../katalog/katalogmodellierung-im-detail.md)).
2. Legen Sie USt-Typen, Produktkategorien und Währungen an; notieren Sie ihre IDs.
3. Exportieren Sie eine Stichprobe aus dem alten System; bringen Sie sie in die Spaltenreihenfolge und das Preisformat.
4. Importieren Sie in eine Testkopie; prüfen Sie **Web-Shop > Produkt Übersicht** und zehn Produkte von Hand.
5. Korrigieren Sie den Konverter, nicht die importierten Daten; wiederholen Sie, bis alles sauber ist.
6. Richten Sie Weiterleitungen von alten Adressen ein.
7. Importieren Sie vor der Eröffnung in den Live-Shop und führen Sie dann den Testplan für den Shop aus
   ([Einen Shop testen](einen-shop-testen.md)).

## Verwandte Seiten

- [Einen Shop betreiben](README.md)
- [Katalogmodellierung](../katalog/README.md)
- [Pakete und ezpkg](../../ersteller/anwendungsentwickler/pakete-und-ezpkg.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
