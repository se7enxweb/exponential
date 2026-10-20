---
title: Den ROI messen
audience: customer, businesses, project-manager
category: customer-success
order: 10
summary: Wie ein Betreiber den Ertrag einer Website misst, die mit Exponential läuft - wenige aussagekräftige Kennzahlen wählen, woher jede Zahl in der Software und außerhalb kommt, wie oft sie erhoben wird, und die Fallen, die ROI-Zahlen falsch machen.
keywords: ROI, Return on Investment, Messen, Kennzahlen, Zahlen, Shop-Dashboard, Formulareinsendungen, Newsletter, Suchbegriffe, Arbeitszeit, Kosten, Nutzen, return on investment, measuring, KPIs, figures, store dashboard, form submissions, newsletter, search phrases, staff time, costs, benefits
type: help
---
# Den ROI messen

Diese Seite ist für Betreiber und Leitungen, die nach dem Start sagen müssen, ob die Website ihre Kosten wert ist. Der
Return on Investment vergleicht, was die Website einbringt, mit dem, was sie kostet. Die Formel ist einfach; schwierig
sind ehrliche Zahlen. Diese Seite sagt Ihnen, welche Zahlen Sie erheben und woher sie kommen. Die Berechnung selbst steht
unter [ROI-Beispielannahmen](roi-beispielannahmen.md) und im [ROI-Arbeitsblatt](roi-arbeitsblatt.md).

## Die Formel

ROI = (Nutzen - Kosten) / Kosten x 100 %, über einen Zeitraum, den Sie wählen, bei einer laufenden Website meist ein
Jahr. Dieselbe Formel wird im Bereich für Unternehmen verwendet ([Vorlage für ein ROI-Arbeitsblatt](../../../../en/us/help/businesses/templates/roi-worksheet-template.md)).

## Wenige Kennzahlen wählen

Wählen Sie drei bis fünf Kennzahlen, die dazu passen, warum Ihre Organisation die Website hat. Alles zu messen führt zu
Berichten, die niemand liest.

| Warum Sie die Website haben | Passende Kennzahlen |
|---|---|
| Um zu verkaufen | Bestellungen, Umsatz, durchschnittliche Bestellung, wiederkehrende Kunden |
| Um Anfragen zu bekommen | Formulareinsendungen pro Monat, und wie viele zu Geschäft wurden |
| Um Mitglieder oder die Öffentlichkeit zu informieren | Newsletter-Abonnenten, Fragen, die die Website statt des Telefons beantwortet hat |
| Um zu veröffentlichen (Nachrichten, Magazin, Dokumentation) | Veröffentlichte Beiträge, Zeit vom Entwurf bis zur Veröffentlichung, Leser |
| Um zusammenzuarbeiten (Intranet) | Arbeitszeit, die beim Finden und Teilen von Informationen gespart wird |

## Woher die Zahlen kommen

| Zahl | Wo in Exponential | Quelle |
|---|---|---|
| Bestellungen, Umsatz, Kunden, abgebrochene Warenkörbe | Shop-Dashboard; Produkt-Statistiken pro Monat | [Shop-Dashboard und Bestellstatus](../../../../en/us/features/6.0/store-dashboard.md), [Shop-Berichte](../handel/shop-berichte.md) |
| Formulareinsendungen | **Setup > Gesammelte Informationen**, mit CSV-Export | [Gesammelte Informationen](../../../../en/us/guides/collected-information.md) |
| Newsletter-Abonnenten | Die Administration von `cjw_newsletter` | [cjw_newsletter](../../../../en/us/features/6.0/extensions/cjw_newsletter.md) |
| Wonach Besucher suchen | Suchbegriffe werden gespeichert (`ezsearch_search_phrase`) | [Personenbezogene Daten in Exponential](../../../../en/us/help/businesses/security/personal-data-in-exponential.md) |
| Veröffentlichte Inhalte | Versionen und Veröffentlichungshistorie der Inhalte | [Veröffentlichen und Versionen](../../../../en/us/help/publishing-and-versions.md) |
| Kosten eines KI-Anbieters, falls genutzt | Das Dashboard von Exponential Intelligence zeigt Kosten, Tokens und Anfragen | `README.md`, „Exponential Intelligence“ |
| Besucherzahlen | Nicht als Bericht eingebaut; ein Statistikwerkzeug | [Besucherstatistik und Analyse](../../../../en/us/help/creators/website-creators/statistics-and-analytics.md) |

Zahlen von außerhalb der Website: Ihre Buchhaltung (tatsächliche Einnahmen, Gebühren, Erstattungen), die Rechnungen
Ihres Hosters und Partners, die Zeiterfassung Ihrer Mitarbeitenden und Ihre Telefon- oder E-Mail-Statistik zu Fragen,
die nun die Website beantwortet.

## Wie oft

| Kennzahl | Erheben | Auswerten |
|---|---|---|
| Shop-Zahlen | Monatlich, aus den Produkt-Statistiken | Vierteljährlich |
| Formulareinsendungen, Abonnenten | Monatlich | Vierteljährlich |
| Kosten | Mit jeder Rechnung | Jährlich, in der [jährlichen Überprüfung des Nutzens](jaehrliche-ueberpruefung-des-nutzens.md) |
| Gesparte Arbeitszeit | Einmal im Jahr, per kurzer Umfrage oder Zeitstichprobe | Jährlich |

## Fallen

- **Zählen, was sich nur verlagert hat.** Eine online aufgegebene Bestellung, die ohnehin telefonisch gekommen wäre, ist
  keine neue Einnahme. Zählen Sie nur, was die Website hinzufügt, oder sagen Sie klar, dass Sie es nicht trennen können.
- **Bestellwert ist kein Ertrag.** Das Shop-Dashboard zeigt den Bestellwert; Gebühren, Erstattungen und Mehrwertsteuer
  stehen in Ihrer Buchhaltung ([Shop-Berichte](../handel/shop-berichte.md)).
- **Gesparte Zeit ist kein gespartes Geld**, es sei denn, die Zeit wird für etwas anderes genutzt. Verwenden Sie einen
  Realisierungsfaktor und sagen Sie, welchen ([Vorlage für ein ROI-Arbeitsblatt](../../../../en/us/help/businesses/templates/roi-worksheet-template.md)).
- **Fehlende Kosten.** Die Arbeitszeit für Inhalte ist meist der größte Kostenposten und der, der am häufigsten
  weggelassen wird ([Gesamtbetriebskosten](../../../../en/us/help/businesses/costs/total-cost-of-ownership.md)).
- **Testdaten.** Testbestellungen und Testeinsendungen blähen die Zahlen auf; entfernen Sie sie.

## Zusammenfassen

Eine Seite genügt: die gewählten Kennzahlen, die Zahlen mit ihrer Quelle, die Kosten, das Ergebnis und was Sie ändern
werden. Bringen Sie sie zur [jährlichen Überprüfung des Nutzens](jaehrliche-ueberpruefung-des-nutzens.md) mit.

## Verwandte Seiten

- [ROI-Beispielannahmen](roi-beispielannahmen.md)
- [ROI-Arbeitsblatt](roi-arbeitsblatt.md)
- [Return on Investment von Stabilität](../../../../en/us/help/businesses/costs/roi-of-stability.md)
- [Betriebskosten für Betreiber](betriebskosten-fuer-betreiber.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
