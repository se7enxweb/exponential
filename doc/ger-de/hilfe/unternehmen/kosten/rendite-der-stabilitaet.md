---
title: Rendite der Stabilität
audience: businesses, project-manager, admin
category: business-solutions
order: 30
summary: Wie Sie den Wert einer stabilen Plattform als Rendite ausdrücken - vermiedene Ausfälle, weniger Notfallkorrekturen, berechenbare Aktualisierungen - mit den Fakten über Exponential, die jeden Nutzen stützen, und einer durchgerechneten Berechnung, die nur aus Beispielannahmen besteht.
keywords: Rendite, ROI, Stabilität, Ausfallzeit, Amortisation, Nutzen, Wartung, Aktualisierungen, Release-Linie, veröffentlichte Versionen, Business Case, return on investment, stability, downtime, payback, benefits, maintenance, upgrades, release line, published versions, business case
type: help
---
# Rendite der Stabilität

Diese Seite ist für Führungskräfte, die Ausgaben für eine Website-Plattform begründen müssen, und für die Personen, die
den Business Case schreiben. Sie erklärt, wie Stabilität zu Geld wird, das Sie behalten, welche Fakten über
Exponential jeden Nutzen stützen und wie Sie eine Rendite (ROI) berechnen, die eine Finanzabteilung prüfen kann.

> **Hinweis**
> Die Berechnung unten verwendet nur **Beispielannahmen**: runde, veranschaulichende Zahlen in einer neutralen
> Währungseinheit. Es sind keine Messungen einer echten Website. Ersetzen Sie sie durch Ihre eigenen Zahlen, und halten
> Sie die Quelle jeder Zahl fest.

## Inhalt

1. [Die Formel](#die-formel)
2. [Wo Stabilität Geld spart](#wo-stabilität-geld-spart)
3. [Die Fakten hinter jedem Nutzen](#die-fakten-hinter-jedem-nutzen)
4. [Ein durchgerechnetes Beispiel](#ein-durchgerechnetes-beispiel)
5. [Amortisationsdauer](#amortisationsdauer)
6. [Den Business Case glaubwürdig machen](#den-business-case-glaubwürdig-machen)

## Die Formel

Die Rendite vergleicht, was eine Investition bringt, mit dem, was sie kostet, über denselben Zeitraum:

```text
ROI = (benefits - costs) / costs x 100 %
Payback period (months) = one-time cost / monthly net benefit
```

Nutzen ist gewonnenes oder nicht verlorenes Geld. Kosten sind, was Sie ausgeben, um ihn zu erreichen. Verwenden Sie auf
beiden Seiten denselben Zeitraum (meist ein Jahr oder die fünf Jahre Ihrer
[Gesamtbetriebskosten](gesamtbetriebskosten.md)).

## Wo Stabilität Geld spart

| Nutzen | Wie er sich im Budget zeigt |
|---|---|
| Weniger Ausfälle | Nicht verlorene Verkäufe, Buchungen, Spenden oder Arbeitsstunden ([Arbeitsblatt: Kosten eines Ausfalls](arbeitsblatt-kosten-eines-ausfalls.md)) |
| Weniger Notfallkorrekturen | Weniger ungeplante Stunden von Entwicklern und Administratoren, oft zu höheren Sätzen |
| Berechenbare Aktualisierungen | Aktualisierungen in der normalen Arbeitszeit geplant, keine überhasteten Projekte, wenn eine Plattform eingestellt wird |
| Weniger Nacharbeit nach Aktualisierungen | Templates und Erweiterungen funktionieren über die Releases einer Linie hinweg weiter |
| Schnellere Wiederherstellung | Getestete Wiederherstellungen und klare Logs verkürzen jeden Vorfall |
| Geringeres Risiko von Datenverlust | Weniger Kosten für Störungsbehandlung und Benachrichtigungen |

## Die Fakten hinter jedem Nutzen

| Nutzen | Fakt über Exponential | Quelle |
|---|---|---|
| Berechenbare Aktualisierungen | 6.0 ist die gepflegte Linie; Sicherheitskorrekturen werden „published with the next 6.0.x release“ (mit dem nächsten Release 6.0.x veröffentlicht) | `SECURITY.md` |
| Keine Überraschungen in einer Version | „A published tag is never changed“ (ein veröffentlichter Tag wird nie geändert) | `SECURITY.md` |
| Langes Leben | „Exponential has made it beyond it's end of life in 2021 and survived“ (Exponential hat sein Lebensende 2021 überdauert und überlebt) | `README.md`, „Exponential Project Status“ |
| Aktuelles PHP | PHP 8.0 bis 8.5 werden unterstützt; die Testsuite läuft bei jedem Push auf PHP 8.1 bis 8.5 | `README.md`, [Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md#die-zusage-zur-unterstützung) |
| Weniger Ausfälle | Velocity kann von einem Watchdog bewacht werden, der es nach einem Absturz neu startet | [SLA-Optionen](../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md) |
| Geplante Arbeit ohne Fehler | Der Wartungsmodus zeigt Besuchern eine freundliche Seite und antwortet mit 503 | [Wartungsmodus](../../../../eng-us/features/6.0/maintenance-mode.md) |
| Schnellere Diagnose | Der Log-Viewer und Fehlerreferenzen finden einen Fehler schnell | [Einen Fehler über seine Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md) |
| Nachweis nach einem Vorfall | Ein manipulationssicherer Audit-Trail zeichnet auf, wer was getan hat | [Der Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md) |
| Sichere Aktualisierungen | Die Upgrade-Prüfung vergleicht die Dateien mit ihren Prüfsummen und die Datenbank mit dem Schema und ändert nichts | [Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md) |

Der Bereich [Zuverlässigkeit](../zuverlaessigkeit/README.md) erklärt jede dieser Praktiken für Unternehmen.

## Ein durchgerechnetes Beispiel

Ein kleines Unternehmen vergleicht sein Jahr vor und nach dem Wechsel zu einer stabilen, gut betriebenen
Exponential-Website. Jeder Eingabewert ist eine **Beispielannahme**.

| Eingabewert | Wert |
|---|---|
| Beispielannahme: Stunden offline pro Jahr vorher | 10 Stunden |
| Beispielannahme: Stunden offline pro Jahr nachher | 2 Stunden |
| Beispielannahme: Kosten einer Stunde offline | 300 Währungseinheiten (aus Ihrem [Arbeitsblatt zu Ausfallkosten](arbeitsblatt-kosten-eines-ausfalls.md)) |
| Beispielannahme: Stunden für Notfallkorrekturen pro Jahr vorher | 40 Stunden |
| Beispielannahme: Stunden für Notfallkorrekturen pro Jahr nachher | 10 Stunden |
| Beispielannahme: Satz für Notfallarbeit | 80 Währungseinheiten pro Stunde |
| Beispielannahme: jährliche Kosten der Stabilitätspraktiken (Monitoring, Wiederherstellungstests, geplante Aktualisierungen) | 2.000 Währungseinheiten |

Schritt für Schritt:

1. Vermiedene Ausfallzeit: 10 - 2 = 8 Stunden. Wert: 8 x 300 = **2.400**.
2. Vermiedene Notfallstunden: 40 - 10 = 30 Stunden. Wert: 30 x 80 = **2.400**.
3. Nutzen insgesamt pro Jahr: 2.400 + 2.400 = **4.800**.
4. Kosten pro Jahr: **2.000**.
5. Nettonutzen: 4.800 - 2.000 = **2.800**.
6. ROI: 2.800 / 2.000 x 100 % = **140 %**.

Lesen Sie es so: Für jede Währungseinheit, die für Stabilität ausgegeben wird, behält das Beispielunternehmen 2,4
Einheiten (4.800 / 2.000). Ihr Ergebnis wird abweichen; die Methode bleibt dieselbe.

## Amortisationsdauer

Wenn die Stabilitätsarbeit mit einmaligen Kosten beginnt, etwa einer Migration oder der Einrichtung des Monitorings,
ergänzen Sie eine Amortisationsdauer:

1. Beispielannahme: einmalige Kosten des Wechsels: **7.000** Währungseinheiten.
2. Monatlicher Nettonutzen aus dem Beispiel oben: 2.800 / 12 = etwa **233** im Monat.
3. Amortisation: 7.000 / 233 = etwa **30 Monate**.

Eine Amortisation innerhalb der geplanten Lebensdauer der Website (zum Beispiel fünf Jahre, 60 Monate) bedeutet, dass
die Investition ihr Geld zurückbringt und danach weiter spart.

## Den Business Case glaubwürdig machen

- [ ] Nehmen Sie die Stunden offline aus Ihrem eigenen Monitoring oder Ihren Notizen zu Vorfällen, nicht aus dem
      Gedächtnis.
- [ ] Nehmen Sie Stundensätze aus echten Rechnungen oder Gehaltsangaben.
- [ ] Zeigen Sie die Berechnung, nicht nur das Ergebnis; die Finanzabteilung rechnet nach.
- [ ] Zählen Sie nur Nutzen, den Sie benennen können; lassen Sie den Ruf aus der Zahl heraus und erwähnen Sie ihn in
      Worten.
- [ ] Nennen Sie keine Preise anderer Produkte, wenn Sie kein schriftliches Angebot haben; vergleichen Sie sonst
      qualitativ ([Exponential sachlich vergleichen](../../geschaeft/exponential-sachlich-vergleichen.md)).
- [ ] Wiederholen Sie die Berechnung nach einem Jahr mit gemessenen Zahlen
      ([Checkliste Jahresrückblick](../vorlagen/checkliste-jahresrueckblick.md)).

## Verwandte Seiten

- [Warum Stabilität wichtig ist](../zuverlaessigkeit/warum-stabilitaet-wichtig-ist.md)
- [Release-Linie und Support-Richtlinie](../zuverlaessigkeit/release-linie-und-support-richtlinie.md)
- [Vorlage ROI-Arbeitsblatt](../vorlagen/vorlage-roi-arbeitsblatt.md)
- [Kennzahlen aus Logs und Audit](../../fuehrung/kennzahlen-aus-logs-und-audit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
