---
title: Mehrere Shops und Multisite
audience: ecommerce, website-creator, businesses
category: e-commerce
order: 73
summary: Mehrere Shopfronten mit Exponential betreiben - was geteilt wird, wenn Siteaccesses eine Datenbank teilen (Bestellungen, USt, Währungen, Rabatte, Status), was je Siteaccess abweichen kann (Währung, Name des Warenkorbs, Templates, Handler), wie Sie Bestellungen auseinanderhalten und wann getrennte Installationen die bessere Wahl sind.
keywords: mehrere Shops, Multisite, Shopfront, Marken, geteilte Bestellungen, Einstellungen je Site, Währung je Site, mehrsprachiger Shop, getrennte Installation, geteilte Datenbank, multi-store, multisite, several shops, siteaccess, store front, brands, shared orders, per-site settings, currency per site, multilingual store, separate installation, shared database
type: help
---
# Mehrere Shops und Multisite

Diese Seite richtet sich an Händler, Agenturen und Entwickler, die mehr als eine Shopfront planen: mehrere Marken,
Länder oder Sprachen. Exponential bedient über Siteaccesses mehrere Sites aus einer Installation
([Mehrere Sites in einer Installation einrichten](../../ersteller/website-ersteller/multisite-einrichten.md)). Der Shop
funktioniert in diesem Aufbau, aber einige seiner Daten teilen sich alle Sites auf derselben Datenbank, und das
bestimmt den Entwurf.

## Inhalt

1. [Geteilt und getrennt](#geteilt-und-getrennt)
2. [Was je Siteaccess abweichen kann](#was-je-siteaccess-abweichen-kann)
3. [Bestellungen auseinanderhalten](#bestellungen-auseinanderhalten)
4. [Drei Entwürfe](#drei-entwürfe)
5. [Checkliste](#checkliste)

## Geteilt und getrennt

Shop-Daten liegen in Datenbanktabellen, nicht je Site. Siteaccesses auf einer Datenbank teilen sich:

| Geteilt | Hinweis |
|---|---|
| Bestellungen, Bestellnummern, Status und die Statusliste | `ezorder` hat keine Site-Spalte; eine Bestellliste, eine Nummernfolge |
| USt-Typen, USt-Regeln, Produktkategorien | Eine Steuereinrichtung |
| Währungen und Wechselkurse | Eine Währungsliste |
| Rabattgruppen und -regeln | Ein Satz |
| Kunden (Benutzerkonten) | Ein Benutzerbestand, sofern die Sites nicht getrennte Benutzerbäume und Richtlinien verwenden |
| Produkte | Geteilt oder getrennt, je nachdem, wo Sie sie im Inhaltsbaum ablegen |
| Das Shop-Dashboard | Kennzahlen für alle Sites zusammen |

Warenkörbe gehören zu Sitzungen, sodass sie der Sitzung des Besuchers auf der Site folgen, auf der er einkauft.

## Was je Siteaccess abweichen kann

Einstellungen in `settings/siteaccess/<siteaccess>/` überschreiben die der Installation für diese Site
([Durchgänge durch die Shop-Einstellungen](../einstellungen/README.md#wohin-shop-einstellungen-gehören),
[Einstellungen und Logs je Site](../../unternehmen/multisite/einstellungen-logs-und-caches-pro-website.md)):

| Kann abweichen | Einstellung oder Ort |
|---|---|
| Standardwährung | `shop.ini [CurrencySettings] PreferredCurrency` |
| Adressname des Warenkorbs | `shop.ini [BasketSettings] BasketViewName` |
| Weiterleitung nach „Zum Warenkorb hinzufügen“ | `site.ini [ShopSettings] RedirectAfterAddToBasket` |
| Templates für Shop- und Bestellmails, Produktseiten | das Design des Siteaccess ([Designs je Marke](../../unternehmen/multisite/ein-design-pro-marke.md)) |
| Absender und Shop-Adresse der Bestellmail | `site.ini [MailSettings]` |
| Sprache des Shops | die Sprachen des Siteaccess ([Mehrsprachige Unternehmen](../../unternehmen/mehrsprachig/README.md)) |

Workflows und Trigger werden in der Datenbank gespeichert und sind für alle Sites gleich. Wenn zwei Sites verschiedene
Zahlungsanbieter brauchen, wählen Sie beide Gateways im Zahlungsereignis aus und lassen Sie den Kunden wählen, oder
schreiben Sie ein Ereignis, das nach Site auswählt.

## Bestellungen auseinanderhalten

Da Bestellungen keine Site tragen, legen Sie fest, wie Mitarbeiter sie auseinanderhalten:

- getrennte Produktordner je Site, sodass die Produkte einer Bestellung zeigen, woher sie stammt;
- die Währung der Bestellung, wenn jede Site in ihrer eigenen Währung verkauft;
- ein Feld, das Ihr eigener Shop-Konto-Handler oder ein Ereignis am After-Trigger von `shop_checkout` hinzufügt und das
  den Siteaccess festhält ([Shop-Konto-Handler](../erweitern/shop-konto-handler.md)).

## Drei Entwürfe

| Entwurf | Passt zu | Achten Sie auf |
|---|---|---|
| Ein Shop, mehrere Sprachen | Ein Unternehmen, eine Steuereinrichtung | Preise und Optionen je Übersetzung ([Katalogmodellierung im Detail](../katalog/katalogmodellierung-im-detail.md#übersetzungen)) |
| Mehrere Markenfronten, ein Unternehmen | Gemeinsamer Lagerbestand und gemeinsame Mitarbeiter, ein Buchhalter | Eine Bestellliste für alle Marken; halten Sie Bestellungen wie oben auseinander |
| Getrennte Unternehmen oder Steuereinrichtungen | Verschiedene Firmen, USt-Registrierungen oder Anbieter | Verwenden Sie getrennte Installationen oder Datenbanken; geteilte Tabellen lassen sich später nicht ohne Aufwand aufteilen |

## Checkliste

- [ ] Entscheiden Sie für jeden Punkt der Tabelle „Geteilt“, ob das Teilen akzeptabel ist.
- [ ] Setzen Sie Währung, Mail-Absender und Templates je Site in den Siteaccess-Einstellungen.
- [ ] Legen Sie vor dem Start fest, wie Mitarbeiter die Bestellungen einer Site erkennen.
- [ ] Testen Sie eine vollständige Bestellung auf jeder Site ([Einen Shop testen](einen-shop-testen.md)).

## Verwandte Seiten

- [Marken mit mehreren Sites](../../unternehmen/multisite/README.md)
- [Eine Installation, viele Sites](../../unternehmen/multisite/eine-installation-viele-websites.md)
- [Checkliste für mehrere Marken](../../unternehmen/multisite/checkliste-fuer-mehrere-marken.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
