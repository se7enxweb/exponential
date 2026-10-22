---
title: Pfad für Shop-Manager
audience: ecommerce, customer, businesses
category: e-commerce
order: 12
summary: Ein Pfad für den Shop-Manager, der einen Exponential-Shop täglich betreibt - die Bildschirme im Reiter Web-Shop, die Berechtigungen, um die Sie bitten sollten, wie Bestellungen weiterlaufen, wovor das Dashboard warnt und wo die Routinen und Checklisten stehen.
keywords: Shop-Manager, Shop-Mitarbeitende, Kundenservice, Reiter Web-Shop, Dashboard, Bestellliste, Bestellansicht, Bestellstatus, Archiv, Kunden, Rücksendungen, Berechtigungen, tägliche Arbeit, store manager, shop staff, customer service, Store tab, dashboard, order list, order view, order status, archive, customers, returns, permissions, daily work
type: help
---
# Pfad für Shop-Manager

Diese Seite ist für den Shop-Manager: die Person, die jeden Morgen den Reiter **Web-Shop** öffnet, Bestellungen
bearbeitet, Kunden antwortet und die Bestellliste sauber hält. Sie zeigt, welche Bildschirme Sie nutzen, um welche
Berechtigungen Sie bitten sollten und wo die Routinen aufgeschrieben sind.

## Inhalt

1. [Ihre Bildschirme](#ihre-bildschirme)
2. [Die Berechtigungen, um die Sie bitten sollten](#die-berechtigungen-um-die-sie-bitten-sollten)
3. [Wie eine Bestellung zu Ihnen kommt](#wie-eine-bestellung-zu-ihnen-kommt)
4. [Eine Bestellung weiterbewegen](#eine-bestellung-weiterbewegen)
5. [Wenn etwas falsch aussieht](#wenn-etwas-falsch-aussieht)
6. [Ihre Routinen](#ihre-routinen)

## Ihre Bildschirme

Alle Bildschirme sind Views des Shop-Moduls und liegen unter dem Reiter **Web-Shop** der Administration.

| Bildschirm | Adresse | Verwenden Sie ihn für |
|---|---|---|
| Dashboard | `shop/dashboard` | Den ersten Blick am Tag: offene Bestellungen, zu lange wartende Bestellungen, verlassene Warenkörbe, „Nächste Schritte“ |
| Bestellliste | `shop/orderlist` | Alle aktiven Bestellungen; nach jeder Spalte sortierbar ([Sortierung der Bestellliste](../../../funktionen/6.0/sortierung-der-bestellliste.md)) |
| Bestellansicht | `shop/orderview/<id>` | Eine Bestellung: Posten, Summen, Kundendaten, Status und Statusverlauf |
| Archiv | `shop/archivelist` | Abgeschlossene Bestellungen, aus der Bestellliste verschoben |
| Kunden | `shop/customerlist`, `shop/customerorderview` | Wer gekauft hat, und jede Bestellung eines Kunden |
| Statistik | `shop/statistics` | Verkaufte Produkte und Summen pro Jahr und Monat |
| Produkt Übersicht | `shop/productsoverview` | Produkte einer Klasse mit ihren Preisen in einer Liste |

Was jeder Abschnitt des Dashboards zeigt, steht unter [Shop-Dashboard und Berichte](../../unternehmen/shops/shop-uebersicht-und-berichte.md).

## Die Berechtigungen, um die Sie bitten sollten

| Sie müssen | Bitten Sie um |
|---|---|
| Dashboard, Bestellungen, Kunden und Statistik sehen; Bestellungen archivieren | `shop/administrate` |
| Bestellstatus ändern | `shop/setstatus`, möglicherweise begrenzt durch `FromStatus` und `ToStatus` |
| Nichts an USt., Währungen oder Rabatten | nicht `shop/setup` |

Die Einschränkungen erklärt [Statusübergänge und Berechtigungen](../bestellungen/statusuebergaenge-und-berechtigungen.md).

## Wie eine Bestellung zu Ihnen kommt

Eine Bestellung erscheint erst in der Bestellliste, wenn der Bestellabschluss beendet ist. Vorher ist sie eine
**temporäre Bestellung**, die Sie in der Liste nicht sehen; das Dashboard zählt solche Fälle als unbestätigte
Bestellabschlüsse. Wenn der Bestellabschluss beendet ist, erhält die Bestellung ihre Bestellnummer und einen ersten
Eintrag im Statusverlauf, und die Bestellmail geht hinaus
([Temporäre Bestellungen und Aktivierung](../kasse/temporaere-bestellungen-und-aktivierung.md)).

## Eine Bestellung weiterbewegen

1. Öffnen Sie die Bestellung aus der Liste „Zu erledigen“ des Dashboards oder aus der Bestellliste.
2. Prüfen Sie die Zahlung: Bei PayPal ist der Status „bezahlt“ der in `paypal.ini` festgelegte
   ([PayPal-Zahlungen](../../unternehmen/shops/zahlungen-mit-paypal.md)).
3. Setzen Sie den nächsten Status. Jede Änderung wird mit Urheber und Zeitpunkt aufgezeichnet und im Verlauf der
   Bestellung angezeigt.
4. Wenn die Bestellung abgeschlossen ist (Delivered, Completed, Cancelled, Partially refunded oder Refunded),
   archivieren Sie sie.

Die vollständige Übersicht der Status und was jeder für den Umsatz bedeutet, steht unter
[Der Lebenszyklus einer Bestellung im Betrieb](../bestellungen/der-lebenszyklus-einer-bestellung-im-betrieb.md).

## Wenn etwas falsch aussieht

| Sie sehen | Wahrscheinliche Ursache | Lesen |
|---|---|---|
| Kunde hat bezahlt, aber keine Bestellung in der Liste | Der Rückruf des Anbieters hat die Website nicht erreicht | [Fehlersuche an der Kasse](../betrieb/fehlersuche-an-der-kasse.md) |
| Keine Versandzeile in Bestellungen | Der Versand-Workflow liegt nicht am Bestätigungs-Trigger | [Das Ereignis einfacher Versand](../kasse/das-ereignis-einfacher-versand.md) |
| Sie können keinen Status wählen | Ihre Richtlinie für `setstatus` erlaubt diesen Übergang nicht | [Statusübergänge und Berechtigungen](../bestellungen/statusuebergaenge-und-berechtigungen.md) |
| Die USt. wird als unbekannt angezeigt | USt-Regeln ohne USt-Handler, oder der Kunde hat kein Land | [Produktkategorien und USt-Regeln](../katalog/produktkategorien-und-ust-regeln.md) |

## Ihre Routinen

Die Routinen stehen im Bereich „Ihren Shop betreiben“; folgen Sie ihnen wie beschrieben:
[Tägliche Shop-Routine](../../kunden/handel/taegliche-shop-routine.md),
[Wöchentliche Checkliste für den Shop](../../kunden/handel/woechentliche-shop-checkliste.md),
[Monatliche Checkliste für den Shop](../../kunden/handel/monatliche-shop-checkliste.md),
[Rücksendungen und Erstattungen](../../kunden/handel/retouren-und-erstattungen.md).

## Verwandte Seiten

- [Pfade nach Rolle](README.md)
- [Bestellungen im Alltag](../../kunden/handel/bestellungen-im-alltag.md)
- [Bestellstatus](../../kunden/handel/bestellstatus.md)
- [Betriebskalender](../betrieb/betriebskalender.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
