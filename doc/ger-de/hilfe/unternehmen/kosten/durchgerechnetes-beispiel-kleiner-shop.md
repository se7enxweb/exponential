---
title: "Durchgerechnetes Beispiel: ein kleiner Shop"
audience: businesses, project-manager, sales-marketing
category: business-solutions
order: 40
summary: Eine Kosten- und Ertragsrechnung Schritt für Schritt für einen kleinen Online-Shop mit dem eingebauten Shop von Exponential, nur mit gekennzeichneten Beispielannahmen, mit den Fakten über den Shop, die jeden Kostenposten prägen.
keywords: kleiner Shop, Online-Shop, E-Commerce, durchgerechnetes Beispiel, TCO, ROI, Rohertragsmarge, Zahlungsgebühren, Hosting, Gewinnschwelle, Budget, small store, online shop, e-commerce, worked example, gross margin, payment fees, hosting, break-even, budget
type: help
---
# Durchgerechnetes Beispiel: ein kleiner Shop

Diese Seite ist für Inhaber eines kleinen Unternehmens, die online verkaufen möchten und wissen müssen, ob sich ein
Shop auf Exponential selbst trägt. Sie geht für einen erfundenen Shop eine Rechnung für das erste Jahr und für fünf
Jahre durch, Schritt für Schritt, damit Sie sie mit Ihren eigenen Zahlen wiederholen können.

> **Hinweis**
> Der Shop, seine Verkäufe und alle Kosten unten sind **Beispielannahmen**: runde Zahlen in einer neutralen
> Währungseinheit, so gewählt, dass die Rechnung leicht nachzuvollziehen ist. Es sind weder Marktdaten noch Preise
> eines Anbieters noch Ergebnisse eines echten Shops. Ersetzen Sie jede durch Ihre eigenen Zahlen.

## Inhalt

1. [Was der Shop nutzt](#was-der-shop-nutzt)
2. [Die Eingabewerte](#die-eingabewerte)
3. [Schritt 1: die Kosten](#schritt-1-die-kosten)
4. [Schritt 2: der Rohertrag aus Online-Verkäufen](#schritt-2-der-rohertrag-aus-online-verkäufen)
5. [Schritt 3: ROI und Gewinnschwelle](#schritt-3-roi-und-gewinnschwelle)
6. [Schritt 4: was ein Ausfall kosten würde](#schritt-4-was-ein-ausfall-kosten-würde)
7. [Was Sie für Ihren Shop ändern](#was-sie-für-ihren-shop-ändern)

## Was der Shop nutzt

Die Fakten, die die Kostenposten prägen, jeweils aus dem Repository:

| Bedarf | Was Exponential bietet | Quelle |
|---|---|---|
| Katalog, Warenkorb, Bestellablauf, Bestellungen | „Advanced e-Commerce Stores (Built in)“ (umfassende eingebaute E-Commerce-Shops) | `README.md`, „Main Exponential features“; [Überblick über den Shop](../../../../eng-us/help/creators/website-creators/shop-overview.md) |
| USt und Preise | USt-Typen und -Regeln, Währungen | [Produkte, Preise und USt](../../../../eng-us/help/creators/website-creators/products-prices-and-vat.md) |
| Zahlungen | `ezpaypal` wird von `composer.json` verlangt; andere Zahlungsanbieter sind Erweiterungen | [Zahlungsanbieter](../../../../eng-us/help/creators/website-creators/payment-gateways.md) |
| Übersicht über Bestellungen | Shop-Dashboard und Bestellstatus | [Shop-Dashboard](../../../../eng-us/features/6.0/store-dashboard.md) |
| Belege | Dauerhafte Bestellbelege | [Bestellbelege](../../../../eng-us/features/6.0/order-receipts.md) |
| Datenbank | SQLite 3, kein Datenbankserver, ausreichend für eine Maschine | [Datenbankunterstützung](../../datenbankunterstuetzung.md) |

Das Beispiel hat daher keinen Posten für ein Shop-Zusatzprodukt oder eine Shop-Lizenz. Die Gebühren des
Zahlungsanbieters bleiben; sie legt der Anbieter fest, den Sie wählen.

## Die Eingabewerte

| Eingabewert | Wert |
|---|---|
| Beispielannahme: Einrichtung durch einen Partner (Design, Produktklassen, Zahlung, Start) | 8.000 Währungseinheiten, einmalig |
| Beispielannahme: Hosting-Tarif | 50 Währungseinheiten im Monat |
| Beispielannahme: Wartung und Aktualisierungen | 10 Stunden im Jahr zu 60 Währungseinheiten pro Stunde |
| Beispielannahme: Online-Bestellungen | 100 im Monat |
| Beispielannahme: durchschnittlicher Bestellwert | 40 Währungseinheiten |
| Beispielannahme: Rohertragsmarge auf Waren | 30 % |
| Beispielannahme: Gebühren des Zahlungsanbieters | 3 % des Umsatzes |
| Lizenzgebühr | 0 (Fakt: `README.md`, „License“) |

## Schritt 1: die Kosten

Erstes Jahr:

1. Einrichtung: **8.000**.
2. Hosting: 50 x 12 = **600**.
3. Wartung: 10 x 60 = **600**.
4. Lizenz: **0**.
5. Kosten im ersten Jahr (ohne Zahlungsgebühren): 8.000 + 600 + 600 + 0 = **9.200**.

Jedes spätere Jahr: 600 + 600 = **1.200**.

Fünf Jahre: 9.200 + (1.200 x 4) = 9.200 + 4.800 = **14.000**.

## Schritt 2: der Rohertrag aus Online-Verkäufen

1. Umsatz pro Monat: 100 Bestellungen x 40 = **4.000**.
2. Umsatz pro Jahr: 4.000 x 12 = **48.000**.
3. Rohertrag pro Jahr: 48.000 x 30 % = **14.400**.
4. Zahlungsgebühren pro Jahr: 48.000 x 3 % = **1.440**.
5. Rohertrag nach Zahlungsgebühren: 14.400 - 1.440 = **12.960**.

## Schritt 3: ROI und Gewinnschwelle

Erstes Jahr:

1. Nettonutzen: 12.960 - 9.200 = **3.760**.
2. ROI: 3.760 / 9.200 x 100 % = etwa **41 %**.

Fünf Jahre:

1. Rohertrag nach Gebühren: 12.960 x 5 = **64.800**.
2. Kosten: **14.000**.
3. Nettonutzen: 64.800 - 14.000 = **50.800**.
4. ROI: 50.800 / 14.000 x 100 % = etwa **363 %**.

Gewinnschwelle für die Einrichtung: Der monatliche Rohertrag nach Gebühren beträgt 12.960 / 12 = 1.080; die monatlichen
laufenden Kosten betragen 1.200 / 12 = 100; jeder Monat trägt also 1.080 - 100 = 980 zur Einrichtung bei.
8.000 / 980 = etwa **8,2 Monate**.

> **Warnung**
> Das setzt voraus, dass die Online-Bestellungen neues Geschäft sind. Wenn einige ohnehin per Telefon oder im Laden
> gekommen wären, zählen Sie nur die zusätzlichen Bestellungen. Das ist der häufigste Fehler in einem Business Case
> für einen Shop.

## Schritt 4: was ein Ausfall kosten würde

1. Beispielannahme: Der Shop ist an einem geschäftigen Tag 4 Stunden offline.
2. Rohertrag nach Gebühren pro Stunde, über das Jahr gemittelt: 12.960 / (365 x 24) = etwa **1,5** Währungseinheiten.
3. An einem geschäftigen Tag ballen sich die Bestellungen; Beispielannahme: geschäftige Stunden bringen das Zehnfache
   des Durchschnitts: etwa **15** pro Stunde.
4. Entgangener Rohertrag: 4 x 15 = **60**, zuzüglich der Arbeitszeit, um Kunden zu antworten, und der Bestellungen,
   die nie wiederkommen.

Für einen kleinen Shop ist der direkte Verlust eines kurzen Ausfalls gering; das verlorene Vertrauen ist es nicht. Das
[Arbeitsblatt: Kosten eines Ausfalls](arbeitsblatt-kosten-eines-ausfalls.md) fügt die anderen Posten hinzu, und
[Shop-Leistung zu Spitzenzeiten](../shops/shop-leistung-zu-spitzenzeiten.md) erklärt, wie Sie an den geschäftigen
Tagen erreichbar bleiben.

## Was Sie für Ihren Shop ändern

- [ ] Ersetzen Sie jede Beispielannahme durch Ihre Zahlen und schreiben Sie die Quelle daneben.
- [ ] Fügen Sie Posten für Produktfotografie, Texte und Versandmaterial hinzu, wenn Sie diese einkaufen.
- [ ] Fügen Sie die echte Gebührenstruktur Ihres Zahlungsanbieters aus seinem Vertrag hinzu.
- [ ] Zählen Sie nur die Bestellungen, die der Shop hinzufügt, nicht die, die er aus einem anderen Kanal verlagert.
- [ ] Prüfen Sie die USt-Pflichten für die Länder, in die Sie verkaufen
      ([USt-Regeln und USt-Typen](../shops/ust-regeln-und-ust-typen.md)).
- [ ] Planen Sie den Start mit der [Checkliste für den Shop-Start](../shops/checkliste-fuer-den-shop-start.md).

## Verwandte Seiten

- [Shops](../shops/README.md)
- [Shop-Planung für kleine Unternehmen](../shops/shop-planung-fuer-kleine-unternehmen.md)
- [Gesamtbetriebskosten](gesamtbetriebskosten.md)
- [Vorlage ROI-Arbeitsblatt](../vorlagen/vorlage-roi-arbeitsblatt.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
