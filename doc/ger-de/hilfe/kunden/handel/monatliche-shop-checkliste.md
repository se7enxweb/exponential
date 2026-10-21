---
title: Monatliche Shop-Checkliste
audience: customer, admin, businesses
category: customer-success
order: 250
summary: Die Checkliste zum Monatsende für den Betreiber eines Exponential-Shops - Zahlen für die Buchhaltung, abgeglichene Zahlungen und Erstattungen, Versandkosten, Kundendaten, Sicherheit und Updates mit dem Hoster und der Plan für den nächsten Monat.
keywords: monatliche Checkliste, Monatsende, Shop-Checkliste, Buchhaltung, Abgleich, Produkt-Statistiken, Updates, Backups, Cronjobs, Shop-Betreiber, monthly checklist, month end, store checklist, bookkeeping, reconciliation, product statistics, cron jobs, store owner
type: help
---
# Monatliche Shop-Checkliste

Diese Seite ist die Checkliste für die ersten Arbeitstage jedes Monats. Das meiste erledigt der Eigentümer, zusammen
mit der Shop-Leitung und dem Hoster. Sie schließt den Monat für die Buchhaltung ab und fängt schleichende Probleme
ab, die ein täglicher Blick nicht zeigt. Eine allgemeine monatliche Checkliste für jede Unternehmenswebsite finden Sie
unter [Monatliche Checkliste für den Betrieb](../../unternehmen/vorlagen/monatliche-betriebscheckliste.md); diese hier
ist für den Shop.

## Die Checkliste

```text
STORE MONTHLY CHECKLIST - month: __________     Owner: __________

FIGURES
[ ] Product statistics for the month saved (Store > Product statistics, shop/statistics)
[ ] Store monthly report filled in (see Store reports)
[ ] Figures handed to the bookkeeper on: __________

RECONCILIATION
[ ] Payment provider statement matches the month's paid orders
[ ] Every refund in the provider's account is on an order as Refunded / Partially refunded
[ ] Orders still Pending or Awaiting payment from last month: followed up or canceled
[ ] Shipping charged compared with the carrier's invoices

CATALOG AND PRICES
[ ] Products without a price: none
[ ] Seasonal products hidden or shown for the coming month
[ ] With several currencies: auto rates and autoprices updated; a few prices compared
[ ] Unused images and files reviewed (content checks), if the catalog changed a lot

PROMOTIONS
[ ] Every discount that should have ended has ended
[ ] Promotion log complete for the month

CUSTOMERS AND DATA
[ ] Privacy requests of the month closed in the request log
[ ] Export files deleted
[ ] Staff who left: accounts disabled

WITH THE HOST OR PARTNER
[ ] Backups ran, and the last restore test date is known
[ ] Cron jobs run (including basket cleanup and the audit)
[ ] Exponential and extension updates: any due? Planned for: __________
[ ] Mail delivery: order mails arriving in inboxes
[ ] Anything unusual in the logs or the dashboard's Next steps

NEXT MONTH
[ ] Sales, holidays and campaigns: dates and preparation (see Peak season preparation)
[ ] Price, VAT or shipping changes coming: dates and owners
```

## Wo jeder Punkt erledigt wird

| Punkt | Wo | Lesen Sie |
|---|---|---|
| Monatszahlen | **Shop > Produkt-Statistiken** | [Shop-Berichte](shop-berichte.md) |
| Abgleich | Das Konto beim Anbieter, die nach Status sortierte Bestellliste | [Zahlungsanbieter](zahlungsanbieter.md), [Retouren und Erstattungen](retouren-und-erstattungen.md) |
| Versand | Die Rechnungen des Versanddienstleisters | [Versand und Abwicklung](versand-und-abwicklung.md) |
| Währungen | **Shop > Währungen** | [Preise und Währungen](preise-und-waehrungen.md) |
| Ungenutzte Dateien | Inhaltsprüfungen | [Die Inhaltsprüfungen](../../../../eng-us/features/6.0/content-checks.md) |
| Datenschutzanfragen | Ihr Anfragenprotokoll | [DSGVO-Anfragen](dsgvo-anfragen.md), [Vorlage für ein DSGVO-Anfragenprotokoll](vorlage-dsgvo-anfragenprotokoll.md) |
| Konten | **Benutzer > Benutzerkonten prüfen** | [Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md) |
| Backups, Cronjobs, Updates | Der Hoster | [Cronjobs](../../../anleitungen/cronjobs.md), [Upgrade](../../../anleitungen/aktualisieren.md) |

## Fragen an den Hoster, jeden Monat

- Sind alle Backups gelaufen, und wann wurde zuletzt eine Wiederherstellung getestet
  ([Wiederherstellungsübungen](../../unternehmen/zuverlaessigkeit/wiederherstellungsuebungen.md))?
- Laufen die Cronjobs planmäßig? **Setup > Cronjobs** zeigt die Teile und ob sie eingeplant sind
  ([Cronjobs](../../../anleitungen/cronjobs.md)).
- Ist ein neues Release von Exponential 6.0.x mit Sicherheitskorrekturen erschienen? Korrekturen werden mit dem
  nächsten Release 6.0.x veröffentlicht (`SECURITY.md`).
- Gibt es etwas in den Logs, das wir wissen sollten ([Log-Viewer](../../../../eng-us/features/6.0/log-viewer.md))?

## Verwandte Seiten

- [Wöchentliche Shop-Checkliste](woechentliche-shop-checkliste.md)
- [Shop-Berichte](shop-berichte.md)
- [Mehrwertsteuer-Checkliste](mehrwertsteuer-checkliste.md)
- [Monatliche Checkliste für die ganze Website](../nutzen/checklisten/monatlich.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
