---
title: Spenden
audience: non-profit, businesses, customer
category: non-profits
order: 10
summary: Was eine Non-Profit-Organisation mit Exponential für Online-Spenden tun kann - es gibt kein eigenes Spendenmodul, daher sind die praktikablen Wege ein Link zu einer externen Spendenplattform oder feste Spendenartikel im eingebauten Shop mit dem Gateway ezpaypal oder ezauthorize - und die Seiten dieses Ordners, die jeden Weg, die Spendenseite und das Vertrauen der Spender erklären.
keywords: Spenden, spenden, Fundraising, Spendenseite, kein Spendenmodul, externe Spendenplattform, Shop, regelmäßige Spenden, Quittungen, Spendenbescheinigung, Vertrauen der Spender, donations, donate, fundraising, giving page, no donation module, external donation platform, shop, ezpaypal, ezauthorize, PayPal, Authorize.Net, recurring giving, receipts, donor trust
type: help
---
# Spenden

Dieser Ordner richtet sich an Fundraiser, Geschäftsführungen und die Person, die die Website baut. Er beginnt mit der
wichtigsten Tatsache und zeigt dann die beiden Wege, die tatsächlich funktionieren.

> **Wichtig**
> Exponential hat **kein eigenes Spendenmodul**. Es gibt keinen Spenden-Datentyp, kein Feld „Betrag nach Wahl“, keine
> regelmäßigen Spenden, keine Funktion für Gift Aid oder Steuerbescheinigungen. Das Repository wurde nach
> Spendenfunktionen durchsucht, und es fanden sich nur Seiten über Spenden an das Exponential-Projekt selbst
> ([Spenden- und Fundraising-Seiten](../../unternehmen/gemeinnuetzige/spenden-und-spendenaufrufe.md#was-es-gibt)).

## Inhalt

1. [Die beiden Wege](#die-beiden-wege)
2. [Die Seiten dieses Ordners](#die-seiten-dieses-ordners)
3. [Welcher Weg für welche Organisation](#welcher-weg-für-welche-organisation)
4. [Was jeder Weg braucht](#was-jeder-weg-braucht)

## Die beiden Wege

| Weg | Was er ist | Gebaut aus |
|---|---|---|
| **A. Externe Spendenplattform** | Ein klarer Spenden-Button, der zu einer Spendenseite bei einem Zahlungsanbieter oder einer Fundraising-Plattform führt | Einer Seite, einem Link oder einem Button-Block in Layouts |
| **B. Spendenartikel im Shop** | Feste Beträge („Spende 25“), die als Produkte über Warenkorb und Kasse verkauft werden | Der mitgelieferten Klasse `product`, dem Shop und dem Gateway `ezpaypal` oder `ezauthorize` aus `composer.json` |

Beide können nebeneinander laufen. Keiner verursacht Lizenzgebühren; Gebühren fallen beim Zahlungsanbieter an.

## Die Seiten dieses Ordners

| Seite | Was sie beantwortet |
|---|---|
| [Spendenwege im Überblick](spendenwege-im-ueberblick.md) | Ein direkter Vergleich und eine Entscheidungstabelle |
| [Externe Spendenplattform, Schritt für Schritt](externe-spendenplattform-schritt-fuer-schritt.md) | Weg A an einem Nachmittag aufbauen |
| [Spendenartikel im Shop, Schritt für Schritt](spendenartikel-im-shop-schritt-fuer-schritt.md) | Weg B aufbauen, einschließlich der Gateway-Tests |
| [Die Spendenseite](die-spendenseite.md) | Was eine gute Spendenseite sagt und zeigt |
| [Vertrauen der Spender und Dank](vertrauen-der-spender-und-dank.md) | HTTPS, Datenschutzerklärung, die Dankesseite, Quittungen, Nachbereitung |

Die Kurzfassung von alledem ist [Spenden- und Fundraising-Seiten](../../unternehmen/gemeinnuetzige/spenden-und-spendenaufrufe.md)
im Bereich für Unternehmen.

## Welcher Weg für welche Organisation

| Ihre Situation | Weg |
|---|---|
| Kleine Organisation ohne Shop, möchte monatliche Spenden | A |
| Verkauft im Shop bereits T-Shirts, Tickets oder Publikationen | B für einmalige Spenden, A für monatliche Spenden |
| Möchte jede Spende in der Administrationsoberfläche der Website sehen | B |
| Niemand kann ein Zahlungs-Gateway testen und pflegen | A |
| Spender sollen einen beliebigen Betrag wählen können | A (der Shop bietet nur feste Preise und festgelegte Optionen) |

## Was jeder Weg braucht

- [ ] Eine Spendenseite mit klarem Zweck ([Die Spendenseite](die-spendenseite.md)).
- [ ] Die ganze Website unter HTTPS ([Live gehen: Hosts und HTTPS](../../ersteller/website-ersteller/livegang-hosts-und-https.md)).
- [ ] Der Zahlungsanbieter in der Datenschutzerklärung genannt.
- [ ] Eine Dankesseite und ein Plan für Quittungen ([Vertrauen der Spender und Dank](vertrauen-der-spender-und-dank.md)).
- [ ] Eine benannte Person, die die Spenden wöchentlich prüft.
- [ ] Ihre Steuerberatung zu Quittungen und Steuern befragt.

## Verwandte Seiten

- [Non-Profit-Organisationen: hier beginnen](../non-profit-organisationen-hier-beginnen.md)
- [Kampagnen und Spendenaufrufe](../kommunikation/kampagnen-und-spendenaufrufe.md)
- [Shops](../../unternehmen/shops/README.md)
- [Handel für Shop-Betreiber](../../kunden/handel/README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
