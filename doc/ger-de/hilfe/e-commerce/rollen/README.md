---
title: Pfade nach Rolle
audience: ecommerce, customer, businesses
category: e-commerce
order: 10
summary: Wer in einem Exponential-Shop was tut - Händler, Shop-Manager, Entwickler und Agentur - mit den Shop-Richtlinienfunktionen, die jede Rolle braucht, der Aufteilung zwischen Händlerarbeit und Entwicklerarbeit und einem Pfad aus Seiten für jede.
keywords: Rollen, Händler, Shop-Manager, Entwickler, Agentur, Partner, Shop-Richtlinien, shop/administrate, shop/setup, shop/setstatus, shop/edit_status, shop/buy, Zuständigkeiten, roles, merchant, store manager, developer, agency, shop policies, responsibilities
type: help
---
# Pfade nach Rolle

Diese Seite ist für alle, die entscheiden müssen, wer in einem Shop-Team was tut. Sie teilt die Arbeit eines
Exponential-Shops in vier Rollen auf, nennt die Shop-Berechtigungen, die jede braucht, und verweist auf einen Lesepfad pro
Rolle.

## Inhalt

1. [Vier Rollen](#vier-rollen)
2. [Händlerarbeit und Entwicklerarbeit](#händlerarbeit-und-entwicklerarbeit)
3. [Die Shop-Richtlinienfunktionen](#die-shop-richtlinienfunktionen)
4. [Wer entscheidet, wer handelt, wer gefragt wird](#wer-entscheidet-wer-handelt-wer-gefragt-wird)
5. [Die Pfade](#die-pfade)

## Vier Rollen

| Rolle | Typische Person | Verantwortet |
|---|---|---|
| Händler | Der Inhaber, eine Geschäftsführerin, ein Produktmanager | Entscheidungen: was verkauft wird, Preise, USt., Zahlungsanbieter, Rabatte, Budget |
| Shop-Manager | Shop-Mitarbeitende, Kundenservice, Versand | Täglicher Betrieb: Bestellungen, Status, Kunden, Rücksendungen |
| Entwickler | Interne Entwickler oder die Entwickler eines Partners | Produktklassen, Templates, Einstellungen, Workflows, Handler, Gateways |
| Agentur | Ein Partner, der Shops für Kunden baut und pflegt | Planung, Umsetzung, Tests, Übergabe, Pflege |

Kleine Shops vereinen Rollen: Der Inhaber ist oft Händler und Shop-Manager zugleich. Halten Sie die Berechtigungen
trotzdem getrennt, damit ein Fehler in der täglichen Arbeit nicht die USt. oder die Preise ändern kann.

## Händlerarbeit und Entwicklerarbeit

Manche Shop-Aufgaben erledigen Mitarbeitende in der Administration; andere brauchen eine Änderung in Einstellungsdateien
oder im Code. Wer weiß, was was ist, wartet nicht auf die falsche Person.

| Aufgabe | Erledigt von | Wo |
|---|---|---|
| Ein Produkt, seinen Preis und seine Bilder hinzufügen oder bearbeiten | Händler oder Redakteur | Inhalt des Produkts bearbeiten |
| USt-Typen und USt-Regeln anlegen | Händler | **Web-Shop > USt-Typen**, `shop/vatrules` |
| Währungen hinzufügen, Wechselkurse aktualisieren | Händler | `shop/currencylist` |
| Rabattgruppen und -regeln anlegen | Händler | `shop/discountgroup` |
| Den Status einer Bestellung ändern, Bestellungen archivieren | Shop-Manager | `shop/orderview`, `shop/orderlist` |
| Einen eigenen Bestellstatus hinzufügen | Händler mit `shop/edit_status` | `shop/status` |
| USt-Regeln einschalten (USt-Handler) | Entwickler | `shop.ini [VATSettings] Handler` |
| Das Formular für Kundendaten wählen (Konto-Handler) | Entwickler | `shopaccount.ini [AccountSettings] Handler` |
| Ein Zahlungs-Gateway anbinden | Entwickler | `paymentgateways.ini`, ein Workflow an `shop_checkout` |
| Versandregeln über einen Pauschalbetrag hinaus | Entwickler | ein Versand-Handler ([Eigener Versand-Handler](../erweitern/eigener-versand-handler.md)) |
| Die Adresse des Warenkorbs umbenennen | Entwickler | `shop.ini [BasketSettings] BasketViewName` |
| Dauerhafte Belege einschalten | Entwickler | `shop.ini [OrderViewSettings] OrderLinkView` |

## Die Shop-Richtlinienfunktionen

Das Shop-Modul hat fünf Richtlinienfunktionen (`kernel/shop/module.php`). Jede Shop-View braucht eine davon, außer der
Belegansicht `shop/orderreceipt`, die keine Richtlinienfunktion hat, weil ihr signiertes Token der Schlüssel ist
([Dauerhafte Bestellbelege](../../../funktionen/6.0/bestellbelege.md)).

| Funktion | Views | Geben Sie sie |
|---|---|---|
| `buy` | `add`, `basket`, `updatebasket`, `register`, `userregister`, `confirmorder`, `checkout`, `orderview`, `wishlist`, `preferredcurrency`, `setpreferredcurrency`, `setusercountry` | Besuchern und Kunden (auch der anonymen Rolle, wenn Gäste kaufen dürfen) |
| `administrate` | `dashboard`, `orderlist`, `archivelist`, `archiveorder`, `unarchiveorder`, `removeorder`, `customerlist`, `customerorderview`, `statistics`, `productsoverview` | Shop-Managern und dem Händler |
| `setup` | `vattype`, `vatrules`, `editvatrule`, `productcategories`, `discountgroup`, `discountgroupedit`, `discountruleedit`, `discountgroupview`, `currencylist`, `editcurrency` | Dem Händler oder wem auch immer Preise und Steuern gehören |
| `edit_status` | `status` (die Liste der Bestellstatus) | Dem Händler |
| `setstatus` | `setstatus`, begrenzt durch `FromStatus` und `ToStatus` | Shop-Managern und Mitarbeitenden im Versand, begrenzt auf ihre Schritte |

Wie die Einschränkungen `FromStatus` und `ToStatus` zusammenwirken, erklärt
[Statusübergänge und Berechtigungen](../bestellungen/statusuebergaenge-und-berechtigungen.md). Rollen werden wie üblich angelegt
([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).

> **Hinweis**
> `administrate` umfasst das Entfernen von Bestellungen (`shop/removeorder`). Wenn Shop-Manager keine Bestellungen löschen
> sollen, denken Sie beim Vergeben daran und setzen Sie stattdessen auf das Archivieren
> ([Archivieren, Entfernen und Aufräumen](../bestellungen/archivieren-entfernen-und-aufraeumen.md)).

## Wer entscheidet, wer handelt, wer gefragt wird

| Entscheidung | Händler | Shop-Manager | Entwickler | Agentur |
|---|---|---|---|---|
| Zahlungsanbieter | entscheidet | wird gefragt | setzt um | berät |
| Einrichtung der USt. | entscheidet, mit einer Steuerberatung | wird informiert | schaltet den Handler ein | berät |
| Verwendete Bestellstatus | entscheidet | wird gefragt | passt bei Bedarf Templates an | berät |
| Versandmodell | entscheidet | wird gefragt | schreibt oder konfiguriert | berät |
| Starttermin | entscheidet | wird gefragt | bestätigt die Tests | bestätigt die Tests |

## Die Pfade

- [Pfad für Händler](pfad-fuer-haendler.md)
- [Pfad für Shop-Manager](pfad-fuer-shop-manager.md)
- [Pfad für Entwickler](pfad-fuer-entwickler.md)
- [Pfad für Agenturen](pfad-fuer-agenturen.md)

## Verwandte Seiten

- [E-Commerce: hier beginnen](../e-commerce-hier-beginnen.md)
- [Ihren Shop betreiben](../../kunden/handel/README.md), mit einer Tabelle „wer meist was tut“ für einen laufenden Shop
- [Shop-Planung für kleine Unternehmen](../../unternehmen/shops/shop-planung-fuer-kleine-unternehmen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
