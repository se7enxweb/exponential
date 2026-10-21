---
title: Shop-Berichte
audience: customer, businesses, project-manager
category: customer-success
order: 190
summary: Aus dem Shop-Dashboard und den Produkt-Statistiken eines Exponential-Shops eine Berichtsroutine machen - was täglich, wöchentlich und monatlich zu lesen ist, ein einseitiger Monatsbericht für den Eigentümer und was die Berichte nicht sagen können.
keywords: Shop-Berichte, Shop-Dashboard, Produkt-Statistiken, Verkaufszahlen, Monatsbericht, Umsatz, offene Bestellungen, abgebrochene Warenkörbe, Bestseller, Kennzahlen, Berichtsroutine, store reports, store dashboard, product statistics, sales figures, monthly report, revenue, abandoned baskets, best sellers, KPIs
type: help
---
# Shop-Berichte

Diese Seite ist für den Eigentümer und die Shop-Leitung, die über den Shop berichten. Der Shop hat zwei Orte mit
Zahlen: das Shop-Dashboard und die Produkt-Statistiken. Diese Seite macht daraus eine Routine und einen einfachen
Monatsbericht. Was jede Zahl bedeutet, ist unter [Shop-Dashboard und Berichte für Unternehmen](../../unternehmen/shops/shop-uebersicht-und-berichte.md) erklärt.

## Die zwei Quellen

| Quelle | Adresse | Zeigt | Benötigt |
|---|---|---|---|
| Shop-Dashboard | `shop/dashboard` | Bestellungen heute, in 7 und 30 Tagen mit Trend; Umsatz und durchschnittliche Bestellung pro Währung; neue, wiederkehrende und alle bisherigen Kunden; offene Bestellungen; laufende und abgebrochene Warenkörbe; Produkte ohne Preis; Umsatz pro Tag; Aufschlüsselung nach Status; Bestseller | `shop/administrate` |
| Produkt-Statistiken | `shop/statistics` | Verkäufe pro Produkt für ein Jahr und einen Monat Ihrer Wahl | `shop/administrate` |

Quellen: [Shop-Dashboard und Bestellstatus](../../../../eng-us/features/6.0/store-dashboard.md), `kernel/shop/module.php`,
`kernel/private/classes/views/shop/orderstatistics.php`. Die Summen verwenden die Rechenweise der Bestellliste, und
fehlgeschlagene, stornierte und erstattete Bestellungen zählen als Bestellungen, aber nicht als Umsatz.

## Eine Berichtsroutine

| Wann | Wer | Liest | Achtet auf |
|---|---|---|---|
| Täglich | Shop-Leitung | Auf einen Blick, Work to do | Offene Bestellungen, die zu lange warten; einen plötzlichen Rückgang der Bestellungen ([Tägliche Shop-Routine](taegliche-shop-routine.md)) |
| Wöchentlich | Eigentümer | Zahlen der letzten 7 Tage, abgebrochene Warenkörbe, Bestseller, Nächste Schritte | Trends; Auswirkungen von Änderungen und Kampagnen |
| Monatlich | Eigentümer, Buchhaltung | Produkt-Statistiken des Monats; Zahlen der letzten 30 Tage | Monatszahlen für die Buchhaltung; saisonaler Vergleich |
| Jährlich | Eigentümer | Produkt-Statistiken pro Monat über das Jahr | Beste und schlechteste Monate; Sortimente, die wachsen oder wegfallen sollen |

## Ein einseitiger Monatsbericht

Einmal im Monat kopieren und ausfüllen. Übernehmen Sie die Zahlen aus dem Dashboard und den Produkt-Statistiken;
notieren Sie, woher jede Zahl stammt, damit der Bericht des nächsten Monats vergleichbar ist.

```text
STORE MONTHLY REPORT - [store name] - [month, year]       Prepared by: ________

Orders in the month: ______      Previous month: ______
Revenue (per currency): ______   Previous month: ______
Average order: ______
New customers: ______            Returning customers: ______
Open orders at month end: ______ Of which waiting too long: ______
Abandoned baskets (dashboard): ______
Refunded or canceled orders: ______

Best sellers (product statistics):
1. ______________  2. ______________  3. ______________

What changed this month (prices, shipping, campaigns, site changes):
__________________________________________________________________

Next steps shown by the dashboard, and what we did:
__________________________________________________________________

Decisions for next month:
__________________________________________________________________
```

## Die Zahlen mit Bedacht lesen

- **Umsatz ist Bestellwert, kein gebuchter Ertrag.** Er ist das, was Kunden bestellt haben, nicht das, was nach
  Gebühren und Erstattungen auf Ihrem Bankkonto angekommen ist; Ihre Buchhaltung bleibt der Maßstab
  ([Shop-Dashboard und Berichte für Unternehmen](../../unternehmen/shops/shop-uebersicht-und-berichte.md)).
- **Status bestimmen die Zahlen.** Offene Bestellungen und Wartezeiten sind nur so genau wie die Status, die Ihr Team
  setzt ([Bestellstatus](bestellstatus.md)).
- **Testbestellungen zählen mit.** Stornieren oder entfernen Sie Testbestellungen, sonst erscheinen sie in den Zahlen.
- **Vergleichen Sie Gleiches mit Gleichem.** Ein Monat mit einer Kampagne oder einem Feiertag ist kein normaler Monat;
  vermerken Sie das im Bericht.

## Was die Berichte nicht abdecken

- Kein Export in eine Tabellenkalkulation: Die Shop-Ansichten haben keinen CSV-Export der Bestellungen. Übertragen Sie
  Zahlen von Hand, oder bitten Sie Ihren Partner um einen Export ([Shop-Dashboard und Berichte für Unternehmen](../../unternehmen/shops/shop-uebersicht-und-berichte.md)).
- Keine Besucherauswertung: Für Besucherzahlen und Konversion siehe
  [Besucherstatistik und Analyse](../../../../eng-us/help/creators/website-creators/statistics-and-analytics.md).
- Keine Lagerzahlen: Der Shop hat keine Lagerverwaltung.

## Verwandte Seiten

- [Shop-Dashboard und Berichte für Unternehmen](../../unternehmen/shops/shop-uebersicht-und-berichte.md)
- [Shop-Dashboard und Bestellstatus](../../../../eng-us/features/6.0/store-dashboard.md)
- [Monatliche Shop-Checkliste](monatliche-shop-checkliste.md)
- [Den ROI messen](../nutzen/den-roi-messen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
