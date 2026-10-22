---
title: Pfad für Händler
audience: ecommerce, businesses, customer
category: e-commerce
order: 11
summary: Ein Lese- und Entscheidungspfad für den Händler, dem ein Exponential-Shop gehört - die Entscheidungen, die nur der Inhaber treffen kann, der Reihe nach, mit der Seite, die jede erklärt, und dem, was Sie an den Entwickler übergeben.
keywords: Händler, Shop-Inhaber, Entscheidungen, Zahlungsanbieter, USt., Preise, Währungen, Rabatte, Budget, Start, Übergabe, Pfad, merchant, store owner, decisions, payment provider, VAT, prices, currencies, discounts, launch, handover, path
type: help
---
# Pfad für Händler

Diese Seite ist für den Händler: den Inhaber oder Manager, der dafür verantwortlich ist, was der Shop verkauft, zu
welchem Preis und nach welchen Regeln. Sie listet die Entscheidungen auf, die nur Sie treffen können, in der Reihenfolge,
in der sie anstehen, und was jede Entscheidung Ihrem Entwickler oder Ihrer Agentur als Arbeitsgrundlage gibt.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Entscheidung 1 Was Sie verkaufen und wie es modelliert wird](#entscheidung-1-was-sie-verkaufen-und-wie-es-modelliert-wird)
3. [Entscheidung 2 Preise und Währungen](#entscheidung-2-preise-und-währungen)
4. [Entscheidung 3 Steuern](#entscheidung-3-steuern)
5. [Entscheidung 4 Kundendaten und Konten](#entscheidung-4-kundendaten-und-konten)
6. [Entscheidung 5 Versand](#entscheidung-5-versand)
7. [Entscheidung 6 Zahlung](#entscheidung-6-zahlung)
8. [Entscheidung 7 Bestellablauf und Status](#entscheidung-7-bestellablauf-und-status)
9. [Entscheidung 8 Was Sie über den Kernel hinaus ergänzen](#entscheidung-8-was-sie-über-den-kernel-hinaus-ergänzen)
10. [Nach dem Start](#nach-dem-start)

## Bevor Sie beginnen

Lesen Sie [Was der eingebaute Shop kann](../../unternehmen/shops/was-der-eingebaute-shop-kann.md) und
[Shop-Planung für kleine Unternehmen](../../unternehmen/shops/shop-planung-fuer-kleine-unternehmen.md). Sie beschreiben den
Shop für Entscheider. Dieser Pfad setzt voraus, dass Sie sich entschieden haben, mit Exponential zu verkaufen.

## Entscheidung 1 Was Sie verkaufen und wie es modelliert wird

| Sie entscheiden | Das gibt dem Entwickler | Lesen |
|---|---|---|
| Arten von Produkten (physisch, digital, Dienstleistungen) und die Felder, die jede braucht | Die Produktklassen | [Katalogmodellierung im Detail](../katalog/katalogmodellierung-im-detail.md) |
| Ob Kunden Optionen (Größe, Farbe) wählen, die den Preis ändern | Optionsfelder | [Optionen und Varianten](../katalog/optionen-und-varianten.md) |
| Wie der Katalog für Kunden gegliedert ist | Die Ordnerstruktur | [Katalogmodellierung](../katalog/README.md) |

## Entscheidung 2 Preise und Währungen

Eine Währung oder mehrere? Wenn mehrere: Legen Sie jeden Preis von Hand fest, oder rechnen Wechselkurse um? Die Antwort
entscheidet zwischen den Feldern `ezprice` und `ezmultiprice` ([Preisfelder im Vergleich](../katalog/preisfelder-im-vergleich.md))
und über die Währungseinstellungen in `shop.ini` ([shop.ini durchgegangen](../settings/shop-ini-durchgegangen.md)).

## Entscheidung 3 Steuern

Ein USt-Satz für alles, einige benannte Sätze pro Produkt oder Sätze nach Land des Kunden und Produktkategorie. Das
Dritte braucht USt-Regeln und einen USt-Handler, den der Entwickler einschaltet
([Produktkategorien und USt-Regeln](../katalog/produktkategorien-und-ust-regeln.md)). Das Steuerrecht selbst ist Sache
Ihrer Steuerberatung ([USt-Regeln und USt-Typen](../../unternehmen/shops/ust-regeln-und-ust-typen.md)).

## Entscheidung 4 Kundendaten und Konten

Müssen Kunden ein Konto anlegen, dürfen sie als Gast kaufen, und welche Adressfelder brauchen Sie? Der Shop-Konto-Handler
setzt die Antwort um ([shopaccount.ini durchgegangen](../settings/shopaccount-ini-durchgegangen.md),
[Kundenkonten und Gastbestellung](../../unternehmen/shops/kundenkonten-und-gastbestellung.md)).

## Entscheidung 5 Versand

Ein Pauschalbetrag pro Bestellung wird mit dem Kernel ausgeliefert (das Workflow-Ereignis „Simple shipping“). Alles
andere, nach Gewicht, nach Land oder nach Tarif des Versanddienstleisters, ist ein Versand-Handler, den ein Entwickler
schreibt ([Das Ereignis einfacher Versand](../kasse/das-ereignis-einfacher-versand.md), [Eigener Versand-Handler](../erweitern/eigener-versand-handler.md)).

## Entscheidung 6 Zahlung

PayPal (`ezpaypal`), Authorize.Net (`ezauthorize`) oder ein Anbieter, für den ein Gateway geschrieben werden muss. Ein
Weiterleitungs-Gateway hält Kartendaten von Ihrer Website fern; ein Kartenformular auf Ihrer Website erhöht Ihre
Sicherheitspflichten für Karten ([Zahlungs-Gateways für Unternehmen](../../unternehmen/shops/zahlungsanbieter-fuer-unternehmen.md),
[Kartenzahlungen und PCI-Grundlagen](../../unternehmen/shops/kartenzahlungen-und-pci-grundlagen.md)).

## Entscheidung 7 Bestellablauf und Status

Welche der siebzehn ausgelieferten Status Sie verwenden und wer eine Bestellung von einem zum nächsten bewegen darf
([Der Lebenszyklus einer Bestellung im Betrieb](../bestellungen/der-lebenszyklus-einer-bestellung-im-betrieb.md),
[Statusübergänge und Berechtigungen](../bestellungen/statusuebergaenge-und-berechtigungen.md)).

## Entscheidung 8 Was Sie über den Kernel hinaus ergänzen

Lager, Gutscheincodes, Export in die Buchhaltung, Versandetiketten und automatische Auslieferung von Downloads sind nicht
im Kernel. Entscheiden Sie, was Sie zum Start brauchen, und planen Sie es als Erweiterungsarbeit ein
([Entscheidungs-Checklisten](../betrieb/entscheidungs-checklisten.md)).

## Nach dem Start

Übergeben Sie die tägliche Arbeit an den Shop-Manager ([Pfad für Shop-Manager](pfad-fuer-shop-manager.md)) und halten Sie
einen jährlichen Rhythmus ([Betriebskalender](../betrieb/betriebskalender.md)). Monatliche Zahlen kommen aus dem
Dashboard und der Statistik ([Shop-Kennzahlen und Exporte](../betrieb/shop-kennzahlen-und-exporte.md)).

## Verwandte Seiten

- [Pfade nach Rolle](README.md)
- [Checkliste für den Shop-Start](../../unternehmen/shops/checkliste-fuer-den-shop-start.md)
- [Die ersten 30 Tage als Inhaber](../../kunden/ihre-ersten-30-tage.md)
- [Durchgerechnetes Beispiel: kleiner Shop](../../unternehmen/kosten/durchgerechnetes-beispiel-kleiner-shop.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
