---
title: Entscheidungs-Checklisten
audience: ecommerce, businesses, project-manager
category: e-commerce
order: 81
summary: Kurze Checklisten für die Entscheidungen, vor denen ein Exponential-Shop steht - Preisfeld, USt-Modus, Konto-Handler, Versand, Zahlungs-Gateway, Belege, Verhalten des Warenkorbs, Aufbau mit mehreren Sites, was über den Kernel hinaus zu bauen ist und ob archiviert oder entfernt wird - jeweils mit den Fakten, die entscheiden.
keywords: Entscheidungs-Checkliste, Shop-Entscheidungen, Preisfeld, USt-Handler, Konto-Handler, Versand, Zahlungs-Gateway, Belege, Warenkorb-Einstellungen, Lagerbestand, Gutscheine, Buchhaltung, archivieren oder entfernen, decision checklist, store decisions, price field, VAT handler, account handler, shipping, payment gateway, receipts, basket settings, multisite, build or buy, stock, coupons, accounting, archive or remove
type: help
---
# Entscheidungs-Checklisten

Diese Seite richtet sich an Händler, Projektleiter und Agenturen, die Entscheidungen für einen Shop treffen und
festhalten müssen, warum. Jede Checkliste ist kurz; haken Sie ab, was zutrifft, und die Antwort ergibt sich. Die Fakten
dahinter stehen auf der verlinkten Seite.

## Inhalt

1. [Preisfeld](#preisfeld)
2. [USt-Modus](#ust-modus)
3. [Kundendaten](#kundendaten)
4. [Versand](#versand)
5. [Zahlung](#zahlung)
6. [Belege und Verhalten des Warenkorbs](#belege-und-verhalten-des-warenkorbs)
7. [Mehrere Sites](#mehrere-sites)
8. [Über den Kernel hinaus](#über-den-kernel-hinaus)
9. [Archivieren oder entfernen](#archivieren-oder-entfernen)

## Preisfeld

- [ ] Wir verkaufen in mehr als einer Währung.
- [ ] Preise je Währung werden nach Markt festgelegt, nicht umgerechnet.

Nichts abgehakt: `ezprice`. Das erste abgehakt: `ezmultiprice`, je nach dem zweiten mit automatischen oder eigenen Preisen.
([Preisfelder im Vergleich](../katalog/preisfelder-im-vergleich.md))

## USt-Modus

- [ ] Alle Produkte haben überall, wo wir verkaufen, denselben Satz.
- [ ] Die Sätze unterscheiden sich nur nach Produkt.
- [ ] Die Sätze unterscheiden sich nach Land des Kunden und Produktkategorie.
- [ ] Die Sätze hängen von etwas anderem ab (Geschäftskunden, Lieferadresse, ein externer Dienst).

Erstes: ein USt-Typ. Zweites: mehrere USt-Typen, je Produkt gewählt. Drittes: USt-Regeln plus
`[VATSettings] Handler=ezdefault`. Viertes: ein eigener USt-Handler.
([Produktkategorien und USt-Regeln](../katalog/produktkategorien-und-ust-regeln.md),
[Eigener USt-Handler](../erweitern/eigener-ust-handler.md))

## Kundendaten

- [ ] Gäste dürfen kaufen.
- [ ] Wir brauchen eine vollständige Postanschrift.
- [ ] Wir brauchen Felder, die den mitgelieferten Formularen fehlen (Firma, USt-ID, Lieferadresse).

Gäste und vollständige Adresse: `ezuser`. Kurzes Formular: `ezsimple`. Nur Konten: `ezdefault`. Zusätzliche Felder:
eigener Handler. ([Durchgang durch shopaccount.ini](../settings/shopaccount-ini-durchgegangen.md))

## Versand

- [ ] Ein Pauschalbetrag pro Bestellung ist akzeptabel.
- [ ] Der Versand muss USt tragen.
- [ ] Die Sätze unterscheiden sich nach Land, Gewicht oder Betrag, oder Kunden wählen eine Versandart.

Nur das erste: das Ereignis einfacher Versand. Alles andere: ein Versand-Handler.
([Das Ereignis einfacher Versand](../kasse/das-ereignis-einfacher-versand.md), [Eigener Versand-Handler](../erweitern/eigener-versand-handler.md))

## Zahlung

- [ ] Unser Anbieter ist PayPal.
- [ ] Unser Anbieter ist Authorize.Net, und wir akzeptieren, dass Kartendaten über unseren Server laufen.
- [ ] Unser Anbieter bietet eine gehostete Zahlungsseite oder eine Weiterleitung.

Erstes: `ezpaypal`. Zweites: `ezauthorize`. Drittes: Schreiben Sie ein Weiterleitungs-Gateway. Sonst: ein transparentes
Gateway, mit der Arbeit für die Kartensicherheit, die das mit sich bringt.
([Entwicklung von Zahlungs-Gateways](../erweitern/entwicklung-von-zahlungs-gateways.md))

## Belege und Verhalten des Warenkorbs

- [ ] Gäste kaufen und sollen ihre Bestellung später erreichen: `OrderLinkView=orderreceipt`.
- [ ] Kunden kaufen viele Artikel auf einmal: `RedirectAfterAddToBasket=reload`.
- [ ] Die Dokumentation des Zahlungsanbieters verlangt einen leeren Warenkorb vor der Zahlung: `ClearBasketOnCheckout=enabled`.
- [ ] Computer werden gemeinsam genutzt: `ClearBasketOnLogout=enabled`.

([Durchgang durch shop.ini](../settings/shop-ini-durchgegangen.md), [Durchgang durch die Shop-Einstellungen in site.ini](../settings/shop-einstellungen-in-site-ini-durchgegangen.md))

## Mehrere Sites

- [ ] Eine Bestellliste, eine Steuereinrichtung und eine Währungsliste für alle Sites ist akzeptabel.

Abgehakt: Siteaccesses auf einer Installation. Nicht abgehakt: getrennte Installationen oder Datenbanken.
([Mehrere Shops und Multisite](mehrere-shops-und-multisite.md))

## Über den Kernel hinaus

Nicht im Kernel, sodass jeder abgehakte Punkt ein Projekt ist:

- [ ] Lagerverwaltung
- [ ] Gutschein- oder Rabattcodes
- [ ] Rechnungen in einem Buchhaltungssystem, Buchhaltungsexport
- [ ] Versandetiketten, Anbindung von Versanddienstleistern
- [ ] Automatische Auslieferung von Dateien nach der Zahlung
- [ ] Marktplätze

Für jeden: eine Erweiterung, ein Workflow-Ereignis oder ein Handler, selbst gebaut oder von einem Partner
([Was der eingebaute Shop leistet](../../unternehmen/shops/was-der-eingebaute-shop-kann.md#was-nicht-enthalten-ist)).

## Archivieren oder entfernen

- [ ] Die Bestellung ist abgeschlossen und muss für die Unterlagen aufbewahrt werden: archivieren.
- [ ] Die Bestellung ist eine Testbestellung im Live-Shop: entfernen.
- [ ] Daten müssen gelöscht werden, und nichts verpflichtet uns, sie aufzubewahren: entfernen und festhalten.
- [ ] Dies ist eine Testdatenbank vor der Übergabe: das Aufräumskript des Shops.

([Archivieren, Entfernen und Aufräumen](../bestellungen/archivieren-entfernen-und-aufraeumen.md))

## Verwandte Seiten

- [Einen Shop betreiben](README.md)
- [Pfad für Händler](../rollen/pfad-fuer-haendler.md)
- [Shop-Planung für kleine Unternehmen](../../unternehmen/shops/shop-planung-fuer-kleine-unternehmen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
