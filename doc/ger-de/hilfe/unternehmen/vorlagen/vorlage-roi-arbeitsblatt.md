---
title: "Vorlage: ROI-Arbeitsblatt"
audience: businesses, project-manager, sales-marketing
category: business-solutions
order: 80
summary: Ein kopierfertiges Arbeitsblatt für den Business Case einer Website-Anwendung mit Exponential - einmalige und laufende Kosten, Nutzen aus Umsatz, eingesparter Zeit und vermiedenen Ausfällen, Nettonutzen, ROI und Amortisation - mit den Formeln, einer Quellenspalte für jede Zahl und einer kurzen ausgefüllten Zeile, die die Rechnung zeigt.
keywords: ROI-Arbeitsblatt, Rendite, Kapitalrendite, Business Case, Gesamtbetriebskosten, Amortisation, Nutzen, Kosten, Vorlage, Tabelle, Formeln, ROI worksheet, return on investment, business case, total cost of ownership, payback, benefits, costs, template, spreadsheet, formulas
type: help
---
# Vorlage: ROI-Arbeitsblatt

Diese Seite ist für die Person, die den Business Case für eine Website mit Exponential vorbereitet. Das Arbeitsblatt
unten sammelt jede Kosten- und Nutzenposition mit ihrer Quelle und berechnet dann den Nettonutzen, die Kapitalrendite
(ROI) und die Amortisationsdauer. Kopieren Sie es in eine Tabellenkalkulation. Die Methode wird unter
[Rendite der Stabilität](../kosten/rendite-der-stabilitaet.md) erklärt, die Kostenpositionen unter
[Gesamtbetriebskosten](../kosten/gesamtbetriebskosten.md).

> **Hinweis**
> Das Arbeitsblatt enthält keine Zahlen. Die eine ausgefüllte Zeile im Beispiel verwendet **Beispielannahmen** in einer
> neutralen Währungseinheit, nur um die Rechnung zu zeigen. Verwenden Sie Ihre eigenen Zahlen, aus Angeboten,
> Rechnungen, Lohnabrechnung und Ihren eigenen Systemen, und schreiben Sie die Quelle neben jede Zahl.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Das Arbeitsblatt](#das-arbeitsblatt)
3. [Formeln](#formeln)
4. [Die Rechnung zeigen](#die-rechnung-zeigen)
5. [Das Ergebnis prüfen](#das-ergebnis-prüfen)

## Bevor Sie beginnen

- [ ] Wählen Sie den Zeitraum (zum Beispiel 5 Jahre) und verwenden Sie ihn auf beiden Seiten.
- [ ] Holen Sie schriftliche Angebote für Umsetzung, Hosting und Support ein
      ([Fragen an einen Partner](../auswahl/fragen-an-einen-partner.md#kosten)).
- [ ] Füllen Sie das [Arbeitsblatt: Kosten eines Ausfalls](../kosten/arbeitsblatt-kosten-eines-ausfalls.md#das-leere-arbeitsblatt)
      aus, wenn die Verfügbarkeit Teil des Business Case ist.
- [ ] Denken Sie an die Positionen, die null sind, mit ihrer Quelle: die Lizenzgebühr
      ([Wofür Sie nicht bezahlen](../kosten/wofuer-sie-nicht-bezahlen.md)).

## Das Arbeitsblatt

```text
ROI-ARBEITSBLATT - <Projekt>   Zeitraum: <n> Jahre   Erstellt von: <Name>   Datum: <Datum>
Währungseinheit: <Einheit>

A. EINMALIGE KOSTEN                                Betrag     Quelle
A1 Umsetzung (Design, Entwicklung, Migration)      ______     ______
A2 Schulung                                        ______     ______
A3 Vorbereitung der Inhalte                        ______     ______
A4 Sonstiges einmalig: ______                      ______     ______
A  Summe einmalig                                  ______

B. LAUFENDE KOSTEN PRO JAHR
B1 Lizenzgebühr für Exponential                    0          README „License“
B2 Hosting                                         ______     ______
B3 Betrieb und Updates (Stunden x Satz)            ______     ______
B4 Supportvertrag                                  ______     ______
B5 Dienste Dritter (Zahlung, Mail, KI ...)         ______     ______
B6 Reserve für Überraschungen (% von B2-B5)        ______     ______
B  Summe laufend pro Jahr                          ______

C. NUTZEN PRO JAHR
C1 Rohertrag aus neuen Online-Verkäufen (nach Gebühren) ______ ______
C2 Eingesparte Arbeitszeit (Stunden x Kosten x Realisierungs-%) ___ ______
C3 Vermiedene Ausfälle (Stunden x Kosten pro Stunde) ______    ______
C4 Entfallene Kosten (altes System, Lizenzen)      ______     ______
C5 Sonstiger messbarer Nutzen: ______              ______     ______
C  Summe Nutzen pro Jahr                           ______

D. ERGEBNISSE
D1 Gesamtkosten im Zeitraum    = A + B x n                    ______
D2 Gesamtnutzen im Zeitraum    = C x n                        ______
D3 Nettonutzen                 = D2 - D1                      ______
D4 ROI                         = D3 / D1 x 100 %              ______
D5 Monatlicher laufender Nettonutzen = (C - B) / 12           ______
D6 Amortisation (Monate)       = A / D5                       ______

E. NICHT IN DEN ZAHLEN (in Worten beschreiben)
E1 ________________________________________________
E2 ________________________________________________

Geprüft von: __________   Freigegeben von: __________   Erneut prüfen nach einem Jahr: <Datum>
```

## Formeln

| Ergebnis | Formel | Hinweis |
|---|---|---|
| Gesamtkosten im Zeitraum | A + B x n | n = Jahre |
| Gesamtnutzen | C x n | lassen Sie C über die Jahre nur wachsen, wenn Sie es begründen können |
| Nettonutzen | Gesamtnutzen - Gesamtkosten | |
| ROI | Nettonutzen / Gesamtkosten x 100 % | |
| Amortisation | einmalige Kosten / monatlicher laufender Nettonutzen | nur sinnvoll, wenn C größer als B ist |

Setzen Sie in einer Tabellenkalkulation jede Zeile in eine eigene Zelle und verweisen Sie in den Formeln auf Zellen,
damit eine Änderung an einer Eingabe das Ergebnis aktualisiert.

## Die Rechnung zeigen

Eine einzeilige Veranschaulichung mit **Beispielannahmen**: A = 10.000; B = 2.000 pro Jahr; C = 6.000 pro Jahr; n = 5.

1. D1 = 10.000 + 2.000 x 5 = 10.000 + 10.000 = **20.000**.
2. D2 = 6.000 x 5 = **30.000**.
3. D3 = 30.000 - 20.000 = **10.000**.
4. D4 = 10.000 / 20.000 x 100 % = **50 %**.
5. D5 = (6.000 - 2.000) / 12 = etwa **333** pro Monat.
6. D6 = 10.000 / 333 = etwa **30 Monate**.

Vollständige durchgerechnete Beispiele: [kleiner Shop](../kosten/durchgerechnetes-beispiel-kleiner-shop.md),
[Intranet](../kosten/durchgerechnetes-beispiel-intranet.md), [gemeinnützige Organisation](../kosten/durchgerechnetes-beispiel-gemeinnuetzige-organisation.md).

## Das Ergebnis prüfen

- [ ] Jede Zahl hat eine Quelle; keine Zeile ist eine Schätzung, die als Tatsache ausgegeben wird.
- [ ] Der Nutzen zählt nur, was die Website hinzufügt, nicht was aus einem anderen Kanal verlagert wird.
- [ ] Die eingesparte Arbeitszeit hat einen Realisierungsfaktor, den Sie vertreten können.
- [ ] Vergleiche mit anderen Produkten beruhen auf schriftlichen Angeboten oder bleiben qualitativ
      ([Exponential sachlich vergleichen](../../geschaeft/exponential-sachlich-vergleichen.md)).
- [ ] Jemand außerhalb des Projekts hat die Rechnung nachvollzogen.
- [ ] Ein Termin ist festgelegt, um die Berechnung mit gemessenen Zahlen zu wiederholen ([Checkliste: Jahresrückblick](checkliste-jahresrueckblick.md)).

## Verwandte Seiten

- [Rendite der Stabilität](../kosten/rendite-der-stabilitaet.md)
- [Gesamtbetriebskosten](../kosten/gesamtbetriebskosten.md)
- [Kennzahlen aus Logs und Audit](../../fuehrung/kennzahlen-aus-logs-und-audit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
