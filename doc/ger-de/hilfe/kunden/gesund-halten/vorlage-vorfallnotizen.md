---
title: Vorlage für Vorfallnotizen
audience: customer, businesses, project-manager
category: customer-success
order: 22
summary: Eine Vorlage für die Notizen des Betreibers während und nach einem Vorfall auf einer Exponential-Website - eine Zeitleiste, was Besucher sahen, wer angerufen wurde, Entscheidungen, Kommunikation, Ursache und Maßnahmen - dazu die Fragen für die Auswertung und wie die eigenen Aufzeichnungen der Website beim Ausfüllen helfen.
keywords: Vorfall, Vorlage, Notizen, Zeitleiste, Nachbesprechung, Ausfallbericht, Maßnahmen, Nachweise, incident, template, notes, timeline, post-incident review, outage report, actions, evidence
type: help
---
# Vorlage für Vorfallnotizen

**Vorlage.** Kopieren Sie sie, füllen Sie sie während des Vorfalls aus und vervollständigen Sie sie danach. Notizen,
die im Moment entstehen, machen die Auswertung nützlich und geben Ihnen Nachweise für Ihren Anbieter, Ihre
Geschäftsleitung oder einen Prüfer. Ersetzen Sie jede Zeile in spitzen Klammern durch Ihre eigenen Fakten.

## Warum Sie es aufschreiben sollten

Der Plan für kleine Teams bittet die leitende Person, „die Zeit festzuhalten“ und, wenn nur eine Person da ist, „die
Uhrzeit jedes“ Schritts aufzuschreiben ([Vorfallreaktion für kleine Teams](../../unternehmen/zuverlaessigkeit/stoerungsbehandlung-fuer-kleine-teams.md)).
Mit Ihren Notizen können Sie außerdem die Darstellung Ihres Anbieters mit dem abgleichen, was Sie gesehen haben, und mit
den vereinbarten Reaktionszeiten.

## Die Vorlage

```text
INCIDENT NOTES                                    Reference: <your own number or date>

1. Summary (fill in afterwards)
   What happened:          <one or two sentences>
   Severity:               <1 critical / 2 high / 3 normal / 4 low>
   Started / noticed / resolved:  <time> / <time> / <time>   (time zone: <zone>)
   Visitors affected:      <all / some pages / editors only / one function>

2. What visitors saw
   Page text and status:   <"We'll be right back", "Something went wrong", blank, browser error ...>
   Error reference(s):     <ERR-... or none>
   Pages checked:          <addresses>

3. Timeline (one line per event, as it happens)
   <time>  <what happened or was done>                      <who>
   <time>  Noticed by <customer / monitor / staff>
   <time>  Called <host / partner>, ticket <number>
   <time>  First answer from <provider>
   <time>  Decision: <fix / roll back / maintenance page / restore>
   <time>  Customers told via <channel>
   <time>  Site back for everyone, checked by <name>

4. Recent changes before it started
   <upgrade, deploy, settings change, import, none known>

5. Communication
   Messages sent:          <time, channel, text>
   Next update promised:   <time>      Kept: <yes / no>

6. Cause and fix (from the provider's account)
   Cause:                  <cause>
   Fix:                    <fix>
   Data lost:              <none / what, for which period>

7. Response against the agreement
   Agreed first response:  <from the contract>     Actual: <time>
   Agreed restore time:    <from the contract>     Actual: <time>

8. Review (within a week, without blame)
   How did we find out, could we have found out sooner?   <answer>
   What slowed us down?                                   <answer>
   What would have prevented it?                          <answer>

9. Actions
   | Action | Owner | Due | Done |
```

## Woher die Fakten kommen

| Fakt | Quelle |
|---|---|
| Genaue Zeiten von Deployments, Einstellungsänderungen, Ein- und Ausschalten der Wartung | Der Kanal `system` des Prüfprotokolls; Umschaltungen der Wartung werden als `system.maintenance.change` festgehalten ([Wartungsmodus](../../../anleitungen/wartungsmodus.md), [Prüfprotokoll für den Betrieb](../../unternehmen/zuverlaessigkeit/audit-trail-fuer-den-betrieb.md)) |
| Der technische Fehler hinter einer Referenz | **Setup > Logdateien**, Feld **Fehlerreferenz** ([Einen Fehler anhand seiner Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md)) |
| Wer sich angemeldet oder Inhalte geändert hat | Das Prüfprotokoll ([Das Prüfprotokoll](../../../../eng-us/features/6.0/audit-trail.md)) |
| Verfügbarkeit und Zeitpunkte von Alarmen | Ihre externe Überwachung ([Überwachung](ueberwachung.md)) |
| Reaktionszeiten | Das Ticketsystem Ihres Anbieters |

Bitten Sie Ihren Administrator oder Hoster nach dem Vorfall um diese Fakten; Sie müssen die Logs nicht selbst lesen.

> **Warnung**
> Kopieren Sie keine Passwörter, Schlüssel oder personenbezogenen Daten von Kunden in die Notizen, auch nicht aus Logs.
> Bewahren Sie die Notizen bei Ihren anderen Unterlagen auf, lesbar nur für die Personen, die sie brauchen.

## Nach der Auswertung

Übernehmen Sie die Maßnahmen in Ihre regelmäßige Routine: die [Zuverlässigkeits-Scorecard](../../unternehmen/zuverlaessigkeit/bewertungsbogen-zuverlaessigkeit.md)
und das [Risikoregister](../../fuehrung/risikomanagement.md). Wenn der Vorfall Sie Geld gekostet hat, hilft Ihnen das
[Arbeitsblatt zu den Kosten von Ausfällen](../../unternehmen/kosten/arbeitsblatt-kosten-eines-ausfalls.md), für die nächste
Vertragsverhandlung eine Zahl zu nennen.

## Verwandte Seiten

- [Wenn die Website nicht erreichbar ist](wenn-die-website-nicht-erreichbar-ist.md)
- [Probleme melden](probleme-melden.md)
- [Ihren Anbieter jedes Jahr überprüfen](../partner-und-hoster/anbieter-jedes-jahr-ueberpruefen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
