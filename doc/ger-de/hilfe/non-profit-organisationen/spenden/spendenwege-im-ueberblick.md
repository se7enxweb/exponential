---
title: Spendenwege im Überblick
audience: non-profit, businesses, project-manager
category: non-profits
order: 20
summary: Ein direkter Vergleich der Wege, auf denen eine Non-Profit-Organisation mit einer Exponential-Website Spenden annehmen kann - eine externe Spendenplattform, Shop-Artikel mit ezpaypal (Weiterleitung zu PayPal) und Shop-Artikel mit ezauthorize (ein Kartenformular auf der eigenen Website an Authorize.Net) - nach Einrichtungsaufwand, freien Beträgen, regelmäßigen Spenden, Kartendaten, Sichtbarkeit, Quittungen und Gebühren, mit einer Entscheidungstabelle.
keywords: Spendenwege, Vergleich, externe Plattform, Weiterleitungs-Gateway, Kartenformular auf der Website, regelmäßige Spenden, freier Betrag, Entscheidung, donation options, comparison, external platform, ezpaypal, ezauthorize, PayPal, Authorize.Net, redirect gateway, on-site card form, PCI, recurring giving, free amount, decision
type: help
---
# Spendenwege im Überblick

Diese Seite vergleicht die drei praktikablen Wege, mit einer Exponential-Website Spenden anzunehmen, damit eine
Geschäftsführung oder ein Vorstand in einer Sitzung entscheiden kann. Exponential hat kein Spendenmodul; jeder Weg
nutzt etwas, das es bereits gibt ([Spenden](README.md)).

## Inhalt

1. [Die drei Wege](#die-drei-wege)
2. [Vergleich](#vergleich)
3. [Kartendaten: Weiterleitung oder Formular auf der Website](#kartendaten-weiterleitung-oder-formular-auf-der-website)
4. [Entscheidungstabelle](#entscheidungstabelle)
5. [Fragen, die Sie vor dem Start klären sollten](#fragen-die-sie-vor-dem-start-klären-sollten)

## Die drei Wege

1. **Externe Plattform.** Ein Link oder Button zu einer Spendenseite bei einem Zahlungsanbieter oder einer
   Fundraising-Plattform.
2. **Shop-Artikel mit `ezpaypal`.** Feste Spendenprodukte im eingebauten Shop; an der Kasse wird der Spender zu PayPal
   geschickt, und PayPal meldet sich bei der Website zurück ([ezpaypal](../../../funktionen/6.0/erweiterungen/ezpaypal.md),
   [Zahlungen mit PayPal](../../unternehmen/shops/zahlungen-mit-paypal.md)).
3. **Shop-Artikel mit `ezauthorize`.** Dieselben Produkte, aber der Spender gibt seine Kartendaten in ein Formular auf
   Ihrer Website ein, das sie an Authorize.Net sendet ([Zahlungs-Gateways](../../kunden/handel/zahlungsanbieter.md)).

`ezpaypal` und `ezauthorize` sind beide in `composer.json` eingetragen; keines ist aktiv, bevor es eingerichtet ist.

## Vergleich

| | Externe Plattform | Shop mit `ezpaypal` | Shop mit `ezauthorize` |
|---|---|---|---|
| Einrichtungsaufwand | Gering: eine Seite und ein Button | Mittel: Produkte, Gateway, Workflow, Tests | Mittel bis hoch: wie `ezpaypal`, dazu Sorgfalt bei der Kartensicherheit |
| Spender wählt einen beliebigen Betrag | Je nach Angebot der Plattform | Nein: feste Artikel oder festgelegte Optionen | Nein: feste Artikel oder festgelegte Optionen |
| Regelmäßige Spenden | Je nach Angebot der Plattform | Nicht dokumentiert | Nicht dokumentiert |
| Kartendaten werden auf Ihrer Website eingegeben | Nein | Nein (bei PayPal) | **Ja** |
| Spenden in der Administrationsoberfläche sichtbar | Nein | Ja, als Bestellungen | Ja, als Bestellungen |
| Quittung | Je nach Angebot der Plattform | Bestellquittung (keine Steuerbescheinigung) | Bestellquittung (keine Steuerbescheinigung) |
| Gebühren | Die der Plattform | Die von PayPal | Die von Authorize.Net und Ihres Händlerkontos |
| Wer muss nach Updates testen | Die Plattform | Sie oder Ihr Partner | Sie oder Ihr Partner |

Quellen: [Spenden- und Fundraising-Seiten](../../unternehmen/gemeinnuetzige/spenden-und-spendenaufrufe.md#die-möglichkeiten-im-vergleich),
[Bestellquittungen](../../../funktionen/6.0/bestellbelege.md),
[Rechnungen und Quittungen](../../kunden/handel/rechnungen-und-belege.md).

## Kartendaten: Weiterleitung oder Formular auf der Website

Bei einer Weiterleitung (externe Plattform oder `ezpaypal`) werden Kartennummern auf den Seiten des Anbieters
eingegeben, nicht auf Ihren. Bei einem Kartenformular auf Ihrer Website (`ezauthorize`) laufen Kartennummern durch Ihre
Website, was die Sicherheitspflichten Ihrer Organisation erhöht. Lesen Sie
[Kartenzahlungen und PCI-Grundlagen](../../unternehmen/shops/kartenzahlungen-und-pci-grundlagen.md) und
[Kartenzahlungen und PCI](../../kunden/handel/kartenzahlungen-und-pci.md), bevor Sie diesen Weg wählen; diese Seiten
vermitteln Grundlagen, keine Compliance-Beratung.

Für eine kleine Non-Profit-Organisation ist eine Weiterleitung meist die sicherere Wahl.

## Entscheidungstabelle

| Wenn Sie ... | Wählen Sie |
|---|---|
| möglichst wenig Arbeit und monatliche Spenden möchten | Externe Plattform |
| den Shop bereits mit PayPal betreiben | Shop mit `ezpaypal` für einmalige Spenden |
| jede Spende in der Administrationsoberfläche sehen müssen | Shop mit `ezpaypal` |
| ein Händlerkonto bei Authorize.Net und einen Partner haben, der die Kartensicherheit pflegt | Shop mit `ezauthorize` |
| niemanden haben, der ein Gateway nach jedem Update testet | Externe Plattform |

## Fragen, die Sie vor dem Start klären sollten

- [ ] Wer eröffnet das Konto beim Anbieter, und auf wessen Namen?
- [ ] Welche Quittungen müssen Spender in Ihrem Land erhalten? (Fragen Sie Ihre Steuerberatung.)
- [ ] Wer gleicht die Spenden mit der Abrechnung des Anbieters ab, und wie oft?
- [ ] Wo wird der Anbieter in Ihrer Datenschutzerklärung genannt?
- [ ] Was geschieht mit einer abgebrochenen Zahlung? (Beim Shop: Die Bestellung bleibt unbezahlt.)
- [ ] Wer testet den Spendenweg nach jedem Update?

Folgen Sie danach [Externe Spendenplattform, Schritt für Schritt](externe-spendenplattform-schritt-fuer-schritt.md) oder
[Spendenartikel im Shop, Schritt für Schritt](spendenartikel-im-shop-schritt-fuer-schritt.md).

## Verwandte Seiten

- [Spenden](README.md)
- [Zahlungs-Gateways für Unternehmen](../../unternehmen/shops/zahlungsanbieter-fuer-unternehmen.md)
- [Zahlungs-Gateways für Website-Ersteller](../../ersteller/website-ersteller/payment-gateways.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
