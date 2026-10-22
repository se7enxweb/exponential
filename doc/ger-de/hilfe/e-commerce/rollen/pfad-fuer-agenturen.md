---
title: Pfad für Agenturen
audience: ecommerce, partner, businesses
category: e-commerce
order: 14
summary: Ein Lieferpfad für Agenturen und Partner, die Exponential-Shops für Kunden bauen - Fragen für die Bedarfsanalyse, Abgrenzung dessen, was der Kernel kann, von dem, was gebaut werden muss, eine Reihenfolge für die Umsetzung, Test- und Übergabepakete und die Pflege nach dem Start.
keywords: Agentur, Partner, Kundenprojekt, Bedarfsanalyse, Abgrenzung, Schätzung, Reihenfolge der Umsetzung, Übergabe, Pflege, Shop-Lieferung, E-Commerce-Projekt, agency, client project, discovery, scoping, estimate, build order, handover, maintenance, store delivery
type: help
---
# Pfad für Agenturen

Diese Seite ist für Agenturen und Partner, die Exponential-Shops an Kunden liefern. Sie macht aus den Fakten des Shops
einen Lieferpfad: was Sie in der Bedarfsanalyse fragen, wie Sie Kernel-Funktionen von individueller Arbeit abgrenzen, die
Reihenfolge der Umsetzung und was Sie übergeben.

## Inhalt

1. [Fragen für die Bedarfsanalyse](#fragen-für-die-bedarfsanalyse)
2. [Kernel und individuelle Arbeit abgrenzen](#kernel-und-individuelle-arbeit-abgrenzen)
3. [Reihenfolge der Umsetzung](#reihenfolge-der-umsetzung)
4. [Test und Abnahme](#test-und-abnahme)
5. [Übergabepaket](#übergabepaket)
6. [Pflege](#pflege)

## Fragen für die Bedarfsanalyse

| Bereich | Fragen Sie | Warum es wichtig ist |
|---|---|---|
| Katalog | Wie viele Produkte, welche Arten, welche Felder, welche Optionen? | Produktklassen und Importe ([Katalogmodellierung im Detail](../katalog/katalogmodellierung-im-detail.md)) |
| Preise | Eine oder mehrere Währungen? Preise von Hand oder umgerechnet? | `ezprice` oder `ezmultiprice` ([Preisfelder im Vergleich](../katalog/preisfelder-im-vergleich.md)) |
| Steuern | Welche Länder, welche Steuerklassen für Produkte? | USt-Typen, -Regeln und ein USt-Handler ([Produktkategorien und USt-Regeln](../katalog/produktkategorien-und-ust-regeln.md)) |
| Versand | Pauschal, nach Gewicht, nach Zone, nach Tarif des Versanddienstleisters, Abholung? | Einfacher Versand oder ein Handler ([Eigener Versand-Handler](../erweitern/eigener-versand-handler.md)) |
| Zahlung | Welcher Anbieter? Hat er eine Weiterleitungsseite? | Vorhandene Erweiterung oder ein neues Gateway ([Entwicklung von Zahlungs-Gateways](../erweitern/entwicklung-von-zahlungs-gateways.md)) |
| Kunden | Konten, Gäste, B2B-Gruppen mit Rabatten? | Konto-Handler, Rabattgruppen ([shopaccount.ini durchgegangen](../einstellungen/shopaccount-ini-durchgegangen.md)) |
| Backoffice | Lager, Buchhaltung, ERP, Etiketten? | Integrationen, nicht im Kernel ([Entscheidungs-Checklisten](../betrieb/entscheidungs-checklisten.md)) |
| Websites | Ein Shop oder mehrere Marken und Sprachen? | [Mehrere Shops und Multisite](../betrieb/mehrere-shops-und-multisite.md) |
| Daten | Produkte von wo importieren? | [Produktimporte und Migrationen](../betrieb/produktimporte-und-migrationen.md) |

## Kernel und individuelle Arbeit abgrenzen

| Im Kernel oder im Repository enthalten | Nur Konfiguration | Individuelle Arbeit |
|---|---|---|
| Warenkorb, Kasse, Bestellungen, 17 Status, Dashboard, Belege | USt-Typen und -Regeln (plus `Handler=ezdefault`), Währungen, Rabattgruppen, Status, Name des Warenkorbs, Belege | Versandregeln über einen Pauschalbetrag hinaus |
| PayPal (`ezpaypal`), Authorize.Net (`ezauthorize`) | Gateway-Einstellungen, der Kassen-Workflow | Gateways für andere Anbieter |
| Pauschaler Versand (`ezsimpleshipping`) | `workflow.ini [SimpleShippingWorkflow]` | Lager, Gutscheine, Etiketten, Export in die Buchhaltung, Auslieferung von Downloads |
| Bestellstatistik, Bestseller, Fetch „auch gekauft“ | Templates, die sie nutzen | Berichte darüber hinaus ([Shop-Kennzahlen und Exporte](../betrieb/shop-kennzahlen-und-exporte.md)) |

Schätzen Sie die Spalte „individuell“ als Erweiterungsarbeit mit eigenen Tests und eigener Dokumentation.

## Reihenfolge der Umsetzung

1. Grundgerüst der Einstellungen in einer Erweiterung oder in `settings/override/` ([Die Shop-Einstellungen durchgegangen](../einstellungen/README.md)).
2. Produktklassen und ein Testkatalog; dann Importe.
3. USt-Typen und -Regeln; Währungen; Rundung.
4. Templates: Vollansicht des Produkts, Warenkorb, Kassenseiten, Bestellmail.
5. Shop-Konto-Handler und das Formular für Kundendaten.
6. Versand-Workflow oder -Handler.
7. Zahlungs-Gateway im Testmodus und der Kassen-Workflow.
8. Status, Rollen und Richtlinien für die Mitarbeitenden des Kunden.
9. Landingpages mit Layouts ([Content Commerce mit Layouts](../betrieb/content-commerce-mit-layouts.md)).
10. Tests, Prüfung der Barrierefreiheit, Checkliste für den Start.

## Test und Abnahme

Vereinbaren Sie die Abnahme vor der Umsetzung anhand eines schriftlichen Testplans: [Einen Shop testen](../betrieb/einen-shop-testen.md)
liefert einen, und der Kunde gibt mit der [Checkliste für den Shop-Start](../../unternehmen/shops/checkliste-fuer-den-shop-start.md)
frei. Auch die Barrierefreiheit gehört zur Abnahme ([Barrierefreiheit im Shop](../betrieb/barrierefreiheit-im-shop.md)).

## Übergabepaket

- [ ] Die geänderten Einstellungen, Datei für Datei, mit dem Grund für jede.
- [ ] Die mit Shop-Triggern verbundenen Workflows und was jedes Ereignis tut.
- [ ] Die für Shop-Mitarbeitende angelegten Rollen und die Richtlinien jeder Rolle.
- [ ] Die Zugangsdaten zum Konto beim Zahlungsanbieter: wer sie hat (nie im Übergabedokument selbst).
- [ ] Die Cronjobs, von denen der Shop abhängt (`basket_cleanup` in `infrequent`, `workflow` in `frequent`).
- [ ] Links zu [Ihren Shop betreiben](../../kunden/handel/README.md) für die Mitarbeitenden des Kunden.
- [ ] Der individuelle Code, seine Tests und wie er aktualisiert wird.

## Pflege

Bieten Sie einen Kalender an, nicht nur eine Support-Hotline: [Betriebskalender](../betrieb/betriebskalender.md)
listet die wiederkehrenden Arbeiten eines Shops auf. Führen Sie nach jedem Exponential-Update den Testplan des Shops
erneut aus ([Upgrades ohne Überraschungen](../../unternehmen/zuverlaessigkeit/aktualisierungen-ohne-ueberraschungen.md)).

## Verwandte Seiten

- [Pfade nach Rolle](README.md)
- [Partner: hier beginnen](../../partner/partner-hier-beginnen.md)
- [Einen Partner auswählen](../../unternehmen/auswahl/einen-partner-auswaehlen.md)
- [Erweiterungen zurückgeben](../../erweiterungen-zurueckgeben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
