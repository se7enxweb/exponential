---
title: SLA-Vorlage
audience: customer, hosting, partner
category: customer-success
order: 6
summary: Eine Vorlage für ein Service Level Agreement zwischen einem Website-Betreiber und dem Hoster oder Partner einer Exponential-Website - Verfügbarkeit, Wartungsfenster, Support, Backups, Wiederherstellung, Sicherheitsupdates, Ausschlüsse und Berichte - mit auszufüllenden Feldern und ohne Zahlen. Keine Rechtsberatung.
keywords: SLA, Vorlage, Service Level Agreement, Verfügbarkeit, RPO, RTO, Wartungsfenster, Sicherheitsupdates, Ausschlüsse, Berichte, keine Rechtsberatung, template, service level agreement, availability, maintenance window, security updates, exclusions, reporting
type: help
---
# SLA-Vorlage

**Vorlage. Keine Rechtsberatung.** Dies ist eine Struktur für ein Service Level Agreement (SLA) für eine
Exponential-Website, aus Sicht des Betreibers geschrieben. Sie folgt den Zusagen, die unter
[SLA-Optionen](../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md) und
[Grundlagen zu Verträgen und SLAs](../../../../eng-us/help/businesses/choosing/contracts-and-slas-basics.md) beschrieben sind. Jeder Wert
in eckigen Klammern ist ein Feld, das Sie mit Ihrem Anbieter vereinbaren; diese Seite nennt keine Zahlen, weil die
richtigen von Ihrer Website, Ihrem Budget und dem abhängen, was Ihr Anbieter personell leisten kann. Lassen Sie das
Ergebnis von Ihrer Rechtsberatung prüfen.

## Wie Sie sie verwenden

1. Legen Sie zuerst Ihren Bedarf fest: wie viel Datenverlust und Ausfallzeit Sie hinnehmen können
   ([Backups](../gesund-halten/backups.md), [Arbeitsblatt zu den Kosten von Ausfällen](../../../../eng-us/help/businesses/costs/cost-of-downtime-worksheet.md)).
2. Füllen Sie die Felder mit Ihrem Anbieter aus. Löschen Sie Abschnitte, die nicht zutreffen.
3. Fügen Sie die Tabelle [Wer was macht](wer-was-macht.md) und die
   [Support-Vereinbarung](vorlage-support-vereinbarung.md) bei.
4. Vereinbaren Sie, wie jeder Wert gemessen und berichtet wird. Ein Versprechen ohne Messung lässt sich nicht prüfen.

## Parteien und Umfang

| Feld | Wert |
|---|---|
| Kunde | [organization name] |
| Anbieter | [host or partner name] |
| Abgedeckte Websites | [site addresses and siteaccesses] |
| Abgedeckte Umgebungen | [live / staging / test] |
| Gültig ab, Version | [date], [version of this agreement] |

## Service-Level

| Zusage | Vereinbarter Wert | Gemessen durch | Berichtet |
|---|---|---|---|
| Verfügbarkeit | [percentage] pro [month] | [external check] an [address of a page that touches the database] | [monthly] |
| Wartungsfenster | [day and time, time zone] | Angekündigt [days ahead] über [channel] | Bei jedem Fenster |
| Erste Reaktion im Support | Wie in der Support-Vereinbarung | Das Ticketsystem des Anbieters | [monthly] |
| Backups | [what] alle [interval], aufbewahrt [period], gespeichert [where] | Zeitstempel der Backups | [monthly] |
| Wiederherstellungspunkt (RPO) | [time] | Zeitstempel der Backups | [monthly] |
| Wiederherstellungszeit (RTO) | [time] | Die Dauer des letzten Wiederherstellungstests | Nach jedem Test |
| Wiederherstellungstests | Alle [interval], Ergebnis an [role] | Das Testprotokoll ([Wiederherstellungstest](../gesund-halten/wiederherstellungstest.md)) | Nach jedem Test |
| Sicherheits-Releases von Exponential | Eingespielt innerhalb von [agreed time] nach dem Release | Version der Website und Deployment-Einträge des Prüfprotokolls | [monthly] |
| Sicherheitsupdates für PHP und Betriebssystem | Eingespielt innerhalb von [agreed time] | Änderungsprotokoll des Anbieters | [monthly] |

> **Tipp**
> Für Sicherheit lautet die messbare Formulierung „Eine veröffentlichte Korrektur wird innerhalb einer bestimmten Zeit
> eingespielt“ ([Grundlagen zu Verträgen und SLAs](../../../../eng-us/help/businesses/choosing/contracts-and-slas-basics.md)).

## Ausschlüsse

Lieferanten schließen üblicherweise Folgendes aus ([SLA-Optionen](../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md)). Lesen Sie
sie so sorgfältig wie die Versprechen.

| Ausschluss | Vereinbart |
|---|---|
| Eigener Code, eigene Templates und Erweiterungen des Kunden sowie vom Kunden geänderte Konfiguration | [yes / no / as follows] |
| Erweiterungen, die der Anbieter nicht geliefert hat | [yes / no / as follows] |
| Ausfälle durch Inhalte des Kunden, etwa ein Import, der die Festplatte füllt | [yes / no / as follows] |
| Geplante Wartung in angekündigten Fenstern | [yes / no / as follows] |
| [other exclusion] | [terms] |

## Wenn ein Level verfehlt wird

| Feld | Wert |
|---|---|
| Gutschriften oder andere Abhilfe | [the remedy, if any] |
| Wie der Kunde sie geltend macht | [process, deadline] |
| Wiederholte Verfehlungen | [what happens, for example a review or the right to end the contract] |

## Berichte

| Bericht | Inhalt | Häufigkeit | An |
|---|---|---|---|
| Servicebericht | Verfügbarkeit, Vorfälle, Wiederherstellungstests, eingespielte Updates, Versionen | [monthly / quarterly] | [role] |
| Vorfallbericht | Zeitleiste, Ursache, Korrektur, verlorene Daten, Maßnahmen ([Vorlage für Vorfallnotizen](../gesund-halten/vorlage-vorfallnotizen.md)) | Nach jedem Vorfall des Schweregrads 1 oder 2 | [role] |

## Überprüfung

| Feld | Wert |
|---|---|
| Überprüfungsdatum | [date each year] |
| Verantwortlich für die Überprüfung | [role on each side] |
| Grundlage | [Ihren Anbieter jedes Jahr überprüfen](anbieter-jedes-jahr-ueberpruefen.md) |

## Unterschriften

| | Kunde | Anbieter |
|---|---|---|
| Name und Rolle | [name, role] | [name, role] |
| Datum | [date] | [date] |

## Verwandte Seiten

- [Verträge und SLAs](vertraege-und-slas.md)
- [Vorlage für eine Support-Vereinbarung](vorlage-support-vereinbarung.md)
- [Vorlage für eine Wartungsvereinbarung](vorlage-wartungsvereinbarung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
