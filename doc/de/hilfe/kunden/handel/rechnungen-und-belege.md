---
title: Rechnungen und Belege
audience: customer, admin, businesses
category: customer-success
order: 150
summary: Was ein Exponential-Shop Kunden als Nachweis ihrer Bestellung gibt - den dauerhaften Beleg mit Drucken und Herunterladen -, warum das kein Rechnungssystem ist und wie ein Shop-Betreiber Rechnungen und Anfragen nach Belegen handhabt. Keine Steuerberatung.
keywords: Beleg, Rechnung, dauerhafter Beleg, orderreceipt, Beleg drucken, Beleg herunterladen, Beleg-Link, Rechnungsnummern, Buchhaltung, keine Steuerberatung, receipt, invoice, permanent receipt, print receipt, download receipt, receipt link, invoice numbers, accounting, bookkeeping
type: help
---
# Rechnungen und Belege

Diese Seite ist für Eigentümer und alle, die die Buchhaltung machen. Sie erklärt, was der Shop Ihren Kunden als
Nachweis einer Bestellung gibt, was er nicht tut und wie Sie Rechnungsanfragen in einem laufenden Shop handhaben. Die
Beleg-Funktion ist unter [Dauerhafte Bestellbelege](../../../../en/us/features/6.0/order-receipts.md) beschrieben.

> **Hinweis**
> Keine Steuerberatung. Ob ein Beleg genügt oder was eine Rechnung enthalten muss, hängt vom für Sie geltenden Recht
> ab. Fragen Sie Ihren Steuerberater.

## Was der Shop Ihren Kunden gibt

| Nachweis | Wo | Hält |
|---|---|---|
| Die Bestellseite nach der Kasse | Der Browser | Bis der Besucher die Seite verlässt |
| Die Bestellbestätigung per E-Mail | Der Posteingang des Kunden | So lange er sie aufbewahrt ([Bestellbestätigung per E-Mail](bestellbestaetigung-per-e-mail.md)) |
| Die Bestellansicht, `shop/orderview/<id>` | Die Website | Für Gäste nur, bis ihre Browsersitzung abläuft |
| Der dauerhafte Beleg, `shop/orderreceipt/<token>` | Die Website | Unbegrenzt, auf jedem Gerät, mit oder ohne Konto |

Der dauerhafte Beleg wird mit `settings/shop.ini [OrderViewSettings] OrderLinkView=orderreceipt` eingeschaltet
(ausgelieferter Wert `orderview`). Er bietet **Drucken**, das nur den Beleg druckt, und **Herunterladen**, eine
eigenständige Datei `receipt-<number>.html` ([Dauerhafte Bestellbelege](../../../../en/us/features/6.0/order-receipts.md)).

> **Tipp**
> Wenn Gäste in Ihrem Shop kaufen können, bitten Sie Ihren Partner, den dauerhaften Beleg einzuschalten. Das erspart
> die meisten Anrufe wegen „Ich habe meine Bestellung verloren“.

## Was der Shop nicht tut

Rechnungen in einem Buchhaltungssystem gehören zu den Dingen, die der Shop einer Erweiterung oder einer Integration
überlässt ([Einen Shop betreiben: Überblick](../../../../en/us/help/creators/website-creators/shop-overview.md)). Die Views des
Shop-Moduls (`kernel/shop/module.php`) enthalten weder eine Rechnungsansicht noch eine Rechnungsnummerierung, keine
Gutschriften und keinen Export der Bestellungen in eine Tabellendatei
([Was der eingebaute Shop leistet](../../../../en/us/help/businesses/stores/what-the-built-in-shop-does.md)).

## Rechnungen in der Praxis

| Ihre Situation | Ein gängiger Ansatz |
|---|---|
| Ihre Beratung sagt, der Beleg genügt | Den Beleg-Link eingeschaltet lassen; sonst ist nichts zu tun |
| Sie brauchen Rechnungen für einige Kunden (Unternehmen) | Auf Anfrage in Ihrer Buchhaltungssoftware aus der Bestellansicht erstellen |
| Sie brauchen für jede Bestellung eine Rechnung | Ihren Partner um eine Integration mit Ihrer Buchhaltungssoftware bitten |
| Sie brauchen Monatszahlen für die Buchhaltung | **Shop > Produkt-Statistiken** pro Monat ([Shop-Berichte](shop-berichte.md)) |

Was Sie auch wählen: Halten Sie fest, wer Rechnungen erstellt, aus welchen Daten und wie schnell Sie auf eine Anfrage antworten.

## Anfragen von Kunden nach Belegen

| Anfrage | Antwort |
|---|---|
| „Schicken Sie mir meinen Beleg noch einmal“ | Den Beleg-Link aus der Bestellung kopieren und senden, oder die Bestell-E-Mail aus der Kopie des Shops weiterleiten |
| „Auf meinem Beleg steht die falsche Adresse“ | Vereinbaren, wie Korrekturen erfolgen; der Beleg zeigt die Bestellung so, wie sie aufgegeben wurde |
| „Ich brauche eine Rechnung mit unserer USt-IdNr.“ | Ihrer oben beschriebenen Rechnungsroutine folgen |
| „Wer kann meinen Beleg sehen?“ | Jeder, der den Link hat; die Fußzeile sagt, dass er vertraulich zu behandeln ist ([Dauerhafte Bestellbelege](../../../../en/us/features/6.0/order-receipts.md)) |

## Das Beleg-Geheimnis

Beleg-Adressen werden mit `shop.ini [OrderReceiptSettings] Secret` signiert. Wird das Geheimnis geändert, ändert sich
jede Beleg-Adresse, und Links in alten E-Mails funktionieren nicht mehr ([Bestellbelege und Bestellbestätigung für Unternehmen](../../../../en/us/help/businesses/stores/order-receipts-and-confirmation-mail.md)).

- [ ] Ihr Partner hat es einmal gesetzt, außerhalb der ausgelieferten Einstellungsdatei, und zusammen mit Ihren anderen Geheimnissen festgehalten.
- [ ] Niemand ändert es im Rahmen eines routinemäßigen Updates.

## Verwandte Seiten

- [Bestellbestätigung per E-Mail](bestellbestaetigung-per-e-mail.md)
- [Dauerhafte Bestellbelege](../../../../en/us/features/6.0/order-receipts.md)
- [Bestellbelege und Bestellbestätigung für Unternehmen](../../../../en/us/help/businesses/stores/order-receipts-and-confirmation-mail.md)
- [Shop-Berichte](shop-berichte.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
