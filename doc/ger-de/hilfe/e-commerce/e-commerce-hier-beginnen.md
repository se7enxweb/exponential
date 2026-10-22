---
title: "E-Commerce: hier beginnen"
audience: ecommerce, customer, businesses
category: e-commerce
order: 1
summary: Der Knotenpunkt für alle, die mit Exponential online verkaufen - Händler, Shop-Manager, E-Commerce-Entwickler und Agenturen - mit einem Pfad pro Rolle, den tieferen Themen, die die Shop-Seiten auslassen (Katalogmodellierung, Shop-Einstellungen, Workflows der Kasse, Bestellbetrieb, den Shop erweitern, Tests, Multisite, Berichte), und Links in die Bereiche „Onlineshops“ und „Ihren Shop betreiben“.
keywords: E-Commerce, Onlineshop, Webshop, Shop, Händler, Shop-Manager, E-Commerce-Entwickler, Agentur, Katalog, Kasse, Workflow, Zahlungs-Gateway, Versand-Handler, USt-Handler, Bestellungen, Berichte, Multisite, hier beginnen, e-commerce, ecommerce, online store, merchant, store manager, checkout, payment gateway, shipping handler, VAT handler, orders, reporting, start here
featured: true
type: help
---
# E-Commerce: hier beginnen

Dieser Bereich ist für Menschen, die mit Exponential online verkaufen: Händler, denen ein Shop gehört, Shop-Manager,
die ihn täglich betreiben, Entwickler, die ihn bauen und erweitern, und Agenturen, die Shops für Kunden liefern. Er ist
der E-Commerce-Knotenpunkt der Hilfe: Er schickt Sie für jede Frage zur passenden vorhandenen Seite und ergänzt die
Seiten, die sonst niemand abdeckt, etwa wie die Kasse intern abläuft, wie Sie jede Shop-Einstellung lesen, wie Sie den
Shop mit eigenen Handlern erweitern und wie Sie einen Shop über ein Jahr hinweg testen, auswerten und betreiben.

> **Hinweis**
> Jede Aussage in diesem Bereich wurde am Exponential-Repository geprüft: am Shop-Modul (`kernel/shop/module.php`),
> an den Shop-Klassen in `kernel/classes/` und `kernel/shop/classes/`, an den ausgelieferten Einstellungen
> (`settings/shop.ini`, `settings/site.ini`, `settings/shopaccount.ini`, `settings/workflow.ini`,
> `settings/paymentgateways.ini`) und an den Zahlungserweiterungen `ezpaypal` und `ezauthorize`. Wo etwas nicht im
> Code steht, sagt die Seite das, statt es zu versprechen.

## Inhalt

1. [Die zwei Bereiche, auf denen dieser Knotenpunkt aufbaut](#die-zwei-bereiche-auf-denen-dieser-knotenpunkt-aufbaut)
2. [Wählen Sie Ihren Pfad](#wählen-sie-ihren-pfad)
3. [Was dieser Bereich ergänzt](#was-dieser-bereich-ergänzt)
4. [Der Shop in einem Diagramm](#der-shop-in-einem-diagramm)
5. [Alle Teile dieses Bereichs](#alle-teile-dieses-bereichs)
6. [Schnelle Antworten](#schnelle-antworten)
7. [Wo Sie sonst nachsehen können](#wo-sie-sonst-nachsehen-können)

## Die zwei Bereiche, auf denen dieser Knotenpunkt aufbaut

Zwei Bereiche der Hilfe behandeln den Shop bereits aus geschäftlicher Sicht. Lesen Sie sie zuerst; dieser Bereich
verweist auf sie und wiederholt sie nicht.

| Bereich | Für | Inhalt |
|---|---|---|
| [Onlineshops und kleine Shops](../unternehmen/shops/README.md) | Inhaber und Entscheider vor und beim Start | Was der eingebaute Shop kann, Planung, Produkte, Preise, USt., Versand, Kasse, Zahlungen, die Checkliste für den Start |
| [Ihren Shop betreiben](../kunden/handel/README.md) | Inhaber und Mitarbeitende nach dem Start | Tägliche, wöchentliche und monatliche Routinen, Bestellungen, Rücksendungen, USt.-Prüfungen, DSGVO-Anfragen, Shop-Berichte |

Die technischen Einrichtungsseiten für Website-Ersteller sind [Einen Shop betreiben: Überblick](../ersteller/website-ersteller/shop-ueberblick.md),
[Produkte, Preise und USt.](../ersteller/website-ersteller/produkte-preise-und-mehrwertsteuer.md),
[Warenkorb, Kasse und Bestellungen](../ersteller/website-ersteller/warenkorb-kasse-und-bestellungen.md) und
[Zahlungs-Gateways](../ersteller/website-ersteller/payment-gateways.md).

## Wählen Sie Ihren Pfad

| Sie sind | Ihr Pfad | Typische erste Frage |
|---|---|---|
| Händler, dem der Shop gehört | [Pfad für Händler](rollen/pfad-fuer-haendler.md) | „Was kostet mich der Betrieb, und was muss ich entscheiden?“ |
| Shop-Manager, der Bestellungen abwickelt | [Pfad für Shop-Manager](rollen/pfad-fuer-shop-manager.md) | „Welche Bildschirme nutze ich täglich, und wer darf welchen Status ändern?“ |
| Entwickler, der den Shop baut oder erweitert | [Pfad für Entwickler](rollen/pfad-fuer-entwickler.md) | „Wo hänge ich Versand, USt. oder einen Zahlungsanbieter ein?“ |
| Agentur oder Partner, die Shops liefert | [Pfad für Agenturen](rollen/pfad-fuer-agenturen.md) | „Wie plane, baue, teste und übergebe ich einen Shop?“ |

Alle vier Pfade werden in [Pfade nach Rolle](rollen/README.md) nebeneinander erklärt. Den ganzen Bereich in
Lesereihenfolge bietet der [Lesepfad für E-Commerce](lesepfad-fuer-e-commerce.md).

## Was dieser Bereich ergänzt

Die Shop-Bereiche beantworten „was“ und „wie oft“. Dieser Bereich beantwortet „wie genau“ und „was, wenn“:

- **Katalogmodellierung im Detail**: die Wahl zwischen `ezprice` und `ezmultiprice`, Optionen, Produktkategorien für
  die USt. und die Ordnerstruktur eines Katalogs ([Katalogmodellierung](katalog/README.md)).
- **Die Shop-Einstellungen durchgegangen**: jede Einstellung rund um den Shop, Datei für Datei, mit dem
  ausgelieferten Wert und wann Sie ihn ändern ([Die Shop-Einstellungen durchgegangen](einstellungen/README.md)).
- **Die Kasse von innen**: die vier Shop-Operationen, die temporäre Bestellung, die Workflow-Trigger und die
  Ereignisse Zahlungs-Gateway, einfacher Versand und Multiplexer ([Kasse und Workflows](kasse/README.md)).
- **Bestellbetrieb**: der Lebenszyklus einer Bestellung in der Datenbank, Statusübergänge, begrenzt durch
  `FromStatus` und `ToStatus`, Archivieren oder Entfernen und der Mail-Handler für Bestellungen
  ([Bestellbetrieb](bestellungen/README.md)).
- **Den Shop erweitern**: Handler für Versand, USt., Warenkorbinformationen, Shop-Konto, Bestellbestätigung und
  Wechselkurse, eigene Zahlungs-Gateways und eigene Preis-Datentypen ([Den Shop erweitern](erweitern/README.md)).
- **Einen Shop betreiben**: Tests, Barrierefreiheit, Multisite, Landingpages mit Layouts, Analyse, Berichte und
  Export, Produktimporte, Headless Commerce, der Prüfpfad für den Handel, ein Jahreskalender,
  Entscheidungs-Checklisten und Fehlersuche ([Einen Shop betreiben](betrieb/README.md)).

## Der Shop in einem Diagramm

```text
Product page --(shop/add, trigger shop_addtobasket)--> Basket (shop/basket, trigger shop_updatebasket)
   --> Customer data (shop/userregister or shop/register, chosen by shopaccount.ini)
   --> Confirmation (shop/confirmorder, trigger before_shop_confirmorder: shipping)
   --> Checkout (shop/checkout, trigger shop_checkout: payment gateway)
   --> Order activated: order number, status history, order mail, receipt link
   --> Store tab: dashboard, order list, statuses, archive, statistics
```

Jeder Schritt wird in [Die Kassenoperationen Schritt für Schritt](kasse/die-kassenoperationen-schritt-fuer-schritt.md) erklärt.

## Alle Teile dieses Bereichs

| Teil | Seiten |
|---|---|
| [Pfade nach Rolle](rollen/README.md) | Händler, Shop-Manager, Entwickler, Agentur |
| [Katalogmodellierung](katalog/README.md) | Modellierung im Detail, Preisfelder im Vergleich, Optionen und Varianten, Produktkategorien und USt-Regeln |
| [Die Shop-Einstellungen durchgegangen](einstellungen/README.md) | `shop.ini`, `site.ini [ShopSettings]`, `shopaccount.ini`, `workflow.ini`, `paymentgateways.ini` |
| [Kasse und Workflows](kasse/README.md) | die Operationen, temporäre Bestellungen, Trigger, Zahlungs-Gateway, einfacher Versand, Multiplexer |
| [Bestellbetrieb](bestellungen/README.md) | Lebenszyklus, Statusberechtigungen, Archivieren und Aufräumen, Bestellmails und Belege |
| [Den Shop erweitern](erweitern/README.md) | Versand, USt., Zahlungs-Gateways, Shop-Konten, Warenkorbinformationen und Bestellbestätigung, Wechselkurse, Preis-Datentypen |
| [Einen Shop betreiben](betrieb/README.md) | Tests, Barrierefreiheit, Multisite, Layouts, Analyse, Berichte, Importe, Headless, Prüfpfad, Kalender, Checklisten, Fehlersuche |
| Nachschlagen | [FAQ](faq-zu-e-commerce.md), [Glossar](glossar-zu-e-commerce.md), [Lesepfad](lesepfad-fuer-e-commerce.md) |

## Schnelle Antworten

- **Ist der Shop ein Zusatzmodul?** Nein. Er ist Teil des Kernels: Das Shop-Modul und seine Views stehen in
  `kernel/shop/module.php` ([Was der eingebaute Shop kann](../unternehmen/shops/was-der-eingebaute-shop-kann.md)).
- **Welche Zahlungsanbieter?** Das Repository enthält zwei Zahlungserweiterungen, `ezpaypal` (PayPal) und
  `ezauthorize` (Authorize.Net). Andere werden als Gateways geschrieben
  ([Entwicklung von Zahlungs-Gateways](erweitern/entwicklung-von-zahlungs-gateways.md)).
- **Berechnet er die USt. nach Land?** Ja, mit USt-Regeln, sobald ein USt-Handler eingeschaltet ist
  ([Produktkategorien und USt-Regeln](katalog/produktkategorien-und-ust-regeln.md)).
- **Was ist nicht enthalten?** Lagerverwaltung, Gutscheincodes, Rechnungen für die Buchhaltung, Versandetiketten und
  automatische Dateiauslieferung sind nicht im Kernel ([FAQ zu E-Commerce](faq-zu-e-commerce.md#was-der-shop-nicht-kann)).

## Wo Sie sonst nachsehen können

| Thema | Seite |
|---|---|
| Workflows allgemein | [Workflows](../../anleitungen/workflows.md) |
| Rollen und Richtlinien | [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md) |
| Das Shop-Dashboard und die siebzehn Bestellstatus | [Shop-Dashboard und Bestellstatus](../../funktionen/6.0/shop-dashboard.md) |
| Dauerhafte Belege | [Dauerhafte Bestellbelege](../../funktionen/6.0/bestellbelege.md) |
| Verkauf in mehreren Sprachen und Websites | [Mehrsprachige Unternehmen](../unternehmen/mehrsprachig/README.md), [Marken mit mehreren Websites](../unternehmen/multisite/README.md) |
| Erweiterungen bauen | [Anwendungsentwickler: hier beginnen](../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md) |

## Verwandte Seiten

- [Onlineshops und kleine Shops](../unternehmen/shops/README.md)
- [Ihren Shop betreiben](../kunden/handel/README.md)
- [Unternehmen: hier beginnen](../unternehmen/unternehmen-hier-beginnen.md)
- [Kunden: hier beginnen](../kunden/kunden-hier-beginnen.md)
- [Lesepfad für E-Commerce](lesepfad-fuer-e-commerce.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
