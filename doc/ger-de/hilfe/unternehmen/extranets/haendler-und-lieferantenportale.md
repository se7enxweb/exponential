---
title: Händler- und Lieferantenportale
audience: businesses, project-manager, partner
category: business-solutions
order: 60
summary: Was Exponential für Handelsportale bietet - Händler, die über den eingebauten Shop mit Ermäßigungsgruppen pro Benutzergruppe bestellen, Produktinformationen und Preislisten nach Stufe, Lieferanten, die Dokumente über Formulare oder Uploads senden, und die ehrliche Liste dessen, was der Shop nicht kann.
keywords: Händlerportal, Lieferantenportal, B2B, Handelsportal, Ermäßigungsgruppen, Gruppenpreise, shop/buy, eingebauter Shop, Bestellungen, Preislisten, Produktinformationen, Uploads von Lieferanten, Information Collector, Lagerbestand, Rechnungen, Integrationen, dealer portal, supplier portal, trade portal, discount groups, group prices, built-in shop, orders, price lists, product information, supplier uploads, information collector, stock, invoices, integrations
type: help
---
# Händler- und Lieferantenportale

Diese Seite ist für Verantwortliche in Vertrieb und Einkauf, die ein Portal für Handelspartner möchten. Händler wollen
ihre Preise sehen und bestellen; Lieferanten wollen wissen, was erwartet wird, und Dokumente zurückschicken. Der
eingebaute Shop von Exponential, seine Berechtigungen und seine Formulare decken einen guten Teil davon ab. Die Seite
sagt auch deutlich, was nicht eingebaut ist, damit Integrationen von Anfang an geplant werden können.

## Inhalt

1. [Händlerportale](#händlerportale)
2. [Bestellen mit dem eingebauten Shop](#bestellen-mit-dem-eingebauten-shop)
3. [Preise pro Händlergruppe](#preise-pro-händlergruppe)
4. [Produktinformationen und Preislisten](#produktinformationen-und-preislisten)
5. [Lieferantenportale](#lieferantenportale)
6. [Was nicht eingebaut ist](#was-nicht-eingebaut-ist)
7. [Checkliste](#checkliste)
8. [Verwandte Seiten](#verwandte-seiten)

## Händlerportale

Ein Händlerportal verbindet drei Dinge, die es in Exponential bereits gibt:

| Bedarf | Funktion | Seite |
|---|---|---|
| Nur Händler dürfen bestellen | `shop/buy` an die Händlerrolle vergeben, nicht an Anonymous | [Einen Shop betreiben: Überblick](../../../../eng-us/help/creators/website-creators/shop-overview.md#permissions) |
| Händler sehen ihre eigenen Preise | Ermäßigungsgruppen pro Benutzergruppe | [Produkte, Preise und USt](../../../../eng-us/help/creators/website-creators/products-prices-and-vat.md#discounts) |
| Händler sehen Material nur für Händler | Sektionen und Stufen | [Kunden- und Partnerbereiche](kunden-und-partnerbereiche.md) |

## Bestellen mit dem eingebauten Shop

Der Kernel enthält einen Shop: Produkte sind Inhalte mit einem Preisfeld, Händler legen sie in einen Warenkorb, gehen
zur Kasse, bestätigen und bezahlen über einen Zahlungsanbieter, und Bestellungen werden im Tab **Shop** der
Administration verwaltet ([Einen Shop betreiben: Überblick](../../../../eng-us/help/creators/website-creators/shop-overview.md)).

| Schritt | Ansicht |
|---|---|
| In den Warenkorb legen | `shop/add` |
| Warenkorb | `shop/basket` |
| Kundendaten | `shop/register` oder `shop/userregister` |
| Bestätigung mit Versand und USt | `shop/confirmorder` |
| Zahlung | ein Workflow am Checkout-Trigger mit einem Zahlungsereignis ([Zahlungsanbieter](../../../../eng-us/help/creators/website-creators/payment-gateways.md)) |
| Bestellliste, Status, Statistiken | `shop/orderlist`, das Shop-Dashboard ([Shop-Dashboard und Bestellstatus](../../../../eng-us/features/6.0/store-dashboard.md)) |

Berechtigungen im Shop: Händler brauchen `shop/buy`; Ihre Bestellabteilung braucht `shop/administrate` und für USt und
Währungen `shop/setup`. Da `shop/buy` eine Richtlinie wie jede andere ist, kann das Bestellen auf die Händlerrolle
beschränkt werden, während die öffentliche Website die Produkte nur zeigt.

Die Einzelheiten eines Shops, von USt-Regeln bis zu Belegen, stehen im Bereich [Shops](../shops/README.md).

## Preise pro Händlergruppe

Ermäßigungsgruppen (`shop/discountgroup`) geben Benutzergruppen einen prozentualen Nachlass auf Produkte oder Klassen,
mit Regeln pro Gruppe (`shop/discountruleedit`)
([Produkte, Preise und USt, Discounts](../../../../eng-us/help/creators/website-creators/products-prices-and-vat.md#discounts)).

| Händlerstufe | Benutzergruppe | Ermäßigungsregel (Beispiel) |
|---|---|---|
| Standardhändler | Dealers | ein Prozentsatz auf die Klasse Product |
| Premiumhändler | Premium dealers | ein höherer Prozentsatz auf die Klasse Product |

Die Prozentsätze in dieser Tabelle sind Platzhalter; Ihre Preispolitik legt sie fest. Preise in mehreren Währungen
verwenden den Datentyp `ezmultiprice` und die Währungsliste (`shop/currencylist`)
([Produkte, Preise und USt](../../../../eng-us/help/creators/website-creators/products-prices-and-vat.md)). Die USt nach Land regeln
USt-Typen und USt-Regeln.

> **Hinweis**
> Ermäßigungsgruppen geben Prozentsätze. Eine vollständig getrennte Preisliste pro Händler (feste Nettopreise pro
> Produkt und Händler) ist keine dokumentierte Funktion des Shops; planen Sie sie als Erweiterung, oder führen Sie
> Preislisten als Dokumente (siehe unten).

## Produktinformationen und Preislisten

Nicht jedes Portal braucht eine Bestellfunktion. Viele Händler brauchen vor allem:

- Produktseiten mit Spezifikationen, Bildern und Downloads, die nur Händler lesen können;
- Preislisten als Dateien pro Stufe ([Dateien mit Partnern teilen](dateien-mit-partnern-teilen.md));
- Produktneuigkeiten, verfolgt mit **Benachrichtigen**
  ([Benachrichtigungen: die Anleitung für Benutzer](../../../../eng-us/features/6.0/notifications.md));
- ein PDF eines Teils des Katalogs ([PDF-Exporte](../../../anleitungen/pdf-exporte.md)).

Produkte und Dokumente lassen sich mit `eztags` über Produktlinien hinweg verschlagworten
([Verschlagworten mit eztags](../../../../eng-us/help/creators/website-creators/tagging-with-eztags.md)).

## Lieferantenportale

Lieferanten lesen meist Anforderungen und schicken Dokumente zurück:

| Bedarf | Funktion | Seite |
|---|---|---|
| Spezifikationen und Qualitätsregeln lesen | Seiten in einer Lieferantensektion | [Kunden- und Partnerbereiche](kunden-und-partnerbereiche.md) |
| Zertifikate in den eigenen Ordner hochladen | `content/create` für die Klasse File, Teilbaum-Einschränkung pro Lieferant | [Dateien mit Partnern teilen](dateien-mit-partnern-teilen.md#partner-hochladen-lassen) |
| Ein Formular mit Anhang senden | Information Collector mit `enhancedezbinaryfile` | [Formulare mit dem Information Collector](../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md) |
| Mitarbeiter erfahren von neuen Uploads | Benachrichtigen für die Lieferantenordner | [Benachrichtigungen für Mitarbeiter](../intranets/benachrichtigungen-fuer-mitarbeiter.md) |
| Uploads werden vor der Verwendung geprüft | ein Freigabe-Workflow für die Lieferantensektion | [Freigabe-Workflows](../intranets/freigabe-workflows.md) |

## Was nicht eingebaut ist

Der Überblick über den Shop listet auf, was der Shop nicht kann: Lagerverwaltung, Rechnungen in einem
Buchhaltungssystem, Versandetiketten und Marktplätze; planen Sie diese als Erweiterungen oder Integrationen
([Einen Shop betreiben, What the shop does not do](../../../../eng-us/help/creators/website-creators/shop-overview.md#what-the-shop-does-not-do)).
Planen Sie für ein Handelsportal bei Bedarf außerdem: die Freigabe von Bestellungen innerhalb der Organisation des
Händlers, Kreditlimits und eine Schnittstelle zu Ihrem ERP. Die REST API und die Remote-Dienste von Exponential sind der
übliche Ausgangspunkt für solche Integrationen
([Headless mit der REST API](../mobil-und-headless/headless-mit-der-rest-api.md),
[Entfernte Dienste und Apps](../../../anleitungen/entfernte-dienste-und-apps.md)).

## Checkliste

- [ ] `shop/buy` nur in der Händlerrolle (und, wenn überhaupt, dort, wo die Öffentlichkeit kaufen darf).
- [ ] Ermäßigungsgruppen und -regeln pro Händlerstufe.
- [ ] Produktinformationen nur für Händler in einer Händlersektion.
- [ ] Preislisten als Dateien pro Stufe, mit Verantwortlichen.
- [ ] Upload-Ordner für Lieferanten mit einer eingeschränkten Rolle.
- [ ] Integrationen für Lagerbestand, Rechnungen und ERP mit einem Partner abgegrenzt.
- [ ] Eine vollständige Testbestellung als Händlerbenutzer.

## Verwandte Seiten

- [Shops](../shops/README.md)
- [Wie Unternehmen online verkaufen](../wie-unternehmen-online-verkaufen.md)
- [Verkaufen über Netzwerke und Partner](../mobil-und-headless/verkaufen-ueber-netzwerke-und-partner.md)
- [Warenkorb, Bestellablauf und Bestellungen](../../../../eng-us/help/creators/website-creators/basket-checkout-and-orders.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
