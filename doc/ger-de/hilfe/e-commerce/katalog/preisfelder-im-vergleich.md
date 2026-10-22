---
title: Preisfelder im Vergleich
audience: ecommerce, website-creator, businesses
category: e-commerce
order: 22
summary: Die beiden Preis-Datentypen des Exponential-Shops nebeneinander - ezprice und ezmultiprice - mit eingeschlossener oder ausgeschlossener USt., den Vorgaben der Klasse, individuellen und automatischen Preisen pro Währung, den Aktualisierungsschaltflächen der Währungsliste und den Textformaten für Importe und Exporte.
keywords: ezprice, ezmultiprice, Preisfeld, mehrere Währungen, individueller Preis, automatischer Preis, Auto-Preise, Wechselkurse, inklusive USt., exklusive USt., Standardwährung, CSV-Preisformat, fromString, price field, multi-currency, custom price, automatic price, autoprices, exchange rates, VAT included, VAT excluded, default currency
type: help
---
# Preisfelder im Vergleich

Diese Seite ist für Entwickler und Händler, die das Preisfeld einer Produktklasse wählen. Exponential liefert zwei
Preis-Datentypen aus. Die Wahl entscheidet, wie Preise eingegeben werden, wie mehrere Währungen funktionieren und wie
Preise in einer Importdatei aussehen; es lohnt sich daher, sie bewusst zu treffen.

## Inhalt

1. [Nebeneinander](#nebeneinander)
2. [USt. eingeschlossen oder ausgeschlossen](#ust-eingeschlossen-oder-ausgeschlossen)
3. [Wie ezmultiprice mit Währungen umgeht](#wie-ezmultiprice-mit-währungen-umgeht)
4. [Textformate für Importe und Exporte](#textformate-für-importe-und-exporte)
5. [Welchen Sie wählen sollten](#welchen-sie-wählen-sollten)
6. [Später wechseln](#später-wechseln)

## Nebeneinander

| | `ezprice` | `ezmultiprice` |
|---|---|---|
| Preise pro Produkt | Ein Betrag | Ein Betrag pro Währung |
| Währung | `[CurrencySettings] PreferredCurrency` des Shops (`shop.ini`) | Jeder Preis trägt seinen Währungscode; die Klasse legt eine Standardwährung fest |
| USt-Typ | Pro Produkt gewählt, Vorgabe aus der Klasse | Ebenso |
| Preis enthält USt. | Pro Produkt, Vorgabe aus der Klasse | Ebenso |
| Umgerechnete Preise | Nein | Ja: „automatische“ Preise, aus Wechselkursen berechnet |
| Beteiligte Bildschirme der Administration | Inhalt bearbeiten | Inhalt bearbeiten und **Web-Shop > Währungen** (`shop/currencylist`) |
| Quelle | `kernel/classes/datatypes/ezprice/ezpricetype.php` | `kernel/classes/datatypes/ezmultiprice/ezmultipricetype.php`, `kernel/shop/classes/ezmultipricedata.php` |

## USt. eingeschlossen oder ausgeschlossen

Beide Datentypen speichern, ob der eingegebene Betrag die USt. enthält (`INCLUDED_VAT = 1`) oder nicht
(`EXCLUDED_VAT = 2`), und welcher USt-Typ gilt. Die Klassendefinition legt die Vorgaben fest, mit denen ein neues
Produkt beginnt. Wählen Sie eine Konvention für den ganzen Shop und teilen Sie sie den Redakteuren mit; eine Mischung aus
Produkten „inklusive“ und „exklusive“ ist die häufigste Ursache falscher Summen. Der USt-Typ kann „durch
USt-Abrechnungsregeln bestimmt“ sein, wenn USt-Regeln verwendet werden
([Produktkategorien und USt-Regeln](produktkategorien-und-ust-regeln.md)).

## Wie ezmultiprice mit Währungen umgeht

Jede Preiszeile eines Felds `ezmultiprice` hat einen Währungscode, einen Wert und einen Typ
(`kernel/shop/classes/ezmultipricedata.php`):

| Typ | Konstante | Bedeutung |
|---|---|---|
| Individuell | `VALUE_TYPE_CUSTOM = 1` | Von Hand eingegeben; wird vom System nie geändert |
| Automatisch | `VALUE_TYPE_AUTO = 2` | Mit dem Wechselkurs der Währung aus einem anderen Preis berechnet |

Die Währungsliste (`shop/currencylist`, Richtlinie `shop/setup`) hat die Schaltflächen, die das steuern:

| Schaltfläche | Was sie tut |
|---|---|
| Neue Währung | Fügt eine Währung hinzu; für vorhandene Produkte werden automatische Preise angelegt |
| Auto-Umrechnungsfaktoren aktualisieren | Ruft Wechselkurse mit dem konfigurierten Handler ab (ausgeliefert: `eZECB`, die Europäische Zentralbank) |
| Auto-Preis aktualisieren | Berechnet jeden automatischen Preis aus den aktuellen Kursen neu |
| Änderungen anwenden | Speichert bearbeitete Kurse und Einstellungen der aufgeführten Währungen |

In den ausgelieferten Einstellungen werden die Kurse nicht durch einen Cronjob aktualisiert; jemand drückt die
Schaltflächen, oder ein Entwickler plant das ein. Die Rundung folgt `shop.ini [MathSettings]`
([shop.ini durchgegangen](../einstellungen/shop-ini-durchgegangen.md#mathsettings)). Die tägliche Arbeit mit Währungen ist
für Inhaber unter [Preise und Währungen](../../kunden/handel/preise-und-waehrungen.md) beschrieben.

## Textformate für Importe und Exporte

Der CSV-Import und -Export und andere Werkzeuge, die die Textform eines Datentyps verwenden, lesen und schreiben diese
Formate (`fromString()` und `toString()` des jeweiligen Datentyps):

| Datentyp | Format | Beispiel |
|---|---|---|
| `ezprice` | `price|vat_type_id|vat_included`, wobei der letzte Teil `1` (eingeschlossen) oder `0` (ausgeschlossen) ist | `19.90|1|1` |
| `ezmultiprice` | `vat_type_id|vat_included|CUR|value|TYPE|CUR|value|TYPE...` mit `TYPE` aus `CUSTOM`, `AUTO` | `1|1|EUR|19.90|CUSTOM|USD|21.50|AUTO` |

Ein Wert für `ezprice`, der keine Zahl ist, wird abgelehnt statt als null gespeichert, und ein unvollständiges letztes
Währungstripel eines Werts für `ezmultiprice` wird ausgelassen. Prüfen Sie die IDs der USt-Typen unter
**Web-Shop > USt-Typen**, bevor Sie eine Datei vorbereiten.

## Welchen Sie wählen sollten

| Ihr Shop | Wählen Sie |
|---|---|
| Verkauft in einer Währung | `ezprice` |
| Zeigt Preise in mehreren Währungen, rechnet aber in einer ab | `ezprice`, und erklären Sie die Umrechnung auf der Seite |
| Verkauft in mehreren Währungen, Preise pro Markt festgelegt | `ezmultiprice` mit individuellen Preisen |
| Verkauft in mehreren Währungen, Preise folgen den Wechselkursen | `ezmultiprice` mit automatischen Preisen |

## Später wechseln

Für den häufigsten Wechsel gibt es ein Skript: `bin/php/convertprice2multiprice.php` wandelt die Attribute `ezprice`
von Inhaltsobjekten in `ezmultiprice` um, behält die vorhandenen Preise und legt die Währung des Shops an, wenn es sie
noch nicht gibt (`kernel/private/classes/commands/convertprice2multiprice.php`). Bevor Sie es ausführen:

- [ ] Erstellen Sie eine Sicherung der Datenbank ([Sicherungen und Wiederherstellungen](../../ersteller/website-ersteller/backups-und-wiederherstellungen.md)).
- [ ] Führen Sie es zuerst auf einer Kopie der Website aus und prüfen Sie die Preise unter **Web-Shop > Produkt Übersicht**.
- [ ] Testen Sie anschließend jedes Template, das einen Preis zeigt, den Warenkorb und eine vollständige Bestellung.

Für die Gegenrichtung gibt es kein Skript. Entscheiden Sie, bevor der Katalog wächst.

## Verwandte Seiten

- [Katalogmodellierung](README.md)
- [Preise und Währungen (Unternehmen)](../../unternehmen/shops/preise-und-waehrungen.md)
- [Wechselkurs-Handler](../erweitern/wechselkurs-handler.md)
- [Produktimporte und Migrationen](../betrieb/produktimporte-und-migrationen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
