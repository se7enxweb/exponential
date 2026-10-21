---
title: ROI-Beispielannahmen
audience: customer, businesses, project-manager
category: customer-success
order: 20
summary: Eine durchgerechnete ROI-Kalkulation über ein Jahr für einen laufenden Shop mit Exponential, bei der jede Eingabe klar als Beispielannahme gekennzeichnet ist, die Rechnung Schritt für Schritt, eine Sensitivitätstabelle und wie Sie jede Zahl durch Ihre eigene ersetzen.
keywords: ROI-Beispiel, Beispielannahmen, durchgerechnetes Beispiel, ROI-Berechnung, Sensitivität, Shop-ROI, ein Jahr, Kosten, Nutzen, Zahlungsgebühren, Arbeitszeit, ROI example, example assumptions, worked example, ROI calculation, sensitivity, store ROI, one year, costs, benefits, payment fees, staff time
type: help
---
# ROI-Beispielannahmen

Diese Seite zeigt die ROI-Rechnung aus [Den ROI messen](den-roi-messen.md) an einem durchgerechneten Beispiel, einem
kleinen Shop in seinem zweiten Jahr. Sie existiert, damit Sie sehen, wie die Teile zusammenpassen, und dann jede Zahl im
[ROI-Arbeitsblatt](roi-arbeitsblatt.md) durch Ihre eigene ersetzen.

> **Warnung**
> **Beispielannahmen: Ersetzen Sie sie durch Ihre eigenen.** Jeder Betrag auf dieser Seite ist eine erfundene, runde
> Zahl in einer neutralen „Währungseinheit“. Keiner ist ein Preis, ein Angebot, ein Marktpreis oder eine Messung einer
> echten Website. Die einzigen Fakten sind die mit einer Quelle, etwa die Lizenzgebühr von null.

## Das Beispiel

Ein kleiner Shop, der seit einem Jahr mit Exponential läuft, mit dem eingebauten Shop, PayPal-Zahlungen und einem
Newsletter. Die Frage für sein zweites Jahr: Trägt sich der Onlineshop selbst?

## Beispielannahmen: Ersetzen Sie sie durch Ihre eigenen

| Eingabe | Beispielwert | Woher Ihre eigene Zahl kommt |
|---|---|---|
| Lizenzgebühr für Exponential | 0 (Fakt) | `README.md`, „License“: kostenlos unter der GNU GPL |
| Hosting | 100 Währungseinheiten pro Monat | Die Rechnungen Ihres Hosters |
| Support durch einen Partner | 1.500 pro Jahr | Ihr Vertrag |
| Ein geplanter Update- und Wartungsauftrag | 2.000 pro Jahr | Das Angebot Ihres Partners ([Betriebskosten für Betreiber](betriebskosten-fuer-betreiber.md)) |
| Arbeitszeit für Produkte und Bestellungen | 5 Stunden pro Woche, 48 Wochen, zu 30 pro Stunde | Ihre Zeiterfassung |
| Online-Umsatz (Bestellwert) | 60.000 pro Jahr | Shop-Dashboard und Produkt-Statistiken ([Shop-Berichte](../handel/shop-berichte.md)) |
| Gebühren des Zahlungsanbieters | 3 % des Online-Umsatzes | Die Abrechnungen Ihres Anbieters |
| Anteil des Online-Umsatzes, der neu ist (sonst nicht gekommen wäre) | 50 % | Ihre eigene Einschätzung, mit Begründung aufgeschrieben |
| Bruttomarge auf Verkäufe | 40 % | Ihre Buchhaltung |
| Gesparte Arbeitszeit bei Bestellungen per Telefon und E-Mail | 3 Stunden pro Woche, 48 Wochen, zu 30 pro Stunde | Ihre Zeiterfassung |
| Realisierung dieser gesparten Zeit | 50 % | Ihre eigene Einschätzung |

## Schritt 1: die Kosten für das Jahr

1. Hosting: 100 x 12 = **1.200**.
2. Support: **1.500**.
3. Update und Wartung: **2.000**.
4. Arbeitszeit: 5 x 48 x 30 = **7.200**.
5. Zahlungsgebühren: 3 % von 60.000 = **1.800**.
6. Lizenz: **0**.
7. Gesamtkosten: 1.200 + 1.500 + 2.000 + 7.200 + 1.800 + 0 = **13.700**.

## Schritt 2: der Nutzen für das Jahr

1. Neuer Umsatz: 50 % von 60.000 = 30.000.
2. Bruttogewinn aus neuem Umsatz: 40 % von 30.000 = **12.000**.
3. Gesparte Arbeitszeit: 3 x 48 x 30 = 4.320; realisiert zu 50 % = **2.160**.
4. Gesamtnutzen: 12.000 + 2.160 = **14.160**.

## Schritt 3: das Ergebnis

1. Nettonutzen: 14.160 - 13.700 = **460**.
2. ROI: 460 / 13.700 x 100 % = etwa **3,4 %**.

In diesem Beispiel trägt sich der Shop gerade so selbst. Das Ergebnis hängt am stärksten von einer Einschätzung ab:
wie viel des Online-Umsatzes wirklich neu ist.

## Sensitivität: was das Ergebnis bewegt

Ändern Sie jeweils eine Eingabe und rechnen Sie neu. Alle Werte sind Beispielannahmen.

| Änderung | Nutzen | Kosten | Netto | ROI |
|---|---|---|---|---|
| Wie oben | 14.160 | 13.700 | 460 | etwa 3,4 % |
| Neuer Anteil 70 % statt 50 % (Bruttogewinn 40 % von 42.000 = 16.800) | 18.960 | 13.700 | 5.260 | etwa 38,4 % |
| Arbeitszeit 3 Stunden pro Woche statt 5 (Kosten 4.320) | 14.160 | 10.820 | 3.340 | etwa 30,9 % |
| Kein geplantes Update in diesem Jahr (Kosten 0 statt 2.000) | 14.160 | 11.700 | 2.460 | etwa 21,0 % |

Rechnung für die Zeilen: 16.800 + 2.160 = 18.960 und 18.960 - 13.700 = 5.260, 5.260 / 13.700 = 0,384;
13.700 - 7.200 + 4.320 = 10.820 und 14.160 - 10.820 = 3.340, 3.340 / 10.820 = 0,309;
13.700 - 2.000 = 11.700 und 14.160 - 11.700 = 2.460, 2.460 / 11.700 = 0,210.

> **Tipp**
> Das Update wegzulassen, um die Zahl zu verbessern, ist eine falsche Ersparnis: Eine nicht gepflegte Website kostet
> später mehr. Behalten Sie eine jährliche Zeile für Updates, wie [Budget für Upgrades und Wartung](../../unternehmen/kosten/budget-fuer-aktualisierungen-und-wartung.md)
> sie in seinem Wartungskalender einplant.

## Was dieses Beispiel weglässt

Dinge, die real sind, sich aber schwer beziffern lassen und neben den Zahlen in Worten beschrieben werden:

- der Wert von Kunden, die wiederkommen, und der Newsletter-Liste;
- weniger Risiko durch den Betrieb auf einer gepflegten Release-Linie (`SECURITY.md`);
- die Kosten eines vermiedenen Ausfalls ([Arbeitsblatt zu den Kosten von Ausfällen](../../unternehmen/kosten/arbeitsblatt-kosten-eines-ausfalls.md)).

## Die Zahlen ersetzen

1. Kopieren Sie das [ROI-Arbeitsblatt](roi-arbeitsblatt.md).
2. Füllen Sie jede Zeile aus der Quelle in der dritten Spalte der Annahmentabelle, nie von dieser Seite.
3. Schreiben Sie neben jede Einschätzung die Begründung (neuer Anteil, Realisierung).
4. Bitten Sie jemanden außerhalb des Projekts, die Rechnung nachzurechnen.

## Verwandte Seiten

- [Den ROI messen](den-roi-messen.md)
- [ROI-Arbeitsblatt](roi-arbeitsblatt.md)
- [Durchgerechnetes Beispiel: ein kleiner Shop](../../unternehmen/kosten/durchgerechnetes-beispiel-kleiner-shop.md)
- [Return on Investment von Stabilität](../../unternehmen/kosten/rendite-der-stabilitaet.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
