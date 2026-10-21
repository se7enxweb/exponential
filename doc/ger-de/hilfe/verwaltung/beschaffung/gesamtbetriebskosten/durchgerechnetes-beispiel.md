---
title: Ein durchgerechnetes TCO-Beispiel mit gekennzeichneten Annahmen
audience: government, project-manager
category: public-sector
order: 110
summary: Eine durchgerechnete Berechnung der Gesamtbetriebskosten für die Website einer erfundenen öffentlichen Stelle auf Exponential, in neutralen Währungseinheiten, in der jede Zahl eine Beispielannahme ist - um die Rechnung und den Aufbau zu zeigen, nie um Ihre Kosten vorherzusagen.
keywords: durchgerechnetes Beispiel, TCO-Beispiel, Kostenberechnung, Beispielannahme, Wirtschaftlichkeitsbetrachtung, Budgetmodell, Kosten über fünf Jahre, Vergleich, worked example, TCO example, cost calculation, example assumption, business case, budget model, five-year cost, comparison
type: help
---
# Ein durchgerechnetes TCO-Beispiel mit gekennzeichneten Annahmen

Diese Seite richtet sich an Projektleitungen, die ein ausgefülltes Modell der Gesamtbetriebskosten sehen möchten, bevor
sie ihr eigenes ausfüllen. Die Organisation, die Zahlen und die Währung sind erfunden. **Jede Zahl auf dieser Seite ist
eine Beispielannahme**, nur gewählt, damit sich die Rechnung leicht nachvollziehen lässt. Keine davon ist ein Preis, eine
Schätzung oder ein Richtwert für Exponential, für Hosting oder für einen Lieferanten. Ersetzen Sie jede durch Ihre
eigenen Angebote und Aufzeichnungen.

## Inhalt
1. [Das erfundene Szenario](#das-erfundene-szenario)
2. [Annahmen](#annahmen)
3. [Einmalige Kosten](#einmalige-kosten)
4. [Wiederkehrende Kosten pro Jahr](#wiederkehrende-kosten-pro-jahr)
5. [Die Summe](#die-summe)
6. [Was das Beispiel lehrt](#was-das-beispiel-lehrt)

## Das erfundene Szenario

Die Behörde für Beispiele betreibt eine öffentliche Website in zwei Sprachen mit einem kleinen Redaktionsteam, das über
die Abteilungen verteilt ist. Sie plant mit einem Zeithorizont von fünf Jahren, hostet bei einem externen Anbieter und
kauft Umsetzung und Support bei einem Lieferanten ein. Die Beträge sind in „CU“ angegeben, einer erfundenen
Währungseinheit.

## Annahmen

| # | Annahme | Wert |
|---|---|---|
| A1 | Zeithorizont | 5 Jahre (Beispielannahme) |
| A2 | Interner Stundensatz, alle Rollen | 50 CU (Beispielannahme) |
| A3 | Tagessatz des Lieferanten | 800 CU (Beispielannahme) |
| A4 | Lizenzgebühr des Kernels | 0 CU - dies ist ein Fakt: GPL, keine Lizenzgebühr ([Lizenzkosten](lizenzkosten.md)) |
| A5 | Aufwand für die Umsetzung | 60 Tage des Lieferanten (Beispielannahme) |
| A6 | Aufwand für die Migration der Inhalte | 15 Tage des Lieferanten (Beispielannahme) |
| A7 | Audit der Barrierefreiheit der gelieferten Website | 6 Tage des Lieferanten (Beispielannahme) |
| A8 | Penetrationstest vor dem Start | 5 Tage des Lieferanten (Beispielannahme) |
| A9 | Hosting, Produktion und Staging | 3.000 CU pro Jahr (Beispielannahme) |
| A10 | Supportvertrag | 6.000 CU pro Jahr (Beispielannahme) |
| A11 | Releases und Updates einspielen | 8 Tage des Lieferanten pro Jahr (Beispielannahme) |
| A12 | Redaktion | 12 Personen, je 4 zusätzliche Stunden pro Monat (Beispielannahme) |
| A13 | Administration der Website | 1 Person, 16 Stunden pro Monat (Beispielannahme) |
| A14 | Erste Schulung | 2 Termine Editors 101 mit je 3 Trainertagen, dazu 1 Kurs zur Administration der Website mit 5 Trainertagen (Beispielannahme für die Zahl der Termine; Kurslängen aus dem [Kurskatalog](../../../../../eng-us/help/trainers/course-catalogue.md)) |
| A15 | Einarbeitung neuer Beschäftigter | 2 Trainertage pro Jahr (Beispielannahme) |
| A16 | Ausstieg am Ende des Zeithorizonts | 10 Tage des Lieferanten (Beispielannahme) |
| A17 | Risikoreserve | 10 % aller übrigen Kosten (Beispielannahme) |

## Einmalige Kosten

| Zeile | Rechnung | CU |
|---|---|---|
| Umsetzung | A5 60 Tage x A3 800 | 48.000 |
| Migration | A6 15 x 800 | 12.000 |
| Audit der Barrierefreiheit | A7 6 x 800 | 4.800 |
| Penetrationstest | A8 5 x 800 | 4.000 |
| Erste Schulung (Trainer) | A14 (3 + 3 + 5) = 11 Tage x 800 | 8.800 |
| Erste Schulung (Teilnehmende) | 12 Redaktionsmitglieder x 3 Tage x 8 h x A2 50 + 1 Admin x 5 Tage x 8 h x 50 | 16.400 |
| Ausstieg | A16 10 x 800 | 8.000 |
| **Summe einmalig** | | **102.000** |

## Wiederkehrende Kosten pro Jahr

| Zeile | Rechnung | CU pro Jahr |
|---|---|---|
| Lizenz | A4 | 0 |
| Hosting | A9 | 3.000 |
| Supportvertrag | A10 | 6.000 |
| Updates | A11 8 x 800 | 6.400 |
| Zusätzliche Zeit der Redaktion | A12 12 x 4 h x 12 Monate x 50 | 28.800 |
| Administration der Website | A13 16 h x 12 x 50 | 9.600 |
| Einarbeitung | A15 2 x 800 | 1.600 |
| **Wiederkehrend pro Jahr** | | **55.400** |

## Die Summe

```text
One-off                      102,000 CU
Recurring  55,400 x 5 years  277,000 CU
Subtotal                     379,000 CU
Risk allowance (A17, 10 %)    37,900 CU
TCO over 5 years             416,900 CU     (all inputs are example assumptions)
```

## Was das Beispiel lehrt

Diese Lehren ergeben sich aus dem Aufbau, nicht aus den erfundenen Zahlen:

- **Die Lizenzzeile ist null, und sie spielt kaum eine Rolle.** In diesem Aufbau dominieren Personalzeit und
  Leistungen. Ein Vergleich der Angebote allein nach dem Lizenzpreis würde fast alles übersehen.
- **Interne Personalzeit ist eine große Zeile**, obwohl sie nie in einem Angebot erscheint. Zählen Sie sie ([Personal und Kompetenzen](personal-und-kompetenzen.md)).
- **Der Ausstieg ist eine einmalige Zeile**, die von Anfang an ins Modell gehört ([Ausstiegspläne](../ausstiegsplaene/README.md)).
- **Updates wiederholen sich jedes Jahr**; ein Modell, das nach dem Start aufhört, unterschätzt die Kosten
  ([Kosten für Upgrades und Wartung](kosten-fuer-upgrades-und-wartung.md)).
- **Sensitivität**: Ändern Sie eine Annahme nach der anderen (zum Beispiel A12) und sehen Sie, wie stark sich die Summe
  bewegt. Die Annahmen, die sie am stärksten bewegen, verdienen die besten Nachweise.

## Verwandte Seiten
- [Gesamtbetriebskosten: Übersicht](README.md)
- [Vorlage für ein TCO-Arbeitsblatt](vorlage-fuer-ein-tco-arbeitsblatt.md)
- [Der Aufbau eines TCO-Modells](aufbau-des-tco-modells.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
