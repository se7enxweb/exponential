---
title: "Ausschreibungsvorlage: Klausel zu Service-Levels"
audience: government, project-manager, hosting, partner
category: public-sector
order: 100
summary: Klauseltext für die Service-Levels einer Exponential-Website - Verfügbarkeit, Reaktion des Supports nach Schweregrad, Wartungsfenster, Wiederherstellung, Sicherheitsupdates, Messung, Berichte und Abhilfen - mit jeder Zahl als Lücke für Ihren eigenen Bedarf.
keywords: Service-Levels, SLA, Verfügbarkeit, Uptime, Reaktionszeit, Schweregrad, Wartungsfenster, RPO, RTO, Gutschriften, Messung, Berichte, Ausschreibungsvorlage, service levels, availability, uptime, response time, severity, maintenance window, credits, measurement, reporting, tender template
type: help
---
# Ausschreibungsvorlage: Klausel zu Service-Levels

Diese Seite richtet sich an Beschaffungsstellen, die Service-Levels für Hosting und Support einer Exponential-Website
festlegen. Sie folgt dem Aufbau von [SLA-Optionen](../../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md), das für Hoster geschrieben
ist, und macht daraus eine Klausel aus Sicht des Kunden. **Diese Hilfe nennt keine Zahlen**: Prozentsätze der
Verfügbarkeit, Reaktionszeiten und Gutschriften hängen von Ihrem Bedarf und Budget ab. Das Projekt Exponential selbst
veröffentlicht kein SLA; die einzige Reaktionszusage in seinen Dateien ist eine erste Antwort auf eine vertrauliche
Sicherheitsmeldung innerhalb eines Werktags ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)).

**Vorlage** - an Ihre Vergaberegeln anpassen; keine Rechtsberatung

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Die Klausel](#die-klausel)
2. [Schweregrade festlegen](#schweregrade-festlegen)
3. [Messen, was versprochen wird](#messen-was-versprochen-wird)
4. [Hinweise für die bewertende Person](#hinweise-für-die-bewertende-person)

## Die Klausel

```text
S. SERVICE LEVELS

S.1 Availability. The public website shall be available <percentage> of the time
    per <calendar month>, measured by <external check> requesting <URL that
    touches the database> every <interval> from <location>. Announced maintenance
    windows under S.3 are excluded.

S.2 Support response. The Supplier responds to and resolves incidents as follows:
      Severity 1: first response <time>, workaround <time>, hours <24x7 / business>
      Severity 2: first response <time>, workaround <time>, hours <...>
      Severity 3: first response <time>, resolution <time>, hours <...>
      Severity 4: first response <time>, resolution next planned release
    "First response" means a reply from a qualified person, not an automatic
    acknowledgment.

S.3 Maintenance. Planned work takes place in <day/time, time zone>, announced <n>
    days ahead to <contact>. Visitors see a maintenance page; editors are informed.

S.4 Recovery. RPO <time>; RTO <time>; restore tested every <interval> (see
    <hosting clause>).

S.5 Security updates. As set out in <vulnerability and patching clause>.

S.6 Reporting. The Supplier reports monthly: availability, incidents by severity
    with response and resolution times, maintenance performed, updates applied,
    restore tests.

S.7 Remedies. If a service level is missed in <period>: <service credits /
    remediation plan / escalation / termination right>, as set out in <annex>.

S.8 Exclusions. Content or configuration changes made by the Customer against the
    Supplier's documented advice; third-party services the Customer chose;
    force majeure as defined in <clause>.
```

## Schweregrade festlegen

| Schweregrad | Beispieldefinition (anpassen) |
|---|---|
| 1 | Öffentliche Website nicht erreichbar oder zeigt allen Besuchern falsche Inhalte; Sicherheitsvorfall; die Redaktion kann gar nicht veröffentlichen |
| 2 | Eine wichtige Funktion fällt aus (Suche, Formulare, eine Sprachversion), ohne Umgehung |
| 3 | Eine Funktion fällt aus, mit Umgehung; eine einzelne Seite ist falsch |
| 4 | Fragen, kosmetische Probleme, Änderungswünsche |

Die eigenen Stufenmodelle von Hosting-Unternehmen stehen in [Support-Stufen](../../../hosting/geschaeft/support-stufen-fuer-exponential-hosting.md).

## Messen, was versprochen wird

| Zusage | Wie sie mit Exponential gemessen wird |
|---|---|
| Verfügbarkeit | externe Prüfung einer Seite, die die Datenbank verwendet ([Zustandsprüfungen und Verfügbarkeit](../../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md)) |
| Von Besuchern gesehene Fehler | Anfrageprotokolle und der Log-Betrachter ([Anfrageprotokolle](../../../../funktionen/6.0/anfrageprotokolle.md), [Log-Betrachter](../../../../funktionen/6.0/log-betrachter.md)) |
| Wartungsfenster | Wartungsmodus mit Nachricht und Endzeit ([Wartungsmodus](../../../../anleitungen/wartungsmodus.md)); das Umschalten wird im Audit-Protokoll festgehalten |
| RPO und RTO | Zeitstempel der Sicherungen und der letzte Wiederherstellungstest |
| Reaktionszeiten | das Ticketsystem des Lieferanten; verlangen Sie Lesezugriff |

Velocity kann von einem Watchdog überwacht werden, der die Engine nach einem Absturz neu startet
([Velocity für Hoster](../../../hosting/server/velocity-fuer-hoster.md)); das hilft der Verfügbarkeit, ersetzt aber keine
Überwachung.

## Hinweise für die bewertende Person

- Vergleichen Sie die Service-Levels mit dem Personal, das der Bieter nennt; ein Versprechen ohne Bereitschaftspersonal
  ist nicht glaubwürdig.
- Ein Partner des Projekts kann seine eigenen Eskalationszeiten beim Projekt nachweisen
  ([Support-Stufen und Reaktionszeiten](../../../partner/support-stufen-und-reaktionszeiten.md)).

## Verwandte Seiten
- [Ausschreibungsvorlagen](README.md)
- [Klausel zu Support und Wartung](klausel-support-und-wartung.md)
- [Klausel zu Hosting und Datenstandort](klausel-hosting-und-datenstandort.md)
- [Anforderungen an Betrieb und Überwachung](../anforderungen/anforderungen-an-betrieb-und-ueberwachung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
