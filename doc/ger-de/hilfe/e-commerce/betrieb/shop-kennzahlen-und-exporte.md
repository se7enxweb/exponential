---
title: Shop-Kennzahlen und Exporte
audience: ecommerce, customer, businesses
category: e-commerce
order: 76
summary: Kennzahlen und Daten aus einem Exponential-Shop herausholen - das Dashboard und die Bestellstatistiken, Bestelldaten als JSON über die Bestelldienste von expservices, Produktdaten als CSV mit ezcsvexport, der Audit-Export und was für einen Buchhaltungsexport gebaut werden muss.
keywords: Berichte, Export, Bestellexport, Buchhaltung, Statistiken, Monatsende, Kennzahlen, reporting, export, order export, accounting, statistics, shop/statistics, exporder, expservices, JSON, ezcsvexport, CSV, audit export, month end, figures
type: help
---
# Shop-Kennzahlen und Exporte

Diese Seite richtet sich an Händler, an die Personen, die Kennzahlen für den Steuerberater aufbereiten, und an
Entwickler, die Shop-Daten außerhalb der Administration brauchen. Die Bestellliste hat keinen Tabellenexport, deshalb
listet diese Seite auf, was es gibt, was es jeweils liefert und was noch zu bauen ist.

## Inhalt

1. [Was es gibt](#was-es-gibt)
2. [Kennzahlen zum Monatsende](#kennzahlen-zum-monatsende)
3. [Bestelldaten als JSON](#bestelldaten-als-json)
4. [Produktdaten als CSV](#produktdaten-als-csv)
5. [Audit-Exporte](#audit-exporte)
6. [Ein Buchhaltungsexport](#ein-buchhaltungsexport)

## Was es gibt

| Bedarf | Werkzeug | Ausgabe |
|---|---|---|
| Verkäufe pro Produkt und Monat | **Web-Shop > Statistik** (`shop/statistics`) | Bildschirm |
| Trends, offene Bestellungen, abgebrochene Warenkörbe | Shop-Dashboard | Bildschirm |
| Bestellungen, Positionen, Kunden, Statistiken | Bestelldienste von `expservices` (`exporder::list`, `view`, `items`, `statistics`, `customers` und mehr) | JSON |
| Produkte eines Teilbaums | `bin/php/ezcsvexport.php` | CSV, eine Datei je Klasse |
| Wer im Handel was geändert hat | Audit-Export (`audit/export`) | CSV, JSON Lines oder JSON |

## Kennzahlen zum Monatsende

1. **Web-Shop > Statistik** für den Monat: Produkte, Mengen und Summen je Währung.
2. Die 30-Tage-Kennzahlen des Dashboards für Trends und den Anteil fehlgeschlagener, stornierter und erstatteter
   Bestellungen, die im Dashboard als Bestellungen zählen, aber nicht als Umsatz.
3. Tragen Sie die Kennzahlen in Ihren Monatsbericht ein, wie unter
   [Shop-Berichte](../../kunden/handel/shop-berichte.md#ein-einseitiger-monatsbericht) beschrieben.

Die Statistiken zählen nur aufgegebene Bestellungen, nie temporäre (`eZOrder::orderStatistics()` filtert
`is_temporary = 0`); archivierte Bestellungen sind enthalten.

## Bestelldaten als JSON

Die Erweiterung `expservices`, die im Ordner `extension/` des Repositorys mitgeliefert wird, bietet die Daten des Shops
als entfernte Dienste an ([Headless Commerce mit expservices](headless-commerce-mit-expservices.md)). Für Kennzahlen
und Exporte:

| Dienst | Liefert | Richtlinie |
|---|---|---|
| `exporder::list` | Bestellungen, filterbar nach Status und archiviert, sortiert nach Anlage, ID, Summe oder Benutzername, seitenweise | `shop/administrate` |
| `exporder::view`, `exporder::items`, `exporder::history` | Eine Bestellung, ihre Positionen, ihre Statushistorie | `shop/administrate` |
| `exporder::statistics` | In einem Jahr oder Monat verkaufte Produkte mit Summen je Währung | `shop/administrate` |
| `exporder::customers`, `exporder::customerOrders` | Kunden und ihre Bestellungen | `shop/administrate` |

Ein Skript mit einem persönlichen API-Token eines eigenen Benutzers (mit `shop/administrate` und sonst nichts) kann
diese abrufen und eine Tabelle schreiben. Die Erweiterung wurde als Version 0.1.0 hinzugefügt, und ihre Funktionsseite
nennt sie einen ersten Entwurf; testen Sie die Dienste, auf die Sie sich verlassen, nach Updates.

## Produktdaten als CSV

```bash
php bin/php/ezcsvexport.php --storage-dir=export <node id>
```

schreibt für den Teilbaum eine CSV-Datei je Inhaltsklasse, Preise im Textformat des Preis-Datentyps
([Preisfelder im Vergleich](../katalog/preisfelder-im-vergleich.md#textformate-für-importe-und-exporte)). Verwenden Sie es
für eine Preisliste, eine Katalogprüfung oder als Vorlage für einen Import.

## Audit-Exporte

Mit `audit/export` exportiert der Reiter **Audit** den Filter der Konsole als CSV, JSON Lines oder JSON, zum Beispiel
alle `commerce.*`-Ereignisse eines Monats ([Audit-Trail für den Handel](audit-trail-fuer-den-handel.md)).

## Ein Buchhaltungsexport

Ein Export in ein Buchhaltungssystem (Bestellungen mit USt je Satz, Zahlungen, Erstattungen) ist nicht im Kernel. Bauen
Sie ihn als:

- ein Skript, das `exporder::list` und `exporder::view` oder direkt die Bestellklassen liest und nach
  `is_temporary = 0` filtert; oder
- ein Ereignis am After-Trigger von `shop_checkout`, das jede aufgegebene Bestellung sofort sendet
  ([Workflows an Shop-Triggern](../kasse/workflows-an-shop-triggern.md)).

Stimmen Sie die Felder zuerst mit Ihrem Steuerberater ab. Lassen Sie Kundendaten weg, sofern das Buchhaltungssystem sie
nicht braucht.

## Verwandte Seiten

- [Einen Shop betreiben](README.md)
- [Shop-Dashboard und Berichte](../../unternehmen/shops/shop-uebersicht-und-berichte.md)
- [Analysen für Shops](analysen-fuer-shops.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
