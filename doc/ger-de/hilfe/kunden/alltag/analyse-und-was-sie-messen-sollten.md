---
title: Analyse und was Sie messen sollten
audience: customer, businesses, project-manager
category: customer-success
order: 21
summary: Was ein Website-Betreiber auf einer Exponential-Website messen kann - was die Software selbst aufzeichnet (Suchstatistiken, Aufrufzähler, Prüfprotokoll, Shop-Zahlen), wofür ein Analysedienst nötig ist, Einwilligung und eine kurze Liste von Fragen, die jeden Monat eine Zahl wert sind.
keywords: Analyse, Messen, Kennzahlen, Statistik, Suchstatistiken, Aufrufzähler, Prüfprotokoll, Shop-Dashboard, Einwilligung, Google Analytics, Berichte, analytics, measuring, KPIs, statistics, search statistics, view counts, audit trail, store dashboard, consent, reporting
type: help
---
# Analyse und was Sie messen sollten

Diese Seite ist für den Betreiber und die Leitung, die darüber berichten, wie es der Website geht. Exponential zeichnet
einige Zahlen selbst auf; für eine Besucheranalyse (woher Besucher kommen, wie lange sie bleiben) brauchen Sie einen
Dienst, den Sie hinzufügen ([Besucherstatistik und Analyse](../../../../eng-us/help/creators/website-creators/statistics-and-analytics.md)).
Beginnen Sie mit den Fragen, die Sie beantwortet haben möchten, nicht mit den Zahlen, die ein Werkzeug zufällig anzeigt.

## Inhalt

1. [Zuerst die Fragen](#zuerst-die-fragen)
2. [Was Exponential aufzeichnet](#was-exponential-aufzeichnet)
3. [Einen Analysedienst hinzufügen](#einen-analysedienst-hinzufügen)
4. [Einwilligung und Datenschutz](#einwilligung-und-datenschutz)
5. [Ein Monatsbericht](#ein-monatsbericht)

## Zuerst die Fragen

| Frage | Woher die Antwort kommt |
|---|---|
| Wonach suchen Besucher, ohne es zu finden? | Suchstatistiken |
| Welche Seiten sind beliebt? | Aufrufzähler, oder ein Analysedienst |
| Veröffentlicht das Team, was wir geplant haben? | das Prüfprotokoll, verglichen mit dem [Redaktionskalender](redaktionskalender.md) |
| Erreicht der Newsletter die Leute? | die eigene Statistik des Newsletters ([Newsletter-Routine](../wachstum/newsletter-routine.md)) |
| Wie läuft der Shop? | das Shop-Dashboard und die Shop-Statistik |
| Woher kommen Besucher, mit welchen Geräten? | nur ein Analysedienst |

## Was Exponential aufzeichnet

Aus [Besucherstatistik und Analyse](../../../../eng-us/help/creators/website-creators/statistics-and-analytics.md):

| Quelle | Was | Wo |
|---|---|---|
| Suchstatistiken | gesuchte Begriffe, Häufigkeit, durchschnittliche Trefferzahl; **standardmäßig ausgeschaltet** (`LogSearchStats=disabled` in `settings/site.ini`) | **Setup > Suchstatistiken** |
| Aufrufzähler | Seitenaufrufe pro Knoten, gezählt vom Cronjob-Teil `updateviewcount` aus dem Zugriffslog | von Templates für Listen der „meistgelesenen“ Seiten verwendet |
| Request-Logs | Anfragen mit Zeitmessung, für den Betrieb | [Request-Logs](../../../../eng-us/features/6.0/request-logs.md) |
| Shop-Statistik | Bestellungen und Umsatz | `shop/statistics`, das [Shop-Dashboard](../../../../eng-us/features/6.0/store-dashboard.md) |
| Prüfprotokoll | wer in der Administration was geändert hat | [Das Prüfprotokoll](../../../../eng-us/features/6.0/audit-trail.md) |

Diese Quellen beantworten Fragen des Betriebs; sie ersetzen keinen Analysedienst (dieselbe Quelle). Die Seite für
Projektleitungen [Ein Projekt messen: Kennzahlen](../../fuehrung/kennzahlen-aus-logs-und-audit.md) listet weitere Zahlen,
die das Produkt liefert, jeweils mit der Seite oder dem Befehl, und eine Vorlage für einen Monatsbericht.

> **Hinweis**
> Das Lesen des Prüfprotokolls erfordert `audit/read`; auf einer neuen Installation hat nur Administrator diese
> Richtlinie ([Ein Projekt messen: Kennzahlen](../../fuehrung/kennzahlen-aus-logs-und-audit.md)). Eine Berichtsrolle,
> beschränkt auf die Kanäle, die Sie brauchen, lässt sich ohne das Recht anlegen, etwas zu ändern.

## Einen Analysedienst hinzufügen

- Die Erweiterung `bcwebsitestatistics` fügt das Google-Analytics-Skript hinzu und kann Shop-Bestellungen melden
  ([bcwebsitestatistics](../../../../eng-us/features/6.0/extensions/bcwebsitestatistics.md)).
- Ein anderer Dienst wird als Skriptdatei über das Design hinzugefügt, wobei sein Host in der Content Security Policy
  der Website erlaubt wird ([Besucherstatistik und Analyse](../../../../eng-us/help/creators/website-creators/statistics-and-analytics.md)).

Fragen Sie Ihren Partner, welchen Dienst Ihre Website verwendet und wer das Konto hat. Das Konto gehört Ihrer
Organisation, nicht dem Partner.

## Einwilligung und Datenschutz

Für Analyse ist in vielen Rechtsordnungen in der Regel die Einwilligung des Besuchers nötig: Laden Sie das Skript erst
nach der Einwilligung, beschreiben Sie es auf der Datenschutzseite, und bevorzugen Sie Dienste, die Sie selbst hosten
können oder die keine Cookies setzen, wenn Ihr Publikum das erwartet
([Besucherstatistik und Analyse](../../../../eng-us/help/creators/website-creators/statistics-and-analytics.md)). Auch Suchbegriffe
können personenbezogene Daten sein, weshalb die Suchstatistiken standardmäßig ausgeschaltet sind
([Website-Suche](../../../../eng-us/help/creators/website-creators/site-search.md)). Dies ist keine Rechtsberatung; fragen Sie die
Person, die für den Datenschutz verantwortlich ist.

## Ein Monatsbericht

**Vorlage.** Halten Sie ihn auf einer Seite:

```text
MONTH: __________   Prepared by: ______________
Published this month (from the audit trail or the calendar): ____ items, planned: ____
Top 5 search phrases without results: ____________________________________
Action taken on them: ____________________________________________________
Most viewed pages (view counts or analytics): ____________________________
Newsletter: editions sent ____, notes: ___________________________________
Store (if any): orders ____, notes: ______________________________________
One thing we change next month: __________________________________________
```

Vergleichen Sie jede Zahl mit demselben Monat davor; die erste Messung ist Ihr Ausgangswert
([Ein Projekt messen: Kennzahlen](../../fuehrung/kennzahlen-aus-logs-und-audit.md)).

## Verwandte Seiten

- [Website-Suche und wonach Besucher suchen](website-suche-und-wonach-besucher-suchen.md)
- [Besucherstatistik und Analyse](../../../../eng-us/help/creators/website-creators/statistics-and-analytics.md)
- [Shop-Dashboard und Berichte](../../unternehmen/shops/shop-uebersicht-und-berichte.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
