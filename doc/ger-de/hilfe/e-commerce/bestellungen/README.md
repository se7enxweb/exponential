---
title: Bestellbetrieb
audience: ecommerce, customer, businesses
category: e-commerce
order: 50
summary: Bestellungen in einem Exponential-Shop über die tägliche Routine hinaus betreiben - der Lebenszyklus einer Bestellung in Daten und Status, durch Richtlinien begrenzte Statusübergänge, Archivieren gegenüber Entfernen gegenüber vollständigem Löschen und Bestellmails und Belege aus Sicht der Betreiber.
keywords: Bestellbetrieb, Lebenszyklus einer Bestellung, Bestellstatus, Statusübergänge, FromStatus, ToStatus, Archiv, Bestellung entfernen, vollständig löschen, Bestellmail, Belege, Shop-Betrieb, order operations, order lifecycle, order status, status transitions, archive, remove order, purge, order mail, receipts, store operations
type: help
---
# Bestellbetrieb

Dieser Teil ist für Shop-Manager, Händler und die Entwickler, die sie unterstützen. Die tägliche Bearbeitung von
Bestellungen steht unter [Bestellungen im Alltag](../../kunden/handel/bestellungen-im-alltag.md) und die Status in
einfacher Sprache unter [Bestellstatus](../../kunden/handel/bestellstatus.md). Diese Seiten erklären, was das System
darunter tut, damit Sie einen Statusablauf entwerfen, die richtigen Berechtigungen vergeben und sicher zwischen Archivieren
und Löschen wählen können.

## Inhalt

1. [Die Seiten dieses Teils](#die-seiten-dieses-teils)
2. [Eine Bestellung in einer Tabelle](#eine-bestellung-in-einer-tabelle)
3. [Wo Bestellungen bearbeitet werden](#wo-bestellungen-bearbeitet-werden)

## Die Seiten dieses Teils

| Seite | Beantwortet |
|---|---|
| [Der Lebenszyklus einer Bestellung im Betrieb](der-lebenszyklus-einer-bestellung-im-betrieb.md) | Die Phasen einer Bestellung, die siebzehn Status als Ablauf, Regeln für Umsatz und „offen“ |
| [Statusübergänge und Berechtigungen](statusuebergaenge-und-berechtigungen.md) | Wie `FromStatus` und `ToStatus` begrenzen, wer was ändern darf, mit Rollenentwürfen |
| [Archivieren, Entfernen und Aufräumen](archivieren-entfernen-und-aufraeumen.md) | Was Archivieren, Entfernen und das Löschen aller Bestellungen mit den Daten tun, und die Ereignisse im Prüfpfad |
| [Bestellmails und Belege für Betreiber](bestellmails-und-belege-fuer-betreiber.md) | Wer welche Mail von welcher Adresse erhält und wie Belege dem Kundenservice helfen |

## Eine Bestellung in einer Tabelle

| Teil | Gespeichert in | Hinweise |
|---|---|---|
| Die Bestellung | `ezorder` | ID, Bestellnummer, Benutzer, E-Mail, angelegt, Status, Archiv-Kennzeichen, Temporär-Kennzeichen, Kundendaten (`data_text_1`) |
| Produktzeilen | `ezproductcollection_item` (über die Produktsammlung der Bestellung) | Name, Preis, USt., Rabatt, Menge zum Zeitpunkt des Kaufs |
| Gewählte Optionen | `ezproductcollection_item_opt` | Name, Wert, Preis jeder Option |
| Zusätzliche Zeilen | `ezorder_item` | Versand und andere von Ereignissen hinzugefügte Zeilen |
| Statusverlauf | `ezorder_status_history` | Status, wer ihn geändert hat, wann |
| Währung | `ezproductcollection.currency_code` | Die Währung, in der die Bestellung aufgegeben wurde |

Weil die Produktzeilen Name und Preis zum Zeitpunkt des Kaufs behalten, ändern spätere Änderungen an einem Produkt nie
eine bestehende Bestellung.

## Wo Bestellungen bearbeitet werden

| Bildschirm | Adresse | Richtlinie |
|---|---|---|
| Bestellliste | `shop/orderlist` | `shop/administrate` |
| Bestellansicht | `shop/orderview/<id>` | `shop/buy` für die eigenen Bestellungen der Kunden; Mitarbeitende verwalten über den Reiter Web-Shop |
| Statusänderung | `shop/setstatus` | `shop/setstatus` |
| Statusliste | `shop/status` | `shop/edit_status` |
| Archiv | `shop/archivelist`, `shop/archiveorder`, `shop/unarchiveorder` | `shop/administrate` |
| Entfernen | `shop/removeorder` | `shop/administrate` |
| Kunden | `shop/customerlist`, `shop/customerorderview` | `shop/administrate` |

## Verwandte Seiten

- [Bestellungen und Bestellstatus](../../unternehmen/shops/bestellungen-und-bestellstatus.md)
- [Shop-Dashboard und Bestellstatus](../../../funktionen/6.0/shop-dashboard.md)
- [Sortierung der Bestellliste](../../../funktionen/6.0/sortierung-der-bestellliste.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
