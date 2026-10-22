---
title: Mehrwertsteuer, Versand und Abholung
audience: local-business, businesses, customer
category: local-business
order: 44
summary: Mehrwertsteuer und Zustellung für den Shop eines lokalen Unternehmens mit Exponential - USt-Typen und Regeln, Preise mit Steuer angezeigt, der Workflow für eine einfache Versandpauschale, Abholung im Geschäft mit dem Status Ready for pickup, Lieferung vor Ort durch eigene Mitarbeitende und was einen Versand-Handler braucht.
keywords: Mehrwertsteuer, Umsatzsteuer, USt-Typen, Mehrwertsteuerregeln, Versand, Versandpauschale, ezsimpleshipping, Abholung, Click and Collect, Lieferung vor Ort, Ready for pickup, Versand-Handler, lokales Unternehmen, VAT, sales tax, VAT types, VAT rules, shipping, flat shipping, pickup, local delivery, shipping handler, local business
type: help
---
# Mehrwertsteuer, Versand und Abholung

Diese Seite ist für Inhaber des Shops eines lokalen Unternehmens, die entscheiden, wie Steuern und Zustellung
funktionieren. Sie fasst die Möglichkeiten zusammen und verweist auf die ausführlichen Seiten. Steuerregeln legt Ihr
Land fest; stimmen Sie sie mit Ihrem Steuerberater ab. Dies ist keine Steuerberatung.

## Inhalt

1. [Mehrwertsteuer in einfachen Worten](#mehrwertsteuer-in-einfachen-worten)
2. [Drei Wege, Waren zu Kunden zu bringen](#drei-wege-waren-zu-kunden-zu-bringen)
3. [Abholung im Geschäft](#abholung-im-geschäft)
4. [Lieferung vor Ort durch eigene Mitarbeitende](#lieferung-vor-ort-durch-eigene-mitarbeitende)
5. [Versand als Paket](#versand-als-paket)
6. [Checkliste](#checkliste)

## Mehrwertsteuer in einfachen Worten

Der Shop hat **USt-Typen** (ein Name und ein Prozentsatz, zum Beispiel ein Regel- und ein ermäßigter Satz) und, für
den Verkauf in mehrere Länder, **Mehrwertsteuerregeln** nach Land des Kunden und Produktkategorie
([Mehrwertsteuerregeln und USt-Typen](../../unternehmen/shops/ust-regeln-und-ust-typen.md)). Das Preisfeld jedes
Produkts nennt seinen USt-Typ und ob der Preis mit oder ohne Mehrwertsteuer eingegeben wird.

Für ein lokales Unternehmen, das im eigenen Land verkauft, reichen meist ein oder zwei USt-Typen: zum Beispiel
Lebensmittel zum ermäßigten Satz und andere Waren zum Regelsatz. Ihr Steuerberater sagt Ihnen, welcher Satz für welches
Produkt gilt.

Regelmäßige Prüfungen: [Checkliste zur Mehrwertsteuer](../../kunden/handel/mehrwertsteuer-checkliste.md) und
[USt-Typen und Regeln für Shop-Betreiber](../../kunden/handel/ust-typen-und-ust-regeln.md).

## Drei Wege, Waren zu Kunden zu bringen

| Weg | Mit den mitgelieferten Werkzeugen | Zusätzliche Arbeit |
|---|---|---|
| Abholung im Geschäft | ja: keine Versandzeile, Status **Ready for pickup** (abholbereit) | Kunden sagen, wo und wann |
| Lieferung vor Ort durch Ihre Mitarbeitenden | ja: eine Pauschale mit dem Workflow für einfachen Versand | eine Regel für das Liefergebiet ist nicht eingebaut |
| Paket über einen Versanddienstleister | ja: eine Pauschale | Tarife nach Gewicht oder Zone brauchen einen Versand-Handler |

Quelle: [Versand und Abwicklung](../../unternehmen/shops/versand-und-bearbeitung.md).

## Abholung im Geschäft

Die mitgelieferten Bestellstatus enthalten **Ready for pickup** (Status 11), und das Shop-Dashboard hebt Bestellungen
hervor, die auf den Kunden warten ([Shop-Dashboard und Bestellstatus](../../../funktionen/6.0/shop-dashboard.md)). Eine
Routine für die Abholung:

1. Die Bestellung geht ein; Mitarbeitende bereiten sie vor.
2. Mitarbeitende setzen den Status auf Ready for pickup und sagen dem Kunden Bescheid (eine kurze E-Mail oder ein
   Anruf).
3. Bei der Abholung setzen Mitarbeitende die Bestellung auf ausgeliefert oder abgeschlossen (so, wie Ihr Statusablauf
   es festlegt, [Bestellstatus](../../kunden/handel/bestellstatus.md)).
4. Nach den vereinbarten Tagen nicht abgeholt: anrufen, dann stornieren und erstatten, wie Ihre Bedingungen es sagen.

Stellen Sie Abholadresse, Zeiten und was mitzubringen ist (Bestellnummer, Ausweis bei Waren mit Altersbeschränkung)
auf die Kassenseite und in die Bestätigungs-E-Mail ([Bestätigungs-E-Mail zur Bestellung](../../kunden/handel/bestellbestaetigung-per-e-mail.md)).

## Lieferung vor Ort durch eigene Mitarbeitende

Der Workflow für einfachen Versand (`ezsimpleshipping`) fügt jeder Bestellung einen festen Betrag hinzu
(`workflow.ini [SimpleShippingWorkflow]`, `ShippingCost`, `ShippingDescription`). Für eine Lieferung im Ort setzen Sie
ihn auf Ihre Liefergebühr. Was er nicht kann:

- Adressen außerhalb Ihres Gebiets ablehnen: Nennen Sie das Gebiet vor der Kasse deutlich, und prüfen Sie die Adresse
  bei jeder Bestellung;
- im selben Shop eine Wahl zwischen Abholung (kostenlos) und Lieferung (gebührenpflichtig) anbieten: Das braucht einen
  Versand-Handler oder Template-Arbeit durch einen Entwickler;
- ein Lieferzeitfenster buchen: nicht eingebaut; vereinbaren Sie die Zeit telefonisch oder per E-Mail.

## Versand als Paket

Eine Pauschale deckt die meisten kleinen Pakete ab. Kostenloser Versand ab einem Bestellwert, Tarife nach Zone oder
Gewicht oder die Tarife der Versanddienstleister brauchen einen **Versand-Handler**, den ein Entwickler schreibt; der
Kernel liefert keinen mit ([Versand und Abwicklung](../../unternehmen/shops/versand-und-bearbeitung.md#ein-eigener-versand-handler)).
Etiketten und Sendungsnummern werden im eigenen Portal des Versanddienstleisters erstellt ([Versand und Abwicklung für Shop-Betreiber](../../kunden/handel/versand-und-abwicklung.md)).

## Checkliste

- [ ] USt-Typen eingerichtet und von Ihrem Steuerberater bestätigt.
- [ ] Jedes Produkt hat den richtigen USt-Typ.
- [ ] Zustellweg gewählt: Abholung, Lieferung vor Ort, Paket oder eine Kombination, die der Ersteller umsetzen kann.
- [ ] Pauschale und ihre Beschreibung festgelegt (falls verwendet).
- [ ] Abholadresse, Zeiten und Regeln auf der Kassenseite und in der Bestätigungs-E-Mail.
- [ ] Eine Testbestellung für jeden Weg zeigt die richtigen Summen.

## Verwandte Seiten

- [Ländergruppen und Regionen](../../unternehmen/shops/laendergruppen-und-regionen.md)
- [Ablauf von Warenkorb und Kasse](../../unternehmen/shops/warenkorb-und-bestellablauf.md)
- [Checkliste zum Livegang eines Shops](../../unternehmen/shops/checkliste-fuer-den-shop-start.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
