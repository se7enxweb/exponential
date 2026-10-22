---
title: Glossar zu E-Commerce
audience: ecommerce, customer, businesses
category: e-commerce
order: 95
summary: Die Begriffe des E-Commerce-Bereichs erklärt - von Produktklasse, Preisfeld und Produktsammlung bis temporäre Bestellung, Trigger, Zahlungsobjekt, Rückruf, Shop-Konto-Handler, dynamische USt. und expservices - jeweils mit der Seite, die sie vollständig erklärt.
keywords: Glossar, E-Commerce-Begriffe, Produktklasse, ezprice, ezmultiprice, Produktsammlung, Warenkorb, temporäre Bestellung, Bestellnummer, Bestellposten, Statusverlauf, Trigger, Workflow-Ereignis, Zahlungs-Gateway, Zahlungsobjekt, Rückruf, Callback, Shop-Konto-Handler, USt-Regel, Produktkategorie, dynamische USt., Handler, Siteaccess, expservices, glossary, e-commerce terms, product class, basket, temporary order, payment object, VAT rule
type: help
---
# Glossar zu E-Commerce

Die im E-Commerce-Bereich verwendeten Begriffe, erklärt für Händler, Shop-Manager und Entwickler. Jeder Eintrag
verweist auf die Seite, die ihn vollständig erklärt. Die geschäftlichen Begriffe stehen auch im
[Glossar für Unternehmen](../unternehmen/glossar-fuer-unternehmen.md) und die Begriffe für Shop-Inhaber im
[Glossar für Kunden](../kunden/glossar-fuer-kunden.md).

## Inhalt

1. [Katalog und Preise](#katalog-und-preise)
2. [Warenkorb und Kasse](#warenkorb-und-kasse)
3. [Bestellungen](#bestellungen)
4. [Steuern und Versand](#steuern-und-versand)
5. [Erweitern und integrieren](#erweitern-und-integrieren)

## Katalog und Preise

| Begriff | Bedeutung | Lesen |
|---|---|---|
| Produktklasse | Eine Inhaltsklasse mit einem Preisfeld | [Katalogmodellierung](katalog/README.md) |
| `ezprice` | Preis-Datentyp mit einem Betrag, einem USt-Typ und der Angabe „inklusive USt.“ oder nicht | [Preisfelder im Vergleich](katalog/preisfelder-im-vergleich.md) |
| `ezmultiprice` | Preis-Datentyp mit einem Betrag pro Währung | ebenda |
| Individueller Preis | Ein von Hand eingegebener Betrag in `ezmultiprice` | ebenda |
| Automatischer Preis | Ein mit Wechselkursen umgerechneter Betrag in `ezmultiprice` | ebenda |
| Options-Datentypen | `ezoption`, `ezmultioption`, `ezmultioption2`, `ezrangeoption`: Auswahlmöglichkeiten mit optionalen Aufpreisen | [Optionen und Varianten](katalog/optionen-und-varianten.md) |
| Produktübersicht | `shop/productsoverview`: Produkte einer Klasse mit ihren Preisen | [Katalogmodellierung](katalog/README.md#der-bildschirm-produktübersicht) |

## Warenkorb und Kasse

| Begriff | Bedeutung | Lesen |
|---|---|---|
| Warenkorb | Die Auswahl des Kunden, an die Session gebunden; `shop/basket` | [Ablauf von Warenkorb und Kasse](../unternehmen/shops/warenkorb-und-bestellablauf.md) |
| Produktsammlung | Die Posten eines Warenkorbs und später der Bestellung, mit Namen, Preisen, USt. und Optionen | [Bestellbetrieb](bestellungen/README.md#eine-bestellung-in-einer-tabelle) |
| Operation | Ein Shop-Schritt mit Triggerpunkten: `addtobasket`, `updatebasket`, `confirmorder`, `checkout` | [Die Kassenoperationen Schritt für Schritt](kasse/die-kassenoperationen-schritt-fuer-schritt.md) |
| Trigger | Der Punkt vor oder nach einer Operation, an dem ein Workflow läuft | [Workflows an Shop-Triggern](kasse/workflows-an-shop-triggern.md) |
| Workflow-Ereignis | Ein Schritt eines Workflows, etwa „Simple shipping“ oder „Payment Gateway“ | ebenda |
| Temporäre Bestellung | Der Bestelldatensatz, mit dem die Kasse arbeitet, bevor er aufgegeben ist | [Temporäre Bestellungen und Aktivierung](kasse/temporaere-bestellungen-und-aktivierung.md) |
| Aktivierung | Der Moment, in dem eine temporäre Bestellung zu einer aufgegebenen Bestellung mit Nummer wird | ebenda |
| Zahlungs-Gateway | Die Klasse, die die Zahlung für einen Anbieter entgegennimmt | [Das Ereignis Zahlungs-Gateway](kasse/das-ereignis-zahlungs-gateway.md) |
| Weiterleitungs-Gateway | Ein Gateway, das den Kunden zum Anbieter schickt und auf den Rückruf wartet | ebenda |
| Transparentes Gateway | Ein Gateway, das in einer Anfrage von Ihrem Server aus abbucht | ebenda |
| Zahlungsobjekt | Der Datensatz, der den Zustand einer Weiterleitungszahlung zwischen den beiden Besuchen festhält | ebenda |
| Rückruf (Callback) | Die Anfrage des Anbieters an Ihre Website, die eine Zahlung bestätigt | ebenda |
| Multiplexer | Ein Workflow-Ereignis, das einen anderen Workflow ausführt, wenn Bedingungen zutreffen | [Der Multiplexer im Shop](kasse/der-multiplexer-im-shop.md) |

## Bestellungen

| Begriff | Bedeutung | Lesen |
|---|---|---|
| Bestellnummer | Die Nummer, die eine aufgegebene Bestellung bei der Aktivierung erhält; nennen Sie sie Kunden | [Temporäre Bestellungen und Aktivierung](kasse/temporaere-bestellungen-und-aktivierung.md#bestell-id-und-bestellnummer) |
| Bestellposten | Eine zusätzliche Zeile in einer Bestellung, etwa der Versand | [Das Ereignis einfacher Versand](kasse/das-ereignis-einfacher-versand.md) |
| Bestellstatus | Die Phase einer Bestellung; siebzehn werden ausgeliefert, eigene ab 1000 | [Der Lebenszyklus einer Bestellung im Betrieb](bestellungen/der-lebenszyklus-einer-bestellung-im-betrieb.md) |
| Statusverlauf | Jede Statusänderung mit Benutzer und Zeitpunkt | ebenda |
| `FromStatus`, `ToStatus` | Einschränkungen von `shop/setstatus` | [Statusübergänge und Berechtigungen](bestellungen/statusuebergaenge-und-berechtigungen.md) |
| Archiv | Eine abgeschlossene Bestellung aus der Bestellliste ausblenden und behalten | [Archivieren, Entfernen und Aufräumen](bestellungen/archivieren-entfernen-und-aufraeumen.md) |
| Dauerhafter Beleg | `shop/orderreceipt/<token>`: ein Beleglink, der nie abläuft | [Bestellmails und Belege für Betreiber](bestellungen/bestellmails-und-belege-fuer-betreiber.md) |

## Steuern und Versand

| Begriff | Bedeutung | Lesen |
|---|---|---|
| USt-Typ | Ein benannter Prozentsatz | [USt-Regeln und USt-Typen](../unternehmen/shops/ust-regeln-und-ust-typen.md) |
| Produktkategorie | Die Steuerklasse eines Produkts, von USt-Regeln verwendet | [Produktkategorien und USt-Regeln](katalog/produktkategorien-und-ust-regeln.md) |
| USt-Regel | Welcher USt-Typ für ein Land und Produktkategorien gilt | ebenda |
| Dynamische USt. | „Durch USt-Regeln bestimmt“: Der USt-Handler wählt zum Zeitpunkt des Kaufs | ebenda |
| Einfacher Versand | Das Workflow-Ereignis für pauschalen Versand | [Das Ereignis einfacher Versand](kasse/das-ereignis-einfacher-versand.md) |
| Versand-Handler | Eine Klasse, die den Versand aus dem Warenkorb berechnet | [Eigener Versand-Handler](erweitern/eigener-versand-handler.md) |

## Erweitern und integrieren

| Begriff | Bedeutung | Lesen |
|---|---|---|
| Handler | Eine über eine Einstellung gewählte Klasse, die der Kernel anstelle seiner eigenen aufruft | [Den Shop erweitern](erweitern/README.md) |
| Shop-Konto-Handler | Bestimmt das Formular für Kundendaten an der Kasse und liest es wieder aus | [Shop-Konto-Handler](erweitern/shop-konto-handler.md) |
| Handler für die Bestellbestätigung | Versendet die Bestellmail | [Handler für Warenkorbinformationen und Bestellbestätigung](erweitern/handler-fuer-warenkorbinformationen-und-bestellbestaetigung.md) |
| Handler für Warenkorbinformationen | Ergänzt die Summen des Warenkorbs pro USt-Satz um den Versand | ebenda |
| Handler für Wechselkurs-Aktualisierung | Ruft Wechselkurse ab | [Wechselkurs-Handler](erweitern/wechselkurs-handler.md) |
| Siteaccess | Eine Website einer Installation, mit eigenen Einstellungen und eigenem Design | [Mehrere Shops und Multisite](betrieb/mehrere-shops-und-multisite.md) |
| `expservices` | Die Erweiterung, die die Funktionen des Shops als entfernte Dienste anbietet | [Headless Commerce mit expservices](betrieb/headless-commerce-mit-expservices.md) |
| Handelskanal | Der Kanal des Prüfpfads für Shop-Ereignisse | [Prüfpfad für den Handel](betrieb/audit-trail-fuer-den-handel.md) |

## Verwandte Seiten

- [E-Commerce: hier beginnen](e-commerce-hier-beginnen.md)
- [FAQ zu E-Commerce](faq-zu-e-commerce.md)
- [Glossar](../../glossar.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
