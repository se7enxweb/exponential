---
title: Support-Stufen für Exponential-Hosting
audience: hosting
category: hosting
order: 40
summary: Support-Stufen, die ein Hoster für Exponential-Websites anbieten kann, die Grenze zwischen Hosting-Support und Anwendungs-Support, Definitionen der Schweregrade, was Support-Mitarbeitende von einem Kunden erfragen sollten und wann an das Projekt eskaliert wird.
keywords: Support, Support-Stufen, Stufen, Schweregrad, Eskalation, Hosting-Support, Anwendungs-Support, Tickets, Diagnose, Fehlerreferenz, Logs, Fehlermeldung, Sicherheitsmeldung, support tiers, levels, severity, escalation, hosting support, application support, tickets, diagnostics, error reference, logs, bug report, security report
type: help
---
# Support-Stufen für Exponential-Hosting

Diese Seite ist für Führungskräfte, die das Support-Angebot eines Hosting-Anbieters festlegen, und für die
Support-Mitarbeitenden, die darin arbeiten. Sie beantwortet: welche Stufen Sie anbieten können, wo Hosting-Support endet
und Anwendungs-Support beginnt, wie Sie den Schweregrad einstufen, was Sie vom Kunden erfragen und wann ein Problem an das
Exponential-Projekt geht.

Alle Stufen auf dieser Seite sind Optionen. Zeiten und Preise legen Sie fest; keine Quelle nennt welche.

## Inhalt

1. [Hosting-Support und Anwendungs-Support](#hosting-support-und-anwendungs-support)
2. [Optionen für Stufen](#optionen-für-stufen)
3. [Schweregrade](#schweregrade)
4. [Was Sie vom Kunden erfragen](#was-sie-vom-kunden-erfragen)
5. [Eskalation an das Projekt](#eskalation-an-das-projekt)
6. [Werkzeuge Ihrer Support-Mitarbeitenden](#werkzeuge-ihrer-support-mitarbeitenden)
7. [Ein Stufenblatt zum Kopieren](#ein-stufenblatt-zum-kopieren)

## Hosting-Support und Anwendungs-Support

| Hosting-Support (Aufgabe des Hosters in jeder Stufe) | Anwendungs-Support (nur, wo die Stufe ihn enthält) |
|---|---|
| der Server, PHP, die Engine, der Datenbankserver | Inhaltsklassen, Templates, Design |
| Backups und Wiederherstellungen | die Erweiterungen und der eigene Code des Kunden |
| TLS-Zertifikate | redaktionelle Fragen („Wie veröffentliche ich?“) |
| Mailzustellung vom Server | Datenkorrekturen innerhalb der Inhalte |
| Exponential-Updates einspielen, wo vereinbart | Upgrades, die Codeänderungen brauchen |

Schreiben Sie diese Tabelle in Ihre Support-Bedingungen. Die meisten Reibungen im Hosting-Support entstehen durch eine
unklare Grenze.

> **Tipp**
> Redaktionelle Fragen haben oft eine Antwort in dieser Hilfe. Verweisen Sie Kunden auf die Zielgruppen Redakteure und
> Benutzer im Hilfe-Center, zum Beispiel [Erste Schritte](../../erste-schritte.md) und [Inhalte bearbeiten](../../inhalte-bearbeiten.md).

## Optionen für Stufen

| Stufe | Enthält typischerweise | Passt typischerweise zu |
|---|---|---|
| Basis | Hosting-Support per Ticket, zu Geschäftszeiten | Shared-Tarifen |
| Standard | Hosting-Support, schnellere erste Antwort, Telefon oder Chat | VPS-Tarifen |
| Verwaltet | Hosting-Support plus Exponential-Updates, Abstimmung von Cache und Leistung, Wiederherstellung auf Anfrage | verwalteten Tarifen |
| Anwendung | Verwaltet plus Hilfe bei Templates, Erweiterungen und Konfiguration, nach Vereinbarung | Kunden ohne eigene Entwickler |
| Entwicklung | Projektarbeit: Migrationen, neue Funktionen, Erweiterungen | als Projekt abgerechnet |

## Schweregrade

| Schweregrad | Definition | Beispiel |
|---|---|---|
| 1 Kritisch | die Website ist nicht erreichbar, oder Daten gehen verloren oder werden offengelegt | jede Seite liefert einen Fehler; ein vermuteter Einbruch |
| 2 Hoch | eine wichtige Funktion versagt, ohne Umgehung | Redakteure können nicht veröffentlichen; die Anmeldung scheitert |
| 3 Normal | eine Funktion versagt, eine Umgehung existiert | ein Ansichtsmodus wird falsch dargestellt |
| 4 Niedrig | Fragen, Wünsche | „Können wir eine Sprache hinzufügen?“ |

Ein vermuteter Sicherheitsvorfall ist immer Schweregrad 1: Folgen Sie [Kompromittierte Website](../runbooks/kompromittierte-website.md).

## Was Sie vom Kunden erfragen

1. Die Adresse der Seite und die Uhrzeit des Vorfalls (mit Zeitzone).
2. Was der Kunde getan hat und was er erwartet hat.
3. Die **Fehlerreferenz**, die auf der Fehlerseite angezeigt wird, falls vorhanden. Der Administrator kann sie nachschlagen;
   siehe [Einen Fehler über seine Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md).
4. Ob das Problem bei allen oder bei einem Konto auftritt.
5. Kürzliche Änderungen: ein Update, eine neue Erweiterung, eine geänderte Einstellung.

Bitten Sie einen Kunden nie, ein Passwort zu schicken. Verwenden Sie ein Wegwerf-Testkonto, wenn Sie etwas im angemeldeten
Zustand nachstellen müssen.

## Eskalation an das Projekt

| Situation | Wohin sie geht |
|---|---|
| Ein reproduzierbarer Fehler in Exponential selbst | in den Software-Tracker, wie auf [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md) beschrieben |
| Eine Sicherheitslücke | vertraulich, wie `SECURITY.md` es beschreibt; nie als öffentliches Issue ([Sicherheitsupdates und Offenlegung](../sicherheit/sicherheitsupdates-und-offenlegung.md)) |
| Eine Frage zum Projekt oder zur Community | in den Community-Tracker oder die Foren ([Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md#orte-der-community)) |
| Bezahlter Support über Ihr Team hinaus | ein Support-Vertrag mit 7x oder einem anderen Anbieter ([Mit dem Projekt und 7x zusammenarbeiten](mit-dem-projekt-und-7x-zusammenarbeiten.md)) |

Bevor Sie eskalieren, entfernen Sie Passwörter, Geheimnisse und personenbezogene Daten aus Logs und Screenshots;
`SECURITY.md` verlangt dasselbe.

## Werkzeuge Ihrer Support-Mitarbeitenden

| Werkzeug | Was es zeigt | Seite |
|---|---|---|
| Log-Viewer in der Administration | Fehler, Warnungen und die Anfrage-Logs an einer Stelle | [Log-Viewer und Log-Rotation](../betrieb/log-viewer-und-log-rotation.md) |
| Anfrage-Logs | jede Anfrage mit Status und Dauer | [Anfrage-Logs](../betrieb/anfrage-logs.md) |
| Prüfprotokoll | wer in der Administration was geändert hat | [Prüfprotokoll](../sicherheit/pruefprotokoll.md) |
| Kontrollzentrum von Velocity | Worker, Cache, Speicher der Engine | [Velocity für Hoster](../server/velocity-fuer-hoster.md) |
| Runbooks | Schritt für Schritt bei häufigen Vorfällen | [Runbooks](../runbooks/README.md) |

## Ein Stufenblatt zum Kopieren

```text
Support tier: <name>
Channels:              <ticket | e-mail | phone | chat>
Hours:                 <hours, time zone>
First response:        S1 <time>  S2 <time>  S3 <time>  S4 <time>
Included:              hosting support; <updates>; <restores>; <application support: yes/no>
Not included:          <custom development, third-party extensions, ...>
Escalation:            to <role>, then to the project or a contracted supplier
Customer obligations:  report with page, time and error reference; keep contacts current
```

## Verwandte Seiten

- [SLA-Optionen](sla-optionen-fuer-exponential-hosting.md)
- [Überblick über die Überwachung](../betrieb/ueberblick-ueber-die-ueberwachung.md)
- [Website nicht erreichbar](../runbooks/website-nicht-erreichbar.md)
- [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
